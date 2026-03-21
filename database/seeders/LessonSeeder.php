<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    private const GROUPS = [
        'Fundamentals of English',
        'Grammar in Depth',
        'Vocabulary Expansion',
    ];

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
                    $group = self::GROUPS[array_rand(self::GROUPS)];

                    for ($index = 1; $index <= $totalLessons; $index++) {
                        if ($index === 1 || $index % 5 === 0) {
                            $group = self::GROUPS[array_rand(self::GROUPS)];
                        }

                        Lesson::query()->create([
                            'name' => sprintf('Lesson %d - %s', $index, $course->name),
                            'slug' => null,
                            'course_id' => $course->id,
                            'group_name' => $group,
                            'video_url' => sprintf(
                                'https://cdn.shining-english.local/courses/%d/lesson-%d.mp4',
                                $course->id,
                                $index
                            ),
                            'description' => fake()->paragraph(),
                            'duration_minutes' => fake()->numberBetween(8, 40),
                            'star_reward_video' => fake()->numberBetween(1, 3),
                            'star_reward_quiz' => fake()->numberBetween(0, 3),
                            'has_quiz' => $index % 3 === 0,
                        ]);
                    }
                }
            });
    }
}
