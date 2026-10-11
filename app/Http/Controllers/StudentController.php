<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Grade;
use App\Models\Country;
use App\Models\Region;
use App\Models\Academy;
use App\Classes\ActiveContest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Controllers\Concerns\ManagesProjectSubmissions;

class StudentController extends Controller
{
    use ManagesProjectSubmissions;

    private $contest;

    public function __construct(ActiveContest $active_contest)
    {
        $this->contest = $active_contest->get();
    }


    public function loginForm(Request $request)
    {
        return view('student.login', [
            'error_message' => $request->session()->get('error_message'),
        ]);
    }


    public function login(Request $request)
    {
        $code = strtolower(trim($request->get('code', '')));
        $project = null;
        if(strlen($code) == 8) {
            $project = Project::where('student_code', $code)->first();
        }
        if(!$project) {
            return redirect('/connexion-eleve')->with('error_message', 'Code projet invalide.');
        }
        $session = $request->session();
        $session->regenerate();
        $session->put('student_project_id', $project->id);
        $session->put('student_project_code', $project->student_code);
        return redirect('/eleve/projet');
    }


    public function logout(Request $request)
    {
        $request->session()->forget(['student_project_id', 'student_project_code']);
        return redirect('/connexion-eleve');
    }


    public function edit(Request $request)
    {
        $project = $request->attributes->get('student_project');
        $blocked = $this->studentEditingBlockedReason($project);
        if($blocked) {
            return view('student.blocked', [
                'message' => $blocked,
            ]);
        }
        return view('projects.edit.index', [
            'editor_type' => 'student',
            'submit_route' => 'student.project.update',
            'project' => $project,
            'schools' => $this->getStudentSchools($project),
            'grades' => Grade::orderBy('name')->get(),
            'countries' => Country::orderBy('name')->get(),
            'regions' => Region::orderBy('country_id', 'desc')->orderBy('name')->get(),
            'academies' => Academy::orderBy('name')->get(),
            'refer_page' => '/eleve/projet',
        ]);
    }


    public function update(StoreProjectRequest $request)
    {
        $project = $request->attributes->get('student_project');
        $blocked = $this->studentEditingBlockedReason($project);
        if($blocked) {
            return response()->json(['errors' => ['project' => [$blocked]]], 422);
        }

        // Teacher-owned fields (per dossier_candidature.csv) are stripped from
        // student submissions : school, grade, class gender split, teacher
        // comment, certifications and finalization.
        $data = $request->except([
            'school_id',
            'grade_id',
            'class_girls',
            'class_boys',
            'class_not_provided',
            'teacher_notes',
            'status_message',
            'cb_tested_by_teacher',
            'tested_by_teacher',
            'cb_reglament_accepted',
            'reglament_accepted',
            'cb_video_authorization',
            'video_authorization',
            'finalize',
        ]);

        $this->deleteUploads($project, $request);
        $project->uploadFiles($request);
        $project->fill($data);
        $project->save();
        $this->syncTeamMembers($project, $request);

        if($request->has('complete')) {
            $errors = $this->projectCompletenessErrors($project, false);
            if(count($errors)) {
                // 422 with field-keyed errors, like validation failures : the
                // editor displays the linked error summary and highlights the
                // fields and tabs concerned.
                return response()->json(['errors' => $errors], 422);
            }
            $project->status = 'completed';
            $project->save();

            // Mail de notification à l'enseignant lorsque les élèves marquent le projet comme terminé :
            // à réactiver plus tard (prévoir les vues mails.status_change.completed et
            // mails.status_change.completed_plain, et l'entrée 'completed' dans App\Mail\StatusChanged).
            // \Mail::to($project->user->email)->send(new \App\Mail\StatusChanged($project));

            session()->flash('message', 'Projet marqué comme terminé');
            return response()->json(['location' => '/eleve/projet']);
        }

        session()->flash('message', 'Projet enregistré');
        return response()->json(['location' => '/eleve/projet']);
    }


    /**
     * Students can only edit draft projects, and only while the contest
     * deposit phase is open (during the 'completion' phase and later,
     * only teachers can finalize projects).
     */
    private function studentEditingBlockedReason(Project $project = null)
    {
        if(!$project) {
            return 'Projet introuvable.';
        }
        if(!in_array($project->status, ['draft', 'completed'])) {
            if($project->status == 'validated') {
                return 'Votre projet a été validé, veuillez patienter pendant les sélections.';
            }
            return 'Vous ne pouvez plus éditer ce projet.';
        }
        // Students can only edit while the deposit is open : during the
        // 'completion' phase and later, only teachers can still edit/finalize.
        if(!$this->contest || $this->contest->status != 'open') {
            return 'Le dépôt de projets est terminé : les élèves ne peuvent plus modifier leur projet, seul l\'enseignant peut désormais le finaliser.';
        }
        return null;
    }


    private function getStudentSchools(Project $project)
    {
        $options = [];
        $data = collect();
        if($project->school) {
            $school = $project->school;
            $title = $school->name.', '.$school->zip.' '.$school->city.', ';
            if($school->region && !is_null($school->region->country_id)) {
                $title .= $school->region->name.', ';
            }
            $title .= $school->country->name;
            $options[$school->id] = $title;
            $data = collect([$school]);
        }
        return [
            'data' => $data,
            'options' => $options
        ];
    }
}
