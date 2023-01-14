<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAwards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('award_premier')->boolean()->default(false); // Prix du meilleur projet Première
            $table->boolean('award_terminal')->boolean()->default(false); // Prix du meilleur projet Terminale
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
            $table->dropColumn('award_premier');
            $table->dropColumn('award_terminal');
        });
    }
}
