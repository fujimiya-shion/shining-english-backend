<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['name' => 'Beginner', 'slug' => 'beginner'],
            ['name' => 'Intermediate', 'slug' => 'intermediate'],
            ['name' => 'Advanced', 'slug' => 'advanced'],
        ];

        foreach ($levels as $level) {
            Level::query()->updateOrCreate(
                ['slug' => $level['slug']],
                ['name' => $level['name']],
            );
        }
    }
}
