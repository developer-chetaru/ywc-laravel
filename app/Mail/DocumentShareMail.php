<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\DocumentShare;
use App\Models\User;

class DocumentShareMail extends Mailable
{
    use Queueable, SerializesModels;

    public DocumentShare $share;
    public User $sender;

    public function __construct(DocumentShare $share, User $sender)
    {
        $this->share = $share;
        $this->sender = $sender;
    }

    public function envelope(): Envelope
    {
        $senderName = $this->sender->first_name . ' ' . $this->sender->last_name;
        return new Envelope(
            subject: "{$senderName} has shared documents with you via YWC",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.document-share',
            with: [
                'share' => $this->share,
                'sender' => $this->sender,
                'shareUrl' => $this->share->share_url,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
