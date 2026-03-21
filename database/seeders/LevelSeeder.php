<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            'Beginner',
            'Elementary',
            'Intermediate',
            'Upper Intermediate',
            'Advanced',
        ];

        foreach ($levels as $name) {
            Level::query()->firstOrCreate(['name' => $name], ['slug' => null]);
        }
    }
}
