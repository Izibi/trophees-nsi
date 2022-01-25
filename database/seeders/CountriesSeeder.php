<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = require(database_path().'/initial_data/countries.php');

        $id = 1;
        $country = Country::firstOrNew([
            'id' => $id
        ]);
        $country->code = 'FR';
        $country->name = $countries['FR'];
        $country->save();

        foreach($countries as $code => $name) {
            if($code == 'FR') {
                continue;
            }
            $id++;
            $country = Country::firstOrNew([
                'id' => $id
            ]);
            $country->code = $code;
            $country->name = $name;
            $country->save();
        }
    }


}
