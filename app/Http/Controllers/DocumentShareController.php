<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DocumentShare;
use App\Services\Documents\DocumentShareService;
use Carbon\Carbon;

class DocumentShareController extends Controller
{
    protected $shareService;

    public function __construct(DocumentShareService $shareService)
    {
        $this->shareService = $shareService;
    }

    /**
     * Create a new document share
     */
    public function store(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array|min:1',
            'document_ids.*' => 'exists:documents,id',
            'recipient_email' => 'nullable|email',
            'recipient_name' => 'nullable|string|max:255',
            'personal_message' => 'nullable|string|max:500',
            'expires_in_days' => 'nullable|integer|min:1|max:365',
        ]);

        $user = Auth::user();
        $expiresAt = null;

        if ($request->expires_in_days) {
            $expiresAt = Carbon::now()->addDays($request->expires_in_days);
        }

        try {
            $share = $this->shareService->createShare(
                $user,
                $request->document_ids,
                $request->recipient_email,
                $request->recipient_name,
                $request->personal_message,
                $expiresAt
            );

            return response()->json([
                'success' => true,
                'share' => $share->load('documents'),
                'share_url' => $share->share_url,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * View shared documents (public, no auth required)
     */
    public function view(Request $request, string $token)
    {
        $share = $this->shareService->getShareByToken($token);

        if (!$share || !$share->isValid()) {
            abort(404, 'Share link not found or expired');
        }

        // Record access
        $this->shareService->recordAccess($share);

        $documents = $share->documents()->with('documentType')->get();
        $sender = $share->user;

        return view('documents.share-view', compact('share', 'documents', 'sender'));
    }

    /**
     * Get all shares for authenticated user
     */
    public function index()
    {
        $user = Auth::user();
        $shares = $this->shareService->getUserShares($user);

        return response()->json([
            'success' => true,
            'shares' => $shares,
        ]);
    }

    /**
     * Revoke a share
     */
    public function revoke(DocumentShare $share)
    {
        $user = Auth::user();

        if ($share->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $this->shareService->revokeShare($share);

        return response()->json([
            'success' => true,
            'message' => 'Share revoked successfully',
        ]);
    }

    /**
     * Resend share email
     */
    public function resend(DocumentShare $share)
    {
        $user = Auth::user();

        if ($share->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if (!$share->recipient_email) {
            return response()->json([
                'success' => false,
                'message' => 'No recipient email associated with this share',
            ], 400);
        }

        \Mail::to($share->recipient_email)->send(new \App\Mail\DocumentShareMail($share, $user));

        return response()->json([
            'success' => true,
            'message' => 'Email resent successfully',
        ]);
    }
}
