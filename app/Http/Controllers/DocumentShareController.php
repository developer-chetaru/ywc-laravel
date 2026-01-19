<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DocumentShare;
use App\Models\ShareAuditLog;
use App\Services\Documents\DocumentShareService;
use App\Services\Documents\WatermarkService;
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
     * Download document from share
     */
    public function download(Request $request, string $token, int $documentId)
    {
        $share = $this->shareService->getShareByToken($token);

        if (!$share || !$share->isValid()) {
            abort(404, 'Share link not found or expired');
        }

        // Verify document belongs to this share
        $document = $share->documents()->findOrFail($documentId);

        if (!$document->file_path || !\Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Document file not found');
        }

        // Record download
        $this->shareService->recordDownload($share);

        // Add watermark for shared documents
        $watermarkedPath = $this->watermarkService->addWatermarkToShared($document);
        $filePath = $watermarkedPath ?? $document->file_path;

        return \Storage::disk('public')->download(
            $filePath,
            ($document->document_name ?? 'document') . '.' . pathinfo($document->file_path, PATHINFO_EXTENSION)
        );
    }

    /**
     * Report abuse
     */
    public function reportAbuse(Request $request, string $token)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $share = $this->shareService->getShareByToken($token);

        if (!$share) {
            abort(404, 'Share link not found');
        }

        $this->shareService->reportAbuse($share, $request->reason);

        return response()->json([
            'success' => true,
            'message' => 'Abuse report submitted. Thank you for your feedback.',
        ]);
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

    /**
     * Get share analytics
     */
    public function analytics()
    {
        $user = Auth::user();
        $shares = DocumentShare::where('user_id', $user->id)
            ->with('documents')
            ->get();

        $analytics = [
            'total_shares' => $shares->count(),
            'active_shares' => $shares->where('is_active', true)->count(),
            'expired_shares' => $shares->where('is_active', false)->count(),
            'total_views' => $shares->sum('access_count'),
            'total_downloads' => $shares->sum('download_count'),
            'shares_by_month' => $shares->groupBy(function($share) {
                return $share->created_at->format('Y-m');
            })->map(function($group) {
                return $group->count();
            }),
            'top_shared_documents' => \DB::table('document_share_documents')
                ->join('documents', 'document_share_documents.document_id', '=', 'documents.id')
                ->join('document_shares', 'document_share_documents.document_share_id', '=', 'document_shares.id')
                ->where('document_shares.user_id', $user->id)
                ->select('documents.id', 'documents.document_name', \DB::raw('COUNT(*) as share_count'))
                ->groupBy('documents.id', 'documents.document_name')
                ->orderBy('share_count', 'desc')
                ->limit(10)
                ->get(),
        ];

        // Get audit logs for detailed analytics
        $auditLogs = ShareAuditLog::where('share_type', 'document')
            ->whereIn('shareable_id', $shares->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        return response()->json([
            'success' => true,
            'analytics' => $analytics,
            'recent_activity' => $auditLogs,
        ]);
    }
}
