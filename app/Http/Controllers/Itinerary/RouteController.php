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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RouteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/itinerary/routes",
     *     summary="List all sailing routes",
     *     tags={"Itinerary Routes"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         @OA\Schema(type="string"),
     *         description="Search in title, description, or tags"
     *     ),
     *     @OA\Parameter(
     *         name="region",
     *         in="query",
     *         @OA\Schema(type="string"),
     *         description="Filter by region"
     *     ),
     *     @OA\Parameter(
     *         name="difficulty",
     *         in="query",
     *         @OA\Schema(type="string"),
     *         description="Filter by difficulty level"
     *     ),
     *     @OA\Parameter(
     *         name="season",
     *         in="query",
     *         @OA\Schema(type="string"),
     *         description="Filter by season"
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         @OA\Schema(type="string"),
     *         description="Filter by status"
     *     ),
     *     @OA\Parameter(
     *         name="visibility",
     *         in="query",
     *         @OA\Schema(type="string"),
     *         description="Filter by visibility"
     *     ),
     *     @OA\Parameter(
     *         name="days",
     *         in="query",
     *         @OA\Schema(type="integer"),
     *         description="Filter by duration in days"
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         @OA\Schema(type="integer", default=15),
     *         description="Items per page"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of routes",
     *         @OA\JsonContent(type="object")
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/itinerary/routes",
     *     summary="Create a new sailing route",
     *     tags={"Itinerary Routes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title"},
     *                 @OA\Property(property="title", type="string", example="Mediterranean Adventure"),
     *                 @OA\Property(property="description", type="string", example="A beautiful sailing route"),
     *                 @OA\Property(property="cover_image", type="string", format="binary", description="Cover image file"),
     *                 @OA\Property(property="stops", type="string", format="json", description="JSON array of route stops"),
     *                 @OA\Property(property="visibility", type="string", enum={"public", "private", "crew"}, example="public", description="Route visibility"),
     *                 @OA\Property(property="difficulty", type="string", example="intermediate"),
     *                 @OA\Property(property="season", type="string", example="summer"),
     *                 @OA\Property(property="region", type="string", example="Mediterranean")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Route created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Route created successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, description="Failed to create route")
     * )
     */
    public function store(StoreRouteRequest $request, RouteBuilder $builder): JsonResponse
    {
        try {
            // Process files BEFORE validation (similar to Livewire component)
            // This ensures files are stored and paths are available for validation
            
            // Process cover image file first
            $coverImagePath = null;
            if ($request->hasFile('cover_image')) {
                $file = $request->file('cover_image');
                if ($file && $file->isValid()) {
                    $coverImagePath = $file->store('route-covers', 'public');
                    Log::info('Cover image stored', [
                        'path' => $coverImagePath,
                        'file_size' => $file->getSize(),
                        'file_mime' => $file->getMimeType(),
                    ]);
                } else {
                    Log::warning('Cover image file is invalid', [
                        'has_file' => $request->hasFile('cover_image'),
                        'file_valid' => $file ? $file->isValid() : false,
                    ]);
                }
            } else {
                Log::info('No cover image file in request', [
                    'all_files' => array_keys($request->allFiles()),
                ]);
            }
            
            // Process stop photos files BEFORE validation
            $stopsPhotosBackup = [];
            $stopsInput = $request->input('stops', []);
            if (is_array($stopsInput)) {
                foreach ($stopsInput as $index => $stopInput) {
                    $processedPhotos = $this->processStopPhotos($request, $index, $stopInput);
                    if (!empty($processedPhotos)) {
                        $stopsPhotosBackup[$index] = $processedPhotos;
                        Log::info("Processed {$index} photos for stop {$index}", [
                            'count' => count($processedPhotos),
                            'paths' => $processedPhotos,
                        ]);
                    }
                }
            }
            
            // Now run validation
            $validated = $request->validated();
            
            // ALWAYS use our stored cover image path if we have one
            // This prevents validation from overwriting with temporary path
            if ($coverImagePath) {
                $validated['cover_image'] = $coverImagePath;
                Log::info('Using stored cover image path', ['path' => $coverImagePath]);
            } elseif (isset($validated['cover_image'])) {
                // Check if it's a URL
                if (filter_var($validated['cover_image'], FILTER_VALIDATE_URL)) {
                    // URL - download and store
                    $downloadedPath = $this->downloadAndStoreImage($validated['cover_image'], 'route-covers');
                    if ($downloadedPath) {
                        $validated['cover_image'] = $downloadedPath;
                    } else {
                        Log::warning("Failed to download cover image from URL: {$validated['cover_image']}");
                        unset($validated['cover_image']);
                    }
                } elseif (empty($validated['cover_image']) || $validated['cover_image'] === '0' || $validated['cover_image'] === '') {
                    // Remove empty or "0" values
                    unset($validated['cover_image']);
                }
                // If it's a string path, keep it as is
            }
            
            // Merge processed stop photos into validated stops data
            if (isset($validated['stops']) && is_array($validated['stops'])) {
                foreach ($validated['stops'] as $index => &$stop) {
                    // ALWAYS use processed photos from backup if available (processed before validation)
                    if (isset($stopsPhotosBackup[$index]) && !empty($stopsPhotosBackup[$index])) {
                        $stop['photos'] = $stopsPhotosBackup[$index];
                        Log::info("Using backup photos for stop {$index}", [
                            'count' => count($stopsPhotosBackup[$index]),
                        ]);
                    } else {
                        // Process photos from validated data (URLs or existing paths)
                        $processedPhotos = $this->processStopPhotos($request, $index, $stop);
                        if (!empty($processedPhotos)) {
                            $stop['photos'] = $processedPhotos;
                        } elseif (isset($stop['photos'])) {
                            // Filter out empty values
                            $stop['photos'] = array_values(array_filter($stop['photos'], function($photo) {
                                return !empty($photo) && is_string($photo);
                            }));
                        } else {
                            $stop['photos'] = [];
                        }
                    }
                }
                unset($stop); // Break reference
            }
            
            // Final check: ensure cover_image is not a temporary path
            if (isset($validated['cover_image']) && (str_starts_with($validated['cover_image'], '/tmp/') || str_starts_with($validated['cover_image'], 'tmp/'))) {
                Log::warning('Cover image is temporary path, removing', ['path' => $validated['cover_image']]);
                unset($validated['cover_image']);
            }
            
            // Log final validated data for debugging
            Log::info('Final validated data before createRoute', [
                'has_cover_image' => isset($validated['cover_image']),
                'cover_image' => $validated['cover_image'] ?? null,
                'stops_count' => count($validated['stops'] ?? []),
                'stops_with_photos' => array_map(function($stop) {
                    return isset($stop['photos']) ? count($stop['photos']) : 0;
                }, $validated['stops'] ?? []),
            ]);
            
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
            ], 200);
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

    /**
     * Process stop photos from request - handles file uploads, URLs, and existing paths
     */
    protected function processStopPhotos(Request $request, int $stopIndex, array $stop): array
    {
        $processedPhotos = [];
        
        // Get all files to check what we have
        $allFiles = $request->allFiles();
        \Log::info("Processing stop photos for stop {$stopIndex}", [
            'all_files_keys' => array_keys($allFiles),
            'stop_photos_in_data' => $stop['photos'] ?? null,
        ]);
        
        // Check for file uploads in nested array format (stops[0][photos][0])
        // First, try to get files as an array using dot notation
        if ($request->hasFile("stops.{$stopIndex}.photos")) {
            $photos = $request->file("stops.{$stopIndex}.photos");
            // Handle both single file and array of files
            $photosArray = is_array($photos) ? $photos : [$photos];
            foreach ($photosArray as $photo) {
                if ($photo && $photo->isValid()) {
                    $path = $photo->store('route-stops', 'public');
                    $processedPhotos[] = $path;
                    \Log::info("Stored photo from stops.{$stopIndex}.photos", ['path' => $path]);
                }
            }
        }
        
        // Also check for individual file uploads using dot notation (stops.0.photos.0, stops.0.photos.1, etc.)
        $photoIndex = 0;
        while (true) {
            $fileKey = "stops.{$stopIndex}.photos.{$photoIndex}";
            if ($request->hasFile($fileKey)) {
                $photo = $request->file($fileKey);
                if ($photo && $photo->isValid()) {
                    $path = $photo->store('route-stops', 'public');
                    $processedPhotos[] = $path;
                    \Log::info("Stored photo from {$fileKey}", ['path' => $path]);
                }
                $photoIndex++;
            } else {
                break; // No more files
            }
        }
        
        // Also check allFiles() for any files that match the pattern (handles array bracket notation)
        // Laravel might parse stops[0][photos][0] differently, so check all keys
        foreach ($allFiles as $key => $file) {
            // Match various patterns:
            // - stops[0][photos][0] (exact match)
            // - stops.0.photos.0 (dot notation)
            // - stops[0].photos[0] (mixed)
            // - stops.0[photos][0] (mixed)
            // Also handle cases where Laravel might flatten it differently
            
            $matched = false;
            
            // Exact match for stops[0][photos][0]
            if ($key === "stops[{$stopIndex}][photos][0]" || 
                preg_match("/^stops\[{$stopIndex}\]\[photos\]\[(\d+)\]$/", $key)) {
                $matched = true;
            }
            // Dot notation stops.0.photos.0
            elseif (preg_match("/^stops\.{$stopIndex}\.photos\.(\d+)$/", $key)) {
                $matched = true;
            }
            // Mixed notation
            elseif (preg_match("/^stops\[{$stopIndex}\]\.photos\[(\d+)\]$/", $key) ||
                    preg_match("/^stops\.{$stopIndex}\[photos\]\[(\d+)\]$/", $key)) {
                $matched = true;
            }
            
            if ($matched) {
                $fileArray = is_array($file) ? $file : [$file];
                foreach ($fileArray as $photo) {
                    if ($photo && $photo->isValid()) {
                        $path = $photo->store('route-stops', 'public');
                        $processedPhotos[] = $path;
                        \Log::info("Stored photo from {$key}", ['path' => $path]);
                    }
                }
            }
        }
        
        // Also try accessing via nested array structure if Laravel parsed it that way
        // Check if stops array has nested structure with files
        $stopsInput = $request->input('stops');
        if (is_array($stopsInput) && isset($stopsInput[$stopIndex])) {
            $stopInput = $stopsInput[$stopIndex];
            if (is_array($stopInput) && isset($stopInput['photos'])) {
                // Check if photos contains UploadedFile objects
                $photosInput = $stopInput['photos'];
                if (is_array($photosInput)) {
                    foreach ($photosInput as $photo) {
                        if ($photo instanceof \Illuminate\Http\UploadedFile && $photo->isValid()) {
                            $path = $photo->store('route-stops', 'public');
                            $processedPhotos[] = $path;
                            \Log::info("Stored photo from nested stops input", ['path' => $path]);
                        }
                    }
                }
            }
        }
        
        // Process photos array from validated data - check for URLs or existing paths
        if (isset($stop['photos']) && is_array($stop['photos'])) {
            foreach ($stop['photos'] as $photo) {
                // Skip if it's an UploadedFile object (already processed above)
                if ($photo instanceof \Illuminate\Http\UploadedFile) {
                    continue;
                }
                
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
        
        return $processedPhotos;
    }

    /**
     * @OA\Get(
     *     path="/api/itinerary/routes/{route}",
     *     summary="Get a specific sailing route",
     *     tags={"Itinerary Routes"},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Route ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Route details",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied"),
     *     @OA\Response(response=404, description="Route not found")
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/itinerary/routes/{route}",
     *     summary="Update a sailing route",
     *     tags={"Itinerary Routes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Route ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="title", type="string", example="Updated Route Title"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="cover_image", type="string", format="binary"),
     *                 @OA\Property(property="stops", type="string", format="json"),
     *                 @OA\Property(property="visibility", type="string", enum={"public", "private", "crew"})
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Route updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Route updated successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied"),
     *     @OA\Response(response=500, description="Failed to update route")
     * )
     */
    public function update(UpdateRouteRequest $request, RouteBuilder $builder, ItineraryRoute $route): JsonResponse
    {
        try {
            Gate::authorize('update', $route);

            // Process files BEFORE validation (similar to Livewire component)
            
            // Process cover image file first
            $coverImagePath = null;
            if ($request->hasFile('cover_image')) {
                // Delete old cover image if it exists
                if ($route->cover_image && Storage::disk('public')->exists($route->cover_image)) {
                    Storage::disk('public')->delete($route->cover_image);
                }
                $file = $request->file('cover_image');
                if ($file && $file->isValid()) {
                    $coverImagePath = $file->store('route-covers', 'public');
                    Log::info('Cover image stored', ['path' => $coverImagePath]);
                }
            }
            
            // Process stop photos files BEFORE validation
            $stopsPhotosBackup = [];
            $stopsInput = $request->input('stops', []);
            if (is_array($stopsInput)) {
                foreach ($stopsInput as $index => $stopInput) {
                    $processedPhotos = $this->processStopPhotos($request, $index, $stopInput);
                    if (!empty($processedPhotos)) {
                        $stopsPhotosBackup[$index] = $processedPhotos;
                    }
                }
            }
            
            // Now run validation
            $validated = $request->validated();
            
            // Merge processed files into validated data
            if ($coverImagePath) {
                $validated['cover_image'] = $coverImagePath;
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
            
            // Merge processed stop photos into validated stops data
            if (isset($validated['stops']) && is_array($validated['stops'])) {
                foreach ($validated['stops'] as $index => &$stop) {
                    // Use processed photos from backup if available
                    if (isset($stopsPhotosBackup[$index]) && !empty($stopsPhotosBackup[$index])) {
                        $stop['photos'] = $stopsPhotosBackup[$index];
                    } else {
                        // Process photos from validated data (URLs or existing paths)
                        $processedPhotos = $this->processStopPhotos($request, $index, $stop);
                        if (!empty($processedPhotos)) {
                            $stop['photos'] = $processedPhotos;
                        } elseif (isset($stop['photos'])) {
                            // Filter out empty values
                            $stop['photos'] = array_values(array_filter($stop['photos'], function($photo) {
                                return !empty($photo) && is_string($photo);
                            }));
                        } else {
                            $stop['photos'] = [];
                        }
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

    /**
     * @OA\Delete(
     *     path="/api/itinerary/routes/{route}",
     *     summary="Delete a sailing route",
     *     tags={"Itinerary Routes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Route ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Route deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Route deleted.")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied"),
     *     @OA\Response(response=404, description="Route not found")
     * )
     */
    public function destroy(ItineraryRoute $route): JsonResponse
    {
        Gate::authorize('delete', $route);

        $route->delete();

        return response()->json([
            'message' => 'Route deleted.',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/itinerary/routes/{route}/clone",
     *     summary="Clone a sailing route",
     *     tags={"Itinerary Routes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Route ID to clone"
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Copy of Route"),
     *             @OA\Property(property="visibility", type="string", enum={"public", "private", "crew"}),
     *             @OA\Property(property="season", type="string"),
     *             @OA\Property(property="difficulty", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Route cloned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Route copied to your account."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied")
     * )
     */
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
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/itinerary/routes/{route}/publish",
     *     summary="Publish or unpublish a route",
     *     tags={"Itinerary Routes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Route ID"
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="publish", type="boolean", example=true),
     *             @OA\Property(property="visibility", type="string", enum={"public", "private", "crew"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Route publish status updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Route published successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/itinerary/routes/{route}/statistics",
     *     summary="Get route statistics",
     *     tags={"Itinerary Routes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Route ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Route statistics",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="views_total", type="integer", example=150),
     *                 @OA\Property(property="views_unique", type="integer", example=120),
     *                 @OA\Property(property="copies_total", type="integer", example=5),
     *                 @OA\Property(property="reviews_count", type="integer", example=10),
     *                 @OA\Property(property="rating_avg", type="number", format="float", example=4.5),
     *                 @OA\Property(property="favorites_count", type="integer", example=25),
     *                 @OA\Property(property="shares_count", type="integer", example=8)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/itinerary/routes/{route}/weather/refresh",
     *     summary="Refresh weather forecast for route",
     *     tags={"Itinerary Routes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Route ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Weather forecast refreshed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Weather forecast refreshed."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied")
     * )
     */
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

