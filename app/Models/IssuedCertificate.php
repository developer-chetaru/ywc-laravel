<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class IssuedCertificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_id',
        'user_id',
        'certification_id',
        'certificate_number',
        'certificate_name',
        'description',
        'issue_date',
        'expiry_date',
        'certificate_file_path',
        'status',
        'revocation_reason',
        'revoked_at',
        'revoked_by',
        'verification_data',
        'issued_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'revoked_at' => 'datetime',
        'verification_data' => 'array',
    ];

    /**
     * Get the training provider
     */
    public function provider()
    {
        return $this->belongsTo(TrainingProvider::class, 'provider_id');
    }

    /**
     * Get the user (certificate holder)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the certification type
     */
    public function certification()
    {
        return $this->belongsTo(TrainingCertification::class, 'certification_id');
    }

    /**
     * Get the user who issued this certificate
     */
    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Get the user who revoked this certificate
     */
    public function revokedBy()
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    /**
     * Check if certificate is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' 
            && (!$this->expiry_date || $this->expiry_date->isFuture());
    }

    /**
     * Check if certificate is expired
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if certificate is expiring soon (within 30 days)
     */
    public function isExpiringSoon(): bool
    {
        return $this->expiry_date 
            && $this->expiry_date->isFuture() 
            && $this->expiry_date->lte(now()->addDays(30));
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }

        if ($this->expiry_date->isPast()) {
            return 0;
        }

        return now()->diffInDays($this->expiry_date);
    }

    /**
     * Generate unique certificate number
     */
    public static function generateCertificateNumber(int $providerId): string
    {
        $prefix = 'YWC-CERT';
        $year = now()->year;
        $random = strtoupper(substr(md5(uniqid()), 0, 8));
        
        return "{$prefix}-{$providerId}-{$year}-{$random}";
    }

    /**
     * Revoke certificate
     */
    public function revoke(string $reason, int $revokedBy): void
    {
        $this->update([
            'status' => 'revoked',
            'revocation_reason' => $reason,
            'revoked_at' => now(),
            'revoked_by' => $revokedBy,
        ]);
    }

    /**
     * Reactivate certificate
     */
    public function reactivate(): void
    {
        $this->update([
            'status' => 'active',
            'revocation_reason' => null,
            'revoked_at' => null,
            'revoked_by' => null,
        ]);
    }

    /**
     * Scope for active certificates
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>', now());
            });
    }

    /**
     * Scope for expiring soon
     */
    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->where('status', 'active')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays($days));
    }

    /**
     * Scope for provider
     */
    public function scopeForProvider($query, int $providerId)
    {
        return $query->where('provider_id', $providerId);
    }
}
