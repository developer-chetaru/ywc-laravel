<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrainingProvider;
use App\Models\TrainingProviderCourse;
use App\Models\TrainingCertification;
use App\Models\TrainingCertificationCategory;
use App\Models\TrainingCourseSchedule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TrainingController extends Controller
{
    /**
     * Get list of training providers with filters
     */
    public function getProviders(Request $request): JsonResponse
    {
        $query = TrainingProvider::with(['activeCourses.certification'])
            ->where('is_active', true);

        // Filter by location/country
        if ($request->filled('country')) {
            $query->whereHas('courses.locations', function($q) use ($request) {
                $q->where('country', $request->country);
            });
        }

        // Filter by certification type
        if ($request->filled('certification_id')) {
            $query->whereHas('courses', function($q) use ($request) {
                $q->where('certification_id', $request->certification_id);
            });
        }

        // Filter verified partners
        if ($request->boolean('verified_only')) {
            $query->where('is_verified_partner', true);
        }

        // Filter by minimum rating
        if ($request->filled('min_rating')) {
            $query->where('rating_avg', '>=', $request->min_rating);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sortBy = $request->input('sort_by', 'rating_avg');
        $sortOrder = $request->input('sort_order', 'desc');
        if (in_array($sortBy, ['rating_avg', 'total_reviews', 'total_students_trained', 'name'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $providers = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => true,
            'message' => 'Training providers retrieved successfully',
            'data' => $providers->items(),
            'meta' => [
                'current_page' => $providers->currentPage(),
                'last_page' => $providers->lastPage(),
                'per_page' => $providers->perPage(),
                'total' => $providers->total(),
            ],
        ]);
    }

    /**
     * Get provider details
     */
    public function getProvider($slug): JsonResponse
    {
        $provider = TrainingProvider::with([
            'activeCourses.certification.category',
            'activeCourses.locations',
            'activeCourses.upcomingSchedules',
            'galleries',
        ])
            ->where('is_active', true)
            ->where('slug', $slug)
            ->first();

        if (!$provider) {
            return response()->json([
                'status' => false,
                'message' => 'Provider not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Provider details retrieved successfully',
            'data' => $provider,
        ]);
    }

    /**
     * Get list of courses with filters
     */
    public function getCourses(Request $request): JsonResponse
    {
        $query = TrainingProviderCourse::with(['certification.category', 'provider', 'locations'])
            ->where('is_active', true)
            ->whereHas('provider', function($q) {
                $q->where('is_active', true);
            });

        // Filter by certification
        if ($request->filled('certification_id')) {
            $query->where('certification_id', $request->certification_id);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->whereHas('certification', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // Filter by provider
        if ($request->filled('provider_id')) {
            $query->where('provider_id', $request->provider_id);
        }

        // Filter by country/location
        if ($request->filled('country')) {
            $query->whereHas('locations', function($q) use ($request) {
                $q->where('country', $request->country);
            });
        }

        // Filter by format
        if ($request->filled('format')) {
            $query->where('format', $request->format);
        }

        // Filter by price range
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by duration
        if ($request->filled('max_duration_days')) {
            $query->where('duration_days', '<=', $request->max_duration_days);
        }

        // Search
        if ($request->filled('search')) {
            $query->whereHas('certification', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Sort
        $sortBy = $request->input('sort_by', 'rating_avg');
        $sortOrder = $request->input('sort_order', 'desc');
        if (in_array($sortBy, ['price', 'rating_avg', 'duration_days', 'review_count'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $courses = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => true,
            'message' => 'Courses retrieved successfully',
            'data' => $courses->items(),
            'meta' => [
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
            ],
        ]);
    }

    /**
     * Get course details
     */
    public function getCourse($id): JsonResponse
    {
        $course = TrainingProviderCourse::with([
            'certification.category',
            'provider',
            'locations',
            'upcomingSchedules',
            'reviews.user:id,first_name,last_name,profile_photo_path',
        ])
            ->where('is_active', true)
            ->find($id);

        if (!$course) {
            return response()->json([
                'status' => false,
                'message' => 'Course not found',
            ], 404);
        }

        // Increment view count
        $course->increment('view_count');

        return response()->json([
            'status' => true,
            'message' => 'Course details retrieved successfully',
            'data' => $course,
        ]);
    }

    /**
     * Get certifications list
     */
    public function getCertifications(Request $request): JsonResponse
    {
        $query = TrainingCertification::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $certifications = $query->orderBy('name')->get();

        return response()->json([
            'status' => true,
            'message' => 'Certifications retrieved successfully',
            'data' => $certifications,
        ]);
    }

    /**
     * Get certification categories
     */
    public function getCategories(): JsonResponse
    {
        $categories = TrainingCertificationCategory::withCount('certifications')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Categories retrieved successfully',
            'data' => $categories,
        ]);
    }

    /**
     * Get filter options
     */
    public function getFilterOptions(): JsonResponse
    {
        $categories = TrainingCertificationCategory::orderBy('name')->get(['id', 'name']);
        
        $certifications = TrainingCertification::orderBy('name')->get(['id', 'name', 'category_id']);
        
        $formats = ['in-person', 'online', 'hybrid', 'blended'];
        
        $priceRange = TrainingProviderCourse::where('is_active', true)
            ->selectRaw('MIN(price) as min, MAX(price) as max')
            ->first();

        $countries = TrainingProviderCourse::join('training_course_locations', 'training_provider_courses.id', '=', 'training_course_locations.provider_course_id')
            ->where('training_provider_courses.is_active', true)
            ->distinct()
            ->pluck('training_course_locations.country')
            ->filter()
            ->values();

        return response()->json([
            'status' => true,
            'message' => 'Filter options retrieved successfully',
            'data' => [
                'categories' => $categories,
                'certifications' => $certifications,
                'formats' => $formats,
                'countries' => $countries,
                'price_range' => [
                    'min' => $priceRange->min ?? 0,
                    'max' => $priceRange->max ?? 5000,
                ],
            ],
        ]);
    }

    /**
     * Get upcoming schedules
     */
    public function getSchedules(Request $request): JsonResponse
    {
        $query = TrainingCourseSchedule::with(['providerCourse.certification', 'providerCourse.provider', 'location'])
            ->where('start_date', '>=', now())
            ->where('is_cancelled', false)
            ->where('is_full', false);

        if ($request->filled('certification_id')) {
            $query->whereHas('providerCourse', function($q) use ($request) {
                $q->where('certification_id', $request->certification_id);
            });
        }

        if ($request->filled('country')) {
            $query->whereHas('location', function($q) use ($request) {
                $q->where('country', $request->country);
            });
        }

        if ($request->filled('month')) {
            $query->whereMonth('start_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->year);
        }

        $schedules = $query->orderBy('start_date')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'status' => true,
            'message' => 'Schedules retrieved successfully',
            'data' => $schedules->items(),
            'meta' => [
                'current_page' => $schedules->currentPage(),
                'last_page' => $schedules->lastPage(),
                'per_page' => $schedules->perPage(),
                'total' => $schedules->total(),
            ],
        ]);
    }
}

