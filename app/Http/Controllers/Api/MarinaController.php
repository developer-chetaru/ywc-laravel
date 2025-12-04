<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Marina;
use App\Models\MarinaGallery;
use App\Services\MarinaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarinaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Marina::query()
            ->withCount('reviews')
            ->when($request->input('search'), function ($q, $search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('country', 'like', "%{$search}%");
                });
            })
            ->when($request->input('country'), fn ($q, $country) => $q->where('country', $country))
            ->when($request->input('type'), fn ($q, $type) => $q->where('type', $type))
            ->when($request->input('min_rating'), fn ($q, $rating) => $q->where('rating_avg', '>=', $rating))
            ->orderByDesc('rating_avg')
            ->orderByDesc('reviews_count')
            ->orderBy('name');

        $marinas = $query->paginate($request->input('per_page', 15));

        // Add cover_image_url to each marina (optimized)
        $baseUrl = Storage::disk('public')->url('');
        $marinas->getCollection()->transform(function ($marina) use ($baseUrl) {
            if ($marina->cover_image) {
                $marina->cover_image_url = $baseUrl . $marina->cover_image;
            }
            return $marina;
        });

        return response()->json($marinas);
    }

    public function show($slug): JsonResponse
    {
        $marina = Marina::where('slug', $slug)
            ->withCount('reviews')
            ->with('gallery')
            ->firstOrFail();
        
        // Add image URLs to gallery items
        if ($marina->gallery) {
            $marina->gallery->transform(function ($item) {
                $item->image_url = $item->image_url;
                return $item;
            });
        }

        if ($marina->cover_image) {
            $marina->cover_image_url = Storage::disk('public')->url($marina->cover_image);
        }

        return response()->json(['data' => $marina]);
    }

    public function store(Request $request, MarinaService $service): JsonResponse
    {
        try {
            $marina = $service->create($request->all(), $request->file('cover_image'));

            return response()->json([
                'message' => 'Marina created successfully.',
                'data' => $marina,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function update(Request $request, $id, MarinaService $service): JsonResponse
    {
        try {
            $marina = Marina::findOrFail($id);
            $marina = $service->update($marina, $request->all(), $request->file('cover_image'));

            return response()->json([
                'message' => 'Marina updated successfully.',
                'data' => $marina,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function destroy($id, MarinaService $service): JsonResponse
    {
        $marina = Marina::findOrFail($id);
        $service->delete($marina);

        return response()->json(['message' => 'Marina deleted successfully.']);
    }

    // Gallery endpoints
    public function addGalleryImage(Request $request, $marinaId): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|max:5120',
            'caption' => 'nullable|string|max:255',
            'category' => 'nullable|in:facilities,berths,amenities,restaurant,shop,other',
            'order' => 'nullable|integer|min:0',
        ]);

        $marina = Marina::findOrFail($marinaId);

        // Check gallery limit (max 20 images)
        $currentCount = $marina->gallery()->count();
        if ($currentCount >= 20) {
            return response()->json(['error' => 'Maximum 20 images allowed per marina.'], 422);
        }

        $path = $request->file('image')->store('marinas/gallery', 'public');

        $gallery = MarinaGallery::create([
            'marina_id' => $marinaId,
            'image_path' => $path,
            'caption' => $request->input('caption'),
            'category' => $request->input('category', 'other'),
            'order' => $request->input('order', $currentCount),
        ]);

        return response()->json([
            'message' => 'Image added successfully.',
            'data' => $gallery->load('marina'),
        ], 200);
    }

    public function deleteGalleryImage($marinaId, $imageId): JsonResponse
    {
        $gallery = MarinaGallery::where('marina_id', $marinaId)->findOrFail($imageId);

        if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
            Storage::disk('public')->delete($gallery->image_path);
        }

        $gallery->delete();

        return response()->json(['message' => 'Image deleted successfully.']);
    }

    public function updateGalleryImageOrder(Request $request, $marinaId): JsonResponse
    {
        $request->validate([
            'images' => 'required|array',
            'images.*.id' => 'required|exists:marina_galleries,id',
            'images.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->input('images') as $imageData) {
            MarinaGallery::where('marina_id', $marinaId)
                ->where('id', $imageData['id'])
                ->update(['order' => $imageData['order']]);
        }

        return response()->json(['message' => 'Image order updated successfully.']);
    }
}

