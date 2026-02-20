<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly CustomerService $customerService,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Dashboard/DashboardIndex', [
            'orderSummary' => $this->orderService->getOrderSummary(),
            'customerGrowth' => $this->customerService->getCustomerGrowthData(),
        ]);
    }
}
   
