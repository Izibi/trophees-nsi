<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class TeamMembersGender extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	DB::statement("ALTER TABLE team_members MODIFY COLUMN gender ENUM('', 'male', 'female', 'other');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	DB::statement("ALTER TABLE team_members MODIFY COLUMN gender ENUM('', 'male', 'female');");
    }
}
