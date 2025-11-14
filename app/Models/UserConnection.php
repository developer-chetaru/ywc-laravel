<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'connected_user_id',
        'status',
        'request_message',
        'category',
        'tags',
        'connection_strength',
        'connected_at',
        'last_interaction_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'connected_at' => 'datetime',
        'last_interaction_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function connectedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'connected_user_id');
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }
}
