<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\HasUuid;
use App\Traits\HasImageUrl;
use App\Traits\HasThumbnail;
use App\Traits\HasUniqueSlug;
use App\Helpers\CacheHelper;

class Brand extends Model
{
    use HasFactory, HasUuid, HasImageUrl, HasThumbnail, HasUniqueSlug;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'image',
        'status'
    ];

    protected $casts = [
        'name' => 'string',
        'slug' => 'string',
        'status' => 'integer'
    ];

    // Boot method to generate UUID and slug automatically
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($brand) {
            if ($brand->isDirty('name') && !$brand->isDirty('slug')) {
                $brand->slug = static::makeUniqueSlug($brand->name, $brand->id);
            }

            // Clear product-related caches when brand status changes
            if ($brand->isDirty('status')) {
                CacheHelper::clearProductCaches();
            }
        });

        static::created(function ($brand) {
            // Clear product caches when new brand is created
            CacheHelper::clearProductCaches();
        });

        static::deleted(function ($brand) {
            // Clear product caches when brand is deleted
            CacheHelper::clearProductCaches();
        });
    }

    // Get the products relationship
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // Get the active products relationship
    public function activeProducts(): HasMany
    {
        return $this->hasMany(Product::class)->where('status', 1);
    }

    // Override fallback image for Brand
    protected function getFallbackImage(): string
    {
        return 'assets/images/no-image.png';
    }

    // Override thumbnail fallback for Brand
    protected function getThumbnailFallback(): ?string
    {
        return asset('assets/images/no-image.png');
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
