<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 'completion' is inserted between 'open' and 'instruction' :
     * during this phase students can no longer edit their projects,
     * only teachers can finalize them.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE contests MODIFY status ENUM('preparing', 'open', 'completion', 'instruction', 'grading-territorial', 'deliberating-territorial', 'grading-national', 'deliberating-national', 'closed')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE contests MODIFY status ENUM('preparing', 'open', 'instruction', 'grading-territorial', 'deliberating-territorial', 'grading-national', 'deliberating-national', 'closed')");
    }
};
