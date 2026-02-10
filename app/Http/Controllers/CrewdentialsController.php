<?php

namespace App\Http\Controllers;

use App\Models\CrewdentialsConsent;
use App\Models\Document;
use App\Models\User;
use App\Models\DocumentType;
use App\Services\Documents\CrewdentialsService;
use App\Jobs\SyncDocumentToCrewdentials;
use App\Jobs\ImportDocumentsFromCrewdentials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CrewdentialsController extends Controller
{
    protected CrewdentialsService $crewdentialsService;

    public function __construct(CrewdentialsService $crewdentialsService)
    {
        $this->crewdentialsService = $crewdentialsService;
    }

    /**
     * Check if user has Crewdentials account (CASE 1)
     */
    public function checkAccount(Request $request)
    {
        $user = Auth::user();
        
        try {
            $hasAccount = $this->crewdentialsService->checkAccountExists($user->email);
            
            return response()->json([
                'success' => true,
                'has_account' => $hasAccount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check account: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store consent
     */
    public function storeConsent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'has_consented' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();

        // If withdrawing consent
        if (!$request->has_consented) {
            $activeConsent = CrewdentialsConsent::getActiveConsent($user->id);
            if ($activeConsent) {
                $activeConsent->update([
                    'has_consented' => false,
                    'withdrawn_at' => now(),
                    'withdrawal_reason' => $request->withdrawal_reason ?? null,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Consent withdrawn successfully',
            ]);
        }

        // Store new consent
        CrewdentialsConsent::create([
            'user_id' => $user->id,
            'has_consented' => true,
            'policy_version' => config('services.crewdentials.policy_version', '1.0'),
            'policy_url' => config('services.crewdentials.policy_url', ''),
            'consented_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Consent stored successfully',
        ]);
    }

    /**
     * Get user's consent status
     */
    public function getConsent()
    {
        $user = Auth::user();
        $consent = CrewdentialsConsent::getActiveConsent($user->id);

        return response()->json([
            'success' => true,
            'has_consent' => $consent !== null,
            'consent' => $consent,
        ]);
    }

    /**
     * Import documents from Crewdentials (CASE 1)
     */
    public function importDocuments(Request $request)
    {
        $user = Auth::user();

        // Check consent
        if (!CrewdentialsConsent::hasConsent($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Consent required before importing documents',
                'requires_consent' => true,
            ], 403);
        }

        try {
            // Get documents from Crewdentials
            $crewdentialsDocs = $this->crewdentialsService->getDocuments($user->email);

            if (empty($crewdentialsDocs) || !isset($crewdentialsDocs['documents'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No documents found on Crewdentials',
                ], 404);
            }

            // Queue import job
            ImportDocumentsFromCrewdentials::dispatch($user, $crewdentialsDocs['documents']);

            // Check if any documents will need category assignment
            $needsCategoryCount = 0;
            foreach ($crewdentialsDocs['documents'] as $doc) {
                $docType = $this->mapCrewdentialsTypeToYWC($doc['type'] ?? 'other');
                $docTypeModel = \App\Models\DocumentType::where('slug', $docType)->first();
                if ($docTypeModel === null || $docType === 'other') {
                    $needsCategoryCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Document import started. You will be notified when complete.',
                'document_count' => count($crewdentialsDocs['documents']),
                'needs_category_count' => $needsCategoryCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to import documents from Crewdentials', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to import documents: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Request verification for documents (CASE 2)
     */
    public function requestVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_ids' => 'required|array|min:1',
            'document_ids.*' => 'exists:documents,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();

        // Check consent
        if (!CrewdentialsConsent::hasConsent($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Consent required before requesting verification',
                'requires_consent' => true,
            ], 403);
        }

        // Verify user owns all documents
        $documents = Document::whereIn('id', $request->document_ids)
            ->where('user_id', $user->id)
            ->get();

        if ($documents->count() !== count($request->document_ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Some documents not found or not owned by you',
            ], 403);
        }

        // Queue sync jobs for each document
        foreach ($documents as $document) {
            SyncDocumentToCrewdentials::dispatch($document, $user, 'verification_request');
        }

        return response()->json([
            'success' => true,
            'message' => 'Verification requests submitted. Documents will be processed in the background.',
            'document_count' => $documents->count(),
        ]);
    }

    /**
     * Webhook endpoint for Crewdentials callbacks
     */
    public function webhook(Request $request)
    {
        // Verify webhook signature
        $signature = $request->header('X-Crewdentials-Signature');
        $secret = config('services.crewdentials.webhook_secret');

        if ($secret && !$this->verifyWebhookSignature($request->getContent(), $signature, $secret)) {
            Log::warning('Crewdentials webhook: Invalid signature', [
                'signature' => $signature,
            ]);
            abort(401, 'Invalid webhook signature');
        }

        $payload = $request->all();
        $eventType = $payload['event_type'] ?? null;

        try {
            switch ($eventType) {
                case 'verification.completed':
                case 'verification.rejected':
                case 'verification.pending':
                case 'document.expired':
                    $this->crewdentialsService->processVerificationResult($payload);
                    break;

                default:
                    Log::warning('Crewdentials webhook: Unknown event type', [
                        'event_type' => $eventType,
                    ]);
            }

            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            Log::error('Crewdentials webhook processing failed', [
                'event_type' => $eventType,
                'payload' => $payload,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Webhook processing failed',
            ], 500);
        }
    }

    /**
     * Verify webhook signature
     */
    protected function verifyWebhookSignature(string $payload, ?string $signature, string $secret): bool
    {
        if (!$signature) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Get documents needing category assignment
     */
    public function getDocumentsNeedingCategory()
    {
        $user = Auth::user();
        
        $documents = Document::where('user_id', $user->id)
            ->where('needs_category_assignment', true)
            ->where('imported_from_crewdentials', true)
            ->with('documentType')
            ->get();

        $documentTypes = DocumentType::where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return response()->json([
            'success' => true,
            'documents' => $documents,
            'document_types' => $documentTypes,
        ]);
    }

    /**
     * Assign category to imported document
     */
    public function assignCategory(Request $request, Document $document)
    {
        $validator = Validator::make($request->all(), [
            'document_type_id' => 'required|exists:document_types,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();

        // Verify ownership
        if ($document->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $documentType = DocumentType::findOrFail($request->document_type_id);

        $document->update([
            'document_type_id' => $documentType->id,
            'type' => $this->mapDocumentTypeToLegacy($documentType->slug),
            'needs_category_assignment' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category assigned successfully',
        ]);
    }

    /**
     * Map document type to legacy enum
     */
    protected function mapDocumentTypeToLegacy(string $slug): string
    {
        $map = [
            'passport' => 'passport',
            'id-visa' => 'idvisa',
            'certificate' => 'certificate',
            'resume' => 'other',
        ];

        return $map[$slug] ?? 'other';
    }

    /**
     * Map Crewdentials type to YWC type
     */
    protected function mapCrewdentialsTypeToYWC(string $type): string
    {
        $typeMap = [
            'passport' => 'passport',
            'id_visa' => 'id-visa',
            'certificate' => 'certificate',
            'resume' => 'resume',
            'other' => 'other',
        ];

        return $typeMap[$type] ?? 'other';
    }

    /**
     * Retry failed sync
     */
    public function retrySync(Request $request, $syncId)
    {
        $sync = \App\Models\CrewdentialsSync::findOrFail($syncId);
        $user = Auth::user();

        if ($sync->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if ($sync->status !== 'failed' || $sync->retry_count >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Sync cannot be retried',
            ], 400);
        }

        $success = $this->crewdentialsService->retryFailedSync($sync);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Sync retry initiated' : 'Sync retry failed',
        ]);
    }

    /**
     * Get failed syncs for retry UI
     */
    public function getFailedSyncs()
    {
        $user = Auth::user();
        
        $failedSyncs = \App\Models\CrewdentialsSync::where('user_id', $user->id)
            ->where('status', 'failed')
            ->where('retry_count', '<', 3)
            ->with('document')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'syncs' => $failedSyncs,
        ]);
    }

    /**
     * Withdraw consent
     */
    public function withdrawConsent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'withdrawal_reason' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $activeConsent = CrewdentialsConsent::getActiveConsent($user->id);

        if (!$activeConsent) {
            return response()->json([
                'success' => false,
                'message' => 'No active consent found',
            ], 404);
        }

        $activeConsent->update([
            'has_consented' => false,
            'withdrawn_at' => now(),
            'withdrawal_reason' => $request->withdrawal_reason ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Consent withdrawn successfully. Future syncs will be stopped.',
        ]);
    }

    /**
     * Get Crewdentials profile preview iframe URL
     */
    public function getProfilePreview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'dashboard_mode' => 'boolean',
            'bg_color' => 'nullable|string',
            'text_color' => 'nullable|string',
            'font_family' => 'nullable|string',
            'accent_color' => 'nullable|string',
            'logo_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $email = $request->email ?? $user->email;
        $dashboardMode = $request->dashboard_mode ?? false;

        // Build style parameters
        $styleParams = [];
        if ($request->bg_color) {
            $styleParams['bgColor'] = $request->bg_color;
        }
        if ($request->text_color) {
            $styleParams['textColor'] = $request->text_color;
        }
        if ($request->font_family) {
            $styleParams['fontFamily'] = $request->font_family;
        }
        if ($request->accent_color) {
            $styleParams['accentColor'] = $request->accent_color;
        }
        if ($request->logo_url) {
            $styleParams['logoUrl'] = $request->logo_url;
        }

        $result = $this->crewdentialsService->getProfilePreview($email, $dashboardMode, $styleParams);

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 500);
    }
}
