<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contest;
use App\Models\Region;
use App\Models\User;
use App\Models\Project;
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
        return Award::where('contest_id', $this->contest->id)->where('prize_id', $prize->id)->where('region_id', $region_id)->first();
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
            $prizes = Prize::where('grade_id', $project->grade_id)
                ->orWhereNull('grade_id')
                ->get();
            
            $awardable = [];
            $national = $this->contest->status == 'deliberating-national';
            $region_id = $national ? 0 : ($project->school ? $project->school->region_id : null);
            
            foreach($prizes as $prize) {
                $awardable[] = [
                    'type' => $national ? 'national' : 'territorial',
                    'region_id' => $region_id,
                    'prize_id' => $prize->id,
                    'name' => Award::getRegionPrizeTitle($prize, $region_id)
                ];
            }
            return $awardable;
        }
        
        $awardable = [];
        if($this->contest->status == 'deliberating-territorial') {
            foreach($user->roles()->where('type', 'president-territorial')->get() as $role) {
                $prizes = Prize::where('grade_id', $project->grade_id)
                    ->orWhereNull('grade_id')
                    ->get();
                
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
                $prizes = Prize::where('id', $role->target_id)
                    ->where(function($query) use ($project) {
                        $query->where('grade_id', $project->grade_id)
                              ->orWhereNull('grade_id');
                    })
                    ->get();
                
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
            $awarded = Award::where('contest_id', $this->contest->id)->where('user_id', $user->id)->where('prize_id', $aw['prize_id'])->where('region_id', $aw['region_id'])->first();
            $aw['name'] .= $awarded ? ' (actuellement attribué au projet ' . $awarded->project->name . ')' : '';
        }
        return $awardable;
    }

    public function edit(Request $request, Project $project) {
        $user = $request->user();
        if($user->role != 'admin' && !$user->hasRole('president-territorial') && !$user->hasRole('president-prize')) { 
            return redirect('/projects');
        }
        $phase = $this->contest->status;
        if($user->role != 'admin' && (($phase != 'deliberating-territorial' && $phase != 'deliberating-national')
            || ($phase == 'deliberating-territorial' && !$user->hasRole('president-territorial'))
            || ($phase == 'deliberating-national' && !$user->hasRole('president-prize')))) {
            return redirect('/awards');
        }

        $awardable = $this->getAwardablePrizes($user, $project);

        // Get currently attributed awards for this project
        $currentAwards = Award::where('project_id', $project->id)
            ->get();
        
        // Find which awardable indices match current awards
        $currentRegularAwardIndex = null;
        $currentSpecialAwardIndices = [];
        $currentRegularAward = null;
        $currentComment = '';
        
        foreach($currentAwards as $currentAward) {
            foreach($awardable as $idx => $aw) {
                if($aw['prize_id'] == $currentAward->prize_id && $aw['region_id'] == $currentAward->region_id) {
                    $prize = Prize::find($aw['prize_id']);
                    if ($prize && $prize->special) {
                        $currentSpecialAwardIndices[] = $idx;
                    } else {
                        $currentRegularAwardIndex = $idx;
                        $currentRegularAward = $currentAward;
                        $currentComment = $currentAward->comment;
                    }
                    break;
                }
            }
        }

        // Separate regular and special prizes
        $regularPrizes = [];
        $specialPrizes = [];
        foreach($awardable as $idx => $aw) {
            $prize = Prize::find($aw['prize_id']);
            if ($prize && $prize->special) {
                $specialPrizes[$idx] = ['name' => $aw['name'], 'prize' => $prize];
            } else {
                $regularPrizes[$idx] = $aw;
            }
        }

        // Make prize arrays into lists for the form
        $regularPrizesList = array_map(function($a) {
            return $a['name'];
        }, $regularPrizes);

        return view('awards.edit', [
            'awardable' => $awardable,
            'regularPrizes' => $regularPrizesList,
            'specialPrizes' => $specialPrizes,
            'project' => $project,
            'currentRegularAwardIndex' => $currentRegularAwardIndex,
            'currentSpecialAwardIndices' => $currentSpecialAwardIndices,
            'currentRegularAward' => $currentRegularAward,
            'currentComment' => $currentComment
        ]);
    }

    public function update(Request $request) {
        $user = $request->user();
        if($user->role != 'admin' && !$user->hasRole('president-territorial') && !$user->hasRole('president-prize')) { 
            return redirect('/projects');
        }
        $phase = $this->contest->status;
        if($user->role != 'admin' && (($phase != 'deliberating-territorial' && $phase != 'deliberating-national')
            || ($phase == 'deliberating-territorial' && !$user->hasRole('president-territorial'))
            || ($phase == 'deliberating-national' && !$user->hasRole('president-prize')))) {
            return redirect('/awards');
        }

        $project = Project::find($request->get('project_id'));
        if(!$project) {
            return redirect('/awards');
        }
        $awardable = $this->getAwardablePrizes($user, $project);
        
        // Handle regular prize from dropdown
        $awardable_id = $request->get('awardable_id');
        $comment = $request->get('comment');
        
        // Handle special prizes from checkboxes
        $special_prize_ids = $request->get('special_prize_ids', []);
        
        // Check if awardable_id is set (including 0 which is valid)
        $hasRegularPrize = $awardable_id !== null && $awardable_id !== '' && $awardable_id !== 'Aucun prix';
        
        // Comment is required if regular prize is selected
        if($hasRegularPrize && !trim($comment)) {
            return redirect()->back()->withErrors(['Le commentaire est requis pour attribuer un prix.']);
        }
        
        // Remove existing regular award for this user if "Aucun prix" is selected or nothing selected
        if(!$hasRegularPrize) {
            $existingRegularAward = Award::where('contest_id', $this->contest->id)
                ->where('project_id', $project->id)
                ->whereHas('prize', function($q) {
                    $q->whereNull('special');
                })
                ->first();
            
            if($existingRegularAward) {
                $existingRegularAward->delete();
            }
        }
        
        // Process regular prize if selected (check for !== null to allow 0)
        if($hasRegularPrize) {
            $awarded = isset($awardable[$awardable_id]) ? $awardable[$awardable_id] : null;
            if(!$awarded) {
                return redirect()->back()->withErrors(['Prix invalide sélectionné.']);
            }
            
            // Get the prize to check if it's special
            $prize = Prize::find($awarded['prize_id']);
            if (!$prize) {
                return redirect()->back()->withErrors(['Prix invalide sélectionné.']);
            }
            
            // Validation for special prizes (shouldn't happen in regular dropdown but check anyway)
            if ($prize->special) {
                return redirect()->back()->withErrors(['Prix invalide sélectionné.']);
            }
            
            // Delete any existing regular award for this user/project
            Award::where('contest_id', $this->contest->id)
                ->where('project_id', $project->id)
                ->whereHas('prize', function($q) {
                    $q->whereNull('special');
                })
                ->delete();
            
            // Create new award
            $award = new Award();
            $award->contest_id = $this->contest->id;
            $award->project_id = $project->id;
            $award->user_id = $user->id;
            $award->prize_id = $awarded['prize_id'];
            $award->region_id = $awarded['region_id'];
            $award->comment = $comment;
            $award->save();

            // Ensure only ONE award per prize per region/national level (across all users)
            $other_awarded = Award::where('contest_id', $this->contest->id)
                ->where('prize_id', $awarded['prize_id'])
                ->where('region_id', $awarded['region_id'])
                ->where('id', '!=', $award->id)
                ->get();
            foreach($other_awarded as $other) {
                $other->delete();
            }
        }
        
        // Remove all special prizes for this user first, then re-add checked ones
        Award::where('contest_id', $this->contest->id)
            ->where('project_id', $project->id)
            ->where('user_id', $user->id)
            ->whereHas('prize', function($q) {
                $q->whereNotNull('special');
            })
            ->delete();
        
        // Process special prizes from checkboxes
        foreach($special_prize_ids as $special_id) {
            if(!isset($awardable[$special_id])) {
                continue;
            }
            
            $awarded = $awardable[$special_id];
            $prize = Prize::find($awarded['prize_id']);
            if (!$prize || !$prize->special) {
                continue;
            }
            
            // Validation for special prizes (laureat type)
            if ($prize->special == 'laureat') {
                // Check if project has exactly 1 regular (non-special) award (across all users)
                $regularAwardsCount = Award::where('contest_id', $this->contest->id)
                    ->where('project_id', $project->id)
                    ->whereHas('prize', function($q) {
                        $q->whereNull('special');
                    })
                    ->count();
                
                if ($regularAwardsCount != 1) {
                    continue; // Skip this special prize if validation fails
                }
            }
            
            // Create new special award
            $award = new Award();
            $award->contest_id = $this->contest->id;
            $award->project_id = $project->id;
            $award->user_id = $user->id;
            $award->prize_id = $awarded['prize_id'];
            $award->region_id = $awarded['region_id'];
            $award->comment = ''; // Special prizes don't have comments
            $award->save();

            // Ensure only ONE award per prize per region/national level (across all users)
            $other_awarded = Award::where('contest_id', $this->contest->id)
                ->where('prize_id', $awarded['prize_id'])
                ->where('region_id', $awarded['region_id'])
                ->where('id', '!=', $award->id)
                ->get();
            foreach($other_awarded as $other) {
                $other->delete();
            }
        }

        return redirect()->route('projects.show', ['project' => $project->id]);
    }

    public function export(Request $request)
    {
        $user = $request->user();
        if(!$user->role == 'admin' && !$user->hasRole('president-territorial') && !$user->hasRole('president-prize')) { 
            return redirect('/projects');
        }

        if($user->role == 'admin') {
            $q = Award::where('contest_id', $this->contest->id)->orderBy('prize_id', 'asc')->orderBy('region_id', 'asc');
        } else {
            $q = Award::where('contest_id', $this->contest->id)->where('user_id', $user->id)->orderBy('prize_id', 'asc')->orderBy('region_id', 'asc');

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
