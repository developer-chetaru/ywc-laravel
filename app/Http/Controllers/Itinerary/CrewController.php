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
    public function index(ItineraryRoute $route): JsonResponse
    {
        Gate::authorize('view', $route);

        $crew = $route->crew()->with('user:id,first_name,last_name,email')->get();

        return response()->json([
            'data' => $crew,
        ]);
    }

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

    public function destroy(ItineraryRoute $route, ItineraryRouteCrew $crew): JsonResponse
    {
        Gate::authorize('manageCrew', $route);

        abort_unless($crew->route_id === $route->id, 404);

        $crew->delete();

        return response()->json([
            'message' => 'Crew member removed.',
        ]);
    }

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

