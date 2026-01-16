<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Traits\HasUuid;
use App\Traits\HasUniqueSlug;
use App\Helpers\CacheHelper;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasUuid, HasUniqueSlug;

    protected $fillable = [
        'uuid',
        'category_id',
        'brand_id',
        'eposnow_product_id',
        'eposnow_category_id',
        'eposnow_brand_id',
        'barcode',
        'stock',
        'product_type',
        'name',
        'slug',
        'total_price',
        'discount_type',
        'discount_value',
        'discount_price',
        'description',
        'short_description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'sort_order',
        'discount_percentage', // Keep for backward compatibility, will be removed later
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'status' => 'integer',
        'category_id' => 'integer',
        'brand_id' => 'integer',
        'eposnow_product_id' => 'integer',
        'eposnow_category_id' => 'integer',
        'eposnow_brand_id' => 'integer',
        'barcode' => 'string',
        'stock' => 'integer',
        'product_type' => 'integer',
        'discount_type' => 'string',
        'discount_value' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'sort_order' => 'integer',
        'discount_percentage' => 'decimal:2', // Keep for backward compatibility
    ];

    // Boot method to generate UUID and slug automatically
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($product) {
            if ($product->isDirty('name') && !$product->isDirty('slug')) {
                $product->slug = static::makeUniqueSlug($product->name, $product->id);
            }

            // Clear price range cache when price or status changes
            if ($product->isDirty(['total_price', 'discount_price', 'status', 'category_id'])) {
                CacheHelper::clearPriceRangeCaches($product->category_id);
                // Also clear for old category if category changed
                if ($product->isDirty('category_id') && $product->getOriginal('category_id')) {
                    CacheHelper::clearPriceRangeCacheForCategory($product->getOriginal('category_id'));
                }
            }

            // Clear categories cache when product status or category changes
            if ($product->isDirty(['status', 'category_id'])) {
                CacheHelper::clearCategoryCaches();
            }

            // Note: Search caches have short TTL (5 minutes) and will expire naturally
            // Clearing all search caches is not efficient, so we rely on TTL
            // If name or slug changes significantly, search results will update within 5 minutes
        });

        static::created(function ($product) {
            // Clear price range cache when new product is created
            CacheHelper::clearPriceRangeCaches($product->category_id);
            // Clear categories cache
            CacheHelper::clearCategoryCaches();
        });

        static::deleted(function ($product) {
            // Clear price range cache when product is deleted
            CacheHelper::clearPriceRangeCaches($product->category_id);
            // Clear categories cache
            CacheHelper::clearCategoryCaches();
        });
    }

    // Get category relationship
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    // Get brand relationship
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    // Get images relationship
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('id', 'asc');
    }

    // Get accordions relationship
    public function accordions(): HasMany
    {
        return $this->hasMany(ProductAccordion::class);
    }

    // Get wishlists relationship
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    // Get cart items relationship
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // Get reviews relationship
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    // Get approved reviews relationship
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->where('status', 1);
    }

    // Get FAQs relationship
    public function faqs(): HasMany
    {
        return $this->hasMany(ProductFaq::class);
    }

    // Get active FAQs relationship
    public function activeFaqs(): HasMany
    {
        return $this->hasMany(ProductFaq::class);
    }

    // Get tags relationship
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    // Get questions relationship
    public function questions(): HasMany
    {
        return $this->hasMany(ProductQuestion::class);
    }

    // Get approved questions relationship
    public function approvedQuestions(): HasMany
    {
        return $this->hasMany(ProductQuestion::class)->where('status', 1);
    }

    // Get views relationship
    public function views(): HasMany
    {
        return $this->hasMany(ProductView::class);
    }

    // Get bundle items relationship (self-referencing)
    public function bundleItems(): HasMany
    {
        return $this->hasMany(ProductBundleItem::class, 'bundle_id');
    }

    // Get products in bundle (many-to-many)
    public function bundleProducts()
    {
        return $this->belongsToMany(
            Product::class,
            'product_bundle_items',
            'bundle_id',
            'product_id'
        )->withPivot('quantity')->withTimestamps();
    }

    // Check if product is a bundle
    public function isBundle(): bool
    {
        return $this->product_type === 4;
    }

    // Scope to filter active products
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Scope to filter inactive products
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    // Scope to filter products by category
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Scope to filter products by brand
    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    // Scope for bundles only (product_type = 4)
    public function scopeBundles($query)
    {
        return $query->where('product_type', 4);
    }

    // Scope for regular products only (exclude bundles)
    public function scopeProducts($query)
    {
        return $query->where(function($q) {
            $q->where('product_type', '!=', 4)
              ->orWhereNull('product_type');
        });
    }

    // Scope to filter featured products (exclude bundles)
    public function scopeFeatured($query)
    {
        return $query->where('status', 1)->where('product_type', 1);
    }

    // Scope to filter products on sale (exclude bundles)
    public function scopeOnSale($query)
    {
        return $query->where('status', 1)->where('product_type', 2);
    }

    // Scope to filter top rated products (exclude bundles)
    public function scopeTopRated($query)
    {
        return $query->where('status', 1)->where('product_type', 3);
    }

    // Scope to eager load first image
    public function scopeWithFirstImage($query)
    {
        // Eager load images with limit - Laravel handles limit per parent
        return $query->with(['images' => function($q) {
            $q->select('id', 'product_id', 'image')
              ->orderBy('id', 'asc')
              ->limit(1);
        }]);
    }

    // Scope to select minimal columns
    public function scopeSelectMinimal($query)
    {
        return $query->select('id', 'uuid', 'name', 'slug', 'total_price', 'discount_price', 'product_type', 'status', 'category_id', 'eposnow_category_id');
    }

    // Get price without tax attribute
    public function getPriceWithoutTaxAttribute()
    {
        return round($this->total_price / 1.15, 2);
    }

    // Get tax amount attribute
    public function getTaxAmountAttribute()
    {
        return round($this->total_price - $this->price_without_tax, 2);
    }

    // Get tax percentage attribute
    public function getTaxPercentageAttribute()
    {
        return 15;
    }

    // Get status badge attribute
    public function getStatusBadgeAttribute()
    {
        return $this->status === 1
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-danger">Inactive</span>';
    }

    // Get main image URL attribute (original)
    public function getMainImageAttribute()
    {
        // Check if images are already loaded (eager loaded)
        if ($this->relationLoaded('images') && $this->images->isNotEmpty()) {
            return $this->images->first()->image_url;
        }

        $firstImage = $this->images()->first();
        if ($firstImage) {
            return $firstImage->image_url;
        }

        return asset('assets/images/placeholder.jpg');
    }

    // Get main image URL attribute safely (original)
    public function getMainImageUrlAttribute(): string
    {
        if ($this->relationLoaded('images') && $this->images->isNotEmpty()) {
            return $this->images->first()->image_url;
        }

        return asset('assets/images/placeholder.jpg');
    }

    // Get main thumbnail URL attribute
    public function getMainThumbnailUrlAttribute(): string
    {
        $placeholderUrl = asset('assets/images/placeholder.jpg');
        
        // Check if images are already loaded (eager loaded)
        if ($this->relationLoaded('images')) {
            if ($this->images->isNotEmpty()) {
                $firstImage = $this->images->first();
                if ($firstImage && $firstImage->image) {
                    $thumbnailUrl = $firstImage->thumbnail_url;
                    if ($thumbnailUrl && $thumbnailUrl !== '') {
                        return $thumbnailUrl;
                    }
                }
            }
            // If images relation is loaded but empty, return placeholder
            return $placeholderUrl;
        }

        // If images not loaded, DON'T query again (causes N+1)
        // Return placeholder - images should be eager loaded with withFirstImage()
        return $placeholderUrl;
    }

    /**
     * Check if image URL is valid (file exists or is a placeholder)
     *
     * @param string $url
     * @return bool
     */
    protected function isValidImageUrl(string $url): bool
    {
        if (!$url || $url === '') {
            return false;
        }
        
        // For asset URLs (placeholder, no-image), always consider them valid
        if (strpos($url, 'assets/images/') !== false || 
            strpos($url, '/assets/') !== false ||
            strpos($url, 'placeholder') !== false ||
            strpos($url, 'no-image') !== false) {
            return true;
        }
        
        // If URL contains 'storage/', check if file exists
        if (strpos($url, '/storage/') !== false || strpos($url, 'storage/') !== false) {
            // Extract path from URL
            $path = parse_url($url, PHP_URL_PATH);
            if ($path) {
                // Remove /storage/ prefix
                $path = preg_replace('#^/storage/#', '', $path);
                $path = ltrim($path, '/');
                
                // Check if file exists
                if ($path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                    return true;
                }
            }
            
            // Also try direct path extraction
            $path = str_replace(asset('storage/'), '', $url);
            $path = str_replace(url('storage/'), '', $path);
            $path = preg_replace('/\?.*$/', '', $path); // Remove query strings
            $path = ltrim($path, '/');
            
            if ($path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                return true;
            }
        }
        
        // If validation fails, return false (will use placeholder)
        return false;
    }

    // Get image URLs attribute
    public function getImageUrlsAttribute()
    {
        return $this->images()->get()->map(function ($image) {
            return $image->image_url;
        })->toArray();
    }

    // Get full name attribute
    public function getFullNameAttribute()
    {
        $parts = [];
        if ($this->brand) {
            $parts[] = $this->brand->name;
        }
        $parts[] = $this->name;

        return implode(' - ', $parts);
    }

    // Get category path attribute
    public function getCategoryPathAttribute()
    {
        return $this->category ? $this->category->name : 'Uncategorized';
    }

    // Get average rating attribute
    public function getAverageRatingAttribute()
    {
        if (!$this->relationLoaded('approvedReviews')) {
            $this->load('approvedReviews');
        }
        $reviews = $this->approvedReviews;
        if ($reviews->isEmpty()) {
            return 0;
        }
        return round($reviews->avg('rating'), 1);
    }

    // Get reviews count attribute
    public function getReviewsCountAttribute()
    {
        if (!$this->relationLoaded('approvedReviews')) {
            $this->load('approvedReviews');
        }
        return $this->approvedReviews->count();
    }

    // Get final price attribute (with discount applied)
    public function getFinalPriceAttribute()
    {
        // Use new discount_type system
        if ($this->discount_type === 'none' || !$this->discount_type) {
            return $this->total_price;
        }

        if ($this->discount_type === 'percentage' && $this->discount_value) {
            return max(0, $this->total_price - ($this->total_price * $this->discount_value / 100));
        }

        if ($this->discount_type === 'direct' && $this->discount_price && $this->discount_price < $this->total_price) {
            return $this->discount_price;
        }

        // Backward compatibility: if discount_price exists but discount_type not set
        if ($this->discount_price && $this->discount_price < $this->total_price && !$this->discount_type) {
            return $this->discount_price;
        }

        return $this->total_price;
    }

    // Get discount percentage attribute (calculated)
    public function getDiscountPercentageAttribute()
    {
        // Use new discount_type system
        if ($this->discount_type === 'percentage' && $this->discount_value) {
            return round($this->discount_value, 2);
        }

        if ($this->discount_type === 'direct' && $this->total_price > 0 && $this->discount_price && $this->discount_price < $this->total_price) {
            return round((($this->total_price - $this->discount_price) / $this->total_price) * 100, 2);
        }

        // Backward compatibility: if discount_percentage exists but discount_type not set
        if (isset($this->attributes['discount_percentage']) && $this->attributes['discount_percentage'] && !$this->discount_type) {
            return round($this->attributes['discount_percentage'], 2);
        }

        return 0;
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
