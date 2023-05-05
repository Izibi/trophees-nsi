<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\School;
use App\Models\Grade;
use App\Models\Rating;
use App\Models\Country;
use App\Models\Region;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StorePojectRequest;
use App\Http\Requests\SetProjectRatingRequest;
use App\Http\Requests\SetProjectStatusRequest;
use Illuminate\Support\Facades\Storage;
use App\Helpers\SortableTable;
use App\Classes\ActiveContest;
use App\Models\Academy;

class ProjectsController extends Controller
{

    public function __construct(ActiveContest $active_contest)
    {
        $this->contest = $active_contest->get();
    }


    public function index(Request $request)
    {
        $rating_mode_accessible = $this->accessible($request->user(), null, 'view_projects_rating');
        $rating_mode = $request->has('rating_mode');
        if($rating_mode && !$rating_mode_accessible) {
            return redirect('/projects');
        }
        $q = $this->getProjectsQuery($request);
        SortableTable::orderBy($q, $this->getSortFields($request));
        $projects = $q->paginate()->appends($request->all());
        $offset = ($projects->currentPage() - 1) * $projects->perPage();
        foreach($projects as &$project) {
            $offset++;
            $p = parse_url($projects->url($offset));
            $project->view_url = '/project?'.$p['query'].'&refer_page='.urlencode($request->fullUrl());
        }

        return view('projects.index.'.$request->user()->role, [
            'rows' => $projects,
            'contest' => $this->contest,
            'rating_mode_accessible' => $rating_mode_accessible,
            'rating_mode' => $rating_mode,
            'regions' => Region::orderBy('country_id', 'desc')->orderBy('name')->get()->pluck('name', 'id')->toArray(),
            'awards_count' => $this->countJuryMemberAwards($request),
            'awards_limit' => config('nsi.awards_limit_per_jury_member'),
            'coordinator' => $request->get('coordinator') == '1'
        ]);
    }

    public function export(Request $request)
    {
        $rating_mode_accessible = $this->accessible($request->user(), null, 'view_projects_rating');
        if(!$rating_mode_accessible) {
            abort(403);
        }
        $q = $this->getProjectsQuery($request)->orderBy('id');

        $callback = function() use ($q) {
            $fh = fopen('php://output', 'w');
            $header = ['Nom', 'Nombre de notes', 'Idée', 'Communication', 'Presentation', 'Image', 'Logique', 'Créativité', 'Organisation', 'Opérationnalité', 'Ouverture', 'Total', 'Mixité', 'Citoyenneté', 'Ingénierie', 'Coup de coeur', 'Originalité'];
            fputcsv($fh, $header);

            $q->chunk(500, function($rows) use ($fh) {
                foreach($rows as $project) {
                    $row = [
                        $project->name,
                        $project->ratings_amount,
                        $project->score_idea,
                        $project->score_communication,
                        $project->score_presentation,
                        $project->score_image,
                        $project->score_logic,
                        $project->score_creativity,
                        $project->score_organisation,
                        $project->score_operationality,
                        $project->score_ouverture,
                        $project->score_total,
                        $project->award_mixed,
                        $project->award_citizenship,
                        $project->award_engineering,
                        $project->award_heart,
                        $project->award_originality,
                    ];
                    fputcsv($fh, $row);
                }
            });
            fclose($fh);
        };

        $file_name = 'trophees_nsi_projects.csv';
        $headers = array(
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename='.$file_name,
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        );
        return response()->stream($callback, 200, $headers);
    }


    private function getSortFields($request) {
        if($request->has('rating_mode')) {
            return $this->getSortFieldsRatingMode($request);
        } else {
            return $this->getSortFieldsDetailsMode($request);
        }
    }


