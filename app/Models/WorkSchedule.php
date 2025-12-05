<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'yacht_id',
        'schedule_date',
        'start_time',
        'end_time',
        'break_minutes',
        'planned_hours',
        'location_status',
        'location_name',
        'work_type',
        'department',
        'status',
        'is_confirmed',
        'confirmed_at',
        'template_id',
        'created_by',
        'created_by_role',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'break_minutes' => 'integer',
        'planned_hours' => 'decimal:2',
        'is_confirmed' => 'boolean',
        'confirmed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function yacht(): BelongsTo
    {
        return $this->belongsTo(Yacht::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(WorkScheduleTemplate::class, 'template_id');
    }

    public function modifications(): HasMany
    {
        return $this->hasMany(WorkScheduleModification::class, 'schedule_id');
    }

    public function workLog(): BelongsTo
    {
        return $this->belongsTo(WorkLog::class, 'id', 'schedule_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed')->where('is_confirmed', true);
    }

    public function scopeModified($query)
    {
        return $query->where('status', 'modified');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('schedule_date', $date);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('schedule_date', '>=', now()->toDateString());
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'modified' => 'Modified',
            'cancelled' => 'Cancelled',
            default => $this->status,
        };
    }

    public function getWorkTypeLabelAttribute(): string
    {
        return match($this->work_type) {
            'regular_duties' => 'Regular Duties',
            'maintenance' => 'Maintenance',
            'guest_service' => 'Guest Service',
            'emergency_standby' => 'Emergency Standby',
            'shore_leave' => 'Shore Leave',
            'rest_period' => 'Rest Period',
            default => $this->work_type,
        };
    }

    public function confirm(): void
    {
        $this->update([
            'status' => 'confirmed',
            'is_confirmed' => true,
            'confirmed_at' => now(),
        ]);
    }

    public function markAsModified(): void
    {
        $this->update([
            'status' => 'modified',
        ]);
    }
}

