<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Document;
use App\Models\DocumentType;
use App\Services\Documents\CrewdentialsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ImportDocumentsFromCrewdentials implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $tries = 3;
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public array $crewdentialsDocuments
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(CrewdentialsService $service): void
    {
        try {
            foreach ($this->crewdentialsDocuments as $crewdDoc) {
                // Check if document already imported
                $existing = Document::where('crewdentials_document_id', $crewdDoc['id'] ?? null)
                    ->where('user_id', $this->user->id)
                    ->first();

                if ($existing) {
                    continue; // Skip already imported
                }

                // Map Crewdentials document type to YWC document type
                $documentType = $this->mapCrewdentialsTypeToYWC($crewdDoc['type'] ?? 'other');
                $documentTypeModel = DocumentType::where('slug', $documentType)->first();

                // Check if category could be auto-matched
                $needsCategoryAssignment = $documentTypeModel === null || $documentType === 'other';

                // Download file if URL provided
                $filePath = null;
                if (!empty($crewdDoc['file_url'])) {
                    $filePath = $this->downloadFile($crewdDoc['file_url'], $this->user->id);
                }

                // Create document
                $document = Document::create([
                    'user_id' => $this->user->id,
                    'type' => $this->mapLegacyType($documentType),
                    'document_type_id' => $documentTypeModel?->id,
                    'document_name' => $crewdDoc['name'] ?? 'Imported Document',
                    'document_number' => $crewdDoc['document_number'] ?? null,
                    'issuing_authority' => $crewdDoc['issuing_authority'] ?? null,
                    'issuing_country' => $crewdDoc['issuing_country'] ?? null,
                    'issue_date' => !empty($crewdDoc['issue_date']) ? Carbon::parse($crewdDoc['issue_date']) : null,
                    'expiry_date' => !empty($crewdDoc['expiry_date']) ? Carbon::parse($crewdDoc['expiry_date']) : null,
                    'file_path' => $filePath,
                    'status' => $this->mapCrewdentialsStatusToYWC($crewdDoc['verification_status'] ?? 'pending'),
                    'crewdentials_document_id' => $crewdDoc['id'] ?? null,
                    'crewdentials_verification_data' => json_encode($crewdDoc['verification_data'] ?? []),
                    'imported_from_crewdentials' => true,
                    'crewdentials_verified_at' => !empty($crewdDoc['verified_at']) ? Carbon::parse($crewdDoc['verified_at']) : null,
                    'needs_category_assignment' => $needsCategoryAssignment,
                ]);

                Log::info('Document imported from Crewdentials', [
                    'document_id' => $document->id,
                    'crewdentials_document_id' => $crewdDoc['id'] ?? null,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('ImportDocumentsFromCrewdentials job failed', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Map Crewdentials document type to YWC type
     */
    protected function mapCrewdentialsTypeToYWC(string $type): string
    {
        $typeMap = [
            'passport' => 'passport',
            'id_visa' => 'idvisa',
            'certificate' => 'certificate',
            'resume' => 'resume',
            'other' => 'other',
        ];

        return $typeMap[$type] ?? 'other';
    }

    /**
     * Map to legacy type enum
     */
    protected function mapLegacyType(string $type): string
    {
        return in_array($type, ['passport', 'idvisa', 'certificate', 'resume', 'other']) 
            ? $type 
            : 'other';
    }

    /**
     * Map Crewdentials status to YWC status
     */
    protected function mapCrewdentialsStatusToYWC(string $status): string
    {
        $statusMap = [
            'verified' => 'approved',
            'rejected' => 'rejected',
            'pending' => 'pending',
            'expired' => 'expired',
        ];

        return $statusMap[$status] ?? 'pending';
    }

    /**
     * Download file from URL
     */
    protected function downloadFile(string $url, int $userId): ?string
    {
        try {
            $response = Http::timeout(30)->get($url);
            
            if ($response->successful()) {
                $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'pdf';
                $filename = 'crewdentials-import-' . time() . '-' . uniqid() . '.' . $extension;
                $year = Carbon::now()->format('Y');
                $month = Carbon::now()->format('m');
                $path = "documents/{$userId}/{$year}/{$month}/{$filename}";
                
                Storage::disk('public')->put($path, $response->body());
                
                return $path;
            }
        } catch (\Exception $e) {
            Log::error('Failed to download file from Crewdentials', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }
}
