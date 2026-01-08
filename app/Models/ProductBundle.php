<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ProductBundle extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'image',
        'bundle_price',
        'discount_percentage',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'bundle_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bundle) {
            // Generate UUID if not provided
            if (empty($bundle->uuid)) {
                $bundle->uuid = Str::uuid();
            }
            
            if (empty($bundle->slug)) {
                $bundle->slug = static::makeUniqueSlug($bundle->name);
            } else {
                // If slug is provided, ensure it's unique
                $bundle->slug = static::makeUniqueSlug($bundle->slug, $bundle->id ?? null);
            }
        });

        static::updating(function ($bundle) {
            // If name changed, update slug to match (unless slug was explicitly changed)
            if ($bundle->isDirty('name') && !$bundle->isDirty('slug')) {
                $bundle->slug = static::makeUniqueSlug($bundle->name, $bundle->id);
            } elseif ($bundle->isDirty('slug')) {
                // If slug is being updated, ensure it's unique
                $bundle->slug = static::makeUniqueSlug($bundle->slug, $bundle->id);
            }
        });
    }

    /**
     * Generate a unique slug from a string
     *
     * @param string $name
     * @param int|null $excludeId Bundle ID to exclude from uniqueness check
     * @return string
     */
    protected static function makeUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->when($excludeId, function ($query) use ($excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    // Get the items relationship
    public function items(): HasMany
    {
        return $this->hasMany(ProductBundleItem::class, 'bundle_id');
    }

    // Get the bundle items relationship
    public function bundleItems(): HasMany
    {
        return $this->hasMany(ProductBundleItem::class, 'bundle_id');
    }

    // Get the products relationship
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_bundle_items', 'bundle_id', 'product_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    // Scope to filter active bundles
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // Scope to order bundles
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
