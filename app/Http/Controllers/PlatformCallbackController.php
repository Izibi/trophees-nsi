<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class PlatformCallbackController extends Controller
{

    protected $redirect_url = '/';

    public function userLogout(Request $request) {
        $user = $this->getUser($request);
        $user->relogin_required = true;
        $user->save();
        return $this->getResponse($request);
    }


    public function refreshUser(Request $request) {
        $user = $this->getUser($request);
        $user->relogin_required = true;
        $user->save();
        return $this->getResponse($request);
    }


    private function getUser($request) {
        return User::findOrFail($request->get('user_id'));
    }


    private function getResponse($request) {
        if($request->has('redirect_url')) {
            $url = $request->get('redirect_url');
        } else {
            $url = config('app.url').$this->redirect_url;
        }
        return redirect($url);
    }
}
