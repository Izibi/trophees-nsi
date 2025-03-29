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
use App\Models\Prize;
use App\Models\Award;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\SetProjectRatingRequest;
use App\Http\Requests\SetProjectStatusRequest;
use Illuminate\Support\Facades\Storage;
use App\Helpers\SortableTable;
use App\Classes\ActiveContest;
use App\Models\Academy;
use App\Mail\StatusChanged;

class ProjectsController extends Controller
{

    public function __construct(ActiveContest $active_contest)
    {
        $this->contest = $active_contest->get();
    }


    public function index(Request $request)
    {
    	$user = $request->user();
        $views = $this->getUserViews($request);
        $view = $this->selectView($request, $views);

        if(!$view) {
            return view('projects.index.'.$request->user()->role, [
                'view' => null,
            ]);
        }

        $rating_mode_accessible = $view['view_rating'];
        $rating_mode = $request->has('rating_mode');
        if($rating_mode && !$rating_mode_accessible) {
            return redirect('/projects');
        }

        $q = $this->getProjectsQuery($request, $view);
        SortableTable::orderBy($q, $this->getSortFields($request));
        $projects = $q->paginate()->appends($request->all());
        $offset = ($projects->currentPage() - 1) * $projects->perPage();
        foreach($projects as &$project) {
            $offset++;
            $p = parse_url($projects->url($offset));
            $project->view_url = '/project?'.$p['query'].'&refer_page='.urlencode($request->fullUrl());
        }

        $views = array_filter($views, function($v) use ($view) {
            return $v['type'] != $view['type'] || (isset($v['target_id']) && isset($view['target_id']) && $v['target_id'] != $view['target_id']);
        });
        return view('projects.index.'.$request->user()->role, [
            'user' => $user,
            'rows' => $projects,
            'contest' => $this->contest,
            'view' => $view,
            'other_views' => $views,
            'rating_mode_accessible' => $rating_mode_accessible,
            'rating_mode' => $rating_mode,
            'url_rating' => '/projects?'.http_build_query(array_merge($request->all(), ['rating_mode' => 1])),
            'url_nonrating' => '/projects?'.http_build_query(array_merge($request->all(), ['rating_mode' => null])),
            'regions' => Region::orderBy('country_id', 'desc')->orderBy('name')->get()->pluck('name', 'id')->toArray(),
            'awards_count' => $this->countJuryMemberAwards($request),
            'awards_limit' => config('nsi.awards_limit_per_jury_member'),
	        'coordinator' => $user->hasRole('coordinator')
        ]);
    }

    public function redirectPaginated(Request $request, Project $project)
    {
    	$user = $request->user();
        $views = $this->getUserViews($request);
        // Take the type= and id= from the refer_page if it exists
        if($request->has('refer_page')) {
            $p = parse_url($request->get('refer_page'));
            if(isset($p['query'])) {
                $q = [];
                parse_str($p['query'], $q);
                if(isset($q['type']) && isset($q['id'])) {
                    $request->merge($q);
                }
            }
        }
        $view = $this->selectView($request, $views);

        if(!$view) {
            return view('projects.index.'.$request->user()->role, [
                'view' => null,
            ]);
        }

        $q = $this->getProjectsQuery($request, $view);
        SortableTable::orderBy($q, $this->getSortFields($request));
        $projects = $q->paginate()->appends($request->all());
        $offset = ($projects->currentPage() - 1) * $projects->perPage();
        // figure out the view_url as above for the selected project, then redirect
        foreach($projects as $listProject) {
            $offset++;
            if($listProject->id == $project->id) {
                $p = parse_url($projects->url($offset));
                // change refer_page in $p['query'] to be $request->get('refer_page')
                $p['query'] = preg_replace('/refer_page=[^&]+/', 'refer_page='.urlencode($request->get('refer_page')), $p['query']);
                $url = '/project?'.$p['query'];
                return redirect($url);
            }
        }
        return redirect('/projects');
    }

