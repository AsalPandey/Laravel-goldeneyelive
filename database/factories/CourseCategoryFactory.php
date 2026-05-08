<?php

namespace Database\Factories;

use App\Models\CourseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CourseCategory>
 */
class CourseCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'IT and Software',
            'Language and Test Prep',
            'Career Skills',
            'Academic Support',
        ]).' '.fake()->numberBetween(1, 99);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'image' => 'site/img/cat-1.jpg',
            'status' => 'active',
            'order_priority' => fake()->numberBetween(1, 20),
            'description' => fake()->paragraph(),
        ];
    }
}
