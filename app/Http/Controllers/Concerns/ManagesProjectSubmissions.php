<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Project;
use App\Models\TeamMember;

trait ManagesProjectSubmissions
{
    /**
     * Completeness checks run when a project is marked as completed (student)
     * or finalized (teacher).
     *
     * All dossier fields are mandatory except the AI fields ("Part de l'IA" /
     * ai_usage_percent and "Usage de l'IA" / ai_usage_notes), and several
     * free-text fields also have a minimum number of characters (see the
     * minimum lengths in config/nsi.php).
     *
     * $for_teacher gates the checks that can only be satisfied by the teacher
     * (class gender split, teacher comment and certifications). Students
     * cannot edit these fields, so they are enforced at finalization but not
     * when the students mark the project as completed.
     *
     * Errors are keyed by form field name, in the same format as Laravel
     * validation errors (field => [messages]), so that the client-side editor
     * can display them inline, link them in the error summary list and
     * highlight the tabs containing them (see resources/js/UI/project-editor.js).
     * Keys are the literal control names as rendered in the form (e.g. array
     * fields keep their brackets).
     */
    private function projectCompletenessErrors(Project $project, bool $for_teacher = false)
    {
        $errors = [];
        $config = config('nsi.project');

        if(empty($project->name)) {
            $errors['name'][] = 'Nom du projet manquant.';
        }
        if(empty($project->school_id)) {
            $errors['school_id'][] = 'Établissement scolaire manquant.';
        }
        if(empty($project->grade_id)) {
            $errors['grade_id'][] = 'Niveau scolaire manquant.';
        }
        if($for_teacher) {
            $class_size = $project->class_girls + $project->class_boys + $project->class_not_provided;
            if($class_size == 0) {
                $errors['class_girls'][] = 'La répartition filles/garçons de la classe n\'est pas renseignée.';
            }
        }

        $team_size = $project->team_girls + $project->team_boys + $project->team_not_provided;
        if($team_size == 0) {
            $errors['team_member_first_name[]'][] = 'Les membres de l\'équipe ne sont pas renseignés.';
        } elseif($team_size < $config['team_size_min'] || $team_size > $config['team_size_max']) {
            $errors['team_member_first_name[]'][] = strtr('La taille totale de l\'équipe doit être entre team_size_min et team_size_max.', $config);
        }

        if(empty($project->image_file)) {
            $errors['image_file'][] = 'Image manquante.';
        }
        foreach($project->team_members as $team_member) {
            if(empty($team_member->parental_permissions_file)) {
                $errors['team_member_parental_permissions_file[]'][] = 'Autorisations parentales manquantes.';
                break;
            }
            if(empty($team_member->first_name) || empty($team_member->last_name)) {
                $errors['team_member_first_name[]'][] = 'Il manque des informations dans la liste des membres de l\'équipe.';
                break;
            }
        }
        if(empty($project->url)) {
            $errors['url'][] = 'URL du dossier technique manquant.';
        }
        if(empty($project->video)) {
            $errors['video'][] = 'URL de la vidéo de démonstration manquante.';
        }
        if(empty($project->description)) {
            $errors['description'][] = 'Résumé du projet manquant.';
        }

        // Long free-text fields with a minimum number of characters.
        $notes_min = $config['notes_min_length'];
        $notes_fields = [
            'work_organisation' => 'Organisation du travail et répartition des tâches',
            'project_description' => 'Description du projet',
            'project_steps' => 'Étapes de réalisation du projet',
            'project_outlook' => 'Ouverture',
            'operationality_notes' => 'Validation de l\'opérationnalité du projet',
        ];
        foreach($notes_fields as $field => $label) {
            $this->checkMinLength($errors, $field, $project->$field, $notes_min, $label);
        }
        // "Nature du code" : minimum length revised down from 50.
        $this->checkMinLength($errors, 'code_notes', $project->code_notes, $config['code_notes_min_length'], 'Nature du code');

        if($for_teacher) {
            $this->checkMinLength($errors, 'teacher_notes', $project->teacher_notes, $notes_min, 'Commentaire de l\'enseignant sur le projet et l\'équipe');
            if(!$project->tested_by_teacher) {
                $errors['cb_tested_by_teacher'][] = 'La certification « projet testé par l\'enseignant » n\'est pas cochée.';
            }
            if(!$project->video_authorization) {
                $errors['cb_video_authorization'][] = 'La certification « autorisations de diffusion de l\'image et de la voix » n\'est pas cochée.';
            }
            if(!$project->reglament_accepted) {
                $errors['cb_reglament_accepted'][] = 'L\'acceptation du règlement du concours n\'est pas cochée.';
            }
        }

        return $errors;
    }


    /**
     * Push an error when a free-text field is empty or shorter than the
     * required minimum number of characters. $field is the form field name
     * the error is keyed by.
     */
    private function checkMinLength(array &$errors, string $field, $value, int $min, string $label) {
        $len = mb_strlen(trim((string)$value));
        if($len < $min) {
            $errors[$field][] = $label.' : au moins '.$min.' caractères sont requis ('.$len.' actuellement).';
        }
    }


    private function syncTeamMembers($project, $request) {
        $members = $project->team_members->keyBy('id')->all();

        $team_member_id = $request->get('team_member_id') ?? [];
        $parental_permissions_file = $request->file('team_member_parental_permissions_file');

        $saved_ids = [];

        $project->team_girls = 0;
        $project->team_boys = 0;
        $project->team_not_provided = 0;
        for($i=0; $i<count($team_member_id); $i++) {
            $data = [
                'project_id' => $project->id,
                'first_name' => $request->get('team_member_first_name')[$i] ?? '',
                'last_name' => $request->get('team_member_last_name')[$i] ?? '',
                'gender' => $request->get('team_member_gender')[$i] ?? ''
            ];
            $id = $team_member_id[$i];

            if(empty($id)) {
                $member = new TeamMember($data);
            } else if(isset($members[$id])) {
                $saved_ids[$id] = true;
                $member = $members[$id];
                $member->fill($data);
            } else {
                // forged id (e.g. a team member of another project) : skip
                continue;
            }
            if(isset($parental_permissions_file[$i])) {
                $member->uploadFile($request->file('team_member_parental_permissions_file')[$i]);
            }
            $member->save();

            if($member->gender == 'male') {
                $project->team_boys++;
            } else if($member->gender == 'female') {
                $project->team_girls++;
            } else {
                $project->team_not_provided++;
            }
        }
        $project->save();

        foreach($members as $member) {
            if(!isset($saved_ids[$member->id])) {
                $member->delete();
            }
        }
    }


    private function deleteUploads($project, $request) {
        if($request->has('delete_uploads')) {
            $allowed_attrs = $project->upload_attributes;
            $to_delete = $request->get('delete_uploads');
            foreach($to_delete as $attr) {
                if(strpos($attr, '.') === false) {
                    continue;
                }
                list($target, $pointer) = explode('.', $attr, 2);
                if($target == 'project') {
                    if(in_array($pointer, $allowed_attrs)) {
                        $project->$pointer = null;
                    }
                } else if($target == 'team_member') {
                    $team_member = $project->team_members()->where('id', $pointer)->first();
                    if($team_member) {
                        $team_member->parental_permissions_file = null;
                        $team_member->save();
                    }
                }
            }
        }
    }
}
