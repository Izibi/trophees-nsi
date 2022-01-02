<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreUserRequest;

class UsersController extends Controller
{

    public function index(Request $request)
    {
        $users = DB::table('users')
            ->select(DB::raw('users.id, users.first_name, users.last_name, users.email, users.secondary_email, users.validated, users.role, regions.name as region_name, users.created_at, users.last_login_at'))
            ->leftJoin('regions', 'users.region_id', '=', 'regions.id')
            ->paginate()
            ->appends($request->all());
        return view('users.index', [
            'rows' => $users
        ]);
    }


    public function edit(Request $request, User $user)
    {
        return view('users.edit', [
            'refer_page' => $request->get('refer_page', '/users'),
            'user' => $user
        ]);
    }


    public function update(StoreUserRequest $request, User $user)
    {
        $user->fill($request->all());
        $user->save();
        $url = $request->get('refer_page', '/users');
        return redirect($url)->withMessage('User updated');
    }


    public function destroy(Request $request, User $user)
    {
        $user->delete();
        $url = $request->get('refer_page', '/users');
        return redirect($url)->withMessage('User deleted');
    }
}
