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
use Illuminate\Support\Facades\Storage;

class TrainingController extends Controller
{
    /**
     * Get full URL for storage file
     * 
     * Uses APP_URL from environment configuration to build full URLs.
     * Falls back to request scheme and host if APP_URL is not set.
     * Returns null if file doesn't exist (unless it's already a full URL).
     * 
     * @param string|null $path Storage file path (relative to storage/app/public)
     * @return string|null Full URL with domain from APP_URL environment variable, or null if file doesn't exist
     */
    private function getFullUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        
        // If already a full URL, return as is
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        
        // Normalize path
        $storagePath = ltrim($path, '/');
        $storagePath = preg_replace('/^storage\//', '', $storagePath);
        
        // Check if file exists in storage/app/public (Laravel storage)
        $fullStoragePath = storage_path('app/public/' . $storagePath);
        $fileExists = file_exists($fullStoragePath);
        
        // If not in storage, check public/images directory (legacy location)
        if (!$fileExists) {
            $publicPath = public_path($storagePath);
            if (file_exists($publicPath)) {
                $fileExists = true;
                // For public directory files, use direct path without /storage/
                $baseUrl = config('app.url');
                if (!$baseUrl) {
                    $baseUrl = request()->getSchemeAndHttpHost();
                }
                $baseUrl = rtrim($baseUrl, '/');
                return $baseUrl . '/' . $storagePath;
            }
        }
        
        // If file doesn't exist in either location, return null
        if (!$fileExists) {
            \Log::warning('Image file not found', [
                'path' => $path,
                'storage_path' => $storagePath,
                'checked_storage' => $fullStoragePath,
                'checked_public' => public_path($storagePath)
            ]);
            return null;
        }
        
        // Get base URL from environment config (APP_URL)
        // This reads from .env file: APP_URL=https://your-domain.com
        $baseUrl = config('app.url');
        if (!$baseUrl) {
            // Fallback to request URL if APP_URL not configured
            $baseUrl = request()->getSchemeAndHttpHost();
        }
        $baseUrl = rtrim($baseUrl, '/');
        
