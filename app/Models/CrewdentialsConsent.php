<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrewdentialsConsent extends Model
{
    protected $fillable = [
        'user_id',
        'has_consented',
        'policy_version',
        'policy_url',
        'consented_at',
        'withdrawn_at',
        'withdrawal_reason',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'has_consented' => 'boolean',
        'consented_at' => 'datetime',
        'withdrawn_at' => 'datetime',
    ];

    /**
     * Get the user who gave consent
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user has active consent
     */
    public function isActive(): bool
    {
        return $this->has_consented && $this->withdrawn_at === null;
    }

    /**
     * Get active consent for a user
     */
    public static function getActiveConsent(int $userId): ?self
    {
        return self::where('user_id', $userId)
            ->where('has_consented', true)
            ->whereNull('withdrawn_at')
            ->latest('consented_at')
            ->first();
    }

    /**
     * Check if user has given consent
     */
    public static function hasConsent(int $userId): bool
    {
        return self::getActiveConsent($userId) !== null;
    }
}
