<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupFailedUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:cleanup-failed-uploads {--hours=24 : Number of hours to keep failed uploads}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up temporary files from failed uploads older than specified hours (default: 24 hours)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');
        $cutoffTime = Carbon::now()->subHours($hours);

        $this->info("Cleaning up temporary upload files older than {$hours} hours...");

        // Clean up temp directory
        $tempPath = 'temp';
        $deletedCount = 0;
        $errors = 0;

        if (Storage::disk('local')->exists($tempPath)) {
            $files = Storage::disk('local')->allFiles($tempPath);

            foreach ($files as $file) {
                try {
                    $lastModified = Carbon::createFromTimestamp(
                        Storage::disk('local')->lastModified($file)
                    );

                    if ($lastModified->lt($cutoffTime)) {
                        Storage::disk('local')->delete($file);
                        $deletedCount++;
                    }
                } catch (\Exception $e) {
                    $errors++;
                    $this->error("Failed to delete file {$file}: {$e->getMessage()}");
                    \Log::error("Failed to delete temp file {$file}", [
                        'file' => $file,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->info("Successfully deleted {$deletedCount} temporary file(s).");
        if ($errors > 0) {
            $this->warn("Failed to delete {$errors} file(s). Check logs for details.");
        }

        return Command::SUCCESS;
    }
}
