<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MentalHealthTherapist extends Model
{
    use SoftDeletes;

    protected $table = 'mental_health_therapists';

    protected $fillable = [
        'user_id',
        'application_status',
        'rejection_reason',
        'biography',
        'profile_photo_path',
        'specializations',
        'languages_spoken',
        'therapeutic_approaches',
        'years_experience',
        'education_history',
        'certifications',
        'timezone',
        'license_numbers',
        'insurance_information',
        'base_hourly_rate',
        'session_type_pricing',
        'duration_pricing',
        'sliding_scale_available',
        'sliding_scale_options',
        'is_active',
        'is_featured',
        'rating',
        'total_sessions',
        'total_reviews',
        'session_completion_rate',
        'average_response_time_minutes',
        'no_show_rate',
        'continuing_education_hours',
        'professional_philosophy',
        'areas_of_focus',
    ];

    protected $casts = [
        'specializations' => 'array',
        'languages_spoken' => 'array',
        'therapeutic_approaches' => 'array',
        'education_history' => 'array',
        'certifications' => 'array',
        'license_numbers' => 'array',
        'session_type_pricing' => 'array',
        'duration_pricing' => 'array',
        'sliding_scale_options' => 'array',
        'areas_of_focus' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sliding_scale_available' => 'boolean',
        'rating' => 'decimal:2',
        'base_hourly_rate' => 'decimal:2',
        'session_completion_rate' => 'decimal:2',
        'no_show_rate' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function credentials(): HasMany
    {
        return $this->hasMany(MentalHealthTherapistCredential::class, 'therapist_id');
    }

    public function availability(): HasMany
    {
        return $this->hasMany(MentalHealthTherapistAvailability::class, 'therapist_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(MentalHealthSessionBooking::class, 'therapist_id');
    }

    public function references(): HasMany
    {
        return $this->hasMany(MentalHealthTherapistReference::class, 'therapist_id');
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(MentalHealthTherapistPayout::class, 'therapist_id');
    }

    public function isApproved(): bool
    {
        return $this->application_status === 'approved' && $this->is_active;
    }
}
