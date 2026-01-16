<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class DocumentDownloadController extends Controller
{
    /**
     * Generate a signed URL for document access
     * URL expires after 15 minutes
     */
    public function signedUrl(Document $document)
    {
        // Check authorization
        if (!Gate::allows('view', $document)) {
            abort(403, 'Unauthorized to access this document');
        }

        // Check if file exists
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Document file not found');
        }

        // Generate signed URL (expires in 15 minutes)
        $url = Storage::disk('public')->temporaryUrl(
            $document->file_path,
            now()->addMinutes(15)
        );

        return response()->json([
            'url' => $url,
            'expires_at' => now()->addMinutes(15)->toIso8601String(),
        ]);
    }

    /**
     * Download document with authorization check
     */
    public function download(Document $document)
    {
        // Check authorization
        if (!Gate::allows('view', $document)) {
            abort(403, 'Unauthorized to download this document');
        }

        // Check if file exists
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Document file not found');
        }

        // Get original filename or generate one
        $filename = $document->document_name ?? 'document';
        $extension = $document->file_type ?? pathinfo($document->file_path, PATHINFO_EXTENSION);
        $downloadFilename = $filename . '.' . $extension;

        // Log download access
        \Log::info('Document downloaded', [
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
        ]);

        return Storage::disk('public')->download(
            $document->file_path,
            $downloadFilename
        );
    }

    /**
     * View document (for inline viewing in browser)
     */
    public function view(Document $document)
    {
        // Check authorization
        if (!Gate::allows('view', $document)) {
            abort(403, 'Unauthorized to view this document');
        }

        // Check if file exists
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Document file not found');
        }

        // Get file content
        $file = Storage::disk('public')->get($document->file_path);
        $mimeType = Storage::disk('public')->mimeType($document->file_path);

        // Log view access
        \Log::info('Document viewed', [
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
        ]);

        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . ($document->document_name ?? 'document') . '"');
    }
}
