<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProjectsAddFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->smallInteger('class_girls')->nullable();
            $table->smallInteger('class_boys')->nullable();
            $table->smallInteger('class_not_provided')->nullable();
            $table->string('parental_permissions_file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('class_girls');
            $table->dropColumn('class_boys');
            $table->dropColumn('class_not_provided');
            $table->dropColumn('parental_permissions_file');
        });
    }
}
