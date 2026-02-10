<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use App\Models\Document;
use App\Models\DocumentShare;
use App\Models\PassportDetail;
use App\Models\IdvisaDetail;
use App\Models\Certificate;
use App\Models\OtherDocument;
use App\Services\Documents\WatermarkService;
use ZipArchive;
use Illuminate\Support\Facades\Storage;

class ShareDocumentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $documents;    // Collection of Document objects
    public $messageText;
  	public $senderName;
    public $sender; // Full user object for email
    public $share; // DocumentShare object for token-based sharing
    public $shareUrl; // Secure share URL

    /**
     * Create a new message instance.
     * 
     * @param DocumentShare $share The document share object
     * @param string $messageText Optional message text (if not using share's personal_message)
     */
    public function __construct(DocumentShare $share, $messageText = null)
    {
        $this->share = $share;
        $this->shareUrl = $share->share_url;
        $this->messageText = $messageText ?? $share->personal_message;
        $this->sender = $share->user;
        $this->senderName = $this->sender->name ?? ($this->sender->first_name . ' ' . $this->sender->last_name);

        // Load documents with proper names
        $this->documents = $share->documents()->get()->map(function ($doc) {
            if ($doc->type === 'passport') {
                $doc->extra_name = 'Passport';
            } elseif ($doc->type === 'idvisa') {
                $doc->extra_name = IdvisaDetail::where('document_id', $doc->id)->value('document_name');
            } elseif ($doc->type === 'certificate') {
                $doc->extra_name = \DB::table('certificate_types')
                    ->where('id', $doc->certificate_type_id)
                    ->value('name');
            } elseif ($doc->type === 'other') {
                $doc->extra_name = OtherDocument::where('document_id', $doc->id)->value('doc_name');
            } else {
                $doc->extra_name = ucfirst($doc->type);
            }

            return $doc;
        });
    }

    // public function __construct(array $documentIds, $messageText)
    // {
    //     // Fetch all selected documents from DB
    //     $this->documents = Document::whereIn('id', $documentIds)->get();
    //     $this->messageText = $messageText;

    //     // Generate ZIP path
    //     $this->zipPath = storage_path('app/public/share_documents.zip');

    //     // Create ZIP containing all selected documents
    //     $zip = new ZipArchive();
    //     if ($zip->open($this->zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
    //         foreach ($this->documents as $document) {
    //             $filePath = storage_path('app/public/' . $document->file_path); // Use public storage
    //             if (file_exists($filePath)) {
    //                 // Add file to ZIP with original filename
    //                 $zip->addFile($filePath, $document->name . '.' . pathinfo($filePath, PATHINFO_EXTENSION));
    //             }
    //         }
    //         $zip->close();
    //     }
    // }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $senderEmail = $this->sender ? $this->sender->email : config('mail.from.address');
        $senderNameForFrom = $this->sender ? ($this->sender->first_name . ' ' . $this->sender->last_name) : config('mail.from.name');
        
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address($senderEmail, $senderNameForFrom),
            subject: "📄 {$this->senderName} shared documents with you on YWC",
        );
    }

    // public function envelope(): Envelope
    // {
    //     return new Envelope(subject: 'Shared Documents (ZIP)');
    // }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.share_documents',
            with: [
                'messageText' => $this->messageText,
                'documents' => $this->documents,
                'senderName' => $this->senderName,
                'sender' => $this->sender,
                'share' => $this->share,
                'shareUrl' => $this->shareUrl,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     * Files are NOT attached - they are encrypted and accessed via secure links
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Documents are encrypted and accessed via secure links, not attached
        // This ensures better security and compliance with data protection standards
        return [];
    }

    // public function attachments(): array
    // {
    //     return [
    //         Attachment::fromPath($this->zipPath)
    //             ->as('SharedDocuments.zip')
    //             ->withMime('application/zip')
    //     ];
    // }
}
