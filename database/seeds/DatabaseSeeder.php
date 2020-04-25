<?php

use App\Models\Account;
use App\Models\AccountStats;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Account::create([
            'uuid' => 'd59dcabb30424b978f7201d1a076637f',
            'username' => '_Cyb3r',
            'fail_count' => 0,
            'skin' => '67820d7834288a1bf579a92058ad9df05b38be69731da0b71f88511a5af908f4',
        ]);

        AccountStats::create([
            'uuid' => 'd59dcabb30424b978f7201d1a076637f',
            'count_request' => 1,
            'count_search' => 1,
            'time_request' => time(),
            'time_search' => time(),
        ]);
    }
}
