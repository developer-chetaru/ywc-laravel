<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Services\Forum\PrivateMessageService;

#[Layout('layouts.app')]
class MessageList extends Component
{
    use WithPagination;

    public string $folder = 'inbox'; // 'inbox', 'sent', 'archived'
    public ?int $selectedMessageId = null;

    protected PrivateMessageService $messageService;

    public function boot(PrivateMessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function mount(?string $folder = 'inbox')
    {
        $this->folder = $folder ?? 'inbox';
        
        // Open send message modal if requested
        if (request()->has('new')) {
            $this->dispatch('openSendMessage');
        }
    }

    public function switchFolder(string $folder)
    {
        $this->folder = $folder;
        $this->resetPage();
        $this->selectedMessageId = null;
    }

    public function selectMessage(int $messageId)
    {
        $this->selectedMessageId = $messageId;
    }

    public function markAsRead(int $messageId)
    {
        $this->messageService->markAsRead(Auth::user(), $messageId);
        $this->dispatch('messageRead');
    }

    public function archiveMessage(int $messageId)
    {
        $this->messageService->archiveMessage(Auth::user(), $messageId);
        session()->flash('success', 'Message archived.');
        $this->resetPage();
    }

    public function unarchiveMessage(int $messageId)
    {
        $this->messageService->unarchiveMessage(Auth::user(), $messageId);
        session()->flash('success', 'Message unarchived.');
        $this->resetPage();
    }

    public function deleteMessage(int $messageId)
    {
        $this->messageService->deleteMessage(Auth::user(), $messageId);
        session()->flash('success', 'Message deleted.');
        $this->resetPage();
        if ($this->selectedMessageId === $messageId) {
            $this->selectedMessageId = null;
        }
    }

    public function render()
    {
        $messages = collect($this->messageService->getUserMessages(Auth::user(), $this->folder, 50));
        $unreadCount = $this->messageService->getUnreadCount(Auth::user());

        return view('livewire.forum.message-list', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
        ]);
    }
}
