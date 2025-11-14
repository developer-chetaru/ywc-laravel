<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CrewProfileController extends Controller
{
    /**
     * Update crew profile information
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
     * Update privacy settings
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
     * Get user's crew profile
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
     * Get another user's public profile
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
