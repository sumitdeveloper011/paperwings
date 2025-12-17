<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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
        'barcode' => 'integer',
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
        });
    }

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function accordions(): HasMany
    {
        return $this->hasMany(ProductAccordion::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
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

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    // Price calculation accessors (15% tax)
    public function getPriceWithoutTaxAttribute()
    {
        return round($this->total_price / 1.15, 2);
    }

    public function getTaxAmountAttribute()
    {
        return round($this->total_price - $this->price_without_tax, 2);
    }

    public function getTaxPercentageAttribute()
    {
        return 15; // 15% tax rate
    }

    // Other accessors
    public function getStatusBadgeAttribute()
    {
        return $this->status === 1
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-danger">Inactive</span>';
    }

    public function getMainImageAttribute()
    {
        $firstImage = $this->images()->first();
        if ($firstImage) {
            return $firstImage->image_url;
        }

        return asset('assets/images/placeholder.jpg');
    }

    public function getImageUrlsAttribute()
    {
        return $this->images()->get()->map(function ($image) {
            return $image->image_url;
        })->toArray();
    }

    public function getFullNameAttribute()
    {
        $parts = [];
        if ($this->brand) {
            $parts[] = $this->brand->name;
        }
        $parts[] = $this->name;

        return implode(' - ', $parts);
    }

    public function getCategoryPathAttribute()
    {
        $path = $this->category->name;
        return $path;
    }

    // Route key binding
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
