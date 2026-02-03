<?php

namespace App\Livewire\Forum;

use App\Services\Forum\ForumNotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class NotificationBell extends Component
{
    public $unreadCount = 0;
    public $showDropdown = false;

    protected $listeners = ['notificationReceived' => 'refreshCount'];

    public function mount()
    {
        $this->refreshCount();
    }

    #[On('notificationRead')]
    public function refreshCount()
    {
        if (Auth::check()) {
            $notificationService = app(ForumNotificationService::class);
            $this->unreadCount = $notificationService->getUnreadCount(Auth::user());
        }
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
        if ($this->showDropdown) {
            $this->refreshCount();
        }
    }

    public function render()
    {
        return view('livewire.forum.notification-bell');
    }
}
