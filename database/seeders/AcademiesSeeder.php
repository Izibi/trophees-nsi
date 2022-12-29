<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academy;

class AcademiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows_data = [
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
        Academy::insert($rows_data);
    }
}
