<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Contest;
use Illuminate\Support\Facades\Artisan;

class CreatePrizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $new_install = Contest::count() == 0;
        Schema::create('prizes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
        });

        if(!$new_install) {
            Artisan::call('db:seed', [
                '--class' => 'PrizesSeeder',
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('charge_prize_id')->unsigned()->nullable()->index();
            $table->foreign('charge_prize_id')->references('id')->on('prizes')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->bigInteger('prize_id')->unsigned()->nullable()->index();
            $table->foreign('prize_id')->references('id')->on('prizes')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('prize_level', [
                'regional',
                'national'
            ])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_charge_prize_id_foreign');
            $table->dropColumn('charge_prize_id');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign('projects_prize_id_foreign');
            $table->dropColumn('prize_id');
            $table->dropColumn('prize_level');
        });
        Schema::dropIfExists('prizes');
    }
}
