<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkSchedule;
use App\Models\WorkScheduleTemplate;
use App\Models\WorkScheduleModification;
use App\Models\WorkLog;
use App\Services\WorkLog\ComplianceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    protected $complianceService;

    public function __construct(ComplianceService $complianceService)
    {
        $this->complianceService = $complianceService;
    }

    /**
     * @OA\Get(
     *     path="/api/work-schedules",
     *     summary="Get work schedules",
     *     tags={"Work Schedules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Filter by user ID (captain/super admin only)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Start date filter (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="End date filter (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status (pending, confirmed, modified, cancelled)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pending", "confirmed", "modified", "cancelled"})
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Schedules retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="pagination", type="object")
     *         )
     *     )
     * )
     * Get schedules for authenticated user or all schedules (captain/super admin)
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $query = WorkSchedule::with(['user:id,first_name,last_name,email', 'yacht:id,name', 'template:id,name'])
            ->when($request->input('user_id'), function ($q, $userId) use ($user) {
                // Only captain/super admin can filter by other users
                if ($user->hasRole(['captain', 'super_admin'])) {
                    $q->where('user_id', $userId);
                }
            }, function ($q) use ($user) {
                // Regular users only see their own schedules
                if (!$user->hasRole(['captain', 'super_admin'])) {
                    $q->where('user_id', $user->id);
                }
            })
            ->when($request->input('start_date'), function ($q, $date) {
                $q->where('schedule_date', '>=', $date);
            })
            ->when($request->input('end_date'), function ($q, $date) {
                $q->where('schedule_date', '<=', $date);
            })
            ->when($request->input('status'), function ($q, $status) {
                $q->where('status', $status);
            })
            ->when($request->input('department'), function ($q, $department) {
                $q->where('department', $department);
            })
            ->orderBy('schedule_date', 'desc')
            ->orderBy('start_time');

        $perPage = $request->input('per_page', 15);
        $schedules = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $schedules->items(),
            'pagination' => [
                'current_page' => $schedules->currentPage(),
                'per_page' => $schedules->perPage(),
                'total' => $schedules->total(),
                'last_page' => $schedules->lastPage(),
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/work-schedules/pending",
     *     summary="Get pending schedules for authenticated user",
     *     tags={"Work Schedules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Pending schedules retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     * Get pending schedules for authenticated user
     */
    public function pending(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $query = WorkSchedule::with(['user:id,first_name,last_name,email', 'yacht:id,name'])
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('schedule_date', '>=', now()->toDateString())
            ->orderBy('schedule_date')
            ->orderBy('start_time');

        $schedules = $query->get();

        return response()->json([
            'success' => true,
            'data' => $schedules,
        ]);
    }

    /**
     * Get schedule details
     */
    public function show($id): JsonResponse
    {
        $schedule = WorkSchedule::with(['user', 'yacht', 'template', 'modifications.modifier'])
            ->findOrFail($id);

        // Authorization check
        $user = Auth::user();
        if (!$user->hasRole(['captain', 'super_admin']) && $schedule->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this schedule',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $schedule,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/work-schedules",
     *     summary="Create a new work schedule",
     *     tags={"Work Schedules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "schedule_date", "start_time", "end_time", "location_status", "work_type"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="schedule_date", type="string", format="date", example="2025-12-06"),
     *             @OA\Property(property="start_time", type="string", format="time", example="08:00"),
     *             @OA\Property(property="end_time", type="string", format="time", example="18:00"),
     *             @OA\Property(property="break_minutes", type="integer", example=60),
     *             @OA\Property(property="location_status", type="string", enum={"in_port", "at_sea", "in_shipyard", "at_anchor"}, example="in_port"),
     *             @OA\Property(property="work_type", type="string", enum={"regular_duties", "maintenance", "guest_service", "emergency_standby", "shore_leave", "rest_period"}, example="regular_duties"),
     *             @OA\Property(property="yacht_id", type="integer", nullable=true),
     *             @OA\Property(property="location_name", type="string", nullable=true),
     *             @OA\Property(property="department", type="string", nullable=true),
     *             @OA\Property(property="notes", type="string", nullable=true),
     *             @OA\Property(property="template_id", type="integer", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Schedule created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Schedule created successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     * Create new schedule
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'schedule_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'break_minutes' => 'nullable|integer|min:0',
            'location_status' => 'required|in:in_port,at_sea,in_shipyard,at_anchor',
            'work_type' => 'required|in:regular_duties,maintenance,guest_service,emergency_standby,shore_leave,rest_period',
            'yacht_id' => 'nullable|exists:yachts,id',
            'location_name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'template_id' => 'nullable|exists:work_schedule_templates,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $data = $validator->validated();

        // Authorization check
        if (!$user->hasRole(['captain', 'super_admin']) && $data['user_id'] !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only create schedules for yourself',
            ], 403);
        }

        // Calculate planned hours
        $start = Carbon::parse($data['schedule_date'] . ' ' . $data['start_time']);
        $end = Carbon::parse($data['schedule_date'] . ' ' . $data['end_time']);
        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }
        $plannedHours = round(($end->diffInMinutes($start) - ($data['break_minutes'] ?? 0)) / 60, 2);

        $schedule = WorkSchedule::create([
            'user_id' => $data['user_id'],
            'yacht_id' => $data['yacht_id'] ?? null,
            'schedule_date' => $data['schedule_date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'break_minutes' => $data['break_minutes'] ?? 0,
            'planned_hours' => $plannedHours,
            'location_status' => $data['location_status'],
            'location_name' => $data['location_name'] ?? null,
            'work_type' => $data['work_type'],
            'department' => $data['department'] ?? null,
            'notes' => $data['notes'] ?? null,
            'template_id' => $data['template_id'] ?? null,
            'created_by' => $user->id,
            'created_by_role' => $user->hasRole('captain') ? 'captain' : 'crew',
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Schedule created successfully',
            'data' => $schedule->load(['user:id,first_name,last_name,email', 'yacht:id,name']),
        ], 201);
    }

    /**
     * Update schedule
     */
    public function update(Request $request, $id): JsonResponse
    {
        $schedule = WorkSchedule::findOrFail($id);
        $user = Auth::user();

        // Authorization check
        if (!$user->hasRole(['captain', 'super_admin']) && $schedule->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this schedule',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'schedule_date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
            'break_minutes' => 'nullable|integer|min:0',
            'location_status' => 'sometimes|in:in_port,at_sea,in_shipyard,at_anchor',
            'work_type' => 'sometimes|in:regular_duties,maintenance,guest_service,emergency_standby,shore_leave,rest_period',
            'yacht_id' => 'nullable|exists:yachts,id',
            'location_name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Recalculate planned hours if times changed
        if (isset($data['start_time']) || isset($data['end_time'])) {
            $startTime = $data['start_time'] ?? $schedule->start_time->format('H:i');
            $endTime = $data['end_time'] ?? $schedule->end_time->format('H:i');
            $scheduleDate = $data['schedule_date'] ?? $schedule->schedule_date->format('Y-m-d');
            $breakMinutes = $data['break_minutes'] ?? $schedule->break_minutes;

            $start = Carbon::parse($scheduleDate . ' ' . $startTime);
            $end = Carbon::parse($scheduleDate . ' ' . $endTime);
            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }
            $data['planned_hours'] = round(($end->diffInMinutes($start) - $breakMinutes) / 60, 2);
        }

        $schedule->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Schedule updated successfully',
            'data' => $schedule->load(['user:id,first_name,last_name,email', 'yacht:id,name']),
        ]);
    }

    /**
     * Delete schedule
     */
    public function destroy($id): JsonResponse
    {
        $schedule = WorkSchedule::findOrFail($id);
        $user = Auth::user();

        // Authorization check
        if (!$user->hasRole(['captain', 'super_admin']) && $schedule->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this schedule',
            ], 403);
        }

        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/work-schedules/{id}/confirm",
     *     summary="Quick confirm a schedule",
     *     tags={"Work Schedules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Schedule confirmed and work log created",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Schedule confirmed and work log created"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=400, description="Schedule already confirmed")
     * )
     * Quick confirm schedule
     */
    public function confirm(Request $request, $id): JsonResponse
    {
        $schedule = WorkSchedule::findOrFail($id);
        $user = Auth::user();

        // Only the schedule owner can confirm
        if ($schedule->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only confirm your own schedules',
            ], 403);
        }

        if ($schedule->status === 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'Schedule is already confirmed',
            ], 400);
        }

        // Confirm the schedule
        $schedule->confirm();

        // Create work log from schedule
        $start = Carbon::parse($schedule->schedule_date->format('Y-m-d') . ' ' . $schedule->start_time->format('H:i'));
        $end = Carbon::parse($schedule->schedule_date->format('Y-m-d') . ' ' . $schedule->end_time->format('H:i'));
        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }
        $totalHours = round(($end->diffInMinutes($start) - $schedule->break_minutes) / 60, 2);

        $workLog = WorkLog::updateOrCreate(
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
                'hours_variance' => 0,
                'variance_type' => 'none',
                'break_minutes' => $schedule->break_minutes,
                'location_status' => $schedule->location_status,
                'location_name' => $schedule->location_name,
                'department' => $schedule->department,
                'is_schedule_confirmed' => true,
                'schedule_confirmed_at' => now(),
                'was_modified' => false,
            ]
        );

        // Check compliance
        $compliance = $this->complianceService->checkDailyCompliance($workLog);
        $workLog->update([
            'is_compliant' => $compliance['is_compliant'],
            'compliance_status' => $compliance['status'],
            'compliance_notes' => json_encode($compliance),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Schedule confirmed and work log created',
            'data' => [
                'schedule' => $schedule->load(['user:id,first_name,last_name,email', 'yacht:id,name']),
                'work_log' => $workLog,
                'compliance' => $compliance,
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/work-schedules/{id}/modify",
     *     summary="Modify a schedule",
     *     tags={"Work Schedules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"start_time", "end_time", "reason_code"},
     *             @OA\Property(property="start_time", type="string", format="time", example="07:00"),
     *             @OA\Property(property="end_time", type="string", format="time", example="19:00"),
     *             @OA\Property(property="break_minutes", type="integer", example=60),
     *             @OA\Property(property="location_status", type="string", enum={"in_port", "at_sea", "in_shipyard", "at_anchor"}, nullable=true),
     *             @OA\Property(property="work_type", type="string", enum={"regular_duties", "maintenance", "guest_service", "emergency_standby", "shore_leave", "rest_period"}, nullable=true),
     *             @OA\Property(property="reason_code", type="string", enum={"weather_delay", "guest_request", "maintenance_priority", "emergency", "crew_request", "itinerary_change", "other"}, example="guest_request"),
     *             @OA\Property(property="reason_description", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Schedule modified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Schedule modified successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     * Modify schedule
     */
    public function modify(Request $request, $id): JsonResponse
    {
        $schedule = WorkSchedule::findOrFail($id);
        $user = Auth::user();

        // Only the schedule owner can modify
        if ($schedule->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only modify your own schedules',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'break_minutes' => 'nullable|integer|min:0',
            'location_status' => 'sometimes|in:in_port,at_sea,in_shipyard,at_anchor',
            'work_type' => 'sometimes|in:regular_duties,maintenance,guest_service,emergency_standby,shore_leave,rest_period',
            'reason_code' => 'required|in:weather_delay,guest_request,maintenance_priority,emergency,crew_request,itinerary_change,other',
            'reason_description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Calculate variance
        $originalHours = $schedule->planned_hours;
        $start = Carbon::parse($schedule->schedule_date->format('Y-m-d') . ' ' . $data['start_time']);
        $end = Carbon::parse($schedule->schedule_date->format('Y-m-d') . ' ' . $data['end_time']);
        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }
        $newHours = round(($end->diffInMinutes($start) - ($data['break_minutes'] ?? $schedule->break_minutes)) / 60, 2);
        $variance = $newHours - $originalHours;

        // Track modification
        $modification = WorkScheduleModification::create([
            'schedule_id' => $schedule->id,
            'modified_by' => $user->id,
            'modification_type' => $variance > 0 ? 'extension' : ($variance < 0 ? 'shortening' : 'time_adjustment'),
            'changes_before' => [
                'start_time' => $schedule->start_time?->format('H:i'),
                'end_time' => $schedule->end_time?->format('H:i'),
                'break_minutes' => $schedule->break_minutes,
                'planned_hours' => $schedule->planned_hours,
            ],
            'changes_after' => [
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'break_minutes' => $data['break_minutes'] ?? $schedule->break_minutes,
                'planned_hours' => $newHours,
            ],
            'reason_code' => $data['reason_code'],
            'reason_description' => $data['reason_description'] ?? null,
            'hours_variance' => $variance,
            'variance_type' => $variance > 0 ? 'overtime' : ($variance < 0 ? 'under_work' : 'none'),
            'requires_approval' => abs($variance) > 2,
        ]);

        // Update schedule
        $schedule->update([
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'break_minutes' => $data['break_minutes'] ?? $schedule->break_minutes,
            'planned_hours' => $newHours,
            'location_status' => $data['location_status'] ?? $schedule->location_status,
            'work_type' => $data['work_type'] ?? $schedule->work_type,
        ]);

        $schedule->markAsModified();

        return response()->json([
            'success' => true,
            'message' => 'Schedule modified successfully',
            'data' => [
                'schedule' => $schedule->load(['user:id,first_name,last_name,email', 'yacht:id,name']),
                'modification' => $modification,
                'variance' => $variance,
            ],
        ]);
    }

    /**
     * Get schedule templates
     */
    public function templates(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $query = WorkScheduleTemplate::with('creator:id,first_name,last_name')
            ->active()
            ->when(!$user->hasRole('super_admin'), function ($q) use ($user) {
                // Show yacht-specific or public templates
                $q->where(function($query) {
                    $query->where('is_public', true);
                });
            })
            ->orderBy('name');

        $templates = $query->get();

        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }

    /**
     * Get captain dashboard summary
     */
    public function captainSummary(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Only captains and super admins can access
        if (!$user->hasRole(['captain', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only captains can access this endpoint',
            ], 403);
        }

        $startDate = $request->input('start_date', now()->startOfWeek()->toDateString());
        $endDate = $request->input('end_date', now()->endOfWeek()->toDateString());

        // Today's overview
        $today = now()->toDateString();
        $todaySchedules = WorkSchedule::where('schedule_date', $today)->get();
        $todayLogs = WorkLog::where('work_date', $today)->get();

        // Crew compliance summary
        $crewMembers = \App\Models\User::whereHas('roles', function($q) {
            $q->whereIn('name', ['crew', 'captain']);
        })->get();

        $complianceSummary = [];
        foreach ($crewMembers as $crew) {
            $crewLogs = WorkLog::where('user_id', $crew->id)
                ->whereBetween('work_date', [$startDate, $endDate])
                ->get();
            
            $weeklyCompliance = $this->complianceService->checkWeeklyCompliance($crew->id, Carbon::parse($startDate));
            
            $complianceSummary[] = [
                'user_id' => $crew->id,
                'user_name' => $crew->first_name . ' ' . $crew->last_name,
                'total_hours' => $crewLogs->sum('total_hours_worked'),
                'total_days' => $crewLogs->count(),
                'compliant_days' => $crewLogs->where('is_compliant', true)->count(),
                'warning_days' => $crewLogs->where('compliance_status', 'warning')->count(),
                'violation_days' => $crewLogs->where('compliance_status', 'violation')->count(),
                'weekly_compliance' => $weeklyCompliance,
                'pending_schedules' => WorkSchedule::where('user_id', $crew->id)
                    ->where('status', 'pending')
                    ->where('schedule_date', '>=', $today)
                    ->count(),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'today_overview' => [
                    'total_schedules' => $todaySchedules->count(),
                    'pending_schedules' => $todaySchedules->where('status', 'pending')->count(),
                    'confirmed_schedules' => $todaySchedules->where('status', 'confirmed')->count(),
                    'total_logs' => $todayLogs->count(),
                    'compliant_logs' => $todayLogs->where('is_compliant', true)->count(),
                    'warning_logs' => $todayLogs->where('compliance_status', 'warning')->count(),
                    'violation_logs' => $todayLogs->where('compliance_status', 'violation')->count(),
                    'crew_on_duty' => $todayLogs->where('total_hours_worked', '>', 0)->pluck('user_id')->unique()->count(),
                ],
                'compliance_summary' => $complianceSummary,
            ],
        ]);
    }
}

