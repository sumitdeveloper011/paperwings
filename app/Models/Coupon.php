<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Coupon extends Model
{
    use HasFactory;

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

    /**
     * Boot function to generate UUID before creating coupon
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Check if coupon is active
     */
    public function isActive()
    {
        return $this->status === 1;
    }

    /**
     * Check if coupon is expired
     */
    public function isExpired()
    {
        return now()->greaterThan($this->end_date);
    }

    /**
     * Check if coupon is valid (active and not expired)
     */
    public function isValid()
    {
        return $this->isActive() && !$this->isExpired() && 
               ($this->usage_limit === null || $this->usage_count < $this->usage_limit);
    }

    /**
     * Get status badge
     */
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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    public function scopeValid($query)
    {
        return $query->where('status', 1)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }
}
