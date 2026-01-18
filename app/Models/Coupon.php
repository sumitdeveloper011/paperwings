<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use App\Helpers\CacheHelper;

class Coupon extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'usage_count',
        'usage_limit_per_user',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'usage_limit_per_user' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($coupon) {
            // Clear relevant caches when coupon status, dates, or limits change
            // Note: Coupons are typically checked in real-time, but clearing cache ensures consistency
            if ($coupon->isDirty(['status', 'start_date', 'end_date', 'usage_limit', 'value', 'type'])) {
                // If coupons are cached anywhere, they should be cleared
                // For now, we'll clear dashboard stats as coupons affect order statistics
                CacheHelper::clearDashboardStats();
            }
        });

        static::created(function ($coupon) {
            // Clear dashboard stats when new coupon is created
            CacheHelper::clearDashboardStats();
        });

        static::deleted(function ($coupon) {
            // Clear dashboard stats when coupon is deleted
            CacheHelper::clearDashboardStats();
        });
    }

    // Get the route key name - use uuid for admin routes
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Check if coupon is active
    public function isActive()
    {
        return $this->status === 1;
    }

    // Check if coupon is expired
    public function isExpired()
    {
        return now()->greaterThan($this->end_date);
    }

    // Check if coupon is valid (active and not expired)
    public function isValid()
    {
        return $this->isActive() && !$this->isExpired() &&
               ($this->usage_limit === null || $this->usage_count < $this->usage_limit);
    }

    // Get status badge attribute
    public function getStatusBadgeAttribute()
    {
        if (!$this->isActive()) {
            return '<span class="badge bg-danger">Inactive</span>';
        }

        if ($this->isExpired()) {
            return '<span class="badge bg-warning">Expired</span>';
        }

        return '<span class="badge bg-success">Active</span>';
    }

    // Scope to filter active coupons
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Scope to filter inactive coupons
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    // Scope to filter valid coupons
    public function scopeValid($query)
    {
        return $query->where('status', 1)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }
}
