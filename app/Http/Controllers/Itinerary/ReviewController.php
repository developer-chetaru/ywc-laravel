<?php

namespace App\Http\Controllers\Itinerary;

use App\Http\Controllers\Controller;
use App\Models\ItineraryRoute;
use App\Models\ItineraryRouteReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/itinerary/routes/{route}/reviews",
     *     summary="Get reviews for a route",
     *     tags={"Itinerary Reviews"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Route ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of reviews",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=403, description="Access denied")
     * )
     */
    public function index(ItineraryRoute $route): JsonResponse
    {
        Gate::authorize('view', $route);

        $reviews = $route->reviews()
            ->with('user:id,first_name,last_name,profile_photo_path')
            ->where('status', 'published')
            ->latest()
            ->paginate(15);

        return response()->json($reviews);
    }

    /**
     * @OA\Post(
     *     path="/api/itinerary/routes/{route}/reviews",
     *     summary="Create a review for a route",
     *     tags={"Itinerary Reviews"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Route ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"rating"},
     *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5, example=5),
     *             @OA\Property(property="comment", type="string", example="Great route! Highly recommended."),
     *             @OA\Property(property="media", type="array", @OA\Items(type="string"), example={"url1", "url2"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review submitted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Review submitted."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request, ItineraryRoute $route): JsonResponse
    {
        Gate::authorize('view', $route);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string'],
            'media' => ['nullable', 'array'],
            'media.*' => ['string'],
        ]);

        $review = $route->reviews()->updateOrCreate(
            ['user_id' => $request->user()->id],
            array_merge($data, ['status' => 'published'])
        );

        $this->syncRouteRating($route);

        return response()->json([
            'message' => 'Review submitted.',
            'data' => $review->fresh('user:id,first_name,last_name'),
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/itinerary/routes/{route}/reviews/{review}",
     *     summary="Update a review",
     *     tags={"Itinerary Reviews"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Route ID"
     *     ),
     *     @OA\Parameter(
     *         name="review",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Review ID"
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5),
     *             @OA\Property(property="comment", type="string"),
     *             @OA\Property(property="status", type="string", enum={"pending", "published", "flagged"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Review updated."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied"),
     *     @OA\Response(response=404, description="Review not found")
     * )
     */
    public function update(Request $request, ItineraryRoute $route, ItineraryRouteReview $review): JsonResponse
    {
        Gate::authorize('update', $route);
        abort_unless($review->route_id === $route->id, 404);

        $data = $request->validate([
            'rating' => ['sometimes', 'integer', 'between:1,5'],
            'comment' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', Rule::in(['pending', 'published', 'flagged'])],
        ]);

        $review->update($data);
        $this->syncRouteRating($route);

        return response()->json([
            'message' => 'Review updated.',
            'data' => $review->fresh('user:id,first_name,last_name'),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/itinerary/routes/{route}/reviews/{review}",
     *     summary="Delete a review",
     *     tags={"Itinerary Reviews"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Route ID"
     *     ),
     *     @OA\Parameter(
     *         name="review",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Review ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review removed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Review removed.")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied"),
     *     @OA\Response(response=404, description="Review not found")
     * )
     */
    public function destroy(ItineraryRoute $route, ItineraryRouteReview $review): JsonResponse
    {
        Gate::authorize('update', $route);
        abort_unless($review->route_id === $route->id, 404);

        $review->delete();
        $this->syncRouteRating($route);

        return response()->json([
            'message' => 'Review removed.',
        ]);
    }

    protected function syncRouteRating(ItineraryRoute $route): void
    {
        $aggregate = $route->reviews()
            ->where('status', 'published')
            ->selectRaw('count(*) as count, avg(rating) as avg')
            ->first();

        $route->rating_count = (int) ($aggregate->count ?? 0);
        $route->rating_avg = $route->rating_count > 0 ? round((float) $aggregate->avg, 2) : 0;
        $route->save();

        if ($route->statistics) {
            $route->statistics->update([
                'reviews_count' => $route->rating_count ?? 0,
                'rating_avg' => $route->rating_avg,
            ]);
        }
    }
}

