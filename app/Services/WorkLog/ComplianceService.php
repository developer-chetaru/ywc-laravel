<?php

namespace App\Services\WorkLog;

use App\Models\WorkLog;
use Carbon\Carbon;

class ComplianceService
{
    // MLC 2006 Regulations
    const MAX_DAILY_WORK_HOURS = 14;
    const MIN_DAILY_REST_HOURS = 10;
    const MIN_CONTINUOUS_REST_HOURS = 6;
    const MAX_WEEKLY_WORK_HOURS = 72;
    const MIN_WEEKLY_REST_HOURS = 77;
    const MAX_REST_PERIODS_PER_DAY = 2;

    /**
     * Check daily compliance for a work log entry
     */
    public function checkDailyCompliance(WorkLog $workLog): array
    {
        $violations = [];
        $warnings = [];
        $isCompliant = true;

        // Check daily work hours limit
        if ($workLog->total_hours_worked > self::MAX_DAILY_WORK_HOURS) {
            $violations[] = [
                'type' => 'daily_work_limit',
                'message' => "Worked {$workLog->total_hours_worked} hours (max: " . self::MAX_DAILY_WORK_HOURS . " hours)",
                'exceeded_by' => $workLog->total_hours_worked - self::MAX_DAILY_WORK_HOURS,
            ];
            $isCompliant = false;
        } elseif ($workLog->total_hours_worked >= 12) {
            $warnings[] = [
                'type' => 'approaching_daily_limit',
                'message' => "Approaching daily limit ({$workLog->total_hours_worked}/" . self::MAX_DAILY_WORK_HOURS . " hours)",
                'remaining' => self::MAX_DAILY_WORK_HOURS - $workLog->total_hours_worked,
            ];
        }

        // Check daily rest hours minimum
        if ($workLog->total_rest_hours < self::MIN_DAILY_REST_HOURS) {
            $violations[] = [
                'type' => 'daily_rest_minimum',
                'message' => "Only {$workLog->total_rest_hours} hours rest (min: " . self::MIN_DAILY_REST_HOURS . " hours)",
                'deficit' => self::MIN_DAILY_REST_HOURS - $workLog->total_rest_hours,
            ];
            $isCompliant = false;
        } elseif ($workLog->total_rest_hours < 11) {
            $warnings[] = [
                'type' => 'approaching_rest_minimum',
                'message' => "Low rest hours ({$workLog->total_rest_hours}/" . self::MIN_DAILY_REST_HOURS . " hours)",
                'deficit' => self::MIN_DAILY_REST_HOURS - $workLog->total_rest_hours,
            ];
        }

        // Check rest periods
        $restPeriods = $workLog->restPeriods;
        if ($restPeriods->count() > self::MAX_REST_PERIODS_PER_DAY) {
            $violations[] = [
                'type' => 'rest_periods_limit',
                'message' => "Too many rest periods ({$restPeriods->count()}, max: " . self::MAX_REST_PERIODS_PER_DAY . ")",
            ];
            $isCompliant = false;
        }

        // Check for at least one 6-hour continuous rest period
        $hasLongRest = $restPeriods->contains(function ($period) {
            return $period->duration_hours >= self::MIN_CONTINUOUS_REST_HOURS;
        });

        if (!$hasLongRest && $restPeriods->count() > 0) {
            $violations[] = [
                'type' => 'continuous_rest_requirement',
                'message' => "No rest period of at least " . self::MIN_CONTINUOUS_REST_HOURS . " hours",
            ];
            $isCompliant = false;
        }

        $status = $isCompliant ? 'compliant' : ($warnings ? 'warning' : 'violation');

        return [
            'is_compliant' => $isCompliant,
            'status' => $status,
            'violations' => $violations,
            'warnings' => $warnings,
        ];
    }

