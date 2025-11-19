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
    /**
     * @OA\Get(
     *     path="/api/itinerary/routes/{route}/comments",
     *     summary="Get comments for a route",
     *     tags={"Itinerary Comments"},
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
     *         description="Paginated list of comments",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=403, description="Access denied")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/itinerary/routes/{route}/comments",
     *     summary="Create a comment on a route",
     *     tags={"Itinerary Comments"},
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
     *             required={"body"},
     *             @OA\Property(property="stop_id", type="integer", example=1, description="Optional: Stop ID to comment on"),
     *             @OA\Property(property="parent_id", type="integer", example=5, description="Optional: Parent comment ID for replies"),
     *             @OA\Property(property="body", type="string", example="Great route! Looking forward to trying it."),
     *             @OA\Property(property="attachments", type="array", @OA\Items(type="string"), example={"url1", "url2"}),
     *             @OA\Property(property="visibility", type="string", enum={"crew", "public"}, example="public")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment posted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comment posted."),
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
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/itinerary/routes/{route}/comments/{comment}",
     *     summary="Update a comment",
     *     tags={"Itinerary Comments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Route ID"
     *     ),
     *     @OA\Parameter(
     *         name="comment",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Comment ID"
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="body", type="string", example="Updated comment text"),
     *             @OA\Property(property="visibility", type="string", enum={"crew", "public"}),
     *             @OA\Property(property="status", type="string", enum={"active", "hidden", "flagged"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comment updated."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied"),
     *     @OA\Response(response=404, description="Comment not found")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/itinerary/routes/{route}/comments/{comment}",
     *     summary="Delete a comment",
     *     tags={"Itinerary Comments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Route ID"
     *     ),
     *     @OA\Parameter(
     *         name="comment",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Comment ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment removed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comment removed.")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied"),
     *     @OA\Response(response=404, description="Comment not found")
     * )
     */
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

