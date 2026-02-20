<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\BusinessException;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    public function getAllCustomers(?string $search = null, ?string $sort = 'id', ?string $direction = 'desc'): LengthAwarePaginator
    {
        $allowedSort = ['name', 'created_at', 'id'];
        $sort = in_array($sort, $allowedSort) ? $sort : 'id';
        $direction = $direction === 'asc' ? 'asc' : 'desc';

        return Customer::query()->when($search, function ($query, $search) {
            $query->where('name', 'ilike', "{$search}%")->orWhere('phone', 'ilike', "{$search}%");
        })->orderBy($sort, $direction)->paginate(10)->withQueryString();
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function update(Customer $customer, array $data): bool
    {
        return $customer->update($data);
    }

    public function destroy(Customer $customer): ?bool
    {
        $this->ensureDeletable($customer);

        return $customer->delete();
    }

    public function ensureDeletable(Customer $customer): void
    {
        // Rule: "Tidak boleh delete jika ada order status Pending/Processing"
        if ($customer->orders()->whereIn('status', [OrderStatus::Pending, OrderStatus::Processing])->exists()) {
            throw new BusinessException('Customer cannot be deleted because they have pending or processing orders.');
        }
    }

    public function getCustomerGrowthData()
    {
        return Customer::query()
            ->select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
    }

    public function getAllCustomersForSelection(): \Illuminate\Support\Collection
    {
        return Customer::all(['id', 'name'])->sortBy('name');
    }
}
