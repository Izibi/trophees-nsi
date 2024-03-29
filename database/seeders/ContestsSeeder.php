<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contest;

class ContestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Contest::create([
            'id' => 1,
            'name' => 'Test contest 1',
            'year' => 2021,
            'message' => 'Test contest 1 message.',
            'status' => 'closed'
        ]);

        $msg = <<<PHP_STR
Test contest 1 message.<br><br>

Users:<br>
nsi-teacher1<br>
nsi-teacher2<br>
nsi-jury1<br>
nsi-jury2<br>
nsi-admin1<br><br>

Users pasword: 123123
PHP_STR;

        Contest::create([
            'id' => 2,
            'name' => 'Test contest 2',
            'year' => 2022,
            'message' => $msg,
            'status' => 'open',
            'active' => 1
        ]);


        Contest::create([
            'id' => 3,
            'name' => 'Test contest 3',
            'year' => 2023,
            'message' => 'Test contest 3 message',
            'status' => 'preparing'
        ]);

    }
}
