<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProductFaq extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'product_id',
        'question',
        'answer',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'status' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($productFaq) {
            if (empty($productFaq->uuid)) {
                $productFaq->uuid = Str::uuid();
            }
        });
    }

    // Get the product relationship
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Scope to filter active FAQs
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // Scope to order FAQs
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
