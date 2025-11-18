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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
            $validated = $request->validated();
            
            // Handle cover image - can be file upload, URL, or path
            if ($request->hasFile('cover_image')) {
                // File upload
                $file = $request->file('cover_image');
                $validated['cover_image'] = $file->store('route-covers', 'public');
            } elseif (isset($validated['cover_image']) && filter_var($validated['cover_image'], FILTER_VALIDATE_URL)) {
                // URL - download and store
                $downloadedPath = $this->downloadAndStoreImage($validated['cover_image'], 'route-covers');
                if ($downloadedPath) {
                    $validated['cover_image'] = $downloadedPath;
                } else {
                    // Log warning but don't fail the request - just skip the cover image
                    \Log::warning("Failed to download cover image from URL: {$validated['cover_image']}");
                    unset($validated['cover_image']); // Remove if download failed
                }
            }
            
            // Handle stop photos - can be file uploads, URLs, or paths
            if ($request->has('stops') && is_array($request->input('stops'))) {
                foreach ($validated['stops'] as $index => &$stop) {
                    $processedPhotos = [];
                    
                    // Check for file uploads in nested array format
                    $stopInput = $request->input("stops.{$index}");
                    if (is_array($stopInput) && $request->hasFile("stops.{$index}.photos")) {
                        $photos = $request->file("stops.{$index}.photos");
                        // Handle both single file and array of files
                        $photosArray = is_array($photos) ? $photos : [$photos];
                        foreach ($photosArray as $photo) {
                            if ($photo && $photo->isValid()) {
                                $processedPhotos[] = $photo->store('route-stops', 'public');
                            }
                        }
                    }
                    
                    // Process photos array - check for URLs
                    if (isset($stop['photos']) && is_array($stop['photos'])) {
                        foreach ($stop['photos'] as $photo) {
                            if (filter_var($photo, FILTER_VALIDATE_URL)) {
                                // It's a URL - download and store
                                $downloadedPath = $this->downloadAndStoreImage($photo, 'route-stops');
                                if ($downloadedPath) {
                                    $processedPhotos[] = $downloadedPath;
                                }
                            } elseif (is_string($photo) && !empty($photo)) {
                                // It's already a path
                                $processedPhotos[] = $photo;
                            }
                        }
                    }
                    
                    // Update stop with processed photos
                    if (!empty($processedPhotos)) {
                        $stop['photos'] = $processedPhotos;
                    }
                }
                unset($stop); // Break reference
            }
            
            $route = $builder->createRoute($request->user(), $validated);

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

    /**
     * Download and store an image from URL
     */
    protected function downloadAndStoreImage(string $url, string $directory): ?string
    {
        try {
            // Add headers to mimic a browser request (required for Google Images and many other services)
            $response = Http::timeout(15)
                ->withOptions([
                    'allow_redirects' => true,
                    'max_redirects' => 5,
                ])
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.9',
                    'Referer' => 'https://www.google.com/',
                    'Accept-Encoding' => 'gzip, deflate, br',
                ])
                ->get($url);
            
            if (!$response->successful()) {
                \Log::warning("Failed to download image: {$url} - Status: {$response->status()}");
                return null;
            }

            $body = $response->body();
            
            // Validate that we actually got image data (check for image magic bytes)
            $imageMagicBytes = [
                "\xFF\xD8\xFF", // JPEG
                "\x89\x50\x4E\x47", // PNG
                "GIF87a", // GIF87a
                "GIF89a", // GIF89a
                "RIFF", // WebP (starts with RIFF)
            ];
            
            $isImage = false;
            foreach ($imageMagicBytes as $magic) {
                if (str_starts_with($body, $magic)) {
                    $isImage = true;
                    break;
                }
            }
            
            // Also check for WebP (RIFF...WEBP)
            if (!$isImage && str_starts_with($body, "RIFF") && strpos($body, "WEBP") !== false) {
                $isImage = true;
            }
            
            if (!$isImage) {
                \Log::warning("Downloaded content is not a valid image: {$url}");
                return null;
            }

            // Get file extension from URL or detect from content type
            $parsedUrl = parse_url($url);
            $path = $parsedUrl['path'] ?? '';
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            
            // If no extension in URL, try to detect from content type or magic bytes
            if (empty($extension)) {
                $contentType = $response->header('Content-Type', '');
                
                if (str_contains($contentType, 'jpeg') || str_contains($contentType, 'jpg') || str_starts_with($body, "\xFF\xD8\xFF")) {
                    $extension = 'jpg';
                } elseif (str_contains($contentType, 'png') || str_starts_with($body, "\x89\x50\x4E\x47")) {
                    $extension = 'png';
                } elseif (str_contains($contentType, 'webp') || (str_starts_with($body, "RIFF") && strpos($body, "WEBP") !== false)) {
                    $extension = 'webp';
                } elseif (str_contains($contentType, 'gif') || str_starts_with($body, "GIF87a") || str_starts_with($body, "GIF89a")) {
                    $extension = 'gif';
                } else {
                    // Default to jpg if we can't detect
                    $extension = 'jpg';
                }
            }

            // Clean extension (remove query params if any)
            $extension = strtolower(explode('?', $extension)[0]);
            
            // Validate extension
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($extension, $allowedExtensions)) {
                $extension = 'jpg'; // Default to jpg if invalid
            }
            
            $filename = Str::random(40) . '.' . $extension;
            $filePath = $directory . '/' . $filename;

            // Ensure directory exists
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Store the image
            Storage::disk('public')->put($filePath, $body);

            \Log::info("Successfully downloaded and stored image: {$url} -> {$filePath}");
            return $filePath;
        } catch (\Exception $e) {
            \Log::warning("Error downloading image {$url}: {$e->getMessage()}");
            return null;
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

            $validated = $request->validated();
            
            // Handle cover image - can be file upload, URL, or path
            if ($request->hasFile('cover_image')) {
                // Delete old cover image if it exists
                if ($route->cover_image && Storage::disk('public')->exists($route->cover_image)) {
                    Storage::disk('public')->delete($route->cover_image);
                }
                $file = $request->file('cover_image');
                $validated['cover_image'] = $file->store('route-covers', 'public');
            } elseif (isset($validated['cover_image']) && filter_var($validated['cover_image'], FILTER_VALIDATE_URL)) {
                // URL - download and store
                // Delete old cover image if it exists
                if ($route->cover_image && Storage::disk('public')->exists($route->cover_image)) {
                    Storage::disk('public')->delete($route->cover_image);
                }
                $downloadedPath = $this->downloadAndStoreImage($validated['cover_image'], 'route-covers');
                if ($downloadedPath) {
                    $validated['cover_image'] = $downloadedPath;
                } else {
                    unset($validated['cover_image']); // Remove if download failed
                }
            }
            
            // Handle stop photos - can be file uploads, URLs, or paths
            if ($request->has('stops') && is_array($request->input('stops'))) {
                foreach ($validated['stops'] as $index => &$stop) {
                    $processedPhotos = [];
                    
                    // Check for file uploads in nested array format
                    $stopInput = $request->input("stops.{$index}");
                    if (is_array($stopInput) && $request->hasFile("stops.{$index}.photos")) {
                        $photos = $request->file("stops.{$index}.photos");
                        // Handle both single file and array of files
                        $photosArray = is_array($photos) ? $photos : [$photos];
                        foreach ($photosArray as $photo) {
                            if ($photo && $photo->isValid()) {
                                $processedPhotos[] = $photo->store('route-stops', 'public');
                            }
                        }
                    }
                    
                    // Process photos array - check for URLs
                    if (isset($stop['photos']) && is_array($stop['photos'])) {
                        foreach ($stop['photos'] as $photo) {
                            if (filter_var($photo, FILTER_VALIDATE_URL)) {
                                // It's a URL - download and store
                                $downloadedPath = $this->downloadAndStoreImage($photo, 'route-stops');
                                if ($downloadedPath) {
                                    $processedPhotos[] = $downloadedPath;
                                }
                            } elseif (is_string($photo) && !empty($photo)) {
                                // It's already a path
                                $processedPhotos[] = $photo;
                            }
                        }
                    }
                    
                    // Update stop with processed photos
                    if (!empty($processedPhotos)) {
                        $stop['photos'] = $processedPhotos;
                    }
                }
                unset($stop); // Break reference
            }

            $route = $builder->updateRoute($route, $validated);

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

