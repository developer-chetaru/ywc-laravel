<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProfileShare;
use App\Services\Documents\ProfileShareService;

class ProfileShareController extends Controller
{
    protected $shareService;

    public function __construct(ProfileShareService $shareService)
    {
        $this->shareService = $shareService;
    }

    /**
     * Create a new profile share
     */
    public function store(Request $request)
    {
        $request->validate([
            'shared_sections' => 'nullable|array',
            'shared_sections.*' => 'in:personal_info,documents,career_history',
            'document_categories' => 'nullable|array',
            'career_entry_ids' => 'nullable|array',
            'career_entry_ids.*' => 'exists:career_history_entries,id',
            'recipient_email' => 'nullable|email',
            'recipient_name' => 'nullable|string|max:255',
            'personal_message' => 'nullable|string|max:500',
            'expires_in_days' => 'nullable|integer|min:1|max:365',
            'generate_qr_code' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $expiresAt = null;

        if ($request->expires_in_days) {
            $expiresAt = \Carbon\Carbon::now()->addDays($request->expires_in_days);
        }

        try {
            $share = $this->shareService->createShare(
                $user,
                $request->shared_sections ?? ['personal_info', 'documents', 'career_history'],
                $request->document_categories,
                $request->career_entry_ids,
                $request->recipient_email,
                $request->recipient_name,
                $request->personal_message,
                $expiresAt,
                $request->generate_qr_code ?? false
            );

            return response()->json([
                'success' => true,
                'share' => $share,
                'share_url' => $share->share_url,
                'qr_code_url' => $share->qr_code_path ? asset('storage/' . $share->qr_code_path) : null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * View shared profile (public, no auth required)
     */
    public function view(Request $request, string $token)
    {
        $share = $this->shareService->getShareByToken($token);

        if (!$share || !$share->isValid()) {
            abort(404, 'Share link not found or expired');
        }

        // Record access
        $this->shareService->recordAccess($share);

        $user = $share->user;

        // Load data based on shared sections
        $data = [
            'share' => $share,
            'user' => $user,
        ];

        if ($share->hasSection('documents')) {
            $query = $user->documents()->with('documentType');
            
            if ($share->document_categories) {
                $query->whereHas('documentType', function($q) use ($share) {
                    $q->whereIn('slug', $share->document_categories);
                });
            }
            
            $data['documents'] = $query->get();
        }

        if ($share->hasSection('career_history')) {
            $query = $user->careerHistoryEntries();
            
            if ($share->career_entry_ids) {
                $query->whereIn('id', $share->career_entry_ids);
            }
            
            $data['careerEntries'] = $query->orderBy('start_date', 'desc')->get();
        }

        return view('profile.share-view', $data);
    }

    /**
     * Get all profile shares for authenticated user
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
     * Revoke a profile share
     */
    public function revoke(ProfileShare $share)
    {
        $user = Auth::user();

        if ($share->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $this->shareService->revokeShare($share);

        return response()->json([
            'success' => true,
            'message' => 'Profile share revoked successfully',
        ]);
    }

    /**
     * Generate QR code for existing share
     */
    public function generateQrCode(ProfileShare $share)
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
}
