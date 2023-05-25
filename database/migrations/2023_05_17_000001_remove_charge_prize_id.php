<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveChargePrizeId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = DB::table('users')->whereNotNull('charge_prize_id')->get();
        foreach ($users as $user) {
            DB::table('prize_user')->insert([
                'user_id' => $user->id,
                'prize_id' => $user->charge_prize_id,
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['charge_prize_id']);
            $table->dropColumn('charge_prize_id');
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
            $table->bigInteger('charge_prize_id')->unsigned()->nullable()->index();
            $table->foreign('charge_prize_id')->references('id')->on('prizes')->onDelete('cascade')->onUpdate('cascade');
        });
    }
}
