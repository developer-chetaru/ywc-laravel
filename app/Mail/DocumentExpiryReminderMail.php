<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Document;
use Carbon\Carbon;

class DocumentExpiryReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Document $document;
    public string $reminderType;
    public string $timeRemaining;
    public bool $isExpired;

    /**
     * Create a new message instance.
     */
    public function __construct(Document $document, string $reminderType)
    {
        $this->document = $document;
        $this->reminderType = $reminderType;
        $this->isExpired = $document->isExpired();
        
        // Calculate time remaining
        if ($this->isExpired) {
            $daysOverdue = $document->expiry_date->diffInDays(now());
            $this->timeRemaining = $daysOverdue . ' day' . ($daysOverdue !== 1 ? 's' : '') . ' overdue';
        } else {
            $daysRemaining = now()->diffInDays($document->expiry_date);
            if ($daysRemaining >= 30) {
                $months = floor($daysRemaining / 30);
                $this->timeRemaining = $months . ' month' . ($months !== 1 ? 's' : '') . ' remaining';
            } else {
                $this->timeRemaining = $daysRemaining . ' day' . ($daysRemaining !== 1 ? 's' : '') . ' remaining';
            }
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $docName = $this->document->document_name ?? 'Your document';
        $subject = $this->isExpired
            ? "Action Required: {$docName} has expired"
            : "Reminder: {$docName} expires soon";

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.document-expiry-reminder',
            with: [
                'document' => $this->document,
                'reminderType' => $this->reminderType,
                'timeRemaining' => $this->timeRemaining,
                'isExpired' => $this->isExpired,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
