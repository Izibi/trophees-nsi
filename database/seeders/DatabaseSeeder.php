<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CountriesSeeder::class);
        $this->call(RegionsSeeder::class);
        $this->call(GradesSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(SchoolsSeeder::class);
        $this->call(ContestsSeeder::class);
        $this->call(ProjectsSeeder::class);
        $this->call(AcademiesSeeder::class);
    }
}
