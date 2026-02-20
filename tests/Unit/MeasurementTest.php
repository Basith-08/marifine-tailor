<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Measurement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeasurementTest extends TestCase
{
    use RefreshDatabase;

    public function test_measurement_can_be_created()
    {
        $customer = Customer::factory()->create();
        $measurement = Measurement::factory()->create([
            'customer_id' => $customer->id,
            'shoulder' => 10.5,
            'chest' => 20.0,
            'waist' => 15.0,
            'sleeve' => 25.0,
            'other_measurements' => ['neck' => 12.0, 'inseam' => 30.0],
        ]);

        $this->assertNotNull($measurement);
        $this->assertDatabaseHas('measurements', [
            'customer_id' => $customer->id,
            'shoulder' => 10.5,
            'chest' => 20.0,
        ]);
        $this->assertIsArray($measurement->other_measurements);
        $this->assertEquals(12.0, $measurement->other_measurements['neck']);
    }

    public function test_measurement_belongs_to_customer()
    {
        $customer = Customer::factory()->create();
        $measurement = Measurement::factory()->create(['customer_id' => $customer->id]);

        $this->assertInstanceOf(Customer::class, $measurement->customer);
        $this->assertEquals($customer->id, $measurement->customer->id);
    }

    public function test_measurement_can_be_updated()
    {
        $customer = Customer::factory()->create();
        $measurement = Measurement::factory()->for($customer)->create();
        $newShoulder = 11.0;
        $newChest = 21.0;

        $measurement->update([
            'shoulder' => $newShoulder,
            'chest' => $newChest,
        ]);

        $this->assertEquals($newShoulder, $measurement->shoulder);
        $this->assertEquals($newChest, $measurement->chest);
        $this->assertDatabaseHas('measurements', [
            'id' => $measurement->id,
            'shoulder' => $newShoulder,
            'chest' => $newChest,
        ]);
    }

    public function test_measurement_can_be_deleted()
    {
        $customer = Customer::factory()->create();
        $measurement = Measurement::factory()->for($customer)->create();
        $measurement->delete();

        $this->assertDatabaseMissing('measurements', [
            'id' => $measurement->id,
        ]);
    }
}
