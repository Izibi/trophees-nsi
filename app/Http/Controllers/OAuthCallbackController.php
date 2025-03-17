<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use FranceIOI\LoginModuleClient\Client;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OAuthCallbackController extends Controller
{


    public function login(Request $request) {
        try {
            $client = new Client(config('login_module_client'));
            $authorization_helper = $client->getAuthorizationHelper();
            $authorization_helper->handleRequestParams($_GET);
            $user_data = $authorization_helper->queryUser();
            if(!$this->validateUserData($user_data)) {
                return redirect(config('login_module_client.base_url'));
                //throw new \Exception('Connexion possible uniquement pour les enseignants confirmés.');
            }
            $user = $this->refreshUser($user_data);
            Auth::logout();
            Session::flush();
            Auth::login($user);
        } catch(\Exception $e) {
            return redirect('/')->with('error_message', $e->getMessage());
        }
        return redirect('/projects');
    }


    public function profile(Request $request) {
        $url = $request->session()->pull('refer_page', '/');
        try {
            $client = new Client(config('login_module_client'));
            $authorization_helper = $client->getAuthorizationHelper();
            $user_data = $authorization_helper->queryUser();
            if($request->user()->id != $user_data['id']) {
                return redirect('/logout');
            }
            $this->refreshUser($user_data);
        } catch(\Exception $e) {
            return redirect($url)->with('error_message', $e->getMessage());
        }
        return redirect($url);
    }



    private function validateUserData($user_data) {
        if($user_data['role'] == 'teacher' || $user_data['role'] == 'other') {
            return true;
        }
        return false;
    }


    private function refreshUser($user_data) {
        $user = User::find($user_data['id']);

        if(!$user) {
            $user = new User();
            $user->id = $user_data['id'];
            $user->role = $user_data['client_admin'] ? 'admin' : 'teacher';
        } else if($user->role == 'admin' && !$user_data['client_admin']) {
            $user->role = 'teacher';
        }
        if(isset($user_data['verification']['role']) && $user_data['role'] == 'other') {
            $user->role = 'jury';
        }
        $attributes = $this->getUserAttributes($user_data);
        $user->fill($attributes);
        $user->last_login_at = Carbon::now();
        $user->relogin_required = false;
        $user->refresh_required = false;
        $user->save();
        return $user;
    }


    private function getUserAttributes($user_data) {
        return [
            'email' => $user_data['primary_email'],
            'secondary_email' => $user_data['secondary_email'],
            'name' => $user_data['first_name'].' '.$user_data['last_name'],
            'login' => $user_data['login'],
            'validated' => isset($user_data['verification']['role']) && $user_data['verification']['role'] == 'VERIFIED'
        ];
    }

}
