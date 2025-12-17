<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'first_name',
        'last_name',
        'phone',
        'email',
        'street_address',
        'street_address_2',
        'suburb',
        'city',
        'region_id',
        'zip_code',
        'country',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the user that owns the address.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the region for this address.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get the full address as a formatted string.
     */
    public function getFullAddressAttribute(): string
    {
        $address = $this->street_address;
        if ($this->street_address_2) {
            $address .= ', ' . $this->street_address_2;
        }
        if ($this->suburb) {
            $address .= ', ' . $this->suburb;
        }
        $address .= ', ' . $this->city;
        if ($this->region) {
            $address .= ', ' . $this->region->name;
        }
        $address .= ' ' . $this->zip_code;
        $address .= ', ' . ($this->country ?? 'New Zealand');
        return $address;
    }

    /**
     * Scope to get default addresses.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to get addresses by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
