<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use App\Exceptions\BusinessException;
use App\Models\Customer;
use App\Models\Order;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = new OrderService();
    }

    public function test_can_create_order_with_default_pending_status()
    {
        $customer = Customer::factory()->create();
        $data = [
            'customer_id' => $customer->id,
            'order_date' => now(),
            'deadline' => now()->addDays(7),
            'item_type' => 'Dress',
        ];

        $order = $this->orderService->create($data);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals(OrderStatus::Pending, $order->status);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => OrderStatus::Pending->value]);
    }

    public function test_can_create_order_with_specified_status()
    {
        $customer = Customer::factory()->create();
        $data = [
            'customer_id' => $customer->id,
            'order_date' => now(),
            'deadline' => now()->addDays(7),
            'item_type' => 'Pants',
            'status' => OrderStatus::Processing->value,
        ];

        $order = $this->orderService->create($data);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals(OrderStatus::Processing, $order->status);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => OrderStatus::Processing->value]);
    }

    public function test_create_order_throws_exception_if_deadline_is_before_order_date()
    {
        $customer = Customer::factory()->create();
        $data = [
            'customer_id' => $customer->id,
            'order_date' => now()->addDays(7),
            'deadline' => now(),
            'item_type' => 'Shirt',
        ];

        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('Deadline must be after the order date.');

        $this->orderService->create($data);
    }

    public function test_can_update_order()
    {
        $order = Order::factory()->for(Customer::factory())->create(['status' => OrderStatus::Pending]);
        $newData = [
            'item_type' => 'Updated Item',
            'status' => OrderStatus::Ready->value,
        ];

        $result = $this->orderService->update($order, $newData);

        $this->assertTrue($result);
        $order->refresh();
        $this->assertEquals('Updated Item', $order->item_type);
        $this->assertEquals(OrderStatus::Ready, $order->status);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'item_type' => 'Updated Item', 'status' => OrderStatus::Ready->value]);
    }

    public function test_update_order_throws_exception_if_deadline_becomes_before_order_date()
    {
        $order = Order::factory()->for(Customer::factory())->create([
            'order_date' => now(),
            'deadline' => now()->addDays(10),
        ]);
        $newData = [
            'deadline' => now()->subDay(), // Changed to subDay to trigger exception
        ];

        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('Deadline must be after the order date.');

        $this->orderService->update($order, $newData);
    }

    public function test_can_change_order_status()
    {
        $order = Order::factory()->for(Customer::factory())->create(['status' => OrderStatus::Pending]);

        $result = $this->orderService->changeStatus($order, OrderStatus::Processing);

        $this->assertTrue($result);
        $order->refresh();
        $this->assertEquals(OrderStatus::Processing, $order->status);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => OrderStatus::Processing->value]);
    }

    public function test_can_destroy_order()
    {
        $order = Order::factory()->for(Customer::factory())->create();

        $result = $this->orderService->destroy($order);

        $this->assertTrue($result);
        $this->assertSoftDeleted($order);
    }

    public function test_validate_deadline_success()
    {
        $orderDate = Carbon::now()->toMutable();
        $deadline = Carbon::now()->addDay()->toMutable();

        $this->orderService->validateDeadline($orderDate, $deadline);
        $this->assertTrue(true); // No exception means success
    }

    public function test_validate_deadline_throws_exception_on_invalid_dates()
    {
        $orderDate = Carbon::now()->addDay()->toMutable();
        $deadline = Carbon::now()->toMutable();

        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('Deadline must be after the order date.');

        $this->orderService->validateDeadline($orderDate, $deadline);
    }

    public function test_get_paginated_orders_returns_paginated_data()
    {
        Order::factory()->count(15)->for(Customer::factory())->create();
        $request = new Request();

        $orders = $this->orderService->getPaginatedOrders($request);

        $this->assertInstanceOf(LengthAwarePaginator::class, $orders);
        $this->assertCount(10, $orders->items()); // Default pagination is 10
    }

    public function test_get_paginated_orders_can_be_searched_by_customer_name()
    {
        $customer1 = Customer::factory()->create(['name' => 'Alice Customer']);
        $customer2 = Customer::factory()->create(['name' => 'Bob Customer']);
        Order::factory()->for($customer1)->create(['item_type' => 'Shirt']);
        Order::factory()->for($customer2)->create(['item_type' => 'Pants']);

        $request = new Request(['search' => 'Alice']);
        $orders = $this->orderService->getPaginatedOrders($request);

        $this->assertCount(1, $orders->items());
        $this->assertEquals('Alice Customer', $orders->first()->customer->name);
    }

    public function test_get_paginated_orders_can_be_filtered_by_status()
    {
        Order::factory()->for(Customer::factory())->count(2)->create(['status' => OrderStatus::Pending]);
        Order::factory()->for(Customer::factory())->count(3)->create(['status' => OrderStatus::Ready]);

        $request = new Request(['status' => OrderStatus::Ready->value]);
        $orders = $this->orderService->getPaginatedOrders($request);

        $this->assertCount(3, $orders->items());
        $orders->each(fn($order) => $this->assertEquals(OrderStatus::Ready, $order->status));
    }

    public function test_get_paginated_orders_can_be_sorted()
    {
        $customer = Customer::factory()->create();
        Order::factory()->for($customer)->create(['deadline' => now()->addDays(5)]);
        Order::factory()->for($customer)->create(['deadline' => now()->addDays(10)]);
        Order::factory()->for($customer)->create(['deadline' => now()->addDays(2)]);

        $requestAsc = new Request(['sort' => 'deadline', 'direction' => 'asc']);
        $ordersAsc = $this->orderService->getPaginatedOrders($requestAsc);
        $this->assertEquals(now()->addDays(2)->format('Y-m-d'), $ordersAsc->first()->deadline->format('Y-m-d'));

        $requestDesc = new Request(['sort' => 'deadline', 'direction' => 'desc']);
        $ordersDesc = $this->orderService->getPaginatedOrders($requestDesc);
        $this->assertEquals(now()->addDays(10)->format('Y-m-d'), $ordersDesc->first()->deadline->format('Y-m-d'));
    }

    public function test_get_order_summary_returns_correct_counts_and_structure()
    {
        Order::factory()->for(Customer::factory())->count(2)->create(['status' => OrderStatus::Pending]);
        Order::factory()->for(Customer::factory())->count(1)->create(['status' => OrderStatus::Processing]);
        Order::factory()->for(Customer::factory())->count(4)->create(['status' => OrderStatus::Ready]);
        Order::factory()->for(Customer::factory())->create(['status' => OrderStatus::Pending, 'deleted_at' => now()]); // Soft deleted

        $summary = $this->orderService->getOrderSummary();

        $this->assertIsArray($summary);
        $this->assertArrayHasKey(OrderStatus::Pending->value, $summary);
        $this->assertArrayHasKey(OrderStatus::Processing->value, $summary);
        $this->assertArrayHasKey(OrderStatus::Ready->value, $summary);

        $this->assertEquals(2, $summary[OrderStatus::Pending->value]['count']);
        $this->assertEquals('Pending', $summary[OrderStatus::Pending->value]['label']);
        $this->assertEquals('gray', $summary[OrderStatus::Pending->value]['color']);

        $this->assertEquals(1, $summary[OrderStatus::Processing->value]['count']);
        $this->assertEquals('Processing', $summary[OrderStatus::Processing->value]['label']);
        $this->assertEquals('yellow', $summary[OrderStatus::Processing->value]['color']);

        $this->assertEquals(4, $summary[OrderStatus::Ready->value]['count']);
        $this->assertEquals('Ready', $summary[OrderStatus::Ready->value]['label']);
        $this->assertEquals('green', $summary[OrderStatus::Ready->value]['color']);
    }
}
