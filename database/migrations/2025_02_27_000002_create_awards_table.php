<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAwardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*     protected $fillable = [
        'project_id',
        'prize_id',
        'region_id',
        'user_id',
        'comment'
    ];*/

        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('project_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('projects')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('prize_id')->unsigned();
            $table->foreign('prize_id')->references('id')->on('prizes')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('region_id')->unsigned();
            // Might be 0 for a national prize
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->text('comment')->nullable();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('awards');
    }
}
