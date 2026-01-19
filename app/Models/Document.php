<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'type', // Legacy enum - kept for backward compatibility
        'document_type_id', // New Phase 1 field
        'document_name',
        'document_number',
        'issuing_authority',
        'issuing_country',
        'notes',
        'tags',
        'featured_on_profile',
        'thumbnail_path',
        'file_path',
        'file_type',
        'file_size',
        'dob',
		'is_preview',
        'issue_date',
        'expiry_date',
        'version',
        'uploaded_by',
        'updated_by',
        'status',
        'ocr_status',
        'ocr_confidence',
        'ocr_data',
        'ocr_error',
        'verification_level_id',
        'highest_verification_level',
    ];

    protected $casts = [
        'tags' => 'array',
        'featured_on_profile' => 'boolean',
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'dob' => 'date',
        'ocr_data' => 'array',
        'ocr_confidence' => 'float',
        'highest_verification_level' => 'integer',
    ];

    /* 🔗 Relationships */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the document type (Phase 1)
     */
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Get expiry reminders for this document
     */
    public function expiryReminders()
    {
        return $this->hasMany(DocumentExpiryReminder::class);
    }

    /**
     * Get status change history for this document
     */
    public function statusChanges()
    {
        return $this->hasMany(DocumentStatusChange::class);
    }

    /**
     * Get all versions of this document
     */
    public function versions()
    {
        return $this->hasMany(DocumentVersion::class)->latest('version_number');
    }

    /**
     * Get the latest version
     */
    public function latestVersion()
    {
        return $this->hasOne(DocumentVersion::class)->latest('version_number');
    }

    public function passportDetail()
    {
        return $this->hasOne(PassportDetail::class);
    }

    public function idvisaDetail()
    {
        return $this->hasOne(IdvisaDetail::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function otherDocument()
    {
        return $this->hasOne(OtherDocument::class);
    }

    /**
     * Get the current verification level
     */
    public function verificationLevel()
    {
        return $this->belongsTo(VerificationLevel::class);
    }

    /**
     * Get all verifications for this document
     */
    public function verifications()
    {
        return $this->hasMany(DocumentVerification::class);
    }

    /**
     * Get the highest verification achieved
     */
    public function highestVerification()
    {
        return $this->hasOne(DocumentVerification::class)
            ->where('status', 'approved')
            ->orderBy('verification_level_id', 'desc')
            ->latest();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
  
  	/* 🔹 Helper functions for status */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if document is expiring soon (within 6 months)
     */
    public function isExpiringSoon(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        $sixMonthsFromNow = now()->addMonths(6);
        return $this->expiry_date->lte($sixMonthsFromNow) && $this->expiry_date->isFuture();
    }

    /**
     * Check if document is expired
     */
    public function isExpired(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->isPast();
    }

    /**
     * Get days until expiry (negative if expired)
     */
    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }

        return now()->diffInDays($this->expiry_date, false);
    }

    /**
     * Scope for expiring soon documents
     */
    public function scopeExpiringSoon($query)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addMonths(6))
            ->where('expiry_date', '>', now());
    }

    /**
     * Scope for expired documents
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now());
    }

    /**
     * Scope for expiring soon or expired
     */
    public function scopeExpiringSoonOrExpired($query)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addMonths(6));
    }

    /**
     * Get document shares
     */
    public function shares()
    {
        return $this->belongsToMany(DocumentShare::class, 'document_share_documents')
            ->withTimestamps();
    }
}
