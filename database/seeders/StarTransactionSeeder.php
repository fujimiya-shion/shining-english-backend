<?php

namespace Database\Seeders;

use App\Models\StarTransaction;
use Illuminate\Database\Seeder;

class StarTransactionSeeder extends Seeder
{
    public function run(): void
    {
        StarTransaction::factory()
            ->count(20)
            ->create();
    }
}
