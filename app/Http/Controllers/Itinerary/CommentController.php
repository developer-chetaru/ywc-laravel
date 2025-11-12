<?php

namespace App\Http\Controllers\Itinerary;

use App\Http\Controllers\Controller;
use App\Models\ItineraryRoute;
use App\Models\ItineraryRouteComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    public function index(ItineraryRoute $route): JsonResponse
    {
        Gate::authorize('view', $route);

        $comments = $route->comments()
            ->with([
                'user:id,first_name,last_name,profile_photo_path',
                'children.user:id,first_name,last_name,profile_photo_path',
            ])
            ->latest()
            ->paginate(20);

        return response()->json($comments);
    }

    public function store(Request $request, ItineraryRoute $route): JsonResponse
    {
        Gate::authorize('view', $route);

        $data = $request->validate([
            'stop_id' => ['nullable', 'exists:itinerary_route_stops,id'],
            'parent_id' => ['nullable', 'exists:itinerary_route_comments,id'],
            'body' => ['required', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['string'],
            'visibility' => ['nullable', Rule::in(['crew', 'public'])],
        ]);

        $comment = $route->comments()->create(array_merge($data, [
            'user_id' => $request->user()->id,
            'status' => 'active',
        ]));

        return response()->json([
            'message' => 'Comment posted.',
            'data' => $comment->fresh('user:id,first_name,last_name'),
        ], 201);
    }

    public function update(Request $request, ItineraryRoute $route, ItineraryRouteComment $comment): JsonResponse
    {
        $user = $request->user();
        Gate::authorize('view', $route);
        abort_unless($comment->route_id === $route->id, 404);

        abort_unless($comment->user_id === $user->id || Gate::allows('manageCrew', $route), 403);

        $data = $request->validate([
            'body' => ['sometimes', 'string'],
            'visibility' => ['sometimes', Rule::in(['crew', 'public'])],
            'status' => ['sometimes', Rule::in(['active', 'hidden', 'flagged'])],
        ]);

        $comment->update($data);

        return response()->json([
            'message' => 'Comment updated.',
            'data' => $comment->fresh('user:id,first_name,last_name'),
        ]);
    }

    public function destroy(Request $request, ItineraryRoute $route, ItineraryRouteComment $comment): JsonResponse
    {
        $user = $request->user();
        Gate::authorize('view', $route);
        abort_unless($comment->route_id === $route->id, 404);

        abort_unless($comment->user_id === $user->id || Gate::allows('manageCrew', $route), 403);

        $comment->delete();

        return response()->json([
            'message' => 'Comment removed.',
        ]);
    }
}

