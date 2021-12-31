<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\School;
use App\Models\Grade;

class ProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $school = School::find(1);
        $grade = Grade::find(1);
        Project::create([
            'name' => 'Test project 1',
            'school_id' => $school->id,
            'grade_id' => $grade->id,
            'team_girls' => 1,
            'team_boys' => 2,
            'team_not_provided' => 3,
            'description' => 'description text',
            'video_url' => 'http://video.url',
            'presentation_file' => '123',
            'status' => 'draft'
        ]);

    }
}
