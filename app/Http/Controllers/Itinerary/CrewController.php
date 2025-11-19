<?php

namespace App\Http\Controllers\Itinerary;

use App\Http\Controllers\Controller;
use App\Models\ItineraryRoute;
use App\Models\ItineraryRouteCrew;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CrewController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/itinerary/routes/{route}/crew",
     *     summary="Get crew members for a route",
     *     tags={"Itinerary Crew"},
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
     *         description="List of crew members",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied")
     * )
     */
    public function index(ItineraryRoute $route): JsonResponse
    {
        Gate::authorize('view', $route);

        $crew = $route->crew()->with('user:id,first_name,last_name,email')->get();

        return response()->json([
            'data' => $crew,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/itinerary/routes/{route}/crew",
     *     summary="Invite crew member to route",
     *     tags={"Itinerary Crew"},
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
     *             required={"role"},
     *             @OA\Property(property="email", type="string", format="email", example="crew@example.com", description="Email (required if user_id not provided)"),
     *             @OA\Property(property="user_id", type="integer", example=2, description="User ID (required if email not provided)"),
     *             @OA\Property(property="role", type="string", enum={"owner", "editor", "viewer"}, example="editor")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Crew invitation sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Crew invitation sent."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request, ItineraryRoute $route): JsonResponse
    {
        Gate::authorize('manageCrew', $route);

        $data = $request->validate([
            'email' => ['required_without:user_id', 'nullable', 'email'],
            'user_id' => ['required_without:email', 'nullable', 'exists:users,id'],
            'role' => ['required', Rule::in(['owner', 'editor', 'viewer'])],
        ]);

        $assignment = $route->crew()->updateOrCreate(
            [
                'user_id' => $data['user_id'] ?? null,
                'email' => $data['email'] ?? null,
            ],
            [
                'role' => $data['role'],
                'status' => 'pending',
                'invited_at' => now(),
            ]
        );

        // TODO: dispatch notification/email invitation.

        return response()->json([
            'message' => 'Crew invitation sent.',
            'data' => $assignment->load('user:id,first_name,last_name,email'),
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/itinerary/routes/{route}/crew/{crew}",
     *     summary="Update crew member",
     *     tags={"Itinerary Crew"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Route ID"
     *     ),
     *     @OA\Parameter(
     *         name="crew",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Crew member ID"
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="role", type="string", enum={"owner", "editor", "viewer"}),
     *             @OA\Property(property="status", type="string", enum={"pending", "accepted", "declined", "revoked"}),
     *             @OA\Property(property="notify_on_updates", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Crew member updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Crew member updated."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied"),
     *     @OA\Response(response=404, description="Crew member not found")
     * )
     */
    public function update(Request $request, ItineraryRoute $route, ItineraryRouteCrew $crew): JsonResponse
    {
        Gate::authorize('manageCrew', $route);

        abort_unless($crew->route_id === $route->id, 404);

        $data = $request->validate([
            'role' => ['sometimes', Rule::in(['owner', 'editor', 'viewer'])],
            'status' => ['sometimes', Rule::in(['pending', 'accepted', 'declined', 'revoked'])],
            'notify_on_updates' => ['sometimes', 'boolean'],
        ]);

        $crew->fill($data);
        if (array_key_exists('status', $data)) {
            $crew->responded_at = now();
        }
        $crew->save();

        return response()->json([
            'message' => 'Crew member updated.',
            'data' => $crew->fresh('user:id,first_name,last_name,email'),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/itinerary/routes/{route}/crew/{crew}",
     *     summary="Remove crew member",
     *     tags={"Itinerary Crew"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Route ID"
     *     ),
     *     @OA\Parameter(
     *         name="crew",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Crew member ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Crew member removed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Crew member removed.")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied"),
     *     @OA\Response(response=404, description="Crew member not found")
     * )
     */
    public function destroy(ItineraryRoute $route, ItineraryRouteCrew $crew): JsonResponse
    {
        Gate::authorize('manageCrew', $route);

        abort_unless($crew->route_id === $route->id, 404);

        $crew->delete();

        return response()->json([
            'message' => 'Crew member removed.',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/itinerary/routes/{route}/crew/{crew}/respond",
     *     summary="Respond to crew invitation",
     *     tags={"Itinerary Crew"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Route ID"
     *     ),
     *     @OA\Parameter(
     *         name="crew",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Crew member ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"action"},
     *             @OA\Property(property="action", type="string", enum={"accept", "decline"}, example="accept")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invitation response processed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You have joined the crew."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied"),
     *     @OA\Response(response=404, description="Crew invitation not found")
     * )
     */
    public function respond(Request $request, ItineraryRoute $route, ItineraryRouteCrew $crew): JsonResponse
    {
        $user = $request->user();
        abort_unless($user && $crew->route_id === $route->id, 404);
        abort_unless($crew->user_id === $user->id || $crew->email === $user->email, 403);

        $action = $request->validate([
            'action' => ['required', Rule::in(['accept', 'decline'])],
        ]);

        if ($action['action'] === 'accept') {
            $crew->markAccepted();
        } else {
            $crew->markDeclined();
        }

        return response()->json([
            'message' => $action['action'] === 'accept'
                ? 'You have joined the crew.'
                : 'You have declined the invitation.',
            'data' => $crew->fresh('user:id,first_name,last_name,email'),
        ]);
    }
}

