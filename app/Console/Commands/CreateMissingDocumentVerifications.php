<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Document;
use App\Models\DocumentVerification;
use App\Models\VerificationLevel;

class CreateMissingDocumentVerifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:create-verifications 
                            {--dry-run : Show what would be created without actually creating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create DocumentVerification records for documents that don\'t have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        // Get default verification level (level 3 - Employer Verified)
        $defaultVerificationLevel = VerificationLevel::where('level', 3)
            ->where('is_active', true)
            ->first();

        if (!$defaultVerificationLevel) {
            // Fallback to first active level
            $defaultVerificationLevel = VerificationLevel::where('is_active', true)
                ->orderBy('level', 'asc')
                ->first();
        }

        if (!$defaultVerificationLevel) {
            $this->error('No active verification level found. Please create verification levels first.');
            return 1;
        }

        $this->info("Using verification level: {$defaultVerificationLevel->name} (Level {$defaultVerificationLevel->level})");

        // Get all documents that don't have a verification record
        $documentsWithoutVerification = Document::whereDoesntHave('verifications')
            ->get();

        $this->info("Found {$documentsWithoutVerification->count()} documents without verification records.");

        if ($documentsWithoutVerification->isEmpty()) {
            $this->info('All documents already have verification records.');
            return 0;
        }

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No records will be created');
            $this->table(
                ['ID', 'Type', 'Status', 'User ID', 'Created At'],
                $documentsWithoutVerification->map(function ($doc) {
                    return [
                        $doc->id,
                        $doc->type ?? 'N/A',
                        $doc->status ?? 'N/A',
                        $doc->user_id ?? 'N/A',
                        $doc->created_at?->format('Y-m-d H:i:s') ?? 'N/A',
                    ];
                })
            );
            return 0;
        }

        $bar = $this->output->createProgressBar($documentsWithoutVerification->count());
        $bar->start();

        $created = 0;
        foreach ($documentsWithoutVerification as $document) {
            try {
                DocumentVerification::create([
                    'document_id' => $document->id,
                    'verification_level_id' => $defaultVerificationLevel->id,
                    'verifier_id' => null,
                    'verifier_type' => 'employer',
                    'status' => 'pending',
                    'notes' => null,
                ]);
                $created++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Failed to create verification for document {$document->id}: {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully created {$created} verification records.");

        return 0;
    }
}
