<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Forum\ForumSubscriptionService;

class SubscribeUsersToMainThread extends Command
{
    protected $signature = 'forum:subscribe-main-thread';
    protected $description = 'Subscribe all active users to the main community thread';

    public function handle(ForumSubscriptionService $service)
    {
        $this->info('🔄 Subscribing all users to main community thread...');
        
        try {
            $count = $service->subscribeAllUsersToMainThread();
            $this->info("✅ Successfully subscribed {$count} users to main thread.");
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
