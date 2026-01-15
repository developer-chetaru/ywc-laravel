<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Document;
use App\Models\DocumentExpiryReminder;
use App\Mail\DocumentExpiryReminderMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ProcessDocumentExpiryReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:process-expiry-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process document expiry reminders and send emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing document expiry reminders...');

        $today = Carbon::today();
        $processed = 0;

        // Process each reminder type with milestone-based windows
        // Use ±3 days tolerance around each milestone to catch documents as they approach expiry
        
        // 6 months reminder: documents expiring approximately 6 months from now (±7 days)
        $sixMonthsTarget = $today->copy()->addMonths(6);
        $processed += $this->processReminderTypeMilestone('6_months', $sixMonthsTarget, 7);

        // 3 months reminder: documents expiring approximately 3 months from now (±7 days)
        $threeMonthsTarget = $today->copy()->addMonths(3);
        $processed += $this->processReminderTypeMilestone('3_months', $threeMonthsTarget, 7);

        // 1 month reminder: documents expiring approximately 1 month from now (±3 days)
        $oneMonthTarget = $today->copy()->addMonth();
        $processed += $this->processReminderTypeMilestone('1_month', $oneMonthTarget, 3);

        // 2 weeks reminder: documents expiring approximately 2 weeks from now (±2 days)
        $twoWeeksTarget = $today->copy()->addWeeks(2);
        $processed += $this->processReminderTypeMilestone('2_weeks', $twoWeeksTarget, 2);

        // 1 week reminder: documents expiring approximately 1 week from now (±2 days)
        $oneWeekTarget = $today->copy()->addWeek();
        $processed += $this->processReminderTypeMilestone('1_week', $oneWeekTarget, 2);

        // Expired reminder: documents that expired today or yesterday (within last 2 days)
        $expiredStart = $today->copy()->subDay()->startOfDay();
        $expiredEnd = $today->copy()->endOfDay();
        $processed += $this->processReminderTypeRange('expired', $expiredStart, $expiredEnd);

        // Process post-expiry weekly reminders
        $processed += $this->processPostExpiryReminders();

        $this->info("Processed {$processed} reminder(s).");
        return 0;
    }

    /**
     * Process reminders for a specific type within a date range
     */
    private function processReminderTypeRange(string $reminderType, Carbon $startDate, Carbon $endDate): int
    {
        $count = 0;
        $skipped = 0;

        // Get documents expiring within the date range
        $documents = Document::whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$startDate, $endDate])
            ->whereHas('user') // Ensure user exists
            ->with('user', 'documentType')
            ->get();

        $this->line("Checking {$reminderType}: {$documents->count()} documents in range ({$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')})");

        foreach ($documents as $document) {
            // Check if reminder already sent for this type
            if (DocumentExpiryReminder::wasSentForDocument($document->id, $reminderType)) {
                $skipped++;
                continue;
            }

            // Send reminder email
            try {
                Mail::to($document->user->email)->send(
                    new DocumentExpiryReminderMail($document, $reminderType)
                );

                // Log the reminder
                DocumentExpiryReminder::create([
                    'document_id' => $document->id,
                    'reminder_type' => $reminderType,
                    'sent_at' => now(),
                    'expiry_date' => $document->expiry_date,
                ]);

                $count++;
                $docName = $document->document_name ?? "Document #{$document->id}";
                $daysUntil = now()->diffInDays($document->expiry_date, false);
                $this->line("Sent {$reminderType} reminder for document: {$docName} (expires in {$daysUntil} days)");
            } catch (\Exception $e) {
                $this->error("Failed to send reminder for document {$document->id}: " . $e->getMessage());
            }
        }

        if ($skipped > 0) {
            $this->line("  Skipped {$skipped} document(s) that already have {$reminderType} reminders");
        }

        return $count;
    }

    /**
     * Process reminders for a specific milestone with tolerance window
     */
    private function processReminderTypeMilestone(string $reminderType, Carbon $targetDate, int $toleranceDays): int
    {
        $startDate = $targetDate->copy()->subDays($toleranceDays)->startOfDay();
        $endDate = $targetDate->copy()->addDays($toleranceDays)->endOfDay();
        
        return $this->processReminderTypeRange($reminderType, $startDate, $endDate);
    }

    /**
     * Process weekly reminders for expired documents
     */
    private function processPostExpiryReminders(): int
    {
        $count = 0;
        $today = Carbon::today();

        // Get expired documents
        $expiredDocuments = Document::whereNotNull('expiry_date')
            ->where('expiry_date', '<', $today)
            ->whereHas('user')
            ->with('user', 'documentType')
            ->get();

        foreach ($expiredDocuments as $document) {
            // Check if we sent a reminder this week
            $lastReminder = DocumentExpiryReminder::where('document_id', $document->id)
                ->where('reminder_type', 'post_expiry_weekly')
                ->where('sent_at', '>=', $today->copy()->startOfWeek())
                ->first();

            if ($lastReminder) {
                continue; // Already sent this week
            }

            // Send weekly reminder
            try {
                Mail::to($document->user->email)->send(
                    new DocumentExpiryReminderMail($document, 'post_expiry_weekly')
                );

                DocumentExpiryReminder::create([
                    'document_id' => $document->id,
                    'reminder_type' => 'post_expiry_weekly',
                    'sent_at' => now(),
                    'expiry_date' => $document->expiry_date,
                ]);

                $count++;
                $docName = $document->document_name ?? "Document #{$document->id}";
                $this->line("Sent post-expiry reminder for document: {$docName}");
            } catch (\Exception $e) {
                $this->error("Failed to send post-expiry reminder for document {$document->id}: " . $e->getMessage());
            }
        }

        return $count;
    }
}
