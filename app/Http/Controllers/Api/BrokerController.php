<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Broker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            ->firstOrFail();

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
}


