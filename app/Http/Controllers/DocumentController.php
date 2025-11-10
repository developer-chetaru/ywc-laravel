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

class DocumentController extends Controller
{
  
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

        $documents = $request->input('documents');
        $message = $request->input('message');
        $authUserName = Auth::user()->name;

        // Send to all recipients at once
        \Mail::to($toEmails->toArray())
            ->send(new \App\Mail\ShareDocumentMail($documents, $message, $authUserName));

        return back()->with('success', 'Documents shared successfully!');
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

          if ($document->file_path && Storage::exists('public/' . $document->file_path)) {
              Storage::delete('public/' . $document->file_path);
          }

          // Delete connected data if needed
          // Example: $document->related()->delete();

          // Delete the document record
          $document->delete();

          return response()->json(['success' => true]);
      }
}