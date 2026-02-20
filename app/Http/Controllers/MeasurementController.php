<?php

namespace App\Http\Controllers;

use App\Http\Requests\Measurement\StoreMeasurementRequest;
use App\Http\Requests\Measurement\UpdateMeasurementRequest;
use App\Models\Customer;
use App\Models\Measurement;
use App\Services\MeasurementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Inertia\Inertia;
use Inertia\Response;
use App\Exceptions\BusinessException; // Import BusinessException

class MeasurementController extends Controller implements HasMiddleware
{
    public function __construct(private readonly MeasurementService $measurementService) {}

    public static function middleware(): array
    {
        return ['auth'];
    }

    public function index(Customer $customer): Response
    {
        // Paginate measurements to match the frontend expectation
        $measurements = $customer->measurements()->paginate(10);

        return Inertia::render('Measurement/MeasurementIndex', [
            'customer' => $customer,
            'measurements' => $measurements,
        ]);
    }

    public function store(StoreMeasurementRequest $request, Customer $customer): RedirectResponse
    {
        $this->measurementService->create($customer, $request->validated());

        return back()->with('success', 'Measurement added successfully');
    }

    public function update(UpdateMeasurementRequest $request, Measurement $measurement): RedirectResponse
    {
        $this->measurementService->update($measurement, $request->validated());

        return back()->with('success', 'Measurement updated successfully');
    }

    public function destroy(Measurement $measurement): RedirectResponse
    {
        try {
            $this->measurementService->destroy($measurement);
            return back()->with('success', 'Measurement deleted successfully');
        } catch (BusinessException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
