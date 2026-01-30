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
     * Also supports document_id parameter: ?document_id=112
     *
     * GET /api/document-verification/verify/{certificateNumber}
     * GET /api/document-verification/verify/{certificateNumber}?document_id=112
     */
    public function verifyByCertificateNumber(Request $request, string $certificateNumber): JsonResponse
    {
        // If document_id is provided, find certificate number from that document
        if ($request->has('document_id')) {
            $documentId = $request->get('document_id');
            $document = Document::withTrashed()->find($documentId);
            
            if (! $document) {
                return response()->json([
                    'success' => false,
                    'verified' => false,
                    'message' => 'Document not found. Please check the Document ID.',
                ], 404);
            }

            // Find approved verification for this document
            $verification = DocumentVerification::where('document_id', $documentId)
                ->where('status', 'approved')
                ->with(['document:id,user_id,document_type_id,document_name,issue_date,expiry_date,status', 'document.documentType:id,name', 'verificationLevel:id,name,level,description'])
                ->first();

            if (! $verification) {
                // Check if there's a pending verification
                $pendingVerification = DocumentVerification::where('document_id', $documentId)
                    ->where('status', 'pending')
                    ->first();
                
                if ($pendingVerification) {
                    return response()->json([
                        'success' => false,
                        'verified' => false,
                        'message' => 'Document verification is pending. Please approve the document first using API 3 (Approve/Reject).',
                        'document_id' => $documentId,
                        'verification_id' => $pendingVerification->id,
                    ], 404);
                }
                
                return response()->json([
                    'success' => false,
                    'verified' => false,
                    'message' => 'Document is not verified yet. Please approve the document first using API 3 (Approve/Reject).',
                    'document_id' => $documentId,
                ], 404);
            }

            // Get or generate certificate number
            if (! $verification->certificate_number) {
                $certificateNumber = 'YWC-'.strtoupper(substr(md5($documentId.$verification->id), 0, 12));
                $verification->update(['certificate_number' => $certificateNumber]);
            } else {
                $certificateNumber = $verification->certificate_number;
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

        // Original logic: verify by certificate number
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
     * Now returns documents directly from documents table (status: pending)
     *
     * GET /api/document-verification/list
     */
    public function list(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 20), 100);
        
        // Optional: allow filtering by status (default: pending)
        $status = $request->get('status', 'pending');
        
        // Debug mode: if debug=1 or debug=true, return all statuses
        $debug = $request->get('debug', false);
        $debug = filter_var($debug, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? ($debug === '1' || $debug === 1 || $debug === 'true');

        // Load documents directly from documents table
        $query = Document::with([
            'user:id,first_name,last_name,email',
            'documentType:id,name',
        ])
            ->withTrashed(); // Include soft-deleted documents
        
        // Apply status filter only if not in debug mode
        if (! $debug) {
            $query->whereRaw('LOWER(status) = ?', [strtolower($status)]);
        }
        
        $documents = $query->orderBy('created_at', 'asc')
            ->paginate($perPage);

        $items = $documents->getCollection()->map(function ($doc) {
            // Get verification level if exists
            $verification = DocumentVerification::where('document_id', $doc->id)
                ->where('status', 'pending')
                ->with('verificationLevel:id,name,level,description')
                ->first();

            return [
                'verification_id' => $verification?->id ?? null,
                'document_id' => $doc->id,
                'document_name' => $doc->document_name ?? 'N/A',
                'document_type' => $doc->documentType?->name ?? ($doc->type ?? 'N/A'),
                'document_number' => $doc->document_number ?? null,
                'issue_date' => $doc->issue_date?->format('Y-m-d'),
                'expiry_date' => $doc->expiry_date?->format('Y-m-d'),
                'document_status' => $doc->status ?? 'pending',
                'verification_level' => $verification?->verificationLevel?->name ?? 'N/A',
                'verification_level_id' => $verification?->verification_level_id ?? null,
                'requested_notes' => $verification?->notes ?? null,
                'created_at' => $doc->created_at->toIso8601String(),
                'owner' => $doc->user ? [
                    'id' => $doc->user->id,
                    'name' => trim(($doc->user->first_name ?? '').' '.($doc->user->last_name ?? '')),
                    'email' => $doc->user->email ?? null,
                ] : null,
            ];
        });

        $response = [
            'success' => true,
            'data' => $items->values(),
            'meta' => [
                'current_page' => $documents->currentPage(),
                'last_page' => $documents->lastPage(),
                'per_page' => $documents->perPage(),
                'total' => $documents->total(),
            ],
        ];
        
        // Add debug info if debug mode is enabled
        if ($debug) {
            $totalAllDocuments = Document::withTrashed()->count();
            $totalPendingDocuments = Document::withTrashed()->whereRaw('LOWER(status) = ?', ['pending'])->count();
            
            $response['debug'] = [
                'debug_mode_enabled' => true,
                'status_filter' => $status,
                'total_documents_found' => $documents->total(),
                'items_after_filtering' => $items->count(),
                'total_all_documents_in_db' => $totalAllDocuments,
                'total_pending_documents' => $totalPendingDocuments,
                'all_statuses_in_db' => Document::withTrashed()->distinct()
                    ->pluck('status')
                    ->toArray(),
                'total_documents_by_status' => Document::withTrashed()
                    ->selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray(),
            ];
        }
        
        return response()->json($response);
    }

    /**
     * Approve or reject a document verification with remark.
     * Requires auth:sanctum (Bearer token).
     *
     * POST /api/document-verification/decide
     * Body: verification_id (optional if document_id provided), document_id (optional if verification_id provided), status (approved|rejected), remark (optional)
     */
    public function decide(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'verification_id' => 'nullable|exists:document_verifications,id',
            'document_id' => 'nullable|exists:documents,id',
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

        // Either verification_id or document_id must be provided
        if (! $request->filled('verification_id') && ! $request->filled('document_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Either verification_id or document_id is required.',
            ], 422);
        }

        // Find verification by verification_id or document_id
        if ($request->filled('verification_id')) {
            $verification = DocumentVerification::where('id', $request->verification_id)
                ->where('status', 'pending')
                ->with(['document', 'verificationLevel'])
                ->first();
        } else {
            // Find or create verification for document_id
            $document = Document::findOrFail($request->document_id);
            
            // Get default verification level (Level 3 - Employer Verified)
            $defaultVerificationLevel = \App\Models\VerificationLevel::where('level', 3)
                ->where('is_active', true)
                ->first();
            
            if (! $defaultVerificationLevel) {
                $defaultVerificationLevel = \App\Models\VerificationLevel::where('is_active', true)
                    ->orderBy('level', 'asc')
                    ->first();
            }

            if (! $defaultVerificationLevel) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active verification level found.',
                ], 500);
            }

            // Find existing pending verification or create new one
            $verification = DocumentVerification::where('document_id', $document->id)
                ->where('status', 'pending')
                ->where('verification_level_id', $defaultVerificationLevel->id)
                ->with(['document', 'verificationLevel'])
                ->first();

            if (! $verification) {
                $verification = DocumentVerification::create([
                    'document_id' => $document->id,
                    'verification_level_id' => $defaultVerificationLevel->id,
                    'verifier_id' => null,
                    'verifier_type' => 'employer',
                    'status' => 'pending',
                    'notes' => null,
                ]);
                $verification->load(['document', 'verificationLevel']);
            }
        }

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

            // Generate certificate number if not already set
            if (! $verification->certificate_number) {
                $certificateNumber = 'YWC-'.strtoupper(substr(md5($document->id.$verification->id), 0, 12));
                $verification->update([
                    'certificate_number' => $certificateNumber,
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
