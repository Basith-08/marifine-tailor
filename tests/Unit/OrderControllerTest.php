<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use App\Exceptions\BusinessException;
use App\Http\Controllers\OrderController;
use App\Http\Requests\Order\ChangeOrderStatusRequest;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Services\CustomerService; // Import CustomerService
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;
use Mockery;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    protected Mockery\MockInterface $orderServiceMock;
    protected OrderController $orderController;
    protected Mockery\MockInterface $customerServiceMock; // Add CustomerService mock

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderServiceMock = Mockery::mock(OrderService::class);
        $this->customerServiceMock = Mockery::mock(CustomerService::class); // Instantiate CustomerService mock
        $this->orderController = new OrderController($this->orderServiceMock, $this->customerServiceMock); // Inject CustomerService mock

        // Alias mock for Customer model removed as it's now handled via CustomerService mock
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_index_renders_order_index_with_orders_filters_statuses_customers_and_summary()
    {
        $mockPaginator = Mockery::mock(LengthAwarePaginator::class);
        $mockPaginator->shouldReceive('toArray')->andReturn(['data' => [Order::factory()->make()]]);
        $mockPaginator->shouldReceive('withQueryString')->andReturnSelf();

        $mockOrderSummary = ['pending' => ['count' => 5, 'label' => 'Pending', 'color' => 'gray']];
        
        $mockCustomers = collect([
            (object)['id' => 1, 'name' => 'Test Customer 1'],
            (object)['id' => 2, 'name' => 'Test Customer 2'],
        ]);
        
        // Mock CustomerService::getAllCustomersForSelection()
        $this->customerServiceMock->shouldReceive('getAllCustomersForSelection')
            ->once()
            ->andReturn($mockCustomers);

        $this->orderServiceMock->shouldReceive('getPaginatedOrders')
            ->once()
            ->andReturn($mockPaginator);

        $this->orderServiceMock->shouldReceive('getOrderSummary')
            ->once()
            ->andReturn($mockOrderSummary);

        Inertia::shouldReceive('render')
            ->once()
            ->with('Order/OrderIndex', Mockery::on(function ($args) use ($mockPaginator, $mockOrderSummary, $mockCustomers) {
                $this->assertEquals($mockPaginator, $args['orders']);
                $this->assertEquals([], $args['filters']); // Empty request
                $this->assertInstanceOf(\Illuminate\Support\Collection::class, $args['orderStatuses']);
                $this->assertArrayHasKey('name', $args['orderStatuses'][0]);
                $this->assertArrayHasKey('value', $args['orderStatuses'][0]);
                $this->assertArrayHasKey('color', $args['orderStatuses'][0]);
                $this->assertEquals($mockCustomers, $args['customers']);
                $this->assertEquals($mockOrderSummary, $args['orderSummary']);
                return true;
            }))
            ->andReturn(Mockery::mock(Response::class));

        $request = Request::create('/orders', 'GET');
        $response = $this->orderController->index($request);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_create_renders_create_view_with_customers_and_statuses()
    {
        $mockCustomers = collect([
            (object)['id' => 1, 'name' => 'Test Customer 1'],
            (object)['id' => 2, 'name' => 'Test Customer 2'],
            (object)['id' => 3, 'name' => 'Test Customer 3'],
        ]);

        // Mock CustomerService::getAllCustomersForSelection()
        $this->customerServiceMock->shouldReceive('getAllCustomersForSelection')
            ->once()
            ->andReturn($mockCustomers);

        Inertia::shouldReceive('render')
            ->once()
            ->with('Order/OrderCreate', Mockery::on(function ($args) use ($mockCustomers) {
                $this->assertEquals($mockCustomers, $args['customers']);
                $this->assertInstanceOf(\Illuminate\Support\Collection::class, $args['orderStatuses']);
                $this->assertArrayHasKey('name', $args['orderStatuses'][0]);
                $this->assertArrayHasKey('value', $args['orderStatuses'][0]);
                return true;
            }))
            ->andReturn(Mockery::mock(Response::class));

        $response = $this->orderController->create();

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_store_creates_order_and_redirects_with_success_message()
    {
        $validatedData = [
            'customer_id' => 1, 'order_date' => now(), 'deadline' => now()->addDays(7), 'item_type' => 'Shirt',
        ];
        $storeRequest = Mockery::mock(StoreOrderRequest::class);
        $storeRequest->shouldReceive('validated')->once()->andReturn($validatedData);

        $this->orderServiceMock->shouldReceive('create')
            ->once()
            ->with($validatedData)
            ->andReturn(Order::factory()->make());

        $response = $this->orderController->store($storeRequest);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('orders.index'), $response->getTargetUrl());
        $this->assertEquals('Order created successfully.', session('success'));
    }

    public function test_store_returns_back_with_error_message_on_business_exception()
    {
        $validatedData = [
            'customer_id' => 1, 'order_date' => now(), 'deadline' => now()->subDays(7), 'item_type' => 'Shirt',
        ];
        $storeRequest = Mockery::mock(StoreOrderRequest::class);
        $storeRequest->shouldReceive('validated')->once()->andReturn($validatedData);
        $errorMessage = 'Deadline must be after the order date.';

        $this->orderServiceMock->shouldReceive('create')
            ->once()
            ->with($validatedData)
            ->andThrow(new BusinessException($errorMessage));

        $response = $this->orderController->store($storeRequest);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue($response->isRedirect());
        $this->assertEquals($errorMessage, session('error'));
    }

    public function test_update_updates_order_and_redirects_with_success_message()
    {
        $order = Order::factory()->make(['id' => 1]);
        $order = Mockery::mock($order)->makePartial();
        $validatedData = [
            'item_type' => 'Updated Item Type', 'status' => OrderStatus::Ready->value,
        ];
        $updateRequest = Mockery::mock(UpdateOrderRequest::class);
        $updateRequest->shouldReceive('validated')->once()->andReturn($validatedData);

        $this->orderServiceMock->shouldReceive('update')
            ->once()
            ->with($order, $validatedData)
            ->andReturn(true);

        $response = $this->orderController->update($updateRequest, $order);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('orders.index'), $response->getTargetUrl());
        $this->assertEquals('Order updated successfully.', session('success'));
    }

    public function test_update_returns_back_with_error_message_on_business_exception()
    {
        $order = Order::factory()->make(['id' => 1]);
        $order = Mockery::mock($order)->makePartial();
        $validatedData = [
            'item_type' => 'Updated Item Type', 'status' => OrderStatus::Ready->value,
        ];
        $updateRequest = Mockery::mock(UpdateOrderRequest::class);
        $updateRequest->shouldReceive('validated')->once()->andReturn($validatedData);
        $errorMessage = 'Deadline must be after the order date.';

        $this->orderServiceMock->shouldReceive('update')
            ->once()
            ->with($order, $validatedData)
            ->andThrow(new BusinessException($errorMessage));

        $response = $this->orderController->update($updateRequest, $order);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue($response->isRedirect());
        $this->assertEquals($errorMessage, session('error'));
    }

    public function test_destroy_deletes_order_and_redirects_with_success_message()
    {
        $order = Order::factory()->make(['id' => 1]);
        $order = Mockery::mock($order)->makePartial();

        $this->orderServiceMock->shouldReceive('destroy')
            ->once()
            ->with($order)
            ->andReturn(true);

        $response = $this->orderController->destroy($order);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('orders.index'), $response->getTargetUrl());
        $this->assertEquals('Order deleted successfully.', session('success'));
    }

    public function test_destroy_returns_back_with_error_message_on_business_exception()
    {
        $order = Order::factory()->make(['id' => 1]);
        $order = Mockery::mock($order)->makePartial();
        $errorMessage = 'Order cannot be deleted due to business rules.';

        $this->orderServiceMock->shouldReceive('destroy')
            ->once()
            ->with($order)
            ->andThrow(new BusinessException($errorMessage));

        $response = $this->orderController->destroy($order);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue($response->isRedirect());
        $this->assertEquals($errorMessage, session('error'));
    }

    public function test_change_status_updates_order_status_and_redirects_back_with_success_message()
    {
        $order = Order::factory()->make(['id' => 1, 'status' => OrderStatus::Pending]);
        $order = Mockery::mock($order)->makePartial();

        // Fix: Mock validated('status') to return string directly
        $changeStatusRequest = Mockery::mock(ChangeOrderStatusRequest::class);
        $changeStatusRequest->shouldReceive('validated')->with('status')->once()->andReturn(OrderStatus::Ready->value);

        $this->orderServiceMock->shouldReceive('changeStatus')
            ->once()
            ->with($order, OrderStatus::Ready)
            ->andReturn(true);

        $response = $this->orderController->changeStatus($changeStatusRequest, $order);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue($response->isRedirect());
        $this->assertEquals('Order status updated successfully.', session('success'));
    }

    public function test_change_status_returns_back_with_error_message_on_business_exception()
    {
        $order = Order::factory()->make(['id' => 1, 'status' => OrderStatus::Pending]);
        $order = Mockery::mock($order)->makePartial();

        // Fix: Mock validated('status') to return string directly
        $changeStatusRequest = Mockery::mock(ChangeOrderStatusRequest::class);
        $changeStatusRequest->shouldReceive('validated')->with('status')->once()->andReturn(OrderStatus::Ready->value);
        $errorMessage = 'Cannot change status to Ready.';

        $this->orderServiceMock->shouldReceive('changeStatus')
            ->once()
            ->with($order, OrderStatus::Ready)
            ->andThrow(new BusinessException($errorMessage));

        $response = $this->orderController->changeStatus($changeStatusRequest, $order);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue($response->isRedirect());
        $this->assertEquals($errorMessage, session('error'));
    }
}