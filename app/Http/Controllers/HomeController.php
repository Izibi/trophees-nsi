<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\ActiveContest;

class HomeController extends Controller
{

    public function index(Request $request, ActiveContest $active_contest) {
        return view('home.index', [
            'error_message' => $request->session()->get('error_message'),
            'contest' => $active_contest->get()
        ]);
    }
}
