<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Repositories\UserRepository;

class AdminInterfaceController extends Controller
{

    protected $redirect_url = '/';
    private $user_repository;


    public function __construct(UserRepository $user_repository) {
        $this->user_repository = $user_repository;
    }


    public function userLogout(Request $request) {
        $this->validateRequest($request);
        $user = $this->getUser($request);
        $user->relogin_required = true;
        $user->save();
        return $this->getResponse($request);
    }


    public function userRefresh(Request $request) {
        $this->validateRequest($request);
        $user = $this->getUser($request);
        $user->refresh_required = true;
        $user->save();
        return $this->getResponse($request);
    }


    public function userLogin(Request $request) {
        $this->validateRequest($request);
        $user = $this->getUser($request);
        Auth::logout();
        Session::flush();
        return redirect('/login');
    }


    public function showUserDelete(Request $request) {
        $redirect_url = $request->get('redirect_url');
        $user = User::find($request->get('user_id'));
        if(!$user) {
            return redirect($redirect_url);
        }
        return view('admin_interface.user_delete', [
            'user' => $user,
            'redirect_url' => $request->get('redirect_url')
        ]);
    }


    public function userDelete(Request $request) {
        $user = $this->getUser($request);

        $backup_user = User::find($request->get('backup_user_id'));
        if(!$backup_user) {
            return redirect()->back()->withInput($request->all())->withError('Backup user not found.');
        }

        DB::beginTransaction();
        try {
            $this->user_repository->delete(
                $user->id,
                $backup_user->id
            );
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput($request->all())->withError($e->getMessage());
        }
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


    private function validateRequest($request) {
        if($request->user()->role != 'admin') {
            abort(403);
        }
    }
}
