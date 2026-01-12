<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUuid;

class ProductFaq extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'product_id',
        'category_id',
        'faqs', // JSON column
    ];

    protected $casts = [
        'faqs' => 'array', // Auto JSON encode/decode
    ];


    // Get the product relationship
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Get the category relationship
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Scope to filter active FAQs (check if any FAQ in JSON is active)
    public function scopeActive($query)
    {
        return $query->whereJsonContains('faqs', [['status' => true]]);
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
