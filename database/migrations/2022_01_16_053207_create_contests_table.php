<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->smallInteger('year');
            $table->text('message');
            $table->enum('status', ['preparing', 'open', 'grading', 'deliberating', 'closed']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->bigInteger('contest_id')->unsigned()->index();
            $table->foreign('contest_id')->references('id')->on('contests')->onDelete('cascade')->onUpdate('cascade');
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
            $table->dropForeign(['contest_id']);
            $table->dropColumn('contest_id');
        });
        Schema::dropIfExists('contests');
    }
}
