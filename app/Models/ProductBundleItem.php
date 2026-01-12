<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBundleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bundle_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    // Get the bundle relationship (now points to Product)
    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'bundle_id')
                    ->where('product_type', 4);
    }

    // Get the product relationship
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
