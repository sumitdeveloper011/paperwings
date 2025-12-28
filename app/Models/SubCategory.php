<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SubCategory extends Model
{
    use HasFactory;

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

        static::creating(function ($subCategory) {
            if (empty($subCategory->uuid)) {
                $subCategory->uuid = Str::uuid();
            }
            if (empty($subCategory->slug)) {
                $subCategory->slug = Str::slug($subCategory->name);
            }
        });

        static::updating(function ($subCategory) {
            if ($subCategory->isDirty('name')) {
                $subCategory->slug = Str::slug($subCategory->name);
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

    // Get image URL attribute
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('assets/images/no-image.png');
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