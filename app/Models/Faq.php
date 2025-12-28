<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'question',
        'answer',
        'category',
        'status',
        'sort_order'
    ];

    protected $casts = [
        'status' => 'integer',
        'sort_order' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($faq) {
            if (empty($faq->uuid)) {
                $faq->uuid = Str::uuid();
            }
            
            if (is_null($faq->sort_order)) {
                $maxOrder = static::max('sort_order') ?? 0;
                $faq->sort_order = $maxOrder + 1;
            }
        });
    }

    // Scope to filter active FAQs
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Scope to filter inactive FAQs
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    // Scope to order FAQs
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Scope to filter FAQs by category
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Get category name attribute
    public function getCategoryNameAttribute()
    {
        return $this->category ? ucfirst(str_replace('_', ' ', $this->category)) : 'General';
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
