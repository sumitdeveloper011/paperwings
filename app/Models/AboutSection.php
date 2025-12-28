<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AboutSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'badge',
        'title',
        'description',
        'button_text',
        'button_link',
        'image',
        'status',
        'sort_order'
    ];

    protected $casts = [
        'status' => 'integer',
        'sort_order' => 'integer'
    ];

    // Boot method to generate UUID automatically
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($aboutSection) {
            if (empty($aboutSection->uuid)) {
                $aboutSection->uuid = Str::uuid();
            }
            
            if (is_null($aboutSection->sort_order)) {
                $maxOrder = static::max('sort_order') ?? 0;
                $aboutSection->sort_order = $maxOrder + 1;
            }
        });
    }

    // Scope to filter active about sections
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Scope to order about sections
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Get image URL attribute
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('assets/frontend/images/about-us.jpg');
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
