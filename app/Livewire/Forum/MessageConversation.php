<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\Forum\PrivateMessageService;

#[Layout('layouts.app')]
class MessageConversation extends Component
{
    public int $messageId;
    public ?int $replyToMessageId = null;
    public string $replyContent = '';
    public string $replySubject = '';

    protected PrivateMessageService $messageService;

    public function boot(PrivateMessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function mount(int $messageId)
    {
        $this->messageId = $messageId;
    }

    public function reply()
    {
        $this->validate([
            'replyContent' => 'required|min:10|max:5000',
            'replySubject' => 'nullable|max:255',
        ]);

        $conversation = $this->messageService->getConversation(Auth::user(), $this->messageId);
        $originalMessage = $conversation->first();

        if (!$originalMessage) {
            session()->flash('error', 'Message not found.');
            return;
        }

        // Determine recipient - get the other participant
        $participants = DB::table('forum_message_participants')
            ->where('message_id', $this->messageId)
            ->where('user_id', '!=', Auth::id())
            ->pluck('user_id')
            ->toArray();

        $recipientId = !empty($participants) ? $participants[0] : $originalMessage->sender_id;

        // If still no recipient, try to find from conversation
        if (!$recipientId || $recipientId === Auth::id()) {
            $otherMessage = $conversation->where('sender_id', '!=', Auth::id())->first();
            $recipientId = $otherMessage ? $otherMessage->sender_id : $originalMessage->sender_id;
        }

        if (!$recipientId || $recipientId === Auth::id()) {
            session()->flash('error', 'Cannot determine recipient.');
            return;
        }

        $recipient = \App\Models\User::find($recipientId);
        if (!$recipient) {
            session()->flash('error', 'Recipient not found.');
            return;
        }

        try {
            $this->messageService->sendMessage(
                Auth::user(),
                $recipient,
                $this->replySubject ?: 'Re: ' . ($originalMessage->subject ?? 'No Subject'),
                $this->replyContent,
                $this->messageId
            );

            session()->flash('success', 'Reply sent successfully.');
            $this->reset(['replyContent', 'replySubject', 'replyToMessageId']);
            $this->dispatch('messageSent');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $conversation = $this->messageService->getConversation(Auth::user(), $this->messageId);
        $originalMessage = $conversation->first();

        return view('livewire.forum.message-conversation', [
            'conversation' => $conversation,
            'originalMessage' => $originalMessage,
        ]);
    }
}
