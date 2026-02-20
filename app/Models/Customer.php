<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'address',
    ];

    /**
     * Get the orders for the customer.
     * Temporary placeholder until the Order model is created.
     */
    public function orders(): HasMany
    {
        // Assuming an 'Order' model will exist and have a 'customer_id' foreign key.
        return $this->hasMany(Order::class);
    }

    /**
     * Get the measurements for the customer.
     */
    public function measurements(): HasMany
    {
        return $this->hasMany(Measurement::class);
    }
}
