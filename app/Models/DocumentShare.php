<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DocumentShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'share_token',
        'token_hash',
        'password_hash',
        'recipient_email',
        'recipient_name',
        'personal_message',
        'expires_at',
        'is_active',
        'access_count',
        'download_count',
        'abuse_reports',
        'last_accessed_at',
        'ip_address',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Generate a unique share token (cryptographically secure)
     */
    public static function generateToken(): string
    {
        do {
            // Use cryptographically secure random bytes
            $token = bin2hex(random_bytes(32)); // 64 character hex string
        } while (self::where('share_token', $token)->exists());

        return $token;
    }

    /**
     * Hash token for secure storage
     */
    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * Verify token without timing attacks
     */
    public static function verifyToken(string $token, string $hash): bool
    {
        return hash_equals($hash, self::hashToken($token));
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'document_share_documents')
            ->withTimestamps();
    }

    /**
     * Check if share is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if share is valid (active and not expired)
     */
    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    /**
     * Increment access count and update last accessed
     */
    public function recordAccess(): void
    {
        $this->increment('access_count');
        $this->update([
            'last_accessed_at' => now(),
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Record download
     */
    public function recordDownload(): void
    {
        $this->increment('download_count');
        $this->update([
            'last_accessed_at' => now(),
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Report abuse
     */
    public function reportAbuse(): void
    {
        $this->increment('abuse_reports');
        
        // Auto-revoke if too many abuse reports
        if ($this->abuse_reports >= 3) {
            $this->update(['is_active' => false]);
        }
    }

    /**
     * Get share URL
     */
    public function getShareUrlAttribute(): string
    {
        return route('documents.share.view', ['token' => $this->share_token]);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('is_active', false)
              ->orWhere(function ($q2) {
                  $q2->whereNotNull('expires_at')
                     ->where('expires_at', '<=', now());
              });
        });
    }
}
