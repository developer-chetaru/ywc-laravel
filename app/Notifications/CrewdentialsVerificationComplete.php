<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class CrewdentialsVerificationComplete extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Document $document,
        public string $status // verified, rejected, pending, expired
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusMessages = [
            'verified' => 'Your document has been verified by Crewdentials.',
            'rejected' => 'Your document verification was rejected. Please check the details.',
            'pending' => 'Crewdentials needs more information to verify your document.',
            'expired' => 'Your document has expired.',
        ];

        $message = (new MailMessage)
            ->subject('Document Verification Update - ' . ($this->document->document_name ?? 'Document'))
            ->line($statusMessages[$this->status] ?? 'Your document verification status has been updated.')
            ->line('Document: ' . ($this->document->document_name ?? 'Unknown'))
            ->action('View Document', route('career-history', ['user' => $notifiable->id]));

        if ($this->status === 'rejected') {
            $message->line('Please review your document and resubmit if needed.');
        }

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'crewdentials_verification',
            'document_id' => $this->document->id,
            'document_name' => $this->document->document_name,
            'status' => $this->status,
            'message' => $this->getStatusMessage(),
        ];
    }

    /**
     * Get status message
     */
    protected function getStatusMessage(): string
    {
        return match($this->status) {
            'verified' => 'Your document "' . ($this->document->document_name ?? 'Document') . '" has been verified by Crewdentials.',
            'rejected' => 'Your document "' . ($this->document->document_name ?? 'Document') . '" verification was rejected.',
            'pending' => 'Crewdentials needs more information to verify your document "' . ($this->document->document_name ?? 'Document') . '".',
            'expired' => 'Your document "' . ($this->document->document_name ?? 'Document') . '" has expired.',
            default => 'Your document verification status has been updated.',
        };
    }
}
