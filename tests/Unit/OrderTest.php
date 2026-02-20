<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_can_be_created()
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'order_date' => now(),
            'deadline' => now()->addDays(7),
            'item_type' => 'Shirt',
            'status' => OrderStatus::Pending,
        ]);

        $this->assertNotNull($order);
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'item_type' => 'Shirt',
            'status' => OrderStatus::Pending->value,
        ]);
        $this->assertInstanceOf(\Carbon\CarbonImmutable::class, $order->order_date);
        $this->assertEquals(OrderStatus::Pending, $order->status);
    }

    public function test_order_belongs_to_customer()
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id]);

        $this->assertInstanceOf(Customer::class, $order->customer);
        $this->assertEquals($customer->id, $order->customer->id);
    }

    public function test_order_can_be_soft_deleted()
    {
        $order = Order::factory()->create();
        $order->delete();

        $this->assertSoftDeleted($order);
        $this->assertNotNull(Order::withTrashed()->find($order->id));
    }

    public function test_order_status_casting()
    {
        $order = Order::factory()->create(['status' => OrderStatus::Ready]);
        $this->assertEquals(OrderStatus::Ready, $order->status);

        $order->status = OrderStatus::Processing;
        $order->save();
        $this->assertEquals(OrderStatus::Processing, $order->status);
    }

    public function test_order_can_be_updated()
    {
        $order = Order::factory()->create([
            'item_type' => 'Dress',
            'status' => OrderStatus::Pending,
        ]);
        $newType = 'Pants';
        $newStatus = OrderStatus::Ready;

        $order->update([
            'item_type' => $newType,
            'status' => $newStatus,
        ]);

        $this->assertEquals($newType, $order->item_type);
        $this->assertEquals($newStatus, $order->status);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'item_type' => $newType,
            'status' => $newStatus->value,
        ]);
    }
}
