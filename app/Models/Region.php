<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Traits\HasUuid;
use App\Traits\HasUniqueSlug;

class Region extends Model
{
    use HasFactory, SoftDeletes, HasUuid, HasUniqueSlug;

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

        static::updating(function ($region) {
            if ($region->isDirty('name') && !$region->isDirty('slug')) {
                $region->slug = static::makeUniqueSlug($region->name, $region->id);
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
