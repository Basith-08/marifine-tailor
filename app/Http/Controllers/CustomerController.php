<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Inertia\Inertia;
use Inertia\Response;
use App\Exceptions\BusinessException; // Import BusinessException

class CustomerController extends Controller implements HasMiddleware
{
    public function __construct(private readonly CustomerService $customerService) {}

    public static function middleware(): array
    {
        return ['auth'];
    }

    public function index(Request $request): Response
    {
        $customers = $this->customerService->getAllCustomers(
            $request->input('search'),
            $request->input('sort'),
            $request->input('direction')
        );

        return Inertia::render('Customer/CustomerIndex', [
            'customers' => $customers,
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }


    public function show(Customer $customer): Response
    {
        return Inertia::render('Customer/CustomerShow', [
            'customer' => $customer,
        ]);
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $this->customerService->create($request->validated());

        return to_route('customers.index')->with('success', 'Customer created successfully');
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->customerService->update($customer, $request->validated());

        return to_route('customers.index')->with('success', 'Customer updated successfully');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        try {
            $this->customerService->destroy($customer);
            return to_route('customers.index')->with('success', 'Customer deleted successfully');
        } catch (BusinessException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
