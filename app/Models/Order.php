<?php

namespace App\Models;

use App\Enums\OrderStatus; // Import the Enum
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'order_date',
        'deadline',
        'item_type',
        'status',
    ];

    protected $casts = [
        'order_date' => 'date',
        'deadline' => 'date',
        'status' => OrderStatus::class, // Cast status to OrderStatus Enum
    ];

    /**
     * Get the customer that owns the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
