<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUuid;

class ShippingPrice extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'region_id',
        'shipping_price',
        'free_shipping_minimum',
        'status',
    ];

    protected $casts = [
        'shipping_price' => 'decimal:2',
        'free_shipping_minimum' => 'decimal:2',
        'status' => 'integer',
    ];


    // Get the region relationship
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    // Scope to get active shipping prices
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Scope to get inactive shipping prices
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
