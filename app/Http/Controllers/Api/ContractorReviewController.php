<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contractor;
use App\Models\ContractorReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContractorReviewController extends Controller
{
    public function index(Request $request, int $contractorId): JsonResponse
    {
        $contractor = Contractor::findOrFail($contractorId);

        $query = $contractor->reviews()
            ->with(['user:id,first_name,last_name,profile_photo_path'])
            ->where('is_approved', true)
            ->when($request->input('rating'), fn ($q, $rating) => $q->where('overall_rating', $rating))
            ->when($request->input('sort') === 'recent', fn ($q) => $q->orderByDesc('created_at'))
            ->when($request->input('sort') === 'rating', fn ($q) => $q->orderByDesc('overall_rating'))
            ->when(!$request->input('sort') || $request->input('sort') === 'helpful', fn ($q) => $q->orderByDesc('helpful_count'));

        $reviews = $query->paginate($request->input('per_page', 10));

        return response()->json($reviews);
    }

    public function show(int $contractorId, int $reviewId): JsonResponse
    {
        $review = ContractorReview::where('contractor_id', $contractorId)
            ->where('id', $reviewId)
            ->where('is_approved', true)
            ->with(['user:id,first_name,last_name,profile_photo_path', 'photos', 'comments'])
            ->firstOrFail();

        return response()->json(['data' => $review]);
    }

    public function store(Request $request, int $contractorId): JsonResponse
    {
        $contractor = Contractor::findOrFail($contractorId);
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'review' => 'required|string|min:20',
            'service_type' => 'nullable|string|max:255',
            'service_cost' => 'nullable|numeric|min:0',
            'timeframe' => 'nullable|string|max:255',
            'overall_rating' => 'required|integer|min:1|max:5',
            'quality_rating' => 'nullable|integer|min:1|max:5',
            'professionalism_rating' => 'nullable|integer|min:1|max:5',
            'pricing_rating' => 'nullable|integer|min:1|max:5',
            'timeliness_rating' => 'nullable|integer|min:1|max:5',
            'would_recommend' => 'boolean',
            'would_hire_again' => 'boolean',
            'is_anonymous' => 'boolean',
            'service_date' => 'nullable|date',
            'yacht_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['contractor_id'] = $contractor->id;
        $data['user_id'] = $user->id;
        $data['is_verified'] = true;
        $data['is_approved'] = true;

        $review = ContractorReview::create($data);

        return response()->json([
            'message' => 'Review submitted successfully.',
            'data' => $review->load(['user']),
        ], 201);
    }

    public function update(Request $request, int $contractorId, int $reviewId): JsonResponse
    {
        $review = ContractorReview::where('contractor_id', $contractorId)
            ->where('id', $reviewId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'review' => 'sometimes|required|string|min:20',
            'service_type' => 'nullable|string|max:255',
            'service_cost' => 'nullable|numeric|min:0',
            'timeframe' => 'nullable|string|max:255',
            'overall_rating' => 'sometimes|required|integer|min:1|max:5',
            'quality_rating' => 'nullable|integer|min:1|max:5',
            'professionalism_rating' => 'nullable|integer|min:1|max:5',
            'pricing_rating' => 'nullable|integer|min:1|max:5',
            'timeliness_rating' => 'nullable|integer|min:1|max:5',
            'would_recommend' => 'boolean',
            'would_hire_again' => 'boolean',
            'is_anonymous' => 'boolean',
            'service_date' => 'nullable|date',
            'yacht_name' => 'nullable|string|max:255',
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

    public function destroy(int $contractorId, int $reviewId, Request $request): JsonResponse
    {
        $review = ContractorReview::where('contractor_id', $contractorId)
            ->where('id', $reviewId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $review->delete();

        return response()->json(['message' => 'Review deleted successfully.']);
    }

    public function vote(Request $request, int $contractorId, int $reviewId): JsonResponse
    {
        $review = ContractorReview::where('contractor_id', $contractorId)
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

        if ($existingVote) {
            if ($existingVote->is_helpful === $request->boolean('is_helpful')) {
                $existingVote->delete();
                if ($request->boolean('is_helpful')) {
                    $review->decrement('helpful_count');
                } else {
                    $review->decrement('not_helpful_count');
                }
            } else {
                $existingVote->update(['is_helpful' => $request->boolean('is_helpful')]);
                if ($request->boolean('is_helpful')) {
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
                'reviewable_type' => ContractorReview::class,
                'reviewable_id' => $review->id,
                'review_id' => $review->id,
                'is_helpful' => $request->boolean('is_helpful'),
            ]);

            if ($request->boolean('is_helpful')) {
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


