<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_post_id',
        'user_id',
        'status',
        'match_score',
        'screening_responses',
        'cover_message',
        'attached_documents',
        'submitted_at',
        'viewed_at',
        'view_duration_seconds',
        'reviewed_at',
        'shortlisted_at',
        'interview_requested_at',
        'interview_scheduled_at',
        'interviewed_at',
        'offer_sent_at',
        'offer_responded_at',
        'declined_at',
        'withdrawn_at',
        'hired_at',
        'captain_rating',
        'captain_notes',
        'folder',
        'notify_on_view',
        'notify_on_status_change',
        'notify_on_message',
        'withdrawal_reason',
        'decline_feedback',
    ];

    protected $casts = [
        'match_score' => 'decimal:2',
        'screening_responses' => 'array',
        'attached_documents' => 'array',
        'submitted_at' => 'datetime',
        'viewed_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'shortlisted_at' => 'datetime',
        'interview_requested_at' => 'datetime',
        'interview_scheduled_at' => 'datetime',
        'interviewed_at' => 'datetime',
        'offer_sent_at' => 'datetime',
        'offer_responded_at' => 'datetime',
        'declined_at' => 'datetime',
        'withdrawn_at' => 'datetime',
        'hired_at' => 'datetime',
        'notify_on_view' => 'boolean',
        'notify_on_status_change' => 'boolean',
        'notify_on_message' => 'boolean',
    ];

    // Relationships
    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(JobMessage::class);
    }

    // Scopes
    public function scopeNew($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeShortlisted($query)
    {
        return $query->where('status', 'shortlisted');
    }

    public function scopeInterviewStage($query)
    {
        return $query->whereIn('status', ['interview_requested', 'interview_scheduled', 'interviewed']);
    }

    // Helper methods
    public function markAsViewed(?int $durationSeconds = null): void
    {
        $this->update([
            'status' => 'viewed',
            'viewed_at' => now(),
            'view_duration_seconds' => $durationSeconds,
        ]);
    }

    public function markAsReviewed(): void
    {
        $this->update([
            'status' => 'reviewed',
            'reviewed_at' => now(),
        ]);
    }

    public function shortlist(): void
    {
        $this->update([
            'status' => 'shortlisted',
            'shortlisted_at' => now(),
        ]);
    }

    public function withdraw(?string $reason = null): void
    {
        $this->update([
            'status' => 'withdrawn',
            'withdrawn_at' => now(),
            'withdrawal_reason' => $reason,
        ]);
    }
}
