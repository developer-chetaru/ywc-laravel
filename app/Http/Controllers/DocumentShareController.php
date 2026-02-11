<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\DocumentShare;
use App\Models\ShareAuditLog;
use App\Services\Documents\DocumentShareService;
use App\Services\Documents\WatermarkService;
use Carbon\Carbon;

class DocumentShareController extends Controller
{
    protected $shareService;
    protected $watermarkService;

    public function __construct(DocumentShareService $shareService, WatermarkService $watermarkService)
    {
        $this->shareService = $shareService;
        $this->watermarkService = $watermarkService;
    }

    /**
     * Create a new document share
     */
    public function store(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array|min:1',
            'document_ids.*' => 'exists:documents,id',
            'recipient_email' => 'nullable|email', // For backward compatibility
            'recipient_emails' => 'nullable|array|max:10', // Multiple emails support
            'recipient_emails.*' => 'email',
            'recipient_name' => 'nullable|string|max:255',
            'personal_message' => 'nullable|string|max:500',
            'expires_in_days' => 'nullable|integer|min:1|max:365',
            'expiry_option' => 'nullable|in:7,30,permanent',
            'password' => 'nullable|string|min:6|max:50',
            'restrict_to_email' => 'nullable|email',
            'generate_qr_code' => 'nullable|boolean',
            'permissions' => 'nullable|array',
            'permissions.can_download' => 'nullable|boolean',
            'permissions.can_print' => 'nullable|boolean',
            'permissions.is_one_time' => 'nullable|boolean',
            'permissions.max_views' => 'nullable|integer|min:1',
        ]);

        $user = Auth::user();
        $expiresAt = null;

        // Handle expiry options: 7 days, 30 days, or permanent (null)
        if ($request->expiry_option === 'permanent') {
            $expiresAt = null;
        } elseif ($request->expiry_option) {
            $expiresAt = Carbon::now()->addDays((int)$request->expiry_option);
        } elseif ($request->expires_in_days) {
            $expiresAt = Carbon::now()->addDays($request->expires_in_days);
        }

        try {
            $generateQrCode = $request->boolean('generate_qr_code', false);
            
            // Handle multiple recipient emails - use first one for share record, send emails to all
            $recipientEmails = $request->recipient_emails ?? [];
            if ($request->recipient_email && !in_array($request->recipient_email, $recipientEmails)) {
                $recipientEmails[] = $request->recipient_email; // For backward compatibility
            }
            $recipientEmails = array_unique(array_filter($recipientEmails)); // Remove duplicates and empty values
            $primaryRecipientEmail = !empty($recipientEmails) ? $recipientEmails[0] : $request->recipient_email;
            
            // Log for debugging
            \Log::info('Creating document share', [
                'user_id' => $user->id,
                'document_ids' => $request->document_ids,
                'recipient_emails' => $recipientEmails,
                'primary_recipient_email' => $primaryRecipientEmail,
                'generate_qr_code' => $generateQrCode,
            ]);
            
            // Create share with primary recipient email (for database record)
            $share = $this->shareService->createShare(
                $user,
                $request->document_ids,
                $primaryRecipientEmail, // Use first email for share record
                $request->recipient_name,
                $request->personal_message,
                $expiresAt,
                $request->permissions,
                $request->password,
                $request->restrict_to_email,
                $generateQrCode
            );
            
            // Send email notifications to all recipient emails
            if (!empty($recipientEmails)) {
                foreach ($recipientEmails as $email) {
                    try {
                        \Mail::to($email)->send(new \App\Mail\DocumentShareMail($share, $user));
                        \Log::info('Share email sent', ['email' => $email, 'share_id' => $share->id]);
                    } catch (\Exception $e) {
                        \Log::error('Failed to send share email', [
                            'email' => $email,
                            'share_id' => $share->id,
                            'error' => $e->getMessage()
                        ]);
                        // Continue sending to other emails even if one fails
                    }
                }
            }

            // Reload share to get updated qr_code_path
            $share->refresh();
            
            \Log::info('Share created, checking QR code', [
                'share_id' => $share->id,
                'qr_code_path' => $share->qr_code_path,
                'qr_code_exists' => $share->qr_code_path ? Storage::disk('public')->exists($share->qr_code_path) : false,
            ]);

            $qrCodeUrl = null;
            if ($share->qr_code_path && Storage::disk('public')->exists($share->qr_code_path)) {
                $qrCodeUrl = asset('storage/' . $share->qr_code_path);
            }

            return response()->json([
                'success' => true,
                'share' => $share->load('documents'),
                'share_url' => $share->share_url,
                'qr_code_url' => $qrCodeUrl,
                'qr_code_generated' => !empty($share->qr_code_path),
                'qr_code_path' => $share->qr_code_path,
            ]);
        } catch (\Exception $e) {
            \Log::error('Document share creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
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

        // Check email restriction
        $accessingEmail = $request->query('email') ?? $request->header('X-Forwarded-For-Email');
        
        // Check if share is accessible (including max_views, email restriction check)
        if (!$share->isAccessible($accessingEmail)) {
            if ($share->restrict_to_email && $accessingEmail && 
                strtolower(trim($accessingEmail)) !== strtolower(trim($share->restrict_to_email))) {
                abort(403, 'This share link is restricted to a specific email address. Please access it from the email link.');
            }
            if ($share->max_views && $share->view_count >= $share->max_views) {
                abort(403, 'Maximum view limit reached. This share link has been viewed the maximum number of times.');
            }
            abort(403, 'Share link is no longer accessible.');
        }

        // Check password protection
        if ($share->requiresPassword() && !session()->has("share_password_verified_{$share->id}")) {
            return view('documents.share-password', compact('share', 'token'));
        }

        // Record access (only if accessible)
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

        // Check email restriction
        $accessingEmail = $request->query('email') ?? $request->header('X-Forwarded-For-Email');
        if (!$share->isAccessible($accessingEmail)) {
            abort(403, 'Share link is no longer accessible.');
        }

        // Check password protection
        if ($share->requiresPassword() && !session()->has("share_password_verified_{$share->id}")) {
            abort(403, 'Password required to access this share.');
        }

        // Check download permission
        if (!$share->canDownload()) {
            abort(403, 'Download is not allowed for this share.');
        }

        // Verify document belongs to this share
        $document = $share->documents()->findOrFail($documentId);

        if (!$document->file_path || !\Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Document file not found');
        }

        // Record download
        $this->shareService->recordDownload($share);

        // Add watermark for shared documents if required
        $filePath = $document->file_path;
        if ($share->require_watermark) {
            $watermarkedPath = $this->watermarkService->addWatermarkToShared($document);
            if ($watermarkedPath) {
                $filePath = $watermarkedPath;
            }
        }

        return \Storage::disk('public')->download(
            $filePath,
            ($document->document_name ?? 'document') . '.' . pathinfo($document->file_path, PATHINFO_EXTENSION)
        );
    }

    /**
     * Verify password for password-protected share
     */
    public function verifyPassword(Request $request, string $token)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $share = $this->shareService->getShareByToken($token);

        if (!$share) {
            abort(404, 'Share link not found');
        }

        if (!$share->verifyPassword($request->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        session()->put("share_password_verified_{$share->id}", true);

        return redirect()->route('documents.share.view', ['token' => $token]);
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
     * Show the create share documents page
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get documents that can be shared (same logic as CareerHistoryController)
        $share_documents = \App\Models\Document::with(['passportDetail', 'idvisaDetail', 'certificates.type', 'certificates.issuer', 'otherDocument'])
            ->where('user_id', $user->id)
            ->where('is_active', 1)
            ->get();
        
        // Get templates
        $templates = \App\Models\ShareTemplate::forUser($user->id)
            ->orderBy('is_default', 'desc')
            ->orderBy('name', 'asc')
            ->get();
        
        return view('documents.share-create', compact('share_documents', 'templates'));
    }

    /**
     * Get all shares for authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 10); // Default 10 per page
        $page = $request->get('page', 1);
        
        // Get all shares
        $allShares = $this->shareService->getUserShares($user);
        
        // Filter active shares
        $activeShares = $allShares->filter(function($share) {
            return $share->is_active && (!$share->expires_at || \Carbon\Carbon::parse($share->expires_at)->isFuture());
        })->values();
        
        // Manual pagination
        $total = $activeShares->count();
        $lastPage = (int) ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        $paginatedShares = $activeShares->slice($offset, $perPage)->values();

        return response()->json([
            'success' => true,
            'shares' => $paginatedShares,
            'pagination' => [
                'current_page' => (int) $page,
                'last_page' => $lastPage,
                'per_page' => (int) $perPage,
                'total' => $total,
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total),
            ],
        ]);
    }

    /**
     * Generate QR code for existing share
     */
    public function generateQrCode(DocumentShare $share)
    {
        $user = Auth::user();

        if ($share->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $qrCodePath = $this->shareService->generateQrCode($share);
        $share->update(['qr_code_path' => $qrCodePath]);

        return response()->json([
            'success' => true,
            'qr_code_url' => asset('storage/' . $qrCodePath),
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
