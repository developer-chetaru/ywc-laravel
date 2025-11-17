<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\UserConnection;
use App\Models\Message;
use App\Models\Group;
use App\Models\GroupMember;
use Livewire\Component;
use Livewire\WithPagination;

class UserConnections extends Component
{
    use WithPagination;

    public $tab = 'connections'; // 'connections', 'requests', 'sent'
    public $selectedConnection = null;
    public $showConnectionModal = false;
    public $requestMessage = '';
    public $requestUserId = null;
    public $showRequestModal = false;

    // Messaging
    public $showMessageModal = false;
    public $messageUserId = null;
    public $messageUserName = '';
    public $messageText = '';
    public $messages = [];
    public $conversationUserId = null;

    // Groups
    public $showGroupModal = false;
    public $showCreateGroupModal = false;
    public $groupName = '';
    public $groupDescription = '';
    public $selectedGroupMembers = [];
    public $groups = [];
    public $selectedGroupId = null;
    public $groupMessages = [];

    public $alert = '';
    public $error = '';

    public function mount()
    {
        // Load initial data
    }

    public function openRequestModal($userId)
    {
        $this->requestUserId = $userId;
        $this->showRequestModal = true;
        $this->requestMessage = '';
    }

    public function closeRequestModal()
    {
        $this->showRequestModal = false;
        $this->requestUserId = null;
        $this->requestMessage = '';
    }

    public function sendConnectionRequest()
    {
        $this->validate([
            'requestUserId' => 'required|exists:users,id',
            'requestMessage' => 'nullable|string|max:500',
        ]);

        $existing = UserConnection::where(function ($q) {
            $q->where('user_id', auth()->id())
                ->where('connected_user_id', $this->requestUserId);
        })->orWhere(function ($q) {
            $q->where('user_id', $this->requestUserId)
                ->where('connected_user_id', auth()->id());
        })->first();

        if ($existing) {
            $this->error = 'Connection request already exists';
            return;
        }

        UserConnection::create([
            'user_id' => auth()->id(),
            'connected_user_id' => $this->requestUserId,
            'status' => 'pending',
            'request_message' => $this->requestMessage,
        ]);

        $this->alert = 'Connection request sent successfully';
        $this->closeRequestModal();
        $this->resetPage();
    }

    public function acceptRequest($connectionId)
    {
        $connection = UserConnection::findOrFail($connectionId);
        
        if ($connection->connected_user_id !== auth()->id()) {
            $this->error = 'Unauthorized';
            return;
        }

        $connection->update([
            'status' => 'accepted',
            'connected_at' => now(),
        ]);

        $this->alert = 'Connection request accepted';
        $this->resetPage();
    }

    public function declineRequest($connectionId)
    {
        $connection = UserConnection::findOrFail($connectionId);
        
        if ($connection->connected_user_id !== auth()->id()) {
            $this->error = 'Unauthorized';
            return;
        }

        $connection->update(['status' => 'declined']);
        $this->alert = 'Connection request declined';
        $this->resetPage();
    }

    public function removeConnection($connectionId)
    {
        $connection = UserConnection::findOrFail($connectionId);
        
        if ($connection->user_id !== auth()->id() && $connection->connected_user_id !== auth()->id()) {
            $this->error = 'Unauthorized';
            return;
        }

        $connection->delete();
        $this->alert = 'Connection removed';
        $this->resetPage();
    }

    public function openMessageModal($userId)
    {
        $user = User::findOrFail($userId);
        
        // Check if connected
        $connection = UserConnection::where(function ($q) use ($userId) {
            $q->where('user_id', auth()->id())
                ->where('connected_user_id', $userId);
        })->orWhere(function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->where('connected_user_id', auth()->id());
        })->where('status', 'accepted')->first();

        if (!$connection) {
            $this->error = 'You must be connected to send messages';
            return;
        }

