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
        ], 201);
    }

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

