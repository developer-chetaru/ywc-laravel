<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\User;
use App\Services\Documents\CrewdentialsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncDocumentToCrewdentials implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Document $document,
        public User $user,
        public string $syncType = 'verification_request'
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(CrewdentialsService $service): void
    {
        try {
            if ($this->syncType === 'verification_request') {
                $service->sendDocumentForVerification($this->document, $this->user);
            }
        } catch (\Exception $e) {
            Log::error('SyncDocumentToCrewdentials job failed', [
                'document_id' => $this->document->id,
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e; // Re-throw to trigger retry
        }
    }
}
