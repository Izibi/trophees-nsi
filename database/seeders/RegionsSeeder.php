<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Region;

class RegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regions = [
            'Auvergne-Rhône-Alpes',
            'Bourgogne-Franche-Comté',
            'Bretagne',
            'Centre-Val de Loire',
            'Corse',
            'Grand Est',
            'Guadeloupe',
            'Guyane',
            'Hauts-de-France',
            'Réunion',
            'Martinique',
            'Mayotte',
            'Normandie',
            'Nouvelle-Aquitaine',
            'Occitanie',
            'Pays de la Loire',
            'Provence-Alpes-Côte d\'Azur',
            'Paris',
            'Versailles',
            'Créteil'
        ];

        $fr = Country::find(1);
        foreach($regions as $region) {
            Region::create([
                'name' => $region,
                'country_id' => $fr->id
            ]);
        }

        Region::create([
            'name' => 'Étranger (AEFE)'
        ]);
    }
}
