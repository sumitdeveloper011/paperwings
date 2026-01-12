<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'email',
        'password',
        'agree_terms',
        'status',
        'avatar',
        'phone',
        'bio',
        'two_factor_enabled',
        'provider',
        'provider_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Get the attributes that should be cast
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'integer',
            'agree_terms' => 'integer',
            'two_factor_enabled' => 'boolean',
        ];
    }

    // Get the user's full name attribute
    public function getNameAttribute(): string
    {
        $name = trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
        return $name ?: 'User';
    }

    // Get the avatar URL attribute
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }
        return asset('assets/images/profile.png');
    }

    // Boot function
    protected static function boot()
    {
        parent::boot();
    }

    // Get the route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Check if user is active
    public function isActive()
    {
        return $this->status === 1;
    }

    // Check if user is inactive
    public function isInactive()
    {
        return $this->status === 0;
    }

    // Send the email verification notification
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmailNotification);
    }

    // Get the email address that should be used for password reset
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    // Get the wishlist items relationship
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    // Get detail relationship
    public function detail(): HasOne
    {
        return $this->hasOne(UserDetail::class);
    }

    // Get user detail relationship
    public function userDetail(): HasOne
    {
        return $this->hasOne(UserDetail::class);
    }

    // Get addresses relationship
    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    // Get orders relationship
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Get default billing address relationship
    public function defaultBillingAddress(): HasOne
    {
        return $this->hasOne(UserAddress::class)->where('type', 'billing')->where('is_default', true);
    }

    // Get default shipping address relationship
    public function defaultShippingAddress(): HasOne
    {
        return $this->hasOne(UserAddress::class)->where('type', 'shipping')->where('is_default', true);
    }

    // Configure activity log options
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'email', 'phone', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "User {$eventName}");
    }
}
