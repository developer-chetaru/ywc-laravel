<?php

namespace App\Services\Itinerary;

use App\Models\ItineraryRoute;
use App\Models\ItineraryRouteLeg;
use App\Models\ItineraryRouteStop;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RouteBuilder
{
    /**
     * Create a new itinerary route with stops and legs for the given owner.
     *
     * @param  array<string, mixed>  $payload
     */
    public function createRoute(User $owner, array $payload): ItineraryRoute
    {
        return DB::transaction(function () use ($owner, $payload) {
            $routeData = $this->extractRouteAttributes($payload);

            /** @var ItineraryRoute $route */
            $route = $owner->itineraryRoutes()->create($routeData);

            $stops = $this->normalizeStops(Arr::get($payload, 'stops', []));
            $this->syncStops($route, $stops);

            $this->recalculateMetrics($route);

            return $route->fresh(['stops', 'legs', 'statistics']);
        });
    }

    /**
     * Update an itinerary route and its related stops/legs.
     *
     * @param  array<string, mixed>  $payload
     */
    public function updateRoute(ItineraryRoute $route, array $payload): ItineraryRoute
    {
        return DB::transaction(function () use ($route, $payload) {
            $routeData = $this->extractRouteAttributes($payload);
            $route->fill($routeData);
            $route->save();

            if (array_key_exists('stops', $payload)) {
                $stops = $this->normalizeStops($payload['stops'] ?? []);
                $this->syncStops($route, $stops);
            }

            $this->recalculateMetrics($route);

            return $route->fresh(['stops', 'legs', 'statistics']);
        });
    }

    /**
     * Clone the given route for a new owner.
     *
     * @param  array<string, mixed>  $overrides
     */
    public function cloneRoute(ItineraryRoute $route, User $owner, array $overrides = []): ItineraryRoute
    {
        return DB::transaction(function () use ($route, $owner, $overrides) {
            $cloneData = Arr::except($route->toArray(), [
                'id',
                'slug',
                'user_id',
                'template_source_id',
                'created_at',
                'updated_at',
                'deleted_at',
                'rating_avg',
                'rating_count',
                'views_count',
                'copies_count',
                'favorites_count',
            ]);

            $cloneData = array_merge($cloneData, $overrides, [
                'template_source_id' => $route->id,
                'status' => 'draft',
                'visibility' => 'private',
                'is_template' => false,
                'published_at' => null,
            ]);

            /** @var ItineraryRoute $clone */
            $clone = $owner->itineraryRoutes()->create($cloneData);

            $stops = $route->stops()
                ->orderBy('sequence')
                ->get()
                ->map(fn (ItineraryRouteStop $stop) => Arr::except($stop->toArray(), [
                    'id',
                    'route_id',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ]));

            $this->syncStops($clone, $this->normalizeStops($stops->all()));
            $this->recalculateMetrics($clone);

            return $clone->fresh(['stops', 'legs', 'statistics']);
        });
    }

    /**
     * Extract the route fillable attributes from request payload.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function extractRouteAttributes(array $payload): array
    {
        return Arr::only($payload, [
            'title',
            'description',
            'slug',
            'cover_image',
            'region',
            'difficulty',
            'season',
            'duration_days',
            'start_date',
            'end_date',
            'distance_nm',
            'average_leg_nm',
            'visibility',
            'status',
            'is_template',
            'is_featured',
            'tags',
            'metadata',
            'published_at',
        ]);
    }

    /**
     * Normalize stops payload into a collection of arrays.
     *
     * @param  array<int, mixed>  $stops
     */
    protected function normalizeStops(array $stops): Collection
    {
        return collect($stops)->map(function ($stop, int $index) {
            $arr = is_array($stop) ? $stop : (array) $stop;
            $arr['sequence'] = $arr['sequence'] ?? $index + 1;
            $arr['day_number'] = $arr['day_number'] ?? $index + 1;
            
            // Ensure photos is an array - handle all possible formats
            if (isset($arr['photos'])) {
                // If it's already an array, use it
                if (is_array($arr['photos'])) {
                    // Already an array, just filter it
                } elseif (is_string($arr['photos'])) {
                    // Try to decode JSON string
                    $decoded = json_decode($arr['photos'], true);
                    $arr['photos'] = is_array($decoded) ? $decoded : [];
                } else {
                    // Not array or string, set to empty
                    $arr['photos'] = [];
                }
                
                // Filter out empty values and re-index
                $arr['photos'] = array_values(array_filter($arr['photos'], function($photo) {
                    return !empty($photo) && is_string($photo);
                }));
            } else {
                $arr['photos'] = [];
            }

            return Arr::only($arr, [
                'id',
                'sequence',
                'day_number',
                'name',
                'location_label',
                'latitude',
                'longitude',
                'stay_duration_hours',
                'description',
                'notes',
                'photos',
                'tasks',
                'checklists',
                'eta',
                'ata',
                'departure_actual',
                'metadata',
                'requires_clearance',
            ]);
        });
    }

    /**
     * Sync the stops for a route and rebuild legs accordingly.
     *
     * @param  Collection<int, array<string, mixed>>  $stops
     */
    protected function syncStops(ItineraryRoute $route, Collection $stops): void
    {
        $existingIds = $route->stops()->pluck('id')->all();
        $handledIds = [];

        foreach ($stops as $stopData) {
            $stopId = Arr::get($stopData, 'id');
            if ($stopId) {
                /** @var ItineraryRouteStop|null $stop */
                $stop = $route->stops()->find($stopId);
                if ($stop) {
                    // Ensure photos are properly set before filling
                    $photos = Arr::get($stopData, 'photos', []);
                    if (!is_array($photos)) {
                        $photos = is_string($photos) ? json_decode($photos, true) ?? [] : [];
                    }
                    $stopData['photos'] = array_values(array_filter($photos, function($photo) {
                        return !empty($photo) && is_string($photo);
                    }));
                    
                    $stop->fill(Arr::except($stopData, ['id']));
                    $stop->save();
                    $handledIds[] = $stop->id;
                    continue;
                }
            }

            // Ensure photos are properly set before creating
            $photos = Arr::get($stopData, 'photos', []);
            if (!is_array($photos)) {
                $photos = is_string($photos) ? json_decode($photos, true) ?? [] : [];
            }
            $stopData['photos'] = array_values(array_filter($photos, function($photo) {
                return !empty($photo) && is_string($photo);
            }));
            
            $created = $route->stops()->create(Arr::except($stopData, ['id']));
            $handledIds[] = $created->id;
        }

        $idsToDelete = array_diff($existingIds, $handledIds);
        if (!empty($idsToDelete)) {
            $route->stops()->whereIn('id', $idsToDelete)->delete();
        }

        $this->rebuildLegs($route);
    }

    /**
     * Rebuild legs for the route based on ordered stops.
     */
    protected function rebuildLegs(ItineraryRoute $route): void
    {
        $stops = $route->stops()->orderBy('sequence')->get();
        $route->legs()->forceDelete();

        $legs = [];
        foreach ($stops as $index => $stop) {
            $next = $stops->get($index + 1);
            if (!$next) {
                continue;
            }

            $distance = $this->calculateDistanceNm(
                (float) $stop->latitude,
                (float) $stop->longitude,
                (float) $next->latitude,
                (float) $next->longitude
            );

            $legs[] = new ItineraryRouteLeg([
                'sequence' => $index + 1,
                'from_stop_id' => $stop->id,
                'to_stop_id' => $next->id,
                'distance_nm' => $distance,
                'estimated_hours' => $distance > 0 ? round($distance / 6, 2) : null, // default 6 knots
                'average_speed_knots' => 6,
            ]);
        }

        if (!empty($legs)) {
            $route->legs()->saveMany($legs);
        }
    }

    /**
     * Recalculate aggregate metrics for the route.
     */
    public function recalculateMetrics(ItineraryRoute $route): void
    {
        $totalDistance = $route->legs()->sum('distance_nm');
        $route->distance_nm = $totalDistance;

        $legsCount = max($route->legs()->count(), 1);
        $route->average_leg_nm = $totalDistance > 0 ? round($totalDistance / $legsCount, 2) : 0;

        // Calculate duration_days based on start_date and end_date if available
        if ($route->start_date && $route->end_date) {
            // Calculate days between start and end date (inclusive)
            $start = \Carbon\Carbon::parse($route->start_date);
            $end = \Carbon\Carbon::parse($route->end_date);
            $route->duration_days = max(1, $start->diffInDays($end) + 1); // +1 to include both start and end days
        } else {
            // Fallback: Calculate based on unique day_numbers or number of stops
            $stops = $route->stops()->get();
            if ($stops->isNotEmpty()) {
                // Get unique day_numbers
                $uniqueDays = $stops->pluck('day_number')->filter()->unique()->sort()->values();
                
                if ($uniqueDays->isNotEmpty()) {
                    // Use the maximum day_number (which represents the total days)
                    $route->duration_days = $uniqueDays->max();
                } else {
                    // Fallback: use number of stops if day_number is not set
                    $route->duration_days = $stops->count();
                }
            } else {
                // If no stops, keep existing duration_days or default to 0
                $route->duration_days = $route->duration_days ?? 0;
            }
        }

        $route->save();

        if ($route->statistics) {
            $route->statistics->update([
                'rating_avg' => $route->rating_avg ?? 0.00,
                'reviews_count' => $route->rating_count ?? 0,
            ]);
        }
    }

    /**
     * Compute nautical miles between two coordinates.
     */
    protected function calculateDistanceNm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        if (($lat1 === 0 && $lon1 === 0) || ($lat2 === 0 && $lon2 === 0)) {
            return 0.0;
        }

        $earthRadiusNm = 3440.065;
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLon = $lon2Rad - $lon1Rad;

        $a = sin($deltaLat / 2) ** 2
            + cos($lat1Rad) * cos($lat2Rad) * sin($deltaLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadiusNm * $c, 2);
    }
}

