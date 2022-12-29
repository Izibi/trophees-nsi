<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Academy;
use App\Models\Region;

class CreateAcademiesTable extends Migration
{


    protected $rows_data = [
        [ "region_id" => 1,  "name" => "Clermont-Ferrand"],
        [ "region_id" => 1,  "name" => "Lyon"],
        [ "region_id" => 1,  "name" => "Grenoble"],
        [ "region_id" => 2,  "name" => "Besançon"],
        [ "region_id" => 2,  "name" => "Dijon"],
        [ "region_id" => 3,  "name" => "Rennes"],
        [ "region_id" => 4,  "name" => "Orléans-Tours"],
        [ "region_id" => 5,  "name" => "Corse"],
        [ "region_id" => 6,  "name" => "Nancy-Metz"],
        [ "region_id" => 6,  "name" => "Reims"],
        [ "region_id" => 6,  "name" => "Strasbourg"],
        [ "region_id" => 7,  "name" => "Guadeloupe"],
        [ "region_id" => 8,  "name" => "Guyane"],
        [ "region_id" => 9,  "name" => "Lille"],
        [ "region_id" => 9,  "name" => "Amiens"],
        [ "region_id" => 18, "name" => "Paris"],
        [ "region_id" => 20, "name" => "Créteil"],
        [ "region_id" => 19, "name" => "Versailles"],
        [ "region_id" => 10, "name" => "La Réunion"],
        [ "region_id" => 11, "name" => "Martinique"],
        [ "region_id" => 12, "name" => "Mayotte"],
        [ "region_id" => 13, "name" => "Normandie"],
        [ "region_id" => 14, "name" => "Bordeaux"],
        [ "region_id" => 14, "name" => "Limoges"],
        [ "region_id" => 14, "name" => "Poitiers"],
        [ "region_id" => 15, "name" => "Montpellier"],
        [ "region_id" => 15, "name" => "Toulouse"],
        [ "region_id" => 16, "name" => "Nantes"],
        [ "region_id" => 17, "name" => "Aix-Marseille"],
        [ "region_id" => 17, "name" => "Nice"],
        [ "region_id" => 21, "name" => "AEFE"],
        [ "region_id" => 22, "name" => "Wallis-et-Futuna"],
        [ "region_id" => 22, "name" => "Polynésie française"],
        [ "region_id" => 22, "name" => "Nouvelle-Calédonie"],
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Region::insert([
            'id' => 22,
            'name' => 'Collectivités Outre-Mer',
            'country_id' => null,
            'created_at' => \Carbon\Carbon::now()
        ]);
        Region::where('id', 21)->update([
            'name' => 'Lycées français à l\'étranger'
        ]);

        Schema::create('academies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->bigInteger('region_id')->unsigned()->nullable()->index();
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade')->onUpdate('cascade');
        });
        Academy::insert($this->rows_data);

        Schema::table('schools', function (Blueprint $table) {
            $table->bigInteger('academy_id')->unsigned()->nullable()->index();
            $table->foreign('academy_id')->references('id')->on('academies')->onDelete('cascade')->onUpdate('cascade');
        });
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

        Region::where('id', 21)->update([
            'name' => 'Étranger (AEFE)'
        ]);
        Region::where('id', 22)->delete();
    }

}
