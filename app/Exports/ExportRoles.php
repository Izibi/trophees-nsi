<?php

namespace App\Exports;

use App\Models\User;

/**
 * Maps an authenticated user to the export role keys used by the column
 * registry (export_csv.csv uses: Admin, Coordinateur, Président territorial,
 * Président national).
 */
class ExportRoles
{
    /**
     * @param User $user
     * @return string[]
     */
    public static function for(User $user)
    {
        $roles = [];
        if ($user->role === 'admin') {
            $roles[] = 'admin';
        }
        if ($user->hasRole('coordinator')) {
            $roles[] = 'coordinator';
        }
        if ($user->hasRole('president-territorial')) {
            $roles[] = 'president-territorial';
        }
        if ($user->hasRole('president-prize')) {
            $roles[] = 'president-prize';
        }
        return $roles;
    }
}
