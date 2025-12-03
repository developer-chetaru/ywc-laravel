<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\RestaurantReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RestaurantReviewController extends Controller
{
    public function index(Request $request, int $restaurantId): JsonResponse
    {
        $restaurant = Restaurant::findOrFail($restaurantId);

        $query = $restaurant->reviews()
            ->with(['user:id,first_name,last_name,profile_photo_path'])
            ->where('is_approved', true)
            ->when($request->input('rating'), fn ($q, $rating) => $q->where('overall_rating', $rating))
            ->when($request->input('sort') === 'recent', fn ($q) => $q->orderByDesc('created_at'))
            ->when($request->input('sort') === 'rating', fn ($q) => $q->orderByDesc('overall_rating'))
            ->when(!$request->input('sort') || $request->input('sort') === 'helpful', fn ($q) => $q->orderByDesc('helpful_count'));

        $reviews = $query->paginate($request->input('per_page', 10));

        return response()->json($reviews);
    }

    public function show(int $restaurantId, int $reviewId): JsonResponse
    {
        $review = RestaurantReview::where('restaurant_id', $restaurantId)
            ->where('id', $reviewId)
            ->where('is_approved', true)
            ->with(['user:id,first_name,last_name,profile_photo_path', 'photos', 'comments'])
            ->firstOrFail();

        return response()->json(['data' => $review]);
    }

    public function store(Request $request, int $restaurantId): JsonResponse
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'review' => 'required|string|min:20',
            'overall_rating' => 'required|integer|min:1|max:5',
            'food_rating' => 'nullable|integer|min:1|max:5',
            'service_rating' => 'nullable|integer|min:1|max:5',
            'atmosphere_rating' => 'nullable|integer|min:1|max:5',
            'value_rating' => 'nullable|integer|min:1|max:5',
            'would_recommend' => 'boolean',
            'is_anonymous' => 'boolean',
            'visit_date' => 'nullable|date',
            'crew_tips' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['restaurant_id'] = $restaurant->id;
        $data['user_id'] = $user->id;
        $data['is_verified'] = true;
        $data['is_approved'] = true;

        $review = RestaurantReview::create($data);

        return response()->json([
            'message' => 'Review submitted successfully.',
            'data' => $review->load(['user']),
        ], 201);
    }

    public function update(Request $request, int $restaurantId, int $reviewId): JsonResponse
    {
        $review = RestaurantReview::where('restaurant_id', $restaurantId)
            ->where('id', $reviewId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'review' => 'sometimes|required|string|min:20',
            'overall_rating' => 'sometimes|required|integer|min:1|max:5',
            'food_rating' => 'nullable|integer|min:1|max:5',
            'service_rating' => 'nullable|integer|min:1|max:5',
            'atmosphere_rating' => 'nullable|integer|min:1|max:5',
            'value_rating' => 'nullable|integer|min:1|max:5',
            'would_recommend' => 'boolean',
            'is_anonymous' => 'boolean',
            'visit_date' => 'nullable|date',
            'crew_tips' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $review->update($validator->validated());

        return response()->json([
            'message' => 'Review updated successfully.',
            'data' => $review->fresh(['user']),
        ]);
    }

    public function destroy(int $restaurantId, int $reviewId, Request $request): JsonResponse
    {
        $review = RestaurantReview::where('restaurant_id', $restaurantId)
            ->where('id', $reviewId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $review->delete();

        return response()->json(['message' => 'Review deleted successfully.']);
    }

    public function vote(Request $request, int $restaurantId, int $reviewId): JsonResponse
    {
        $review = RestaurantReview::where('restaurant_id', $restaurantId)
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
        $existingVote = $review->votes()->where('user_id', $user->id)->first();
        $isHelpful = $request->boolean('is_helpful');

        if ($existingVote) {
            if ($existingVote->is_helpful === $isHelpful) {
                $existingVote->delete();
                if ($isHelpful) {
                    $review->decrement('helpful_count');
                } else {
                    $review->decrement('not_helpful_count');
                }
            } else {
                $existingVote->update(['is_helpful' => $isHelpful]);
                if ($isHelpful) {
                    $review->increment('helpful_count');
                    $review->decrement('not_helpful_count');
                } else {
                    $review->increment('not_helpful_count');
                    $review->decrement('helpful_count');
                }
            }
        } else {
            $review->votes()->create([
                'user_id' => $user->id,
                'reviewable_type' => RestaurantReview::class,
                'reviewable_id' => $review->id,
                'review_id' => $review->id,
                'is_helpful' => $isHelpful,
            ]);

            if ($isHelpful) {
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


