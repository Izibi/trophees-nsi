<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationServerLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_server_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('username');
            $table->timestamp('login_date');
            $table->timestamp('logout_date')->nullable();
            $table->index('username');
            $table->index('logout_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluation_server_log');
    }
}
