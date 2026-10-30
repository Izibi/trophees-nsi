<?php

namespace App\Exports;

use App\Models\User;

/**
 * Rows of the users export: same data and columns as the legacy
 * ExportController::users(), keyed by export column key.
 */
class UsersExportBuilder
{
    /**
     * @return \Generator rows keyed by column key
     */
    public function rows()
    {
        $page = 1;
        do {
            $users = User::with('country', 'region', 'schools')
                ->orderBy('id')
                ->forPage($page, 100)
                ->get();

            foreach ($users as $user) {
                $schoolNames = [];
                foreach ($user->schools as $school) {
                    $schoolInfo = $school->name;
                    if ($school->address) {
                        $schoolInfo .= ', ' . $school->address;
                    }
                    if ($school->zip || $school->city) {
                        $schoolInfo .= ', ' . ($school->zip ? $school->zip . ' ' : '') . ($school->city ?? '');
                    }
                    $schoolNames[] = $schoolInfo;
                }

                yield [
                    'id' => $user->id,
                    'created_at' => $user->created_at,
                    'name' => $user->name,
                    'login' => $user->login,
                    'email' => $user->email,
                    'secondary_email' => $user->secondary_email,
                    'role' => $user->role,
                    'validated' => $user->validated,
                    // Test the loaded relation, not the FK: region_id/country_id
                    // can point to a missing row (dangling reference).
                    'region' => $user->region ? $user->region->name : '',
                    'country' => $user->country ? $user->country->name : '',
                    'last_login_at' => $user->last_login_at,
                    'schools' => implode(' | ', $schoolNames),
                ];
            }

            $page++;
        } while (count($users) == 100);
    }
}
