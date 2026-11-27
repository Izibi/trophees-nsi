<?php

namespace App\Exports;

use App\Models\User;

/**
 * Territory / prize scopes of a user: the single source of truth for "what
 * data may this user see" in the exports.
 *
 *  - coordinator: their own region;
 *  - every territorial / président-territorial role: that region;
 *  - every prize / président-prize role: that prize.
 *
 * Shared by the projects export (region / prize project views) and the
 * laureates export (award filtering), so both stay consistent with each
 * other and with the access rules used elsewhere in the application.
 */
class UserScopeViews
{
    /**
     * @param User $user
     * @return array[] list of ['type' => region|prize, 'target_id' => int]
     */
    public static function for(User $user)
    {
        $views = [];
        if ($user->hasRole('coordinator') && $user->region_id) {
            $views['region:' . $user->region_id] = ['type' => 'region', 'target_id' => $user->region_id];
        }
        foreach ($user->roles as $role) {
            if (in_array($role->type, ['territorial', 'president-territorial'], true) && $role->target_id) {
                $views['region:' . $role->target_id] = ['type' => 'region', 'target_id' => $role->target_id];
            }
            if (in_array($role->type, ['prize', 'president-prize'], true) && $role->target_id) {
                $views['prize:' . $role->target_id] = ['type' => 'prize', 'target_id' => $role->target_id];
            }
        }
        return array_values($views);
    }
}
