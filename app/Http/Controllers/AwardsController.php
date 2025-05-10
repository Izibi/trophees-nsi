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
use App\Models\Award;
use App\Classes\ActiveContest;


class AwardsController extends Controller
{
    public function __construct(ActiveContest $active_contest)
    {
        $this->contest = $active_contest->get();
    }

    public function index(Request $request) {
        $role = $request->user()->role;
        $isAdmin = $role == 'admin';
        if(!$isAdmin && !$request->user()->hasRole('president-territorial') && !$request->user()->hasRole('president-prize')) { 
            return redirect('/projects');
        }
        $territorialData = $this->getPresidentTerritorialData($request, $isAdmin);
        $nationalData = $this->getPresidentNationalData($request, $isAdmin);
        if(count($territorialData) == 0 && count($nationalData) == 0) {
            return redirect('/projects');
        }
        return view('awards.'.$role, [
            'isAdmin' => $isAdmin,
            'phase' => $this->contest->status,
            'prizes' => Prize::get(),
            'territorial' => $territorialData,
            'national' => $nationalData
        ]);
    }

    private function getRegionPrize($prize, $region_id) {
        return Award::where('prize_id', $prize->id)->where('region_id', $region_id)->first();
    }

    private function getPresidentTerritorialData($request, $isAdmin) {
        if($isAdmin) {
            $regions = Region::get();
        } else {
            $roles = $request->user()->roles()->where('type', 'president-territorial')->get();
            $regions = [];
            foreach($roles as $role) {
                $regions[] = Region::find($role->target_id);
            }
        }
        $prizes = Prize::get();
        $data = [];
        foreach($regions as $region) {
            $newData = [
                'region' => $region,
                'prizes' => []
            ];
            foreach($prizes as $prize) {
                $newData['prizes'][] = [
                    'prize' => $prize,
                    'awarded' => $this->getRegionPrize($prize, $region->id)
                ];
            }
            $data[] = $newData;
        }
        return $data;
    }

    private function getPresidentNationalData($request, $isAdmin) {
        if($isAdmin) {
            $prizes = Prize::get();
        } else {
            $roles = $request->user()->roles()->where('type', 'president-prize')->get();
            $prizes = [];
            foreach($roles as $role) {
                $prizes[] = Prize::find($role->target_id);
            }
        }
        $data = [];
        foreach($prizes as $prize) {
            $data[] = [
                'prize' => $prize,
                'awarded' => $this->getRegionPrize($prize, 0)
            ];
        }
        return $data;
    }

    private function getAwardablePrizes($user, $project) {
        if($user->role == 'admin') {
            $prizes = Prize::where('grade_id', $project->grade_id)->get();
            $national = $this->contest->status == 'deliberating-national';
            foreach($prize as $prize) {
                $awardable[] = [
                    'type' => $national ? 'national' : 'territorial',
                    'region_id' => $national ? 0 : $project->region_id,
                    'prize_id' => $prize->id,
                    'name' => Award::getRegionPrizeTitle($prize, $user->roles()->where('type', 'president-territorial')->first()->target_id)
                ];
            }
            return $awardable;
        }

        $awardable = [];
        if($this->contest->status == 'deliberating-territorial') {
            foreach($user->roles()->where('type', 'president-territorial')->get() as $role) {
                $prizes = Prize::where('grade_id', $project->grade_id)->get();
                foreach($prizes as $prize) {
                    $awardable[] = [
                        'type' => 'territorial',
                        'region_id' => Region::find($role->target_id)->id,
                        'prize_id' => $prize->id,
                        'name' => Award::getRegionPrizeTitle($prize, $role->target_id)
                    ];
                }
            }
        }
        if($this->contest->status == 'deliberating-national') {
            foreach($user->roles()->where('type', 'president-prize')->get() as $role) {
                $prizes = Prize::where('id', $role->target_id)->where('grade_id', $project->grade_id)->get();
                foreach($prizes as $prize) {
                    $awardable[] = [
                        'type' => 'national',
                        'region_id' => 0,
                        'prize_id' => $prize->id,
                        'name' => Award::getRegionPrizeTitle($prize, 0)
                    ];
                }
            }
        }
        foreach($awardable as &$aw) {
            $awarded = Award::where('user_id', $user->id)->where('prize_id', $aw['prize_id'])->where('region_id', $aw['region_id'])->first();
            $aw['name'] .= $awarded ? ' (actuellement attribué au projet ' . $awarded->project->name . ')' : '';
        }
        return $awardable;
    }

    public function create(Request $request, Project $project) {
        $user = $request->user();
        if(!$user->hasRole('president-territorial') && !$user->hasRole('president-prize')) { 
            return redirect('/projects');
        }
        $phase = $this->contest->status;
        if(($phase != 'deliberating-territorial' && $phase != 'deliberating-national')
            || ($phase == 'deliberating-territorial' && !$user->hasRole('president-territorial'))
            || ($phase == 'deliberating-national' && !$user->hasRole('president-prize'))) {
            return redirect('/awards');
        }

        $awardable = $this->getAwardablePrizes($user, $project);
        $abc = json_encode($awardable);

        // Make $awardable a list for the form select
        $awardable = array_map(function($a) {
            return $a['name'];
        }, $awardable);

        return view('awards.edit', [
            'awardable' => $awardable,
            'award' => null,
            'project' => $project
        ]);
    }

