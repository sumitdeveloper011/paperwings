<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class CookiePreference extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'session_id',
        'user_id',
        'essential_cookies',
        'analytics_cookies',
        'marketing_cookies',
        'functionality_cookies',
        'preferences_saved_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'essential_cookies' => 'boolean',
        'analytics_cookies' => 'boolean',
        'marketing_cookies' => 'boolean',
        'functionality_cookies' => 'boolean',
        'preferences_saved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
