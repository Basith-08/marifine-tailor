<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Measurement;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_be_created()
    {
        $customer = Customer::factory()->create([
            'name' => 'John Doe',
            'phone' => '1234567890',
            'address' => '123 Main St, Anytown, CA 90210',
        ]);

        $this->assertNotNull($customer);
        $this->assertDatabaseHas('customers', [
            'name' => 'John Doe',
        ]);
    }

    public function test_customer_has_many_orders()
    {
        $customer = Customer::factory()->create();
        Order::factory()->count(3)->create(['customer_id' => $customer->id]);

        $this->assertCount(3, $customer->orders);
        $this->assertInstanceOf(Order::class, $customer->orders->first());
    }

    public function test_customer_has_many_measurements()
    {
        $customer = Customer::factory()->create();
        Measurement::factory()->count(2)->create(['customer_id' => $customer->id]);

        $this->assertCount(2, $customer->measurements);
        $this->assertInstanceOf(Measurement::class, $customer->measurements->first());
    }

    public function test_customer_can_be_soft_deleted()
    {
        $customer = Customer::factory()->create();
        $customer->delete();

        $this->assertSoftDeleted($customer);
        $this->assertNotNull(Customer::withTrashed()->find($customer->id));
    }
}
