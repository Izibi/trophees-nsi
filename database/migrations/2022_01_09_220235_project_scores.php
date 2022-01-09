<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProjectScores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->float('score_total')->unsigned()->nullable()->index();
            $table->float('score_idea')->unsigned()->nullable();
            $table->float('score_communication')->unsigned()->nullable();
            $table->float('score_presentation')->unsigned()->nullable();
            $table->float('score_image')->unsigned()->nullable();
            $table->float('score_logic')->unsigned()->nullable();
            $table->float('score_creativity')->unsigned()->nullable();
            $table->float('score_organisation')->unsigned()->nullable();
            $table->float('score_operationality')->unsigned()->nullable();
            $table->float('score_ouverture')->unsigned()->nullable();
            $table->integer('ratings_amount')->unsigned()->nullable();
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
            $table->dropColumn('score_total');
            $table->dropColumn('score_idea');
            $table->dropColumn('score_communication');
            $table->dropColumn('score_presentation');
            $table->dropColumn('score_image');
            $table->dropColumn('score_logic');
            $table->dropColumn('score_creativity');
            $table->dropColumn('score_organisation');
            $table->dropColumn('score_operationality');
            $table->dropColumn('score_ouverture');
            $table->dropColumn('ratings_amount');
        });
    }
}
