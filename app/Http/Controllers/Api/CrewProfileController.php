<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Crew Profile",
 *     description="Crew profile management endpoints"
 * )
 */
class CrewProfileController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/crew-profile/update",
     *     summary="Update crew profile information",
     *     tags={"Crew Profile"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="years_experience", type="integer", example=5, description="Years of experience (0-100)"),
     *             @OA\Property(property="current_yacht", type="string", example="Yacht Name"),
     *             @OA\Property(property="languages", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="certifications", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="specializations", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="interests", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="availability_status", type="string", enum={"available", "busy", "looking_for_work", "on_leave"}, example="available"),
     *             @OA\Property(property="availability_message", type="string", example="Available for work", maxLength=500),
     *             @OA\Property(property="looking_to_meet", type="boolean", example=false),
     *             @OA\Property(property="looking_for_work", type="boolean", example=true),
     *             @OA\Property(property="sea_service_time_months", type="integer", example=60, minimum=0),
     *             @OA\Property(property="previous_yachts", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Profile updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'years_experience' => 'nullable|integer|min:0|max:100',
            'current_yacht' => 'nullable|string|max:255',
            'languages' => 'nullable|array',
            'certifications' => 'nullable|array',
            'specializations' => 'nullable|array',
            'interests' => 'nullable|array',
            'availability_status' => 'nullable|in:available,busy,looking_for_work,on_leave',
            'availability_message' => 'nullable|string|max:500',
            'looking_to_meet' => 'nullable|boolean',
            'looking_for_work' => 'nullable|boolean',
            'sea_service_time_months' => 'nullable|integer|min:0',
            'previous_yachts' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = auth('api')->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }
        
        $user->update($request->only([
            'years_experience',
            'current_yacht',
            'languages',
            'certifications',
            'specializations',
            'interests',
            'availability_status',
            'availability_message',
            'looking_to_meet',
            'looking_for_work',
            'sea_service_time_months',
            'previous_yachts',
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => $user->fresh(),
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/crew-profile/privacy",
     *     summary="Update privacy settings",
     *     tags={"Crew Profile"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="location_privacy", type="string", enum={"exact", "approximate", "city_only", "hidden"}, example="city_only"),
     *             @OA\Property(property="share_location", type="boolean", example=true),
     *             @OA\Property(property="auto_hide_at_sea", type="boolean", example=false),
     *             @OA\Property(property="visibility", type="string", enum={"everyone", "connections_only", "verified_only", "invisible"}, example="everyone"),
     *             @OA\Property(property="show_in_discovery", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Privacy settings updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Privacy settings updated"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function updatePrivacySettings(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'location_privacy' => 'nullable|in:exact,approximate,city_only,hidden',
            'share_location' => 'nullable|boolean',
            'auto_hide_at_sea' => 'nullable|boolean',
            'visibility' => 'nullable|in:everyone,connections_only,verified_only,invisible',
            'show_in_discovery' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = auth('api')->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }
        
        $user->update($request->only([
            'location_privacy',
            'share_location',
            'auto_hide_at_sea',
            'visibility',
            'show_in_discovery',
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Privacy settings updated',
            'data' => [
                'location_privacy' => $user->location_privacy,
                'share_location' => $user->share_location,
                'auto_hide_at_sea' => $user->auto_hide_at_sea,
                'visibility' => $user->visibility,
                'show_in_discovery' => $user->show_in_discovery,
            ],
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/crew-profile",
     *     summary="Get authenticated user's crew profile",
     *     tags={"Crew Profile"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Profile retrieved"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="first_name", type="string", example="John"),
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                 @OA\Property(property="profile_photo_url", type="string", nullable=true),
     *                 @OA\Property(property="position", type="string", example="Captain"),
     *                 @OA\Property(property="years_experience", type="integer", example=5),
     *                 @OA\Property(property="current_yacht", type="string", example="Yacht Name"),
     *                 @OA\Property(property="languages", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="certifications", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="specializations", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="interests", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="availability_status", type="string", example="available"),
     *                 @OA\Property(property="availability_message", type="string", example="Available for work"),
     *                 @OA\Property(property="looking_to_meet", type="boolean", example=false),
     *                 @OA\Property(property="looking_for_work", type="boolean", example=true),
     *                 @OA\Property(property="sea_service_time_months", type="integer", example=60),
     *                 @OA\Property(property="previous_yachts", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="rating", type="number", format="float", example=4.5),
     *                 @OA\Property(property="total_reviews", type="integer", example=20),
     *                 @OA\Property(property="nationality", type="string", example="British"),
     *                 @OA\Property(property="privacy", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function getProfile(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        
        $profile = [
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'profile_photo_url' => $user->profile_photo_url,
            'position' => $user->roles->pluck('name')->first(),
            'years_experience' => $user->years_experience,
            'current_yacht' => $user->current_yacht,
            'languages' => $user->languages ?? [],
            'certifications' => $user->certifications ?? [],
            'specializations' => $user->specializations ?? [],
            'interests' => $user->interests ?? [],
            'availability_status' => $user->availability_status,
            'availability_message' => $user->availability_message,
            'looking_to_meet' => $user->looking_to_meet,
            'looking_for_work' => $user->looking_for_work,
            'sea_service_time_months' => $user->sea_service_time_months,
            'previous_yachts' => $user->previous_yachts ?? [],
            'rating' => $user->rating,
            'total_reviews' => $user->total_reviews,
            'nationality' => $user->nationality,
            'privacy' => [
                'location_privacy' => $user->location_privacy,
                'share_location' => $user->share_location,
                'visibility' => $user->visibility,
                'show_in_discovery' => $user->show_in_discovery,
            ],
        ];

        return response()->json([
            'status' => true,
            'message' => 'Profile retrieved',
            'data' => $profile,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/crew-profile/{user}",
     *     summary="Get another user's public profile",
     *     tags={"Crew Profile"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Profile retrieved"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="first_name", type="string", example="John"),
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="profile_photo_url", type="string", nullable=true),
     *                 @OA\Property(property="position", type="string", example="Captain"),
     *                 @OA\Property(property="years_experience", type="integer", example=5),
     *                 @OA\Property(property="current_yacht", type="string", example="Yacht Name"),
     *                 @OA\Property(property="languages", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="nationality", type="string", example="British"),
     *                 @OA\Property(property="availability_status", type="string", example="available"),
     *                 @OA\Property(property="availability_message", type="string", example="Available for work"),
     *                 @OA\Property(property="rating", type="number", format="float", example=4.5),
     *                 @OA\Property(property="total_reviews", type="integer", example=20),
     *                 @OA\Property(property="is_online", type="boolean", example=true),
     *                 @OA\Property(property="last_seen_at", type="string", format="date-time", nullable=true),
     *                 @OA\Property(property="latitude", type="number", format="float", nullable=true),
     *                 @OA\Property(property="longitude", type="number", format="float", nullable=true),
     *                 @OA\Property(property="location_name", type="string", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied (connection required or profile not visible)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You must be connected to view this profile")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Profile not available",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Profile not available")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function getPublicProfile(User $user): JsonResponse
    {
        $currentUser = auth('api')->user();

        // Check if user is visible
        if ($user->visibility === 'invisible' && $user->id !== $currentUser->id) {
            return response()->json([
                'status' => false,
                'message' => 'Profile not available',
            ], 404);
        }

        // Check connection requirement
        if ($user->visibility === 'connections_only' && $user->id !== $currentUser->id) {
            $isConnected = \App\Models\UserConnection::where(function ($q) use ($currentUser, $user) {
                $q->where('user_id', $currentUser->id)
                    ->where('connected_user_id', $user->id)
                    ->where('status', 'accepted');
            })->orWhere(function ($q) use ($currentUser, $user) {
                $q->where('user_id', $user->id)
                    ->where('connected_user_id', $currentUser->id)
                    ->where('status', 'accepted');
            })->exists();

            if (!$isConnected) {
                return response()->json([
                    'status' => false,
                    'message' => 'You must be connected to view this profile',
                ], 403);
            }
        }

        $profile = [
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'profile_photo_url' => $user->profile_photo_url,
            'position' => $user->roles->pluck('name')->first(),
            'years_experience' => $user->years_experience,
            'current_yacht' => $user->current_yacht,
            'languages' => $user->languages ?? [],
            'nationality' => $user->nationality,
            'availability_status' => $user->availability_status,
            'availability_message' => $user->availability_message,
            'rating' => $user->rating,
            'total_reviews' => $user->total_reviews,
            'is_online' => $user->is_online,
            'last_seen_at' => $user->last_seen_at,
        ];

        // Add location if privacy allows
        if ($user->share_location && $user->location_privacy !== 'hidden') {
            if ($user->location_privacy === 'exact') {
                $profile['latitude'] = $user->latitude;
                $profile['longitude'] = $user->longitude;
            }
            $profile['location_name'] = $user->location_name;
        }

        return response()->json([
            'status' => true,
            'message' => 'Profile retrieved',
            'data' => $profile,
        ], 200);
    }
}