        // Return full URL: {APP_URL}/storage/{path}
        return $baseUrl . '/storage/' . $storagePath;
    }

    /**
     * Transform provider data to include full image URLs
     */
    private function transformProvider($provider)
    {
        if (!$provider) {
            return $provider;
        }

        // Handle Eloquent models
        if (is_object($provider) && method_exists($provider, 'getAttribute')) {
            if ($provider->logo) {
                $provider->logo_url = $this->getFullUrl($provider->logo);
            }

            // Transform galleries
            if ($provider->relationLoaded('galleries') && $provider->galleries) {
                foreach ($provider->galleries as $gallery) {
                    if ($gallery->image_path) {
                        $gallery->image_url = $this->getFullUrl($gallery->image_path);
                    }
                }
            }
        } else {
            // Handle arrays or stdClass objects
            if (is_array($provider)) {
                $provider = (object) $provider;
            }

            if (isset($provider->logo)) {
                $provider->logo_url = $this->getFullUrl($provider->logo);
            }

            // Transform galleries
            if (isset($provider->galleries) && is_iterable($provider->galleries)) {
                foreach ($provider->galleries as $gallery) {
                    if (isset($gallery->image_path)) {
                        $gallery->image_url = $this->getFullUrl($gallery->image_path);
                    }
                }
            }
        }

        return $provider;
    }

    /**
     * Transform course data to include full image URLs
     */
    private function transformCourse($course)
    {
        if (!$course) {
            return $course;
        }

        // Handle Eloquent models
        if (is_object($course) && method_exists($course, 'getAttribute')) {
            // Transform certification cover image if present
            if ($course->relationLoaded('certification') && $course->certification && $course->certification->cover_image) {
                $course->certification->cover_image_url = $this->getFullUrl($course->certification->cover_image);
            }

            // Transform provider if present
            if ($course->relationLoaded('provider') && $course->provider) {
                $course->provider = $this->transformProvider($course->provider);
            }

            // Transform reviews user profile photos
            if ($course->relationLoaded('reviews') && $course->reviews) {
                foreach ($course->reviews as $review) {
                    if ($review->relationLoaded('user') && $review->user && $review->user->profile_photo_path) {
                        $review->user->profile_photo_url = $this->getFullUrl($review->user->profile_photo_path);
                    }
                }
            }
        } else {
            // Handle arrays or stdClass objects
            if (is_array($course)) {
                $course = (object) $course;
            }

            // Transform certification cover image if present
            if (isset($course->certification) && isset($course->certification->cover_image)) {
                $course->certification->cover_image_url = $this->getFullUrl($course->certification->cover_image);
            }

            // Transform provider if present
            if (isset($course->provider)) {
                $course->provider = $this->transformProvider($course->provider);
            }

            // Transform reviews user profile photos
            if (isset($course->reviews) && is_iterable($course->reviews)) {
                foreach ($course->reviews as $review) {
                    if (isset($review->user) && isset($review->user->profile_photo_path)) {
                        $review->user->profile_photo_url = $this->getFullUrl($review->user->profile_photo_path);
                    }
                }
            }
        }

        return $course;
    }

    /**
     * Get list of training providers with filters
     * 
     * Returns a paginated list of active training providers with optional filtering and sorting.
     * Supports filtering by country, certification type, verification status, rating, and search by name.
     * 
     * @param Request $request
     * @return JsonResponse
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

        // Transform providers to include full image URLs
        $transformedProviders = collect($providers->items())->map(function ($provider) {
            return $this->transformProvider($provider);
        });

        return response()->json([
            'status' => true,
            'message' => 'Training providers retrieved successfully',
            'data' => $transformedProviders,
            'meta' => [
                'current_page' => $providers->currentPage(),
                'last_page' => $providers->lastPage(),
                'per_page' => $providers->perPage(),
                'total' => $providers->total(),
            ],
        ]);
    }

    /**
     * Get detailed information about a specific training provider
     * 
     * Returns comprehensive details including active courses, locations, schedules, and gallery.
     * 
     * @param string $slug Provider slug identifier
     * @return JsonResponse
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

        // Transform provider to include full image URLs
        $provider = $this->transformProvider($provider);

        return response()->json([
            'status' => true,
            'message' => 'Provider details retrieved successfully',
            'data' => $provider,
        ]);
    }

    /**
     * Get list of training courses with advanced filters
     * 
     * Returns a paginated list of active courses with filtering options including:
     * certification, category, provider, location, format, price range, duration, and search.
     * 
     * @param Request $request
     * @return JsonResponse
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

        // Transform courses to include full image URLs
        $transformedCourses = collect($courses->items())->map(function ($course) {
            return $this->transformCourse($course);
        });

        return response()->json([
            'status' => true,
            'message' => 'Courses retrieved successfully',
            'data' => $transformedCourses,
            'meta' => [
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
            ],
        ]);
    }

    /**
     * Get detailed information about a specific course
     * 
     * Returns comprehensive course details including certification info, provider details,
     * locations, upcoming schedules, and reviews. Automatically increments view count.
     * 
     * @param int $id Course ID
     * @return JsonResponse
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

        // Transform course to include full image URLs
        $course = $this->transformCourse($course);

        return response()->json([
            'status' => true,
            'message' => 'Course details retrieved successfully',
            'data' => $course,
        ]);
    }

    /**
     * Get list of available certifications
     * 
     * Returns all certifications with optional filtering by category and search functionality.
     * 
     * @param Request $request
     * @return JsonResponse
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

        $certifications = $query->with(['category', 'providers'])->orderBy('name')->get();

        // Transform certifications to include full image URLs
        $transformedCertifications = $certifications->map(function ($certification) {
            if ($certification->cover_image) {
                $certification->cover_image_url = $this->getFullUrl($certification->cover_image);
            }
            return $certification;
        });

        return response()->json([
            'status' => true,
            'message' => 'Certifications retrieved successfully',
            'data' => $transformedCertifications,
        ]);
    }

    /**
     * Get list of certification categories
     * 
     * Returns all certification categories with the count of certifications in each category.
     * 
     * @return JsonResponse
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
     * Get available filter options for courses and providers
     * 
     * Returns all available filter options including categories, certifications,
     * course formats, countries, and price range. Useful for building filter UIs.
     * 
     * @return JsonResponse
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
     * Get upcoming course schedules
     * 
     * Returns a paginated list of upcoming course schedules that are not cancelled or full.
     * Supports filtering by certification, country, month, and year.
     * 
     * @param Request $request
     * @return JsonResponse
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

        // Transform schedules to include full image URLs for provider courses
        $transformedSchedules = collect($schedules->items())->map(function ($schedule) {
            if (isset($schedule->providerCourse)) {
                $schedule->providerCourse = $this->transformCourse($schedule->providerCourse);
            }
            return $schedule;
        });

        return response()->json([
            'status' => true,
            'message' => 'Schedules retrieved successfully',
            'data' => $transformedSchedules,
            'meta' => [
                'current_page' => $schedules->currentPage(),
                'last_page' => $schedules->lastPage(),
                'per_page' => $schedules->perPage(),
                'total' => $schedules->total(),
            ],
        ]);
    }
}

