<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EvaluationServerLog;
use App\Models\Region;
use App\Models\User;


class EvaluationServerController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request) {
        $user = $request->user();
        if($user->role != 'jury' && $user->role != 'admin') {
            return redirect('/');
        }
        $url = null;
        if($user->server_password_local) {
            $url = '/guacamole/?username=jury-' . $user->id . '&password=' . $user->server_password_local;
        }
        return view('evaluation_server.index', [
            'user' => $user,
            'nb' => EvaluationServerLog::where('logout_date', null)->count(),
            'url' => $url
        ]);
    }

    private function generatePassword() {
        return bin2hex(random_bytes(8));
    }

    private function ensureUserHasRemotePassword($user) {
        if(strlen($user->server_password_remote) == 0) {
            $user->server_password_remote = $this->generatePassword();
            $user->save();
        }
    }

    private function generateUserMapping() {
        $config = config('nsi.evaluation_server');
        $userMapping = '<user-mapping>';
        $usersToMap = $config['fixed_users'];
        
        $users = User::whereIn('role', ['jury', 'admin'])->get();
        foreach($users as $user) {
            $this->ensureUserHasRemotePassword($user);
            // Local passwords are regenerated each time
            $user->server_password_local = $this->generatePassword();
            $user->save();

            $usersToMap[] = [
                'local_username' => 'jury-' . $user->id,
                'remote_username' => 'jury-' . $user->id,
                'local_password_md5' => md5($user->server_password_local),
                'remote_password' => $user->server_password_remote
            ];
        }

        foreach($usersToMap as $user) {
            $userMapping .= '<authorize username="'.$user['local_username'].'" password="'.$user['local_password_md5'].'" encoding="md5">';
            $userMapping .= '<connection name="Serveur Trophees NSI">';
            $userMapping .= '<protocol>rdp</protocol>';
            $userMapping .= '<param name="hostname">'.$config['ip_address'].'</param>';
            $userMapping .= '<param name="port">3389</param>';
            $userMapping .= '<param name="username">'.$user['remote_username'].'</param>';
            $userMapping .= '<param name="password">'.$user['remote_password'].'</param>';
            $userMapping .= '<param name="ignore-cert">true</param>';
            $userMapping .= '</connection>';
            $userMapping .= '</authorize>';
        }

        $userMapping .= '</user-mapping>';

        file_put_contents(base_path('evaluation_server/user-mapping.xml'), $userMapping);
    }


    public function recreateMapping(Request $request) {
        $api_password = config('nsi.evaluation_server.api_password');
        if($request->get('password') != $api_password) {
            return response()->json(['error' => 'Invalid API password'], 403);
        }
        $this->generateUserMapping();
        return response()->json(['success' => true]);
    }


    public function getUserData(Request $request) {
        $api_password = config('nsi.evaluation_server.api_password');
        if($request->get('password') != $api_password) {
            return response()->json(['error' => 'Invalid API password'], 403);
        }
        $json = [
            'users' => [],
            'groups' => []
        ];
        $regions = Region::all();
        foreach($regions as $region) {
            $json['groups'][] = [
                'name' => 'jury-region-'.$region->id,
                'foldername' => ['ProjetsTerritoires\\'.str_replace('/', '-', $region->name)]
            ];
        }
        $users = User::whereIn('role', ['jury', 'admin'])->get();
        foreach($users as $user) {
            $this->ensureUserHasRemotePassword($user);
            $userJson = [
                'username' => 'jury-'.$user->id,
                'password' => $user->server_password_remote,
                'groups' => []
            ];
            if($user->role == 'admin') {
                foreach($regions as $region) {
                    $userJson['groups'][] = 'jury-region-'.$region->id;
                }
            } else {
                foreach($user->roles as $role) {
                    if($role->type == 'territorial') {
                        $userJson['groups'][] = 'jury-region-'.$role->target_id;
                    }
                }
            }
            $json['users'][] = $userJson;
        }

        return response()->json($json);
    }


    public function receiveQueryUser(Request $request) {
        $api_password = config('nsi.evaluation_server.api_password');
        if($request->input('password') != $api_password) {
            return response()->json(['error' => 'Invalid API password'], 403);
        }
        $data = $request->input('data');
        $lines = explode("\n", $data);
        array_shift($lines);
        $onlineUsers = [];
        foreach($lines as $line) {
            $onlineUsers[] = str_replace('>', '', explode(' ', trim($line))[0]);
        }
        foreach($onlineUsers as $user) {
            if(!EvaluationServerLog::where('username', $user)->where('logout_date', null)->exists()) {
                $log = new EvaluationServerLog();
                $log->username = $user;
                $log->login_date = now();
                $log->save();
            }
        }
        $logs = EvaluationServerLog::where('logout_date', null)->get();
        foreach($logs as $log) {
            if(!in_array($log->username, $onlineUsers)) {
                $log->logout_date = now();
                $log->save();
            }
        }
        return response()->json(['success' => true]);
    }
}
