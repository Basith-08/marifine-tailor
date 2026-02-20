<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $orderDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $deadline = $this->faker->dateTimeBetween($orderDate, (clone $orderDate)->modify('+2 months'));

        return [
            'customer_id' => Customer::factory(),
            'order_date' => $orderDate,
            'deadline' => $deadline,
            'item_type' => $this->faker->randomElement(['Shirt', 'Pants', 'Suit', 'Dress']),
            'status' => $this->faker->randomElement(OrderStatus::cases()),
        ];
    }
}
