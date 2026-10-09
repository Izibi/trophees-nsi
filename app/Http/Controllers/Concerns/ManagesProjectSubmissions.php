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
     * $for_teacher is reserved for checks that will only apply when the teacher
     * finalizes the project. It is undecided for now : all checks currently
     * apply to both students and teachers.
     */
    private function projectCompletenessErrors(Project $project, bool $for_teacher = false)
    {
        $errors = [];
        $config = config('nsi.project');

        if(empty($project->school_id)) {
            $errors[] = 'Établissement scolaire manquant.';
        }
        if(empty($project->grade_id)) {
            $errors[] = 'Niveau scolaire manquant.';
        }

        $team_size = $project->team_girls + $project->team_boys + $project->team_not_provided;
        if($team_size == 0) {
            $errors[] = 'Les membres de l\'équipe ne sont pas renseignés.';
        } elseif($team_size < $config['team_size_min'] || $team_size > $config['team_size_max']) {
            $errors[] = strtr('La taille totale de l\'équipe doit être entre team_size_min et team_size_max.', $config);
        }

        if(empty($project->image_file)) {
            $errors[] = 'Image manquante.';
        }
        foreach($project->team_members as $team_member) {
            if(empty($team_member->parental_permissions_file)) {
                $errors[] = 'Autorisations parentales manquantes.';
                break;
            }
            if(empty($team_member->first_name) || empty($team_member->last_name)) {
                $errors[] = 'Il manque des informations dans la liste des membres de l\'équipe.';
                break;
            }
        }
        if(empty($project->url)) {
            $errors[] = 'URL du projet manquant.';
        }
        return $errors;
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
