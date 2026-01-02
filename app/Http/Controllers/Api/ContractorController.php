<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponseTrait;
use App\Models\Contractor;
use App\Models\ContractorGallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContractorController extends Controller
{
    use ApiResponseTrait;
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
            ->with('gallery')
            ->firstOrFail();

        // Add image URLs to gallery items
        if ($contractor->gallery) {
            $contractor->gallery->transform(function ($item) {
                $item->image_url = $item->image_url;
                return $item;
            });
        }

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

    // Gallery endpoints
    public function addGalleryImage(Request $request, $contractorId): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
            'caption' => 'nullable|string|max:255',
            'category' => 'nullable|in:work_samples,equipment,team,facilities,other',
            'order' => 'nullable|integer|min:0',
        ]);

        $contractor = Contractor::findOrFail($contractorId);

        $currentCount = $contractor->gallery()->count();
        if ($currentCount >= 20) {
            return $this->errorResponse('Maximum 20 images allowed per contractor.', 422);
        }

        $path = $request->file('image')->store('contractors/gallery', 'public');

        $gallery = ContractorGallery::create([
            'contractor_id' => $contractorId,
            'image_path' => $path,
            'caption' => $request->input('caption'),
            'category' => $request->input('category', 'other'),
            'order' => $request->input('order', $currentCount),
        ]);

        return response()->json([
            'message' => 'Image added successfully.',
            'data' => $gallery->load('contractor'),
        ], 200);
    }

    public function deleteGalleryImage($contractorId, $imageId): JsonResponse
    {
        $gallery = ContractorGallery::where('contractor_id', $contractorId)->findOrFail($imageId);

        if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
            Storage::disk('public')->delete($gallery->image_path);
        }

        $gallery->delete();

        return response()->json(['message' => 'Image deleted successfully.']);
    }

    public function updateGalleryImageOrder(Request $request, $contractorId): JsonResponse
    {
        $request->validate([
            'images' => 'required|array',
            'images.*.id' => 'required|exists:contractor_galleries,id',
            'images.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->input('images') as $imageData) {
            ContractorGallery::where('contractor_id', $contractorId)
                ->where('id', $imageData['id'])
                ->update(['order' => $imageData['order']]);
        }

        return response()->json(['message' => 'Image order updated successfully.']);
    }
}


