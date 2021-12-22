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
        $fr = Country::where('name', 'France')->first();
        Region::create([
            'country_id' => $fr->id,
            'name' => 'Bourgogne'
        ]);
        Region::create([
            'country_id' => $fr->id,
            'name' => 'Aix-Marseille'
        ]);        
    }
}
