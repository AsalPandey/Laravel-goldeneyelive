<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->randomElement([
            'Web Development', 'Graphic Design', 'IELTS Preparation',
            'Korean Language', 'Python Programming', 'Digital Marketing',
            'Data Science', 'Mobile App Development',
        ]).' '.fake()->numberBetween(100, 400);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => '<p>'.fake()->paragraphs(2, true).'</p>',
            'course_outline' => '<ul><li>'.implode('</li><li>', fake()->sentences(5)).'</li></ul>',
            'rating_star' => fake()->randomFloat(1, 3.5, 5.0),
            'rating_count' => fake()->numberBetween(10, 200),
            'capacity' => fake()->randomElement(['20', '25', '30', '40']),
            'photo' => 'site/img/cat-1.jpg',
            'price' => 'Rs. '.fake()->randomElement(['5,000', '10,000', '15,000', '20,000']),
            'duration' => fake()->randomElement(['2 Months', '3 Months', '4 Months', '6 Months']),
            'instructor' => fake()->name(),
            'category' => fake()->randomElement(['Computer Classes', 'Language Classes']),
            'category_slug' => fake()->randomElement(['computer-classes', 'language-classes']),
            'status' => 'active',
            'badge_text' => 'Job Oriented',
            'display_order' => 100,
        ];
    }

    /**
     * Mark the course as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Mark the course as featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
