<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 'completed' is inserted between 'draft' and 'finalized' :
     * students mark a draft project as 'completed' when they are done,
     * the teacher then finalizes it.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE projects MODIFY status ENUM('draft', 'completed', 'finalized', 'validated', 'incomplete', 'masked') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE projects MODIFY status ENUM('draft', 'finalized', 'validated', 'incomplete', 'masked') DEFAULT 'draft'");
    }
};
