<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddPhaseToRatings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->string('phase', 20)->nullable()->after('project_id'); // Values: 'territorial' or 'national'
            $table->index(['project_id', 'phase']);
        });
        
        // Backfill existing ratings to 'territorial'
        DB::table('ratings')->update(['phase' => 'territorial']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropIndex(['project_id', 'phase']);
            $table->dropColumn('phase');
        });
    }
}
