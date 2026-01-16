<?php

namespace App\Console\Commands;

use App\Models\Document;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupPermanentlyDeletedDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:cleanup-permanent-deletes {--days=90 : Number of days after soft delete to permanently delete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete documents that were soft-deleted more than specified days ago (default: 90 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Cleaning up documents soft-deleted before {$cutoffDate->toDateString()}...");

        // Get documents that were soft-deleted before the cutoff date
        $deletedDocuments = Document::onlyTrashed()
            ->where('deleted_at', '<', $cutoffDate)
            ->get();

        $count = 0;
        $errors = 0;

        foreach ($deletedDocuments as $document) {
            try {
                // Delete file from storage
                if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                    Storage::disk('public')->delete($document->file_path);
                }

                // Delete thumbnail from storage
                if ($document->thumbnail_path && Storage::disk('public')->exists($document->thumbnail_path)) {
                    Storage::disk('public')->delete($document->thumbnail_path);
                }

                // Permanently delete the record
                $document->forceDelete();
                $count++;

            } catch (\Exception $e) {
                $errors++;
                $this->error("Failed to delete document ID {$document->id}: {$e->getMessage()}");
                \Log::error("Failed to permanently delete document {$document->id}", [
                    'document_id' => $document->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Successfully permanently deleted {$count} document(s).");
        if ($errors > 0) {
            $this->warn("Failed to delete {$errors} document(s). Check logs for details.");
        }

        return Command::SUCCESS;
    }
}
