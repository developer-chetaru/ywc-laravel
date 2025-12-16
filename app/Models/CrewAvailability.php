<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Database\Factories\CrewAvailabilityFactory;

class CrewAvailability extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory()
    {
        return CrewAvailabilityFactory::new();
    }

    protected $fillable = [
        'user_id',
        'status',
        'available_from',
        'available_until',
        'notice_required',
        'day_work',
        'short_contracts',
        'medium_contracts',
        'emergency_cover',
        'long_term_seasonal',
        'available_positions',
        'day_rate_min',
        'day_rate_max',
        'half_day_rate',
        'emergency_rate',
        'weekly_contract_rate',
        'rates_negotiable',
        'blocked_dates',
        'current_location',
        'latitude',
        'longitude',
        'search_radius_km',
        'auto_update_location',
        'notify_same_day_urgent',
        'notify_24_hour_jobs',
        'notify_3_day_jobs',
        'notify_weekly_contracts',
        'alert_frequency',
        'quiet_hours_start',
        'quiet_hours_end',
        'profile_visibility',
        'show_ratings',
        'show_last_worked_date',
        'show_job_count',
        'show_response_time',
        'show_current_vessel',
        'show_full_experience',
        'allow_direct_booking',
        'total_jobs_completed',
        'average_rating',
        'completion_rate_percentage',
        'average_response_time_minutes',
        'repeat_hire_rate_percentage',
    ];

    protected $casts = [
        'available_from' => 'date',
        'available_until' => 'date',
        'available_positions' => 'array',
        'day_rate_min' => 'decimal:2',
        'day_rate_max' => 'decimal:2',
        'half_day_rate' => 'decimal:2',
        'emergency_rate' => 'decimal:2',
        'weekly_contract_rate' => 'decimal:2',
        'blocked_dates' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'auto_update_location' => 'boolean',
        'day_work' => 'boolean',
        'short_contracts' => 'boolean',
        'medium_contracts' => 'boolean',
        'emergency_cover' => 'boolean',
        'long_term_seasonal' => 'boolean',
        'rates_negotiable' => 'boolean',
        'notify_same_day_urgent' => 'boolean',
        'notify_24_hour_jobs' => 'boolean',
        'notify_3_day_jobs' => 'boolean',
        'notify_weekly_contracts' => 'boolean',
        'show_ratings' => 'boolean',
        'show_last_worked_date' => 'boolean',
        'show_job_count' => 'boolean',
        'show_response_time' => 'boolean',
        'show_current_vessel' => 'boolean',
        'show_full_experience' => 'boolean',
        'allow_direct_booking' => 'boolean',
        'average_rating' => 'decimal:2',
        'quiet_hours_start' => 'datetime',
        'quiet_hours_end' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeAvailableNow($query)
    {
        return $query->where('status', 'available_now');
    }

    public function scopeAvailableWithinRadius($query, float $latitude, float $longitude, int $radiusKm)
    {
        // This would need a more complex query for distance calculation
        // For now, simplified version
        return $query->whereNotNull('latitude')
            ->whereNotNull('longitude');
    }

    // Helper methods
    public function isAvailableNow(): bool
    {
        return $this->status === 'available_now' 
            && ($this->available_from === null || $this->available_from->isPast() || $this->available_from->isToday())
            && ($this->available_until === null || $this->available_until->isFuture() || $this->available_until->isToday());
    }

    public function setAvailableNow(): void
    {
        $this->update([
            'status' => 'available_now',
            'available_from' => now(),
        ]);
    }
}
