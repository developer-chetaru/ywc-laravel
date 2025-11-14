<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\UserConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Send a message
     */
    public function send(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:5000',
            'message_type' => 'nullable|in:text,image,location',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $sender = auth('api')->user();
        $receiver = User::findOrFail($request->receiver_id);

        // Check if users are connected (for non-blocked messages)
        if ($request->message_type !== 'system') {
            $connection = UserConnection::where(function ($q) use ($sender, $receiver) {
                $q->where('user_id', $sender->id)
                    ->where('connected_user_id', $receiver->id);
            })->orWhere(function ($q) use ($sender, $receiver) {
                $q->where('user_id', $receiver->id)
                    ->where('connected_user_id', $sender->id);
            })->first();

            if (!$connection || $connection->status !== 'accepted') {
                return response()->json([
                    'status' => false,
                    'message' => 'You must be connected to send messages',
                ], 403);
            }

            // Check if blocked
            if ($connection->status === 'blocked') {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot send message to blocked user',
                ], 403);
            }
        }

        $message = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => $request->message,
            'message_type' => $request->message_type ?? 'text',
            'metadata' => $request->metadata,
        ]);

        // Update connection last interaction
        if ($connection) {
            $connection->update(['last_interaction_at' => now()]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Message sent',
            'data' => $message->load('sender:id,first_name,last_name,email,profile_photo_path'),
        ], 201);
    }

    /**
     * Get conversation with a user
     */
    public function getConversation(Request $request, User $user): JsonResponse
    {
        $currentUser = auth('api')->user();

        $messages = Message::where(function ($q) use ($currentUser, $user) {
            $q->where('sender_id', $currentUser->id)
                ->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($currentUser, $user) {
            $q->where('sender_id', $user->id)
                ->where('receiver_id', $currentUser->id);
        })
        ->with(['sender:id,first_name,last_name,email,profile_photo_path', 'receiver:id,first_name,last_name,email,profile_photo_path'])
        ->orderBy('created_at', 'asc')
        ->get();

        // Mark messages as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $currentUser->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'status' => true,
            'message' => 'Conversation retrieved',
            'data' => $messages,
        ], 200);
    }

    /**
     * Get all conversations
     */
    public function getConversations(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        // Get distinct conversations
        $conversations = Message::select('sender_id', 'receiver_id', DB::raw('MAX(created_at) as last_message_at'))
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->groupBy(DB::raw('IF(sender_id = ' . $user->id . ', receiver_id, sender_id)'))
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($conv) use ($user) {
                $otherUserId = $conv->sender_id === $user->id ? $conv->receiver_id : $conv->sender_id;
                $otherUser = User::find($otherUserId);
                
                $lastMessage = Message::where(function ($q) use ($user, $otherUserId) {
                    $q->where('sender_id', $user->id)
                        ->where('receiver_id', $otherUserId);
                })->orWhere(function ($q) use ($user, $otherUserId) {
                    $q->where('sender_id', $otherUserId)
                        ->where('receiver_id', $user->id);
                })->latest()->first();

                $unreadCount = Message::where('sender_id', $otherUserId)
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->count();

                return [
                    'user' => $otherUser ? [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'email' => $otherUser->email,
                        'profile_photo_url' => $otherUser->profile_photo_url,
                    ] : null,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                    'last_message_at' => $conv->last_message_at,
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Conversations retrieved',
            'data' => $conversations,
        ], 200);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request, User $user): JsonResponse
    {
        $currentUser = auth('api')->user();

        $updated = Message::where('sender_id', $user->id)
            ->where('receiver_id', $currentUser->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'status' => true,
            'message' => 'Messages marked as read',
            'data' => ['updated_count' => $updated],
        ], 200);
    }

    /**
     * Get unread message count
     */
    public function getUnreadCount(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        $count = Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'status' => true,
            'message' => 'Unread count retrieved',
            'data' => ['unread_count' => $count],
        ], 200);
    }
}
