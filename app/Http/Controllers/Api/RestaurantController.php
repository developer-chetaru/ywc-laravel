<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\RestaurantGallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RestaurantController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Restaurant::query()
            ->withCount('reviews')
            ->when($request->input('search'), function ($q, $search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('country', 'like', "%{$search}%");
                });
            })
            ->when($request->input('type'), fn ($q, $type) => $q->where('type', $type))
            ->when($request->input('min_rating'), fn ($q, $rating) => $q->where('rating_avg', '>=', $rating))
            ->orderByDesc('rating_avg')
            ->orderByDesc('reviews_count')
            ->orderBy('name');

        $restaurants = $query->paginate($request->input('per_page', 15));

        return response()->json($restaurants);
    }

    public function show(string $slug): JsonResponse
    {
        $restaurant = Restaurant::where('slug', $slug)
            ->withCount('reviews')
            ->with('gallery')
            ->firstOrFail();

        // Add image URLs to gallery items
        if ($restaurant->gallery) {
            $restaurant->gallery->transform(function ($item) {
                $item->image_url = $item->image_url;
                return $item;
            });
        }

        return response()->json(['data' => $restaurant]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'cuisine_type' => 'nullable|array',
            'cuisine_type.*' => 'string',
            'price_range' => 'nullable|string|max:50',
            'opening_hours' => 'nullable|array',
            'crew_friendly' => 'boolean',
            'crew_discount' => 'boolean',
            'crew_discount_details' => 'nullable|string',
            'is_verified' => 'boolean',
        ]);

        $restaurant = Restaurant::create($validated);

        return response()->json([
            'message' => 'Restaurant created successfully.',
            'data' => $restaurant,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $restaurant = Restaurant::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'cuisine_type' => 'nullable|array',
            'cuisine_type.*' => 'string',
            'price_range' => 'nullable|string|max:50',
            'opening_hours' => 'nullable|array',
            'crew_friendly' => 'boolean',
            'crew_discount' => 'boolean',
            'crew_discount_details' => 'nullable|string',
            'is_verified' => 'boolean',
        ]);

        $restaurant->update($validated);

        return response()->json([
            'message' => 'Restaurant updated successfully.',
            'data' => $restaurant,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $restaurant = Restaurant::findOrFail($id);
        $restaurant->delete();

        return response()->json(['message' => 'Restaurant deleted successfully.']);
    }

    // Gallery endpoints
    public function addGalleryImage(Request $request, $restaurantId): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|max:5120',
            'caption' => 'nullable|string|max:255',
            'category' => 'nullable|in:interior,exterior,food,menu,atmosphere,other',
            'order' => 'nullable|integer|min:0',
        ]);

        $restaurant = Restaurant::findOrFail($restaurantId);

        $currentCount = $restaurant->gallery()->count();
        if ($currentCount >= 20) {
            return response()->json(['error' => 'Maximum 20 images allowed per restaurant.'], 422);
        }

        $path = $request->file('image')->store('restaurants/gallery', 'public');

        $gallery = RestaurantGallery::create([
            'restaurant_id' => $restaurantId,
            'image_path' => $path,
            'caption' => $request->input('caption'),
            'category' => $request->input('category', 'other'),
            'order' => $request->input('order', $currentCount),
        ]);

        return response()->json([
            'message' => 'Image added successfully.',
            'data' => $gallery->load('restaurant'),
        ], 200);
    }

    public function deleteGalleryImage($restaurantId, $imageId): JsonResponse
    {
        $gallery = RestaurantGallery::where('restaurant_id', $restaurantId)->findOrFail($imageId);

        if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
            Storage::disk('public')->delete($gallery->image_path);
        }

        $gallery->delete();

        return response()->json(['message' => 'Image deleted successfully.']);
    }

    public function updateGalleryImageOrder(Request $request, $restaurantId): JsonResponse
    {
        $request->validate([
            'images' => 'required|array',
            'images.*.id' => 'required|exists:restaurant_galleries,id',
            'images.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->input('images') as $imageData) {
            RestaurantGallery::where('restaurant_id', $restaurantId)
                ->where('id', $imageData['id'])
                ->update(['order' => $imageData['order']]);
        }

        return response()->json(['message' => 'Image order updated successfully.']);
    }
}


