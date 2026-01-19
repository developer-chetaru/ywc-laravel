<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'verification_level_id',
        'verifier_id',
        'verifier_type',
        'status',
        'notes',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * Get the document being verified
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the verification level
     */
    public function verificationLevel()
    {
        return $this->belongsTo(VerificationLevel::class);
    }

    /**
     * Get the verifier (user who verified)
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }

    /**
     * Scope for pending verifications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved verifications
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected verifications
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
