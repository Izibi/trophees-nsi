<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\Region;
use App\Models\Country;

class SchoolsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // france
        $region = Region::find(1);
        School::create([
            'region_id' => $region->id,
            'country_id' => $region->country_id,
            'name' => 'Test France school 1',
            'address' => 'address 1',
            'city' => 'city 1',
            'zip' => '111',
            'uai' => '111',
            'hidden' => false
        ]);
        $region = Region::find(2);
        School::create([
            'region_id' => $region->id,
            'country_id' => $region->country_id,
            'name' => 'Test France school 2',
            'address' => 'address 2',
            'city' => 'city 2',
            'zip' => '222',
            'uai' => '222',
            'hidden' => false
        ]);
        $region = Region::find(2);
        School::create([
            'region_id' => $region->id,
            'country_id' => $region->country_id,
            'name' => 'Test France school 3 (hidden)',
            'address' => 'address 3',
            'city' => 'city 3',
            'zip' => '333',
            'uai' => '333',
            'hidden' => true
        ]);

        // foreign
        $region = Region::whereNull('country_id')->first();
        $country = Country::find(2);
        School::create([
            'region_id' => $region->id,
            'country_id' => $country->id,
            'name' => 'Test foreign school 4',
            'address' => 'address 4',
            'city' => 'city 4',
            'zip' => '444',
            'uai' => '444',
            'hidden' => false
        ]);
        $country = Country::find(3);
        School::create([
            'region_id' => $region->id,
            'country_id' => $country->id,
            'name' => 'Test foreign school 5',
            'address' => 'address 5',
            'city' => 'city 5',
            'zip' => '555',
            'uai' => '555',
            'hidden' => false
        ]);
    }
}
