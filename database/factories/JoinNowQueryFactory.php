<?php

namespace Database\Factories;

use App\Models\JoinNowQuery;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JoinNowQuery>
 */
class JoinNowQueryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'firstName' => $this->faker->firstName(),
            'lastName' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'course' => $this->faker->word(),
            'queries' => $this->faker->paragraph(),
            'status' => 'new',
        ];
    }
}