    private function getUserViews(Request $request) {
        $user = $request->user();
        $user_role = $user->role;
        $views = [];

        $phase = $this->contest->status;
        $coordinator = $user->roles()->where('type', 'coordinator')->exists();

        if($user_role == 'teacher') {
            $views[] = ['type' => 'own', 'create' => $phase == 'open', 'edit' => $phase == 'open' || $phase == 'instruction', 'status' => false, 'rate' => false, 'view_rating' => false];
            if($coordinator) {
                $views[] = ['type' => 'region', 'target_id' => $user->region_id, 'name' => Region::find($user->region_id)->name, 'create' => false, 'edit' => false, 'status' => false, 'rate' => false, 'view_rating' => $phase == 'deliberating-territorial'];
            }
        } elseif($user_role == 'jury' && $user->hasRole('teacher')) {
            $views[] = ['type' => 'own', 'create' => $phase == 'open', 'edit' => $phase == 'open' || $phase == 'instruction', 'status' => false, 'rate' => false, 'view_rating' => false];
        } elseif($user_role == 'admin') {
            return [['type' => 'all', 'create' => false, 'edit' => true, 'status' => true, 'rate' => true, 'view_rating' => true]];
        }

        foreach($user->roles as $role) {
            if($role->type == 'territorial' && ($coordinator || $phase == 'grading-territorial' || $phase == 'deliberating-territorial')) {
                $views[] = ['type' => 'region', 'target_id' => $role->target_id, 'name' => Region::find($role->target_id)->name, 'create' => false, 'edit' => false, 'status' => false, 'rate' => $phase == 'grading-territorial', 'view_rating' => $phase == 'deliberating-territorial'];
            }
        }
        foreach($user->roles as $role) {
            if($role->type == 'prize' && ($coordinator || $phase == 'grading-national' || $phase == 'deliberating-national')) {
                $views[] = ['type' => 'prize', 'target_id' => $role->target_id, 'name' => Prize::find($role->target_id)->name, 'create' => false, 'edit' => false, 'status' => false, 'rate' => $phase == 'grading-national', 'view_rating' => $phase == 'deliberating-national'];
            }
        }

        return $views;
    }

    private function selectView(Request $request, $views) {
        $view = null;
        $view_type = $request->get('type');
        $view_target_id = $request->get('id');
        foreach($views as $v) {
            if($v['type'] == $view_type && $v['target_id'] == $view_target_id) {
                $view = $v;
                break;
            }
        }
        if(!$view) {
            if(count($views) > 0) {
                $view = $views[0];
            } else {
                $view = null;
            }
        }
        return $view;
    }

