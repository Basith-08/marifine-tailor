<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Measurement>
 */
class MeasurementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shoulder' => $this->faker->randomFloat(2, 30, 50),
            'chest' => $this->faker->randomFloat(2, 70, 120),
            'waist' => $this->faker->randomFloat(2, 60, 110),
            'sleeve' => $this->faker->randomFloat(2, 50, 70),
            'other_measurements' => [
                'hip' => $this->faker->randomFloat(2, 80, 130),
                'inseam' => $this->faker->randomFloat(2, 70, 90),
            ],
        ];
    }
}
