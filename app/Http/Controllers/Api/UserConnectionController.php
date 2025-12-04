<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserConnection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class UserConnectionController extends Controller
{
    /**
     * Send connection request
     */
    public function sendRequest(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $sender = $request->user();
        
        if (!$sender) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
        $receiver = User::findOrFail($request->user_id);

        // Check if already connected
        $existing = UserConnection::where(function ($q) use ($sender, $receiver) {
            $q->where('user_id', $sender->id)
                ->where('connected_user_id', $receiver->id);
        })->orWhere(function ($q) use ($sender, $receiver) {
            $q->where('user_id', $receiver->id)
                ->where('connected_user_id', $sender->id);
        })->first();

        if ($existing) {
            return response()->json([
                'status' => false,
                'message' => 'Connection request already exists',
                'data' => ['status' => $existing->status],
            ], 409);
        }

        $connection = UserConnection::create([
            'user_id' => $sender->id,
            'connected_user_id' => $receiver->id,
            'status' => 'pending',
            'request_message' => $request->message,
            'category' => $request->category,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Connection request sent',
            'data' => $connection->load('connectedUser:id,first_name,last_name,email,profile_photo_path'),
        ], 200);
    }

    /**
     * Accept connection request
     */
    public function acceptRequest(Request $request, UserConnection $connection): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        // Verify the connection request is for this user
        if ($connection->connected_user_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($connection->status !== 'pending') {
            return response()->json([
                'status' => false,
                'message' => 'Connection request is not pending',
            ], 400);
        }

        $connection->update([
            'status' => 'accepted',
            'connected_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Connection request accepted',
            'data' => $connection->load('user:id,first_name,last_name,email,profile_photo_path'),
        ], 200);
    }

    /**
     * Decline connection request
     */
    public function declineRequest(Request $request, UserConnection $connection): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        if ($connection->connected_user_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $connection->update(['status' => 'declined']);

        return response()->json([
            'status' => true,
            'message' => 'Connection request declined',
        ], 200);
    }

    /**
     * Get connection requests (pending)
     */
    public function getRequests(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $requests = UserConnection::where('connected_user_id', $user->id)
            ->where('status', 'pending')
            ->with('user:id,first_name,last_name,email,profile_photo_path,years_experience')
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Connection requests retrieved',
            'data' => $requests,
        ], 200);
    }

    /**
     * Get all connections
     */
    public function getConnections(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $connections = UserConnection::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhere('connected_user_id', $user->id);
        })
        ->where('status', 'accepted')
        ->with(['user:id,first_name,last_name,email,profile_photo_path', 'connectedUser:id,first_name,last_name,email,profile_photo_path'])
        ->latest('connected_at')
        ->get()
        ->map(function ($connection) use ($user) {
            $connectedUser = $connection->user_id === $user->id 
                ? $connection->connectedUser 
                : $connection->user;
            
            return [
                'id' => $connection->id,
                'user' => $connectedUser,
                'category' => $connection->category,
                'connection_strength' => $connection->connection_strength,
                'connected_at' => $connection->connected_at,
                'last_interaction_at' => $connection->last_interaction_at,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Connections retrieved',
            'data' => $connections,
        ], 200);
    }

    /**
     * Remove connection
     */
    public function removeConnection(Request $request, UserConnection $connection): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        if ($connection->user_id !== $user->id && $connection->connected_user_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $connection->delete();

        return response()->json([
            'status' => true,
            'message' => 'Connection removed',
        ], 200);
    }

    /**
     * Block user
     */
    public function blockUser(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
        $blockedUser = User::findOrFail($request->user_id);

        // Delete existing connection if any
        UserConnection::where(function ($q) use ($user, $blockedUser) {
            $q->where('user_id', $user->id)
                ->where('connected_user_id', $blockedUser->id);
        })->orWhere(function ($q) use ($user, $blockedUser) {
            $q->where('user_id', $blockedUser->id)
                ->where('connected_user_id', $user->id);
        })->delete();

        // Create blocked connection
        UserConnection::create([
            'user_id' => $user->id,
            'connected_user_id' => $blockedUser->id,
            'status' => 'blocked',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User blocked',
        ], 200);
    }
}
