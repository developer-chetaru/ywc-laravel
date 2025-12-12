<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MentalHealthHabit extends Model
{
    protected $table = 'mental_health_habits';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'frequency',
        'frequency_days',
        'category',
        'current_streak',
        'longest_streak',
        'last_completed_at',
        'is_active',
    ];

    protected $casts = [
        'frequency_days' => 'array',
        'last_completed_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tracking(): HasMany
    {
        return $this->hasMany(MentalHealthHabitTracking::class, 'habit_id');
    }
}
