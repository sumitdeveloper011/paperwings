<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    use HasFactory;

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
        'discount_price',
        'description',
        'short_description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
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
        'discount_price' => 'decimal:2',
    ];

    // Boot method to generate UUID and slug automatically
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->uuid)) {
                $product->uuid = Str::uuid();
            }
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name')) {
                $product->slug = Str::slug($product->name);
            }

            // Clear price range cache when price or status changes
            if ($product->isDirty(['total_price', 'discount_price', 'status', 'eposnow_category_id'])) {
                Cache::forget('price_range_all_products');
                if ($product->eposnow_category_id) {
                    Cache::forget('price_range_category_' . $product->eposnow_category_id);
                }
                // Also clear for old category if category changed
                if ($product->isDirty('eposnow_category_id') && $product->getOriginal('eposnow_category_id')) {
                    Cache::forget('price_range_category_' . $product->getOriginal('eposnow_category_id'));
                }
            }

            // Clear categories cache when product status or category changes
            if ($product->isDirty(['status', 'category_id', 'eposnow_category_id'])) {
                Cache::forget('categories_with_count_all');
                Cache::forget('categories_with_count_sidebar');
                Cache::forget('header_categories');
                Cache::forget('footer_categories');
            }

            // Note: Search caches have short TTL (5 minutes) and will expire naturally
            // Clearing all search caches is not efficient, so we rely on TTL
            // If name or slug changes significantly, search results will update within 5 minutes
        });

        static::created(function ($product) {
            // Clear price range cache when new product is created
            Cache::forget('price_range_all_products');
            if ($product->eposnow_category_id) {
                Cache::forget('price_range_category_' . $product->eposnow_category_id);
            }
            // Clear categories cache
            Cache::forget('categories_with_count_all');
            Cache::forget('categories_with_count_sidebar');
            Cache::forget('header_categories');
        });

        static::deleted(function ($product) {
            // Clear price range cache when product is deleted
            Cache::forget('price_range_all_products');
            if ($product->eposnow_category_id) {
                Cache::forget('price_range_category_' . $product->eposnow_category_id);
            }
            // Clear categories cache
            Cache::forget('categories_with_count_all');
            Cache::forget('categories_with_count_sidebar');
            Cache::forget('header_categories');
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
        return $this->hasMany(ProductImage::class);
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
        return $this->hasMany(ProductFaq::class)->where('status', true);
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

    // Get bundle items relationship
    public function bundleItems(): HasMany
    {
        return $this->hasMany(ProductBundleItem::class);
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

    // Scope to filter featured products
    public function scopeFeatured($query)
    {
        return $query->where('status', 1)->where('product_type', 1);
    }

    // Scope to filter products on sale
    public function scopeOnSale($query)
    {
        return $query->where('status', 1)->where('product_type', 2);
    }

    // Scope to filter top rated products
    public function scopeTopRated($query)
    {
        return $query->where('status', 1)->where('product_type', 3);
    }

    // Scope to eager load first image
    public function scopeWithFirstImage($query)
    {
        return $query->with(['images' => function($q) {
            $q->select('id', 'product_id', 'image')
              ->orderBy('id')
              ->limit(1);
        }]);
    }

    // Scope to select minimal columns
    public function scopeSelectMinimal($query)
    {
        return $query->select('id', 'name', 'slug', 'total_price', 'discount_price', 'product_type', 'status', 'category_id', 'eposnow_category_id');
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

    // Get main image URL attribute
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

    // Get main image URL attribute safely
    public function getMainImageUrlAttribute(): string
    {
        if ($this->relationLoaded('images') && $this->images->isNotEmpty()) {
            return $this->images->first()->image_url;
        }

        return asset('assets/images/placeholder.jpg');
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
        $path = $this->category->name;
        return $path;
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

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
