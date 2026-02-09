<?php

namespace App\Livewire\Forum\Pages\Category;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use TeamTeaTime\Forum\Events\UserViewingIndex;
use TeamTeaTime\Forum\Support\Access\CategoryAccess;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;
use App\Models\User;
use App\Models\Message;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class CategoryIndex extends Component
{
    use WithFileUploads;
    
    public $categories = [];
    public $selectedThread = null;
    public $search = '';
    public $filteredCategories = [];
    public $sortBy = 'recent'; // recent, popular, oldest, most_threads
    public $filterBy = 'all'; // all, active, pinned
    
    // Direct Chat properties
    public $showDirectChat = false;
    public $showMessageModal = false;
    public $messageUserId = null;
    public $messageUserName = '';
    public $messageText = '';
    public $messages = [];
    public $conversationUserId = null;
    public $chatSearch = '';
    public $messageImage = null;

    public function mount(Request $request)
    {
        $categories = CategoryAccess::getFilteredTreeFor($request->user())->toTree();

        // TODO: This is a workaround for a serialisation issue. See: https://github.com/lazychaser/laravel-nestedset/issues/487
        //       Once the issue is fixed, this can be removed.
        $this->categories = CategoryAccess::removeParentRelationships($categories);

        // Sort categories by created_at descending (newest first)
        $this->categories = collect($this->categories)->sortByDesc('created_at')->values()->all();

        // Load threads for each category - order by updated_at to show most recently active threads first
        foreach ($this->categories as $category) {
            // Load threads with proper ordering (newest first)
            $threads = $category->threads()
                ->orderBy('updated_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Set the sorted collection back to the category
            $category->setRelation('threads', $threads);
        }

        // Apply initial filtering
        $this->applyFilters();

        if ($request->user() !== null) {
            UserViewingIndex::dispatch($request->user());
        }
    }

    public function updatedSearch()
    {
        $this->applyFilters();
    }

    public function updatedSortBy()
    {
        $this->applyFilters();
    }

    public function updatedFilterBy()
    {
        $this->applyFilters();
    }

    public function applyFilters()
    {
        $allCategories = collect($this->categories);
        
        // Filter by search term
        if (!empty(trim($this->search))) {
            $searchTerm = strtolower(trim($this->search));
            $allCategories = $allCategories->filter(function($category) use ($searchTerm) {
                return str_contains(strtolower($category->title), $searchTerm) ||
                       str_contains(strtolower($category->description ?? ''), $searchTerm) ||
                       $category->threads->contains(function($thread) use ($searchTerm) {
                           return str_contains(strtolower($thread->title), $searchTerm);
                       });
            });
        }
        
        // Filter by status
        if ($this->filterBy === 'active') {
            $allCategories = $allCategories->filter(function($category) {
                return $category->threads->count() > 0 && 
                       $category->threads->max('updated_at') > now()->subDays(7);
            });
        } elseif ($this->filterBy === 'pinned') {
            $allCategories = $allCategories->filter(function($category) {
                return $category->threads->contains('pinned', true);
            });
        }
        
        // Sort categories
        $allCategories = $allCategories->sortBy(function($category) {
            switch ($this->sortBy) {
                case 'popular':
                    return -$category->threads->count(); // Most threads first
                case 'oldest':
                    return $category->created_at->timestamp;
                case 'most_threads':
                    return -$category->threads->count();
                case 'recent':
                default:
                    $latestThread = $category->threads->max('updated_at');
                    return $latestThread ? -$latestThread->timestamp : -$category->created_at->timestamp;
            }
        })->values();
        
        $this->filteredCategories = $allCategories->all();
    }

    public function loadThread($threadId)
    {
        try {
            $thread = Thread::with([
                'category', 
                'posts' => function($query) {
                    $query->orderBy('created_at', 'asc');
                },
                'posts.user:id,first_name,last_name,email,profile_photo_path'
            ])->findOrFail($threadId);
            
            $this->selectedThread = $thread;
            
            \Log::info('CategoryIndex: Thread loaded', [
                'thread_id' => $threadId,
                'thread_title' => $thread->title,
            ]);
        } catch (\Exception $e) {
            \Log::error('CategoryIndex: Failed to load thread', [
                'thread_id' => $threadId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Thread not found: ' . $e->getMessage());
            $this->selectedThread = null;
        }
    }

    public function openThread($threadId)
    {
        $this->loadThread($threadId);
    }

    public function requestAccess($threadId)
    {
        // This method can be used for requesting access to private threads
        // For now, we'll just redirect to the thread
        return $this->openThread($threadId);
    }

    // Direct Chat Methods
    public function toggleDirectChat()
    {
        $this->showDirectChat = !$this->showDirectChat;
        $this->selectedThread = null;
    }

    public function openMessageModal($userId)
    {
        $user = User::findOrFail($userId);
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
        $this->messageImage = null;
        $this->messages = [];
        $this->conversationUserId = null;
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
                'receiver_id' => $message->receiver_id,
                'message' => $message->message,
                'message_type' => $message->message_type,
                'attachment_path' => $message->attachment_path,
                'attachment_url' => $message->attachment_path ? asset('storage/' . $message->attachment_path) : null,
                'is_sent' => $message->sender_id === auth()->id(),
                'is_read' => $message->is_read,
                'sender_name' => $message->sender->name,
                'sender_photo' => $message->sender->profile_photo_url,
                'created_at' => $message->created_at,
                'time_display' => $timeDisplay,
            ];
        })->toArray();
    }

    public function sendMessage()
    {
        $this->validate([
            'messageUserId' => 'required|exists:users,id',
            'messageText' => 'nullable|string|max:5000',
            'messageImage' => 'nullable|image|max:5120', // 5MB max
        ]);

        // At least one of message or image must be provided
        if (empty(trim($this->messageText)) && !$this->messageImage) {
            session()->flash('error', 'Please enter a message or select an image');
            return;
        }

        $attachmentPath = null;
        $messageType = 'text';

        // Handle image upload
        if ($this->messageImage) {
            $attachmentPath = $this->messageImage->store('messages', 'public');
            $messageType = 'image';
        }

        // Allow direct chat with any user (no connection required)
        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $this->messageUserId,
            'message' => $this->messageText ?? '',
            'attachment_path' => $attachmentPath,
            'message_type' => $messageType,
        ]);

        $this->messageText = '';
        $this->messageImage = null;
        $this->loadMessages($this->messageUserId);
        session()->flash('success', 'Message sent successfully');
        $this->dispatch('scroll-to-bottom');
    }

    public function refreshMessages()
    {
        if ($this->showMessageModal && $this->conversationUserId) {
            $this->loadMessages($this->conversationUserId);
        }
    }

    public function getUnreadCount($userId)
    {
        return Message::where('sender_id', $userId)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->count();
    }

    public function render(): View
    {
        // Calculate total forums (categories)
        $totalForums = Category::count();
        
        // Calculate total threads
        $totalThreads = Thread::count();

        // Use filtered categories (includes search, sort, and filter)
        $displayCategories = $this->filteredCategories;

        // Get all users for Direct Chat (all users in the system)
        $usersQuery = User::where('id', '!=', auth()->id())
            ->select('id', 'first_name', 'last_name', 'email', 'profile_photo_path', 'years_experience');
        
        // Apply search filter if provided
        if (!empty(trim($this->chatSearch))) {
            $searchTerm = trim($this->chatSearch);
            $usersQuery->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                    ->orWhere('last_name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"]);
            });
        }
        
        $allUsers = $usersQuery->get()
            ->map(function ($user) {
                $unreadCount = Message::where('sender_id', $user->id)
                    ->where('receiver_id', auth()->id())
                    ->where('is_read', false)
                    ->count();
                
                $lastMessage = Message::where(function ($q) use ($user) {
                    $q->where('sender_id', auth()->id())
                        ->where('receiver_id', $user->id);
                })->orWhere(function ($q) use ($user) {
                    $q->where('sender_id', $user->id)
                        ->where('receiver_id', auth()->id());
                })
                ->latest()
                ->first();
                
                return [
                    'user' => $user,
                    'unread_count' => $unreadCount,
                    'last_message' => $lastMessage,
                ];
            })
            ->sortByDesc(function ($item) {
                return $item['last_message'] ? $item['last_message']->created_at : now()->subYears(100);
            })
            ->values();

        return ViewFactory::make('forum::pages.category.index', [
            'totalForums' => $totalForums,
            'totalThreads' => $totalThreads,
            'selectedThread' => $this->selectedThread,
            'displayCategories' => $displayCategories,
            'allUsers' => $allUsers,
        ])->layout('forum::layouts.main');
    }
}
