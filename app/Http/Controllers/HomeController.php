<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    
    public function index(Request $request) {
        return view('home.index', [
            'error_message' => $request->session()->get('error_message')
        ]);
    }
}
