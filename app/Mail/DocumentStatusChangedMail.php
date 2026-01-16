<?php

namespace App\Mail;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Document $document;
    public string $status;
    public ?string $notes;

    /**
     * Create a new message instance.
     */
    public function __construct(Document $document, string $status, ?string $notes = null)
    {
        $this->document = $document;
        $this->status = $status;
        $this->notes = $notes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->status) {
            'approved' => 'Document Approved: ' . ($this->document->document_name ?? 'Your Document'),
            'rejected' => 'Document Rejected: ' . ($this->document->document_name ?? 'Your Document'),
            default => 'Document Status Updated: ' . ($this->document->document_name ?? 'Your Document'),
        };

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
            view: 'emails.document-status-changed',
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
