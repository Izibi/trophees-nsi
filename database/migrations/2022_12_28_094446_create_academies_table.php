<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Academy;

class CreateAcademiesTable extends Migration
{


    protected $rows_data = [
        [ "key" => "aix-marseille", "name" => "Aix-Marseille"],
        [ "key" => "amiens", "name" => "Amiens"],
        [ "key" => "besancon", "name" => "Besançon"],
        [ "key" => "bordeaux", "name" => "Bordeaux"],
        [ "key" => "clermont-ferrand", "name" => "Clermont-Ferrand"],
        [ "key" => "corse", "name" => "Corse"],
        [ "key" => "creteil", "name" => "Créteil"],
        [ "key" => "dijon", "name" => "Dijon"],
        [ "key" => "grenoble", "name" => "Grenoble"],
        [ "key" => "guadeloupe", "name" => "Guadeloupe"],
        [ "key" => "guyane", "name" => "Guyane"],
        [ "key" => "la-reunion", "name" => "La Réunion"],
        [ "key" => "lille", "name" => "Lille"],
        [ "key" => "limoges", "name" => "Limoges"],
        [ "key" => "lyon", "name" => "Lyon"],
        [ "key" => "martinique", "name" => "Martinique"],
        [ "key" => "mayotte", "name" => "Mayotte"],
        [ "key" => "montpellier", "name" => "Montpellier"],
        [ "key" => "nancy-metz", "name" => "Nancy-Metz"],
        [ "key" => "nantes", "name" => "Nantes"],
        [ "key" => "nice", "name" => "Nice"],
        [ "key" => "normandie", "name" => "Normandie"],
        [ "key" => "nouvelle-caledonie", "name" => "Nouvelle-Calédonie"],
        [ "key" => "orleans-tours", "name" => "Orléans-Tours"],
        [ "key" => "paris", "name" => "Paris"],
        [ "key" => "poitiers", "name" => "Poitiers"],
        [ "key" => "polynesie-francaise", "name" => "Polynésie française"],
        [ "key" => "reims", "name" => "Reims"],
        [ "key" => "rennes", "name" => "Rennes"],
        [ "key" => "saint-pierre-et-miquelon", "name" => "Saint-Pierre-et-Miquelon"],
        [ "key" => "strasbourg", "name" => "Strasbourg"],
        [ "key" => "toulouse", "name" => "Toulouse"],
        [ "key" => "versailles", "name" => "Versailles"],
        [ "key" => "wallis-et-futuna", "name" => "Wallis-et-Futuna"],
        [ "key" => "etranger", "name" => "Étranger"]
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('academies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('key');
            $table->string('name');
        });
        Academy::insert($this->rows_data);

        Schema::table('projects', function (Blueprint $table) {
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
        Schema::dropIfExists('academies');

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('academy_id');
        });
    }
}
