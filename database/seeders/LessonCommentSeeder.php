<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\LessonComment;
use Illuminate\Database\Seeder;

class LessonCommentSeeder extends Seeder
{
    public function run(): void
    {
        if (Lesson::query()->doesntExist()) {
            $this->call(LessonSeeder::class);
        }

        Lesson::query()
            ->select(['id'])
            ->chunkById(100, function ($lessons): void {
                foreach ($lessons as $lesson) {
                    $count = fake()->numberBetween(1, 4);
                    for ($index = 1; $index <= $count; $index++) {
                        LessonComment::query()->create([
                            'lesson_id' => $lesson->id,
                            'name' => fake()->name(),
                            'content' => fake()->sentence(14),
                        ]);
                    }
                }
            });
    }
}
