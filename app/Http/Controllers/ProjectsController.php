<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
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

class ProjectsController extends Controller
{

    public function __construct(ActiveContest $active_contest)
    {
        $this->contest = $active_contest->get();
    }


    public function index(Request $request)
    {
        $q = $this->getProjectsQuery($request);
        SortableTable::orderBy($q, $this->getSortFields($request));
        $projects = $q->paginate()->appends($request->all());
        return view('projects.index.'.$request->user()->role, [
            'rows' => $projects
        ]);
    }


    private function getSortFields($request) {
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
        $res['created_at'] = 'projects.created_at';
        $res['status'] = 'projects.status';
        return $res;
    }


    private function getProjectsQuery($request) {
        $user = $request->user();

        $q = DB::table('projects');

        if($user->role == 'teacher') {
            $q->select(DB::raw('projects.id, projects.name, schools.name as school_name, projects.created_at, projects.status'));
            $q->leftJoin('schools', 'projects.school_id', '=', 'schools.id');
            $q->where('projects.user_id', '=', $user->id);
        } else if($user->role == 'jury') {
            $q->select(DB::raw('projects.id, projects.name, projects.created_at, projects.status'));
            $q->leftJoin('schools', 'projects.school_id', '=', 'schools.id');
            $q->where('schools.region_id', '=', $user->region_id);
            $q->where('projects.status', '=', 'validated');
        } else if($user->role == 'admin') {
            $q->select(DB::raw('projects.id, projects.name, schools.name as school_name, users.name as user_name, regions.name as region_name, projects.created_at, projects.status'));
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
            $filter_region = $request->get('filter_region');
            if(strlen($filter_region) > 0) {
                $q->where('regions.name', 'LIKE', '%'.$filter_region.'%');
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
        return view('projects.edit', [
            'project' => null,
            'schools' => $this->getUserSchools($user),
            'grades' => Grade::orderBy('name')->get(),
            'countries' => Country::orderBy('name')->get(),
            'regions' => Region::orderBy('country_id', 'desc')->orderBy('name')->get(),
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
        if($request->has('finalize')) {
            $res = $this->finalizeProject($project, $request);
            if($res !== true) {
                return $res;
            }
        }
        $url = $request->get('refer_page', '/projects');
        return redirect($url)->withMessage('Project created');
    }


    public function show(Request $request, Project $project)
    {
        $user = $request->user();
        if(!$this->accessible($user, $project, 'view')) {
            return $this->accessDeniedResponse();
        }
        $data = [
            'refer_page' => $request->get('refer_page', '/projects'),
            'project' => $project
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
        return view('projects.edit', [
            'submit_route' => 'projects.update',
            'project' => $project,
            'schools' => $this->getUserSchools($user),
            'grades' => Grade::orderBy('name')->get(),
            'countries' => Country::orderBy('name')->get(),
            'regions' => Region::orderBy('country_id', 'desc')->orderBy('name')->get(),
            'refer_page' => $request->get('refer_page', '/projects')
        ]);
    }


    public function update(StorePojectRequest $request, Project $project)
    {
        if(!$this->accessible($request->user(), $project, 'edit')) {
            return $this->accessDeniedResponse();
        }
        $project->uploadFiles($request);
        $project->fill($request->all());
        $project->save();

        if($request->has('finalize')) {
            $res = $this->finalizeProject($project, $request);
            if($res !== true) {
                return $res;
            }
        }

        $url = $request->get('refer_page', '/projects');
        return redirect($url)->withMessage('Project updated');
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
        $url = $request->get('refer_page', '/projects');
        return redirect($url)->withMessage('Rating updated');
    }


    public function setStatus(SetProjectStatusRequest $request, Project $project)
    {
        $user = $request->user();
        if(!$this->accessible($user, $project, 'change_status')) {
            return $this->accessDeniedResponse();
        }
        $project->status = $request->get('status');
        $project->save();
        $url = $request->get('refer_page', '/projects');
        return redirect($url)->withMessage('Status updated');
    }


    public function destroy(Request $request, Project $project)
    {
        if(!$this->accessible($request->user(), $project, 'edit')) {
            return $this->accessDeniedResponse();
        }
        $project->delete();
        $url = $request->get('refer_page', '/projects');
        return redirect($url)->withMessage('Project deleted');
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
                return $user->role == 'teacher';
                break;
            case 'view':
                return $user->role == 'admin' ||
                    ($user->role == 'teacher' && $user->id == $project->user_id) ||
                    ($user->role == 'jury' && $user->region_id == $project->school->region_id);
                break;
            case 'edit':
                return $user->role == 'teacher' && $user->id == $project->user_id && $project->status == 'draft';
                break;
            case 'change_status':
                return $user->role == 'admin';
                break;
            case 'rate':
                return $user->role == 'jury' && $user->region_id == $project->school->region_id && $project->status == 'validated';
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
            $errors[] = strtr('Total size of the team must be between team_size_min and team_size_max', $config);
        }

        if(empty($project->presentation_file)) {
            $errors[] = 'Presentation PDF not uploaded.';
        }
        if(empty($project->zip_file)) {
            $errors[] = 'Zip of project not uploaded.';
        }
        if(empty($project->image_file)) {
            $errors[] = 'Image not uploaded.';
        }

        if(count($errors)) {
            return redirect()->route('projects.edit', $project)->withError($errors);
        }

        $project->status = 'finalized';
        $project->save();
        return true;
    }

}
