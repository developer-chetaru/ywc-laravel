<?php

namespace App\Http\Controllers;

use App\Models\DocumentShare;
use App\Models\ShareTemplate;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ShareWizardController extends Controller
{
    /**
     * Show share wizard
     */
    public function showWizard($documentId)
    {
        $document = Document::where('user_id', Auth::id())->findOrFail($documentId);
        $templates = ShareTemplate::where('user_id', Auth::id())
            ->orWhere('is_default', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('usage_count', 'desc')
            ->get();

        return view('shares.wizard', compact('document', 'templates'));
    }

    /**
     * Create share from wizard
     */
    public function createShare(Request $request, $documentId)
    {
        $document = Document::where('user_id', Auth::id())->findOrFail($documentId);

        $validated = $request->validate([
            'recipient_email' => 'required|email',
            'recipient_name' => 'nullable|string|max:255',
            'personal_message' => 'nullable|string|max:1000',
            'expires_in_days' => 'nullable|integer|min:1|max:365',
            
            // Permissions
            'can_download' => 'boolean',
            'can_print' => 'boolean',
            'can_share' => 'boolean',
            'can_comment' => 'boolean',
            
            // Access Control
            'is_one_time' => 'boolean',
            'max_views' => 'nullable|integer|min:1',
            'password' => 'nullable|string|min:6',
            'require_watermark' => 'boolean',
            
            // Time Restrictions
            'access_start_date' => 'nullable|date|after_or_equal:today',
            'access_end_date' => 'nullable|date|after:access_start_date',
            
            // Template
            'template_id' => 'nullable|exists:share_templates,id',
            'share_notes' => 'nullable|string|max:500',
        ]);

        // Generate share token
        $token = DocumentShare::generateToken();

        // Calculate expiry
        $expiresAt = null;
        if ($request->filled('expires_in_days')) {
            $expiresAt = now()->addDays($validated['expires_in_days']);
        }

        // Create share
        $share = DocumentShare::create([
            'user_id' => Auth::id(),
            'share_token' => $token,
            'token_hash' => DocumentShare::hashToken($token),
            'recipient_email' => $validated['recipient_email'],
            'recipient_name' => $validated['recipient_name'] ?? null,
            'personal_message' => $validated['personal_message'] ?? null,
            'expires_at' => $expiresAt,
            'is_active' => true,
            'password_hash' => $request->filled('password') ? Hash::make($validated['password']) : null,
            
            // Granular permissions
            'can_download' => $request->boolean('can_download', true),
            'can_print' => $request->boolean('can_print', true),
            'can_share' => $request->boolean('can_share', false),
            'can_comment' => $request->boolean('can_comment', false),
            
            // Access control
            'is_one_time' => $request->boolean('is_one_time', false),
            'max_views' => $validated['max_views'] ?? null,
            'view_count' => 0,
            'require_watermark' => $request->boolean('require_watermark', false),
            
            // Time restrictions
            'access_start_date' => $validated['access_start_date'] ?? null,
            'access_end_date' => $validated['access_end_date'] ?? null,
            
            // Metadata
            'share_type' => 'wizard',
            'share_notes' => $validated['share_notes'] ?? null,
        ]);

        // Attach document
        $share->documents()->attach($documentId);

        // Update template usage count
        if ($request->filled('template_id')) {
            ShareTemplate::find($validated['template_id'])->increment('usage_count');
        }

        // Generate share URL
        $shareUrl = route('shared.view', ['token' => $token]);

        return redirect()->route('career-history.index')
            ->with('success', 'Document shared successfully!')
            ->with('share_url', $shareUrl);
    }

    /**
     * View shared document (public)
     */
    public function viewShared(Request $request, $token)
    {
        $tokenHash = DocumentShare::hashToken($token);
        $share = DocumentShare::where('token_hash', $tokenHash)
            ->with('documents')
            ->firstOrFail();

        // Check if accessible
        if (!$share->isAccessible()) {
            abort(403, 'This share link is no longer accessible.');
        }

        // Check password
        if ($share->requiresPassword() && !session()->has("share_password_verified_{$share->id}")) {
            return view('shares.password', compact('share', 'token'));
        }

        // Increment view count
        $share->incrementViewCount();

        $document = $share->documents->first();

        return view('shares.view', compact('share', 'document', 'token'));
    }

    /**
     * Verify share password
     */
    public function verifyPassword(Request $request, $token)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $tokenHash = DocumentShare::hashToken($token);
        $share = DocumentShare::where('token_hash', $tokenHash)->firstOrFail();

        if (!$share->verifyPassword($request->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        session()->put("share_password_verified_{$share->id}", true);

        return redirect()->route('shared.view', ['token' => $token]);
    }

    /**
     * Download shared document
     */
    public function downloadShared($token)
    {
        $tokenHash = DocumentShare::hashToken($token);
        $share = DocumentShare::where('token_hash', $tokenHash)
            ->with('documents')
            ->firstOrFail();

        if (!$share->canDownload()) {
            abort(403, 'Download is not allowed for this share.');
        }

        $document = $share->documents->first();

        if (!$document) {
            abort(404, 'Document not found.');
        }

        // If watermark required, apply it
        if ($share->require_watermark) {
            return $this->downloadWithWatermark($document, $share);
        }

        $share->increment('download_count');

        return response()->download(storage_path('app/' . $document->file_path));
    }

    /**
     * Download with watermark
     */
    private function downloadWithWatermark($document, $share)
    {
        // TODO: Implement watermarking service
        // For now, just download normally
        $share->increment('download_count');
        return response()->download(storage_path('app/' . $document->file_path));
    }
}
