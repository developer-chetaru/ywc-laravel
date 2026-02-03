<?php

namespace App\Livewire\Forum;

use App\Models\ForumNotification;
use App\Services\Forum\ForumNotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationList extends Component
{
    use WithPagination;

    public $limit = null; // For dropdown display
    public $filter = 'all'; // all, unread, read

    protected $listeners = ['refreshNotifications' => '$refresh', 'markAllAsRead' => 'markAllAsRead'];

    public function mount($limit = null)
    {
        $this->limit = $limit;
    }

    public function markAsRead($notificationId)
    {
        if (!Auth::check()) {
            return;
        }

        $notificationService = app(ForumNotificationService::class);
        $notificationService->markAsRead($notificationId, Auth::user());
        
        $this->dispatch('notificationRead');
        $this->dispatch('refreshNotifications');
    }

    public function markAllAsRead()
    {
        if (!Auth::check()) {
            return;
        }

        $notificationService = app(ForumNotificationService::class);
        $notificationService->markAllAsRead(Auth::user());
        
        $this->dispatch('notificationRead');
        $this->dispatch('refreshNotifications');
    }

    public function render()
    {
        if (!Auth::check()) {
            return view('livewire.forum.notification-list', [
                'notifications' => collect(),
            ]);
        }

        $query = ForumNotification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($this->filter === 'unread') {
            $query->where('is_read', false);
        } elseif ($this->filter === 'read') {
            $query->where('is_read', true);
        }

        if ($this->limit) {
            $notifications = $query->limit($this->limit)->get();
        } else {
            $notifications = $query->paginate(20);
        }

        return view('livewire.forum.notification-list', [
            'notifications' => $notifications,
        ]);
    }
}
