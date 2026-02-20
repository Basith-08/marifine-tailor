<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Measurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'shoulder',
        'chest',
        'waist',
        'sleeve',
        'other_measurements',
    ];

    protected $casts = [
        'other_measurements' => 'array',
    ];

    /**
     * Get the customer that owns the measurement.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
