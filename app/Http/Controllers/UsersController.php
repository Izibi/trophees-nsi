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
	$prize_id = null;
	foreach($user->prizes as $prize) {
		$prize_id = $prize->id;
		break;
	}
	$user->prize_id = $prize_id;
        return view('users.edit', [
            'refer_page' => $request->get('refer_page', '/users'),
            'user' => $user,
            'countries' => Country::orderBy('name')->get(),
            'regions' => Region::orderBy('country_id', 'desc')->orderBy('name')->get(),
	    'prizes' => Prize::orderBy('name')->get(),
	    'prize_id' => $prize_id
        ]);
    }


    public function update(StoreUserRequest $request, User $user)
    {
        $user->fill($request->all());
	$user->save();
	$prize_saved = false;
	$prize_id = $request->input('prize_id');
	foreach($user->prizes as $prize) {
		$user->prizes()->detach($prize->id);
	}
	if($prize_id !== null) {
		$user->prizes()->attach($prize_id);
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
