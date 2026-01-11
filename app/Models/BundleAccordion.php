<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BundleAccordion extends Model
{
    use HasFactory;

    protected $table = 'bundle_accordions';

    protected $fillable = [
        'uuid',
        'bundle_id',
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

    // Get the bundle relationship
    public function bundle(): BelongsTo
    {
        return $this->belongsTo(ProductBundle::class);
    }
}
