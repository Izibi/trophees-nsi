<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Region;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $region = Region::find(1);
        User::create([
            'id' => 100002089,
            'name' => 'nsi teacher',
            'role' => 'teacher'
        ]);
        User::create([
            'id' => 100002091,
            'name' => 'nsi jury',
            'role' => 'jury',
            'region_id' => $region->id,
            'country_id' => $region->country_id
        ]);
        User::create([
            'id' => 100002092,
            'name' => 'nsi admin',
            'role' => 'admin'
        ]);

    }
}
