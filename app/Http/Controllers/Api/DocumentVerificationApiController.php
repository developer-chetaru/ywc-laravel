<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DocumentVerificationApiController extends Controller
{
    /**
     * Verify document by certificate number (public API for 3rd parties).
     * No auth required – certificate number acts as proof.
     *
     * GET /api/document-verification/verify/{certificateNumber}
     */
    public function verifyByCertificateNumber(string $certificateNumber): JsonResponse
    {
        $certificateNumber = strtoupper(trim($certificateNumber));

        if (empty($certificateNumber) || ! str_starts_with($certificateNumber, 'YWC-')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid certificate number format. Expected format: YWC-XXXXXXXXXXXX',
            ], 400);
        }

        $verification = DocumentVerification::where('certificate_number', $certificateNumber)
            ->where('status', 'approved')
            ->with(['document:id,user_id,document_type_id,document_name,issue_date,expiry_date,status', 'document.documentType:id,name', 'verificationLevel:id,name,level,description'])
            ->first();

        if (! $verification) {
            $verifications = DocumentVerification::where('status', 'approved')
                ->with(['document:id,user_id,document_type_id,document_name,issue_date,expiry_date,status', 'document.documentType:id,name', 'verificationLevel:id,name,level,description'])
                ->get();

            foreach ($verifications as $v) {
                $computed = 'YWC-'.strtoupper(substr(md5($v->document_id.$v->id), 0, 12));
                if ($computed === $certificateNumber) {
                    $verification = $v;
                    break;
                }
            }
        }

        if (! $verification) {
            return response()->json([
                'success' => false,
                'verified' => false,
                'message' => 'Certificate number not found or document is not verified.',
            ], 404);
        }

        $document = $verification->document;

        return response()->json([
            'success' => true,
            'verified' => true,
            'certificate_number' => $certificateNumber,
            'document' => [
                'id' => $document->id,
                'document_name' => $document->document_name,
                'document_type' => $document->documentType?->name,
                'issue_date' => $document->issue_date?->format('Y-m-d'),
                'expiry_date' => $document->expiry_date?->format('Y-m-d'),
                'status' => $document->status,
            ],
            'verification' => [
                'level' => $verification->verificationLevel->level,
                'level_name' => $verification->verificationLevel->name,
                'description' => $verification->verificationLevel->description,
                'verified_at' => $verification->verified_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * List pending document verifications (for 3rd party to verify).
     * Requires auth:sanctum (Bearer token).
     *
     * GET /api/document-verification/list
     */
    public function list(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 20), 100);

        $verifications = DocumentVerification::with([
            'document:id,user_id,document_type_id,document_name,issue_date,expiry_date,status,document_number',
            'document.user:id,first_name,last_name,email',
            'document.documentType:id,name',
            'verificationLevel:id,name,level,description',
        ])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);

        $items = $verifications->getCollection()->map(function ($v) {
            $doc = $v->document;

            return [
                'verification_id' => $v->id,
                'document_id' => $doc->id,
                'document_name' => $doc->document_name,
                'document_type' => $doc->documentType?->name,
                'document_number' => $doc->document_number,
                'issue_date' => $doc->issue_date?->format('Y-m-d'),
                'expiry_date' => $doc->expiry_date?->format('Y-m-d'),
                'document_status' => $doc->status,
                'verification_level' => $v->verificationLevel->name,
                'verification_level_id' => $v->verification_level_id,
                'requested_notes' => $v->notes,
                'created_at' => $v->created_at->toIso8601String(),
                'owner' => $doc->user ? [
                    'id' => $doc->user->id,
                    'name' => $doc->user->first_name.' '.$doc->user->last_name,
                    'email' => $doc->user->email,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $items,
            'meta' => [
                'current_page' => $verifications->currentPage(),
                'last_page' => $verifications->lastPage(),
                'per_page' => $verifications->perPage(),
                'total' => $verifications->total(),
            ],
        ]);
    }

    /**
     * Approve or reject a document verification with remark.
     * Requires auth:sanctum (Bearer token).
     *
     * POST /api/document-verification/decide
     * Body: verification_id, status (approved|rejected), remark (optional)
     */
    public function decide(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'verification_id' => 'required|exists:document_verifications,id',
            'status' => 'required|in:approved,rejected',
            'remark' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $verification = DocumentVerification::where('id', $request->verification_id)
            ->where('status', 'pending')
            ->with(['document', 'verificationLevel'])
            ->first();

        if (! $verification) {
            return response()->json([
                'success' => false,
                'message' => 'Verification not found or already decided.',
            ], 404);
        }

        $document = $verification->document;
        $remark = $request->filled('remark') ? $request->remark : null;

        $verification->update([
            'status' => $request->status,
            'verifier_id' => Auth::id(),
            'notes' => $remark,
            'verified_at' => now(),
        ]);

        if ($request->status === 'approved') {
            $verificationLevel = $verification->verificationLevel;
            $currentLevel = $document->verificationLevel ? $document->verificationLevel->level : 0;
            if ($verificationLevel->level > $currentLevel) {
                $document->update([
                    'verification_level_id' => $verificationLevel->id,
                    'highest_verification_level' => $verificationLevel->level,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Verification '.$request->status.' successfully.',
            'data' => [
                'verification_id' => $verification->id,
                'document_id' => $document->id,
                'status' => $verification->status,
                'remark' => $remark,
                'verified_at' => $verification->verified_at->toIso8601String(),
            ],
        ]);
    }
}
