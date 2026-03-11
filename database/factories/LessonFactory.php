<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Models\Lesson>
     */
    protected $model = Lesson::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'name' => $title,
            'slug' => null,
            'course_id' => Course::factory(),
            'video_url' => fake()->url(),
            'star_reward_video' => fake()->numberBetween(0, 5),
            'star_reward_quiz' => fake()->numberBetween(0, 5),
            'has_quiz' => fake()->boolean(35),
        ];
    }
}
