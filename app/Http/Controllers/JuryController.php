<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contest;
use App\Models\Region;
use App\Models\User;
use App\Models\Project;
use App\Models\Rating;
use App\Models\Grade;
use App\Models\Prize;
use App\Classes\ActiveContest;


class JuryController extends Controller
{
    public function __construct(ActiveContest $active_contest)
    {
        $this->contest = $active_contest->get();
    }

    public function index(Request $request) {
        $isAdmin = $request->user()->role == 'admin';
        if(!$isAdmin && !$request->user()->hasRole('coordinator')) { 
            return redirect('/');
        }
        if($isAdmin) {
            $regions = Region::get();
            $prizes = Prize::get();
        } else {
            $roles = $request->user()->roles()->where('type', 'territorial')->get();
            $regions = [];
            foreach($roles as $role) {
                $regions[] = Region::find($role->target_id);
            }
            $prizes = $request->user()->prizes()->get();
        }

        $data = [];
        foreach($regions as $region) {
            $members = $this->getMembers('territorial', $region->id);
            $president = $this->getPresident($members, 'territorial');
            $data[] = [
                'id' => $region->id,
                'type' => 'territorial',
                'name' => $region->name,
                'president' => $president,
                'members' => $members
            ];
        }

        foreach($prizes as $prize) {
            $members = $this->getMembers('prize', $prize->id);
            $president = $this->getPresident($members, 'prize');
            $data[] = [
                'id' => $prize->id,
                'type' => 'prize',
                'name' => $prize->name,
                'president' => $president,
                'members' => $members
            ];
        }

        return view('jury.index', ['data' => $data]);
    }

    public function nominate(Request $request) {
        $isAdmin = $request->user()->role == 'admin';
        if(!$isAdmin && !$request->user()->hasRole('coordinator')) { 
            return redirect('/');
        }

        $type = $request->get('type');
        if($type == 'territorial') {
            $target = Region::find($request->get('target'));
        } elseif($type == 'prize') {
            $target = Prize::find($request->get('target'));
        } else {
            return redirect('/jury');
        }
        $target_user = User::find($request->get('user'));

        if(!$target || !$target_user) {
            return redirect('/jury');
        }
        if(!$isAdmin && !$request->user()->hasRole('coordinator') && !$request->user()->hasRole($type, $target->id)) {
            return redirect('/jury');
        }

        $president = $this->getPresident($this->getMembers($type, $target->id), $type);
        if($president && $president !== $target_user) {
            $president->roles()->where('type', 'president-' . $type)->where('target_id', $target->id)->delete();
        }

        $target_user->roles()->updateOrCreate([
            'type' => 'president-' . $type,
            'target_id' => $target->id
        ]);

        return redirect('/jury');
    }

    private function getMembers($type, $target_id) {
        return User::whereHas('roles', function($q) use ($type, $target_id) {
            $q->where('type', $type)->where('target_id', $target_id);
        })->get();
    }

    private function getPresident($members, $type) {
        foreach($members as $member) {
            if($member->hasRole('president-' . $type)) {
                return $member;
            }
        }
        return null;
    }
}
