<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\School;
use App\Models\Grade;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StorePojectRequest;
use App\Http\Requests\SetRatingRequest;
use Illuminate\Support\Facades\Storage;
use App\Helpers\SortableTable;

class ProjectsController extends Controller
{


    private $sort_fields = [
        'id' => 'projects.id',
        'name' => 'projects.name',
        'school_name' => 'schools.name',
        'region_name' => 'regions.name',
        'created_at' => 'projects.created_at',
        'status' => 'projects.status'
    ];


    public function index(Request $request)
    {
        $q = DB::table('projects')
            ->select(DB::raw('projects.id, projects.name, schools.name as school_name, regions.name as region_name, projects.created_at, projects.status'))
            ->leftJoin('schools', 'projects.school_id', '=', 'schools.id')
            ->leftJoin('regions', 'schools.region_id', '=', 'regions.id');
        SortableTable::orderBy($q, $this->sort_fields);
        $projects = $q->paginate()->appends($request->all());
        return view('projects.index', [
            'rows' => $projects
        ]);
    }


    public function create(Request $request)
    {
        return view('projects.edit', [
            'project' => null,
            'schools' => $this->getUserSchools($request->user()),
            'grades' => Grade::get()->pluck('name', 'id')->toArray(),            
            'refer_page' => $request->get('refer_page', '/projects')
        ]);
    }


    public function store(StorePojectRequest $request)
    {
        $project = new Project($request->all());
        if($request->hasFile('presentation_file')) {
            $file = $request->file('presentation_file');
            $project->presentation_file = $file->hashName();
            $file->storeAs('/', $project->presentation_file, 'uploads');
        }
        if($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $project->image_file = $file->hashName();
            $file->storeAs('/', $project->image_file, 'uploads');
        }        
        $project->user_id = $request->user()->id;
        $project->save();
        $url = $request->get('refer_page', '/projects');
        return redirect($url)->withMessage('Project created');
    }


    public function show(Request $request, Project $project)
    {
        $data = [
            'refer_page' => $request->get('refer_page', '/projects'),
            'project' => $project
        ];
        $user = $request->user();
        if($user->role == 'jury') {
            $data['rating'] = Rating::where('project_id', '=', $project->id)->where('user_id', '=', $user->id)->first();
        }
        return view('projects.show-'.$user->role, $data);
    }


    public function edit(Request $request, Project $project)
    {
        return view('projects.edit', [
            'submit_route' => 'projects.update',
            'project' => $project,
            'schools' => $this->getUserSchools($request->user()),
            'grades' => Grade::get()->pluck('name', 'id')->toArray(),
            'refer_page' => $request->get('refer_page', '/projects')
        ]);
    }


    public function update(StorePojectRequest $request, Project $project)
    {
        if($request->hasFile('presentation_file')) {
            $file = $request->file('presentation_file');
            $project->presentation_file = $file->hashName();
            $file->storeAs('/', $project->presentation_file, 'uploads');
        }
        if($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $project->image_file = $file->hashName();
            $file->storeAs('/', $project->image_file, 'uploads');
        }

        $project->fill($request->all());
        $project->save();
        $url = $request->get('refer_page', '/projects');
        return redirect($url)->withMessage('Project updated');        
    }


    public function setRating(SetRatingRequest $request, Project $project)
    {
        $user = $request->user();
        $rating = Rating::where('project_id', '=', $project->id)->where('user_id', '=', $user->id)->first();
        if(!$rating) {
            $rating = new Rating([
                'user_id' => $user->id,
                'project_id' => $project->id
            ]);
        }
        $rating->fill($request->all());
        $rating->save();
        return redirect()->back()->withMessage('Rating updated');        
    }    


    public function destroy(Request $request, Project $project)
    {
        $project->delete();
        $url = $request->get('refer_page', '/projects');
        return redirect($url)->withMessage('Project deleted');
    }



    private function getUserSchools($user) {
        $data = $user->schools()->with('country', 'region')->get();
        $options = [];
        foreach($data as $school) {
            $options[$school->id] = $school->name.', '.$school->zip.' '.$school->city.', '.$school->country->name;
        }
        return [
            'data' => $data,
            'options' => $options
        ];
    }
}
