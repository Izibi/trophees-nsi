<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DraftRatingNullableScores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->smallInteger('score_idea')->unsigned()->nullable()->change();
            $table->smallInteger('score_communication')->unsigned()->nullable()->change();
            $table->smallInteger('score_presentation')->unsigned()->nullable()->change();
            $table->smallInteger('score_image')->unsigned()->nullable()->change();
            $table->smallInteger('score_logic')->unsigned()->nullable()->change();
            $table->smallInteger('score_creativity')->unsigned()->nullable()->change();
            $table->smallInteger('score_organisation')->unsigned()->nullable()->change();
            $table->smallInteger('score_operationality')->unsigned()->nullable()->change();
            $table->smallInteger('score_ouverture')->unsigned()->nullable()->change();
            $table->smallInteger('score_total')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('
            UPDATE
                ratings
            SET
                score_idea = IFNULL(score_idea, 0),
                score_communication = IFNULL(score_communication, 0),
                score_presentation = IFNULL(score_presentation, 0),
                score_image = IFNULL(score_image, 0),
                score_logic = IFNULL(score_logic, 0),
                score_creativity = IFNULL(score_creativity, 0),
                score_organisation = IFNULL(score_organisation, 0),
                score_operationality = IFNULL(score_operationality, 0),
                score_ouverture = IFNULL(score_ouverture, 0),
                score_total = IFNULL(score_total, 0)
        ');
        Schema::table('ratings', function (Blueprint $table) {
            $table->smallInteger('score_idea')->unsigned()->default(0)->nullable(false)->change();
            $table->smallInteger('score_communication')->unsigned()->default(0)->nullable(false)->change();
            $table->smallInteger('score_presentation')->unsigned()->default(0)->nullable(false)->change();
            $table->smallInteger('score_image')->unsigned()->default(0)->nullable(false)->change();
            $table->smallInteger('score_logic')->unsigned()->default(0)->nullable(false)->change();
            $table->smallInteger('score_creativity')->unsigned()->default(0)->nullable(false)->change();
            $table->smallInteger('score_organisation')->unsigned()->default(0)->nullable(false)->change();
            $table->smallInteger('score_operationality')->unsigned()->default(0)->nullable(false)->change();
            $table->smallInteger('score_ouverture')->unsigned()->default(0)->nullable(false)->change();
            $table->smallInteger('score_total')->unsigned()->default(0)->nullable(false)->change();
        });
    }
}
