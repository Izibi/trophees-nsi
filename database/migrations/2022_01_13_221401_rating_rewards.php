<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RatingRewards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->boolean('award_mixed')->boolean()->default(false); // mixité
            $table->boolean('award_citizenship')->boolean()->default(false); //  citoyenneté
            $table->boolean('award_engineering')->boolean()->default(false); // ingénierie
            $table->boolean('award_heart')->boolean()->default(false); // coup de coeur
            $table->boolean('award_originality')->boolean()->default(false); // originalité
            $table->text('notes')->nullable();
        });


        Schema::table('projects', function (Blueprint $table) {
            $table->integer('award_mixed')->unsigned()->nullable();
            $table->integer('award_citizenship')->unsigned()->nullable();
            $table->integer('award_engineering')->unsigned()->nullable();
            $table->integer('award_heart')->unsigned()->nullable();
            $table->integer('award_originality')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropColumn('award_mixed');
            $table->dropColumn('award_citizenship');
            $table->dropColumn('award_engineering');
            $table->dropColumn('award_heart');
            $table->dropColumn('award_originality');
            $table->dropColumn('notes');
        });


        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('award_mixed');
            $table->dropColumn('award_citizenship');
            $table->dropColumn('award_engineering');
            $table->dropColumn('award_heart');
            $table->dropColumn('award_originality');
        });
    }
}