<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use App\Traits\HasUuid;
use App\Traits\HasUniqueSlug;
use App\Helpers\CacheHelper;

class Tag extends Model
{
    use HasFactory, HasUuid, HasUniqueSlug;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($tag) {
            if ($tag->isDirty('name') && !$tag->isDirty('slug')) {
                $tag->slug = static::makeUniqueSlug($tag->name, $tag->id);
            }

            // Clear product-related caches when tag changes (affects product filters)
            CacheHelper::clearProductCaches();
        });

        static::created(function ($tag) {
            // Clear product caches when new tag is created
            CacheHelper::clearProductCaches();
        });

        static::deleted(function ($tag) {
            // Clear product caches when tag is deleted
            CacheHelper::clearProductCaches();
        });
    }

    // Get the products relationship
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_tags');
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
