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
        $countries = Country::get();
        Region::create([
            'country_id' => $countries[0]->id,
            'name' => 'Bourgogne'
        ]);
        Region::create([
            'country_id' => $countries[0]->id,
            'name' => 'Aix-Marseille'
        ]);

        Region::create([
            'country_id' => $countries[1]->id,
            'name' => 'Test region 1'
        ]);
        Region::create([
            'country_id' => $countries[1]->id,
            'name' => 'Test region 2'
        ]);
        Region::create([
            'country_id' => $countries[1]->id,
            'name' => 'Test region 3'
        ]);
    }
}
