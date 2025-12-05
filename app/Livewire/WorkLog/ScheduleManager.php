<?php

namespace App\Livewire\WorkLog;

use Livewire\Component;
use App\Models\WorkSchedule;
use App\Models\WorkScheduleTemplate;
use App\Models\WorkScheduleModification;
use App\Models\WorkLog;
use App\Models\Yacht;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ScheduleManager extends Component
{
    use AuthorizesRequests;

    // View state
    public $viewMode = 'list'; // list, create, edit, templates
    public $selectedDate;
    public $dateRange = 'week'; // today, week, month, custom
    public $customStartDate;
    public $customEndDate;

    // Schedule form
    public $scheduleId = null;
    public $userId;
    public $yachtId;
    public $scheduleDate;
    public $startTime;
    public $endTime;
    public $breakMinutes = 0;
    public $locationStatus = 'in_port';
    public $locationName = '';
    public $workType = 'regular_duties';
    public $department = '';
    public $notes = '';
    public $templateId = null;

    // Template form
    public $templateForm = [
        'name' => '',
        'description' => '',
        'category' => 'custom',
        'default_start_time' => '',
        'default_end_time' => '',
        'default_break_minutes' => 0,
        'default_location_status' => 'in_port',
        'default_work_type' => 'regular_duties',
        'default_department' => '',
    ];

    // Confirmation
    public $confirmationScheduleId = null;
    public $showConfirmationModal = false;

    // Modification
    public $modificationScheduleId = null;
    public $showModificationModal = false;
    public $modificationForm = [
        'start_time' => '',
        'end_time' => '',
        'break_minutes' => 0,
        'location_status' => '',
        'work_type' => '',
        'reason_code' => '',
        'reason_description' => '',
    ];

    // Filters
    public $filterUserId = null;
    public $filterStatus = 'all';
    public $filterDepartment = '';

    public function mount()
    {
        $this->selectedDate = now()->format('Y-m-d');
        $this->scheduleDate = now()->format('Y-m-d');
        
        // Set default user (current user or first available)
        if (Auth::user()->hasRole(['captain', 'super_admin'])) {
            // Captain can assign to any user
            $this->userId = Auth::id();
        } else {
            // Crew can only create for themselves
            $this->userId = Auth::id();
        }
    }

    public function getSchedulesProperty()
    {
        $query = WorkSchedule::with(['user', 'yacht', 'creator']);

        // Date filtering
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

        $query->whereBetween('schedule_date', [$startDate, $endDate]);

        // Permission filtering
        if (!Auth::user()->hasRole(['captain', 'super_admin'])) {
            // Regular crew can only see their own schedules
            $query->where('user_id', Auth::id());
        } elseif ($this->filterUserId) {
            // Captain can filter by user
            $query->where('user_id', $this->filterUserId);
        }

        // Status filter
        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        // Department filter
        if ($this->filterDepartment) {
            $query->where('department', $this->filterDepartment);
        }

        return $query->orderBy('schedule_date')->orderBy('start_time')->get();
    }

    public function getTemplatesProperty()
    {
        $query = WorkScheduleTemplate::with('creator')->active();

        // If not super admin, show only yacht-specific or public templates
        if (!Auth::user()->hasRole('super_admin')) {
            $query->where(function($q) {
                $q->where('yacht_id', $this->yachtId)
                  ->orWhere('is_public', true);
            });
        }

        return $query->orderBy('name')->get();
    }

    public function getUsersProperty()
    {
        // Only captains and super admins can see all users
        if (Auth::user()->hasRole(['captain', 'super_admin'])) {
            return User::orderBy('first_name')->orderBy('last_name')->get();
        }
        return collect([Auth::user()]);
    }

    public function getYachtsProperty()
    {
        return Yacht::orderBy('name')->get();
    }

    public function createSchedule()
    {
        $this->viewMode = 'create';
        $this->resetScheduleForm();
        $this->scheduleDate = $this->selectedDate;
    }

    public function editSchedule($scheduleId)
    {
        $schedule = WorkSchedule::findOrFail($scheduleId);
        
        // Authorization check
        if (!Auth::user()->hasRole(['captain', 'super_admin']) && $schedule->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $this->scheduleId = $schedule->id;
        $this->userId = $schedule->user_id;
        $this->yachtId = $schedule->yacht_id;
        $this->scheduleDate = $schedule->schedule_date->format('Y-m-d');
        $this->startTime = $schedule->start_time ? $schedule->start_time->format('H:i') : '';
        $this->endTime = $schedule->end_time ? $schedule->end_time->format('H:i') : '';
        $this->breakMinutes = $schedule->break_minutes;
        $this->locationStatus = $schedule->location_status;
        $this->locationName = $schedule->location_name ?? '';
        $this->workType = $schedule->work_type;
        $this->department = $schedule->department ?? '';
        $this->notes = $schedule->notes ?? '';
        $this->templateId = $schedule->template_id;
        
        $this->viewMode = 'edit';
    }

    public function saveSchedule()
    {
        $this->validate([
            'userId' => 'required|exists:users,id',
            'scheduleDate' => 'required|date',
            'startTime' => 'required',
            'endTime' => 'required',
            'locationStatus' => 'required|in:in_port,at_sea,in_shipyard,at_anchor',
            'workType' => 'required',
        ]);

        // Calculate planned hours
        $start = Carbon::parse($this->scheduleDate . ' ' . $this->startTime);
        $end = Carbon::parse($this->scheduleDate . ' ' . $this->endTime);
        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }
        $plannedHours = round(($end->diffInMinutes($start) - $this->breakMinutes) / 60, 2);

        // Authorization check
        if (!Auth::user()->hasRole(['captain', 'super_admin']) && $this->userId !== Auth::id()) {
            abort(403, 'You can only create schedules for yourself');
        }

        $data = [
            'user_id' => $this->userId,
            'yacht_id' => $this->yachtId,
            'schedule_date' => $this->scheduleDate,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'break_minutes' => $this->breakMinutes,
            'planned_hours' => $plannedHours,
            'location_status' => $this->locationStatus,
            'location_name' => $this->locationName,
            'work_type' => $this->workType,
            'department' => $this->department,
            'notes' => $this->notes,
            'template_id' => $this->templateId,
            'created_by' => Auth::id(),
            'created_by_role' => Auth::user()->hasRole('captain') ? 'captain' : 'crew',
            'status' => 'pending',
        ];

        if ($this->scheduleId) {
            $schedule = WorkSchedule::findOrFail($this->scheduleId);
            $schedule->update($data);
            session()->flash('message', 'Schedule updated successfully!');
        } else {
            $schedule = WorkSchedule::create($data);
            session()->flash('message', 'Schedule created successfully!');
        }

        $this->viewMode = 'list';
        $this->resetScheduleForm();
    }

    public function deleteSchedule($scheduleId)
    {
        $schedule = WorkSchedule::findOrFail($scheduleId);
        
        // Authorization check
        if (!Auth::user()->hasRole(['captain', 'super_admin']) && $schedule->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $schedule->delete();
        session()->flash('message', 'Schedule deleted successfully!');
    }

    public function confirmSchedule($scheduleId)
    {
        $schedule = WorkSchedule::findOrFail($scheduleId);
        
        // Only the schedule owner can confirm
        if ($schedule->user_id !== Auth::id()) {
            abort(403, 'You can only confirm your own schedules');
        }

        $schedule->confirm();

        // Create or update work log from schedule
        $this->createWorkLogFromSchedule($schedule);

        session()->flash('message', 'Schedule confirmed and logged!');
        $this->showConfirmationModal = false;
    }

    public function quickConfirm($scheduleId)
    {
        $this->confirmSchedule($scheduleId);
    }

    public function modifySchedule($scheduleId)
    {
        $schedule = WorkSchedule::findOrFail($scheduleId);
        
        // Only the schedule owner can modify
        if ($schedule->user_id !== Auth::id()) {
            abort(403, 'You can only modify your own schedules');
        }

        $this->modificationScheduleId = $scheduleId;
        $this->modificationForm = [
            'start_time' => $schedule->start_time ? $schedule->start_time->format('H:i') : '',
            'end_time' => $schedule->end_time ? $schedule->end_time->format('H:i') : '',
            'break_minutes' => $schedule->break_minutes,
            'location_status' => $schedule->location_status,
            'work_type' => $schedule->work_type,
            'reason_code' => '',
            'reason_description' => '',
        ];
        $this->showModificationModal = true;
    }

    public function saveModification()
    {
        $this->validate([
            'modificationForm.start_time' => 'required',
            'modificationForm.end_time' => 'required',
            'modificationForm.reason_code' => 'required',
        ]);

        $schedule = WorkSchedule::findOrFail($this->modificationScheduleId);
        
        // Calculate variance
        $originalHours = $schedule->planned_hours;
        $start = Carbon::parse($schedule->schedule_date->format('Y-m-d') . ' ' . $this->modificationForm['start_time']);
        $end = Carbon::parse($schedule->schedule_date->format('Y-m-d') . ' ' . $this->modificationForm['end_time']);
        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }
        $newHours = round(($end->diffInMinutes($start) - $this->modificationForm['break_minutes']) / 60, 2);
        $variance = $newHours - $originalHours;

        // Track modification
        $modification = WorkScheduleModification::create([
            'schedule_id' => $schedule->id,
            'modified_by' => Auth::id(),
            'modification_type' => $variance > 0 ? 'extension' : ($variance < 0 ? 'shortening' : 'time_adjustment'),
            'changes_before' => [
                'start_time' => $schedule->start_time?->format('H:i'),
                'end_time' => $schedule->end_time?->format('H:i'),
                'break_minutes' => $schedule->break_minutes,
                'planned_hours' => $schedule->planned_hours,
            ],
            'changes_after' => [
                'start_time' => $this->modificationForm['start_time'],
                'end_time' => $this->modificationForm['end_time'],
                'break_minutes' => $this->modificationForm['break_minutes'],
                'planned_hours' => $newHours,
            ],
            'reason_code' => $this->modificationForm['reason_code'],
            'reason_description' => $this->modificationForm['reason_description'],
            'hours_variance' => $variance,
            'variance_type' => $variance > 0 ? 'overtime' : ($variance < 0 ? 'under_work' : 'none'),
            'requires_approval' => abs($variance) > 2, // Require approval for >2 hour variance
        ]);

        // Update schedule
        $schedule->update([
            'start_time' => $this->modificationForm['start_time'],
            'end_time' => $this->modificationForm['end_time'],
            'break_minutes' => $this->modificationForm['break_minutes'],
            'planned_hours' => $newHours,
            'location_status' => $this->modificationForm['location_status'] ?: $schedule->location_status,
            'work_type' => $this->modificationForm['work_type'] ?: $schedule->work_type,
        ]);

        $schedule->markAsModified();

        session()->flash('message', 'Schedule modified successfully!');
        $this->showModificationModal = false;
        $this->modificationScheduleId = null;
    }

    public function applyTemplate($templateId, $startDate = null)
    {
        $template = WorkScheduleTemplate::findOrFail($templateId);
        $startDate = $startDate ? Carbon::parse($startDate) : now();

        // Apply template to create schedule
        $this->templateId = $templateId;
        $this->scheduleDate = $startDate->format('Y-m-d');
        $this->startTime = $template->default_start_time ? $template->default_start_time->format('H:i') : '';
        $this->endTime = $template->default_end_time ? $template->default_end_time->format('H:i') : '';
        $this->breakMinutes = $template->default_break_minutes;
        $this->locationStatus = $template->default_location_status;
        $this->workType = $template->default_work_type;
        $this->department = $template->default_department ?? '';

        $template->incrementUsage();
        $this->viewMode = 'create';
    }

    private function createWorkLogFromSchedule(WorkSchedule $schedule)
    {
        $start = Carbon::parse($schedule->schedule_date->format('Y-m-d') . ' ' . $schedule->start_time->format('H:i'));
        $end = Carbon::parse($schedule->schedule_date->format('Y-m-d') . ' ' . $schedule->end_time->format('H:i'));
        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }
        $totalHours = round(($end->diffInMinutes($start) - $schedule->break_minutes) / 60, 2);
        $variance = $totalHours - $schedule->planned_hours;

        WorkLog::updateOrCreate(
            [
                'user_id' => $schedule->user_id,
                'work_date' => $schedule->schedule_date,
            ],
            [
                'schedule_id' => $schedule->id,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'total_hours_worked' => $totalHours,
                'scheduled_hours' => $schedule->planned_hours,
                'hours_variance' => $variance,
                'variance_type' => $variance > 0 ? 'overtime' : ($variance < 0 ? 'under_work' : 'none'),
                'break_minutes' => $schedule->break_minutes,
                'location_status' => $schedule->location_status,
                'location_name' => $schedule->location_name,
                'department' => $schedule->department,
                'is_schedule_confirmed' => true,
                'schedule_confirmed_at' => now(),
                'was_modified' => $schedule->status === 'modified',
            ]
        );
    }

    private function resetScheduleForm()
    {
        $this->scheduleId = null;
        $this->startTime = '';
        $this->endTime = '';
        $this->breakMinutes = 0;
        $this->locationStatus = 'in_port';
        $this->locationName = '';
        $this->workType = 'regular_duties';
        $this->department = '';
        $this->notes = '';
        $this->templateId = null;
    }

    public function render()
    {
        return view('livewire.work-log.schedule-manager', [
            'schedules' => $this->schedules,
            'templates' => $this->templates,
            'users' => $this->users,
            'yachts' => $this->yachts,
        ])->layout('layouts.app');
    }
}

