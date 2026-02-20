<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Measurement;
use App\Exceptions\BusinessException; // Assuming BusinessException is generic enough for this
use Illuminate\Database\Eloquent\Collection;

class MeasurementService
{
    public function create(Customer $customer, array $data): Measurement
    {
        // Ensure customer exists is handled by the request validation 'exists:customers,id'
        // Numeric validation also handled by request
        return $customer->measurements()->create($data);
    }

    public function getByCustomer(Customer $customer): Collection
    {
        return $customer->measurements()->get();
    }

    public function update(Measurement $measurement, array $data): bool
    {
        // Numeric validation also handled by request
        return $measurement->update($data);
    }

    public function destroy(Measurement $measurement): ?bool
    {
        return $measurement->delete();
    }
}
