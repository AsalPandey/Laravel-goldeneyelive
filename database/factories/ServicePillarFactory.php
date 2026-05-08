<?php

namespace Database\Factories;

use App\Models\ServicePillar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServicePillar>
 */
class ServicePillarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'icon' => 'fa fa-star',
            'slug' => $this->faker->unique()->slug(),
            'summary' => $this->faker->sentence(12),
            'bullets' => [
                $this->faker->sentence(8),
                $this->faker->sentence(8),
            ],
            'cta_label' => 'Learn More',
            'cta_url' => '/contact',
            'is_featured' => false,
            'status' => 'active',
            'sort_order' => $this->faker->numberBetween(1, 50),
        ];
    }
}
