<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponseTrait;
use App\Models\JobPost;
use App\Models\JobApplication;
use App\Models\SavedJobPost;
use App\Services\JobBoard\JobMatchingService;
use App\Services\JobBoard\JobNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class JobBoardController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get list of job posts (Public endpoint)
     */
    public function index(Request $request): JsonResponse
    {
        $query = JobPost::published()
            ->with(['user:id,first_name,last_name', 'yacht:id,name']);

        // Filter by job type
        if ($request->has('job_type') && in_array($request->job_type, ['permanent', 'temporary'])) {
            $query->where('job_type', $request->job_type);
        }

        // Filter by position
        if ($request->has('position')) {
            $query->where(function($q) use ($request) {
                $q->where('position_title', 'like', '%' . $request->position . '%')
                    ->orWhere('position_level', 'like', '%' . $request->position . '%');
            });
        }

        // Filter by location
        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filter by department
        if ($request->has('department') && in_array($request->department, ['deck', 'interior', 'engineering', 'galley', 'other'])) {
            $query->where('department', $request->department);
        }

        // Search
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('position_title', 'like', '%' . $request->search . '%')
                    ->orWhere('about_position', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }

        // Salary filter
        if ($request->has('salary_min')) {
            $query->where(function($q) use ($request) {
                $q->where('salary_max', '>=', $request->salary_min)
                    ->orWhere('salary_min', '>=', $request->salary_min);
            });
        }
        if ($request->has('salary_max')) {
            $query->where(function($q) use ($request) {
                $q->where('salary_min', '<=', $request->salary_max)
                    ->orWhere('salary_max', '<=', $request->salary_max);
            });
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'salary_high':
                $query->orderBy('salary_max', 'desc');
                break;
            case 'salary_low':
                $query->orderBy('salary_min', 'asc');
                break;
            case 'match_score':
                // For match score, we'd need to calculate it per user, so default to newest
                $query->latest('published_at');
                break;
            case 'newest':
            default:
                $query->latest('published_at');
                break;
        }

        $perPage = $request->input('per_page', 15);
        $jobs = $query->paginate($perPage);

        // Transform data
        $jobs->getCollection()->transform(function ($job) {
            return [
                'id' => $job->id,
                'job_type' => $job->job_type,
                'position_title' => $job->position_title,
                'department' => $job->department,
                'location' => $job->location,
                'salary_min' => $job->salary_min,
                'salary_max' => $job->salary_max,
                'salary_currency' => $job->salary_currency,
                'start_date' => $job->start_date?->format('Y-m-d'),
                'views_count' => $job->views_count,
                'applications_count' => $job->applications_count,
                'published_at' => $job->published_at?->toISOString(),
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Job posts retrieved successfully',
            'data' => $jobs->items(),
            'meta' => [
                'current_page' => $jobs->currentPage(),
                'per_page' => $jobs->perPage(),
                'total' => $jobs->total(),
                'last_page' => $jobs->lastPage(),
                'from' => $jobs->firstItem(),
                'to' => $jobs->lastItem(),
            ],
        ]);
    }

    /**
     * Create a new job post
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'job_type' => 'required|in:permanent,temporary',
            'position_title' => 'required|string|max:255',
            'department' => 'required|in:deck,interior,engineering,galley,other',
            'position_level' => 'nullable|string|max:255',
            'yacht_id' => 'nullable|exists:yachts,id',
            'location' => 'nullable|string|max:255',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'salary_currency' => 'nullable|string|max:3',
            'start_date' => 'nullable|date',
            'about_position' => 'nullable|string',
            'public_post' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse(new ValidationException($validator));
        }

        $user = Auth::user();
        
        // Check if user can post jobs
        if ($user->hasRole('super_admin')) {
            return $this->errorResponse('Admins cannot post jobs as regular users.', 403);
        }

        $jobPost = DB::transaction(function () use ($request, $user) {
            $jobData = $request->only([
                'job_type', 'position_title', 'department', 'position_level',
                'yacht_id', 'location', 'salary_min', 'salary_max', 'salary_currency',
                'start_date', 'about_position', 'public_post'
            ]);
            
            $jobData['user_id'] = $user->id;
            $jobData['status'] = 'draft';
            
            $jobPost = JobPost::create($jobData);
            
            return $jobPost;
        });

        return $this->successResponse($jobPost, 'Job post created successfully', 201);
    }

    /**
     * Get job post details (Public endpoint)
     */
    public function show($id): JsonResponse
    {
        $job = JobPost::with(['user:id,first_name,last_name', 'yacht', 'screeningQuestions'])
            ->findOrFail($id);

        // Increment views
        $job->incrementViews();

        $data = [
            'id' => $job->id,
            'job_type' => $job->job_type,
            'position_title' => $job->position_title,
            'department' => $job->department,
            'position_level' => $job->position_level,
            'location' => $job->location,
            'salary_min' => $job->salary_min,
            'salary_max' => $job->salary_max,
            'salary_currency' => $job->salary_currency,
            'about_position' => $job->about_position,
            'responsibilities' => $job->responsibilities,
            'required_certifications' => $job->required_certifications,
            'views_count' => $job->views_count,
            'applications_count' => $job->applications_count,
            'published_at' => $job->published_at?->toISOString(),
            'screening_questions' => $job->screeningQuestions->map(function ($q) {
                return [
                    'id' => $q->id,
                    'question' => $q->question,
                    'is_required' => $q->is_required,
                ];
            }),
        ];

        return $this->successResponse($data, 'Job post retrieved successfully');
    }

    /**
     * Update job post
     */
    public function update(Request $request, $id): JsonResponse
    {
        $job = JobPost::findOrFail($id);

        // Check ownership
        if ($job->user_id !== Auth::id()) {
            return $this->unauthorizedResponse('You are not authorized to update this job post.');
        }

        $validator = Validator::make($request->all(), [
            'position_title' => 'sometimes|string|max:255',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'about_position' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse(new ValidationException($validator));
        }

        $job->update($request->only([
            'position_title', 'salary_min', 'salary_max', 'about_position'
        ]));

        return $this->successResponse($job, 'Job post updated successfully');
    }

    /**
     * Apply for a job
     */
    public function apply(Request $request, $id): JsonResponse
    {
        $job = JobPost::findOrFail($id);

        $user = Auth::user();

        // Check if already applied
        $existing = JobApplication::where('user_id', $user->id)
            ->where('job_post_id', $id)
            ->first();

        if ($existing) {
            return $this->errorResponse('You have already applied for this position.', 400);
        }

        $validator = Validator::make($request->all(), [
            'cover_message' => 'nullable|string|max:2000',
            'screening_responses' => 'nullable|array',
            'attached_documents' => 'nullable|array',
            'attached_documents.*' => 'integer|exists:documents,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse(new ValidationException($validator));
        }

        $application = DB::transaction(function () use ($request, $job, $user) {
            // Calculate match score
            $matchingService = app(JobMatchingService::class);
            $matchScore = $matchingService->calculateMatchScore($user, $job);

            // Create application
            $application = JobApplication::create([
                'job_post_id' => $job->id,
                'user_id' => $user->id,
                'status' => 'submitted',
                'match_score' => $matchScore,
                'screening_responses' => $request->input('screening_responses', []),
                'cover_message' => $request->input('cover_message'),
                'attached_documents' => $request->input('attached_documents', []),
                'submitted_at' => now(),
            ]);

            // Update job post application count
            $job->incrementApplications();

            // Notify captain
            try {
                app(JobNotificationService::class)->notifyNewApplication($application);
            } catch (\Exception $e) {
                Log::error('Error notifying captain: ' . $e->getMessage());
            }

            return $application;
        });

        return $this->successResponse([
            'id' => $application->id,
            'job_post_id' => $application->job_post_id,
            'status' => $application->status,
            'match_score' => $application->match_score,
            'submitted_at' => $application->submitted_at->toISOString(),
        ], 'Application submitted successfully', 201);
    }

    /**
     * Get applications for a job
     */
    public function getApplications(Request $request, $id): JsonResponse
    {
        $job = JobPost::findOrFail($id);

        // Check ownership
        if ($job->user_id !== Auth::id()) {
            return $this->unauthorizedResponse('You are not authorized to view applications for this job post.');
        }

        $query = JobApplication::where('job_post_id', $id)
            ->with(['user:id,first_name,last_name,email']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->input('per_page', 15);
        $applications = $query->paginate($perPage);

        $applications->getCollection()->transform(function ($app) {
            return [
                'id' => $app->id,
                'user_id' => $app->user_id,
                'status' => $app->status,
                'match_score' => $app->match_score,
                'cover_message' => $app->cover_message,
                'submitted_at' => $app->submitted_at?->toISOString(),
                'user' => $app->user ? [
                    'id' => $app->user->id,
                    'first_name' => $app->user->first_name,
                    'last_name' => $app->user->last_name,
                ] : null,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Applications retrieved successfully',
            'data' => $applications->items(),
            'meta' => [
                'current_page' => $applications->currentPage(),
                'per_page' => $applications->perPage(),
                'total' => $applications->total(),
                'last_page' => $applications->lastPage(),
            ],
        ]);
    }

    /**
     * Get my job applications
     */
    public function getMyApplications(Request $request): JsonResponse
    {
        $query = JobApplication::where('user_id', Auth::id())
            ->with(['jobPost:id,position_title,location']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->input('per_page', 15);
        $applications = $query->latest('submitted_at')->paginate($perPage);

        $applications->getCollection()->transform(function ($app) {
            return [
                'id' => $app->id,
                'job_post_id' => $app->job_post_id,
                'status' => $app->status,
                'match_score' => $app->match_score,
                'submitted_at' => $app->submitted_at?->toISOString(),
                'job_post' => $app->jobPost ? [
                    'id' => $app->jobPost->id,
                    'position_title' => $app->jobPost->position_title,
                    'location' => $app->jobPost->location,
                ] : null,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Applications retrieved successfully',
            'data' => $applications->items(),
            'meta' => [
                'current_page' => $applications->currentPage(),
                'per_page' => $applications->perPage(),
                'total' => $applications->total(),
                'last_page' => $applications->lastPage(),
            ],
        ]);
    }

    /**
     * Get application details
     */
    public function getApplication($id): JsonResponse
    {
        $application = JobApplication::with(['jobPost', 'user'])
            ->findOrFail($id);

        // Check authorization
        $user = Auth::user();
        if ($application->user_id !== $user->id && $application->jobPost->user_id !== $user->id) {
            return $this->unauthorizedResponse('You are not authorized to view this application.');
        }

        $data = [
            'id' => $application->id,
            'job_post_id' => $application->job_post_id,
            'status' => $application->status,
            'match_score' => $application->match_score,
            'cover_message' => $application->cover_message,
            'screening_responses' => $application->screening_responses,
            'submitted_at' => $application->submitted_at?->toISOString(),
        ];

        return $this->successResponse($data, 'Application details retrieved successfully');
    }

    /**
     * Update application status
     */
    public function updateApplicationStatus(Request $request, $id): JsonResponse
    {
        $application = JobApplication::with('jobPost')->findOrFail($id);

        // Check authorization - only job owner can update status
        if ($application->jobPost->user_id !== Auth::id()) {
            return $this->unauthorizedResponse('You are not authorized to update this application status.');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:viewed,reviewed,shortlisted,interview_requested,interview_scheduled,interviewed,offer_sent,hired,declined',
            'captain_notes' => 'nullable|string',
            'captain_rating' => 'nullable|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse(new ValidationException($validator));
        }

        $status = $request->input('status');
        $updateData = [
            'status' => $status,
        ];

        // Set timestamp based on status
        $timestampField = $status . '_at';
        if (in_array($timestampField, ['viewed_at', 'reviewed_at', 'shortlisted_at', 'interview_requested_at', 
            'interview_scheduled_at', 'interviewed_at', 'offer_sent_at', 'hired_at', 'declined_at'])) {
            $updateData[$timestampField] = now();
        }

        if ($request->has('captain_notes')) {
            $updateData['captain_notes'] = $request->input('captain_notes');
        }

        if ($request->has('captain_rating')) {
            $updateData['captain_rating'] = $request->input('captain_rating');
        }

        $application->update($updateData);

        return $this->successResponse($application, 'Application status updated successfully');
    }

    /**
     * Withdraw application
     */
    public function withdrawApplication(Request $request, $id): JsonResponse
    {
        $application = JobApplication::findOrFail($id);

        // Check authorization
        if ($application->user_id !== Auth::id()) {
            return $this->unauthorizedResponse('You are not authorized to withdraw this application.');
        }

        $application->withdraw($request->input('withdrawal_reason'));

        return $this->successResponse(null, 'Application withdrawn successfully');
    }

    /**
     * Save a job post
     */
    public function saveJob($id): JsonResponse
    {
        $job = JobPost::findOrFail($id);
        $user = Auth::user();

        $existing = SavedJobPost::where('user_id', $user->id)
            ->where('job_post_id', $id)
            ->first();

        if ($existing) {
            return $this->errorResponse('Job post is already saved.', 400);
        }

        SavedJobPost::create([
            'user_id' => $user->id,
            'job_post_id' => $id,
        ]);

        $job->increment('saved_count');

        return $this->successResponse(null, 'Job post saved successfully');
    }

    /**
     * Unsave a job post
     */
    public function unsaveJob($id): JsonResponse
    {
        $job = JobPost::findOrFail($id);
        $user = Auth::user();

        $saved = SavedJobPost::where('user_id', $user->id)
            ->where('job_post_id', $id)
            ->first();

        if (!$saved) {
            return $this->errorResponse('Job post is not saved.', 400);
        }

        $saved->delete();
        $job->decrement('saved_count');

        return $this->successResponse(null, 'Job post unsaved successfully');
    }

    /**
     * Get saved job posts
     */
    public function getSavedJobs(Request $request): JsonResponse
    {
        $query = SavedJobPost::where('user_id', Auth::id())
            ->with(['jobPost:id,position_title,location']);

        $perPage = $request->input('per_page', 15);
        $savedJobs = $query->latest()->paginate($perPage);

        $savedJobs->getCollection()->transform(function ($saved) {
            return [
                'id' => $saved->jobPost->id,
                'position_title' => $saved->jobPost->position_title,
                'location' => $saved->jobPost->location,
                'saved_at' => $saved->created_at->toISOString(),
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Saved jobs retrieved successfully',
            'data' => $savedJobs->items(),
            'meta' => [
                'current_page' => $savedJobs->currentPage(),
                'per_page' => $savedJobs->perPage(),
                'total' => $savedJobs->total(),
                'last_page' => $savedJobs->lastPage(),
            ],
        ]);
    }

    /**
     * Publish a job post
     */
    public function publishJob($id): JsonResponse
    {
        $job = JobPost::findOrFail($id);

        // Check ownership
        if ($job->user_id !== Auth::id()) {
            return $this->unauthorizedResponse('You are not authorized to publish this job post.');
        }

        $job->publish();

        return $this->successResponse($job, 'Job post published successfully');
    }

    /**
     * Mark job post as filled
     */
    public function markFilled($id): JsonResponse
    {
        $job = JobPost::findOrFail($id);

        // Check ownership
        if ($job->user_id !== Auth::id()) {
            return $this->unauthorizedResponse('You are not authorized to mark this job post as filled.');
        }

        $job->markAsFilled();

        return $this->successResponse($job, 'Job post marked as filled successfully');
    }

    /**
     * Get messages for a job
     */
    public function getMessages(Request $request, $id): JsonResponse
    {
        $job = JobPost::findOrFail($id);
        $user = Auth::user();

        // Check authorization - user must be job owner or have an application
        $hasApplication = JobApplication::where('job_post_id', $id)
            ->where('user_id', $user->id)
            ->exists();

        if ($job->user_id !== $user->id && !$hasApplication) {
            return $this->unauthorizedResponse('You are not authorized to view messages for this job post.');
        }

        $query = $job->messages()
            ->with(['sender:id,first_name,last_name']);

        if ($request->has('application_id')) {
            $query->where('application_id', $request->application_id);
        }

        $messages = $query->latest()->get();

        $messages->transform(function ($msg) {
            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'sender_id' => $msg->sender_id,
                'created_at' => $msg->created_at->toISOString(),
                'sender' => $msg->sender ? [
                    'id' => $msg->sender->id,
                    'first_name' => $msg->sender->first_name,
                    'last_name' => $msg->sender->last_name,
                ] : null,
            ];
        });

        return $this->successResponse($messages->toArray(), 'Messages retrieved successfully');
    }

    /**
     * Get ratings for a job
     */
    public function getRatings($id): JsonResponse
    {
        $job = JobPost::findOrFail($id);

        $ratings = $job->ratings()
            ->with(['user:id,first_name,last_name'])
            ->latest()
            ->get();

        $ratings->transform(function ($rating) {
            return [
                'id' => $rating->id,
                'rating' => $rating->rating,
                'comment' => $rating->comment,
                'user' => $rating->user ? [
                    'id' => $rating->user->id,
                    'first_name' => $rating->user->first_name,
                    'last_name' => $rating->user->last_name,
                ] : null,
                'created_at' => $rating->created_at->toISOString(),
            ];
        });

        return $this->successResponse($ratings->toArray(), 'Ratings retrieved successfully');
    }
}
