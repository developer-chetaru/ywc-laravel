<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserConnection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CrewDiscoveryController extends Controller
{
    /**
     * Update user's location
     */
    public function updateLocation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'location_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
        
        try {
            // Automatically enable location sharing when user sets location
            $user->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'location_name' => $request->location_name,
            'location_updated_at' => now(),
            'share_location' => true, // Auto-enable when setting location
            'show_in_discovery' => true, // Auto-enable discovery
        ]);

            return response()->json([
                'status' => true,
                'message' => 'Location updated successfully',
                'data' => [
                    'latitude' => $user->latitude,
                    'longitude' => $user->longitude,
                    'location_name' => $user->location_name,
                    'location_updated_at' => $user->location_updated_at,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update location: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Discover nearby crew members
     */
    public function discoverNearby(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.1|max:1000', // in kilometers
            'distance' => 'nullable|in:1,5,10,50,100,1000',
            'position' => 'nullable|string',
            'experience_level' => 'nullable|in:new,intermediate,experienced,senior',
            'status' => 'nullable|in:online,available,looking_for_work',
            'languages' => 'nullable|array',
            'interests' => 'nullable|array',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
        
        try {
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $radius = $request->distance ?? $request->radius ?? 10; // Default 10km

        // Convert distance preset to radius
        $distanceMap = [1 => 1, 5 => 5, 10 => 10, 50 => 50, 100 => 100, 1000 => 1000];
        if ($request->distance && isset($distanceMap[$request->distance])) {
            $radius = $distanceMap[$request->distance];
        }

        // Build query for nearby users
        $query = User::select('users.*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$latitude, $longitude, $latitude]
            )
            ->where('users.id', '!=', $user->id)
            ->where('users.share_location', true)
            ->where('users.show_in_discovery', true)
            ->whereNotNull('users.latitude')
            ->whereNotNull('users.longitude')
            ->having('distance', '<=', $radius)
            ->orderBy('distance');

        // Apply visibility filters
        $query->where(function ($q) use ($user) {
            $q->where('users.visibility', 'everyone')
                ->orWhere(function ($subQ) use ($user) {
                    $subQ->where('users.visibility', 'connections_only')
                        ->whereExists(function ($existsQuery) use ($user) {
                            $existsQuery->select(DB::raw(1))
                                ->from('user_connections')
                                ->whereColumn('user_connections.connected_user_id', 'users.id')
                                ->where('user_connections.user_id', $user->id)
                                ->where('user_connections.status', 'accepted');
                        });
                })
                ->orWhere(function ($subQ) use ($user) {
                    $subQ->where('users.visibility', 'verified_only')
                        ->where('users.is_active', true);
                });
        });

        // Filter by position/role
        if ($request->position) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->position . '%');
            });
        }

        // Filter by experience level
        if ($request->experience_level) {
            $experienceMap = [
                'new' => [0, 2],
                'intermediate' => [2, 5],
                'experienced' => [5, 10],
                'senior' => [10, 999],
            ];
            
            if (isset($experienceMap[$request->experience_level])) {
                [$min, $max] = $experienceMap[$request->experience_level];
                $query->whereBetween('years_experience', [$min, $max]);
            }
        }

        // Filter by status
        if ($request->status === 'online') {
            $query->where('is_online', true);
        } elseif ($request->status === 'available') {
            $query->where('availability_status', 'available')
                ->orWhere('looking_to_meet', true);
        } elseif ($request->status === 'looking_for_work') {
            $query->where('looking_for_work', true);
        }

        // Filter by languages
        if ($request->languages && is_array($request->languages)) {
            foreach ($request->languages as $language) {
                $query->whereJsonContains('languages', $language);
            }
        }

        // Filter by interests
        if ($request->interests && is_array($request->interests)) {
            foreach ($request->interests as $interest) {
                $query->whereJsonContains('interests', $interest);
            }
        }

        $limit = $request->limit ?? 50;
        $nearbyCrew = $query->limit($limit)->get();

        // Format response with additional data
        $formattedCrew = $nearbyCrew->map(function ($crew) use ($user) {
            $distance = $crew->distance ?? $user->getDistanceTo($crew->latitude, $crew->longitude);
            
            // Check connection status
            $connection = UserConnection::where(function ($q) use ($user, $crew) {
                $q->where('user_id', $user->id)
                    ->where('connected_user_id', $crew->id);
            })->orWhere(function ($q) use ($user, $crew) {
                $q->where('user_id', $crew->id)
                    ->where('connected_user_id', $user->id);
            })->first();

            return [
                'id' => $crew->id,
                'name' => $crew->name,
                'first_name' => $crew->first_name,
                'last_name' => $crew->last_name,
                'email' => $crew->email,
                'profile_photo_url' => $crew->profile_photo_url,
                'position' => $crew->roles->pluck('name')->first(),
                'years_experience' => $crew->years_experience,
                'current_yacht' => $crew->current_yacht,
                'languages' => $crew->languages ?? [],
                'nationality' => $crew->nationality,
                'availability_status' => $crew->availability_status,
                'availability_message' => $crew->availability_message,
                'looking_to_meet' => $crew->looking_to_meet,
                'looking_for_work' => $crew->looking_for_work,
                'is_online' => $crew->is_online,
                'last_seen_at' => $crew->last_seen_at,
                'rating' => $crew->rating,
                'total_reviews' => $crew->total_reviews,
                'distance' => round($distance, 2),
                'location_name' => $crew->location_name,
                'connection_status' => $connection ? $connection->status : null,
                'latitude' => $crew->location_privacy === 'exact' ? $crew->latitude : null,
                'longitude' => $crew->location_privacy === 'exact' ? $crew->longitude : null,
            ];
        });

            return response()->json([
                'status' => true,
                'message' => 'Nearby crew found',
                'data' => [
                    'count' => $formattedCrew->count(),
                    'radius' => $radius,
                    'crew' => $formattedCrew,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to discover nearby crew: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get online crew members
     */
    public function getOnlineCrew(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
        
        try {
            $onlineCrew = User::where('id', '!=', $user->id)
            ->where('is_online', true)
            ->where('show_in_discovery', true)
            ->where('share_location', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with('roles')
            ->limit(50)
            ->get()
            ->map(function ($crew) use ($user) {
                $distance = $user->latitude && $user->longitude 
                    ? $user->getDistanceTo($crew->latitude, $crew->longitude)
                    : null;

                return [
                    'id' => $crew->id,
                    'name' => $crew->name,
                    'position' => $crew->roles->pluck('name')->first(),
                    'distance' => $distance ? round($distance, 2) : null,
                    'location_name' => $crew->location_name,
                    'availability_message' => $crew->availability_message,
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'Online crew retrieved',
                'data' => $onlineCrew,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve online crew: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all crew locations (for map view)
     */
    public function getAllCrewLocations(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
        
        $validator = Validator::make($request->all(), [
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.1|max:1000',
            'position' => 'nullable|string',
            'experience_level' => 'nullable|in:new,intermediate,experienced,senior',
            'status' => 'nullable|in:online,available,looking_for_work',
            'search' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $latitude = $request->latitude ?? $user->latitude;
            $longitude = $request->longitude ?? $user->longitude;
            $radius = $request->radius;

        $query = User::where(function ($q) use ($user) {
                // Include all users with shared locations OR current user (even without share_location enabled)
                $q->where(function ($subQ) {
                    $subQ->where('users.share_location', true)
                        ->where('users.show_in_discovery', true);
                })->orWhere('users.id', $user->id);
            })
            ->where(function ($q) {
                // Must have at least latitude AND longitude
                $q->whereNotNull('users.latitude')
                    ->whereNotNull('users.longitude');
            })
            ->with('roles');

        // Calculate distance if coordinates provided
        if ($latitude && $longitude) {
            $query->select('users.*')
                ->selectRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                    [$latitude, $longitude, $latitude]
                );
        } else {
            $query->select('users.*')
                ->selectRaw('NULL AS distance');
        }

        // Apply visibility filters
        $query->where(function ($q) use ($user) {
            $q->where('users.visibility', 'everyone')
                ->orWhere(function ($subQ) use ($user) {
                    $subQ->where('users.visibility', 'connections_only')
                        ->whereExists(function ($existsQuery) use ($user) {
                            $existsQuery->select(DB::raw(1))
                                ->from('user_connections')
                                ->whereColumn('user_connections.connected_user_id', 'users.id')
                                ->where('user_connections.user_id', $user->id)
                                ->where('user_connections.status', 'accepted');
                        });
                })
                ->orWhere(function ($subQ) {
                    $subQ->where('users.visibility', 'verified_only')
                        ->where('users.is_active', true);
                });
        });

        // Filter by position/role
        if ($request->position) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->position . '%');
            });
        }

        // Filter by experience level
        if ($request->experience_level) {
            $experienceMap = [
                'new' => [0, 2],
                'intermediate' => [2, 5],
                'experienced' => [5, 10],
                'senior' => [10, 999],
            ];
            
            if (isset($experienceMap[$request->experience_level])) {
                [$min, $max] = $experienceMap[$request->experience_level];
                $query->whereBetween('years_experience', [$min, $max]);
            }
        }

        // Filter by status
        if ($request->status === 'online') {
            $query->where('is_online', true);
        } elseif ($request->status === 'available') {
            $query->where(function ($q) {
                $q->where('availability_status', 'available')
                    ->orWhere('looking_to_meet', true);
            });
        } elseif ($request->status === 'looking_for_work') {
            $query->where('looking_for_work', true);
        }

        // Search filter
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('location_name', 'like', '%' . $request->search . '%');
            });
        }

        // Apply distance filter if radius is set
        if ($radius && $latitude && $longitude) {
            $query->having('distance', '<=', $radius);
        }

        $crew = $query->when($latitude && $longitude && $radius, function ($q) {
                return $q->orderBy('distance');
            }, function ($q) {
                return $q->orderBy('is_online', 'desc')->orderBy('last_seen_at', 'desc');
            })
            ->limit(500)
            ->get()
            ->map(function ($crew) use ($user) {
                $connection = UserConnection::where(function ($q) use ($user, $crew) {
                    $q->where('user_id', $user->id)
                        ->where('connected_user_id', $crew->id);
                })->orWhere(function ($q) use ($user, $crew) {
                    $q->where('user_id', $crew->id)
                        ->where('connected_user_id', $user->id);
                })->first();

                // Get coordinates based on privacy
                $lat = null;
                $lng = null;
                if ($crew->location_privacy === 'exact') {
                    $lat = $crew->latitude;
                    $lng = $crew->longitude;
                } elseif ($crew->location_privacy === 'approximate' && $crew->latitude && $crew->longitude) {
                    // Add small random offset for approximate location
                    $lat = $crew->latitude + (rand(-100, 100) / 10000);
                    $lng = $crew->longitude + (rand(-100, 100) / 10000);
                }

                return [
                    'id' => $crew->id,
                    'name' => $crew->name,
                    'first_name' => $crew->first_name,
                    'last_name' => $crew->last_name,
                    'email' => $crew->email,
                    'profile_photo_url' => $crew->profile_photo_url,
                    'position' => $crew->roles->pluck('name')->first(),
                    'years_experience' => $crew->years_experience,
                    'current_yacht' => $crew->current_yacht,
                    'languages' => $crew->languages ?? [],
                    'nationality' => $crew->nationality,
                    'availability_status' => $crew->availability_status,
                    'availability_message' => $crew->availability_message,
                    'looking_to_meet' => $crew->looking_to_meet,
                    'looking_for_work' => $crew->looking_for_work,
                    'is_online' => $crew->is_online,
                    'last_seen_at' => $crew->last_seen_at,
                    'rating' => $crew->rating,
                    'total_reviews' => $crew->total_reviews,
                    'distance' => $crew->distance ? round($crew->distance, 2) : null,
                    'location_name' => $crew->location_name,
                    'location_updated_at' => $crew->location_updated_at ? ($crew->location_updated_at instanceof \Carbon\Carbon ? $crew->location_updated_at->toIso8601String() : $crew->location_updated_at) : null,
                    'connection_status' => $connection ? $connection->status : null,
                    'is_self' => $crew->id === $user->id,
                    'latitude' => $lat,
                    'longitude' => $lng,
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'Crew locations retrieved',
                'data' => [
                    'count' => $crew->count(),
                    'crew' => $crew,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve crew locations: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update online status
     */
    public function updateOnlineStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'is_online' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
        
        try {
            if ($request->is_online) {
                $user->setOnline();
            } else {
                $user->setOffline();
            }

            return response()->json([
                'status' => true,
                'message' => 'Online status updated',
                'data' => [
                    'is_online' => $user->is_online,
                    'last_seen_at' => $user->last_seen_at,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update online status: ' . $e->getMessage(),
            ], 500);
        }
    }
}
