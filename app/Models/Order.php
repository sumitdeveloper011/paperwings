<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasUuid;
use App\Helpers\CacheHelper;

class Order extends Model
{
    use HasFactory, HasUuid;

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
        'currency',
        'payment_confirmed_at',
        'stripe_customer_id',
        'stripe_payment_method_id',
        'stripe_payment_method_type',
        'stripe_receipt_url',
        'refund_amount',
        'refund_reason',
        'refunded_at',
        'payment_failure_reason',
        'dispute_status',
        'dispute_reason',
        'status',
        'tracking_id',
        'tracking_url',
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
        'refund_amount' => 'decimal:2',
        'payment_confirmed_at' => 'datetime',
        'refunded_at' => 'datetime',
        'admin_viewed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($order) {
            // Clear dashboard stats cache when order status or payment status changes
            if ($order->isDirty(['status', 'payment_status'])) {
                CacheHelper::clearDashboardStats();
            }
        });

        static::created(function ($order) {
            // Clear dashboard stats cache when new order is created
            CacheHelper::clearDashboardStats();
        });

        static::deleted(function ($order) {
            // Clear dashboard stats cache when order is deleted
            CacheHelper::clearDashboardStats();
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
