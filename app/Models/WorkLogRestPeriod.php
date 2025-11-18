<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkLogRestPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_log_id',
        'start_time',
        'end_time',
        'duration_hours',
        'type',
        'is_uninterrupted',
        'location',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'duration_hours' => 'decimal:2',
        'is_uninterrupted' => 'boolean',
    ];

    public function workLog(): BelongsTo
    {
        return $this->belongsTo(WorkLog::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'night_sleep' => 'Night Sleep',
            'afternoon_nap' => 'Afternoon Nap',
            'lunch_break' => 'Lunch Break',
            'coffee_break' => 'Coffee Break',
            'other' => 'Other',
            default => $this->type,
        };
    }
}
