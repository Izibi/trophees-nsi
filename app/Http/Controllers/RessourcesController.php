<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RessourcesController extends Controller
{
    public function index()
    {
        return view('ressources.index');
    }
}