    private function getSortFieldsRatingMode($request) {
        return [
            'name' => 'projects.name',
            'score_total' => 'projects.score_total',
            'score_idea' => 'projects.score_idea',
            'score_communication' => 'projects.score_communication',
            'score_presentation' => 'projects.score_presentation',
            'score_image' => 'projects.score_image',
            'score_logic' => 'projects.score_logic',
            'score_creativity' => 'projects.score_creativity',
            'score_organisation' => 'projects.score_organisation',
            'score_operationality' => 'projects.score_operationality',
            'score_ouverture' => 'projects.score_ouverture',
            'ratings_amount' => 'projects.ratings_amount',
            'award_mixed' => 'projects.award_mixed',
            'award_citizenship' => 'projects.award_citizenship',
            'award_engineering' => 'projects.award_engineering',
            'award_heart' => 'projects.award_heart',
            'award_originality' => 'projects.award_originality'
        ];
    }


    private function getSortFieldsDetailsMode($request) {
        $user_role = $request->user()->role;
        $res = [];
        $res['id'] = 'projects.id';
        $res['name'] = 'projects.name';
        if($user_role == 'teacher' || $user_role == 'admin') {
            $res['school_name'] = 'schools.name';
        }
        if($user_role == 'admin') {
            $res['region_name'] = 'regions.name';
            $res['user_name'] = 'users.name';
        }
        if($user_role == 'jury') {
            $res['rating_published'] = 'ratings.published';
        }
        $res['created_at'] = 'projects.created_at';
        $res['status'] = 'projects.status';
        return $res;
    }


    private function getProjectsQuery($request) {
        $user = $request->user();
        $coordinator = $request->get('coordinator') == '1' && !empty($user->region_id);

        $q = DB::table('projects');
        $q->where('projects.contest_id', '=', $this->contest->id);

        if($user->role == 'teacher') {
            $q->select(DB::raw('projects.*, schools.name as school_name'));
            $q->leftJoin('schools', 'projects.school_id', '=', 'schools.id');
            if($coordinator) {
                $q->where('schools.region_id', $user->region_id);
            } else {
                $q->where('projects.user_id', '=', $user->id);
            }
        } else if($user->role == 'jury') {
            $q->select(DB::raw('projects.*, ratings.published as rating_published'));
            $q->leftJoin('schools', 'projects.school_id', '=', 'schools.id');
            $q->where(function($sq) use ($user) {
                $sq->where('schools.region_id', '=', $user->region_id);
                if($user->charge_prize_id) {
                    $sq->orWhere('projects.prize_id', '=', $user->charge_prize_id);
                }
            });
            $q->where('projects.status', '=', 'validated');
            $q->leftJoin('ratings', function($join) use ($user) {
                $join->on('projects.id', '=', 'ratings.project_id');
                $join->on(DB::raw($user->id), '=', 'ratings.user_id');
            });
        } else if($user->role == 'admin') {
            $q->select(DB::raw('projects.*, schools.name as school_name, users.name as user_name, regions.name as region_name'));
            $q->leftJoin('schools', 'projects.school_id', '=', 'schools.id');
            $q->leftJoin('users', 'projects.user_id', '=', 'users.id');
            $q->leftJoin('regions', 'schools.region_id', '=', 'regions.id');
        }

        if($request->has('filter')) {
            $filter_id = $request->get('filter_id');
            if(strlen($filter_id) > 0) {
                $q->where('projects.id', '=', $filter_id);
            }
            $filter_name = $request->get('filter_name');
            if(strlen($filter_name) > 0) {
                $q->where('projects.name', 'LIKE', '%'.$filter_name.'%');
            }
            $filter_school = $request->get('filter_school');
            if(strlen($filter_school) > 0) {
                $q->where('schools.name', 'LIKE', '%'.$filter_school.'%');
            }
            $filter_region_id = $request->get('filter_region_id');
            if($filter_region_id) {
                $q->where('schools.region_id', $filter_region_id);
            }
            $filter_status = $request->get('filter_status');
            if(strlen($filter_status) > 0) {
                $q->where('projects.status', '=', $filter_status);
            }
        }
        return $q;
    }



