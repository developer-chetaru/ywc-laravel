<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\ProfileShare;
use App\Models\User;

class ProfileShareMail extends Mailable
{
    use Queueable, SerializesModels;

    public ProfileShare $share;
    public User $sender;

    public function __construct(ProfileShare $share, User $sender)
    {
        $this->share = $share;
        $this->sender = $sender;
    }

    public function envelope(): Envelope
    {
        $senderName = $this->sender->first_name . ' ' . $this->sender->last_name;
        return new Envelope(
            subject: "{$senderName} has shared their profile with you via YWC",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.profile-share',
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
