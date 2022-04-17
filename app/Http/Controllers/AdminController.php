<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use FranceIOI\LoginModuleClient\Client;

class AdminController extends Controller
{

    public function users() {
        return $this->getRedirect('users');
    }

    private function getRedirect($section) {
        $client = new Client(config('login_module_client'));
        $redirect_helper = $client->getRedirectHelper();
        $url = $redirect_helper->getAdminInterfaceUrl($section);
        return redirect($url);

    }
}
