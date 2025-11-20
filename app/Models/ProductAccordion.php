<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProductAccordion extends Model
{
    use HasFactory;

    protected $table = 'products_accordion';

    protected $fillable = [
        'uuid',
        'product_id',
        'eposnow_product_id',
        'heading',
        'content',
    ];

    // Boot method to generate UUID automatically
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($accordion) {
            if (empty($accordion->uuid)) {
                $accordion->uuid = Str::uuid();
            }
        });
    }

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

