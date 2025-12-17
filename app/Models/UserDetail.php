<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'gender',
        'bio',
        'image',
        'date_of_birth',
        'country',
        'city',
        'state',
        'zip_code',
        'about',
        'website',
        'occupation',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get the user that owns the detail.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the image URL.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }
        return asset('assets/images/placeholder.jpg');
    }
}
