<?php

namespace Database\Seeders;

use App\Models\Measurement;
use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::factory()
            ->count(50)
            ->has(Measurement::factory()->count(1))
            ->has(Order::factory()->count(5))
            ->create();
    }
}
