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
        } elseif($request->user()->role == 'teacher') {
            $regions = [Region::find($request->user()->region_id)];
            $prizes = [];
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

    public function export(Request $request) {
        $isAdmin = $request->user()->role == 'admin';
        if(!$isAdmin && !$request->user()->hasRole('coordinator')) { 
            return redirect('/');
        }

        $type = $request->get('type');
        $target_id = $request->get('target');

        if($type == 'territorial') {
            $target = Region::find($target_id);
        } elseif($type == 'prize') {
            $target = Prize::find($target_id);
        } else {
            return redirect('/jury');
        }

        if(!$target) {
            return redirect('/jury');
        }

        // Check if user has rights to access this target
        if(!$isAdmin && !$request->user()->hasRole('coordinator') && !$request->user()->hasRole($type, $target->id)) {
            return redirect('/jury');
        }

        $callback = function() use ($type, $target_id) {
            $fh = fopen('php://output', 'w');
            $columns = ['ID', 'Nom', 'Email', 'Email secondaire', 'Région', 'Pays', 'Dernière connexion', 'Estimation du nombre de projets', 'Date de mise à jour de l\'estimation', 'Etablissements scolaires'];
            fputcsv($fh, $columns);

            $loginCutoff = now()->subMonths(5);

            if($type == 'territorial') {
                // Export users from that territory (either with schools in region or with region_id but no schools)
                User::with('country', 'region', 'schools')
                    ->where(function($query) use ($target_id) {
                        $query->whereHas('schools', function($q) use ($target_id) {
                            $q->where('region_id', $target_id);
                        })
                        ->orWhere(function($q) use ($target_id) {
                            $q->where('region_id', $target_id)
                              ->whereDoesntHave('schools');
                        });
                    })
                    ->where(function($query) {
                        $query->where('role', 'teacher')
                              ->orWhere(function($q) {
                                  $q->where('role', 'jury')
                                    ->whereHas('roles', function($r) {
                                        $r->where('type', 'teacher');
                                    });
                              });
                    })
                    ->where('last_login_at', '>=', $loginCutoff)
                    ->chunk(100, function($users) use ($fh) {
                        foreach($users as $user) {
                            $this->writeUserRow($fh, $user);
                        }
                    });
            } elseif($type == 'prize') {
                // Export users who submitted a project considered for that prize
                User::with('country', 'region', 'schools')
                    ->whereHas('projects', function($query) use ($target_id) {
                        $query->whereHas('awards', function($q) use ($target_id) {
                            $q->where('prize_id', $target_id);
                        });
                    })
                    ->where(function($query) {
                        $query->where('role', 'teacher')
                              ->orWhere(function($q) {
                                  $q->where('role', 'jury')
                                    ->whereHas('roles', function($r) {
                                        $r->where('type', 'teacher');
                                    });
                              });
                    })
                    ->where('last_login_at', '>=', $loginCutoff)
                    ->chunk(100, function($users) use ($fh) {
                        foreach($users as $user) {
                            $this->writeUserRow($fh, $user);
                        }
                    });
            }

            fclose($fh);
        };

        $filename = 'trophees_nsi_users_' . $type . '_' . $target_id . '.csv';
        return $this->outputFile($filename, $callback);
    }

    private function writeUserRow($fh, $user) {
        // Get schools information
        $schools = $user->schools;
        $schoolNames = [];
        foreach($schools as $school) {
            $schoolInfo = $school->name;
            if($school->address) {
                $schoolInfo .= ', ' . $school->address;
            }
            if($school->zip || $school->city) {
                $schoolInfo .= ', ' . ($school->zip ? $school->zip . ' ' : '') . ($school->city ?? '');
            }
            $schoolNames[] = $schoolInfo;
        }
        $schoolsText = implode(' | ', $schoolNames);

        $row = [
            $user->id,
            $user->name,
            $user->email,
            $user->secondary_email,
            !is_null($user->region_id) ? $user->region->name : '',
            !is_null($user->country_id) ? $user->country->name : '',
            $user->last_login_at,
            $user->estimated ?? '',
            $user->estimated_update ?? '',
            $schoolsText
        ];
        fputcsv($fh, $row);
    }

    private function outputFile($file_name, $callback) {
        $headers = array(
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename='.$file_name,
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        );
        return response()->stream($callback, 200, $headers);
    }
}
