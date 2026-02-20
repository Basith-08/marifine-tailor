<?php

namespace Tests\Unit;

use App\Exceptions\BusinessException;
use App\Http\Controllers\CustomerController;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;
use Mockery;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    protected Mockery\MockInterface $customerServiceMock;
    protected CustomerController $customerController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customerServiceMock = Mockery::mock(CustomerService::class);
        $this->customerController = new CustomerController($this->customerServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_index_renders_customer_index_with_customers_and_filters()
    {
        $mockPaginator = Mockery::mock(LengthAwarePaginator::class);
        $mockPaginator->shouldReceive('toArray')->andReturn(['data' => [Customer::factory()->make()]]);
        $mockPaginator->shouldReceive('items')->andReturn(collect([Customer::factory()->make()]));
        $mockPaginator->shouldReceive('hasPages')->andReturn(false);
        $mockPaginator->shouldReceive('onEachSide')->andReturn(1);
        $mockPaginator->shouldReceive('currentPage')->andReturn(1);
        $mockPaginator->shouldReceive('lastPage')->andReturn(1);
        $mockPaginator->shouldReceive('perPage')->andReturn(10);
        $mockPaginator->shouldReceive('total')->andReturn(1);
        $mockPaginator->shouldReceive('url')->andReturn('http://localhost');
        $mockPaginator->shouldReceive('withQueryString')->andReturnSelf();


        $this->customerServiceMock->shouldReceive('getAllCustomers')
            ->once()
            ->with(null, null, null) // Check that arguments are passed correctly from Request
            ->andReturn($mockPaginator);

        Inertia::shouldReceive('render')
            ->once()
            ->with('Customer/CustomerIndex', [
                'customers' => $mockPaginator,
                'filters' => [],
            ])
            ->andReturn(Mockery::mock(Response::class));

        $request = Request::create('/customers', 'GET');
        $response = $this->customerController->index($request);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_show_renders_customer_show_with_customer_data()
    {
        $customer = Customer::factory()->make();

        Inertia::shouldReceive('render')
            ->once()
            ->with('Customer/CustomerShow', ['customer' => $customer])
            ->andReturn(Mockery::mock(Response::class));

        $response = $this->customerController->show($customer);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_store_creates_customer_and_redirects_with_success_message()
    {
        $validatedData = ['name' => 'New Customer', 'phone' => '123', 'address' => 'Test Address'];
        $storeRequest = Mockery::mock(StoreCustomerRequest::class);
        $storeRequest->shouldReceive('validated')->once()->andReturn($validatedData);

        $this->customerServiceMock->shouldReceive('create')
            ->once()
            ->with($validatedData)
            ->andReturn(Customer::factory()->make());

        $response = $this->customerController->store($storeRequest);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('customers.index'), $response->getTargetUrl());
        $this->assertEquals('Customer created successfully', session('success'));
    }

    public function test_update_updates_customer_and_redirects_with_success_message()
    {
        $customer = Customer::factory()->make(['id' => 1]);
        $validatedData = ['name' => 'Updated Customer', 'phone' => '456', 'address' => 'Updated Address'];
        $updateRequest = Mockery::mock(UpdateCustomerRequest::class);
        $updateRequest->shouldReceive('validated')->once()->andReturn($validatedData);

        $this->customerServiceMock->shouldReceive('update')
            ->once()
            ->with($customer, $validatedData)
            ->andReturn(true);

        $response = $this->customerController->update($updateRequest, $customer);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('customers.index'), $response->getTargetUrl());
        $this->assertEquals('Customer updated successfully', session('success'));
    }

    public function test_destroy_deletes_customer_and_redirects_with_success_message()
    {
        $customer = Customer::factory()->make(['id' => 1]);

        $this->customerServiceMock->shouldReceive('destroy')
            ->once()
            ->with($customer)
            ->andReturn(true);

        $response = $this->customerController->destroy($customer);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('customers.index'), $response->getTargetUrl());
        $this->assertEquals('Customer deleted successfully', session('success'));
    }

    public function test_destroy_returns_to_previous_page_with_error_message_on_business_exception()
    {
        $customer = Customer::factory()->make(['id' => 1]);
        $errorMessage = 'Customer cannot be deleted due to business rules.';

        $this->customerServiceMock->shouldReceive('destroy')
            ->once()
            ->with($customer)
            ->andThrow(new BusinessException($errorMessage));

        $response = $this->customerController->destroy($customer);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue($response->isRedirect());
        $this->assertEquals($errorMessage, session('error'));
    }
}
