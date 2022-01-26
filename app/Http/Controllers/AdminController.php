<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function users() {
        $conf = config('login_module_client');
        return redirect($conf['base_url'].'/client_admin/'.$conf['id'].'/users');
    }
}
