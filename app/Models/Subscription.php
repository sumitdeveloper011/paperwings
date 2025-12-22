<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subscription extends Model
{
    use HasFactory;

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
            if (empty($subscription->uuid)) {
                $subscription->uuid = Str::uuid();
            }
            if (empty($subscription->subscribed_at)) {
                $subscription->subscribed_at = now();
            }
            if (empty($subscription->status)) {
                $subscription->status = 1; // Active by default
            }
        });
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

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return $this->status === 1
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-danger">Inactive</span>';
    }

    // Route key binding
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}

