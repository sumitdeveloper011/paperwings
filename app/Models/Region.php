<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Region extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    // Boot method to generate slug automatically
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($region) {
            if (empty($region->uuid)) {
                $region->uuid = Str::uuid();
            }
            if (empty($region->slug)) {
                $region->slug = Str::slug($region->name);
            }
        });

        static::updating(function ($region) {
            if ($region->isDirty('name')) {
                $region->slug = Str::slug($region->name);
            }
        });
    }

    // Scope to get active regions
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Scope to get inactive regions
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    // Get the shipping price relationship
    public function shippingPrice(): HasOne
    {
        return $this->hasOne(ShippingPrice::class);
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
