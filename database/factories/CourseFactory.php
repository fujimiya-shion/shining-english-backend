<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Models\Course>
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->sentence(3),
            'slug' => null,
            'price' => fake()->numberBetween(50, 500),
            'status' => true,
            'thumbnail' => null,
            'category_id' => Category::factory(),
            'description' => fake()->sentence(),
            'rating' => fake()->randomFloat(1, 1, 5),
            'learned' => fake()->numberBetween(0, 100),
        ];
    }
}
