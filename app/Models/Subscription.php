<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Subscription extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'email',
        'status',
        'subscribed_at',
        'unsubscribed_at',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    // Boot method to generate UUID automatically
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            if (empty($subscription->subscribed_at)) {
                $subscription->subscribed_at = now();
            }
            if (empty($subscription->status)) {
                $subscription->status = 1; // Active by default
            }
        });
    }

    // Scope to filter active subscriptions
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Scope to filter inactive subscriptions
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    // Get status badge attribute
    public function getStatusBadgeAttribute()
    {
        return $this->status === 1
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-danger">Inactive</span>';
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}

