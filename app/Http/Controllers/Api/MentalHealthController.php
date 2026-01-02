<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MentalHealthTherapist;
use App\Models\MentalHealthResource;
use App\Models\MentalHealthSession;
use App\Models\MentalHealthSessionBooking;
use App\Models\MentalHealthTherapistAvailability;
use App\Models\MentalHealthMoodTracking;
use App\Models\MentalHealthGoal;
use App\Models\MentalHealthJournal;
use App\Models\MentalHealthHabit;
use App\Models\MentalHealthHabitTracking;
use App\Models\MentalHealthCredit;
use App\Models\MentalHealthCourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

/**
 * @OA\Tag(
 *     name="Mental Health",
 *     description="Mental Health & Wellness Support APIs"
 * )
 * 
 * @OA\Schema(
 *     schema="Therapist",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="biography", type="string", example="Licensed therapist with 10 years experience"),
 *     @OA\Property(property="specializations", type="array", @OA\Items(type="string"), example={"anxiety", "depression", "stress"}),
 *     @OA\Property(property="languages_spoken", type="array", @OA\Items(type="string"), example={"English", "Spanish"}),
 *     @OA\Property(property="years_experience", type="integer", example=10),
 *     @OA\Property(property="base_hourly_rate", type="number", format="float", example=150.00),
 *     @OA\Property(property="rating", type="number", format="float", example=4.8),
 *     @OA\Property(property="total_sessions", type="integer", example=250),
 *     @OA\Property(property="is_featured", type="boolean", example=true),
 *     @OA\Property(property="sliding_scale_available", type="boolean", example=true),
 *     @OA\Property(property="user", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="first_name", type="string"),
 *         @OA\Property(property="last_name", type="string"),
 *         @OA\Property(property="profile_photo_path", type="string")
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="MentalHealthResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Managing Anxiety at Sea"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="category", type="string", example="anxiety"),
 *     @OA\Property(property="type", type="string", enum={"article", "video", "audio", "exercise"}),
 *     @OA\Property(property="is_free", type="boolean", example=true),
 *     @OA\Property(property="duration_minutes", type="integer", example=15)
 * )
 */
