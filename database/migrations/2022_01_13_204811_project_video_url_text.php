<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ProjectVideoUrlText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->renameColumn('video_url', 'video');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->text('video')->nullable()->change();
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
            $table->renameColumn('video', 'video_url');
        });
        DB::table('projects')->where(DB::raw('LENGTH(video_url) > 255'))->update(['video_url' => '']);
        Schema::table('projects', function (Blueprint $table) {
            $table->string('video_url')->nullable()->change();
        });
    }
}
