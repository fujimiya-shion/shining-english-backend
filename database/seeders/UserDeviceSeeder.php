<?php

namespace Database\Seeders;

use App\Models\UserDevice;
use Illuminate\Database\Seeder;

class UserDeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserDevice::factory()->count(3)->create();
    }
}