    public function export(Request $request)
    {
        $user = $request->user();
        $coordinator = $user->roles()->where('type', 'coordinator')->exists();
        $phase = $this->contest->status;
        $has_info = false;

        if($user->role == 'admin') {
            $has_notes = true;
            $has_info = true;
            $q = Project::where('contest_id', $this->contest->id);
        } elseif($coordinator) {
            $has_notes = in_array($phase, ['grading-territorial', 'deliberating-territorial', 'grading-national', 'deliberating-national', 'closed']);
            $q = null;
            $views = $this->getUserViews($request);
            foreach($views as $view) {
                if($view['type'] == 'region' || $view['type'] == 'prize') {
                    $q2 = $this->getProjectsQuery($request, $view);
                    if($q) {
                        $q = $q->union($q2);
                    } else {
                        $q = $q2;
                    }
                    continue;
                }
            }
            if(!$q) {
                return redirect('/projects');
            }
        } else {
            return redirect('/projects');
        }
        $q = $q->orderBy('id', 'asc');

        $callback = function() use ($q, $has_notes, $has_info) {
            $fh = fopen('php://output', 'w');
            $header = ['ID', 'Nom', 'Niveau scolaire', 'Statut', 'Enseignant'];
            if($has_info) {
                $header[] = 'Email enseignant';
            }
            $header = array_merge($header, ['Etablissement scolaire', 'Académie']);
            if($has_notes) {
                $header = array_merge($header, ['Nombre de notes', 'Opérationnalité', 'Communication', 'Total']);
            }
            if($has_info) {
                $header = array_merge($header, ['Filles dans l\'équipe', 'Garçons dans l\'équipe', 'Elèves de genre non renseigné dans l\'équipe', 'Filles dans la classe NSI', 'Garçons dans la classe NSI', 'Elèves de genre non renseigné dans la classe NSI', 'Membres de l\'équipe (prénom, nom, genre sur plusieurs colonnes)']);
            }
            fputcsv($fh, $header);

            $q->chunk(500, function($rows) use ($fh, $has_notes, $has_info) {
                foreach($rows as $project) {
                    $row = [
                        $project->id,
                        $project->name,
                        $project->grade ? $project->grade->name : '',
                        $project->status,
                        $project->user->name];
                    if($has_info) {
                        $row[] = $project->user->email;
                    }
                    $row = array_merge($row, [
                        $project->school ? $project->school->name : '',
                        $project->school ? $project->school->academy->name : '',
                    ]);
                    if($has_notes) {
                        $row = array_merge($row, [
                            Rating::where('project_id', '=', $project->id)->count(),
                            $project->score_operationality,
                            $project->score_communication,
                            $project->score_total
                        ]);
                    }
                    if($has_info) {
                        $row = array_merge($row, [
                            $project->team_girls,
                            $project->team_boys,
                            $project->team_not_provided,
                            $project->class_girls,
                            $project->class_boys,
                            $project->class_not_provided
                        ]);
                        foreach($project->team_members as $member) {
                            $row = array_merge($row, [
                                $member->first_name,
                                $member->last_name,
                                $member->gender
                            ]);
                        }
                    }
                    fputcsv($fh, $row);
                }
            });
            fclose($fh);
        };

        $file_name = 'trophees_nsi_projets.csv';
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


    private function getProjectsQuery($request, $view) {
        $user = $request->user();
        $coordinator = $request->get('coordinator') == '1' && !empty($user->region_id);

        $q = Project::select('projects.*');
        $q->where('projects.contest_id', '=', $this->contest->id);
        

        $needsSchoolJoin = false;
        if($view['type'] == 'own') {
            $q->where('projects.user_id', '=', $user->id);
        } else if($view['type'] == 'region') {
            $q->where('schools.region_id', '=', $view['target_id']);
            $needsSchoolJoin = true;
        } else if($view['type'] == 'prize') {
            $q->join('awards', 'projects.id', '=', 'awards.project_id');
            $q->where('awards.prize_id', '=', $view['target_id'])->where('region_id', '!=', 0);
        }

        if($request->has('filter')) {
            $filter_id = trim($request->get('filter_id'));
            if(strlen($filter_id) > 0) {
                $q->where('projects.id', '=', $filter_id);
            }
            $filter_name = trim($request->get('filter_name'));
            if(strlen($filter_name) > 0) {
                $q->where('projects.name', 'LIKE', '%'.$filter_name.'%');
            }
            $filter_school = trim($request->get('filter_school'));
            if(strlen($filter_school) > 0) {
                $q->where('schools.name', 'LIKE', '%'.$filter_school.'%');
                $needsSchoolJoin = true;
            }
            $filter_region_id = trim($request->get('filter_region_id'));
            if($filter_region_id) {
                $q->where('schools.region_id', $filter_region_id);
                $needsSchoolJoin = true;
            }
            $filter_status = trim($request->get('filter_status'));
            if(strlen($filter_status) > 0) {
                $q->where('projects.status', '=', $filter_status);
            }
        }
        if($needsSchoolJoin) {
            $q->join('schools', 'projects.school_id', '=', 'schools.id');
        }
        return $q;
    }


    private function sendMail($project, $oldStatus, $message = null) {
        if($project->status == $oldStatus) {
            return;
        }
        if(!in_array($project->status, ['finalized', 'validated', 'incomplete'])) {
            return;
        }
        $mail = new StatusChanged($project, $message);
        \Mail::to($project->user->email)->send($mail);
    }


    public function create(Request $request)
    {
        $user = $request->user();
        if(!$this->accessible($request, null, 'create')) {
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


    public function store(StoreProjectRequest $request)
    {
        $user = $request->user();
        if(!$this->accessible($request, null, 'create')) {
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
        $this->sendMail($project, null);
        $url = $request->get('refer_page', '/projects');
        return response()->json([
            'location' => $url
        ]);
    }


    private function canAward(Request $request, Project $project) {
        $user = $request->user();
        if($this->contest->status != 'deliberating-territorial' && $this->contest->status != 'deliberating-national') {
            return false;
        }
        if($user->hasRole('president-territorial') && $this->contest->status == 'deliberating-territorial') {
            return true;
        }
        if($user->hasRole('president-prize') && $this->contest->status == 'deliberating-national') {
            foreach($user->roles()->where('type', 'president-prize')->get() as $role) {
                $prize = Prize::find($role->target_id);
                if($prize && $prize->grade_id == $project->grade_id) {
                    return true;
                }
            }
        }
        return false;
    }


    public function show(Request $request, Project $project)
    {
        // this method is not used anymore, but saved for future :)
        $user = $request->user();
        if(!$this->accessible($request, $project)) {
            return $this->accessDeniedResponse();
        }
        $awards = [];
        if($user->role == 'jury' || $user->role == 'admin') {
            $awards = Award::where('project_id', '=', $project->id)->get();
        }
        $can_award = $this->canAward($request, $project);
        $awarded = $can_award && Award::where('project_id', $project->id)->where('user_id', $user->id)->exists();
        $can_rate = $this->accessible($request, $project, 'rate');
        $data = [
            'refer_page' => $request->get('refer_page', '/projects'),
            'project' => $project,
            'projects_paginator' => false,
            'contest' => $this->contest,
            'can_award' => $can_award,
            'awarded' => $awarded,
            'can_rate' => $can_rate,
            'awards' => $awards
        ];
        if($user->role == 'jury') {
            $data['rating'] = Rating::where('project_id', '=', $project->id)->where('user_id', '=', $user->id)->first();
        }
        return view('projects.show.'.$user->role, $data);
    }


    public function showPaginated(Request $request)
    {
        $views = $this->getUserViews($request);
        $view = $this->selectView($request, $views);
        $q = $this->getProjectsQuery($request, $view);
        SortableTable::orderBy($q, $this->getSortFields($request));
        $projects = $q->paginate(1)->appends($request->all());
        if(!count($projects)) {
            return redirect($request->get('refer_page', '/projects'));
        }

        $project = Project::find($projects[0]->id);

        $user = $request->user();
        if(!$this->accessible($request, $project)) {
            return $this->accessDeniedResponse();
        }

        $awards = [];
        if($user->role == 'jury' || $user->role == 'admin') {
            $awards = Award::where('project_id', '=', $project->id)->get();
        }
        $can_award = $this->canAward($request, $project);
        $awarded = $can_award && Award::where('project_id', $project->id)->where('user_id', $user->id)->exists();

        $can_rate = $this->accessible($request, $project, 'rate');
        $data = [
            'refer_page' => $request->get('refer_page', '/projects'),
            'project' => $project,
            'projects_paginator' => $projects,
            'contest' => $this->contest,
            'can_rate' => $can_rate,
            'can_award' => $can_award,
            'awarded' => $awarded,
            'awards' => $awards
        ];
        if($user->role == 'jury') {
            $data['rating'] = Rating::where('project_id', '=', $project->id)->where('user_id', '=', $user->id)->first();
        }
        return view('projects.show.'.$user->role, $data);
    }



    public function edit(Request $request, Project $project)
    {
        $user = $request->user();
        $phase = $this->contest->status;
        if(!$this->accessible($request, $project, 'edit')) {
            return $this->accessDeniedResponse();
        }
        if($user->role == 'teacher' &&
            (!in_array($project->status, ['draft', 'incomplete']) ||
            ($project->status == 'draft' && $phase != 'open')) ||
            ($project->status == 'incomplete' && !in_array($phase, ['open', 'instruction']))) {
            return redirect('/projects');
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


    public function update(StoreProjectRequest $request, Project $project)
    {
        $user = $request->user();
        if(!$this->accessible($request, $project, 'edit')) {
            return $this->accessDeniedResponse();
        }
        if($user->role == 'teacher' && !in_array($project->status, ['draft', 'incomplete'])) {
            return redirect('/projects');
        }

        $oldStatus = $project->status;
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
        $this->sendMail($project, $oldStatus);
        session()->flash('message', 'Project enregistré');
        $url = $request->get('refer_page', '/projects');
        return response()->json([
            'location' => $url
        ]);
    }


    public function setRating(SetProjectRatingRequest $request, Project $project)
    {
        $user = $request->user();
        if(!$this->accessible($request, $project, 'rate')) {
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
        if(!$this->accessible($request, $project, 'status')) {
            return $this->accessDeniedResponse();
        }
        $oldStatus = $project->status;
        $project->status = $request->get('status');
        $project->save();
        $this->sendMail($project, $oldStatus, $request->get('message'));
        //$url = $request->get('refer_page', '/projects');
        return redirect()->back()->withMessage('Statut enregistré');
    }


    public function destroy(Request $request, Project $project)
    {
        if(!$this->accessible($request, $project, 'edit')) {
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


    private function accessible($request, $project = null, $action = null) {
        $views = $this->getUserViews($request);
        foreach($views as $view) {
            if($action && !$view[$action]) continue;
            if(!$project) return true;

            if($this->getProjectsQuery($request, $view)->where('projects.id', '=', $project->id)->count()) {
                return true;
            }
        }
        return false;
    }


    private function accessDeniedResponse() {
        return redirect('/');
    }


    private function finalizeProject(&$project, $request) {
        $errors = [];
        $config = config('nsi.project');

        $team_size = $project->team_girls + $project->team_boys + $project->team_not_provided;
        if($team_size == 0) {
            $errors[] = 'Les membres de l\'équipe ne sont pas renseignés.';
        } elseif($team_size < $config['team_size_min'] || $team_size > $config['team_size_max']) {
            $errors[] = strtr('La taille totale de l\'équipe doit être entre team_size_min et team_size_max.', $config);
        }

        if(empty($project->image_file)) {
            $errors[] = 'Image manquante.';
        }
        foreach($project->team_members as $team_member) {
            if(empty($team_member->parental_permissions_file)) {
                $errors[] = 'Autorisations parentales manquantes.';
                break;
            }
            if(empty($team_member->first_name) || empty($team_member->last_name)) {
    		    $errors[] = 'Il manque des informations dans la liste des membres de l\'équipe.';
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

        $res = DB::table('ratings')
            ->select(DB::raw('
                SUM(ratings.award_mixed) as award_mixed,
                SUM(ratings.award_citizenship) as award_citizenship,
                SUM(ratings.award_engineering) as award_engineering,
                SUM(ratings.award_heart) as award_heart,
                SUM(ratings.award_originality) as award_originality,
                COUNT(*) as rows_total
            '))
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
