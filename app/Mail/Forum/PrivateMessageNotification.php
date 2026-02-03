<?php

namespace App\Mail\Forum;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PrivateMessageNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public User $sender;

    /**
     * Create a new message instance.
     */
    public function __construct($message, User $sender)
    {
        $this->message = $message;
        $this->sender = $sender;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $messageId = is_object($this->message) ? $this->message->id : $this->message['id'];
        $messageUrl = route('forum.messages.conversation', ['messageId' => $messageId]);

        return $this->subject("New Private Message from {$this->sender->first_name} {$this->sender->last_name}")
            ->view('emails.forum.private-message', [
                'message' => is_object($this->message) ? (array) $this->message : $this->message,
                'sender' => $this->sender,
                'messageUrl' => $messageUrl,
            ]);
    }
}
