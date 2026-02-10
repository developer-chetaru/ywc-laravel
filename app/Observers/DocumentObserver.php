<?php

namespace App\Observers;

use App\Models\Document;
use App\Models\CrewdentialsConsent;
use App\Jobs\SyncDocumentToCrewdentials;
use App\Services\Documents\CrewdentialsService;
use Illuminate\Support\Facades\Log;

class DocumentObserver
{
    protected CrewdentialsService $crewdentialsService;

    public function __construct(CrewdentialsService $crewdentialsService)
    {
        $this->crewdentialsService = $crewdentialsService;
    }

    /**
     * Handle the Document "created" event.
     */
    public function created(Document $document): void
    {
        // Auto-sync to Crewdentials if enabled and user has consent
        if (!$document->imported_from_crewdentials && $this->shouldAutoSync($document)) {
            try {
                SyncDocumentToCrewdentials::dispatch($document, $document->user, 'verification_request');
            } catch (\Exception $e) {
                Log::error('Auto-sync failed on document creation', [
                    'document_id' => $document->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Handle the Document "updated" event.
     */
    public function updated(Document $document): void
    {
        // Auto-sync updates to Crewdentials if enabled and user has consent
        if (!$document->imported_from_crewdentials && $this->shouldAutoSync($document)) {
            // Only sync if important fields changed
            if ($document->wasChanged(['file_path', 'document_name', 'expiry_date', 'issue_date'])) {
                try {
                    SyncDocumentToCrewdentials::dispatch($document, $document->user, 'verification_request');
                } catch (\Exception $e) {
                    Log::error('Auto-sync failed on document update', [
                        'document_id' => $document->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Handle the Document "deleted" event (soft delete).
     */
    public function deleted(Document $document): void
    {
        // Flag as deleted on Crewdentials (soft delete only)
        if (!$document->imported_from_crewdentials && $this->shouldAutoSync($document)) {
            if ($document->crewdentials_document_id) {
                try {
                    // Mark sync as deleted (soft delete flag)
                    \App\Models\CrewdentialsSync::create([
                        'user_id' => $document->user_id,
                        'document_id' => $document->id,
                        'sync_type' => 'export',
                        'direction' => 'to_crewdentials',
                        'status' => 'completed',
                        'crewdentials_document_id' => $document->crewdentials_document_id,
                        'crewdentials_response' => json_encode(['deleted' => true, 'deleted_at' => now()]),
                        'synced_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to flag document deletion on Crewdentials', [
                        'document_id' => $document->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Check if document should be auto-synced
     */
    protected function shouldAutoSync(Document $document): bool
    {
        // Check if user has consent
        if (!CrewdentialsConsent::hasConsent($document->user_id)) {
            return false;
        }

        // Check if auto-sync is enabled (can be added as user setting later)
        // For now, default to true if consent exists
        return true;
    }
}
