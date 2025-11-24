<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkLog;
use App\Models\WorkLogRestPeriod;
use App\Services\WorkLog\ComplianceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WorkLogController extends Controller
{
    protected $complianceService;

    public function __construct(ComplianceService $complianceService)
    {
        $this->complianceService = $complianceService;
    }

    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Super admin can filter by user_id
        $query = WorkLog::query()
            ->with(['restPeriods', 'user'])
            ->when($request->input('user_id'), function ($q, $userId) use ($user) {
                // Only super admin can filter by other users
                if ($user->hasRole('super_admin')) {
                    $q->where('user_id', $userId);
                }
            }, function ($q) use ($user) {
                // Regular users only see their own logs
                $q->where('user_id', $user->id);
            })
            ->when($request->input('start_date'), function ($q, $date) {
                $q->where('work_date', '>=', $date);
            })
            ->when($request->input('end_date'), function ($q, $date) {
                $q->where('work_date', '<=', $date);
            })
            ->when($request->input('location_status'), function ($q, $status) {
                $q->where('location_status', $status);
            })
            ->when($request->input('compliance_status'), function ($q, $status) {
                $q->where('compliance_status', $status);
            })
            ->when($request->has('is_compliant'), function ($q) use ($request) {
                $q->where('is_compliant', $request->boolean('is_compliant'));
            })
            ->orderByDesc('work_date')
            ->orderByDesc('created_at');

        $workLogs = $query->paginate($request->input('per_page', 15));

        return response()->json($workLogs);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'work_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i:s',
            'end_time' => 'nullable|date_format:H:i:s',
            'total_hours_worked' => 'nullable|numeric|min:0|max:24',
            'overtime_hours' => 'nullable|numeric|min:0',
            'break_minutes' => 'nullable|integer|min:0',
            'total_rest_hours' => 'nullable|numeric|min:0|max:24',
            'sleep_hours' => 'nullable|numeric|min:0|max:24',
            'rest_uninterrupted' => 'nullable|boolean',
            'location_status' => 'nullable|in:at_sea,in_port,in_yard,on_leave,shore_leave',
            'location_name' => 'nullable|string|max:255',
            'port_name' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'yacht_id' => 'nullable|integer',
            'yacht_name' => 'nullable|string|max:255',
            'yacht_type' => 'nullable|string|max:255',
            'yacht_length' => 'nullable|string|max:50',
            'yacht_flag' => 'nullable|string|max:255',
            'position_rank' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'weather_conditions' => 'nullable|string|max:255',
            'sea_state' => 'nullable|string|max:255',
            'visibility' => 'nullable|string|max:255',
            'activities' => 'nullable|array',
            'notes' => 'nullable|string',
            'comments' => 'nullable|string',
            'is_day_off' => 'nullable|boolean',
            'rest_periods' => 'nullable|array',
            'rest_periods.*.start_time' => 'required_with:rest_periods|date_format:H:i:s',
            'rest_periods.*.end_time' => 'required_with:rest_periods|date_format:H:i:s',
            'rest_periods.*.duration_hours' => 'nullable|numeric',
            'rest_periods.*.type' => 'nullable|in:night_sleep,day_rest,break,other',
            'rest_periods.*.is_uninterrupted' => 'nullable|boolean',
            'rest_periods.*.location' => 'nullable|string',
            'rest_periods.*.notes' => 'nullable|string',
        ]);

        $workLog = new WorkLog();
        $workLog->user_id = Auth::id();
        $workLog->fill($validated);
        
        // Handle rest_periods separately
        $restPeriods = $validated['rest_periods'] ?? [];
        unset($validated['rest_periods']);
        
        $workLog->save();

        // Create rest periods if provided
        if (!empty($restPeriods)) {
            foreach ($restPeriods as $period) {
                WorkLogRestPeriod::create([
                    'work_log_id' => $workLog->id,
                    'start_time' => $period['start_time'],
                    'end_time' => $period['end_time'],
                    'duration_hours' => $period['duration_hours'] ?? null,
                    'type' => $period['type'] ?? 'other',
                    'is_uninterrupted' => $period['is_uninterrupted'] ?? true,
                    'location' => $period['location'] ?? null,
                    'notes' => $period['notes'] ?? null,
                ]);
            }
        }

        // Calculate compliance
        $compliance = $this->complianceService->checkDailyCompliance($workLog);
        $workLog->is_compliant = $compliance['is_compliant'];
        $workLog->compliance_status = $compliance['status'];
        $workLog->save();

        $workLog->load('restPeriods');

        return response()->json([
            'message' => 'Work log entry created successfully.',
            'data' => $workLog,
        ], 200);
    }

    public function show($id): JsonResponse
    {
        $workLog = WorkLog::with(['restPeriods', 'user', 'verifier'])
            ->findOrFail($id);

        // Check if user has access (own log or super admin)
        if ($workLog->user_id !== Auth::id() && !Auth::user()->hasRole('super_admin')) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        return response()->json(['data' => $workLog]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $workLog = WorkLog::findOrFail($id);

        // Check if user has access (own log or super admin)
        if ($workLog->user_id !== Auth::id() && Auth::user()->role !== 'super_admin') {
            return response()->json(['message' => 'Access denied - can only update own entries (or super admin)'], 403);
        }

        $validated = $request->validate([
            'work_date' => 'sometimes|date',
            'start_time' => 'nullable|date_format:H:i:s',
            'end_time' => 'nullable|date_format:H:i:s',
            'total_hours_worked' => 'nullable|numeric|min:0|max:24',
            'overtime_hours' => 'nullable|numeric|min:0',
            'break_minutes' => 'nullable|integer|min:0',
            'total_rest_hours' => 'nullable|numeric|min:0|max:24',
            'sleep_hours' => 'nullable|numeric|min:0|max:24',
            'rest_uninterrupted' => 'nullable|boolean',
            'location_status' => 'nullable|in:at_sea,in_port,in_yard,on_leave,shore_leave',
            'location_name' => 'nullable|string|max:255',
            'port_name' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'yacht_id' => 'nullable|integer',
            'yacht_name' => 'nullable|string|max:255',
            'yacht_type' => 'nullable|string|max:255',
            'yacht_length' => 'nullable|string|max:50',
            'yacht_flag' => 'nullable|string|max:255',
            'position_rank' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'weather_conditions' => 'nullable|string|max:255',
            'sea_state' => 'nullable|string|max:255',
            'visibility' => 'nullable|string|max:255',
            'activities' => 'nullable|array',
            'notes' => 'nullable|string',
            'comments' => 'nullable|string',
            'is_day_off' => 'nullable|boolean',
            'rest_periods' => 'nullable|array',
        ]);

        // Handle rest_periods separately
        $restPeriods = $validated['rest_periods'] ?? null;
        unset($validated['rest_periods']);

        $workLog->fill($validated);
        $workLog->save();

        // Update rest periods if provided
        if ($restPeriods !== null) {
            // Delete existing rest periods
            $workLog->restPeriods()->delete();
            
            // Create new rest periods
            foreach ($restPeriods as $period) {
                WorkLogRestPeriod::create([
                    'work_log_id' => $workLog->id,
                    'start_time' => $period['start_time'] ?? null,
                    'end_time' => $period['end_time'] ?? null,
                    'duration_hours' => $period['duration_hours'] ?? null,
                    'type' => $period['type'] ?? 'other',
                    'is_uninterrupted' => $period['is_uninterrupted'] ?? true,
                    'location' => $period['location'] ?? null,
                    'notes' => $period['notes'] ?? null,
                ]);
            }
        }

        // Recalculate compliance
        $compliance = $this->complianceService->checkDailyCompliance($workLog);
        $workLog->is_compliant = $compliance['is_compliant'];
        $workLog->compliance_status = $compliance['status'];
        $workLog->save();

        $workLog->load('restPeriods');

        return response()->json([
            'message' => 'Work log entry updated successfully.',
            'data' => $workLog,
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $workLog = WorkLog::findOrFail($id);

        // Check if user has access (own log or super admin)
        if ($workLog->user_id !== Auth::id() && Auth::user()->role !== 'super_admin') {
            return response()->json(['message' => 'Access denied - can only delete own entries (or super admin)'], 403);
        }

        $workLog->delete();

        return response()->json([
            'message' => 'Work log entry deleted successfully.',
        ], 200);
    }

    public function statistics(Request $request): JsonResponse
    {
        $user = Auth::user();
        $userId = $request->input('user_id');
        
        // Super admin can filter by user_id
        if ($userId && $user->hasRole('super_admin')) {
            $targetUserId = $userId;
        } else {
            $targetUserId = $user->id;
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $dateFilter = $request->input('date_filter', 'month');

        // Apply date filter
        if ($dateFilter === 'today') {
            $startDate = Carbon::today()->toDateString();
            $endDate = Carbon::today()->toDateString();
        } elseif ($dateFilter === 'week') {
            $startDate = Carbon::now()->startOfWeek()->toDateString();
            $endDate = Carbon::now()->endOfWeek()->toDateString();
        } elseif ($dateFilter === 'month') {
            $startDate = Carbon::now()->startOfMonth()->toDateString();
            $endDate = Carbon::now()->endOfMonth()->toDateString();
        }

        $query = WorkLog::where('user_id', $targetUserId)
            ->when($startDate, fn($q) => $q->where('work_date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('work_date', '<=', $endDate));

        $totalDays = $query->count();
        $totalHoursWorked = $query->sum('total_hours_worked') ?? 0;
        $totalRestHours = $query->sum('total_rest_hours') ?? 0;
        $averageHoursPerDay = $totalDays > 0 ? $totalHoursWorked / $totalDays : 0;
        $daysAtSea = $query->clone()->where('location_status', 'at_sea')->count();
        $daysInPort = $query->clone()->where('location_status', 'in_port')->count();
        $daysOnLeave = $query->clone()->where('location_status', 'on_leave')->count();
        $compliantDays = $query->clone()->where('is_compliant', true)->count();
        $violationDays = $query->clone()->where('compliance_status', 'violation')->count();

        return response()->json([
            'data' => [
                'total_days' => $totalDays,
                'total_hours_worked' => round($totalHoursWorked, 2),
                'total_rest_hours' => round($totalRestHours, 2),
                'average_hours_per_day' => round($averageHoursPerDay, 2),
                'days_at_sea' => $daysAtSea,
                'days_in_port' => $daysInPort,
                'days_on_leave' => $daysOnLeave,
                'compliant_days' => $compliantDays,
                'violation_days' => $violationDays,
            ],
        ]);
    }

    public function compliance(Request $request): JsonResponse
    {
        $user = Auth::user();
        $userId = $request->input('user_id');
        
        // Super admin can filter by user_id
        if ($userId && $user->hasRole('super_admin')) {
            $targetUserId = $userId;
        } else {
            $targetUserId = $user->id;
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $dateFilter = $request->input('date_filter', 'week');

        // Apply date filter
        if ($dateFilter === 'today') {
            $startDate = Carbon::today()->toDateString();
            $endDate = Carbon::today()->toDateString();
        } elseif ($dateFilter === 'week') {
            $startDate = Carbon::now()->startOfWeek()->toDateString();
            $endDate = Carbon::now()->endOfWeek()->toDateString();
        } elseif ($dateFilter === 'month') {
            $startDate = Carbon::now()->startOfMonth()->toDateString();
            $endDate = Carbon::now()->endOfMonth()->toDateString();
        }

        $startDateCarbon = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfWeek();
        $endDateCarbon = $endDate ? Carbon::parse($endDate) : Carbon::now()->endOfWeek();
        
        $summary = $this->complianceService->getComplianceSummary($targetUserId, $startDateCarbon, $endDateCarbon);
        
        // Add weekly compliance check
        $weeklyCompliance = $this->complianceService->checkWeeklyCompliance($targetUserId, $startDateCarbon);
        $summary['weekly_compliance'] = $weeklyCompliance;

        return response()->json(['data' => $summary]);
    }

    public function addRestPeriod(Request $request, $id): JsonResponse
    {
        $workLog = WorkLog::findOrFail($id);

        // Check if user has access
        if ($workLog->user_id !== Auth::id() && Auth::user()->role !== 'super_admin') {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $validated = $request->validate([
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s',
            'duration_hours' => 'nullable|numeric',
            'type' => 'nullable|in:night_sleep,day_rest,break,other',
            'is_uninterrupted' => 'nullable|boolean',
            'location' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $restPeriod = WorkLogRestPeriod::create([
            'work_log_id' => $workLog->id,
            ...$validated,
        ]);

        // Recalculate compliance
        $this->complianceService->checkCompliance($workLog->fresh());

        return response()->json([
            'message' => 'Rest period added successfully.',
            'data' => $restPeriod,
        ], 200);
    }

    public function updateRestPeriod(Request $request, $id, $restPeriodId): JsonResponse
    {
        $workLog = WorkLog::findOrFail($id);

        // Check if user has access
        if ($workLog->user_id !== Auth::id() && Auth::user()->role !== 'super_admin') {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $restPeriod = WorkLogRestPeriod::where('work_log_id', $id)
            ->findOrFail($restPeriodId);

        $validated = $request->validate([
            'start_time' => 'sometimes|date_format:H:i:s',
            'end_time' => 'sometimes|date_format:H:i:s',
            'duration_hours' => 'nullable|numeric',
            'type' => 'nullable|in:night_sleep,day_rest,break,other',
            'is_uninterrupted' => 'nullable|boolean',
            'location' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $restPeriod->fill($validated);
        $restPeriod->save();

        // Recalculate compliance
        $this->complianceService->checkCompliance($workLog->fresh());

        return response()->json([
            'message' => 'Rest period updated successfully.',
            'data' => $restPeriod,
        ], 200);
    }

    public function deleteRestPeriod($id, $restPeriodId): JsonResponse
    {
        $workLog = WorkLog::findOrFail($id);

        // Check if user has access
        if ($workLog->user_id !== Auth::id() && Auth::user()->role !== 'super_admin') {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $restPeriod = WorkLogRestPeriod::where('work_log_id', $id)
            ->findOrFail($restPeriodId);

        $restPeriod->delete();

        // Recalculate compliance
        $this->complianceService->checkCompliance($workLog->fresh());

        return response()->json([
            'message' => 'Rest period deleted successfully.',
        ], 200);
    }
}

