<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contest;
use App\Models\Region;
use App\Models\User;
use App\Models\Project;
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
        $isCoordinator = $request->user()->hasRole('coordinator');
        $isPresidentTerritorial = $request->user()->hasRole('president-territorial');
        $isPresidentPrize = $request->user()->hasRole('president-prize');
        
        if(!$isAdmin && !$isCoordinator && !$isPresidentTerritorial && !$isPresidentPrize) { 
            return redirect('/');
        }
        if($isAdmin) {
            $regions = Region::get();
            $prizes = Prize::get();
        } elseif($request->user()->role == 'teacher') {
            $regions = [Region::find($request->user()->region_id)];
            $prizes = [];
        } else {
            // Get regions for territorial jury members and presidents
            $territorialRoles = $request->user()->roles()->whereIn('type', ['territorial', 'president-territorial'])->get();
            $regions = [];
            foreach($territorialRoles as $role) {
                $region = Region::find($role->target_id);
                if($region && !in_array($region, $regions)) {
                    $regions[] = $region;
                }
            }
            // Get prizes for prize jury members and presidents
            $prizeRoles = $request->user()->roles()->whereIn('type', ['prize', 'president-prize'])->get();
            $prizes = [];
            foreach($prizeRoles as $role) {
                $prize = Prize::find($role->target_id);
                if($prize && !in_array($prize, $prizes)) {
                    $prizes[] = $prize;
                }
            }
        }

        $data = [];
        foreach($regions as $region) {
            $members = $this->getMembers('territorial', $region->id);
            $president = $this->getPresident($members, 'territorial');
            $projectsCount = $this->countEvaluableProjects('territorial', $region->id);
            $membersWithStats = $this->addRatingStats($members, 'territorial', $region->id);
            $data[] = [
                'id' => $region->id,
                'type' => 'territorial',
                'name' => $region->name,
                'president' => $president,
                'members' => $membersWithStats,
                'projects_count' => $projectsCount
            ];
        }

        foreach($prizes as $prize) {
            $members = $this->getMembers('prize', $prize->id);
            $president = $this->getPresident($members, 'prize');
            $projectsCount = $this->countEvaluableProjects('prize', $prize->id);
            $membersWithStats = $this->addRatingStats($members, 'prize', $prize->id);
            $data[] = [
                'id' => $prize->id,
                'type' => 'prize',
                'name' => $prize->name,
                'president' => $president,
                'members' => $membersWithStats,
                'projects_count' => $projectsCount
            ];
        }

        return view('jury.index', ['data' => $data]);
    }

    public function nominate(Request $request) {
        $isAdmin = $request->user()->role == 'admin';
        $isCoordinator = $request->user()->hasRole('coordinator');
        if(!$isAdmin && !$isCoordinator) { 
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
        if(!$isAdmin && !$isCoordinator && !$request->user()->hasRole($type, $target->id)) {
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

    private function countEvaluableProjects($type, $target_id) {
        if($type == 'territorial') {
            return Project::where('projects.contest_id', $this->contest->id)
                ->where('projects.status', 'validated')
                ->join('schools', 'projects.school_id', '=', 'schools.id')
                ->where('schools.region_id', '=', $target_id)
                ->count();
        } elseif($type == 'prize') {
            return Project::where('projects.contest_id', $this->contest->id)
                ->where('projects.status', 'validated')
                ->join('awards', function($join) use ($target_id) {
                    $join->on('projects.id', '=', 'awards.project_id')
                         ->where('awards.contest_id', '=', $this->contest->id)
                         ->where('awards.prize_id', '=', $target_id)
                         ->where('awards.region_id', '!=', 0);
                })
                ->count();
        }
        return 0;
    }

    private function addRatingStats($members, $type, $target_id) {
        $currentPhase = \App\Models\Rating::getCurrentPhase();
        
        foreach($members as $member) {
            $query = \App\Models\Rating::where('ratings.user_id', $member->id)
                ->where('ratings.phase', $currentPhase)
                ->join('projects', 'ratings.project_id', '=', 'projects.id')
                ->where('projects.contest_id', $this->contest->id)
                ->where('projects.status', 'validated');
            
            if($type == 'territorial') {
                $query->join('schools', 'projects.school_id', '=', 'schools.id')
                      ->where('schools.region_id', '=', $target_id);
            } elseif($type == 'prize') {
                $query->join('awards', function($join) use ($target_id) {
                    $join->on('projects.id', '=', 'awards.project_id')
                         ->where('awards.contest_id', '=', $this->contest->id)
                         ->where('awards.prize_id', '=', $target_id)
                         ->where('awards.region_id', '!=', 0);
                });
            }
            
            $member->ratings_published = (clone $query)->where('ratings.published', 1)->count();
            $member->ratings_draft = (clone $query)->where('ratings.published', 0)->count();
            $member->ratings_total = $member->ratings_published + $member->ratings_draft;
        }
        
        return $members;
    }

    public function export(Request $request) {
        $isAdmin = $request->user()->role == 'admin';
        $isCoordinator = $request->user()->hasRole('coordinator');
        
        if(!$isAdmin && !$isCoordinator) { 
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
        $hasAccessToTarget = $request->user()->hasRole($type, $target->id) || 
                             $request->user()->hasRole('president-' . $type, $target->id);
        if(!$isAdmin && !$isCoordinator && !$hasAccessToTarget) {
            return redirect('/jury');
        }

        $callback = function() use ($type, $target_id) {
            $fh = fopen('php://output', 'w');
            $columns = ['ID', 'Nom', 'Email', 'Email secondaire', 'Région', 'Pays', 'Dernière connexion', 'Estimation du nombre de projets', 'Date de mise à jour de l\'estimation', 'Etablissement scolaire'];
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
                    ->chunk(100, function($users) use ($fh, $target_id) {
                        foreach($users as $user) {
                            $this->writeUserRow($fh, $user, $target_id);
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

    private function writeUserRow($fh, $user, $region_id = null) {
        // Don't write if estimated is falsy
        if(!$user->estimated) {
            return;
        }

        // Get schools information
        $schools = $user->schools;
        
        // Filter by region_id if provided
        if($region_id !== null) {
            $schools = $schools->filter(function($school) use ($region_id) {
                return $school->region_id == $region_id;
            });
        }
        
        // If no schools, don't write any row
        if($schools->isEmpty()) {
            return;
        }
        
        // Split estimated count between schools (round up)
        $estimatedPerSchool = ceil($user->estimated / count($schools));
        
        foreach($schools as $school) {
            $schoolInfo = $school->name;
            if($school->address) {
                $schoolInfo .= ', ' . $school->address;
            }
            if($school->zip || $school->city) {
                $schoolInfo .= ', ' . ($school->zip ? $school->zip . ' ' : '') . ($school->city ?? '');
            }

            $row = [
                $user->id,
                $user->name,
                $user->email,
                $user->secondary_email,
                !is_null($school->region_id) ? $school->region->name : '',
                !is_null($user->country_id) ? $user->country->name : '',
                $user->last_login_at,
                $estimatedPerSchool,
                $user->estimated_update ?? '',
                $schoolInfo
            ];
            fputcsv($fh, $row);
        }
    }

    public function exportAll(Request $request) {
        $isAdmin = $request->user()->role == 'admin';
        if(!$isAdmin) { 
            return redirect('/');
        }

        $callback = function() {
            $fh = fopen('php://output', 'w');
            $columns = ['ID', 'Nom', 'Email', 'Email secondaire', 'Région', 'Pays', 'Dernière connexion', 'Estimation du nombre de projets', 'Date de mise à jour de l\'estimation', 'Etablissement scolaire'];
            fputcsv($fh, $columns);

            $loginCutoff = now()->subMonths(5);

            User::with('country', 'region', 'schools')
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

            fclose($fh);
        };

        $filename = 'trophees_nsi_users_all.csv';
        return $this->outputFile($filename, $callback);
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
