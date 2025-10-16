<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'product_id',
        'category_id',
        // 'subcategory_id',
        'brand_id',
        'name',
        'slug',
        'total_price',
        'description',
        'short_description',
        'barcode',
        'accordion_data',
        'images',
        'status',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'accordion_data' => 'array',
        'images' => 'array',
        'status' => 'integer',
        'category_id' => 'integer',
        'barcode' => 'integer',
        'subcategory_id' => 'integer',
        'brand_id' => 'integer',
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
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
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

    public function scopeBySubCategory($query, $subCategoryId)
    {
        return $query->where('subcategory_id', $subCategoryId);
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
        if ($this->images && is_array($this->images) && count($this->images) > 0) {
            return asset('storage/'.$this->images[0]);
        }

        return asset('assets/images/no-image.png');
    }

    public function getImageUrlsAttribute()
    {
        if ($this->images && is_array($this->images)) {
            return collect($this->images)->map(function ($image) {
                return asset('storage/'.$image);
            })->toArray();
        }

        return [];
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
        if ($this->subCategory) {
            $path .= ' > '.$this->subCategory->name;
        }

        return $path;
    }

    // Route key binding
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