    public function create(Request $request)
    {
        $user = $request->user();
        if(!$this->accessible($user, null, 'create')) {
            return $this->accessDeniedResponse();
        }
        return view('projects.edit.index', [
            'project' => null,
            'schools' => $this->getUserSchools($user),
            'grades' => Grade::orderBy('name')->get(),
            'countries' => Country::orderBy('name')->get(),
            'regions' => Region::orderBy('country_id', 'desc')->orderBy('name')->get(),
            'academies' => Academy::orderBy('name')->get(),
            'refer_page' => $request->get('refer_page', '/projects')
        ]);
    }


    public function store(StorePojectRequest $request)
    {
        $user = $request->user();
        if(!$this->accessible($user, null, 'create')) {
            return $this->accessDeniedResponse();
        }
        $project = new Project($request->all());
        $project->uploadFiles($request);
        $project->user_id = $user->id;
        $project->contest_id = $this->contest->id;
        $project->save();
        $this->syncTeamMembers($project, $request);
        if($request->has('finalize')) {
            $res = $this->finalizeProject($project, $request);
            if($res !== true) {
                return $res;
            }
        }
        session()->flash('message', 'Projet créé');
        $url = $request->get('refer_page', '/projects');
        return response()->json([
            'location' => $url
        ]);
    }


    public function show(Request $request, Project $project)
    {
        // this method is not used anymore, but saved for future :)
        $user = $request->user();
        if(!$this->accessible($user, $project, 'view')) {
            return $this->accessDeniedResponse();
        }
        $data = [
            'refer_page' => $request->get('refer_page', '/projects'),
            'project' => $project,
            'projects_paginator' => false,
            'contest' => $this->contest
        ];
        if($user->role == 'jury') {
            $data['rating'] = Rating::where('project_id', '=', $project->id)->where('user_id', '=', $user->id)->first();
        }
        return view('projects.show.'.$user->role, $data);
    }


    public function showPaginated(Request $request)
    {
        $q = $this->getProjectsQuery($request);
        SortableTable::orderBy($q, $this->getSortFields($request));
        $projects = $q->paginate(1)->appends($request->all());
        if(!count($projects)) {
            return redirect($request->get('refer_page', '/projects'));
        }

        $project = Project::find($projects[0]->id);

        $user = $request->user();
        if(!$this->accessible($user, $project, 'view')) {
            return $this->accessDeniedResponse();
        }
        $data = [
            'refer_page' => $request->get('refer_page', '/projects'),
            'project' => $project,
            'projects_paginator' => $projects,
            'contest' => $this->contest
        ];
        if($user->role == 'jury') {
            $data['rating'] = Rating::where('project_id', '=', $project->id)->where('user_id', '=', $user->id)->first();
        }
        return view('projects.show.'.$user->role, $data);
    }



    public function edit(Request $request, Project $project)
    {
        $user = $request->user();
        if(!$this->accessible($user, $project, 'edit')) {
            return $this->accessDeniedResponse();
        }
        return view('projects.edit.index', [
            'submit_route' => 'projects.update',
            'project' => $project,
            'schools' => $this->getUserSchools($user->role != 'admin' ? $user : $project->user),
            'grades' => Grade::orderBy('name')->get(),
            'countries' => Country::orderBy('name')->get(),
            'regions' => Region::orderBy('country_id', 'desc')->orderBy('name')->get(),
            'academies' => Academy::orderBy('name')->get(),
            'refer_page' => $request->get('refer_page', '/projects')
        ]);
    }


    public function update(StorePojectRequest $request, Project $project)
    {
        if(!$this->accessible($request->user(), $project, 'edit')) {
            return $this->accessDeniedResponse();
        }

        $this->deleteUploads($project, $request);
        $project->uploadFiles($request);
        $project->fill($request->all());
        $project->save();
        $this->syncTeamMembers($project, $request);

        if($request->has('finalize')) {
            $res = $this->finalizeProject($project, $request);
            if($res !== true) {
                return $res;
            }
        }
        session()->flash('message', 'Project enregistré');
        $url = $request->get('refer_page', '/projects');
        return response()->json([
            'location' => $url
        ]);
    }


