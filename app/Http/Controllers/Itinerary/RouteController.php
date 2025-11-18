<?php

namespace App\Http\Controllers\Itinerary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Itinerary\StoreRouteRequest;
use App\Http\Requests\Itinerary\UpdateRouteRequest;
use App\Models\ItineraryRoute;
use App\Services\Itinerary\RouteBuilder;
use App\Services\Itinerary\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

class RouteController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = ItineraryRoute::query()
            ->with([
                'owner:id,first_name,last_name,email',
                'statistics',
            ])
            ->when(!$user, fn ($q) => $q->public())
            ->when($search = $request->input('search'), function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereJsonContains('tags', $search);
                });
            })
            ->when($region = $request->input('region'), fn ($q) => $q->where('region', $region))
            ->when($difficulty = $request->input('difficulty'), fn ($q) => $q->where('difficulty', $difficulty))
            ->when($season = $request->input('season'), fn ($q) => $q->where('season', $season))
            ->when($status = $request->input('status'), fn ($q) => $q->where('status', $status))
            ->when($visibility = $request->input('visibility'), fn ($q) => $q->where('visibility', $visibility))
            ->when($days = $request->input('days'), fn ($q) => $q->where('duration_days', $days))
            ->when($request->filled('min_distance'), fn ($q) => $q->where('distance_nm', '>=', (float) $request->input('min_distance')))
            ->when($request->filled('max_distance'), fn ($q) => $q->where('distance_nm', '<=', (float) $request->input('max_distance')))
            ->when($request->filled('min_duration'), fn ($q) => $q->where('duration_days', '>=', (int) $request->input('min_duration')))
            ->when($request->filled('max_duration'), fn ($q) => $q->where('duration_days', '<=', (int) $request->input('max_duration')))
            ->when($request->boolean('templates'), fn ($q) => $q->templates())
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->orderBy('title');

        if ($user) {
            $query->where(function ($sub) use ($user) {
                $sub->where('visibility', 'public')
                    ->orWhere('user_id', $user->id)
                    ->orWhere(function ($crewQuery) use ($user) {
                        $crewQuery->where('visibility', 'crew')
                            ->whereHas('crew', function ($crew) use ($user) {
                                $crew->where('user_id', $user->id)
                                    ->where('status', 'accepted');
                            });
                    });
            });
        }

        $routes = $query->paginate($request->input('per_page', 15))->withQueryString();

        return response()->json($routes);
    }

    public function store(StoreRouteRequest $request, RouteBuilder $builder): JsonResponse
    {
        try {
            $route = $builder->createRoute($request->user(), $request->validated());

            $route->loadMissing([
                'stops',
                'legs',
                'statistics',
                'owner:id,first_name,last_name,email',
            ]);

            return response()->json([
                'message' => 'Route created successfully.',
                'data' => $route,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create route.',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred while creating the route.',
            ], 500);
        }
    }

    public function show(Request $request, ItineraryRoute $route): JsonResponse
    {
        try {
            Gate::authorize('view', $route);

            $route->loadMissing([
                'stops.weatherSnapshots',
                'legs.from',
                'legs.to',
                'crew.user:id,first_name,last_name,email',
                'reviews.user:id,first_name,last_name',
                'statistics',
                'owner:id,first_name,last_name,email',
            ]);

            // Ensure photos are properly formatted for each stop
            foreach ($route->stops as $stop) {
                // If photos is a string, decode it
                if (is_string($stop->photos)) {
                    $decoded = json_decode($stop->photos, true);
                    $stop->photos = is_array($decoded) ? $decoded : [];
                }
                // If photos is null, set to empty array
                if ($stop->photos === null) {
                    $stop->photos = [];
                }
                // Ensure it's an array
                if (!is_array($stop->photos)) {
                    $stop->photos = [];
                }
                // Convert photo paths to full URLs and filter out invalid ones
                $stop->photos = array_values(array_filter(
                    array_map(function($photo) {
                        if (empty($photo) || !is_string($photo)) {
                            return null;
                        }
                        // Check if file exists
                        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($photo)) {
                            return [
                                'path' => $photo,
                                'url' => \Illuminate\Support\Facades\Storage::disk('public')->url($photo),
                            ];
                        }
                        return null;
                    }, $stop->photos),
                    fn($photo) => $photo !== null
                ));
            }

            // Convert cover_image to full URL if it exists
            if ($route->cover_image) {
                $route->cover_image_url = \Illuminate\Support\Facades\Storage::disk('public')->url($route->cover_image);
            }

            return response()->json([
                'data' => $route,
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'message' => 'You do not have permission to view this route.',
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve route.',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred while retrieving the route.',
            ], 500);
        }
    }

    public function update(UpdateRouteRequest $request, RouteBuilder $builder, ItineraryRoute $route): JsonResponse
    {
        try {
            Gate::authorize('update', $route);

            $route = $builder->updateRoute($route, $request->validated());

            $route->loadMissing([
                'stops',
                'legs',
                'statistics',
                'owner:id,first_name,last_name,email',
            ]);

            return response()->json([
                'message' => 'Route updated successfully.',
                'data' => $route,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update route.',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred while updating the route.',
            ], 500);
        }
    }

    public function destroy(ItineraryRoute $route): JsonResponse
    {
        Gate::authorize('delete', $route);

        $route->delete();

        return response()->json([
            'message' => 'Route deleted.',
        ]);
    }

    public function cloneRoute(Request $request, RouteBuilder $builder, ItineraryRoute $route): JsonResponse
    {
        Gate::authorize('copy', $route);

        $clone = $builder->cloneRoute(
            $route,
            $request->user(),
            Arr::only($request->all(), ['title', 'visibility', 'season', 'difficulty'])
        );

        return response()->json([
            'message' => 'Route copied to your account.',
            'data' => $clone,
        ], 201);
    }

    public function publish(Request $request, ItineraryRoute $route): JsonResponse
    {
        Gate::authorize('publish', $route);

        $route->update([
            'status' => $request->boolean('publish') ? 'active' : 'draft',
            'visibility' => $request->input('visibility', $route->visibility),
            'published_at' => $request->boolean('publish') ? now() : null,
        ]);

        return response()->json([
            'message' => $request->boolean('publish')
                ? 'Route published successfully.'
                : 'Route moved back to draft.',
            'data' => $route->fresh(['statistics']),
        ]);
    }

    public function statistics(ItineraryRoute $route): JsonResponse
    {
        Gate::authorize('view', $route);

        if (!$route->statistics) {
            $statistic = new \App\Models\ItineraryRouteStatistic([
                'route_id' => $route->id,
                'views_total' => 0,
                'views_unique' => 0,
                'copies_total' => 0,
                'reviews_count' => 0,
                'rating_avg' => 0.00,
                'favorites_count' => 0,
                'shares_count' => 0,
            ]);
            $statistic->save();
            $route->refresh();
        }

        return response()->json([
            'data' => $route->statistics,
        ]);
    }

    public function refreshWeather(ItineraryRoute $route, WeatherService $weatherService): JsonResponse
    {
        Gate::authorize('update', $route);

        $route->loadMissing('stops');
        $weatherService->syncRouteWeather($route);

        return response()->json([
            'message' => 'Weather forecast refreshed.',
            'data' => $route->fresh('stops.weatherSnapshots'),
        ]);
    }
}

