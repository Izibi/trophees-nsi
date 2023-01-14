<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\User;
use App\Models\Project;

class StatisticsController extends Controller
{

    public function index() {
        return view('statistics.index', [
            'data' => $this->getData()
        ]);
    }


    private function getData() {
        $data = [];
        $regions = Region::get();
        $i = 0;
        foreach($regions as $region) {
            if(!count($region->academies)) {
                continue;
            }
            $region_data = [
                'name' => $region->name,
                'teachers' => 0,
                'academies' => []
            ];
            foreach($region->academies as $academy) {
                $academy_data = [
                    'name' => $academy->name,
                    'teachers' => $this->countTeachers($academy),
                    'projects_draft' => $this->countProjectsDraft($academy),
                    'projects_finalized_premier' => $this->countProjectsFinalizedPremier($academy),
                    'projects_finalized_terminal' => $this->countProjectsFinalizedTerminal($academy),
                    'projects_finalized' => $this->countProjectsFinalized($academy),
                    'accent_row' => $i % 2 == 0
                ];
                $region_data['academies'][] = $academy_data;
                $region_data['teachers'] += $academy_data['teachers'];
            }
            $data[] = $region_data;
            $i++;
        }
        return $data;
    }


    private function countTeachers($academy) {
        return User::whereHas('schools', function($q) use ($academy) {
            $q->where('academy_id', $academy->id);
        })->count();
    }

    private function countProjectsDraft($academy) {
        return Project::whereHas('school', function($q) use ($academy) {
            $q->where('academy_id', $academy->id);
        })->where('status', 'draft')->count();
    }

    private function countProjectsFinalizedPremier($academy) {
        return Project::whereHas('school', function($q) use ($academy) {
            $q->where('academy_id', $academy->id);
        })->where('status', 'finalized')->where('award_terminal', 1)->count();
    }

    private function countProjectsFinalizedTerminal($academy) {
        return Project::whereHas('school', function($q) use ($academy) {
            $q->where('academy_id', $academy->id);
        })->where('status', 'finalized')->where('award_terminal', 1)->count();
    }

    private function countProjectsFinalized($academy) {
        return Project::whereHas('school', function($q) use ($academy) {
            $q->where('academy_id', $academy->id);
        })->where('status', 'finalized')->count();
    }
}