    public function setRating(SetProjectRatingRequest $request, Project $project)
    {
        $user = $request->user();
        if(!$this->accessible($user, $project, 'rate')) {
            return $this->accessDeniedResponse();
        }
        $rating = Rating::where('project_id', '=', $project->id)->where('user_id', '=', $user->id)->first();
        if(!$rating) {
            $rating = new Rating([
                'user_id' => $user->id,
                'project_id' => $project->id
            ]);
        }
        $rating->fill($request->all());
        $rating->save();
        //$url = $request->get('refer_page', '/projects');
        return redirect()->back()->withMessage('Notes enregistrées');
    }


    public function setStatus(SetProjectStatusRequest $request, Project $project)
    {
        $user = $request->user();
        if(!$this->accessible($user, $project, 'change_status')) {
            return $this->accessDeniedResponse();
        }
        $project->status = $request->get('status');
        $project->save();
        //$url = $request->get('refer_page', '/projects');
        return redirect()->back()->withMessage('Statut enregistré');
    }


    public function destroy(Request $request, Project $project)
    {
        if(!$this->accessible($request->user(), $project, 'edit')) {
            return $this->accessDeniedResponse();
        }
        $project->delete();
        $url = $request->get('refer_page', '/projects');
        return redirect($url)->withMessage('Projet supprimé');
    }


    private function getUserSchools($user) {
        $data = $user->schools()->with('country', 'region')->get();
        $options = [];
        foreach($data as $school) {
            $title = $school->name.', '.$school->zip.' '.$school->city.', ';
            if(!is_null($school->region->country_id)) {
                $title .= $school->region->name.', ';
            }
            $title .= $school->country->name;
            $options[$school->id] = $title;
        }
        return [
            'data' => $data,
            'options' => $options
        ];
    }


    private function accessible($user, $project, $action) {
        switch($action) {
            case 'create':
                return $user->role == 'teacher' && $this->contest->status == 'open';
                break;
            case 'view':
                return $user->role == 'admin' ||
                    ($user->role == 'teacher' && $user->id == $project->user_id) ||
                    ($user->coordinator && $user->region_id && $user->region_id === $project->school->region_id) ||
                    ($user->role == 'jury' &&
                        $user->region_id == $project->school->region_id &&
                        $project->status != 'masked' &&
                        $project->contest_id == $this->contest->id &&
                        ($this->contest->status == 'grading' || $this->contest->status == 'deliberating' || $this->contest->status == 'closed')
                    );
                break;
            case 'edit':
                return
                    $user->role == 'admin' ||
                    ($user->role == 'teacher' &&
                    $user->id == $project->user_id &&
                    $project->status == 'draft' &&
                    $project->contest_id == $this->contest->id &&
                    $this->contest->status == 'open');
                break;
            case 'change_status':
                return $user->role == 'admin';
                break;
            case 'rate':
                return $user->role == 'jury' &&
                    $user->region_id == $project->school->region_id &&
                    $project->status == 'validated' &&
                    $project->contest_id == $this->contest->id &&
                    ($this->contest->status == 'grading' || $this->contest->status == 'deliberating');
                break;
            case 'view_projects_rating':
                return
                    $user->role == 'admin' ||
                    ($user->role == 'jury' && $this->contest->status == 'deliberating');
                break;
        }
    }


    private function accessDeniedResponse() {
        return redirect('/');
    }



