<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'image',
        'status'
    ];

    protected $casts = [
        'name' => 'string',
        'slug' => 'string',
        'status' => 'integer'
    ];

    // Boot method to generate UUID and slug automatically
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            if (empty($brand->uuid)) {
                $brand->uuid = Str::uuid();
            }
            if (empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
        });

        static::updating(function ($brand) {
            if ($brand->isDirty('name')) {
                $brand->slug = Str::slug($brand->name);
            }
        });
    }

    // Get the products relationship
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // Get the active products relationship
    public function activeProducts(): HasMany
    {
        return $this->hasMany(Product::class)->where('status', 1);
    }

    // Get image URL attribute
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('assets/images/no-image.png');
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}