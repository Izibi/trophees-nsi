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
        Country::create([
            'id' => 1,
            'name' => 'France'
        ]);
        Country::create([
            'id' => 2,
            'name' => 'Test country 2'
        ]);
        Country::create([
            'id' => 3,
            'name' => 'Test country 3'
        ]);
    }
}
