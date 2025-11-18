<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkLogStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'period_start',
        'period_end',
        'period_type',
        'total_days_worked',
        'total_hours_worked',
        'average_hours_per_day',
        'total_overtime_hours',
        'total_rest_hours',
        'average_rest_per_day',
        'days_at_sea',
        'days_in_port',
        'days_on_leave',
        'days_in_yard',
        'days_shore_leave',
        'compliant_days',
        'warning_days',
        'violation_days',
        'compliance_percentage',
        'qualifying_sea_days',
        'qualifying_sea_hours',
        'weekly_work_hours',
        'weekly_rest_hours',
        'weekly_compliant',
        'metadata',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'total_hours_worked' => 'decimal:2',
        'average_hours_per_day' => 'decimal:2',
        'total_overtime_hours' => 'decimal:2',
        'total_rest_hours' => 'decimal:2',
        'average_rest_per_day' => 'decimal:2',
        'compliance_percentage' => 'decimal:2',
        'qualifying_sea_hours' => 'decimal:2',
        'weekly_work_hours' => 'decimal:2',
        'weekly_rest_hours' => 'decimal:2',
        'weekly_compliant' => 'boolean',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeWeekly($query)
    {
        return $query->where('period_type', 'weekly');
    }

    public function scopeMonthly($query)
    {
        return $query->where('period_type', 'monthly');
    }
}
