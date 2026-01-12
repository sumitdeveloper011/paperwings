<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

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

        static::creating(function ($category) {
            if (empty($category->uuid)) {
                $category->uuid = Str::uuid();
            }
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $category->slug = Str::slug($category->name);
            }

            // Clear categories cache when status changes
            if ($category->isDirty('status')) {
                Cache::forget('categories_with_count_all');
                Cache::forget('categories_with_count_sidebar');
                Cache::forget('header_categories');
            }
        });

        static::created(function ($category) {
            // Clear categories cache when new category is created
            Cache::forget('categories_with_count_all');
            Cache::forget('categories_with_count_sidebar');
            Cache::forget('header_categories');
        });

        static::deleted(function ($category) {
            // Clear categories cache when category is deleted
            Cache::forget('categories_with_count_all');
            Cache::forget('categories_with_count_sidebar');
            Cache::forget('header_categories');
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

    // Get image URL attribute (original)
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/'.$this->image) : asset('assets/images/no-image.png');
    }

    // Get thumbnail path attribute
    public function getThumbnailPathAttribute()
    {
        if (!$this->image) {
            return null;
        }

        // Check if path has /original/ folder structure
        if (strpos($this->image, '/original/') !== false) {
            // Replace /original/ with /thumbnails/
            return str_replace('/original/', '/thumbnails/', $this->image);
        }

        // For old structure (backward compatibility)
        $pathParts = explode('/', $this->image);
        $fileName = array_pop($pathParts);
        $basePath = implode('/', $pathParts);

        return $basePath . '/thumbnails/' . $fileName;
    }

    // Get thumbnail URL attribute
    public function getThumbnailUrlAttribute()
    {
        if (!$this->image) {
            return asset('assets/images/no-image.png');
        }

        $thumbnailPath = $this->thumbnail_path;

        // Check if thumbnail exists
        if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
            return asset('storage/' . $thumbnailPath);
        }

        // Fallback to original if thumbnail doesn't exist
        return $this->image_url;
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
