<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('project_id')->unsigned()->index();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade')->onUpdate('cascade');            
            $table->smallInteger('score_idea')->unsigned()->default(0);
            $table->smallInteger('score_communication')->unsigned()->default(0);
            $table->smallInteger('score_presentation')->unsigned()->default(0);
            $table->smallInteger('score_image')->unsigned()->default(0);
            $table->smallInteger('score_logic')->unsigned()->default(0);
            $table->smallInteger('score_creativity')->unsigned()->default(0);
            $table->smallInteger('score_organisation')->unsigned()->default(0);
            $table->smallInteger('score_operationality')->unsigned()->default(0);
            $table->smallInteger('score_ouverture')->unsigned()->default(0);
            $table->smallInteger('score_total')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ratings');
    }
}
