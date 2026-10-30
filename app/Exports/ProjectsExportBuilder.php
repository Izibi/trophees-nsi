<?php

namespace App\Exports;

use App\Models\Contest;
use App\Models\Project;
use App\Models\User;
use App\Models\Award;
use Illuminate\Support\Facades\DB;

/**
 * Rows of the coordination (projects) export: one row per project.
 *
 * Scores are aggregated from the `ratings` table grouped by phase — the
 * projects.score_* columns only ever hold the current phase's aggregates
 * (see Rating::refreshProjectRatings), so they cannot provide both
 * territorial and national values in the same row.
 *
 * Award cells concatenate all matching awards of the project for the phase,
 * separated by " | ".
 */
class ProjectsExportBuilder
{
    /** @var Contest */
    private $contest;

    /** @var User */
    private $user;

    public function __construct(Contest $contest, User $user)
    {
        $this->contest = $contest;
        $this->user = $user;
    }

    /**
     * @param string|null $laureateFilter one of: territorial, national, none, null
     * @return \Generator rows keyed by column key
     */
    public function rows($laureateFilter = null)
    {
        $base = $this->baseQuery($laureateFilter);
        if (!$base) {
            return;
        }

        $page = 1;
        do {
            $projects = $base->forPage($page, 500)->get();
            $ids = $projects->pluck('id')->all();
            $scores = $this->scoresByProject($ids);
            $awards = $this->awardsByProject($ids);

            foreach ($projects as $project) {
                yield $this->buildRow($project, $scores, $awards);
            }

            $page++;
        } while (count($projects) == 500);
    }

    /**
     * Base query: all contest projects for admins, otherwise the union of the
     * territory / prize views the user is scoped to (mirrors the access rules
     * of the legacy /projects_export link, without any phase gating).
     *
     * @param string|null $laureateFilter
     * @return \Illuminate\Database\Eloquent\Builder|null
     */
    private function baseQuery($laureateFilter)
    {
        if ($this->user->role == 'admin') {
            $q = Project::where('contest_id', $this->contest->id);
        } else {
            $views = $this->scopeViews();
            if (!count($views)) {
                return null;
            }
            $union = null;
            foreach ($views as $view) {
                $viewQuery = $this->scopeQuery($view);
                if ($union === null) {
                    $union = $viewQuery;
                } else {
                    $union = $union->union($viewQuery);
                }
            }
            // Wrap the union in a subquery so ordering / pagination work on top of it.
            $q = Project::fromSub($union, 'projects');
        }

        $q->with([
            'user',
            'school',
            'grade',
            'team_members' => function ($query) {
                $query->orderBy('id');
            },
        ]);
        $q->orderBy('projects.id');

        if (in_array($laureateFilter, ['territorial', 'national', 'none'], true)) {
            $contestId = $this->contest->id;
            if ($laureateFilter == 'none') {
                $q->whereDoesntHave('awards', function ($query) use ($contestId) {
                    $query->where('contest_id', $contestId);
                });
            } else {
                $q->whereHas('awards', function ($query) use ($contestId, $laureateFilter) {
                    $query->where('contest_id', $contestId);
                    if ($laureateFilter == 'territorial') {
                        $query->where('region_id', '!=', 0);
                    } else {
                        $query->where('region_id', 0);
                    }
                });
            }
        }

        return $q;
    }

    /**
     * Territory / prize scopes of the user, deduplicated:
     *  - coordinator: their own region;
     *  - every territorial / président-territorial role: that region;
     *  - every prize / président-prize role: those territorial awards.
     *
     * @return array[] list of ['type' => region|prize, 'target_id' => int]
     */
    private function scopeViews()
    {
        $views = [];
        if ($this->user->hasRole('coordinator') && $this->user->region_id) {
            $views['region:' . $this->user->region_id] = ['type' => 'region', 'target_id' => $this->user->region_id];
        }
        foreach ($this->user->roles as $role) {
            if (in_array($role->type, ['territorial', 'president-territorial'], true) && $role->target_id) {
                $views['region:' . $role->target_id] = ['type' => 'region', 'target_id' => $role->target_id];
            }
            if (in_array($role->type, ['prize', 'president-prize'], true) && $role->target_id) {
                $views['prize:' . $role->target_id] = ['type' => 'prize', 'target_id' => $role->target_id];
            }
        }
        return array_values($views);
    }