    public function edit(Request $request, Award $award) {
        $user = $request->user();
        if(!$user->hasRole('president-territorial') && !$user->hasRole('president-prize')) { 
            return redirect('/projects');
        }
        $phase = $this->contest->status;
        if(($phase != 'deliberating-territorial' && $phase != 'deliberating-national')
            || ($phase == 'deliberating-territorial' && !$user->hasRole('president-territorial'))
            || ($phase == 'deliberating-national' && !$user->hasRole('president-prize'))) {
            return redirect('/awards');
        }

        $project = $award->project;

        $awardable = $this->getAwardablePrizes($user, $project);

        // Make $awardable a list for the form select
        $awardable = array_map(function($a) {
            return $a['name'];
        }, $awardable);

        return view('awards.edit', [
            'awardable' => $awardable,
            'award' => $award,
            'project' => $project
        ]);
    }

    public function update(Request $request) {
        $user = $request->user();
        if(!$user->hasRole('president-territorial') && !$user->hasRole('president-prize')) { 
            return redirect('/projects');
        }
        $phase = $this->contest->status;
        if(($phase != 'deliberating-territorial' && $phase != 'deliberating-national')
            || ($phase == 'deliberating-territorial' && !$user->hasRole('president-territorial'))
            || ($phase == 'deliberating-national' && !$user->hasRole('president-prize'))) {
            return redirect('/awards');
        }

        $project = Project::find($request->get('project_id'));
        if(!$project) {
            return redirect('/awards');
        }
        $awardable = $this->getAwardablePrizes($user, $project);
        $awardable_id = $request->get('awardable_id');
        $awarded = isset($awardable[$awardable_id]) ? $awardable[$awardable_id] : null;
        $comment = $request->get('comment');
        if(!$awarded || !trim($comment)) {
            return redirect()->back();
        }
        
        $award = Award::where('project_id', $project->id)->where('user_id', $user->id)->where('region_id', $awarded['region_id'])->where('prize_id', $awarded['prize_id'])->first();
        if(!$award) {
            $award = new Award();
            $award->project_id = $project->id;
            $award->user_id = $user->id;
        }
        $award->prize_id = $awarded['prize_id'];
        $award->region_id = $awarded['region_id'];
        $award->comment = $comment;
        $award->save();

        $other_awarded = Award::where('user_id', $user->id)->where('prize_id', $awarded['prize_id'])->where('region_id', $awarded['region_id'])->where('id', '!=', $award->id)->get();
        foreach($other_awarded as $other) {
            $other->delete();
        }

        return redirect('/awards');
    }

    public function delete(Request $request, Award $award) {
        $user = $request->user();
        if(!$user->hasRole('president-territorial') && !$user->hasRole('president-prize')) { 
            return redirect('/projects');
        }
        $phase = $this->contest->status;
        if(($phase != 'deliberating-territorial' && $phase != 'deliberating-national')
            || ($phase == 'deliberating-territorial' && !$user->hasRole('president-territorial'))
            || ($phase == 'deliberating-national' && !$user->hasRole('president-prize'))
            || $award->user_id != $user->id) {
            return redirect('/awards');
        }

        $award->delete();
        return redirect('/awards');
    }


    public function export(Request $request)
    {
        $user = $request->user();
        if(!$user->role == 'admin' && !$user->hasRole('president-territorial') && !$user->hasRole('president-prize')) { 
            return redirect('/projects');
        }

        if($user->role == 'admin') {
            $q = Award::orderBy('prize_id', 'asc')->orderBy('region_id', 'asc');
        } else {
            $q = Award::where('user_id', $user->id)->orderBy('prize_id', 'asc')->orderBy('region_id', 'asc');

        }

        $callback = function() use ($q) {
            $fh = fopen('php://output', 'w');
            $header = ['ID', 'Prix', 'Territoire', 'Nom', 'Enseignant', 'Etablissement scolaire', 'Académie', 'Commentaire', 'Membres de l\'équipe'];
            fputcsv($fh, $header);

            $q->chunk(500, function($rows) use ($fh) {
                foreach($rows as $award) {
                    $project = $award->project;
                    $team_members = "";
                    foreach($project->team_members as $member) {
                        $team_members .= $member->first_name . " " . $member->last_name . " (" . ($member->gender == 'male' ? 'M' : 'F') . "), ";
                    }
                    $row = [
                        $project->id,
                        $award->prize->name,
                        $award->region_id != 0 ? Region::find($award->region_id)->name : 'National',
                        $project->name,
                        $project->user->name,
                        $project->school ? $project->school->name : '',
                        $project->school ? $project->school->academy->name : '',
                        $award->comment,
                        $team_members
                    ];
                    fputcsv($fh, $row);
                }
            });
            fclose($fh);
        };

        $file_name = 'trophees_nsi_laureats.csv';
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