class MentalHealthController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/mental-health/therapists",
     *     summary="Get list of therapists with filters",
     *     tags={"Mental Health"},
     *     @OA\Parameter(
     *         name="specialization",
     *         in="query",
     *         description="Filter by specialization (e.g., anxiety, depression, stress)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Filter by language spoken (e.g., English, Spanish, French)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="min_rating",
     *         in="query",
     *         description="Minimum rating (1-5)",
     *         required=false,
     *         @OA\Schema(type="number", format="float", minimum=1, maximum=5)
     *     ),
     *     @OA\Parameter(
     *         name="max_rate",
     *         in="query",
     *         description="Maximum hourly rate",
     *         required=false,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="sliding_scale",
     *         in="query",
     *         description="Filter therapists offering sliding scale pricing",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="is_featured",
     *         in="query",
     *         description="Filter featured therapists only",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of therapists",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Therapist")),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function getTherapists(Request $request): JsonResponse
    {
        $query = MentalHealthTherapist::with(['user:id,first_name,last_name,email,profile_photo_path'])
            ->where('application_status', 'approved')
            ->where('is_active', true);

        // Filter by specialization
        if ($request->filled('specialization')) {
            $query->whereJsonContains('specializations', $request->specialization);
        }

        // Filter by language
        if ($request->filled('language')) {
            $query->whereJsonContains('languages_spoken', $request->language);
        }

        // Filter by minimum rating
        if ($request->filled('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        // Filter by maximum hourly rate
        if ($request->filled('max_rate')) {
            $query->where('base_hourly_rate', '<=', $request->max_rate);
        }

        // Filter by sliding scale availability
        if ($request->boolean('sliding_scale')) {
            $query->where('sliding_scale_available', true);
        }

        // Filter featured therapists
        if ($request->boolean('is_featured')) {
            $query->where('is_featured', true);
        }

        // Sort options
        $sortBy = $request->input('sort_by', 'rating');
        $sortOrder = $request->input('sort_order', 'desc');
        
        if (in_array($sortBy, ['rating', 'base_hourly_rate', 'years_experience', 'total_sessions'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $therapists = $query->paginate($request->input('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => $therapists->items(),
            'meta' => [
                'current_page' => $therapists->currentPage(),
                'last_page' => $therapists->lastPage(),
                'per_page' => $therapists->perPage(),
                'total' => $therapists->total(),
            ],
            'filters' => [
                'specializations' => $this->getAvailableSpecializations(),
                'languages' => $this->getAvailableLanguages(),
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/mental-health/therapists/{id}",
     *     summary="Get therapist details",
     *     tags={"Mental Health"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Therapist ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Therapist details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Therapist")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Therapist not found")
     * )
     */
    public function getTherapist($id): JsonResponse
    {
        $therapist = MentalHealthTherapist::with([
            'user:id,first_name,last_name,email,profile_photo_path',
            'credentials',
            'availability',
        ])
            ->where('application_status', 'approved')
            ->where('is_active', true)
            ->find($id);

        if (!$therapist) {
            return response()->json([
                'success' => false,
                'message' => 'Therapist not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $therapist,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/mental-health/resources",
     *     summary="Get mental health resources",
     *     tags={"Mental Health"},
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by category",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter by type (article, video, audio, exercise)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"article", "video", "audio", "exercise"})
     *     ),
     *     @OA\Parameter(
     *         name="is_free",
     *         in="query",
     *         description="Filter free resources only",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of resources",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/MentalHealthResource"))
     *         )
     *     )
     * )
     */
    public function getResources(Request $request): JsonResponse
    {
        $query = MentalHealthResource::where('status', 'published');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('type')) {
            $query->where('resource_type', $request->type);
        }

        $resources = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => $resources->items(),
            'meta' => [
                'current_page' => $resources->currentPage(),
                'last_page' => $resources->lastPage(),
                'per_page' => $resources->perPage(),
                'total' => $resources->total(),
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/mental-health/filter-options",
     *     summary="Get available filter options for therapists",
     *     tags={"Mental Health"},
     *     @OA\Response(
     *         response=200,
     *         description="Available filter options",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="specializations", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="languages", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="price_range", type="object",
     *                     @OA\Property(property="min", type="number"),
     *                     @OA\Property(property="max", type="number")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getFilterOptions(): JsonResponse
    {
        $priceRange = MentalHealthTherapist::where('application_status', 'approved')
            ->where('is_active', true)
            ->selectRaw('MIN(base_hourly_rate) as min, MAX(base_hourly_rate) as max')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'specializations' => $this->getAvailableSpecializations(),
                'languages' => $this->getAvailableLanguages(),
                'price_range' => [
                    'min' => $priceRange->min ?? 0,
                    'max' => $priceRange->max ?? 500,
                ],
            ],
        ]);
    }

    /**
     * Get unique specializations from all active therapists
     */
    private function getAvailableSpecializations(): array
    {
        $therapists = MentalHealthTherapist::where('application_status', 'approved')
            ->where('is_active', true)
            ->whereNotNull('specializations')
            ->pluck('specializations');

        $specializations = [];
        foreach ($therapists as $specs) {
            if (is_array($specs)) {
                $specializations = array_merge($specializations, $specs);
            }
        }

        return array_values(array_unique($specializations));
    }

    /**
     * Get unique languages from all active therapists
     */
    private function getAvailableLanguages(): array
    {
        $therapists = MentalHealthTherapist::where('application_status', 'approved')
            ->where('is_active', true)
            ->whereNotNull('languages_spoken')
            ->pluck('languages_spoken');

        $languages = [];
        foreach ($therapists as $langs) {
            if (is_array($langs)) {
                $languages = array_merge($languages, $langs);
            }
        }

        return array_values(array_unique($languages));
    }

    /**
     * Get therapist availability
     */
    public function getTherapistAvailability(Request $request, $id): JsonResponse
    {
        try {
            $therapist = MentalHealthTherapist::where('application_status', 'approved')
                ->where('is_active', true)
                ->find($id);

            if (!$therapist) {
                return response()->json([
                    'status' => false,
                    'message' => 'Therapist not found',
                ], 404);
            }

            // Determine date range
            if ($request->filled('date')) {
                $startDate = Carbon::parse($request->date);
                $endDate = $startDate->copy();
            } else {
                // Default to next 7 days
                $startDate = Carbon::today();
                $endDate = Carbon::today()->addDays(6);
            }

            // Get recurring availability (weekly schedule)
            $recurringAvailability = MentalHealthTherapistAvailability::where('therapist_id', $id)
                ->where('is_recurring', true)
                ->where('is_active', true)
                ->where('is_blocked', false)
                ->whereNull('specific_date')
                ->get();

            // Get specific date availability (one-time schedules)
            $specificAvailability = MentalHealthTherapistAvailability::where('therapist_id', $id)
                ->where('is_active', true)
                ->where('is_blocked', false)
                ->whereNotNull('specific_date')
                ->whereDate('specific_date', '>=', $startDate->format('Y-m-d'))
                ->whereDate('specific_date', '<=', $endDate->format('Y-m-d'))
                ->get();

            // Get blocked dates
            $blockedDates = MentalHealthTherapistAvailability::where('therapist_id', $id)
                ->where('is_blocked', true)
                ->whereNotNull('specific_date')
                ->whereDate('specific_date', '>=', $startDate->format('Y-m-d'))
                ->whereDate('specific_date', '<=', $endDate->format('Y-m-d'))
                ->pluck('specific_date')
                ->map(function ($date) {
                    return is_string($date) ? $date : Carbon::parse($date)->format('Y-m-d');
                })
                ->toArray();

            // Get existing bookings to exclude booked slots
            $existingBookings = MentalHealthSessionBooking::where('therapist_id', $id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereBetween('scheduled_at', [
                    $startDate->startOfDay(),
                    $endDate->endOfDay()
                ])
                ->get()
                ->map(function ($booking) {
                    return [
                        'date' => Carbon::parse($booking->scheduled_at)->format('Y-m-d'),
                        'time' => Carbon::parse($booking->scheduled_at)->format('H:i'),
                        'duration' => $booking->duration_minutes,
                    ];
                })
                ->groupBy('date');

            $result = [];
            $currentDate = $startDate->copy();

            // Generate availability for each day in range
            while ($currentDate->lte($endDate)) {
                $dateStr = $currentDate->format('Y-m-d');
                $dayOfWeek = strtolower($currentDate->format('l')); // monday, tuesday, etc.

                // Skip if date is blocked
                if (in_array($dateStr, $blockedDates)) {
                    $currentDate->addDay();
                    continue;
                }

                $timeSlots = [];

                // Check for specific date availability first
                $specificSlots = $specificAvailability->filter(function ($slot) use ($dateStr) {
                    $slotDate = is_string($slot->specific_date) 
                        ? $slot->specific_date 
                        : Carbon::parse($slot->specific_date)->format('Y-m-d');
                    return $slotDate === $dateStr;
                });
                if ($specificSlots->isNotEmpty()) {
                    foreach ($specificSlots as $slot) {
                        $slots = $this->generateTimeSlots($slot, $currentDate, $existingBookings->get($dateStr, collect()), $therapist);
                        $timeSlots = array_merge($timeSlots, $slots);
                    }
                } else {
                    // Use recurring availability for this day of week
                    $daySlots = $recurringAvailability->where('day_of_week', $dayOfWeek);
                    foreach ($daySlots as $slot) {
                        $slots = $this->generateTimeSlots($slot, $currentDate, $existingBookings->get($dateStr, collect()), $therapist);
                        $timeSlots = array_merge($timeSlots, $slots);
                    }
                }

                // Sort time slots by start time
                usort($timeSlots, function ($a, $b) {
                    return strcmp($a['start_time'], $b['start_time']);
                });

                if (!empty($timeSlots)) {
                    $result[] = [
                        'date' => $dateStr,
                        'day_name' => $currentDate->format('l'),
                        'timezone' => $therapist->timezone ?? 'UTC',
                        'time_slots' => $timeSlots,
                    ];
                }

                $currentDate->addDay();
            }

            return response()->json([
                'status' => true,
                'message' => 'Availability retrieved successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error retrieving availability: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate time slots from availability record
     */
    private function generateTimeSlots($availability, Carbon $date, $existingBookings, $therapist = null): array
    {
        $slots = [];
        
        // Parse start and end times (they are TIME fields, not datetime)
        $startTimeStr = is_string($availability->start_time) 
            ? $availability->start_time 
            : Carbon::parse($availability->start_time)->format('H:i:s');
        $endTimeStr = is_string($availability->end_time) 
            ? $availability->end_time 
            : Carbon::parse($availability->end_time)->format('H:i:s');
            
        $startTime = Carbon::createFromTimeString($startTimeStr);
        $endTime = Carbon::createFromTimeString($endTimeStr);
        
        $bufferMinutes = $availability->buffer_minutes ?? 15;
        $sessionDurations = $availability->session_durations ?? [30, 60, 90];

        // Get booked times for this date
        $bookedTimes = $existingBookings->pluck('time')->toArray();

        // Generate slots for each available duration
        foreach ($sessionDurations as $duration) {
            $currentTime = $startTime->copy();

            while ($currentTime->copy()->addMinutes($duration)->lte($endTime)) {
                $slotStart = $currentTime->format('H:i');
                $slotEnd = $currentTime->copy()->addMinutes($duration)->format('H:i');

                // Check if this slot is already booked
                $isBooked = false;
                foreach ($bookedTimes as $bookedTime) {
                    $bookedStart = Carbon::createFromTimeString($bookedTime);
                    $bookedEnd = $bookedStart->copy()->addMinutes(60); // Assume 60 min booking
                    $slotStartTime = Carbon::createFromTimeString($slotStart);
                    $slotEndTime = Carbon::createFromTimeString($slotEnd);

                    if ($slotStartTime->lt($bookedEnd) && $slotEndTime->gt($bookedStart)) {
                        $isBooked = true;
                        break;
                    }
                }

                if (!$isBooked) {
                    $datetime = $date->copy()->setTimeFromTimeString($slotStart);
                    $timezone = $therapist->timezone ?? 'UTC';
                    if ($timezone !== 'UTC') {
                        try {
                            $datetime->setTimezone($timezone);
                        } catch (\Exception $e) {
                            // If timezone is invalid, use UTC
                            $timezone = 'UTC';
                        }
                    }

                    $slots[] = [
                        'start_time' => $slotStart,
                        'end_time' => $slotEnd,
                        'datetime' => $datetime->toIso8601String(),
                        'is_available' => true,
                        'session_types' => ['video', 'phone'], // Default session types
                        'duration_minutes' => $duration,
                    ];
                }

                // Move to next slot (duration + buffer)
                $currentTime->addMinutes($duration + $bufferMinutes);
            }
        }

        return $slots;
    }

    /**
     * @OA\Get(
     *     path="/api/mental-health/dashboard",
     *     summary="Get mental health dashboard",
     *     tags={"Mental Health"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard data retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Dashboard data retrieved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function getDashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        // Get upcoming sessions (next 3)
        $upcomingSessions = MentalHealthSessionBooking::with(['therapist.user:id,first_name,last_name'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at', 'asc')
            ->limit(3)
            ->get()
            ->map(function ($booking) {
                $therapist = $booking->therapist;
                $therapistUser = $therapist ? $therapist->user : null;
                $scheduledAt = Carbon::parse($booking->scheduled_at);
                $daysUntil = now()->diffInDays($scheduledAt, false);
                
                return [
                    'id' => $booking->id,
                    'therapist_id' => $booking->therapist_id,
                    'therapist_name' => $therapistUser 
                        ? ($therapistUser->first_name . ' ' . $therapistUser->last_name)
                        : 'Unknown Therapist',
                    'scheduled_at' => $booking->scheduled_at->toIso8601String(),
                    'session_type' => $booking->session_type ?? 'video',
                    'duration_minutes' => $booking->duration_minutes ?? 60,
                    'status' => $booking->status,
                    'days_until' => max(0, $daysUntil),
                ];
            });

        // Get recent mood entries (last 7 days)
        $recentMoodEntries = MentalHealthMoodTracking::where('user_id', $user->id)
            ->where('tracked_date', '>=', now()->subDays(7))
            ->orderBy('tracked_date', 'desc')
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'tracked_date' => $entry->tracked_date->format('Y-m-d'),
                    'mood_rating' => $entry->mood_rating,
                    'primary_mood' => $entry->primary_mood,
                    'energy_level' => $entry->energy_level,
                    'stress_level' => $entry->stress_level,
                ];
            });

        // Get active goals
        $activeGoals = MentalHealthGoal::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('target_date', 'asc')
            ->get()
            ->map(function ($goal) {
                return [
                    'id' => $goal->id,
                    'title' => $goal->title,
                    'target_date' => $goal->target_date ? $goal->target_date->format('Y-m-d') : null,
                    'progress_percentage' => (float) $goal->progress_percentage,
                ];
            });

        // Get recent resources (last 5 published resources)
        $recentResources = MentalHealthResource::with(['category'])
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($resource) {
                return [
                    'id' => $resource->id,
                    'title' => $resource->title,
                    'category' => $resource->category,
                    'resource_type' => $resource->resource_type,
                    'reading_time_minutes' => $resource->reading_time_minutes ?? 10,
                ];
            });

        // Calculate mood trends
        $moodEntries7Days = MentalHealthMoodTracking::where('user_id', $user->id)
            ->where('tracked_date', '>=', now()->subDays(7))
            ->whereNotNull('mood_rating')
            ->get();
        
        $moodEntries30Days = MentalHealthMoodTracking::where('user_id', $user->id)
            ->where('tracked_date', '>=', now()->subDays(30))
            ->whereNotNull('mood_rating')
            ->get();

        $averageMood7Days = $moodEntries7Days->isNotEmpty() 
            ? round($moodEntries7Days->avg('mood_rating'), 1)
            : null;
        
        $averageMood30Days = $moodEntries30Days->isNotEmpty()
            ? round($moodEntries30Days->avg('mood_rating'), 1)
            : null;

        // Determine trend
        $trend = 'stable';
        if ($averageMood7Days !== null && $averageMood30Days !== null) {
            if ($averageMood7Days > $averageMood30Days) {
                $trend = 'improving';
            } elseif ($averageMood7Days < $averageMood30Days) {
                $trend = 'declining';
            }
        }

        // Get daily entries for charting (last 30 days)
        $dailyEntries = MentalHealthMoodTracking::where('user_id', $user->id)
            ->where('tracked_date', '>=', now()->subDays(30))
            ->orderBy('tracked_date', 'asc')
            ->get()
            ->groupBy(function ($entry) {
                return $entry->tracked_date->format('Y-m-d');
            })
            ->map(function ($entries, $date) {
                $entry = $entries->first();
                return [
                    'date' => $date,
                    'mood_rating' => $entry->mood_rating,
                    'energy_level' => $entry->energy_level,
                    'stress_level' => $entry->stress_level,
                ];
            })
            ->values();

        // Calculate statistics
        $totalSessions = MentalHealthSessionBooking::where('user_id', $user->id)->count();
        $completedSessions = MentalHealthSessionBooking::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();
        
        $moodEntriesThisMonth = MentalHealthMoodTracking::where('user_id', $user->id)
            ->whereMonth('tracked_date', now()->month)
            ->whereYear('tracked_date', now()->year)
            ->count();
        
        $journalEntriesThisMonth = MentalHealthJournal::where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        $activeHabits = MentalHealthHabit::where('user_id', $user->id)
            ->where('is_active', true)
            ->count();
        
        // Calculate habit completion rate
        $habitsCompletionRate = 0;
        if ($activeHabits > 0) {
            try {
                // Get active habit IDs for the user
                $activeHabitIds = MentalHealthHabit::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->pluck('id');
                
                if ($activeHabitIds->isNotEmpty()) {
                    $totalHabitTracking = MentalHealthHabitTracking::whereIn('habit_id', $activeHabitIds)
                        ->whereMonth('tracked_date', now()->month)
                        ->whereYear('tracked_date', now()->year)
                        ->count();
                    
                    $expectedCompletions = $activeHabits * now()->daysInMonth;
                    $habitsCompletionRate = $expectedCompletions > 0 
                        ? round(($totalHabitTracking / $expectedCompletions) * 100, 1)
                        : 0;
                }
            } catch (\Exception $e) {
                // If habit tracking table doesn't exist or has issues, default to 0
                $habitsCompletionRate = 0;
            }
        }
        
        // Get available credits
        $creditsAvailable = MentalHealthCredit::where('user_id', $user->id)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expired_at')
                    ->orWhere('expired_at', '>', now());
            })
            ->sum('amount');
        
        $coursesEnrolled = MentalHealthCourseEnrollment::where('user_id', $user->id)
            ->whereNull('completed_at')
            ->count();

        return response()->json([
            'status' => true,
            'message' => 'Dashboard data retrieved successfully',
            'data' => [
                'upcoming_sessions' => $upcomingSessions,
                'recent_mood_entries' => $recentMoodEntries,
                'active_goals' => $activeGoals->count(),
                'goals' => $activeGoals,
                'recent_resources' => $recentResources,
                'mood_trend' => [
                    'average_mood_7_days' => $averageMood7Days,
                    'average_mood_30_days' => $averageMood30Days,
                    'trend' => $trend,
                    'daily_entries' => $dailyEntries,
                ],
                'statistics' => [
                    'total_sessions' => $totalSessions,
                    'completed_sessions' => $completedSessions,
                    'mood_entries_this_month' => $moodEntriesThisMonth,
                    'journal_entries_this_month' => $journalEntriesThisMonth,
                    'active_habits' => $activeHabits,
                    'habits_completion_rate' => $habitsCompletionRate,
                    'credits_available' => (float) $creditsAvailable,
                    'courses_enrolled' => $coursesEnrolled,
                ],
            ],
        ]);
    }
}

