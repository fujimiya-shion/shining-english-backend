<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        if (Category::query()->doesntExist()) {
            $this->call(CategorySeeder::class);
        }

        $categoryIds = Category::query()->pluck('id')->all();

        Course::factory()
            ->count(100)
            ->state(fn (): array => [
                'category_id' => fake()->randomElement($categoryIds),
            ])
            ->create();
    }
}
