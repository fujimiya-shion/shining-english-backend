<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        if (Course::query()->doesntExist()) {
            $this->call(CourseSeeder::class);
        }

        Course::query()
            ->select(['id', 'name'])
            ->chunkById(50, function ($courses): void {
                foreach ($courses as $course) {
                    $totalLessons = fake()->numberBetween(8, 16);

                    for ($index = 1; $index <= $totalLessons; $index++) {
                        Lesson::query()->create([
                            'name' => sprintf('Lesson %d - %s', $index, $course->name),
                            'slug' => null,
                            'course_id' => $course->id,
                            'video_url' => sprintf(
                                'https://cdn.shining-english.local/courses/%d/lesson-%d.mp4',
                                $course->id,
                                $index
                            ),
                            'star_reward_video' => fake()->numberBetween(1, 3),
                            'star_reward_quiz' => fake()->numberBetween(0, 3),
                            'has_quiz' => $index % 3 === 0,
                        ]);
                    }
                }
            });
    }
}
