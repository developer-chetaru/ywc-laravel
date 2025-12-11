<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FinancialNotifications extends Component
{
    use WithPagination;

    public function markAsRead($notificationId)
    {
        FinancialNotification::where('id', $notificationId)
            ->where('user_id', Auth::id())
            ->update(['is_read' => true]);
    }

    public function markAllAsRead()
    {
        FinancialNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        session()->flash('message', 'All notifications marked as read.');
    }

    public function delete($notificationId)
    {
        FinancialNotification::where('id', $notificationId)
            ->where('user_id', Auth::id())
            ->delete();
        
        session()->flash('message', 'Notification deleted.');
        $this->resetPage();
    }

    public function render()
    {
        $notifications = FinancialNotification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = FinancialNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('livewire.financial-planning.financial-notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}

