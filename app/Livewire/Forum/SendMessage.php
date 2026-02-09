<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Services\Forum\PrivateMessageService;

#[Layout('layouts.app')]
class SendMessage extends Component
{
    use WithFileUploads;

    public ?int $recipientId = null;
    public string $recipientName = '';
    public string $subject = '';
    public string $content = '';
    public $attachments = [];
    public bool $showModal = false;
    public bool $isSending = false;

    protected PrivateMessageService $messageService;

    public function boot(PrivateMessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    protected $listeners = ['openSendMessage' => 'openModal'];

    public function mount(?int $recipientId = null, ?string $recipientName = null)
    {
        // Get recipientId from query parameter if not provided
        if (!$recipientId && request()->has('recipient_id')) {
            $recipientId = (int) request()->query('recipient_id');
        }
        
        if ($recipientId) {
            $this->recipientId = $recipientId;
            $recipient = \App\Models\User::find($recipientId);
            $this->recipientName = $recipientName ?? ($recipient ? $recipient->first_name . ' ' . $recipient->last_name : '');
        }
    }

    public function openModal(?int $recipientId = null, ?string $recipientName = null)
    {
        if ($recipientId) {
            $this->recipientId = $recipientId;
            $recipient = \App\Models\User::find($recipientId);
            $this->recipientName = $recipientName ?? ($recipient ? $recipient->first_name . ' ' . $recipient->last_name : '');
        } else {
            // Open without recipient - user can select
            $this->recipientId = null;
            $this->recipientName = '';
        }
        $this->showModal = true;
        $this->reset(['subject', 'content', 'attachments']);
    }

    public function closeModal()
    {
        if ($this->isSending) {
            return; // Don't close while sending
        }
        $this->showModal = false;
        $this->reset(['recipientId', 'recipientName', 'subject', 'content', 'attachments', 'isSending']);
    }

    public function send()
    {
        if ($this->isSending) {
            return; // Prevent double submission
        }

        // Validate
        try {
            $this->validate([
                'recipientId' => 'required|exists:users,id',
                'subject' => 'nullable|string|max:255',
                'content' => 'required|string|min:10|max:5000',
                'attachments.*' => 'nullable|file|max:5120', // 5MB
            ], [
                'recipientId.required' => 'Please select a recipient.',
                'recipientId.exists' => 'Selected recipient does not exist.',
                'content.required' => 'Message content is required.',
                'content.min' => 'Message must be at least 10 characters.',
                'content.max' => 'Message must not exceed 5000 characters.',
                'attachments.*.max' => 'Each attachment must not exceed 5MB.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->isSending = false;
            throw $e;
        }

        $recipient = \App\Models\User::find($this->recipientId);
        if (!$recipient) {
            session()->flash('error', 'Recipient not found.');
            $this->isSending = false;
            return;
        }

        $this->isSending = true;

        try {
            // Process attachments - store files first for Livewire
            $attachmentData = [];
            if ($this->attachments && !empty($this->attachments)) {
                foreach ($this->attachments as $file) {
                    if ($file) {
                        // Store the file and get the path
                        $storedPath = $file->store('forum/messages', 'public');
                        
                        $attachmentData[] = [
                            'file' => $storedPath, // Pass stored path instead of file object
                            'name' => $file->getClientOriginalName(),
                            'type' => $file->getMimeType(),
                            'size' => $file->getSize(),
                        ];
                    }
                }
            }

            $messageId = $this->messageService->sendMessage(
                Auth::user(),
                $recipient,
                trim($this->subject) ?: null,
                trim($this->content),
                null,
                !empty($attachmentData) ? $attachmentData : null
            );

            if (!$messageId) {
                throw new \Exception('Failed to create message. Please try again.');
            }

            $this->isSending = false;
            session()->flash('success', 'Message sent successfully!');
            return $this->redirect(route('forum.messages.index'), navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->isSending = false;
            // Validation errors are automatically handled by Livewire
            throw $e;
        } catch (\Exception $e) {
            $this->isSending = false;
            \Log::error('Send message error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'recipient_id' => $this->recipientId,
            ]);
            session()->flash('error', 'Failed to send message: ' . $e->getMessage());
        }
    }

    public function removeAttachment($index)
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }

    public function render()
    {
        return view('livewire.forum.send-message');
    }
}
