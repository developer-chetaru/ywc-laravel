<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentalHealthJournal extends Model
{
    protected $table = 'mental_health_journals';

    protected $fillable = [
        'user_id',
        'content',
        'mood_tag',
        'tags',
        'entry_type',
        'prompt_id',
        'is_shareable_with_therapist',
        'shared_with_therapist_id',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_shareable_with_therapist' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(MentalHealthTherapist::class, 'shared_with_therapist_id');
    }
}
