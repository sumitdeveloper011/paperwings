<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUuid;

class ProductAccordion extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'products_accordion';

    protected $fillable = [
        'uuid',
        'product_id',
        'eposnow_product_id',
        'heading',
        'content',
    ];


    // Get the product relationship
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

