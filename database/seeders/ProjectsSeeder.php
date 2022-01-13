<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\School;
use App\Models\Grade;
use App\Models\User;

class ProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('role', 'teacher')->first();
        $school = School::find(1);
        $grade = Grade::find(1);
        Project::create([
            'name' => 'Test project 1',
            'user_id' => $user->id,
            'school_id' => $school->id,
            'grade_id' => $grade->id,
            'team_girls' => 1,
            'team_boys' => 2,
            'team_not_provided' => 3,
            'description' => 'description text',
            'video' => '<iframe width="560" height="315" sandbox="allow-same-origin allow-scripts allow-popups" title="Cette erreur idiote qu&#39;on fait tous au sujet des Bogdanoff... • la parenthèse - #2" src="https://peertube.fr/videos/embed/33c242ed-566c-49a0-a2d8-2f332c6dcdd0" frameborder="0" allowfullscreen></iframe>',
            //'presentation_file' => '123',
            'status' => 'validated'
        ]);

        Project::create([
            'name' => 'Test project 2',
            'user_id' => $user->id,
            'school_id' => $school->id,
            'grade_id' => $grade->id,
            'team_girls' => 3,
            'team_boys' => 2,
            'team_not_provided' => 1,
            'description' => 'description text',
            'video' => '<iframe width="560" height="315" sandbox="allow-same-origin allow-scripts allow-popups" title="Cette erreur idiote qu&#39;on fait tous au sujet des Bogdanoff... • la parenthèse - #2" src="https://peertube.fr/videos/embed/33c242ed-566c-49a0-a2d8-2f332c6dcdd0" frameborder="0" allowfullscreen></iframe>',
            //'presentation_file' => '123',
            'status' => 'draft'
        ]);

        Project::create([
            'name' => 'Test project 3',
            'user_id' => $user->id,
            'school_id' => $school->id,
            'grade_id' => $grade->id,
            'team_girls' => 1,
            'team_boys' => 1,
            'team_not_provided' => 1,
            'description' => 'description text',
            'video' => '<iframe width="560" height="315" sandbox="allow-same-origin allow-scripts allow-popups" title="Cette erreur idiote qu&#39;on fait tous au sujet des Bogdanoff... • la parenthèse - #2" src="https://peertube.fr/videos/embed/33c242ed-566c-49a0-a2d8-2f332c6dcdd0" frameborder="0" allowfullscreen></iframe>',
            //'presentation_file' => '123',
            'status' => 'draft'
        ]);        
    }
}
