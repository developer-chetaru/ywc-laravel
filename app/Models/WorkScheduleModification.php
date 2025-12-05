<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkScheduleModification extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'modified_by',
        'modification_type',
        'changes_before',
        'changes_after',
        'reason_code',
        'reason_description',
        'hours_variance',
        'variance_type',
        'requires_approval',
        'is_approved',
        'approved_by',
        'approved_at',
        'metadata',
    ];

    protected $casts = [
        'changes_before' => 'array',
        'changes_after' => 'array',
        'hours_variance' => 'decimal:2',
        'requires_approval' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(WorkSchedule::class, 'schedule_id');
    }

    public function modifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approve(?int $approvedBy = null): void
    {
        $this->update([
            'is_approved' => true,
            'approved_by' => $approvedBy ?? auth()->id(),
            'approved_at' => now(),
        ]);
    }

    public function reject(?int $rejectedBy = null): void
    {
        $this->update([
            'is_approved' => false,
            'approved_by' => $rejectedBy ?? auth()->id(),
            'approved_at' => now(),
        ]);
    }

    public function getModificationTypeLabelAttribute(): string
    {
        return match($this->modification_type) {
            'time_adjustment' => 'Time Adjustment',
            'location_change' => 'Location Change',
            'work_type_change' => 'Work Type Change',
            'cancellation' => 'Cancellation',
            'extension' => 'Extension',
            'shortening' => 'Shortening',
            'other' => 'Other',
            default => $this->modification_type,
        };
    }

    public function getReasonCodeLabelAttribute(): string
    {
        return match($this->reason_code) {
            'weather_delay' => 'Weather Delay',
            'guest_request' => 'Guest Request',
            'maintenance_priority' => 'Maintenance Priority',
            'emergency' => 'Emergency',
            'crew_request' => 'Crew Request',
            'itinerary_change' => 'Itinerary Change',
            'other' => 'Other',
            default => $this->reason_code ?? 'Not specified',
        };
    }
}

