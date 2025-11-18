<?php

namespace App\Livewire\WorkLog;

use Livewire\Component;
use App\Models\WorkLog;
use App\Models\WorkLogRestPeriod;
use App\Models\Yacht;
use App\Services\WorkLog\ComplianceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class WorkLogIndex extends Component
{
    // Form fields
    public $selectedDate;
    public $workLogId = null;
    public $startTime;
    public $endTime;
    public $totalHoursWorked = 0;
    public $overtimeHours = 0;
    public $breakMinutes = 0;
    public $totalRestHours = 0;
    public $sleepHours = 0;
    public $restUninterrupted = true;
    public $locationStatus = 'at_sea';
    public $locationName = '';
    public $portName = '';
    public $latitude = null;
    public $longitude = null;
    public $yachtId = null;
    public $yachtName = '';
    public $yachtType = '';
    public $yachtLength = '';
    public $yachtFlag = '';
    public $positionRank = '';
    public $department = '';
    public $weatherConditions = '';
    public $seaState = '';
    public $visibility = '';
    public $activities = [];
    public $activityInput = '';
    public $notes = '';
    public $isDayOff = false;
    
    // Rest periods
    public $restPeriods = [];
    public $showRestPeriodForm = false;
    public $editingRestPeriodIndex = null;
    public $restPeriodStart = '';
    public $restPeriodEnd = '';
    public $restPeriodType = 'night_sleep';
    public $restPeriodLocation = '';
    public $restPeriodNotes = '';
    
    // View state
    public $viewMode = 'dashboard'; // dashboard, entry, history, statistics
    public $dateFilter = 'week'; // today, week, month, custom
    public $customStartDate = '';
    public $customEndDate = '';
    
    // Statistics
    public $complianceSummary = [];
    public $weeklyCompliance = [];
    
    protected ComplianceService $complianceService;

    public function boot()
    {
        $this->complianceService = app(ComplianceService::class);
    }

    public $isEditing = false; // Track if we're editing an existing entry
    
    public function mount()
    {
        $this->selectedDate = now()->format('Y-m-d');
        // Don't auto-load entry on mount - show blank form
        $this->resetForm();
        $this->loadComplianceData();
    }

    public function loadTodayEntry()
    {
        $query = WorkLog::where('work_date', $this->selectedDate);
        
        // Super admin can see all, regular users see only their own
        if (!Auth::user()->hasRole('super_admin')) {
            $query->where('user_id', Auth::id());
        }
        
        $workLog = $query->first();

        if ($workLog) {
            $this->isEditing = true;
            $this->workLogId = $workLog->id;
            $this->startTime = $workLog->start_time?->format('H:i');
            $this->endTime = $workLog->end_time?->format('H:i');
            $this->totalHoursWorked = $workLog->total_hours_worked;
            $this->overtimeHours = $workLog->overtime_hours;
            $this->breakMinutes = $workLog->break_minutes;
            $this->totalRestHours = $workLog->total_rest_hours;
            $this->sleepHours = $workLog->sleep_hours;
            $this->restUninterrupted = $workLog->rest_uninterrupted;
            $this->locationStatus = $workLog->location_status;
            $this->locationName = $workLog->location_name ?? '';
            $this->portName = $workLog->port_name ?? '';
            $this->latitude = $workLog->latitude;
            $this->longitude = $workLog->longitude;
            $this->yachtName = $workLog->yacht_name ?? '';
            $this->yachtType = $workLog->yacht_type ?? '';
            $this->yachtLength = $workLog->yacht_length ?? '';
            $this->yachtFlag = $workLog->yacht_flag ?? '';
            $this->positionRank = $workLog->position_rank ?? '';
            $this->department = $workLog->department ?? '';
            
            // Try to find yacht by name
            if ($this->yachtName) {
                $yacht = Yacht::where('name', $this->yachtName)->first();
                if ($yacht) {
                    $this->yachtId = $yacht->id;
                }
            }
            $this->weatherConditions = $workLog->weather_conditions ?? '';
            $this->seaState = $workLog->sea_state ?? '';
            $this->visibility = $workLog->visibility ?? '';
            $this->activities = $workLog->activities ?? [];
            $this->notes = $workLog->notes ?? '';
            $this->isDayOff = $workLog->is_day_off;
            
            $this->restPeriods = $workLog->restPeriods->map(function ($period) {
                return [
                    'id' => $period->id,
                    'start_time' => $period->start_time->format('H:i'),
                    'end_time' => $period->end_time->format('H:i'),
                    'duration_hours' => $period->duration_hours,
                    'type' => $period->type,
                    'is_uninterrupted' => $period->is_uninterrupted,
                    'location' => $period->location ?? '',
                    'notes' => $period->notes ?? '',
                ];
            })->toArray();
        } else {
            $this->resetForm();
        }
    }

    public function resetForm()
    {
        $this->workLogId = null;
        $this->isEditing = false;
        $this->startTime = '';
        $this->endTime = '';
        $this->totalHoursWorked = 0;
        $this->overtimeHours = 0;
        $this->breakMinutes = 0;
        $this->totalRestHours = 0;
        $this->sleepHours = 0;
        $this->restUninterrupted = true;
        $this->locationStatus = 'at_sea';
        $this->locationName = '';
        $this->portName = '';
        $this->latitude = null;
        $this->longitude = null;
        $this->yachtId = null;
        $this->yachtName = '';
        $this->yachtType = '';
        $this->yachtLength = '';
        $this->yachtFlag = '';
        $this->positionRank = '';
        $this->department = '';
        $this->weatherConditions = '';
        $this->seaState = '';
        $this->visibility = '';
        $this->activities = [];
        $this->activityInput = '';
        $this->notes = '';
        $this->isDayOff = false;
        $this->restPeriods = [];
    }

    public function calculateHours()
    {
        if ($this->startTime && $this->endTime) {
            try {
                // Parse times - handle both "HH:MM" and "HH:MM:SS" formats
                $startParts = explode(':', $this->startTime);
                $endParts = explode(':', $this->endTime);
                
                $startHour = (int)($startParts[0] ?? 0);
                $startMin = (int)($startParts[1] ?? 0);
                $endHour = (int)($endParts[0] ?? 0);
                $endMin = (int)($endParts[1] ?? 0);
                
                // Create Carbon instances for today
                $start = Carbon::today()->setTime($startHour, $startMin, 0);
                $end = Carbon::today()->setTime($endHour, $endMin, 0);
                
                // If end time is before or equal to start time, assume it's the next day
                if ($end->lessThanOrEqualTo($start)) {
                    $end->addDay();
                }
                
                // Calculate total minutes worked (subtract break time)
                $totalMinutes = $end->diffInMinutes($start) - ($this->breakMinutes ?? 0);
                
                // Ensure we don't get negative values
                if ($totalMinutes < 0) {
                    $totalMinutes = 0;
                }
                
                $this->totalHoursWorked = round($totalMinutes / 60, 2);
                
                // Calculate overtime (hours over 8)
                if ($this->totalHoursWorked > 8) {
                    $this->overtimeHours = round($this->totalHoursWorked - 8, 2);
                } else {
                    $this->overtimeHours = 0;
                }
            } catch (\Exception $e) {
                // If calculation fails, set to 0
                $this->totalHoursWorked = 0;
                $this->overtimeHours = 0;
            }
        }
    }
    
    public function updatedStartTime()
    {
        $this->calculateHours();
    }
    
    public function updatedEndTime()
    {
        $this->calculateHours();
    }
    
    public function updatedBreakMinutes()
    {
        $this->calculateHours();
    }
    
    public function updatedYachtId()
    {
        if ($this->yachtId) {
            $yacht = Yacht::find($this->yachtId);
            if ($yacht) {
                $this->yachtName = $yacht->name;
                $this->yachtType = $yacht->type ?? '';
                $this->yachtLength = $yacht->length_meters ? $yacht->length_meters . 'm' : ($yacht->length_feet ? $yacht->length_feet . 'ft' : '');
                $this->yachtFlag = $yacht->flag_registry ?? '';
            }
        } else {
            $this->yachtName = '';
            $this->yachtType = '';
            $this->yachtLength = '';
            $this->yachtFlag = '';
        }
    }

    public function addActivity()
    {
        if (!empty($this->activityInput)) {
            $this->activities[] = $this->activityInput;
            $this->activityInput = '';
        }
    }

    public function removeActivity($index)
    {
        unset($this->activities[$index]);
        $this->activities = array_values($this->activities);
    }

    public function addRestPeriod()
    {
        if ($this->restPeriodStart && $this->restPeriodEnd) {
            $start = Carbon::parse($this->restPeriodStart);
            $end = Carbon::parse($this->restPeriodEnd);
            
            if ($end->lessThan($start)) {
                $end->addDay();
            }
            
            $duration = round($end->diffInHours($start), 2);
            
            $period = [
                'start_time' => $this->restPeriodStart,
                'end_time' => $this->restPeriodEnd,
                'duration_hours' => $duration,
                'type' => $this->restPeriodType,
                'is_uninterrupted' => true,
                'location' => $this->restPeriodLocation,
                'notes' => $this->restPeriodNotes,
            ];
            
            if ($this->editingRestPeriodIndex !== null) {
                $this->restPeriods[$this->editingRestPeriodIndex] = $period;
                $this->editingRestPeriodIndex = null;
            } else {
                $this->restPeriods[] = $period;
            }
            
            $this->calculateTotalRestHours();
            $this->resetRestPeriodForm();
        }
    }

    public function calculateTotalRestHours()
    {
        $this->totalRestHours = array_sum(array_column($this->restPeriods, 'duration_hours'));
    }

    public function editRestPeriod($index)
    {
        $period = $this->restPeriods[$index];
        $this->restPeriodStart = $period['start_time'];
        $this->restPeriodEnd = $period['end_time'];
        $this->restPeriodType = $period['type'];
        $this->restPeriodLocation = $period['location'] ?? '';
        $this->restPeriodNotes = $period['notes'] ?? '';
        $this->editingRestPeriodIndex = $index;
        $this->showRestPeriodForm = true;
    }

    public function removeRestPeriod($index)
    {
        unset($this->restPeriods[$index]);
        $this->restPeriods = array_values($this->restPeriods);
        $this->calculateTotalRestHours();
    }

    public function resetRestPeriodForm()
    {
        $this->restPeriodStart = '';
        $this->restPeriodEnd = '';
        $this->restPeriodType = 'night_sleep';
        $this->restPeriodLocation = '';
        $this->restPeriodNotes = '';
        $this->showRestPeriodForm = false;
        $this->editingRestPeriodIndex = null;
    }

    public function save()
    {
        $this->validate([
            'selectedDate' => 'required|date',
            'totalHoursWorked' => 'required|numeric|min:0|max:24',
            'totalRestHours' => 'required|numeric|min:0|max:24',
        ]);

        $compliance = $this->complianceService->checkDailyCompliance(
            new WorkLog([
                'total_hours_worked' => $this->totalHoursWorked,
                'total_rest_hours' => $this->totalRestHours,
                'restPeriods' => collect($this->restPeriods)->map(function ($p) {
                    return new WorkLogRestPeriod($p);
                }),
            ])
        );

        // Super admin can edit any user's log, but regular users can only edit their own
        $userId = Auth::id();
        if (Auth::user()->hasRole('super_admin') && $this->workLogId) {
            $existingLog = WorkLog::find($this->workLogId);
            if ($existingLog) {
                $userId = $existingLog->user_id;
            }
        }
        
        $workLog = WorkLog::updateOrCreate(
            [
                'user_id' => $userId,
                'work_date' => $this->selectedDate,
            ],
            [
                'start_time' => $this->startTime ? $this->startTime . ':00' : null,
                'end_time' => $this->endTime ? $this->endTime . ':00' : null,
                'total_hours_worked' => $this->totalHoursWorked,
                'overtime_hours' => $this->overtimeHours,
                'break_minutes' => $this->breakMinutes,
                'total_rest_hours' => $this->totalRestHours,
                'sleep_hours' => $this->sleepHours,
                'rest_uninterrupted' => $this->restUninterrupted,
                'location_status' => $this->locationStatus,
                'location_name' => $this->locationName,
                'port_name' => $this->portName,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'yacht_name' => $this->yachtName,
                'yacht_type' => $this->yachtType,
                'yacht_length' => $this->yachtLength,
                'yacht_flag' => $this->yachtFlag,
                'position_rank' => $this->positionRank,
                'department' => $this->department,
                'weather_conditions' => $this->weatherConditions,
                'sea_state' => $this->seaState,
                'visibility' => $this->visibility,
                'activities' => $this->activities,
                'notes' => $this->notes,
                'is_day_off' => $this->isDayOff,
                'is_compliant' => $compliance['is_compliant'],
                'compliance_status' => $compliance['status'],
                'compliance_notes' => json_encode($compliance),
                'is_at_sea' => $this->locationStatus === 'at_sea',
                'counts_towards_sea_service' => $this->locationStatus === 'at_sea' && !$this->isDayOff,
            ]
        );

        // Save rest periods
        $workLog->restPeriods()->delete();
        foreach ($this->restPeriods as $period) {
            WorkLogRestPeriod::create([
                'work_log_id' => $workLog->id,
                'start_time' => $period['start_time'],
                'end_time' => $period['end_time'],
                'duration_hours' => $period['duration_hours'],
                'type' => $period['type'],
                'is_uninterrupted' => $period['is_uninterrupted'] ?? true,
                'location' => $period['location'] ?? null,
                'notes' => $period['notes'] ?? null,
            ]);
        }

        $this->workLogId = $workLog->id;
        $this->loadComplianceData();
        
        session()->flash('message', 'Work log saved successfully!');
        
        // Reset form after saving (clear for next entry)
        $savedDate = $this->selectedDate;
        $this->resetForm();
        $this->selectedDate = $savedDate;
        $this->isEditing = false;
        
        // Show success message
        $this->dispatch('entry-saved');
    }
    
    public function getYachtsProperty()
    {
        return Yacht::orderBy('name')->get();
    }
    
    public function getHistoryEntriesProperty()
    {
        $query = WorkLog::query();
        
        // Super admin can see all, regular users see only their own
        if (!Auth::user()->hasRole('super_admin')) {
            $query->where('user_id', Auth::id());
        }
        
        $startDate = match($this->dateFilter) {
            'today' => now(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'custom' => $this->customStartDate ? Carbon::parse($this->customStartDate) : now()->startOfMonth(),
            default => now()->startOfWeek(),
        };

        $endDate = match($this->dateFilter) {
            'today' => now(),
            'week' => now()->endOfWeek(),
            'month' => now()->endOfMonth(),
            'custom' => $this->customEndDate ? Carbon::parse($this->customEndDate) : now()->endOfMonth(),
            default => now()->endOfWeek(),
        };
        
        return $query->with('user:id,first_name,last_name,email')
            ->whereBetween('work_date', [$startDate, $endDate])
            ->orderBy('work_date', 'desc')
            ->get();
    }
    
    public function getStatisticsDataProperty()
    {
        $query = WorkLog::query();
        
        // Super admin can see all, regular users see only their own
        if (!Auth::user()->hasRole('super_admin')) {
            $query->where('user_id', Auth::id());
        }
        
        $startDate = match($this->dateFilter) {
            'today' => now(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'custom' => $this->customStartDate ? Carbon::parse($this->customStartDate) : now()->startOfMonth(),
            default => now()->startOfMonth(),
        };

        $endDate = match($this->dateFilter) {
            'today' => now(),
            'week' => now()->endOfWeek(),
            'month' => now()->endOfMonth(),
            'custom' => $this->customEndDate ? Carbon::parse($this->customEndDate) : now()->endOfMonth(),
            default => now()->endOfMonth(),
        };
        
        $workLogs = $query->whereBetween('work_date', [$startDate, $endDate])->get();
        
        return [
            'total_days' => $workLogs->count(),
            'total_hours_worked' => $workLogs->sum('total_hours_worked'),
            'total_rest_hours' => $workLogs->sum('total_rest_hours'),
            'average_hours_per_day' => $workLogs->count() > 0 ? round($workLogs->sum('total_hours_worked') / $workLogs->count(), 2) : 0,
            'days_at_sea' => $workLogs->where('location_status', 'at_sea')->count(),
            'days_in_port' => $workLogs->where('location_status', 'in_port')->count(),
            'days_on_leave' => $workLogs->where('location_status', 'on_leave')->count(),
            'compliant_days' => $workLogs->where('is_compliant', true)->count(),
            'violation_days' => $workLogs->where('compliance_status', 'violation')->count(),
        ];
    }

    public function loadComplianceData()
    {
        $startDate = match($this->dateFilter) {
            'today' => now(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'custom' => $this->customStartDate ? Carbon::parse($this->customStartDate) : now()->startOfMonth(),
            default => now()->startOfWeek(),
        };

        $endDate = match($this->dateFilter) {
            'today' => now(),
            'week' => now()->endOfWeek(),
            'month' => now()->endOfMonth(),
            'custom' => $this->customEndDate ? Carbon::parse($this->customEndDate) : now()->endOfMonth(),
            default => now()->endOfWeek(),
        };

        // For super admin, we'll show aggregated data for all users
        // For regular users, show only their own data
        $userId = Auth::user()->hasRole('super_admin') ? null : Auth::id();

        if ($userId) {
            $this->complianceSummary = $this->complianceService->getComplianceSummary(
                $userId,
                $startDate,
                $endDate
            );

            $this->weeklyCompliance = $this->complianceService->checkWeeklyCompliance(
                $userId,
                now()->startOfWeek()
            );
        } else {
            // For super admin, calculate aggregate statistics (but weekly compliance should be per user)
            $workLogs = WorkLog::whereBetween('work_date', [$startDate, $endDate])->get();
            $totalDays = $workLogs->count();
            $compliantDays = $workLogs->where('is_compliant', true)->count();
            $warningDays = $workLogs->where('compliance_status', 'warning')->count();
            $violationDays = $workLogs->where('compliance_status', 'violation')->count();
            
            $this->complianceSummary = [
                'total_days' => $totalDays,
                'compliant_days' => $compliantDays,
                'warning_days' => $warningDays,
                'violation_days' => $violationDays,
                'compliance_percentage' => $totalDays > 0 ? round(($compliantDays / $totalDays) * 100, 2) : 100,
                'total_hours_worked' => $workLogs->sum('total_hours_worked'),
                'total_rest_hours' => $workLogs->sum('total_rest_hours'),
                'average_hours_per_day' => $totalDays > 0 ? round($workLogs->sum('total_hours_worked') / $totalDays, 2) : 0,
            ];
            
            // For super admin, show average weekly compliance or hide remaining hours
            // Weekly limit is per user, not aggregate
            $weekLogs = WorkLog::whereBetween('work_date', [now()->startOfWeek(), now()->endOfWeek()])->get();
            $weekWorkHours = $weekLogs->sum('total_hours_worked');
            $weekRestHours = $weekLogs->sum('total_rest_hours');
            
            $this->weeklyCompliance = [
                'is_compliant' => true, // Can't determine aggregate compliance
                'total_work_hours' => $weekWorkHours,
                'total_rest_hours' => $weekRestHours,
                'days_worked' => $weekLogs->count(),
                'remaining_work_hours' => null, // Not applicable for aggregate view
                'is_aggregate' => true, // Flag to indicate this is aggregate data
            ];
        }
    }

    public function updatedDateFilter()
    {
        $this->loadComplianceData();
        // Force refresh of computed properties
        $this->dispatch('$refresh');
    }
    
    public function updatedCustomStartDate()
    {
        $this->loadComplianceData();
    }
    
    public function updatedCustomEndDate()
    {
        $this->loadComplianceData();
    }

    public function updatedSelectedDate()
    {
        // Only load entry if we're in editing mode, otherwise show blank form
        if ($this->isEditing) {
            $this->loadTodayEntry();
        } else {
            $this->resetForm();
        }
    }
    
    public function updatedViewMode()
    {
        // When switching to entry mode, reset form unless we're editing
        if ($this->viewMode === 'entry' && !$this->isEditing) {
            $this->resetForm();
            $this->selectedDate = now()->format('Y-m-d');
        }
    }
    
    public function newEntry()
    {
        $this->viewMode = 'entry';
        $this->isEditing = false;
        $this->selectedDate = now()->format('Y-m-d');
        $this->resetForm();
    }
    
    public function loadEntryForDate($date)
    {
        $this->selectedDate = $date;
        $this->isEditing = true;
        $this->viewMode = 'entry';
        $this->loadTodayEntry();
    }

    public function getRecentEntriesProperty()
    {
        $query = WorkLog::query();
        
        // Super admin can see all, regular users see only their own
        if (!Auth::user()->hasRole('super_admin')) {
            $query->where('user_id', Auth::id());
        }
        
        return $query->with('user:id,first_name,last_name,email')
            ->orderBy('work_date', 'desc')
            ->limit(10)
            ->get();
    }

    public function getChartDataProperty()
    {
        $query = WorkLog::where('work_date', '>=', now()->subDays(7));
        
        // Super admin can see all, regular users see only their own
        if (!Auth::user()->hasRole('super_admin')) {
            $query->where('user_id', Auth::id());
        }
        
        $entries = $query->orderBy('work_date')->get();

        return [
            'dates' => $entries->pluck('work_date')->map(fn($d) => $d->format('M d'))->toArray(),
            'work_hours' => $entries->pluck('total_hours_worked')->toArray(),
            'rest_hours' => $entries->pluck('total_rest_hours')->toArray(),
        ];
    }

    public function render()
    {
        return view('livewire.work-log.work-log-index', [
            'recentEntries' => $this->recentEntries,
            'chartData' => $this->chartData,
            'yachts' => $this->yachts,
            'historyEntries' => $this->historyEntries,
            'statisticsData' => $this->statisticsData,
        ])->layout('layouts.app');
    }
}