        $this->messageUserId = $userId;
        $this->messageUserName = $user->name;
        $this->messageText = '';
        $this->showMessageModal = true;
        $this->loadMessages($userId);
    }

    public function closeMessageModal()
    {
        $this->showMessageModal = false;
        $this->messageUserId = null;
        $this->messageUserName = '';
        $this->messageText = '';
        $this->messages = [];
    }

    public function loadMessages($userId)
    {
        $this->conversationUserId = $userId;
        
        // Mark all messages from this user as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        
        $this->messages = Message::where(function ($q) use ($userId) {
            $q->where('sender_id', auth()->id())
                ->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($userId) {
            $q->where('sender_id', $userId)
                ->where('receiver_id', auth()->id());
        })
        ->with(['sender:id,first_name,last_name,email,profile_photo_path', 'receiver:id,first_name,last_name,email,profile_photo_path'])
        ->orderBy('created_at', 'asc')
        ->get()
        ->map(function ($message) {
            // Ensure we have a Carbon instance with proper timezone
            $createdAt = $message->created_at instanceof \Carbon\Carbon 
                ? $message->created_at->copy() 
                : \Carbon\Carbon::parse($message->created_at);
            
            // Use current time in same timezone
            $now = now();
            
            // Calculate absolute time differences (always positive) and round them
            $diffInSeconds = (int) abs($now->diffInSeconds($createdAt));
            $diffInMinutes = (int) abs($now->diffInMinutes($createdAt));
            $diffInHours = (int) abs($now->diffInHours($createdAt));
            $diffInDays = (int) abs($now->diffInDays($createdAt));
            
            // Format time display with relative time for recent messages
            $timeDisplay = '';
            
            // Less than 30 seconds ago - show "Just now"
            if ($diffInSeconds < 30) {
                $timeDisplay = 'Just now';
            }
            // Less than 1 minute ago - show "Just now"
            elseif ($diffInSeconds < 60) {
                $timeDisplay = 'Just now';
            }
            // Less than 1 hour ago - show minutes (rounded)
            elseif ($diffInMinutes < 60) {
                $timeDisplay = $diffInMinutes . ' minute' . ($diffInMinutes > 1 ? 's' : '') . ' ago';
            }
            // Less than 2 hours ago - show hours (rounded)
            elseif ($diffInHours < 2) {
                $timeDisplay = $diffInHours . ' hour ago';
            }
            // Today - show time (always show actual time for messages older than 2 hours today)
            elseif ($createdAt->isToday()) {
                $timeDisplay = $createdAt->format('g:i A');
            }
            // Yesterday - show "Yesterday at time"
            elseif ($createdAt->isYesterday()) {
                $timeDisplay = 'Yesterday at ' . $createdAt->format('g:i A');
            }
            // This week - show day and time
            elseif ($createdAt->isCurrentWeek()) {
                $timeDisplay = $createdAt->format('l \a\t g:i A');
            }
            // This year - show date and time
            elseif ($createdAt->isCurrentYear()) {
                $timeDisplay = $createdAt->format('M j, g:i A');
            }
            // Older - show full date and time
            else {
                $timeDisplay = $createdAt->format('M j, Y g:i A');
            }
            
            return [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id,
                'message' => $message->message,
                'message_type' => $message->message_type,
                'is_sent' => $message->sender_id === auth()->id(),
                'is_read' => $message->is_read,
                'sender_name' => $message->sender->name,
                'sender_photo' => $message->sender->profile_photo_url,
                'created_at' => $message->created_at,
                'time_display' => $timeDisplay,
                'relative_time' => $createdAt->diffForHumans(),
            ];
        })->toArray();
    }
    
    public function getUnreadCount($userId)
    {
        return Message::where('sender_id', $userId)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->count();
    }

    public function sendMessage()
    {
        $this->validate([
            'messageUserId' => 'required|exists:users,id',
            'messageText' => 'required|string|max:5000',
        ]);

        // Check connection
        $connection = UserConnection::where(function ($q) {
            $q->where('user_id', auth()->id())
                ->where('connected_user_id', $this->messageUserId);
        })->orWhere(function ($q) {
            $q->where('user_id', $this->messageUserId)
                ->where('connected_user_id', auth()->id());
        })->where('status', 'accepted')->first();

        if (!$connection) {
            $this->error = 'You must be connected to send messages';
            return;
        }

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $this->messageUserId,
            'message' => $this->messageText,
            'message_type' => 'text',
        ]);

        // Update connection last interaction
        $connection->update(['last_interaction_at' => now()]);

        $this->messageText = '';
        $this->loadMessages($this->messageUserId);
        $this->alert = 'Message sent successfully';
        
        // Scroll to bottom after a short delay
        $this->dispatch('scroll-to-bottom');
    }
    
    /**
     * Refresh messages - called by Livewire polling when modal is open
     * This enables real-time chat without queues, Pusher, or background processes
     */
    public function refreshMessages()
    {
        if ($this->showMessageModal && $this->conversationUserId) {
            $this->loadMessages($this->conversationUserId);
        }
        if ($this->showGroupModal && $this->selectedGroupId) {
            $this->loadGroupMessages($this->selectedGroupId);
        }
    }

    // Group Methods
    public function openCreateGroupModal()
    {
        $this->showCreateGroupModal = true;
        $this->groupName = '';
        $this->groupDescription = '';
        $this->selectedGroupMembers = [];
    }

    public function closeCreateGroupModal()
    {
        $this->showCreateGroupModal = false;
        $this->groupName = '';
        $this->groupDescription = '';
        $this->selectedGroupMembers = [];
    }

    public function createGroup()
    {
        $this->validate([
            'groupName' => 'required|string|max:255',
            'groupDescription' => 'nullable|string|max:1000',
            'selectedGroupMembers' => 'required|array|min:1',
        ]);

        try {
            $group = Group::create([
                'name' => $this->groupName,
                'description' => $this->groupDescription,
                'created_by' => auth()->id(),
            ]);

            // Add creator as admin
            GroupMember::create([
                'group_id' => $group->id,
                'user_id' => auth()->id(),
                'role' => 'admin',
            ]);

            // Add selected members
            foreach ($this->selectedGroupMembers as $userId) {
                GroupMember::create([
                    'group_id' => $group->id,
                    'user_id' => $userId,
                    'role' => 'member',
                ]);
            }

            $this->alert = 'Group created successfully!';
            $this->closeCreateGroupModal();
        } catch (\Exception $e) {
            $this->error = 'Failed to create group: ' . $e->getMessage();
        }
    }

    public function openGroupModal($groupId)
    {
        $this->selectedGroupId = $groupId;
        $this->showGroupModal = true;
        $this->loadGroupMessages($groupId);
    }

    public function closeGroupModal()
    {
        $this->showGroupModal = false;
        $this->selectedGroupId = null;
        $this->groupMessages = [];
        $this->messageText = '';
    }

    public function loadGroupMessages($groupId)
    {
        $this->groupMessages = Message::where('group_id', $groupId)
            ->with(['sender:id,first_name,last_name,email,profile_photo_path'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                $createdAt = $message->created_at instanceof \Carbon\Carbon 
                    ? $message->created_at->copy() 
                    : \Carbon\Carbon::parse($message->created_at);
                
                $now = now();
                $diffInSeconds = (int) abs($now->diffInSeconds($createdAt));
                $diffInMinutes = (int) abs($now->diffInMinutes($createdAt));
                $diffInHours = (int) abs($now->diffInHours($createdAt));
                
                $timeDisplay = '';
                if ($diffInSeconds < 30) {
                    $timeDisplay = 'Just now';
                } elseif ($diffInSeconds < 60) {
                    $timeDisplay = 'Just now';
                } elseif ($diffInMinutes < 60) {
                    $timeDisplay = $diffInMinutes . ' minute' . ($diffInMinutes > 1 ? 's' : '') . ' ago';
                } elseif ($diffInHours < 2) {
                    $timeDisplay = $diffInHours . ' hour ago';
                } elseif ($createdAt->isToday()) {
                    $timeDisplay = $createdAt->format('g:i A');
                } elseif ($createdAt->isYesterday()) {
                    $timeDisplay = 'Yesterday at ' . $createdAt->format('g:i A');
                } elseif ($createdAt->isCurrentWeek()) {
                    $timeDisplay = $createdAt->format('l \a\t g:i A');
                } elseif ($createdAt->isCurrentYear()) {
                    $timeDisplay = $createdAt->format('M j, g:i A');
                } else {
                    $timeDisplay = $createdAt->format('M j, Y g:i A');
                }
                
                return [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender->name,
                    'sender_photo' => $message->sender->profile_photo_url,
                    'message' => $message->message,
                    'is_sent' => $message->sender_id === auth()->id(),
                    'time_display' => $timeDisplay,
                    'created_at' => $message->created_at,
                ];
            });
    }

    public function sendGroupMessage()
    {
        if (!$this->selectedGroupId || !$this->messageText) {
            return;
        }

        $this->validate([
            'selectedGroupId' => 'required|exists:groups,id',
            'messageText' => 'required|string|max:5000',
        ]);

        // Check if user is member of group
        $isMember = GroupMember::where('group_id', $this->selectedGroupId)
            ->where('user_id', auth()->id())
            ->exists();

        if (!$isMember) {
            $this->error = 'You are not a member of this group';
            return;
        }

        Message::create([
            'sender_id' => auth()->id(),
            'group_id' => $this->selectedGroupId,
            'message' => trim($this->messageText),
            'message_type' => 'text',
        ]);

        $this->messageText = '';
        $this->loadGroupMessages($this->selectedGroupId);
        $this->alert = 'Message sent successfully';
        $this->dispatch('scroll-to-bottom');
    }

    public function render()
    {
        // Always paginate all three to get accurate counts in tabs
        $requests = UserConnection::where('connected_user_id', auth()->id())
            ->where('status', 'pending')
            ->with('user:id,first_name,last_name,email,profile_photo_path,years_experience')
            ->latest()
            ->paginate(10, ['*'], 'requests_page');

        // Get connections with pagination
        $connectionsQuery = UserConnection::where(function ($q) {
            $q->where('user_id', auth()->id())
                ->orWhere('connected_user_id', auth()->id());
        })
        ->where('status', 'accepted')
        ->with(['user:id,first_name,last_name,email,profile_photo_path', 'connectedUser:id,first_name,last_name,email,profile_photo_path'])
        ->latest('connected_at');
        
        // Get paginated connections
        $connectionsPaginated = $connectionsQuery->paginate(10, ['*'], 'connections_page');
        
        // Add unread counts to each connection
        $connections = $connectionsPaginated->getCollection()->map(function ($connection) {
            $connectedUser = $connection->user_id === auth()->id() 
                ? $connection->connectedUser 
                : $connection->user;
            
            // Get unread message count
            $unreadCount = Message::where('sender_id', $connectedUser->id)
                ->where('receiver_id', auth()->id())
                ->where('is_read', false)
                ->count();
            
            return [
                'connection' => $connection,
                'user' => $connectedUser,
                'unread_count' => $unreadCount,
            ];
        });
        
        // Set the modified collection back to paginator
        $connectionsPaginated->setCollection($connections);
        $connections = $connectionsPaginated;

        $sentRequests = UserConnection::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->with('connectedUser:id,first_name,last_name,email,profile_photo_path')
            ->latest()
            ->paginate(10, ['*'], 'sent_page');

        // Load user's groups
        $this->groups = Group::whereHas('members', function ($q) {
            $q->where('user_id', auth()->id());
        })
        ->with(['creator:id,first_name,last_name', 'members:id,first_name,last_name'])
        ->latest()
        ->get();

        return view('livewire.user-connections', [
            'requests' => $requests,
            'connections' => $connections,
            'sentRequests' => $sentRequests,
        ])->layout('layouts.app');
    }
}
