<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\Region;

class SchoolsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $region = Region::find(1);
        School::create([
            'region_id' => $region->id,
            'country_id' => $region->country_id,
            'name' => 'Test school 1',
            'address' => 'address 1',
            'city' => 'city 1',
            'zip' => '111',
            'uai' => '111',
            'hidden' => false
        ]);
        School::create([
            'region_id' => $region->id,
            'country_id' => $region->country_id,
            'name' => 'Test school 2',
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
            'name' => 'Test school 3',
            'address' => 'address 3',
            'city' => 'city 3',
            'zip' => '333',
            'uai' => '333',
            'hidden' => false
        ]);       
    }
}
