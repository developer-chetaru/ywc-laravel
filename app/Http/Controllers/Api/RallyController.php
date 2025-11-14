<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rally;
use App\Models\RallyAttendee;
use App\Models\RallyComment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RallyController extends Controller
{
    /**
     * Create a new rally
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:social,active,cultural,professional,learning,celebration',
            'privacy' => 'required|in:public,private,invite_only',
            'start_date' => 'required|date|after:now',
            'end_date' => 'nullable|date|after:start_date',
            'location_name' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string',
            'meeting_point' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'cost' => 'nullable|numeric|min:0',
            'what_to_bring' => 'nullable|string',
            'requirements' => 'nullable|string',
            'contact_info' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = auth('api')->user();

        $rally = Rally::create([
            'organizer_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'privacy' => $request->privacy,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'location_name' => $request->location_name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'meeting_point' => $request->meeting_point,
            'max_participants' => $request->max_participants,
            'cost' => $request->cost ?? 0,
            'what_to_bring' => $request->what_to_bring,
            'requirements' => $request->requirements,
            'contact_info' => $request->contact_info,
            'status' => 'published',
        ]);

        // Auto-add organizer as going
        RallyAttendee::create([
            'rally_id' => $rally->id,
            'user_id' => $user->id,
            'rsvp_status' => 'going',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Rally created successfully',
            'data' => $rally->load('organizer:id,first_name,last_name,email'),
        ], 201);
    }

    /**
     * Discover rallies
     */
    public function discover(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.1|max:1000',
            'type' => 'nullable|in:social,active,cultural,professional,learning,celebration',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after:date_from',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = auth('api')->user();
        $query = Rally::where('status', 'published')
            ->where('privacy', 'public')
            ->where('start_date', '>=', now());

        // Location-based filtering
        if ($request->latitude && $request->longitude) {
            $radius = $request->radius ?? 50; // Default 50km
            $query->select('rallies.*')
                ->selectRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                    [$request->latitude, $request->longitude, $request->latitude]
                )
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->having('distance', '<=', $radius)
                ->orderBy('distance');
        } else {
            $query->orderBy('start_date');
        }

        // Filter by type
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->where('start_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->where('start_date', '<=', $request->date_to);
        }

        $limit = $request->limit ?? 20;
        $rallies = $query->with(['organizer:id,first_name,last_name,email', 'goingAttendees'])
            ->limit($limit)
            ->get()
            ->map(function ($rally) use ($user) {
                $goingCount = $rally->goingAttendees->count();
                $userRsvp = RallyAttendee::where('rally_id', $rally->id)
                    ->where('user_id', $user->id)
                    ->first();

                return [
                    'id' => $rally->id,
                    'title' => $rally->title,
                    'description' => $rally->description,
                    'type' => $rally->type,
                    'start_date' => $rally->start_date,
                    'end_date' => $rally->end_date,
                    'location_name' => $rally->location_name,
                    'distance' => isset($rally->distance) ? round($rally->distance, 2) : null,
                    'cost' => $rally->cost,
                    'max_participants' => $rally->max_participants,
                    'going_count' => $goingCount,
                    'organizer' => $rally->organizer,
                    'user_rsvp' => $userRsvp ? $userRsvp->rsvp_status : null,
                    'rating' => $rally->rating,
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Rallies retrieved',
            'data' => $rallies,
        ], 200);
    }

    /**
     * Get rally details
     */
    public function show(Rally $rally): JsonResponse
    {
        $user = auth('api')->user();

        // Check privacy
        if ($rally->privacy === 'private' && $rally->organizer_id !== $user->id) {
            $hasAccess = RallyAttendee::where('rally_id', $rally->id)
                ->where('user_id', $user->id)
                ->exists();
            
            if (!$hasAccess) {
                return response()->json([
                    'status' => false,
                    'message' => 'This rally is private',
                ], 403);
            }
        }

        $rally->incrementViews();

        $rally->load([
            'organizer:id,first_name,last_name,email,profile_photo_path',
            'goingAttendees.user:id,first_name,last_name,email,profile_photo_path',
            'maybeAttendees.user:id,first_name,last_name,email,profile_photo_path',
            'comments.user:id,first_name,last_name,email,profile_photo_path',
        ]);

        $userRsvp = RallyAttendee::where('rally_id', $rally->id)
            ->where('user_id', $user->id)
            ->first();

        return response()->json([
            'status' => true,
            'message' => 'Rally details retrieved',
            'data' => [
                'rally' => $rally,
                'user_rsvp' => $userRsvp,
            ],
        ], 200);
    }

    /**
     * RSVP to rally
     */
    public function rsvp(Request $request, Rally $rally): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'rsvp_status' => 'required|in:going,maybe,cant_go,interested',
            'guests_count' => 'nullable|integer|min:0',
            'comment' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = auth('api')->user();

        // Check max participants if going
        if ($request->rsvp_status === 'going' && $rally->max_participants) {
            $goingCount = RallyAttendee::where('rally_id', $rally->id)
                ->where('rsvp_status', 'going')
                ->count();
            
            if ($goingCount >= $rally->max_participants) {
                return response()->json([
                    'status' => false,
                    'message' => 'Rally is full',
                ], 400);
            }
        }

        $attendee = RallyAttendee::updateOrCreate(
            [
                'rally_id' => $rally->id,
                'user_id' => $user->id,
            ],
            [
                'rsvp_status' => $request->rsvp_status,
                'guests_count' => $request->guests_count ?? 0,
                'comment' => $request->comment,
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'RSVP updated',
            'data' => $attendee->load('user:id,first_name,last_name,email'),
        ], 200);
    }

    /**
     * Add comment to rally
     */
    public function addComment(Request $request, Rally $rally): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:rally_comments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = auth('api')->user();

        $comment = RallyComment::create([
            'rally_id' => $rally->id,
            'user_id' => $user->id,
            'parent_id' => $request->parent_id,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Comment added',
            'data' => $comment->load('user:id,first_name,last_name,email,profile_photo_path'),
        ], 201);
    }
}
