<?php
namespace App\Repositories;

use App\Models\Project;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class UserRepository {


    public function delete($user_id, $backup_user_id) {
        // transfer projects
        Project::where([
            'user_id' => $user_id
        ])->update([
            'user_id' => $backup_user_id
        ]);

        // delete ratings
        Rating::where([
            'user_id' => $user_id,
            'published' => 0
        ])->delete();
        Rating::where([
            'user_id' => $user_id,
            'published' => 1
        ])->update([
            'user_id' => null
        ]);

        // delete links between user and schools
        DB::table('school_user')->where([
            'user_id' => $user_id
        ])->delete();

        User::find($user_id)->delete();
    }

}