<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use FranceIOI\LoginModuleClient\Client;

class AuthController extends Controller
{
    
    public function login() {
        $this->logoutUser();
        $client = new Client(config('login_module_client'));
        $redirect_helper = $client->getRedirectHelper();
        $authorization_helper = $client->getAuthorizationHelper();
        $url = $authorization_helper->getUrl(); // ['locale' => 'en']
        return redirect($url);
    }


    public function logout(Request $request) {
        if(!Auth::check()) {
            return redirect('/');
        }
        if($request->has('complete')) {
            $this->logoutUser();
            return redirect('/');
        } else {
            $client = new Client(config('login_module_client'));
            $redirect_helper = $client->getRedirectHelper();
            $url = $redirect_helper->getLogoutUrl(config('app.url').'/logout?complete=1');
            return redirect($url);
        }
    }


    private function logoutUser() {
        Session::flush();
        Auth::logout();        
        return redirect('/');
    }    



}
