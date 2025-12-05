<?php

namespace App\Livewire\WorkLog;

use Livewire\Component;
use App\Models\WorkSchedule;
use App\Models\WorkLog;
use App\Models\User;
use App\Models\Yacht;
use App\Services\WorkLog\ComplianceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CaptainDashboard extends Component
{
    use AuthorizesRequests;

    public $selectedDate;
    public $dateRange = 'week';
    public $customStartDate;
    public $customEndDate;
    public $filterUserId = null;
    public $filterDepartment = '';
    public $filterStatus = 'all';

    protected ComplianceService $complianceService;

    public function boot()
    {
        $this->complianceService = app(ComplianceService::class);
        
        // Only captains and super admins can access
        if (!Auth::user()->hasRole(['captain', 'super_admin'])) {
            abort(403, 'Only captains can access this dashboard');
        }
    }

    public function mount()
    {
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function getCrewSchedulesProperty()
    {
        $startDate = match($this->dateRange) {
            'today' => now()->toDateString(),
            'week' => now()->startOfWeek()->toDateString(),
            'month' => now()->startOfMonth()->toDateString(),
            'custom' => $this->customStartDate ?: now()->startOfMonth()->toDateString(),
            default => now()->startOfWeek()->toDateString(),
        };

        $endDate = match($this->dateRange) {
            'today' => now()->toDateString(),
            'week' => now()->endOfWeek()->toDateString(),
            'month' => now()->endOfMonth()->toDateString(),
            'custom' => $this->customEndDate ?: now()->endOfMonth()->toDateString(),
            default => now()->endOfWeek()->toDateString(),
        };

        $query = WorkSchedule::with(['user', 'yacht'])
            ->whereBetween('schedule_date', [$startDate, $endDate]);

        if ($this->filterUserId) {
            $query->where('user_id', $this->filterUserId);
        }

        if ($this->filterDepartment) {
            $query->where('department', $this->filterDepartment);
        }

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        return $query->orderBy('schedule_date')->orderBy('start_time')->get();
    }

    public function getCrewWorkLogsProperty()
    {
        $startDate = match($this->dateRange) {
            'today' => now()->toDateString(),
            'week' => now()->startOfWeek()->toDateString(),
            'month' => now()->startOfMonth()->toDateString(),
            'custom' => $this->customStartDate ?: now()->startOfMonth()->toDateString(),
            default => now()->startOfWeek()->toDateString(),
        };

        $endDate = match($this->dateRange) {
            'today' => now()->toDateString(),
            'week' => now()->endOfWeek()->toDateString(),
            'month' => now()->endOfMonth()->toDateString(),
            'custom' => $this->customEndDate ?: now()->endOfMonth()->toDateString(),
            default => now()->endOfWeek()->toDateString(),
        };

        $query = WorkLog::with('user')
            ->whereBetween('work_date', [$startDate, $endDate]);

        if ($this->filterUserId) {
            $query->where('user_id', $this->filterUserId);
        }

        if ($this->filterDepartment) {
            $query->where('department', $this->filterDepartment);
        }

        return $query->orderBy('work_date', 'desc')->get();
    }

    public function getCrewMembersProperty()
    {
        return User::whereHas('roles', function($q) {
            $q->whereIn('name', ['crew', 'captain']);
        })->orderBy('first_name')->orderBy('last_name')->get();
    }

    public function getComplianceSummaryProperty()
    {
        $startDate = match($this->dateRange) {
            'today' => now(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'custom' => $this->customStartDate ? Carbon::parse($this->customStartDate) : now()->startOfMonth(),
            default => now()->startOfWeek(),
        };

        $endDate = match($this->dateRange) {
            'today' => now(),
            'week' => now()->endOfWeek(),
            'month' => now()->endOfMonth(),
            'custom' => $this->customEndDate ? Carbon::parse($this->customEndDate) : now()->endOfMonth(),
            default => now()->endOfWeek(),
        };

        $workLogs = WorkLog::whereBetween('work_date', [$startDate, $endDate])->get();

        $crewCompliance = [];
        foreach ($this->crewMembers as $crew) {
            $crewLogs = $workLogs->where('user_id', $crew->id);
            $weeklyCompliance = $this->complianceService->checkWeeklyCompliance($crew->id, now()->startOfWeek());
            
            $crewCompliance[] = [
                'user' => $crew,
                'total_hours' => $crewLogs->sum('total_hours_worked'),
                'total_days' => $crewLogs->count(),
                'compliant_days' => $crewLogs->where('is_compliant', true)->count(),
                'warning_days' => $crewLogs->where('compliance_status', 'warning')->count(),
                'violation_days' => $crewLogs->where('compliance_status', 'violation')->count(),
                'weekly_compliance' => $weeklyCompliance,
                'pending_schedules' => WorkSchedule::where('user_id', $crew->id)
                    ->where('status', 'pending')
                    ->where('schedule_date', '>=', now()->toDateString())
                    ->count(),
            ];
        }

        return $crewCompliance;
    }

    public function getTodayOverviewProperty()
    {
        $today = now()->toDateString();
        
        $schedules = WorkSchedule::where('schedule_date', $today)
            ->with('user')
            ->get();
        
        $workLogs = WorkLog::where('work_date', $today)
            ->with('user')
            ->get();

        return [
            'total_schedules' => $schedules->count(),
            'pending_schedules' => $schedules->where('status', 'pending')->count(),
            'confirmed_schedules' => $schedules->where('status', 'confirmed')->count(),
            'total_logs' => $workLogs->count(),
            'compliant_logs' => $workLogs->where('is_compliant', true)->count(),
            'warning_logs' => $workLogs->where('compliance_status', 'warning')->count(),
            'violation_logs' => $workLogs->where('compliance_status', 'violation')->count(),
            'crew_on_duty' => $workLogs->where('total_hours_worked', '>', 0)->pluck('user_id')->unique()->count(),
        ];
    }

    public function render()
    {
        return view('livewire.work-log.captain-dashboard', [
            'crewSchedules' => $this->crewSchedules,
            'crewWorkLogs' => $this->crewWorkLogs,
            'crewMembers' => $this->crewMembers,
            'complianceSummary' => $this->complianceSummary,
            'todayOverview' => $this->todayOverview,
        ])->layout('layouts.app');
    }
}

