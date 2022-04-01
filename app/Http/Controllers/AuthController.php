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
        $authorization_helper = $client->getAuthorizationHelper();
        $url = $authorization_helper->getUrl(['locale' => app()->getLocale()]);
        return redirect($url);
    }


    public function profile(Request $request) {
        $client = new Client(config('login_module_client'));
        $redirect_helper = $client->getRedirectHelper();
        $refer_page = $request->get('refer_page', '/');
        $request->session()->flash('refer_page', $refer_page);
        $url = $redirect_helper->getProfileUrl(config('app.url').'/oauth_callback/profile');
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
    }


}
