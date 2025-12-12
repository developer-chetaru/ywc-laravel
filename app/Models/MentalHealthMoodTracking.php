<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentalHealthMoodTracking extends Model
{
    protected $table = 'mental_health_mood_tracking';

    protected $fillable = [
        'user_id',
        'tracked_date',
        'mood_rating',
        'primary_mood',
        'secondary_emotions',
        'energy_level',
        'sleep_quality',
        'stress_level',
        'physical_symptoms',
        'medications',
        'trigger_notes',
        'tracked_time',
        'is_quick_checkin',
    ];

    protected $casts = [
        'tracked_date' => 'date',
        'tracked_time' => 'datetime',
        'secondary_emotions' => 'array',
        'physical_symptoms' => 'array',
        'medications' => 'array',
        'is_quick_checkin' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
