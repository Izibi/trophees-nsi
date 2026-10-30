<?php

namespace App\Exports;

use App\Models\Award;
use App\Models\Contest;
use App\Models\Region;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * Rows of the Lauréats export (import file for the new website): one row per
 * awarded project and award — a project with several awards appears on
 * several rows — sorted by award (prize, then region) then project ID.
 */
class AwardsExportBuilder
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
     * @return \Generator rows keyed by column key
     */
    public function rows()
    {
        $contestId = $this->contest->id;

        $q = Award::with([
                'prize',
                'project.user',
                'project.school',
                'project.team_members' => function ($query) {
                    $query->orderBy('id');
                },
            ])
            ->where('contest_id', $contestId)
            ->orderBy('prize_id')
            ->orderBy('region_id')
            ->orderBy('project_id');

        // Same scoping as the legacy /awards/export link: admins export every
        // award of the contest, other roles only the awards they granted.
        if ($this->user->role != 'admin') {
            $q->where('user_id', $this->user->id);
        }

        $regionNames = Region::pluck('name', 'id');

        $page = 1;
        do {
            $awards = $q->forPage($page, 500)->get();

            $projectIds = $awards->pluck('project_id')->unique()->values()->all();
            $byProject = [];
            if (count($projectIds)) {
                $byProject = Award::with('prize')
                    ->whereIn('project_id', $projectIds)
                    ->where('contest_id', $contestId)
                    ->orderBy('prize_id')
                    ->orderBy('region_id')
                    ->get()
                    ->groupBy('project_id');
            }

            foreach ($awards as $award) {
                yield $this->buildRow($award, $byProject, $regionNames);
            }

            $page++;
        } while (count($awards) == 500);
    }

    /**
     * @param Award       $award     the award this row belongs to
     * @param \Illuminate\Support\Collection|array $byProject all contest awards keyed by project ID
     * @param \Illuminate\Support\Collection $regionNames region id => name
     * @return array row keyed by column key
     */
    private function buildRow(Award $award, $byProject, $regionNames)
    {
        $project = $award->project;
        $school = $project ? $project->school : null;
        $isNational = $award->region_id == 0;

        // This row's own prize goes in its own phase column; the other phase
        // column carries the project's awards of the other phase, if any.
        $ownPrize = $award->prize ? $award->prize->name : '';
        $otherPhasePrizes = [];
        $allAwards = isset($byProject[$award->project_id]) ? $byProject[$award->project_id] : [];
        foreach ($allAwards as $other) {
            if (!$other->prize) {
                continue;
            }
            if ($isNational && $other->region_id != 0) {
                $otherPhasePrizes[] = $other->prize->name;
            } elseif (!$isNational && $other->region_id == 0) {
                $otherPhasePrizes[] = $other->prize->name;
            }
        }

        $row = [
            'project_name' => $project ? $project->name : '',
            'territory' => $isNational
                ? 'National'
                : $regionNames->get($award->region_id, ''),
            'school_name' => $school ? $school->name : '',
            'school_city' => $school ? ($school->city ?? '') : '',
            'teacher_name' => ($project && $project->user) ? $project->user->name : '',
            'terr_prize' => $isNational ? ExportFormats::joinValues($otherPhasePrizes) : $ownPrize,
            'nat_prize' => $isNational ? $ownPrize : ExportFormats::joinValues($otherPhasePrizes),
            'description' => $project ? ($project->description ?? '') : '',
            'video' => $project ? ($project->video ?? '') : '',
            'image' => ($project && $project->image_file)
                ? Storage::disk('uploads')->url($project->image_file)
                : '',
        ];

        $members = $project ? $project->team_members->values() : collect();
        for ($i = 0; $i < 4; $i++) {
            $member = isset($members[$i]) ? $members[$i] : null;
            $row['member_' . ($i + 1)] = $member ? ExportFormats::memberFullName($member) : '';
        }

        return $row;
    }
}
