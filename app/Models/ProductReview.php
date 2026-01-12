<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUuid;

class ProductReview extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'product_id',
        'user_id',
        'name',
        'email',
        'rating',
        'review',
        'status',
        'verified_purchase',
        'helpful_count',
    ];

    protected $casts = [
        'rating' => 'integer',
        'status' => 'integer',
        'verified_purchase' => 'boolean',
        'helpful_count' => 'integer',
    ];


    // Get the product relationship
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Get the user relationship
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scope to filter approved reviews
    public function scopeApproved($query)
    {
        return $query->where('status', 1);
    }

    // Scope to filter pending reviews
    public function scopePending($query)
    {
        return $query->where('status', 0);
    }

    // Scope to filter rejected reviews
    public function scopeRejected($query)
    {
        return $query->where('status', 2);
    }

    // Get reviewer name attribute
    public function getReviewerNameAttribute()
    {
        return $this->user ? $this->user->name : $this->name;
    }

    // Get status badge attribute
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            0 => '<span class="badge bg-warning">Pending</span>',
            1 => '<span class="badge bg-success">Approved</span>',
            2 => '<span class="badge bg-danger">Rejected</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
