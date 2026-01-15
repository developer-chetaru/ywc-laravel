<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProfileShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'share_token',
        'recipient_email',
        'recipient_name',
        'personal_message',
        'shared_sections',
        'document_categories',
        'career_entry_ids',
        'expires_at',
        'is_active',
        'view_count',
        'download_count',
        'last_accessed_at',
        'ip_address',
        'qr_code_path',
    ];

    protected $casts = [
        'shared_sections' => 'array',
        'document_categories' => 'array',
        'career_entry_ids' => 'array',
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Generate a unique share token
     */
    public static function generateToken(): string
    {
        do {
            $token = Str::random(64);
        } while (self::where('share_token', $token)->exists());

        return $token;
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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
     * Increment view count and update last accessed
     */
    public function recordView(): void
    {
        $this->increment('view_count');
        $this->update([
            'last_accessed_at' => now(),
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Increment download count
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
     * Get share URL
     */
    public function getShareUrlAttribute(): string
    {
        return route('profile.share.view', ['token' => $this->share_token]);
    }

    /**
     * Check if a section is shared
     */
    public function hasSection(string $section): bool
    {
        return in_array($section, $this->shared_sections ?? []);
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
