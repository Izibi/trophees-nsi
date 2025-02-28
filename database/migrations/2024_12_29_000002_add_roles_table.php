<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create new table roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('user_id');
            $table->string('type');
            $table->unsignedBigInteger('target_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->unique(['user_id', 'type', 'target_id']);
            $table->index('user_id');
        });

        // Migrate the data from the users table
        DB::statement('
            INSERT INTO roles (created_at, updated_at, user_id, type, target_id)
            (SELECT NOW(), NOW(), id, "territorial", region_id
            FROM users
            WHERE role="jury" AND region_id IS NOT NULL)
        ');

        DB::statement('
            INSERT INTO roles (created_at, updated_at, user_id, type, target_id)
            (SELECT NOW(), NOW(), id, "coordinator", NULL
            FROM users
            WHERE coordinator = 1)
        ');

        DB::statement('
            INSERT INTO roles (created_at, updated_at, user_id, type, target_id)
            (SELECT NOW(), NOW(), user_id, "prize", prize_id
            FROM prize_user)
        ');

        // Drop the columns from the users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('coordinator');
        });

        // Drop the prize_user table
        Schema::dropIfExists('prize_user');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('coordinator')->default(false);
        });

        Schema::create('prize_user', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');;
            $table->bigInteger('prize_id')->unsigned();
            $table->foreign('prize_id')->references('id')->on('prizes')->onUpdate('cascade')->onDelete('cascade');;
            $table->unique(['user_id', 'prize_id']);
        });

        DB::statement('
            UPDATE users
            JOIN roles ON users.id = roles.user_id
            SET users.region_id = roles.target_id
            WHERE roles.type = "territorial"
        ');

        DB::statement('
            UPDATE users
            JOIN roles ON users.id = roles.user_id
            SET users.coordinator = 1
            WHERE roles.type = "coordinator"
        ');

        DB::statement('
            INSERT INTO prize_user (user_id, prize_id)
            (SELECT user_id, target_id
            FROM roles
            WHERE type = "prize")
        ');
    }
}
