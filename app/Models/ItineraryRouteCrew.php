<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItineraryRouteCrew extends Model
{
    use HasFactory;

    protected $table = 'itinerary_route_crew';

    protected $fillable = [
        'route_id',
        'user_id',
        'email',
        'role',
        'status',
        'notify_on_updates',
        'permissions',
        'invited_at',
        'responded_at',
    ];

    protected $casts = [
        'permissions' => 'array',
        'notify_on_updates' => 'boolean',
        'invited_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(ItineraryRoute::class, 'route_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAccepted(): void
    {
        $this->forceFill([
            'status' => 'accepted',
            'responded_at' => now(),
        ])->save();
    }

    public function markDeclined(): void
    {
        $this->forceFill([
            'status' => 'declined',
            'responded_at' => now(),
        ])->save();
    }
}

