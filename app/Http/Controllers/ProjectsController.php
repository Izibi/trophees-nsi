<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\School;
use App\Models\Grade;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StorePojectRequest;
use Illuminate\Support\Facades\Storage;

class ProjectsController extends Controller
{


    public function index(Request $request)
    {
        $projects = DB::table('projects')
            ->select(DB::raw('projects.id, projects.name, schools.name as school_name, regions.name as region_name, projects.created_at, projects.status'))
            ->leftJoin('schools', 'projects.school_id', '=', 'schools.id')
            ->leftJoin('regions', 'schools.region_id', '=', 'regions.id')            
            ->paginate()
            ->appends($request->all());
        return view('projects.index', [
            'rows' => $projects
        ]);
    }


    public function create(Request $request)
    {
        return view('projects.edit', [
            'project' => null,
            'schools' => School::get()->pluck('name', 'id')->toArray(),
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
            $file->storeAs('/', $project->presentation_file, 'presentation_files');
        }
        $project->save();
        $url = $request->get('refer_page', '/projects');
        return redirect($url)->withMessage('Project created');
    }


    public function show(Project $project)
    {
        return view('projects.view', [
            'project' => $project
        ]);
    }


    public function edit(Request $request, Project $project)
    {
        return view('projects.edit', [
            'submit_route' => 'projects.update',
            'project' => $project,
            'schools' => School::get()->pluck('name', 'id')->toArray(),
            'grades' => Grade::get()->pluck('name', 'id')->toArray(),
            'refer_page' => $request->get('refer_page', '/projects')
        ]);
    }


    public function update(StorePojectRequest $request, Project $project)
    {
        if($request->hasFile('presentation_file')) {
            $file = $request->file('presentation_file');
            $project->presentation_file = $file->hashName();
            $file->storeAs('/', $project->presentation_file, 'presentation_files');
        }

        $project->fill($request->all());
        $project->save();
        $url = $request->get('refer_page', '/projects');
        return redirect($url)->withMessage('Project updated');        
    }


    public function destroy(Request $request, Project $project)
    {
        $project->delete();
        $url = $request->get('refer_page', '/projects');
        return redirect($url)->withMessage('Project deleted');
    }

}
