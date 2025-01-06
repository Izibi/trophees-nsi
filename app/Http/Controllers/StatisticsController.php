<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contest;
use App\Models\Region;
use App\Models\User;
use App\Models\Project;
use App\Models\Rating;
use App\Models\Grade;

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
                $projects = $this->getProjects($academy);
                $academy_data = [
                    'name' => $academy->name,
                    'teachers' => $this->countTeachers($projects),
                    'projects_draft' => $this->countProjectsDraft($projects),
                    'projects_finalized_premiere' => $this->countProjectsFinalizedPremiere($projects),
                    'projects_finalized_terminale' => $this->countProjectsFinalizedTerminale($projects),
                    'projects_finalized' => $this->countProjectsFinalized($projects),
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


    private function getProjects($academy) {
        $year = Contest::where('status', 'open')->get()->first();
        return Project::where('contest_id', $year->id)->whereHas('school', function($q) use ($academy) {
            $q->where('academy_id', $academy->id);
        })->get();
    }

    private function countTeachers($projects) {
        return $projects->groupBy('user_id')->count();
    }

    private function countProjectsDraft($projects) {
        return $projects->where('status', 'draft')->count();
    }

    private function countProjectsFinalizedPremiere($projects) {
        $grade = Grade::where('name', 'Première')->get()->first()->id;
        return $projects->whereIn('status', ['finalized', 'validated'])->where('grade_id', $grade)->count();
    }

    private function countProjectsFinalizedTerminale($projects) {
        $grade = Grade::where('name', 'Terminale')->get()->first()->id;
        return $projects->whereIn('status', ['finalized', 'validated'])->where('grade_id', $grade)->count();
    }

    private function countProjectsFinalized($projects) {
        return $projects->whereIn('status', ['finalized', 'validated'])->count();
    }
}
