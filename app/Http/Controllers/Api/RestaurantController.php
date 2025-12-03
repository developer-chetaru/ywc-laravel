<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            ->firstOrFail();

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
}


