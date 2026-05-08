<?php

namespace Database\Factories;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_name' => $this->faker->name(),
            'course_name' => $this->faker->word(),
            'content' => $this->faker->paragraph(),
            'rating' => $this->faker->numberBetween(4, 5),
            'status' => 'active',
        ];
    }
}
