<?php

namespace Database\Factories;

use App\Enums\StarTransactionType;
use App\Models\StarTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StarTransaction>
 */
class StarTransactionFactory extends Factory
{
    protected $model = StarTransaction::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'amount' => fake()->numberBetween(1, 200),
            'type' => fake()->randomElement(StarTransactionType::cases()),
            'description' => fake()->sentence(),
        ];
    }
}
