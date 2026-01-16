<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class ContactMessage extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'image',
        'status',
        'admin_notes',
        'admin_viewed_at',
    ];

    protected $casts = [
        'admin_viewed_at' => 'datetime',
    ];


    // Scope to filter pending messages
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope to filter in progress messages
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    // Scope to filter solved messages
    public function scopeSolved($query)
    {
        return $query->where('status', 'solved');
    }

    // Scope to filter closed messages
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    // Get status badge attribute
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'in_progress' => '<span class="badge bg-info">In Progress</span>',
            'solved' => '<span class="badge bg-success">Solved</span>',
            'closed' => '<span class="badge bg-secondary">Closed</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
