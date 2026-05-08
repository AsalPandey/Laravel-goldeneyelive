<?php

namespace Database\Factories;

use App\Models\NewsLetter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NewsLetter>
 */
class NewsLetterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}