    /**
     * Check weekly compliance for a user
     */
    public function checkWeeklyCompliance(int $userId, Carbon $weekStart): array
    {
        $weekEnd = $weekStart->copy()->addDays(6);
        
        $workLogs = WorkLog::forUser($userId)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->where('is_day_off', false)
            ->get();

        $totalWorkHours = $workLogs->sum('total_hours_worked');
        $totalRestHours = $workLogs->sum('total_rest_hours');
        $daysWorked = $workLogs->count();

        $violations = [];
        $warnings = [];
        $isCompliant = true;

        // Check weekly work hours limit
        if ($totalWorkHours > self::MAX_WEEKLY_WORK_HOURS) {
            $violations[] = [
                'type' => 'weekly_work_limit',
                'message' => "Worked {$totalWorkHours} hours this week (max: " . self::MAX_WEEKLY_WORK_HOURS . " hours)",
                'exceeded_by' => $totalWorkHours - self::MAX_WEEKLY_WORK_HOURS,
            ];
            $isCompliant = false;
        } elseif ($totalWorkHours >= 65) {
            $warnings[] = [
                'type' => 'approaching_weekly_limit',
                'message' => "Approaching weekly limit ({$totalWorkHours}/" . self::MAX_WEEKLY_WORK_HOURS . " hours)",
                'remaining' => self::MAX_WEEKLY_WORK_HOURS - $totalWorkHours,
            ];
        }

        // Check weekly rest hours minimum
        if ($totalRestHours < self::MIN_WEEKLY_REST_HOURS) {
            $violations[] = [
                'type' => 'weekly_rest_minimum',
                'message' => "Only {$totalRestHours} hours rest this week (min: " . self::MIN_WEEKLY_REST_HOURS . " hours)",
                'deficit' => self::MIN_WEEKLY_REST_HOURS - $totalRestHours,
            ];
            $isCompliant = false;
        }

        return [
            'is_compliant' => $isCompliant,
            'total_work_hours' => $totalWorkHours,
            'total_rest_hours' => $totalRestHours,
            'days_worked' => $daysWorked,
            'violations' => $violations,
            'warnings' => $warnings,
            'remaining_work_hours' => max(0, self::MAX_WEEKLY_WORK_HOURS - $totalWorkHours),
            'is_aggregate' => false, // This is per-user data
        ];
    }

    /**
     * Get compliance summary for a date range
     */
    public function getComplianceSummary(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        $workLogs = WorkLog::forUser($userId)
            ->whereBetween('work_date', [$startDate, $endDate])
            ->get();

        $totalDays = $workLogs->count();
        $compliantDays = $workLogs->where('is_compliant', true)->count();
        $warningDays = $workLogs->where('compliance_status', 'warning')->count();
        $violationDays = $workLogs->where('compliance_status', 'violation')->count();

        $compliancePercentage = $totalDays > 0 
            ? round(($compliantDays / $totalDays) * 100, 2) 
            : 100;

        return [
            'total_days' => $totalDays,
            'compliant_days' => $compliantDays,
            'warning_days' => $warningDays,
            'violation_days' => $violationDays,
            'compliance_percentage' => $compliancePercentage,
            'total_hours_worked' => $workLogs->sum('total_hours_worked'),
            'total_rest_hours' => $workLogs->sum('total_rest_hours'),
            'average_hours_per_day' => $totalDays > 0 
                ? round($workLogs->sum('total_hours_worked') / $totalDays, 2) 
                : 0,
        ];
    }

    /**
     * Calculate sea service time
     */
    public function calculateSeaService(int $userId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = WorkLog::forUser($userId)->qualifyingSeaService();

        if ($startDate) {
            $query->where('work_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('work_date', '<=', $endDate);
        }

        $workLogs = $query->get();

        $totalDays = $workLogs->count();
        $totalHours = $workLogs->sum('total_hours_worked');

        // Calculate months and years
        $firstDay = $workLogs->min('work_date');
        $lastDay = $workLogs->max('work_date');

        if ($firstDay && $lastDay) {
            $years = $firstDay->diffInYears($lastDay);
            $months = $firstDay->copy()->addYears($years)->diffInMonths($lastDay);
            $days = $firstDay->copy()->addYears($years)->addMonths($months)->diffInDays($lastDay);
        } else {
            $years = 0;
            $months = 0;
            $days = 0;
        }

        return [
            'total_days' => $totalDays,
            'total_hours' => $totalHours,
            'years' => $years,
            'months' => $months,
            'days' => $days,
            'first_day' => $firstDay?->format('Y-m-d'),
            'last_day' => $lastDay?->format('Y-m-d'),
        ];
    }
}

