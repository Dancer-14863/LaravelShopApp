<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 1, 100),
            'user_id' => $this->faker->numberBetween(1, 10)
        ];
    }

    public function withUserId(int $userId): self
    {
        return $this->state(function (array $attributes) use ($userId) {
            return [
                'user_id' => $userId,
            ];
        });
    }
}