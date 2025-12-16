<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Database\Factories\JobPostFactory;

class JobPost extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory()
    {
        return JobPostFactory::new();
    }

    protected $fillable = [
        'user_id',
        'yacht_id',
        'vessel_verification_id',
        'job_type',
        'temporary_work_type',
        'position_title',
        'department',
        'position_level',
        'vessel_type',
        'vessel_size',
        'flag',
        'program_type',
        'cruising_regions',
        'contract_type',
        'rotation_schedule',
        'start_date',
        'start_date_flexibility',
        'contract_duration_months',
        'work_start_date',
        'work_end_date',
        'work_start_time',
        'work_end_time',
        'total_hours',
        'urgency_level',
        'location',
        'latitude',
        'longitude',
        'berth_details',
        'salary_min',
        'salary_max',
        'salary_currency',
        'salary_negotiable',
        'day_rate_min',
        'day_rate_max',
        'hourly_rate',
        'benefits',
        'additional_benefits',
        'required_certifications',
        'preferred_certifications',
        'min_years_experience',
        'min_vessel_size_experience',
        'essential_skills',
        'preferred_skills',
        'required_languages',
        'preferred_languages',
        'other_requirements',
        'what_to_bring',
        'about_position',
        'about_vessel_program',
        'responsibilities',
        'ideal_candidate',
        'crew_size',
        'crew_atmosphere',
        'contact_name',
        'contact_phone',
        'whatsapp_available',
        'payment_method',
        'payment_timing',
        'cancellation_policy',
        'contact_preference',
        'response_timeline',
        'public_post',
        'allow_search_engine_index',
        'notify_matching_crew',
        'featured_posting',
        'status',
        'published_at',
        'filled_at',
        'expires_at',
        'views_count',
        'applications_count',
        'saved_count',
    ];

    protected $casts = [
        'vessel_size' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'day_rate_min' => 'decimal:2',
        'day_rate_max' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'benefits' => 'array',
        'required_certifications' => 'array',
        'preferred_certifications' => 'array',
        'essential_skills' => 'array',
        'preferred_skills' => 'array',
        'required_languages' => 'array',
        'preferred_languages' => 'array',
        'salary_negotiable' => 'boolean',
        'whatsapp_available' => 'boolean',
        'public_post' => 'boolean',
        'allow_search_engine_index' => 'boolean',
        'notify_matching_crew' => 'boolean',
        'featured_posting' => 'boolean',
        'start_date' => 'date',
        'work_start_date' => 'date',
        'work_end_date' => 'date',
        'work_start_time' => 'datetime',
        'work_end_time' => 'datetime',
        'published_at' => 'datetime',
        'filled_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function yacht(): BelongsTo
    {
        return $this->belongsTo(Yacht::class);
    }

    public function vesselVerification(): BelongsTo
    {
        return $this->belongsTo(VesselVerification::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function screeningQuestions(): HasMany
    {
        return $this->hasMany(JobPostScreeningQuestion::class);
    }

    public function savedBy(): HasMany
    {
        return $this->hasMany(SavedJobPost::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(JobMessage::class);
    }

    public function temporaryWorkBookings(): HasMany
    {
        return $this->hasMany(TemporaryWorkBooking::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(JobRating::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePermanent($query)
    {
        return $query->where('job_type', 'permanent');
    }

    public function scopeTemporary($query)
    {
        return $query->where('job_type', 'temporary');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'active')
            ->where('public_post', true)
            ->whereNotNull('published_at');
    }

    // Helper methods
    public function isPermanent(): bool
    {
        return $this->job_type === 'permanent';
    }

    public function isTemporary(): bool
    {
        return $this->job_type === 'temporary';
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementApplications(): void
    {
        $this->increment('applications_count');
    }

    public function publish(): void
    {
        $this->update([
            'status' => 'active',
            'published_at' => now(),
        ]);
    }

    public function markAsFilled(): void
    {
        $this->update([
            'status' => 'filled',
            'filled_at' => now(),
        ]);
    }

    protected static function booted(): void
    {
        static::saved(function (JobPost $jobPost) {
            if ($jobPost->wasChanged('status') && $jobPost->status === 'active' && $jobPost->notify_matching_crew && $jobPost->wasRecentlyCreated) {
                // Notify matching crew when job is published
                try {
                    app(\App\Services\JobBoard\JobNotificationService::class)->notifyMatchingCrew($jobPost);
                } catch (\Exception $e) {
                    // Log error but don't break the save
                    \Log::error('Error notifying matching crew: ' . $e->getMessage());
                }
            }
        });
    }
}
