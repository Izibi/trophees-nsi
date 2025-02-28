<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeContestStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE contests MODIFY status ENUM('preparing', 'open', 'instruction', 'grading-territorial', 'deliberating-territorial', 'grading-national', 'deliberating-national', 'closed')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE contests MODIFY status ENUM('preparing', 'open', 'grading', 'deliberating', 'closed')");
    }
}
