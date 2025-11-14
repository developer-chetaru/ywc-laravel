<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rally extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizer_id',
        'title',
        'description',
        'type',
        'privacy',
        'start_date',
        'end_date',
        'location_name',
        'latitude',
        'longitude',
        'address',
        'meeting_point',
        'max_participants',
        'cost',
        'what_to_bring',
        'requirements',
        'contact_info',
        'status',
        'views',
        'rating',
        'total_ratings',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'cost' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(RallyAttendee::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(RallyComment::class);
    }

    public function goingAttendees(): HasMany
    {
        return $this->hasMany(RallyAttendee::class)->where('rsvp_status', 'going');
    }

    public function maybeAttendees(): HasMany
    {
        return $this->hasMany(RallyAttendee::class)->where('rsvp_status', 'maybe');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isPublic(): bool
    {
        return $this->privacy === 'public';
    }

    public function isPrivate(): bool
    {
        return $this->privacy === 'private';
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
