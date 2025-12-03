<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contractor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContractorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Contractor::query()
            ->withCount('reviews')
            ->when($request->input('search'), function ($q, $search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('business_name', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('country', 'like', "%{$search}%");
                });
            })
            ->when($request->input('category'), fn ($q, $category) => $q->where('category', $category))
            ->when($request->input('min_rating'), fn ($q, $rating) => $q->where('rating_avg', '>=', $rating))
            ->orderByDesc('rating_avg')
            ->orderByDesc('reviews_count')
            ->orderBy('name');

        $contractors = $query->paginate($request->input('per_page', 15));

        return response()->json($contractors);
    }

    public function show(string $slug): JsonResponse
    {
        $contractor = Contractor::where('slug', $slug)
            ->withCount('reviews')
            ->firstOrFail();

        return response()->json(['data' => $contractor]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'specialties' => 'nullable|array',
            'specialties.*' => 'string',
            'languages' => 'nullable|array',
            'languages.*' => 'string',
            'emergency_service' => 'boolean',
            'response_time' => 'nullable|string|max:255',
            'service_area' => 'nullable|string|max:255',
            'price_range' => 'nullable|string|max:255',
        ]);

        $contractor = Contractor::create($validated);

        return response()->json([
            'message' => 'Contractor created successfully.',
            'data' => $contractor,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $contractor = Contractor::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'category' => 'nullable|string',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'specialties' => 'nullable|array',
            'specialties.*' => 'string',
            'languages' => 'nullable|array',
            'languages.*' => 'string',
            'emergency_service' => 'boolean',
            'response_time' => 'nullable|string|max:255',
            'service_area' => 'nullable|string|max:255',
            'price_range' => 'nullable|string|max:255',
            'is_verified' => 'nullable|boolean',
        ]);

        $contractor->update($validated);

        return response()->json([
            'message' => 'Contractor updated successfully.',
            'data' => $contractor,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $contractor = Contractor::findOrFail($id);
        $contractor->delete();

        return response()->json(['message' => 'Contractor deleted successfully.']);
    }
}


