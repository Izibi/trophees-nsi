<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreUserRequest;
use App\Helpers\SortableTable;
use App\Models\Country;
use App\Models\Region;
use App\Models\Prize;
use App\Models\Role;

class UsersController extends Controller
{


    private $sort_fields = [
        'name' => 'users.name',
        'email' => 'users.email',
        'secondary_email' => 'users.secondary_email',
        'validated' => 'users.validated',
        'role' => 'users.role',
        'country' => 'country.name',
        'region' => 'regions.name',
        'created_at' => 'users.created_at',
        'last_login_at' => 'users.last_login_at'
    ];


    public function index(Request $request)
    {
        $q = $this->getUsersQuery($request);
        SortableTable::orderBy($q, $this->sort_fields);
        $users = $q->paginate()->appends($request->all());
        return view('users.index', [
            'rows' => $users,
            'regions' => Region::orderBy('country_id', 'desc')->orderBy('name')->get()->pluck('name', 'id')->toArray()
        ]);
    }


    private function getUsersQuery($request) {
        $q = DB::table('users')
            ->select(DB::raw('users.id, users.name, users.email, users.secondary_email, users.validated, users.role, countries.name as country_name, regions.name as region_name, users.created_at, users.last_login_at'))
            ->leftJoin('countries', 'users.country_id', '=', 'countries.id')
            ->leftJoin('regions', 'users.region_id', '=', 'regions.id');
        if($request->has('filter')) {
            $filter_name = $request->get('filter_name');
            if(strlen($filter_name) > 0) {
                $q->where('users.name', 'LIKE', '%'.$filter_name.'%');
            }
            $filter_email = $request->get('filter_email');
            if(strlen($filter_email) > 0) {
                $filter_email = '%'.$filter_email.'%';
                $q->where(function($q) use ($filter_email) {
                    $q->where('users.email', 'LIKE', $filter_email)->orWhere('users.secondary_email', 'LIKE', $filter_email);
                });
            }
            $filter_role = $request->get('filter_role');
            if($filter_role) {
                $q->where('users.role', $filter_role);
            }
            $filter_region_id = $request->get('filter_region_id');
            if(strlen($filter_region_id) > 0) {
                $q->where('users.region_id', '=', $filter_region_id);
            }
        }
        return $q;
    }


    public function edit(Request $request, User $user)
    {
        $coordinator = $user->hasRole('coordinator');
        return view('users.edit', [
            'refer_page' => $request->get('refer_page', '/users'),
            'user' => $user,
            'countries' => Country::orderBy('name')->get(),
            'regions' => Region::orderBy('country_id', 'desc')->orderBy('name')->get(),
    	    'prizes' => Prize::orderBy('name')->get(),
            'coordinator' => $coordinator
        ]);
    }


    public function update(StoreUserRequest $request, User $user)
    {
        $user->fill($request->all());
    	$user->save();

        if($request->get('role') == 'jury') {
            $roles_id = $request->get('roles_id', []);
            $roles_type = $request->get('roles_type', []);
            $roles_target = $request->get('roles_target', []);
            foreach($user->roles as $role) {
                if(!in_array("".$role->id, $roles_id)) {
                    $role->delete();
                }
            }
            foreach($roles_id as $i => $role_id) {
                if($role_id) {
                    $role = Role::find($role_id);
                } else {
                    $role = new Role();
                    $role->user_id = $user->id;
                }
                $role->type = $roles_type[$i];
                $role->target_id = $roles_target[$i];
                try {
                    $role->save();
                } catch(\Exception $e) {}
            }
            foreach($user->roles as $role) {
                if($role->target_id === null && in_array($role->type, ['territorial', 'prize', 'president-territorial', 'president-prize'])) {
                    $role->delete();
                }
            }
            foreach($user->roles->where('type', 'president-territorial') as $president_role) {
                if($user->roles->where('type', 'territorial')->where('target_id', $president_role->target_id)->count() == 0) {
                    $role = new Role();
                    $role->user_id = $user->id;
                    $role->type = 'territorial';
                    $role->target_id = $president_role->target_id;
                    $role->save();
                }
            }
            foreach($user->roles->where('type', 'president-prize') as $president_role) {
                if($user->roles->where('type', 'prize')->where('target_id', $president_role->target_id)->count() == 0) {
                    $role = new Role();
                    $role->user_id = $user->id;
                    $role->type = 'prize';
                    $role->target_id = $president_role->target_id;
                    $role->save();
                }
            }
        } elseif($request->get('role') == 'teacher') {
            $coordinator = $request->get('cb_coordinator');
            foreach($user->roles as $role) {
                if($role->type == 'coordinator' && $coordinator) {
                    $coordinator = false;
                } else {
                    $role->delete();
                }
            }
            if($coordinator) {
                $role = new Role();
                $role->user_id = $user->id;
                $role->type = 'coordinator';
                $role->save();
            }
        }

        $url = $request->get('refer_page', '/users');
        return redirect($url)->withMessage('Utilisateur enregistré');
    }


    public function destroy(Request $request, User $user)
    {
        $user->delete();
        $url = $request->get('refer_page', '/users');
        return redirect($url)->withMessage('Utilisateur supprimé');
    }
}
