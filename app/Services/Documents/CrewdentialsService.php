<?php

namespace App\Services\Documents;

use App\Models\User;
use App\Models\Document;
use App\Models\CrewdentialsConsent;
use App\Models\CrewdentialsSync;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CrewdentialsService
{
    protected string $apiBaseUrl;
    protected string $apiKey;
    protected int $maxRetries = 3;
    protected int $retryDelay = 5; // seconds

    public function __construct()
    {
        $this->apiBaseUrl = config('services.crewdentials.api_url', 'https://crewdentials-api.onrender.com/api/v1');
        $this->apiKey = config('services.crewdentials.api_key', '');
    }

    /**
     * Check if user has an account on Crewdentials
     */
    public function checkAccountExists(string $email): bool
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->apiBaseUrl . '/account/check', [
                'email' => $email,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['exists'] ?? false;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Crewdentials account check failed', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get documents from Crewdentials for a user
     */
    public function getDocuments(string $email): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->apiBaseUrl . '/documents', [
                'crewEmailAddress' => $email,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Crewdentials get documents failed', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Send document to Crewdentials for verification
     */
    public function sendDocumentForVerification(Document $document, User $user): array
    {
        // Check consent first
        if (!CrewdentialsConsent::hasConsent($user->id)) {
            throw new \Exception('User has not given consent to share data with Crewdentials');
        }

        try {
            // Prepare document data
            $documentData = [
                'crewEmailAddress' => $user->email,
                'documentId' => $document->id,
                'documentType' => $this->mapDocumentType($document),
                'documentName' => $document->document_name ?? $document->documentType->name ?? 'Document',
                'issueDate' => $document->issue_date?->format('Y-m-d'),
                'expiryDate' => $document->expiry_date?->format('Y-m-d'),
                'documentNumber' => $document->document_number,
                'issuingAuthority' => $document->issuing_authority,
                'issuingCountry' => $document->issuing_country,
            ];

            // If file exists, include file data
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                $filePath = Storage::disk('public')->path($document->file_path);
                $fileContent = base64_encode(file_get_contents($filePath));
                $documentData['file'] = $fileContent;
                $documentData['fileName'] = basename($document->file_path);
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->apiBaseUrl . '/documents/verify', $documentData);

            if ($response->successful()) {
                $result = $response->json();
                
                // Log sync
                CrewdentialsSync::create([
                    'user_id' => $user->id,
                    'document_id' => $document->id,
                    'sync_type' => 'verification_request',
                    'direction' => 'to_crewdentials',
                    'status' => 'completed',
                    'crewdentials_document_id' => $result['document_id'] ?? null,
                    'crewdentials_response' => json_encode($result),
                    'synced_at' => now(),
                ]);

                return $result;
            }

            throw new \Exception('Crewdentials API error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Crewdentials send document failed', [
                'document_id' => $document->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            // Log failed sync
            CrewdentialsSync::create([
                'user_id' => $user->id,
                'document_id' => $document->id,
                'sync_type' => 'verification_request',
                'direction' => 'to_crewdentials',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Update document status from Crewdentials webhook
     */
    public function processVerificationResult(array $webhookData): bool
    {
        try {
            $crewdentialsDocumentId = $webhookData['document_id'] ?? null;
            $status = $webhookData['status'] ?? null; // verified, rejected, pending, expired
            $verificationData = $webhookData['verification_data'] ?? [];

            // Find sync record
            $sync = CrewdentialsSync::where('crewdentials_document_id', $crewdentialsDocumentId)
                ->where('sync_type', 'verification_request')
                ->latest()
                ->first();

            if (!$sync || !$sync->document_id) {
                Log::warning('Crewdentials webhook: Document not found', [
                    'crewdentials_document_id' => $crewdentialsDocumentId,
                ]);
                return false;
            }

            $document = Document::find($sync->document_id);
            if (!$document) {
                return false;
            }

            // Update document status
            $ywcStatus = $this->mapCrewdentialsStatusToYWC($status);
            $document->update([
                'status' => $ywcStatus,
                'crewdentials_verification_data' => json_encode($verificationData),
            ]);

            // Send notification to user
            try {
                $document->user->notify(new \App\Notifications\CrewdentialsVerificationComplete($document, $status));
            } catch (\Exception $e) {
                Log::error('Failed to send verification notification', [
                    'document_id' => $document->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Log verification result sync
            CrewdentialsSync::create([
                'user_id' => $document->user_id,
                'document_id' => $document->id,
                'sync_type' => 'verification_result',
                'direction' => 'from_crewdentials',
                'status' => 'completed',
                'crewdentials_document_id' => $crewdentialsDocumentId,
                'crewdentials_response' => json_encode($webhookData),
                'synced_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Crewdentials webhook processing failed', [
                'webhook_data' => $webhookData,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Map YWC document type to Crewdentials format
     */
    protected function mapDocumentType(Document $document): string
    {
        // Map document types
        $typeMap = [
            'passport' => 'passport',
            'idvisa' => 'id_visa',
            'certificate' => 'certificate',
            'resume' => 'resume',
            'other' => 'other',
        ];

        $legacyType = $document->type ?? 'other';
        return $typeMap[$legacyType] ?? 'other';
    }

    /**
     * Map Crewdentials status to YWC status
     */
    protected function mapCrewdentialsStatusToYWC(string $crewdentialsStatus): string
    {
        $statusMap = [
            'verified' => 'approved',
            'rejected' => 'rejected',
            'pending' => 'pending',
            'expired' => 'expired',
        ];

        return $statusMap[$crewdentialsStatus] ?? 'pending';
    }

    /**
     * Retry failed sync with exponential backoff
     */
    public function retryFailedSync(CrewdentialsSync $sync): bool
    {
        if ($sync->retry_count >= $this->maxRetries) {
            return false;
        }

        $sync->increment('retry_count');
        $sync->update([
            'last_retry_at' => now(),
            'status' => 'processing',
        ]);

        // Wait before retry (exponential backoff)
        sleep($this->retryDelay * $sync->retry_count);

        // Retry based on sync type
        try {
            if ($sync->sync_type === 'verification_request' && $sync->document_id) {
                $document = Document::find($sync->document_id);
                $user = User::find($sync->user_id);
                
                if ($document && $user) {
                    $this->sendDocumentForVerification($document, $user);
                    return true;
                }
            }
        } catch (\Exception $e) {
            $sync->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            return false;
        }

        return false;
    }

    /**
     * Get profile preview iframe URL from Crewdentials
     */
    public function getProfilePreview(string $email, bool $dashboardMode = false, array $styleParams = []): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->apiBaseUrl . '/profilePreview', [
                'crewEmailAddress' => $email,
                'dashboardMode' => $dashboardMode,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $iframeUrl = $data['publicProfileIframeUrl'] ?? null;

                // Apply custom styling parameters if provided
                if ($iframeUrl && !empty($styleParams)) {
                    $queryParams = [];
                    
                    if (isset($styleParams['bgColor'])) {
                        $queryParams['bgColor'] = urlencode($styleParams['bgColor']);
                    }
                    if (isset($styleParams['textColor'])) {
                        $queryParams['textColor'] = urlencode($styleParams['textColor']);
                    }
                    if (isset($styleParams['fontFamily'])) {
                        $queryParams['fontFamily'] = urlencode($styleParams['fontFamily']);
                    }
                    if (isset($styleParams['accentColor'])) {
                        $queryParams['accentColor'] = urlencode($styleParams['accentColor']);
                    }
                    if (isset($styleParams['logoUrl'])) {
                        $queryParams['logoUrl'] = urlencode($styleParams['logoUrl']);
                    }

                    if (!empty($queryParams)) {
                        $separator = strpos($iframeUrl, '?') !== false ? '&' : '?';
                        $iframeUrl .= $separator . http_build_query($queryParams);
                    }
                }

                return [
                    'success' => true,
                    'crewEmailAddress' => $data['crewEmailAddress'] ?? $email,
                    'publicProfileIframeUrl' => $iframeUrl,
                    'hasProfile' => $data['hasProfile'] ?? false,
                    'isConnected' => $data['isConnected'] ?? false,
                    'docsCount' => $data['docsCount'] ?? 0,
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to get profile preview: ' . $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('Crewdentials profile preview failed', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get profile preview: ' . $e->getMessage(),
            ];
        }
    }
}
