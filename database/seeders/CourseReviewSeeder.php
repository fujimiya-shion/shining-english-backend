<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseReview;
use Illuminate\Database\Seeder;

class CourseReviewSeeder extends Seeder
{
    public function run(): void
    {
        if (Course::query()->doesntExist()) {
            $this->call(CourseSeeder::class);
        }

        Course::query()
            ->select(['id'])
            ->chunkById(50, function ($courses): void {
                foreach ($courses as $course) {
                    $count = fake()->numberBetween(2, 6);
                    for ($index = 1; $index <= $count; $index++) {
                        CourseReview::query()->create([
                            'course_id' => $course->id,
                            'name' => fake()->name(),
                            'rating' => fake()->numberBetween(3, 5),
                            'content' => fake()->sentence(18),
                        ]);
                    }
                }
            });
    }
}
