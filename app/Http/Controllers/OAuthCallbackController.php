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
        Session::flush();
        Auth::logout();        
        try {
            $client = new Client(config('login_module_client'));
            $authorization_helper = $client->getAuthorizationHelper();
            $authorization_helper->handleRequestParams($_GET);
            $user_data = $authorization_helper->queryUser();
            if(!$this->validateUserData($user_data)) {
                throw new \Exception('Login available for verified teachers only.');
            }
            $user = $this->refreshUser($user_data);
            Auth::login($user);        
        } catch(\Exception $e) {
            return redirect('/')->with('error_message', $e->getMessage());
        }
        return redirect('/presentation');
    }


    public function profile(Request $request) {
        $client = new Client(config('login_module_client'));
        $authorization_helper = $client->getAuthorizationHelper();
        $user_data = $authorization_helper->queryUser();
        $this->refreshUser($user_data);
        $user = User::find($user_data['id']);
        $user->fill($user_data);
        $user->save();
    }



    private function validateUserData($user_data) {
        if($user_data['role'] == 'teacher' && 
            isset($user_data['verification']['role']) && 
            $user_data['verification']['role'] == 'VERIFIED') {
            return true;
        }
        return false;
    }


    private function refreshUser($user_data) {
        $user = User::find($user_data['id']);
        if(!$user) {
            $user = new User();
            $user->id = $user_data['id'];
        }
        $attributes = $this->getUserAttributes($user_data);
        $user->fill($attributes);
        $user->last_login_at = Carbon::now();
        $user->save();
        return $user;
    }


    private function getUserAttributes($user_data) {
        return [
            'email' => $user_data['primary_email'],
            'secondary_email' => $user_data['secondary_email'],
            'first_name' => $user_data['first_name'],
            'last_name' => $user_data['last_name'],
            'role' => 'teacher'
        ];
    }

}
