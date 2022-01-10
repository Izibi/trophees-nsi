<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'id' => 100002089,
            'name' => 'nsi teacher',
            'role' => 'teacher'
        ]);
        User::create([
            'id' => 100002091,
            'name' => 'nsi jury',
            'role' => 'jury',
            'region_id' => 3
        ]);
        User::create([
            'id' => 100002092,
            'name' => 'nsi admin',
            'role' => 'admin'
        ]);
        
    }
}
