<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Document;
use App\Services\Documents\VersionService;

class CleanupOldVersions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:cleanup-versions 
                            {--keep=10 : Number of versions to keep per document}
                            {--document= : Specific document ID to clean up}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old document versions, keeping only the most recent N versions per document';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $keepCount = (int) $this->option('keep');
        $documentId = $this->option('document');
        $dryRun = $this->option('dry-run');

        $this->info("Starting version cleanup (keeping {$keepCount} versions per document)...");
        
        if ($dryRun) {
            $this->warn("DRY RUN MODE - No files will be deleted");
        }

        $versionService = app(VersionService::class);
        $totalDeleted = 0;
        $totalFilesDeleted = 0;

        if ($documentId) {
            // Clean up specific document
            $document = Document::find($documentId);
            if (!$document) {
                $this->error("Document {$documentId} not found.");
                return 1;
            }
            
            $deleted = $this->cleanupDocument($document, $keepCount, $versionService, $dryRun);
            $totalDeleted += $deleted['versions'];
            $totalFilesDeleted += $deleted['files'];
        } else {
            // Clean up all documents
            $documents = Document::has('versions')->get();
            $this->info("Found {$documents->count()} documents with versions to process...");
            
            $bar = $this->output->createProgressBar($documents->count());
            $bar->start();

            foreach ($documents as $document) {
                $deleted = $this->cleanupDocument($document, $keepCount, $versionService, $dryRun);
                $totalDeleted += $deleted['versions'];
                $totalFilesDeleted += $deleted['files'];
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
        }

        $this->info("Cleanup complete!");
        $this->info("Total versions deleted: {$totalDeleted}");
        $this->info("Total files deleted: {$totalFilesDeleted}");

        return 0;
    }

    protected function cleanupDocument(Document $document, int $keepCount, VersionService $versionService, bool $dryRun): array
    {
        $versions = $document->versions()
            ->orderBy('version_number', 'desc')
            ->get();

        if ($versions->count() <= $keepCount) {
            return ['versions' => 0, 'files' => 0];
        }

        $versionsToDelete = $versions->skip($keepCount);
        $deletedVersions = 0;
        $deletedFiles = 0;

        foreach ($versionsToDelete as $version) {
            if ($dryRun) {
                $this->line("  [DRY RUN] Would delete version {$version->version_number} of document {$document->id}");
                if ($version->file_path) {
                    $this->line("    - File: {$version->file_path}");
                    $deletedFiles++;
                }
                if ($version->thumbnail_path) {
                    $this->line("    - Thumbnail: {$version->thumbnail_path}");
                    $deletedFiles++;
                }
            } else {
                // Delete files
                if ($version->file_path && \Storage::disk('public')->exists($version->file_path)) {
                    \Storage::disk('public')->delete($version->file_path);
                    $deletedFiles++;
                }
                if ($version->thumbnail_path && \Storage::disk('public')->exists($version->thumbnail_path)) {
                    \Storage::disk('public')->delete($version->thumbnail_path);
                    $deletedFiles++;
                }
                $version->delete();
            }
            $deletedVersions++;
        }

        return ['versions' => $deletedVersions, 'files' => $deletedFiles];
    }
}
