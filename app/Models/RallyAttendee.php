<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RallyAttendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'rally_id',
        'user_id',
        'rsvp_status',
        'guests_count',
        'comment',
        'checked_in',
        'checked_in_at',
    ];

    protected $casts = [
        'checked_in' => 'boolean',
        'checked_in_at' => 'datetime',
    ];

    public function rally(): BelongsTo
    {
        return $this->belongsTo(Rally::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isGoing(): bool
    {
        return $this->rsvp_status === 'going';
    }

    public function isMaybe(): bool
    {
        return $this->rsvp_status === 'maybe';
    }

    public function isInterested(): bool
    {
        return $this->rsvp_status === 'interested';
    }

    public function checkIn(): void
    {
        $this->update([
            'checked_in' => true,
            'checked_in_at' => now(),
        ]);
    }
}