    private function finalizeProject(&$project, $request) {
        $errors = [];
        $config = config('nsi.project');

        $team_size = $project->team_girls + $project->team_boys + $project->team_not_provided;
        if($team_size < $config['team_size_min'] || $team_size > $config['team_size_max']) {
//            $errors[] = strtr('Total size of the team must be between team_size_min and team_size_max', $config);
            $errors[] = strtr('La taille totale de l\'équipe doit être entre team_size_min et team_size_max', $config);
        }

        if(empty($project->presentation_file)) {
//            $errors[] = 'Presentation PDF not uploaded.';
            $errors[] = 'PDF de présentation manquant.';
        }
        if(empty($project->image_file)) {
//            $errors[] = 'Image not uploaded.';
            $errors[] = 'Image manquante.';
        }
        foreach($project->team_members as $team_member) {
            if(empty($team_member->parental_permissions_file)) {
                $errors[] = 'Autorisations parentales manquantes.';
                break;
            }
            if(empty($team_member->first_name) || empty($team_member->last_name)) {
                $errors[] = 'Team members list is not completed.';
                break;
            }
        }
        if(empty($project->url)) {
            $errors[] = 'URL du projet manquant.';
        }
        if(count($errors)) {
            return response()->json([
                'finalization_errors' => $errors
            ]);
        }

        $project->status = 'finalized';
        $project->save();
        return true;
    }


    private function countJuryMemberAwards($request) {
        $user = $request->user();
        $right_contest_mode = $this->contest->status == 'grading' || $this->contest->status == 'deliberating';
        if($user->role !== 'jury' || !$right_contest_mode) {
            return false;
        }

        $res = DB::table('projects')
            ->select(DB::raw('
                SUM(projects.award_mixed) as award_mixed,
                SUM(projects.award_citizenship) as award_citizenship,
                SUM(projects.award_engineering) as award_engineering,
                SUM(projects.award_heart) as award_heart,
                SUM(projects.award_originality) as award_originality,
                COUNT(*) as rows_total
            '))
            ->leftJoin('ratings', 'ratings.project_id', '=', 'projects.id')
            ->where('projects.contest_id', '=', $this->contest->id)
            ->where('ratings.user_id', '=', $user->id)
            ->first();
        if(!$res || !$res->rows_total) {
            return false;
        }
        return (array) $res;
    }


    private function syncTeamMembers($project, $request) {
        $members = $project->team_members->keyBy('id')->all();

        $team_member_id = $request->get('team_member_id') ?? [];
        $parental_permissions_file = $request->file('team_member_parental_permissions_file');

        $saved_ids = [];

        $project->team_girls = 0;
        $project->team_boys = 0;
        $project->team_not_provided = 0;
        for($i=0; $i<count($team_member_id); $i++) {
            $data = [
                'project_id' => $project->id,
                'first_name' => $request->get('team_member_first_name')[$i] ?? '',
                'last_name' => $request->get('team_member_last_name')[$i] ?? '',
                'gender' => $request->get('team_member_gender')[$i] ?? ''
            ];
            $id = $team_member_id[$i];

            if(empty($id)) {
                $member = new TeamMember($data);
            } else if(isset($members[$id])) {
                $saved_ids[$id] = true;
                $member = $members[$id];
                $member->fill($data);
            }
            if(isset($parental_permissions_file[$i])) {
                $member->uploadFile($request->file('team_member_parental_permissions_file')[$i]);
            }
            $member->save();

            if($member->gender == 'male') {
                $project->team_boys++;
            } else if($member->gender == 'female') {
                $project->team_girls++;
            } else {
                $project->team_not_provided++;
            }
        }
        $project->save();

        foreach($members as $member) {
            if(!isset($saved_ids[$member->id])) {
                $member->delete();
            }
        }
    }


    private function deleteUploads($project, $request) {
        if($request->has('delete_uploads')) {
            $to_delete = $request->get('delete_uploads');
            foreach($to_delete as $attr) {
                list($target, $pointer) = explode('.', $attr);
                if($target == 'project') {
                    $project->$pointer = null;
                } else if($target == 'team_member') {
                    $team_member = $project->team_members()->where('id', $pointer)->first();
                    $team_member->parental_permissions_file = null;
                    $team_member->save();
                }
            }
        }
    }

}
