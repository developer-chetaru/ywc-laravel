<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentalHealthTherapistAvailability extends Model
{
    protected $table = 'mental_health_therapist_availability';

    protected $fillable = [
        'therapist_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_recurring',
        'specific_date',
        'is_blocked',
        'block_reason',
        'session_durations',
        'buffer_minutes',
        'max_daily_sessions',
        'max_weekly_sessions',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'specific_date' => 'date',
        'is_recurring' => 'boolean',
        'is_blocked' => 'boolean',
        'is_active' => 'boolean',
        'session_durations' => 'array',
    ];

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(MentalHealthTherapist::class, 'therapist_id');
    }
}
