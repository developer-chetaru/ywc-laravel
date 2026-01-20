<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class VerificationAppeal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'verification_id',
        'document_id',
        'user_id',
        'reason',
        'disputed_fields',
        'supporting_evidence',
        'evidence_files',
        'status',
        'assigned_to',
        'priority',
        'reviewed_by',
        'review_notes',
        'resolution',
        'reviewed_at',
        'original_decision',
        'new_decision',
        'changes_made',
        'user_notified',
        'user_notified_at',
        'appeal_reference',
    ];

    protected $casts = [
        'disputed_fields' => 'array',
        'evidence_files' => 'array',
        'changes_made' => 'array',
        'user_notified' => 'boolean',
        'reviewed_at' => 'datetime',
        'user_notified_at' => 'datetime',
    ];

    /**
     * Boot method to generate appeal reference
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($appeal) {
            if (!$appeal->appeal_reference) {
                $appeal->appeal_reference = 'APL-' . strtoupper(Str::random(10));
            }
        });
    }

    /**
     * Relationships
     */
    public function verification()
    {
        return $this->belongsTo(DocumentVerification::class, 'verification_id');
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    public function scopeResolved($query)
    {
        return $query->whereIn('status', ['approved', 'rejected']);
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 1);
    }

    /**
     * Helper Methods
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isUnderReview(): bool
    {
        return $this->status === 'under_review';
    }

    public function isResolved(): bool
    {
        return in_array($this->status, ['approved', 'rejected']);
    }

    public function assign(int $userId): void
    {
        $this->update([
            'assigned_to' => $userId,
            'status' => 'under_review',
        ]);
    }

    public function approve(string $resolution, ?array $changesMade = null, ?int $reviewerId = null): void
    {
        $this->update([
            'status' => 'approved',
            'resolution' => $resolution,
            'changes_made' => $changesMade,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
        ]);

        $this->notifyUser();
    }

    public function reject(string $resolution, ?int $reviewerId = null): void
    {
        $this->update([
            'status' => 'rejected',
            'resolution' => $resolution,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
        ]);

        $this->notifyUser();
    }

    public function withdraw(): void
    {
        $this->update(['status' => 'withdrawn']);
    }

    public function notifyUser(): void
    {
        $this->update([
            'user_notified' => true,
            'user_notified_at' => now(),
        ]);

        // TODO: Send notification/email to user
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            1 => 'High',
            2 => 'Medium',
            3 => 'Low',
            default => 'Unknown',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            1 => 'red',
            2 => 'yellow',
            3 => 'green',
            default => 'gray',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'under_review' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            'withdrawn' => 'gray',
            default => 'gray',
        };
    }
}
