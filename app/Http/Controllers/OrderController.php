<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Exceptions\BusinessException;
use App\Http\Requests\Order\ChangeOrderStatusRequest;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Models\Order;
use App\Models\Customer;
use App\Services\CustomerService; // Import CustomerService
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller implements HasMiddleware
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly CustomerService $customerService // Inject CustomerService
    ) {}

    public static function middleware(): array
    {
        return ['auth'];
    }

    public function index(Request $request): Response
    {
        return Inertia::render('Order/OrderIndex', [
            'orders' => $this->orderService->getPaginatedOrders($request),
            'filters' => $request->only(['search', 'status', 'sort', 'direction']),
            'orderStatuses' => collect(OrderStatus::cases())->map(fn ($status) => [
                'name' => $status->label(),
                'value' => $status->value,
                'color' => $status->color(),
            ]),
            'customers' => $this->customerService->getAllCustomersForSelection(), // Use CustomerService
            'orderSummary' => $this->orderService->getOrderSummary(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Order/OrderCreate', [
            'customers' => $this->customerService->getAllCustomersForSelection(),
            'orderStatuses' => collect(OrderStatus::cases())->map(fn ($status) => [
                'name' => $status->label(),
                'value' => $status->value,
            ]),
        ]);
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        try {
            $this->orderService->create($request->validated());
            return to_route('orders.index')->with('success', 'Order created successfully.');
        } catch (BusinessException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        try {
            $this->orderService->update($order, $request->validated());
            return to_route('orders.index')->with('success', 'Order updated successfully.');
        } catch (BusinessException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Order $order): RedirectResponse
    {
        try {
            $this->orderService->destroy($order);
            return to_route('orders.index')->with('success', 'Order deleted successfully.');
        } catch (BusinessException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function changeStatus(ChangeOrderStatusRequest $request, Order $order): RedirectResponse
    {
        try {
            $newStatus = OrderStatus::from($request->validated('status'));
            $this->orderService->changeStatus($order, $newStatus);
            return back()->with('success', 'Order status updated successfully.');
        } catch (BusinessException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
