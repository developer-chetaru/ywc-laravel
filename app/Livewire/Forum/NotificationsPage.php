<?php

namespace App\Livewire\Forum;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationsPage extends Component
{
    public $filter = 'all'; // all, unread, read

    public function updatedFilter()
    {
        $this->dispatch('refreshNotifications');
    }

    public function render()
    {
        return view('livewire.forum.notifications-page')->layout('layouts.app');
    }
}
