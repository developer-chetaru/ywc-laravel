<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentStatusChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\DocumentStatusChangedMail;

class DocumentApprovalController extends Controller
{
    /**
     * Get pending documents for approval
     */
    public function index(Request $request)
    {
        // Allow unauthenticated access for token-based authentication
        // The API route has auth:sanctum middleware, but we can check token here too
        
        $query = Document::with(['user', 'documentType'])
            ->where('status', 'pending')
            ->whereNull('deleted_at');

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('document_name', 'like', '%' . $search . '%')
                    ->orWhere('document_number', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            });
        }

        // Type filter
        if ($request->has('filter_type') && $request->filter_type !== 'all') {
            $query->where('type', $request->filter_type);
        }

        $documents = $query->orderBy('created_at', 'desc')
            ->paginate(12);

        // Format file paths and ensure relationships are loaded
        $documents->getCollection()->transform(function ($document) {
            // Ensure user relationship is loaded
            if (!$document->relationLoaded('user')) {
                $document->load('user');
            }
            
            // Ensure documentType relationship is loaded
            if (!$document->relationLoaded('documentType')) {
                $document->load('documentType');
            }
            
            if ($document->file_path && $document->file_path !== 'null') {
                // Check if it's already a full URL
                if (!filter_var($document->file_path, FILTER_VALIDATE_URL)) {
                    // Try to get storage URL - use asset() for proper URL generation
                    if (\Storage::disk('public')->exists($document->file_path)) {
                        $document->file_path = asset('storage/' . $document->file_path);
                    } elseif (\Storage::disk('local')->exists($document->file_path)) {
                        // Try local disk
                        $document->file_path = \Storage::disk('local')->url($document->file_path);
                    } else {
                        // If file doesn't exist, set to null
                        $document->file_path = null;
                    }
                } else {
                    // Already a full URL, ensure it uses proper domain
                    $document->file_path = str_replace('http://127.0.0.1:8000', url('/'), $document->file_path);
                    $document->file_path = str_replace('http://localhost:8000', url('/'), $document->file_path);
                }
            } else {
                $document->file_path = null;
            }
            
            return $document;
        });

        return response()->json([
            'success' => true,
            'documents' => $documents
        ]);
    }

    /**
     * Approve or reject a document
     */
    public function approve(Request $request, $documentId)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500',
        ]);

        $document = Document::where('status', 'pending')
            ->findOrFail($documentId);

        $oldStatus = $document->status;
        $newStatus = $request->action === 'approve' ? 'approved' : 'rejected';

        $document->update([
            'status' => $newStatus,
            'updated_by' => Auth::id(),
        ]);

        // Create status change record
        DocumentStatusChange::create([
            'document_id' => $document->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => Auth::id(),
            'notes' => $request->notes ?: "Document {$newStatus} via approval page",
        ]);

        // Send notification email
        try {
            Mail::to($document->user->email)
                ->send(new DocumentStatusChangedMail($document, $newStatus, $request->notes));
        } catch (\Exception $e) {
            \Log::error('Failed to send document status email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => "Document {$newStatus} successfully!",
        ]);
    }
}
