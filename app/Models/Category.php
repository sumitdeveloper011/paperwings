<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\HasUuid;
use App\Traits\HasImageUrl;
use App\Traits\HasThumbnail;
use App\Traits\HasUniqueSlug;
use App\Helpers\CacheHelper;

class Category extends Model
{
    use HasFactory, HasUuid, HasImageUrl, HasThumbnail, HasUniqueSlug;

    protected $fillable = [
        'uuid',
        'category_id',
        'eposnow_category_id',
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'image',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    // Boot method to generate UUID and slug automatically
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = static::makeUniqueSlug($category->name, $category->id);
            }

            // Clear categories cache when status changes
            if ($category->isDirty('status')) {
                CacheHelper::clearCategoryCaches();
                // Clear price range caches for all products in this category
                CacheHelper::clearPriceRangeCacheForCategory($category->id);
            }
        });

        static::created(function ($category) {
            // Clear categories cache when new category is created
            CacheHelper::clearCategoryCaches();
        });

        static::deleted(function ($category) {
            // Clear categories cache when category is deleted
            CacheHelper::clearCategoryCaches();
            // Clear price range cache for this category
            CacheHelper::clearPriceRangeCacheForCategory($category->id);
            // Also clear all products price range (category deletion affects product counts)
            CacheHelper::clearPriceRangeCaches();
        });
    }

    // Get products relationship
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    // Get active products relationship - use category_id instead of eposnow_category_id
    public function activeProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id')
                    ->where('status', 1);
    }

    // Scope to filter active categories
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Scope to filter inactive categories
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    // Scope to order categories by name
    public function scopeOrdered($query)
    {
        return $query->orderBy('name', 'asc');
    }

    // Get status badge attribute
    public function getStatusBadgeAttribute()
    {
        return $this->status === 1
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-danger">Inactive</span>';
    }

    // Override fallback image for Category
    protected function getFallbackImage(): string
    {
        return 'assets/images/no-image.png';
    }

    // Override thumbnail fallback for Category
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
