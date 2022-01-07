<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Http\Requests\StoreSchoolRequest;

class UserSchoolsController extends Controller
{
    

    public function add(Request $request) {
        $user = $request->user();
        $id = $request->get('id');
        if(!$user->schools()->where('school_id', $id)->exists()) {
            $user->schools()->attach($id);
        }
        return response()->json(
            $this->getUserSchools($user)
        );
    }


    public function remove(Request $request) {
        $user = $request->user();
        $user->schools()->detach($request->get('id'));
        return response()->json(
            $this->getUserSchools($user)
        );
    }


    public function create(StoreSchoolRequest $request) {
        $user = $request->user();
        $school = new School();
        $school->fill($request->all());
        $school->save();
        $user->schools()->attach($school->id);
        return response()->json([
            'success' => true,
            'schools' => $this->getUserSchools($user)
        ]);        
    }    


    private function getUserSchools($user) {
        return $user->schools()->with('country', 'region')->get();
    }


    public function search(Request $request) {
        $q = $request->get('q');
        $res = [];
        if(strlen($q) > 0) {
            $like_q = '%'.$request->get('q').'%';
            $res = School::with('country', 'region')
                ->where('name', 'LIKE', $like_q)
                ->orWhere('city', 'LIKE', $like_q)
                ->orWhere('zip', 'LIKE', $like_q)
                ->get();
        }
        return response()->json($res);
    }    

}
