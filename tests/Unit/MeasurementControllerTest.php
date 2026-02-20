<?php

namespace Tests\Unit;

use App\Exceptions\BusinessException;
use App\Http\Controllers\MeasurementController;
use App\Http\Requests\Measurement\StoreMeasurementRequest;
use App\Http\Requests\Measurement\UpdateMeasurementRequest;
use App\Models\Customer;
use App\Models\Measurement;
use App\Services\MeasurementService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;
use Mockery;
use Tests\TestCase;

class MeasurementControllerTest extends TestCase
{
    protected Mockery\MockInterface $measurementServiceMock;
    protected MeasurementController $measurementController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->measurementServiceMock = Mockery::mock(MeasurementService::class);
        $this->measurementController = new MeasurementController($this->measurementServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_index_renders_measurement_index_with_customer_and_measurements()
    {
        $customer = Customer::factory()->make(['id' => 1]); // Create a real model instance
        $customer = Mockery::mock($customer)->makePartial(); // Wrap in a partial mock

        $mockPaginator = Mockery::mock(LengthAwarePaginator::class);
        $mockPaginator->shouldReceive('toArray')->andReturn(['data' => [Measurement::factory()->make()]]);
        // Mock any other methods the paginator might use in the view if needed
        $mockPaginator->shouldReceive('withQueryString')->andReturnSelf();
        
        $hasManyMock = Mockery::mock(HasMany::class);
        $hasManyMock->shouldReceive('paginate')->with(10)->andReturn($mockPaginator);

        // Expect the measurements method to be called on the customer mock
        $customer->shouldReceive('measurements')->once()->andReturn($hasManyMock);


        Inertia::shouldReceive('render')
            ->once()
            ->with('Measurement/MeasurementIndex', [
                'customer' => $customer,
                'measurements' => $mockPaginator,
            ])
            ->andReturn(Mockery::mock(Response::class));

        $response = $this->measurementController->index($customer);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_store_creates_measurement_and_redirects_back_with_success_message()
    {
        $customer = Customer::factory()->make(['id' => 1]); // Use real customer for passing to service mock
        $validatedData = ['shoulder' => 10.0, 'chest' => 20.0];
        $storeRequest = Mockery::mock(StoreMeasurementRequest::class);
        $storeRequest->shouldReceive('validated')->once()->andReturn($validatedData);

        $this->measurementServiceMock->shouldReceive('create')
            ->once()
            ->with($customer, $validatedData)
            ->andReturn(Measurement::factory()->make());

        $response = $this->measurementController->store($storeRequest, $customer);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue($response->isRedirect());
        $this->assertEquals('Measurement added successfully', session('success'));
    }

    public function test_update_updates_measurement_and_redirects_back_with_success_message()
    {
        $measurement = Measurement::factory()->make(['id' => 1]); // Create a real model instance
        $measurement = Mockery::mock($measurement)->makePartial(); // Wrap in a partial mock

        $validatedData = ['shoulder' => 11.0, 'chest' => 21.0];
        $updateRequest = Mockery::mock(UpdateMeasurementRequest::class);
        $updateRequest->shouldReceive('validated')->once()->andReturn($validatedData);

        $this->measurementServiceMock->shouldReceive('update')
            ->once()
            ->with($measurement, $validatedData)
            ->andReturn(true);

        $response = $this->measurementController->update($updateRequest, $measurement);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue($response->isRedirect());
        $this->assertEquals('Measurement updated successfully', session('success'));
    }

    public function test_destroy_deletes_measurement_and_redirects_back_with_success_message()
    {
        $measurement = Measurement::factory()->make(['id' => 1]); // Create a real model instance
        $measurement = Mockery::mock($measurement)->makePartial(); // Wrap in a partial mock

        $this->measurementServiceMock->shouldReceive('destroy')
            ->once()
            ->with($measurement)
            ->andReturn(true);

        $response = $this->measurementController->destroy($measurement);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue($response->isRedirect());
        $this->assertEquals('Measurement deleted successfully', session('success'));
    }

    public function test_destroy_returns_back_with_error_message_on_business_exception()
    {
        $measurement = Measurement::factory()->make(['id' => 1]); // Create a real model instance
        $measurement = Mockery::mock($measurement)->makePartial(); // Wrap in a partial mock
        $errorMessage = 'Measurement cannot be deleted due to business rules.';

        $this->measurementServiceMock->shouldReceive('destroy')
            ->once()
            ->with($measurement)
            ->andThrow(new BusinessException($errorMessage));

        $response = $this->measurementController->destroy($measurement);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue($response->isRedirect());
        $this->assertEquals($errorMessage, session('error'));
    }
}
