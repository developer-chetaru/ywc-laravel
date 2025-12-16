<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VesselVerification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'yacht_id',
        'verification_method',
        'vessel_name',
        'imo_number',
        'mmsi_number',
        'flag_state',
        'role_on_vessel',
        'authority_description',
        'captain_license_path',
        'vessel_registration_path',
        'authorization_letter_path',
        'management_company_docs_path',
        'status',
        'rejection_reason',
        'verified_at',
        'expires_at',
        'verification_email',
        'email_verified',
        'email_verified_at',
        'verification_phone',
        'phone_verified',
        'phone_verified_at',
        'reviewed_by_user_id',
        'reviewer_notes',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
        'email_verified' => 'boolean',
        'email_verified_at' => 'datetime',
        'phone_verified' => 'boolean',
        'phone_verified_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function yacht()
    {
        return $this->belongsTo(Yacht::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Helper methods
    public function isVerified(): bool
    {
        return $this->status === 'verified' && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function markAsVerified(?int $reviewerId = null, ?string $notes = null): void
    {
        $this->update([
            'status' => 'verified',
            'verified_at' => now(),
            'reviewed_by_user_id' => $reviewerId,
            'reviewer_notes' => $notes,
            'expires_at' => now()->addYears(2), // Verifications expire after 2 years
        ]);
    }

    public function markAsRejected(?int $reviewerId = null, ?string $reason = null): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by_user_id' => $reviewerId,
        ]);
    }
}
