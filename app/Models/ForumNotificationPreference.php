<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumNotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'email_enabled',
        'on_site_enabled',
        'digest_mode',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'on_site_enabled' => 'boolean',
    ];

    /**
     * Get the user that owns the preference
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create default preferences for a user
     */
    public static function getOrCreateDefaults(User $user): array
    {
        $types = ['new_reply', 'new_thread', 'quote', 'reaction', 'best_answer', 'pm', 'moderation', 'mention'];
        $preferences = [];

        foreach ($types as $type) {
            $preference = self::firstOrCreate(
                ['user_id' => $user->id, 'type' => $type],
                [
                    'email_enabled' => true,
                    'on_site_enabled' => true,
                    'digest_mode' => 'none',
                ]
            );
            $preferences[$type] = $preference;
        }

        return $preferences;
    }
}
