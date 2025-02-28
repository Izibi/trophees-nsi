<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contest;
use App\Models\Region;
use App\Models\User;
use App\Models\Project;
use App\Models\Rating;
use App\Models\Grade;
use App\Classes\ActiveContest;


class StatisticsController extends Controller
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
        $data = ['data' => $this->getData(), 'regional_data' => []];
        if(!$isAdmin) {
            $data['regional_data'] = $this->getData($request->user());
        }
        return view('statistics.index', $data);
    }


    private function getData($user = null) {
        $data = [];
        if($user) {
            if($user->role === 'admin') {
                $regions = Region::get();
            } elseif($user->role === 'jury') {
                $roles = $user->roles()->where('type', 'territorial')->get();
                $regions = [];
                foreach($roles as $role) {
                    $regions[] = Region::find($role->target_id);
                }
            } else {
                $regions = [Region::find($user->region_id)];

            }
        } else {
            $regions = Region::get();
        }
        $i = 0;
        $premiere = Grade::where('name', 'Première')->get()->first()->id;
        $terminale = Grade::where('name', 'Terminale')->get()->first()->id;
        foreach($regions as $region) {
            if(!count($region->academies)) {
                continue;
            }
            $region_data = [
                'name' => $region->name,
                'teachers' => 0,
                'academies' => []
            ];
            $validatedStatus = $user ? 'validated' : ['finalized', 'validated'];
            foreach($region->academies as $academy) {
                $projects = $this->getProjects($academy);
                $academy_data = [
                    'name' => $academy->name,
                    'teachers' => $this->countTeachers($projects),
                    'projects_draft' => $this->countProjects($projects, 'draft'),
                    'projects_finalized_premiere' => $this->countProjects($projects, $validatedStatus, $premiere),
                    'projects_finalized_terminale' => $this->countProjects($projects, $validatedStatus, $terminale),
                    'projects_finalized' => $this->countProjects($projects, $validatedStatus),
                    'accent_row' => $i % 2 == 0
                ];
                if($user) {
                    foreach(['validated', 'incomplete'] as $status) {
                        $academy_data['projects_'.$status] = $this->countProjects($projects, $status);
                        $academy_data['projects_'.$status.'_premiere'] = $this->countProjects($projects, $status, $premiere);
                        $academy_data['projects_'.$status.'_terminale'] = $this->countProjects($projects, $status, $terminale);
                    }
                }
                $region_data['academies'][] = $academy_data;
                $region_data['teachers'] += $academy_data['teachers'];
            }
            $data[] = $region_data;
            $i++;
        }
        return $data;
    }

    private function getProjects($academy) {
        return Project::where('contest_id', $this->contest->id)->whereHas('school', function($q) use ($academy) {
            $q->where('academy_id', $academy->id);
        })->get();
    }

    private function countTeachers($projects) {
        return $projects->groupBy('user_id')->count();
    }

    private function countProjects($projects, $status = null, $grade = null) {
        if($status) {
            $projects = $projects->where('status', $status);
        }
        if($grade) {
            $projects = $projects->where('grade_id', $grade);
        }
        return $projects->count();
    }

    public function export_detail(Request $request) {
        return $this->export($request, true);
    }

    public function export(Request $request, $detail = false)
    {
        $isAdmin = $request->user()->role == 'admin';
        if(!$isAdmin && !$request->user()->hasRole('coordinator')) { 
            return redirect('/');
        }
        $data = $this->getData($detail ? $request->user() : null);

        $callback = function() use ($data, $detail) {
            $fh = fopen('php://output', 'w');
            $header = ['Région', 'Enseignants régionaux', 'Académie', 'Enseignants académiques', 'Projets en cours', 'Projets finalisés', 'Projets finalisés 1ère', 'Projets finalisés Tle'];
            if($detail) {
                $header = array_merge($header, ['Projets validés 1ère', 'Projets validés Tle', 'Projets validés', 'Projets incomplets 1ère', 'Projets incomplets Tle', 'Projets incomplets']);
            }
            fputcsv($fh, $header);

            foreach($data as $region_data) {
                foreach($region_data['academies'] as $academy_data) {
                    $row = [
                        $region_data['name'],
                        $region_data['teachers'],
                        $academy_data['name'],
                        $academy_data['teachers'],
                        $academy_data['projects_draft'],
                        $academy_data['projects_finalized'],
                        $academy_data['projects_finalized_premiere'],
                        $academy_data['projects_finalized_terminale']
                    ];
                    if($detail) {
                        $row = array_merge($row, [
                            $academy_data['projects_validated_premiere'],
                            $academy_data['projects_validated_terminale'],
                            $academy_data['projects_validated'],
                            $academy_data['projects_incomplete_premiere'],
                            $academy_data['projects_incomplete_terminale'],
                            $academy_data['projects_incomplete']
                        ]);
                    }
                    fputcsv($fh, $row);
                }
            };
            fclose($fh);
        };

        $file_name = 'trophees_nsi_statistiques' . ($detail ? '' : '_nationales') . '.csv';
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
