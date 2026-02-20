<?php

namespace Tests\Unit;

use App\Exceptions\BusinessException;
use App\Models\Customer;
use App\Models\Order;
use App\Services\CustomerService;
use App\Enums\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class CustomerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerService $customerService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customerService = new CustomerService();
    }

    public function test_can_create_customer()
    {
        $data = [
            'name' => 'Test Customer',
            'phone' => '1234567890',
            'address' => '123 Test St',
        ];

        $customer = $this->customerService->create($data);

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertDatabaseHas('customers', $data);
    }

    public function test_can_update_customer()
    {
        $customer = Customer::factory()->create();
        $newData = [
            'name' => 'Updated Customer Name',
            'phone' => '0987654321',
        ];

        $result = $this->customerService->update($customer, $newData);

        $this->assertTrue($result);
        $this->assertDatabaseHas('customers', array_merge(['id' => $customer->id], $newData));
    }

    public function test_can_destroy_customer()
    {
        $customer = Customer::factory()->create();

        $result = $this->customerService->destroy($customer);

        $this->assertTrue($result);
        $this->assertSoftDeleted($customer);
    }

    public function test_cannot_destroy_customer_with_pending_orders()
    {
        $customer = Customer::factory()->create();
        Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::Pending,
        ]);

        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('Customer cannot be deleted because they have pending or processing orders.');

        $this->customerService->destroy($customer);
    }

    public function test_cannot_destroy_customer_with_processing_orders()
    {
        $customer = Customer::factory()->create();
        Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::Processing,
        ]);

        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('Customer cannot be deleted because they have pending or processing orders.');

        $this->customerService->destroy($customer);
    }

    public function test_can_destroy_customer_with_ready_orders()
    {
        $customer = Customer::factory()->create();
        Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::Ready,
        ]);

        $result = $this->customerService->destroy($customer);

        $this->assertTrue($result);
        $this->assertSoftDeleted($customer);
    }

    public function test_get_all_customers_returns_paginated_data()
    {
        Customer::factory()->count(15)->create();

        $customers = $this->customerService->getAllCustomers();

        $this->assertInstanceOf(LengthAwarePaginator::class, $customers);
        $this->assertCount(10, $customers->items()); // Default pagination is 10
    }

    public function test_get_all_customers_can_be_searched_by_name()
    {
        Customer::factory()->create(['name' => 'John Doe']);
        Customer::factory()->create(['name' => 'Jane Smith']);

        $customers = $this->customerService->getAllCustomers('John');

        $this->assertCount(1, $customers->items());
        $this->assertEquals('John Doe', $customers->first()->name);
    }

    public function test_get_all_customers_can_be_searched_by_phone()
    {
        Customer::factory()->create(['name' => 'John Doe', 'phone' => '1112223333']);
        Customer::factory()->create(['name' => 'Jane Smith', 'phone' => '4445556666']);

        $customers = $this->customerService->getAllCustomers('111');

        $this->assertCount(1, $customers->items());
        $this->assertEquals('John Doe', $customers->first()->name);
    }

    public function test_get_all_customers_can_be_sorted()
    {
        Customer::factory()->create(['name' => 'Alice', 'created_at' => Carbon::yesterday()]);
        Customer::factory()->create(['name' => 'Bob', 'created_at' => Carbon::now()]);

        // Sort by name ascending
        $customersByNameAsc = $this->customerService->getAllCustomers(null, 'name', 'asc');
        $this->assertEquals('Alice', $customersByNameAsc->first()->name);

        // Sort by name descending
        $customersByNameDesc = $this->customerService->getAllCustomers(null, 'name', 'desc');
        $this->assertEquals('Bob', $customersByNameDesc->first()->name);

        // Sort by created_at descending (default)
        $customersByCreatedAtDesc = $this->customerService->getAllCustomers(null, 'created_at', 'desc');
        $this->assertEquals('Bob', $customersByCreatedAtDesc->first()->name);
    }

    public function test_get_customer_growth_data()
    {
        // Create customers for the last 12 months to ensure data exists for the last year
        // and some older data that should be excluded.
        for ($i = 0; $i < 12; $i++) {
            Customer::factory()->create([
                'created_at' => now()->subMonths($i)->subDays(15), // Mid-month
            ]);
            Customer::factory()->create([
                'created_at' => now()->subMonths($i)->subDays(5),  // Later mid-month
            ]);
        }

        // Create some very old customers to ensure the `where('created_at', '>=', now()->subYear())` clause works
        Customer::factory()->count(5)->create(['created_at' => now()->subYears(2)]);


        $growthData = $this->customerService->getCustomerGrowthData();

        // Ensure the count of months is correct (should be 12 months for the last year, including current month)
        $this->assertCount(12, $growthData);

        // Verify the structure of the data (e.g., 'month' and 'count' keys)
        $this->assertArrayHasKey('month', $growthData->first());
        $this->assertArrayHasKey('count', $growthData->first());

        // For each month, we created 2 customers, so the count should be 2 for each month in the last year
        foreach ($growthData as $monthData) {
            $this->assertEquals(2, $monthData['count']);
        }

        // Test that older data is excluded
        $firstMonthInResult = Carbon::parse($growthData->first()['month'])->startOfMonth();
        $expectedFirstMonth = now()->subMonths(11)->startOfMonth(); // 12 months ago starting from current month
        $this->assertEquals($expectedFirstMonth->format('Y-m'), $firstMonthInResult->format('Y-m'));
    }
}
