<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ResultsController extends Controller
{
    
    public function index() {
        return view('results.index', [
            'rows' => Project::whereNotNull('score_total')
                ->orderBy('score_total', 'desc')
                ->paginate()
        ]);
    }

}