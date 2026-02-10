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
        'restrict_to_email',
        'personal_message',
        'expires_at',
        'is_active',
        'access_count',
        'view_count',
        'download_count',
        'abuse_reports',
        'last_accessed_at',
        'ip_address',
        'qr_code_path',
        'can_download',
        'can_print',
        'can_share',
        'can_comment',
        'is_one_time',
        'max_views',
        'require_watermark',
        'access_start_date',
        'access_end_date',
        'share_type',
        'share_notes',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'is_active' => 'boolean',
        'can_download' => 'boolean',
        'can_print' => 'boolean',
        'can_share' => 'boolean',
        'can_comment' => 'boolean',
        'is_one_time' => 'boolean',
        'require_watermark' => 'boolean',
        'access_start_date' => 'datetime',
        'access_end_date' => 'datetime',
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
     * Check if share is accessible now (within access window)
     */
    public function isAccessible(?string $accessingEmail = null): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->isExpired()) {
            return false;
        }

        // Check email restriction - if restrict_to_email is set, only that email can access
        if ($this->restrict_to_email && $accessingEmail) {
            if (strtolower(trim($accessingEmail)) !== strtolower(trim($this->restrict_to_email))) {
                return false;
            }
        }

        // Check access window
        if ($this->access_start_date && now()->lt($this->access_start_date)) {
            return false;
        }

        if ($this->access_end_date && now()->gt($this->access_end_date)) {
            return false;
        }

        // Check max views
        if ($this->max_views && $this->view_count >= $this->max_views) {
            return false;
        }

        // Check one-time access
        if ($this->is_one_time && $this->view_count > 0) {
            return false;
        }

        return true;
    }

    /**
     * Check if password is required
     */
    public function requiresPassword(): bool
    {
        return !empty($this->password_hash);
    }

    /**
     * Verify password
     */
    public function verifyPassword(string $password): bool
    {
        if (empty($this->password_hash)) {
            return false;
        }
        return \Hash::check($password, $this->password_hash);
    }

    /**
     * Increment view count
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');

        // Deactivate if one-time or max views reached
        if ($this->is_one_time || ($this->max_views && $this->view_count >= $this->max_views)) {
            $this->update(['is_active' => false]);
        }
    }

    /**
     * Check if user can download
     */
    public function canDownload(): bool
    {
        return $this->can_download && $this->isAccessible();
    }

    /**
     * Check if user can print
     */
    public function canPrint(): bool
    {
        return $this->can_print && $this->isAccessible();
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
        // Check if max_views limit reached before incrementing
        if ($this->max_views && $this->view_count >= $this->max_views) {
            // Already at max, don't increment
            return;
        }
        
        $this->increment('access_count');
        $this->increment('view_count'); // Also increment view_count for display
        
        // Refresh to get updated view_count
        $this->refresh();
        
        // Deactivate if max views reached after increment
        if ($this->max_views && $this->view_count >= $this->max_views) {
            $this->update(['is_active' => false]);
        }
        
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
