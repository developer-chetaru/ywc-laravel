<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use App\Models\Document;
use App\Models\PassportDetail;
use App\Models\IdvisaDetail;
use App\Models\Certificate;
use App\Models\OtherDocument;
use ZipArchive;
use Illuminate\Support\Facades\Storage;

class ShareDocumentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $documents;    // Collection of Document objects
    public $messageText;
  	public $senderName;

    /**
     * Create a new message instance.
     */
    public function __construct($documents, $messageText, $senderName)
    {
         $this->documents = Document::whereIn('id', $documents)->get()->map(function ($doc) {
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

        $this->messageText = $messageText;
        $this->senderName = $senderName;
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
        return new Envelope(
            subject: 'Shared Documents',
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
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->documents as $document) {
            $filePath = storage_path('app/public/' . $document->file_path);
            if (file_exists($filePath)) {
                $attachments[] = Attachment::fromPath($filePath)
                    ->as($document->name . '.' . pathinfo($filePath, PATHINFO_EXTENSION))
                    ->withMime(mime_content_type($filePath));
            }
        }

        return $attachments;
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
