<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use Carbon\Carbon;
use App\Exceptions\BusinessException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function create(array $data): Order
    {
        // Default status to Pending if not provided
        if (!isset($data['status'])) {
            $data['status'] = OrderStatus::Pending;
        } elseif (is_string($data['status'])) {
            // Convert string status to Enum
            $data['status'] = OrderStatus::from($data['status']);
        }

        $this->validateDeadline(Carbon::parse($data['order_date']), Carbon::parse($data['deadline']));

        return Order::create($data);
    }

    public function update(Order $order, array $data): bool
    {
        if (isset($data['order_date']) || isset($data['deadline'])) {
            $orderDate = isset($data['order_date']) ? Carbon::parse($data['order_date']) : $order->order_date->toMutable();
            $deadline = isset($data['deadline']) ? Carbon::parse($data['deadline']) : $order->deadline->toMutable();
            $this->validateDeadline($orderDate, $deadline);
        }

        if (isset($data['status']) && is_string($data['status'])) {
            $data['status'] = OrderStatus::from($data['status']);
        }

        return $order->update($data);
    }

    public function changeStatus(Order $order, OrderStatus $status): bool
    {
        $order->status = $status;
        return $order->save();
    }

    public function destroy(Order $order): ?bool
    {
        // For now, simply soft delete. Additional business logic (e.g., checks) can be added here.
        return $order->delete();
    }

    public function validateDeadline(Carbon $orderDate, Carbon $deadline): void
    {
        if ($deadline->lessThanOrEqualTo($orderDate)) {
            throw new BusinessException('Deadline must be after the order date.');
        }
    }

    public function getPaginatedOrders(Request $request): LengthAwarePaginator
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $sort = $request->input('sort', 'deadline');
        $direction = $request->input('direction', 'asc');

        return Order::with('customer')
            ->when($search, function ($query, $search) {
                $query->whereHas('customer', function ($q) use ($search) {
                    $q->where('name', 'ilike', "%{$search}%");
                });
            })
            ->when($status, fn($query) => $query->where('status', $status))
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();
    }

    public function getOrderSummary(): array
    {
        $statusCounts = DB::table('orders')
                        ->select('status', DB::raw('count(*) as count'))
                        ->whereNull('deleted_at')
                        ->groupBy('status')
                        ->pluck('count', 'status')
                        ->toArray();

        $orderSummary = [];
        foreach (OrderStatus::cases() as $statusEnum) {
            $orderSummary[$statusEnum->value] = [
                'count' => $statusCounts[$statusEnum->value] ?? 0,
                'label' => $statusEnum->label(),
                'color' => $statusEnum->color(),
            ];
        }
        return $orderSummary;
    }
}
