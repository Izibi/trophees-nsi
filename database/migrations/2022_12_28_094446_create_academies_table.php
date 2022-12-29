<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Academy;
use App\Models\Region;
use Illuminate\Support\Facades\Artisan;

class CreateAcademiesTable extends Migration
{


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $new_install = Region::count() == 0;
        if(!$new_install) {
            Region::insert([
                'id' => 22,
                'name' => 'Collectivités Outre-Mer',
                'country_id' => null,
                'created_at' => \Carbon\Carbon::now()
            ]);
            Region::where('id', 21)->update([
                'name' => 'Lycées français à l\'étranger'
            ]);
        }

        Schema::create('academies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->bigInteger('region_id')->unsigned()->nullable()->index();
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->bigInteger('academy_id')->unsigned()->nullable()->index();
            $table->foreign('academy_id')->references('id')->on('academies')->onDelete('cascade')->onUpdate('cascade');
        });
        if(!$new_install) {
            Artisan::call('db:seed', [
                '--class' => 'AcademiesSeeder',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropForeign('schools_academy_id_foreign');
            $table->dropColumn('academy_id');
        });
        Schema::dropIfExists('academies');

        if(Region::count()) {
            Region::where('id', 21)->update([
                'name' => 'Étranger (AEFE)'
            ]);
            Region::where('id', 22)->delete();
        }
    }

}
