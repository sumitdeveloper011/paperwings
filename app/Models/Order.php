<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'order_number',
        'user_id',
        'session_id',
        'billing_first_name',
        'billing_last_name',
        'billing_email',
        'billing_phone',
        'billing_street_address',
        'billing_city',
        'billing_suburb',
        'billing_region_id',
        'billing_zip_code',
        'billing_country',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_email',
        'shipping_phone',
        'shipping_street_address',
        'shipping_city',
        'shipping_suburb',
        'shipping_region_id',
        'shipping_zip_code',
        'shipping_country',
        'subtotal',
        'discount',
        'coupon_code',
        'tax',
        'shipping',
        'shipping_price',
        'total',
        'payment_method',
        'payment_status',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'status',
        'notes',
        'admin_viewed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping' => 'decimal:2',
        'shipping_price' => 'decimal:2',
        'total' => 'decimal:2',
        'admin_viewed_at' => 'datetime',
    ];

    // Boot function to generate UUID automatically
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->uuid)) {
                $order->uuid = Str::uuid();
            }
        });
    }

    // Generate unique order number
    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    // Get the user relationship
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Get the order items relationship
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Get billing region relationship
    public function billingRegion(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'billing_region_id');
    }

    // Get shipping region relationship
    public function shippingRegion(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'shipping_region_id');
    }

    // Get full billing name attribute
    public function getBillingFullNameAttribute(): string
    {
        return trim($this->billing_first_name . ' ' . $this->billing_last_name);
    }

    // Get full shipping name attribute
    public function getShippingFullNameAttribute(): string
    {
        return trim($this->shipping_first_name . ' ' . $this->shipping_last_name);
    }

    // Get the route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
