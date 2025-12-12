<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentalHealthSession extends Model
{
    protected $table = 'mental_health_sessions';

    protected $fillable = [
        'booking_id',
        'session_type',
        'started_at',
        'ended_at',
        'duration_minutes',
        'status',
        'session_notes',
        'client_summary',
        'homework_assigned',
        'topics_discussed',
        'is_recorded',
        'recording_path',
        'recording_deleted_at',
        'client_feedback',
        'client_rating',
        'therapist_feedback',
        'chat_messages',
        'video_room_id',
        'voice_call_id',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'recording_deleted_at' => 'datetime',
        'is_recorded' => 'boolean',
        'homework_assigned' => 'array',
        'topics_discussed' => 'array',
        'chat_messages' => 'array',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(MentalHealthSessionBooking::class, 'booking_id');
    }
}
