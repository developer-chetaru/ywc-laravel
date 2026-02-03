<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Forum\ForumNotificationService;

class SendForumDigestEmails extends Command
{
    protected $signature = 'forum:send-digest {mode=daily : The digest mode (daily or weekly)}';
    protected $description = 'Send digest emails for forum notifications';

    public function handle()
    {
        $mode = $this->argument('mode');
        
        if (!in_array($mode, ['daily', 'weekly'])) {
            $this->error('Mode must be either "daily" or "weekly"');
            return 1;
        }

        $this->info("Sending {$mode} digest emails...");

        $notificationService = app(ForumNotificationService::class);
        $notificationService->sendDigestEmails($mode);

        $this->info("{$mode} digest emails sent successfully!");
        return 0;
    }
}
