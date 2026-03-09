<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Level;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        if (Category::query()->doesntExist()) {
            $this->call(CategorySeeder::class);
        }
        if (Level::query()->doesntExist()) {
            $this->call(LevelSeeder::class);
        }

        $categoryIds = Category::query()->pluck('id')->all();
        $levelIds = Level::query()->pluck('id')->all();

        $courses = Course::factory()
            ->count(100)
            ->state(fn (): array => [
                'category_id' => fake()->randomElement($categoryIds),
            ])
            ->create();

        foreach ($courses as $course) {
            $selectedLevelIds = fake()->randomElements($levelIds, fake()->numberBetween(1, min(2, count($levelIds))));
            $course->levels()->sync($selectedLevelIds);
        }
    }
}
