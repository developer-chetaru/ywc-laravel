<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DocumentShare;
use App\Models\ProfileShare;
use App\Models\ShareAuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class CleanupExpiredShares extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shares:cleanup-expired 
                            {--days=90 : Number of days after expiry to permanently delete}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired and old shares from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Cleaning up shares expired more than {$days} days ago (before {$cutoffDate->format('Y-m-d')})");

        // Clean up document shares
        $documentShares = DocumentShare::where('is_active', false)
            ->orWhere(function($q) use ($cutoffDate) {
                $q->whereNotNull('expires_at')
                  ->where('expires_at', '<', $cutoffDate);
            })
            ->where('created_at', '<', $cutoffDate)
            ->get();

        $this->info("Found {$documentShares->count()} expired document shares");

        // Clean up profile shares
        $profileShares = ProfileShare::where('is_active', false)
            ->orWhere(function($q) use ($cutoffDate) {
                $q->whereNotNull('expires_at')
                  ->where('expires_at', '<', $cutoffDate);
            })
            ->where('created_at', '<', $cutoffDate)
            ->get();

        $this->info("Found {$profileShares->count()} expired profile shares");

        if ($dryRun) {
            $this->warn("DRY RUN - No shares will be deleted");
            $this->table(
                ['Type', 'ID', 'User ID', 'Created', 'Expires', 'Status'],
                $documentShares->take(10)->map(function($share) {
                    return [
                        'Document',
                        $share->id,
                        $share->user_id,
                        $share->created_at->format('Y-m-d'),
                        $share->expires_at ? $share->expires_at->format('Y-m-d') : 'Never',
                        $share->is_active ? 'Active' : 'Inactive',
                    ];
                })->toArray()
            );
            return 0;
        }

        $deletedDocuments = 0;
        $deletedProfiles = 0;

        // Delete document shares
        foreach ($documentShares as $share) {
            // Delete associated QR codes if any
            if ($share->qr_code_path && Storage::disk('public')->exists($share->qr_code_path)) {
                Storage::disk('public')->delete($share->qr_code_path);
            }
            $share->delete();
            $deletedDocuments++;
        }

        // Delete profile shares
        foreach ($profileShares as $share) {
            // Delete associated QR codes if any
            if ($share->qr_code_path && Storage::disk('public')->exists($share->qr_code_path)) {
                Storage::disk('public')->delete($share->qr_code_path);
            }
            $share->delete();
            $deletedProfiles++;
        }

        // Clean up old audit logs (older than 1 year)
        $oldAuditLogs = ShareAuditLog::where('created_at', '<', Carbon::now()->subYear())->count();
        ShareAuditLog::where('created_at', '<', Carbon::now()->subYear())->delete();

        $this->info("Deleted {$deletedDocuments} document shares");
        $this->info("Deleted {$deletedProfiles} profile shares");
        $this->info("Deleted {$oldAuditLogs} old audit log entries");

        return 0;
    }
}
