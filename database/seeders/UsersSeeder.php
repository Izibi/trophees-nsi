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
            'id' => 100000002,
            'name' => 'nsi teacher1',
            'login' => 'nsi-teacher1',
            'role' => 'teacher',
            'region_id' => $region->id,
            'country_id' => $region->country_id
        ]);
        User::create([
            'id' => 100000003,
            'name' => 'nsi teacher2',
            'login' => 'nsi-teacher2',
            'role' => 'teacher',
            'region_id' => $region->id,
            'country_id' => $region->country_id
        ]);


        User::create([
            'id' => 100000004,
            'name' => 'nsi jury1',
            'login' => 'nsi-jury1',
            'role' => 'jury',
            'region_id' => $region->id,
            'country_id' => $region->country_id
        ]);
        User::create([
            'id' => 100000005,
            'name' => 'nsi jury1',
            'login' => 'nsi-jury1',
            'role' => 'jury',
            'region_id' => $region->id,
            'country_id' => $region->country_id
        ]);


        User::create([
            'id' => 100000006,
            'name' => 'nsi admin1',
            'login' => 'nsi-admin1',
            'role' => 'admin'
        ]);
    }
}
