<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Broker;
use App\Models\BrokerGallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrokerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Broker::query()
            ->withCount('reviews')
            ->when($request->input('search'), function ($q, $search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('business_name', 'like', "%{$search}%")
                        ->orWhere('primary_location', 'like', "%{$search}%");
                });
            })
            ->when($request->input('min_rating'), fn ($q, $rating) => $q->where('rating_avg', '>=', $rating))
            ->orderByDesc('rating_avg')
            ->orderByDesc('reviews_count')
            ->orderBy('name');

        $brokers = $query->paginate($request->input('per_page', 15));

        return response()->json($brokers);
    }

    public function show(string $slug): JsonResponse
    {
        $broker = Broker::where('slug', $slug)
            ->withCount('reviews')
            ->with('gallery')
            ->firstOrFail();

        // Add image URLs to gallery items
        if ($broker->gallery) {
            $broker->gallery->transform(function ($item) {
                $item->image_url = $item->image_url;
                return $item;
            });
        }

        return response()->json(['data' => $broker]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'primary_location' => 'nullable|string|max:255',
            'office_locations' => 'nullable|array',
            'office_locations.*' => 'string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'specialties' => 'nullable|array',
            'specialties.*' => 'string',
            'fee_structure' => 'nullable|string',
            'regions_served' => 'nullable|array',
            'regions_served.*' => 'string',
            'years_in_business' => 'nullable|integer|min:0',
            'is_myba_member' => 'boolean',
            'is_licensed' => 'boolean',
            'is_verified' => 'boolean',
            'certifications' => 'nullable|array',
            'certifications.*' => 'string',
        ]);

        $broker = Broker::create($validated);

        return response()->json([
            'message' => 'Broker created successfully.',
            'data' => $broker,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $broker = Broker::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'primary_location' => 'nullable|string|max:255',
            'office_locations' => 'nullable|array',
            'office_locations.*' => 'string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'specialties' => 'nullable|array',
            'specialties.*' => 'string',
            'fee_structure' => 'nullable|string',
            'regions_served' => 'nullable|array',
            'regions_served.*' => 'string',
            'years_in_business' => 'nullable|integer|min:0',
            'is_myba_member' => 'boolean',
            'is_licensed' => 'boolean',
            'is_verified' => 'boolean',
            'certifications' => 'nullable|array',
            'certifications.*' => 'string',
        ]);

        $broker->update($validated);

        return response()->json([
            'message' => 'Broker updated successfully.',
            'data' => $broker,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $broker = Broker::findOrFail($id);
        $broker->delete();

        return response()->json(['message' => 'Broker deleted successfully.']);
    }

    // Gallery endpoints
    public function addGalleryImage(Request $request, $brokerId): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|max:5120',
            'caption' => 'nullable|string|max:255',
            'category' => 'nullable|in:office,team,events,certifications,other',
            'order' => 'nullable|integer|min:0',
        ]);

        $broker = Broker::findOrFail($brokerId);

        $currentCount = $broker->gallery()->count();
        if ($currentCount >= 20) {
            return response()->json(['error' => 'Maximum 20 images allowed per broker.'], 422);
        }

        $path = $request->file('image')->store('brokers/gallery', 'public');

        $gallery = BrokerGallery::create([
            'broker_id' => $brokerId,
            'image_path' => $path,
            'caption' => $request->input('caption'),
            'category' => $request->input('category', 'other'),
            'order' => $request->input('order', $currentCount),
        ]);

        return response()->json([
            'message' => 'Image added successfully.',
            'data' => $gallery->load('broker'),
        ], 200);
    }

    public function deleteGalleryImage($brokerId, $imageId): JsonResponse
    {
        $gallery = BrokerGallery::where('broker_id', $brokerId)->findOrFail($imageId);

        if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
            Storage::disk('public')->delete($gallery->image_path);
        }

        $gallery->delete();

        return response()->json(['message' => 'Image deleted successfully.']);
    }

    public function updateGalleryImageOrder(Request $request, $brokerId): JsonResponse
    {
        $request->validate([
            'images' => 'required|array',
            'images.*.id' => 'required|exists:broker_galleries,id',
            'images.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->input('images') as $imageData) {
            BrokerGallery::where('broker_id', $brokerId)
                ->where('id', $imageData['id'])
                ->update(['order' => $imageData['order']]);
        }

        return response()->json(['message' => 'Image order updated successfully.']);
    }
}