    /**
     * Query for one scope view. Mirrors the legacy export behaviour for
     * non-admins: only `validated` projects, excluding the user's own projects.
     *
     * @param array $view
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function scopeQuery(array $view)
    {
        $contestId = $this->contest->id;
        $q = Project::select('projects.*')->where('projects.contest_id', '=', $contestId);

        if ($view['type'] == 'region') {
            $q->join('schools', 'projects.school_id', '=', 'schools.id')
                ->where('schools.region_id', '=', $view['target_id']);
        } else {
            $targetId = $view['target_id'];
            $q->join('awards', function ($join) use ($contestId) {
                $join->on('awards.project_id', '=', 'projects.id')
                    ->where('awards.contest_id', '=', $contestId);
            })
                ->where('awards.prize_id', '=', $targetId)
                ->where('awards.region_id', '!=', 0);
        }

        $q->where('projects.status', '=', 'validated')
            ->where('projects.user_id', '!=', $this->user->id);

        return $q;
    }

    /**
     * Per-phase score aggregates, keyed by project ID then phase.
     *
     * @param int[] $ids
     * @return array
     */
    private function scoresByProject(array $ids)
    {
        $scores = [];
        if (!count($ids)) {
            return $scores;
        }

        $rows = DB::table('ratings')
            ->selectRaw(
                'project_id, phase, COUNT(*) as ratings_amount, '
                . 'AVG(score_operationality) as avg_technical, '
                . 'AVG(score_communication) as avg_non_technical, '
                . 'SUM(CASE WHEN score_operationality IS NOT NULL THEN 1 ELSE 0 END) as technical_count, '
                . 'SUM(CASE WHEN score_communication IS NOT NULL THEN 1 ELSE 0 END) as non_technical_count'
            )
            ->whereIn('project_id', $ids)
            ->where('published', 1)
            ->where('ignored', 0)
            ->groupBy('project_id', 'phase')
            ->get();

        foreach ($rows as $row) {
            // Same "note totale" logic as Rating::refreshProjectRatings().
            if ($row->technical_count > 0 && $row->non_technical_count > 0) {
                $total = $row->avg_technical + $row->avg_non_technical;
            } elseif ($row->technical_count > 0) {
                $total = $row->avg_technical * 2;
            } elseif ($row->non_technical_count > 0) {
                $total = $row->avg_non_technical * 2;
            } else {
                $total = 0;
            }

            $scores[$row->project_id][$row->phase] = [
                'amount' => $row->ratings_amount,
                'technical' => $row->avg_technical,
                'non_technical' => $row->avg_non_technical,
                'total' => $total,
            ];
        }

        return $scores;
    }

    /**
     * Contest awards of the given projects, keyed by project ID, sorted by
     * prize then region for a deterministic concatenation.
     *
     * @param int[] $ids
     * @return array|\Illuminate\Support\Collection
     */
    private function awardsByProject(array $ids)
    {
        if (!count($ids)) {
            return [];
        }
        return Award::with('prize')
            ->whereIn('project_id', $ids)
            ->where('contest_id', $this->contest->id)
            ->orderBy('prize_id')
            ->orderBy('region_id')
            ->get()
            ->groupBy('project_id');
    }

