<?php

namespace Database\Factories;

use App\Models\FAQ;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FAQ>
 */
class FAQFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question' => $this->faker->sentence(),
            'answer' => $this->faker->paragraph(),
            'status' => 'active',
            'order_priority' => 0,
        ];
    }
}
