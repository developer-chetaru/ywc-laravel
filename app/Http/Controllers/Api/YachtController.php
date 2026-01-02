<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponseTrait;
use App\Models\Yacht;
use App\Models\YachtGallery;
use App\Services\YachtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class YachtController extends Controller
{
    use ApiResponseTrait;
    public function index(Request $request): JsonResponse
    {
        $query = Yacht::query()
            ->withCount('reviews')
            ->when($request->input('search'), function ($q, $search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('home_port', 'like', "%{$search}%")
                        ->orWhere('builder', 'like', "%{$search}%");
                });
            })
            ->when($request->input('type'), fn ($q, $type) => $q->where('type', $type))
            ->when($request->input('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->input('min_rating'), fn ($q, $rating) => $q->where('rating_avg', '>=', $rating))
            ->when($request->input('min_recommendation'), fn ($q, $rec) => $q->where('recommendation_percentage', '>=', $rec))
            ->orderByDesc('rating_avg')
            ->orderByDesc('reviews_count')
            ->orderBy('name');

        $yachts = $query->paginate($request->input('per_page', 15));

        // Add cover_image_url to each yacht (optimized)
        $baseUrl = Storage::disk('public')->url('');
        $yachts->getCollection()->transform(function ($yacht) use ($baseUrl) {
            if ($yacht->cover_image) {
                $yacht->cover_image_url = $baseUrl . $yacht->cover_image;
            }
            return $yacht;
        });

        return response()->json($yachts);
    }

    public function show($slug): JsonResponse
    {
        $yacht = Yacht::where('slug', $slug)
            ->withCount('reviews')
            ->with('gallery')
            ->firstOrFail();

        if ($yacht->cover_image) {
            $yacht->cover_image_url = Storage::disk('public')->url($yacht->cover_image);
        }

        // Add image URLs to gallery items
        if ($yacht->gallery) {
            $yacht->gallery->transform(function ($item) {
                $item->image_url = $item->image_url;
                return $item;
            });
        }

        return response()->json(['data' => $yacht]);
    }

    public function store(Request $request, YachtService $service): JsonResponse
    {
        try {
            $user = $request->user();
            $yacht = $service->create($request->all(), $request->file('cover_image'), $user);

            return $this->successResponse($yacht, 'Yacht created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id, YachtService $service): JsonResponse
    {
        try {
            $yacht = Yacht::findOrFail($id);
            
            // Check authorization
            Gate::authorize('update', $yacht);
            
            $yacht = $service->update($yacht, $request->all(), $request->file('cover_image'));

            return $this->successResponse($yacht, 'Yacht updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->unauthorizedResponse('You do not have permission to update this yacht.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy($id, YachtService $service): JsonResponse
    {
        $yacht = Yacht::findOrFail($id);
        
        // Check authorization
        Gate::authorize('delete', $yacht);
        
        $service->delete($yacht);

        return response()->json(['message' => 'Yacht deleted successfully.']);
    }

    // Gallery endpoints
    public function addGalleryImage(Request $request, $yachtId): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
            'caption' => 'nullable|string|max:255',
            'category' => 'nullable|in:exterior,interior,crew_areas,deck,engine_room,bridge,crew_mess,crew_cabins,other',
            'order' => 'nullable|integer|min:0',
        ]);

        $yacht = Yacht::findOrFail($yachtId);
        
        // Check authorization - user must be able to update the yacht to add gallery images
        Gate::authorize('update', $yacht);

        // Check gallery limit (max 20 images)
        $currentCount = $yacht->gallery()->count();
        if ($currentCount >= 20) {
            return $this->errorResponse('Maximum 20 images allowed per yacht.', 422);
        }

        $path = $request->file('image')->store('yacht-gallery', 'public');

        $gallery = YachtGallery::create([
            'yacht_id' => $yachtId,
            'image_path' => $path,
            'caption' => $request->input('caption'),
            'category' => $request->input('category', 'other'),
            'order' => $request->input('order', $currentCount),
        ]);

        return response()->json([
            'message' => 'Image added successfully.',
            'data' => $gallery->load('yacht'),
        ], 201);
    }

    public function deleteGalleryImage($yachtId, $imageId): JsonResponse
    {
        $gallery = YachtGallery::where('yacht_id', $yachtId)->findOrFail($imageId);
        $yacht = Yacht::findOrFail($yachtId);
        
        // Check authorization - user must be able to update the yacht to delete gallery images
        Gate::authorize('update', $yacht);

        if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
            Storage::disk('public')->delete($gallery->image_path);
        }

        $gallery->delete();

        return response()->json(['message' => 'Image deleted successfully.']);
    }

    public function updateGalleryImageOrder(Request $request, $yachtId): JsonResponse
    {
        $request->validate([
            'images' => 'required|array',
            'images.*.id' => 'required|exists:yacht_galleries,id',
            'images.*.order' => 'required|integer|min:0',
        ]);

        $yacht = Yacht::findOrFail($yachtId);
        
        // Check authorization - user must be able to update the yacht to reorder gallery images
        Gate::authorize('update', $yacht);

        foreach ($request->input('images') as $imageData) {
            YachtGallery::where('yacht_id', $yachtId)
                ->where('id', $imageData['id'])
                ->update(['order' => $imageData['order']]);
        }

        return response()->json(['message' => 'Image order updated successfully.']);
    }
}

