<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Traits\HasUuid;
use App\Traits\HasImageUrl;
use App\Traits\HasUniqueSlug;

class SubCategory extends Model
{
    use HasFactory, HasUuid, HasImageUrl, HasUniqueSlug;

    protected $table = 'subcategories';

    protected $fillable = [
        'uuid',
        'category_id',
        'name',
        'slug',
        'status',
        'image'
    ];

    protected $casts = [
        'status' => 'integer',
        'category_id' => 'integer'
    ];

    // Boot method to generate UUID and slug automatically
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($subCategory) {
            if ($subCategory->isDirty('name') && !$subCategory->isDirty('slug')) {
                $subCategory->slug = static::makeUniqueSlug($subCategory->name, $subCategory->id);
            }
        });
    }

    // Get the category relationship
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Scope to filter active subcategories
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Scope to filter inactive subcategories
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    // Scope to filter subcategories by category
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Get the products relationship
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'subcategory_id');
    }

    // Get the active products relationship
    public function activeProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'subcategory_id')->where('status', 'active');
    }

    // Get status badge attribute
    public function getStatusBadgeAttribute()
    {
        return $this->status === 1
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-danger">Inactive</span>';
    }

    // Override fallback image for SubCategory
    protected function getFallbackImage(): string
    {
        return 'assets/images/no-image.png';
    }

    // Get full name attribute
    public function getFullNameAttribute()
    {
        return $this->category->name . ' > ' . $this->name;
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
