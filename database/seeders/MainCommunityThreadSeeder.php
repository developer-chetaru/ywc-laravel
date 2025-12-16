<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;
use App\Models\User;
use Illuminate\Support\Facades\File;

class MainCommunityThreadSeeder extends Seeder
{
    public function run(): void
    {
        // Find or create "Community Announcements" category
        $category = Category::firstOrCreate(
            ['title' => 'Community Announcements'],
            [
                'description' => 'Official announcements and updates for all crew members',
                'color_light_mode' => '#0053FF',
                'is_private' => false,
                'accepts_threads' => true,
                'thread_count' => 0,
                'post_count' => 0,
            ]
        );

        // Get super admin or first user as author
        $author = User::whereHas('roles', function($q) {
            $q->where('name', 'super_admin');
        })->first() ?? User::first();

        if (!$author) {
            $this->command->error('No users found. Please create a user first.');
            return;
        }

        // Create main community thread
        $mainThread = Thread::firstOrCreate(
            ['title' => 'Community Announcements - Main Thread'],
            [
                'category_id' => $category->id,
                'author_id' => $author->id,
                'pinned' => true,
                'locked' => false,
            ]
        );

        // Store main thread ID in config
        $this->updateForumConfig($mainThread->id);

        $this->command->info("✅ Main community thread created with ID: {$mainThread->id}");
        $this->command->info("📝 Run 'php artisan forum:subscribe-main-thread' to subscribe all users.");
    }

    private function updateForumConfig($threadId): void
    {
        $configPath = config_path('forum.php');
        
        if (file_exists($configPath)) {
            $config = include $configPath;
        } else {
            $config = [];
        }
        
        $config['main_thread_id'] = $threadId;
        
        // Write config file
        $content = "<?php\n\nreturn " . var_export($config, true) . ";\n";
        file_put_contents($configPath, $content);
    }
}
