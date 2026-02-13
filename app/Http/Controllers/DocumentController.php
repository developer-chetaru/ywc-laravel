<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ShareDocumentMail;
use Illuminate\Support\Facades\Validator;
use App\Models\Document;
use App\Models\PassportDetail;
use App\Models\IdvisaDetail;
use App\Models\Certificate;
use App\Models\OtherDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\Documents\DocumentShareService;
use Carbon\Carbon;

class DocumentController extends Controller
{
    protected $shareService;

    public function __construct(DocumentShareService $shareService)
    {
        $this->shareService = $shareService;
    }
  
  	public function share(Request $request)
    {
        $request->validate([
            'emails' => 'required|string',
            'message' => 'required|string',
            'documents' => 'required|array|min:1'
        ]);

        $toEmails = collect(explode(',', $request->input('emails')))
            ->map(fn($email) => trim($email))
            ->filter()
            ->unique();

        // Validate email format
        foreach ($toEmails as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return back()->withErrors(['emails' => "Invalid email address: {$email}"]);
            }
        }

        $documentIds = $request->input('documents');
        $message = $request->input('message');
        $user = Auth::user();

        // Default expiry: 30 days
        $expiresAt = Carbon::now()->addDays(30);

        // Create a share for each recipient
        $sharesCreated = [];
        
        foreach ($toEmails as $recipientEmail) {
            try {
                // Create token-based share using DocumentShareService
                // Pass null for recipient_email to prevent auto-email, we'll send manually
                $share = $this->shareService->createShare(
                    $user,
                    $documentIds,
                    null, // Don't pass recipient_email to prevent auto-email
                    null, // recipient_name
                    $message,
                    $expiresAt
                );
                
                // Update share with recipient email
                $share->update(['recipient_email' => $recipientEmail]);
                
                // Send email using ShareDocumentMail (legacy template but with token-based share)
                \Mail::to($recipientEmail)->send(new \App\Mail\ShareDocumentMail($share, $message));
                
                $sharesCreated[] = $share;
            } catch (\Exception $e) {
                // Log error but continue with other recipients
                \Log::error('Failed to create share for ' . $recipientEmail . ': ' . $e->getMessage());
                continue;
            }
        }

        if (empty($sharesCreated)) {
            return back()->withErrors(['emails' => 'Failed to create share links. Please try again.']);
        }

        return back()->with('success', 'Documents shared successfully! Share links have been sent to recipients.');
    }
  
  
    public function sharesdfsfdsdf(Request $request)
    {
         $request->validate([
            'emails' => 'required|string',
            'message' => 'required|string',
            'documents' => 'required|array|min:1'
        ]);

        $toEmails = explode(',', $request->input('emails'));
        $documents = $request->input('documents');
        $message = $request->input('message');
        $authUserName = Auth::user()->name;

        // Send emails
        foreach ($toEmails as $email) {
            \Mail::to(trim($email))
                ->send(new \App\Mail\ShareDocumentMail($documents,$message,$authUserName));
        }

        return back()->with('success', 'Documents shared successfully!');
    }
  
    public function destroy($id)
    {
        $document = Document::find($id);

        if (!$document) {
            return response()->json(['success' => false, 'message' => 'Document not found.']);
        }

        // Check authorization - user can only delete their own documents
        if ($document->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. You can only delete your own documents.'], 403);
        }

        // Delete file if exists
        if ($document->file_path) {
            try {
                if (Storage::disk('public')->exists($document->file_path)) {
                    Storage::disk('public')->delete($document->file_path);
                }
                // Also delete thumbnail if exists
                if ($document->thumbnail_path && Storage::disk('public')->exists($document->thumbnail_path)) {
                    Storage::disk('public')->delete($document->thumbnail_path);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to delete document file: ' . $e->getMessage());
            }
        }

        // Delete related data
        $document->passportDetail?->delete();
        $document->idvisaDetail?->delete();
        $document->certificates()->delete();
        $document->otherDocument?->delete();
        $document->statusChanges()->delete();
        $document->versions()->delete();

        // Delete the document record
        $document->delete();

        return response()->json(['success' => true, 'message' => 'Document deleted successfully.']);
    }
}