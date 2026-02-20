<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Measurement;
use App\Services\MeasurementService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeasurementServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MeasurementService $measurementService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->measurementService = new MeasurementService();
    }

    public function test_can_create_measurement()
    {
        $customer = Customer::factory()->create();
        $data = [
            'shoulder' => 10.0,
            'chest' => 20.0,
            'waist' => 30.0,
            'sleeve' => 25.0,
            'other_measurements' => ['neck' => 15.0],
        ];

        $measurement = $this->measurementService->create($customer, $data);

        $this->assertInstanceOf(Measurement::class, $measurement);
        $this->assertEquals($customer->id, $measurement->customer_id);
        $this->assertDatabaseHas('measurements', [
            'customer_id' => $customer->id,
            'shoulder' => 10.0,
            'chest' => 20.0,
            'waist' => 30.0,
            'sleeve' => 25.0,
        ]);
        $this->assertIsArray($measurement->other_measurements);
        $this->assertEquals(15.0, $measurement->other_measurements['neck']);
    }

    public function test_can_get_measurements_by_customer()
    {
        $customer = Customer::factory()->create();
        Measurement::factory()->count(3)->for($customer)->create();
        Measurement::factory()->count(2)->for(Customer::factory())->create(); // Other measurements for other customers

        $measurements = $this->measurementService->getByCustomer($customer);

        $this->assertInstanceOf(Collection::class, $measurements);
        $this->assertCount(3, $measurements);
        $measurements->each(function ($measurement) use ($customer) {
            $this->assertEquals($customer->id, $measurement->customer_id);
        });
    }

    public function test_can_update_measurement()
    {
        $measurement = Measurement::factory()->for(Customer::factory())->create();
        $newData = [
            'shoulder' => 11.0,
            'chest' => 21.0,
        ];

        $result = $this->measurementService->update($measurement, $newData);

        $this->assertTrue($result);
        $this->assertDatabaseHas('measurements', array_merge(['id' => $measurement->id], $newData));
    }

    public function test_can_destroy_measurement()
    {
        $measurement = Measurement::factory()->for(Customer::factory())->create();

        $result = $this->measurementService->destroy($measurement);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('measurements', ['id' => $measurement->id]);
    }
}
