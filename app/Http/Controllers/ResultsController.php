<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultsController extends Controller
{
    
    public function index(Request $request) {
        $q = $this->getProjectsQuery();
        $projects = $q->paginate();
        return view('results.index', [
            'projects' => $projects
        ]);
    }


    private function getProjectsQuery() {
        $q = DB::table('projects')
            ->select(DB::raw('projects.id, projects.name'))
            ->where('projects.status', '=', 'validated');
        return $q;
    }    
}
