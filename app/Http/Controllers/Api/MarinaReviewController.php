<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Marina;
use App\Models\MarinaReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MarinaReviewController extends Controller
{
    public function index(Request $request, $marinaId): JsonResponse
    {
        $marina = Marina::findOrFail($marinaId);

        $query = $marina->reviews()
            ->with(['user:id,first_name,last_name,profile_photo_path'])
            ->where('is_approved', true)
            ->when($request->input('rating'), fn ($q, $rating) => $q->where('overall_rating', $rating))
            ->when($request->input('sort') === 'recent', fn ($q) => $q->orderByDesc('created_at'))
            ->when($request->input('sort') === 'rating', fn ($q) => $q->orderByDesc('overall_rating'))
            ->when(!$request->input('sort') || $request->input('sort') === 'helpful', fn ($q) => $q->orderByDesc('helpful_count'));

        $reviews = $query->paginate($request->input('per_page', 10));

        // Add photo URLs
        $reviews->getCollection()->transform(function ($review) {
            $review->photos->transform(function ($photo) {
                $photo->photo_url = Storage::disk('public')->url($photo->photo_path);
                return $photo;
            });
            return $review;
        });

        return response()->json($reviews);
    }

    public function show($marinaId, $reviewId): JsonResponse
    {
        $review = MarinaReview::where('marina_id', $marinaId)
            ->where('id', $reviewId)
            ->where('is_approved', true)
            ->with(['user:id,first_name,last_name,profile_photo_path', 'photos'])
            ->firstOrFail();

        $review->photos->transform(function ($photo) {
            $photo->photo_url = Storage::disk('public')->url($photo->photo_path);
            return $photo;
        });

        return response()->json(['data' => $review]);
    }

    public function store(Request $request, $marinaId): JsonResponse
    {
        $marina = Marina::findOrFail($marinaId);
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'review' => 'required|string|min:50',
            'tips_tricks' => 'nullable|string',
            'overall_rating' => 'required|integer|min:1|max:5',
            'fuel_rating' => 'nullable|integer|min:1|max:5',
            'water_rating' => 'nullable|integer|min:1|max:5',
            'electricity_rating' => 'nullable|integer|min:1|max:5',
            'wifi_rating' => 'nullable|integer|min:1|max:5',
            'showers_rating' => 'nullable|integer|min:1|max:5',
            'laundry_rating' => 'nullable|integer|min:1|max:5',
            'maintenance_rating' => 'nullable|integer|min:1|max:5',
            'provisioning_rating' => 'nullable|integer|min:1|max:5',
            'staff_rating' => 'nullable|integer|min:1|max:5',
            'value_rating' => 'nullable|integer|min:1|max:5',
            'protection_rating' => 'nullable|integer|min:1|max:5',
            'is_anonymous' => 'boolean',
            'visit_date' => 'nullable|date',
            'yacht_length_meters' => 'nullable|string|max:50',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['marina_id'] = $marinaId;
        $data['user_id'] = $user->id;
        $data['is_verified'] = true;
        $data['is_approved'] = true;

        $photos = $request->file('photos', []);
        unset($data['photos']);

        $review = MarinaReview::create($data);

        // Handle photo uploads
        foreach ($photos as $photo) {
            $path = $photo->store('review-photos', 'public');
            $review->photos()->create([
                'reviewable_type' => MarinaReview::class,
                'reviewable_id' => $review->id,
                'review_id' => $review->id,
                'photo_path' => $path,
            ]);
        }

        return response()->json([
            'message' => 'Review submitted successfully.',
            'data' => $review->fresh(['user', 'photos']),
        ], 201);
    }

    public function update(Request $request, $marinaId, $reviewId): JsonResponse
    {
        $review = MarinaReview::where('marina_id', $marinaId)
            ->where('id', $reviewId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'review' => 'sometimes|required|string|min:50',
            'tips_tricks' => 'nullable|string',
            'overall_rating' => 'sometimes|required|integer|min:1|max:5',
            'fuel_rating' => 'nullable|integer|min:1|max:5',
            'water_rating' => 'nullable|integer|min:1|max:5',
            'electricity_rating' => 'nullable|integer|min:1|max:5',
            'wifi_rating' => 'nullable|integer|min:1|max:5',
            'showers_rating' => 'nullable|integer|min:1|max:5',
            'laundry_rating' => 'nullable|integer|min:1|max:5',
            'maintenance_rating' => 'nullable|integer|min:1|max:5',
            'provisioning_rating' => 'nullable|integer|min:1|max:5',
            'staff_rating' => 'nullable|integer|min:1|max:5',
            'value_rating' => 'nullable|integer|min:1|max:5',
            'protection_rating' => 'nullable|integer|min:1|max:5',
            'is_anonymous' => 'boolean',
            'visit_date' => 'nullable|date',
            'yacht_length_meters' => 'nullable|string|max:50',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $photos = $request->file('photos', []);
        unset($data['photos']);

        $review->update($data);

        // Handle new photo uploads
        foreach ($photos as $photo) {
            $path = $photo->store('review-photos', 'public');
            $review->photos()->create([
                'reviewable_type' => MarinaReview::class,
                'reviewable_id' => $review->id,
                'review_id' => $review->id,
                'photo_path' => $path,
            ]);
        }

        return response()->json([
            'message' => 'Review updated successfully.',
            'data' => $review->fresh(['user', 'photos']),
        ]);
    }

    public function destroy($marinaId, $reviewId, Request $request): JsonResponse
    {
        $review = MarinaReview::where('marina_id', $marinaId)
            ->where('id', $reviewId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $review->delete();

        return response()->json(['message' => 'Review deleted successfully.']);
    }

    public function vote(Request $request, $marinaId, $reviewId): JsonResponse
    {
        $review = MarinaReview::where('marina_id', $marinaId)
            ->where('id', $reviewId)
            ->where('is_approved', true)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'is_helpful' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $existingVote = $review->userVote($user->id);

        if ($existingVote) {
            if ($existingVote->is_helpful === $request->is_helpful) {
                // Remove vote
                $existingVote->delete();
                if ($request->is_helpful) {
                    $review->decrement('helpful_count');
                } else {
                    $review->decrement('not_helpful_count');
                }
            } else {
                // Change vote
                $existingVote->update(['is_helpful' => $request->is_helpful]);
                if ($request->is_helpful) {
                    $review->increment('helpful_count');
                    $review->decrement('not_helpful_count');
                } else {
                    $review->increment('not_helpful_count');
                    $review->decrement('helpful_count');
                }
            }
        } else {
            // New vote
            $review->votes()->create([
                'user_id' => $user->id,
                'reviewable_type' => MarinaReview::class,
                'reviewable_id' => $review->id,
                'is_helpful' => $request->is_helpful,
            ]);
            if ($request->is_helpful) {
                $review->increment('helpful_count');
            } else {
                $review->increment('not_helpful_count');
            }
        }

        return response()->json([
            'message' => 'Vote recorded.',
            'data' => $review->fresh(),
        ]);
    }
}

