<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MentalHealthTherapist;
use App\Models\MentalHealthResource;
use App\Models\MentalHealthSession;
use App\Models\MentalHealthSessionBooking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
        $query = MentalHealthResource::where('is_published', true);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->boolean('is_free')) {
            $query->where('is_free', true);
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
}