    /**
     * @param Project $project
     * @param array   $scores from scoresByProject()
     * @param \Illuminate\Support\Collection|array $awards from awardsByProject()
     * @return array row keyed by column key
     */
    private function buildRow(Project $project, array $scores, $awards)
    {
        $user = $project->user;
        $school = $project->school;
        list($firstName, $lastName) = ExportFormats::splitName($user ? $user->name : '');

        $projectScores = isset($scores[$project->id]) ? $scores[$project->id] : [];
        $territorialScores = isset($projectScores['territorial']) ? $projectScores['territorial'] : null;
        $nationalScores = isset($projectScores['national']) ? $projectScores['national'] : null;

        $projectAwards = isset($awards[$project->id]) ? $awards[$project->id] : collect();
        $territorialAwards = [];
        $nationalAwards = [];
        foreach ($projectAwards as $award) {
            if ($award->region_id == 0) {
                $nationalAwards[] = $award;
            } else {
                $territorialAwards[] = $award;
            }
        }

        $territorialPrizes = [];
        $territorialJuryComments = [];
        $territorialTeamComments = [];
        foreach ($territorialAwards as $award) {
            $territorialPrizes[] = $award->prize ? $award->prize->name : '';
            $territorialJuryComments[] = $award->comment;
            $territorialTeamComments[] = $award->comment_team;
        }

        $nationalPrizes = [];
        $nationalTeamComments = [];
        foreach ($nationalAwards as $award) {
            $nationalPrizes[] = $award->prize ? $award->prize->name : '';
            $nationalTeamComments[] = $award->comment_team;
        }

        $row = [
            'id' => $project->id,
            'name' => $project->name,
            'status' => $project->status,
            'teacher_first_name' => $firstName,
            'teacher_last_name' => $lastName,
            'teacher_email' => $user ? ($user->email ?? '') : '',
            'school_name' => $school ? $school->name : '',
            'school_address' => $school ? ($school->address ?? '') : '',
            'school_zip' => $school ? ($school->zip ?? '') : '',
            'school_city' => $school ? ($school->city ?? '') : '',
            'grade' => $project->grade ? $project->grade->name : '',
            'team_girls' => $project->team_girls ?? '',
            'team_boys' => $project->team_boys ?? '',
            'team_not_provided' => $project->team_not_provided ?? '',
            'class_girls' => $project->class_girls ?? '',
            'class_boys' => $project->class_boys ?? '',
            'class_not_provided' => $project->class_not_provided ?? '',
            'teacher_comment' => $project->teacher_notes ?? '',

            'terr_ratings_amount' => $territorialScores ? $territorialScores['amount'] : '',
            'terr_score_technical' => $territorialScores ? $territorialScores['technical'] : '',
            'terr_score_non_technical' => $territorialScores ? $territorialScores['non_technical'] : '',
            'terr_score_total' => $territorialScores ? $territorialScores['total'] : '',
            'terr_prize' => ExportFormats::joinValues($territorialPrizes),
            'terr_jury_comment' => ExportFormats::joinValues($territorialJuryComments),
            'terr_team_comment' => ExportFormats::joinValues($territorialTeamComments),

            'nat_ratings_amount' => $nationalScores ? $nationalScores['amount'] : '',
            'nat_score_technical' => $nationalScores ? $nationalScores['technical'] : '',
            'nat_score_non_technical' => $nationalScores ? $nationalScores['non_technical'] : '',
            'nat_score_total' => $nationalScores ? $nationalScores['total'] : '',
            'nat_prize' => ExportFormats::joinValues($nationalPrizes),
            'nat_team_comment' => ExportFormats::joinValues($nationalTeamComments),
        ];

        // Fixed team member slots 1..4, ordered by team_members.id.
        $members = $project->team_members->values();
        for ($i = 0; $i < 4; $i++) {
            $member = isset($members[$i]) ? $members[$i] : null;
            $row['member_' . ($i + 1) . '_first_name'] = $member ? $member->first_name : '';
            $row['member_' . ($i + 1) . '_last_name'] = $member ? $member->last_name : '';
            $row['member_' . ($i + 1) . '_gender'] = $member ? ExportFormats::genderLabel($member->gender) : '';
        }

        return $row;
    }
}
