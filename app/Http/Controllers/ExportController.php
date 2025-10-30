<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ExportController extends Controller
{

    public function users() {
        $callback = function() {
            $fh = fopen('php://output', 'w');
            $columns = ['ID', 'Date de création', 'Nom', 'Login', 'Email', 'Email secondaire', 'Rôle', 'Validé', 'Région', 'Pays', 'Dernière connexion', 'Estimation du nombre de projets', 'Date de mise à jour de l\'estimation', 'Etablissements scolaires'];
            fputcsv($fh, $columns);

            User::with('country', 'region', 'schools')->chunk(100, function($users) use ($fh) {
                foreach($users as $user) {
                    // Get schools information
                    $schools = $user->schools;
                    $schoolNames = [];
                    foreach($schools as $school) {
                        $schoolInfo = $school->name;
                        if($school->address) {
                            $schoolInfo .= ', ' . $school->address;
                        }
                        if($school->zip || $school->city) {
                            $schoolInfo .= ', ' . ($school->zip ? $school->zip . ' ' : '') . ($school->city ?? '');
                        }
                        $schoolNames[] = $schoolInfo;
                    }
                    $schoolsText = implode(' | ', $schoolNames);

                    $row = [
                        $user->id,
                        $user->created_at,
                        $user->name,
                        $user->login,
                        $user->email,
                        $user->secondary_email,
                        $user->role,
                        $user->validated,
                        !is_null($user->region_id) ? $user->region->name : '',
                        !is_null($user->country_id) ? $user->country->name : '',
                        $user->last_login_at,
                        $user->estimated ?? '',
                        $user->estimated_update ?? '',
                        $schoolsText
                    ];
                    fputcsv($fh, $row);
                }
            });

            fclose($fh);
        };

        return $this->outputFile('trophees_nsi_users.csv', $callback);

    }




    private function outputFile($file_name, $callback) {
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
