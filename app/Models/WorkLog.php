<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'work_date',
        'start_time',
        'end_time',
        'total_hours_worked',
        'overtime_hours',
        'break_minutes',
        'total_rest_hours',
        'rest_uninterrupted',
        'sleep_hours',
        'location_status',
        'location_name',
        'port_name',
        'latitude',
        'longitude',
        'yacht_name',
        'yacht_type',
        'yacht_length',
        'yacht_flag',
        'position_rank',
        'department',
        'captain_name',
        'company_name',
        'weather_conditions',
        'sea_state',
        'visibility',
        'activities',
        'notes',
        'comments',
        'is_compliant',
        'compliance_status',
        'compliance_notes',
        'counts_towards_sea_service',
        'is_at_sea',
        'metadata',
        'is_day_off',
        'is_verified',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'work_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_hours_worked' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'total_rest_hours' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'activities' => 'array',
        'metadata' => 'array',
        'rest_uninterrupted' => 'boolean',
        'is_compliant' => 'boolean',
        'counts_towards_sea_service' => 'boolean',
        'is_at_sea' => 'boolean',
        'is_day_off' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function restPeriods(): HasMany
    {
        return $this->hasMany(WorkLogRestPeriod::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeCompliant($query)
    {
        return $query->where('is_compliant', true);
    }

    public function scopeWithWarnings($query)
    {
        return $query->where('compliance_status', 'warning');
    }

    public function scopeViolations($query)
    {
        return $query->where('compliance_status', 'violation');
    }

    public function scopeAtSea($query)
    {
        return $query->where('location_status', 'at_sea')->where('is_at_sea', true);
    }

    public function scopeQualifyingSeaService($query)
    {
        return $query->where('counts_towards_sea_service', true)
            ->where('is_at_sea', true)
            ->where('location_status', 'at_sea');
    }

    public function getLocationStatusLabelAttribute(): string
    {
        return match($this->location_status) {
            'at_sea' => 'At Sea ⚓',
            'in_port' => 'In Port 🏖️',
            'in_yard' => 'In Yard/Dry Dock 🔧',
            'on_leave' => 'On Leave 🏝️',
            'shore_leave' => 'Shore Leave 🌆',
            default => $this->location_status,
        };
    }

    public function getComplianceStatusColorAttribute(): string
    {
        return match($this->compliance_status) {
            'compliant' => 'green',
            'warning' => 'yellow',
            'violation' => 'red',
            default => 'gray',
        };
    }
}
