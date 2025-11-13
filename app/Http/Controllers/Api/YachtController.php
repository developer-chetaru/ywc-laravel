<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Yacht;
use App\Services\YachtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class YachtController extends Controller
{
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
            ->firstOrFail();

        if ($yacht->cover_image) {
            $yacht->cover_image_url = Storage::disk('public')->url($yacht->cover_image);
        }

        return response()->json(['data' => $yacht]);
    }

    public function store(Request $request, YachtService $service): JsonResponse
    {
        try {
            $yacht = $service->create($request->all(), $request->file('cover_image'));

            return response()->json([
                'message' => 'Yacht created successfully.',
                'data' => $yacht,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function update(Request $request, $id, YachtService $service): JsonResponse
    {
        try {
            $yacht = Yacht::findOrFail($id);
            $yacht = $service->update($yacht, $request->all(), $request->file('cover_image'));

            return response()->json([
                'message' => 'Yacht updated successfully.',
                'data' => $yacht,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function destroy($id, YachtService $service): JsonResponse
    {
        $yacht = Yacht::findOrFail($id);
        $service->delete($yacht);

        return response()->json(['message' => 'Yacht deleted successfully.']);
    }
}

