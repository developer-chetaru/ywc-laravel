# Complete Implementation Guide - All Features

This guide provides step-by-step instructions and code for implementing all remaining features.

## 📋 Implementation Checklist

- [x] Landing Page & Waitlist
- [ ] Forum Main Thread Subscription
- [ ] Industry Review Sections (Contractors, Brokers, Restaurants)
- [ ] Captain Dashboard
- [ ] Waitlist Admin Interface

---

## 1. Forum Main Thread Subscription

### Step 1: Create Forum Subscription Service

**File:** `app/Services/Forum/ForumSubscriptionService.php`

```php
<?php

namespace App\Services\Forum;

use App\Models\User;
use TeamTeaTime\Forum\Models\Thread;
use Illuminate\Support\Facades\DB;

class ForumSubscriptionService
{
    /**
     * Subscribe a user to a thread (mark as read to enable notifications)
     */
    public function subscribeUserToThread(User $user, Thread $thread): void
    {
        // Check if already subscribed
        $exists = DB::table('forum_threads_read')
            ->where('thread_id', $thread->id)
            ->where('user_id', $user->id)
            ->exists();

        if (!$exists) {
            DB::table('forum_threads_read')->insert([
                'thread_id' => $thread->id,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Subscribe all users to main community thread
     */
    public function subscribeAllUsersToMainThread(): int
    {
        $mainThreadId = config('forum.main_thread_id');
        
        if (!$mainThreadId) {
            throw new \Exception('Main thread ID not configured. Run MainCommunityThreadSeeder first.');
        }

        $thread = Thread::find($mainThreadId);
        if (!$thread) {
            throw new \Exception('Main thread not found.');
        }

        $users = User::where('is_active', true)->get();
        $subscribed = 0;

        foreach ($users as $user) {
            try {
                $this->subscribeUserToThread($user, $thread);
                $subscribed++;
            } catch (\Exception $e) {
                // Log error but continue
                \Log::warning("Failed to subscribe user {$user->id}: " . $e->getMessage());
            }
        }

        return $subscribed;
    }

    /**
     * Auto-subscribe new user to main thread
     */
    public function subscribeNewUser(User $user): void
    {
        $mainThreadId = config('forum.main_thread_id');
        
        if ($mainThreadId) {
            $thread = Thread::find($mainThreadId);
            if ($thread) {
                $this->subscribeUserToThread($user, $thread);
            }
        }
    }
}
```

### Step 2: Update MainCommunityThreadSeeder

**File:** `database/seeders/MainCommunityThreadSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MainCommunityThreadSeeder extends Seeder
{
    public function run(): void
    {
        // Find or create "Community Announcements" category
        $category = Category::firstOrCreate(
            ['name' => 'Community Announcements'],
            [
                'description' => 'Official announcements and updates for all crew members',
                'color' => '#0053FF',
                'is_private' => false,
                'enable_threads' => true,
                'thread_count' => 0,
                'post_count' => 0,
            ]
        );

        // Create main community thread
        $mainThread = Thread::firstOrCreate(
            ['title' => 'Community Announcements - Main Thread'],
            [
                'category_id' => $category->id,
                'author_id' => User::whereHas('roles', function($q) {
                    $q->where('name', 'super_admin');
                })->first()->id ?? 1,
                'first_post_id' => null,
                'last_post_id' => null,
                'is_locked' => false,
                'is_pinned' => true,
                'is_private' => false,
                'reply_count' => 0,
                'view_count' => 0,
            ]
        );

        // Store main thread ID in config
        $configPath = config_path('forum.php');
        if (!file_exists($configPath)) {
            // Create config file if it doesn't exist
            $this->createForumConfig($mainThread->id);
        } else {
            // Update existing config
            $this->updateForumConfig($mainThread->id);
        }

        $this->command->info("Main community thread created with ID: {$mainThread->id}");
        $this->command->info("Run 'php artisan forum:subscribe-main-thread' to subscribe all users.");
    }

    private function createForumConfig($threadId): void
    {
        $config = "<?php\n\nreturn [\n    'main_thread_id' => {$threadId},\n];\n";
        file_put_contents(config_path('forum.php'), $config);
    }

    private function updateForumConfig($threadId): void
    {
        // Read existing config
        $config = include config_path('forum.php');
        $config['main_thread_id'] = $threadId;
        
        // Write back
        $content = "<?php\n\nreturn " . var_export($config, true) . ";\n";
        file_put_contents(config_path('forum.php'), $content);
    }
}
```

### Step 3: Update SubscribeUsersToMainThread Command

**File:** `app/Console/Commands/SubscribeUsersToMainThread.php`

```php
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
        $this->info('Subscribing all users to main community thread...');
        
        try {
            $count = $service->subscribeAllUsersToMainThread();
            $this->info("Successfully subscribed {$count} users to main thread.");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
```

### Step 4: Auto-subscribe on User Registration

**File:** `app/Actions/Fortify/CreateNewUser.php`

Add after user creation (around line 44):

```php
use App\Services\Forum\ForumSubscriptionService;

// After $user->assignRole($input['role']);
// Add:
if (config('forum.main_thread_id')) {
    $subscriptionService = app(ForumSubscriptionService::class);
    $subscriptionService->subscribeNewUser($user);
}
```

### Step 5: Run Commands

```bash
# Seed main thread
php artisan db:seed --class=MainCommunityThreadSeeder

# Subscribe existing users
php artisan forum:subscribe-main-thread
```

---

## 2. Industry Review Sections (Contractors, Brokers, Restaurants)

### Step 1: Create Models and Migrations

```bash
php artisan make:model Contractor -m
php artisan make:model ContractorReview -m
php artisan make:model Broker -m
php artisan make:model BrokerReview -m
php artisan make:model Restaurant -m
php artisan make:model RestaurantReview -m
```

### Step 2: Contractor Migration

**File:** `database/migrations/xxxx_create_contractors_table.php`

```php
Schema::create('contractors', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('company_name')->nullable();
    $table->string('service_type'); // maintenance, electrical, plumbing, etc.
    $table->string('location')->nullable();
    $table->string('contact_email')->nullable();
    $table->string('contact_phone')->nullable();
    $table->text('description')->nullable();
    $table->string('website')->nullable();
    $table->decimal('average_rating', 3, 2)->default(0);
    $table->integer('total_reviews')->default(0);
    $table->timestamps();
    $table->softDeletes();
});
```

### Step 3: ContractorReview Migration

**File:** `database/migrations/xxxx_create_contractor_reviews_table.php`

```php
Schema::create('contractor_reviews', function (Blueprint $table) {
    $table->id();
    $table->foreignId('contractor_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('title');
    $table->text('review');
    $table->decimal('overall_rating', 3, 2);
    $table->decimal('quality_rating', 3, 2)->nullable();
    $table->decimal('timeliness_rating', 3, 2)->nullable();
    $table->decimal('communication_rating', 3, 2)->nullable();
    $table->decimal('pricing_rating', 3, 2)->nullable();
    $table->decimal('professionalism_rating', 3, 2)->nullable();
    $table->boolean('would_recommend')->default(true);
    $table->boolean('is_anonymous')->default(false);
    $table->boolean('is_verified')->default(false);
    $table->boolean('is_approved')->default(false);
    $table->date('service_date')->nullable();
    $table->integer('helpful_count')->default(0);
    $table->integer('not_helpful_count')->default(0);
    $table->timestamps();
    $table->softDeletes();
});
```

### Step 4: Update IndustryReviewIndex Component

Add new tabs to `app/Livewire/IndustryReview/IndustryReviewIndex.php`:

```php
public $activeTab = 'yachts'; // Add: 'contractors', 'brokers', 'restaurants'

public function setTab($tab)
{
    $this->activeTab = $tab;
    if ($tab === 'yachts') {
        $this->loadYachts();
    } elseif ($tab === 'marinas') {
        $this->loadMarinas();
    } elseif ($tab === 'contractors') {
        $this->loadContractors();
    } elseif ($tab === 'brokers') {
        $this->loadBrokers();
    } elseif ($tab === 'restaurants') {
        $this->loadRestaurants();
    }
}
```

### Step 5: Create Livewire Components

Create similar components to `YachtReviewIndex` and `MarinaReviewIndex`:
- `app/Livewire/IndustryReview/ContractorReviewIndex.php`
- `app/Livewire/IndustryReview/BrokerReviewIndex.php`
- `app/Livewire/IndustryReview/RestaurantReviewIndex.php`

---

## 3. Captain Dashboard

### Step 1: Create Captain Dashboard Component

**File:** `app/Livewire/CaptainDashboard.php`

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Document;
use App\Models\WorkLog;
use App\Models\Yacht;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CaptainDashboard extends Component
{
    use WithPagination;

    public $selectedYacht = null;
    public $search = '';
    public $department = '';
    public $certificateFilter = 'all'; // all, expiring, expired
    public $yachts = [];

    public function mount()
    {
        $user = Auth::user();
        
        // Get yachts where user is captain
        if ($user->hasRole('captain')) {
            $this->yachts = Yacht::where('name', $user->current_yacht)->get();
            if ($this->yachts->isNotEmpty()) {
                $this->selectedYacht = $this->yachts->first()->name;
            }
        }
    }

    public function getCrewMembersProperty()
    {
        $query = User::where('current_yacht', $this->selectedYacht)
            ->where('is_active', true);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%");
            });
        }

        return $query->paginate(20);
    }

    public function getExpiringCertificatesProperty()
    {
        $days = match($this->certificateFilter) {
            'expiring_30' => 30,
            'expiring_60' => 60,
            'expiring_90' => 90,
            default => null,
        };

        $query = Document::whereHas('user', function($q) {
            $q->where('current_yacht', $this->selectedYacht);
        })
        ->whereNotNull('expiry_date');

        if ($days) {
            $query->whereBetween('expiry_date', [
                now(),
                now()->addDays($days)
            ]);
        }

        return $query->orderBy('expiry_date')->get();
    }

    public function getWorkHoursSummaryProperty()
    {
        $crewIds = User::where('current_yacht', $this->selectedYacht)
            ->pluck('id');

        return WorkLog::whereIn('user_id', $crewIds)
            ->whereBetween('work_date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->selectRaw('user_id, SUM(total_hours) as total_hours, SUM(overtime_hours) as total_overtime')
            ->groupBy('user_id')
            ->get();
    }

    public function render()
    {
        return view('livewire.captain-dashboard', [
            'crewMembers' => $this->crewMembers,
            'expiringCertificates' => $this->expiringCertificates,
            'workHoursSummary' => $this->workHoursSummary,
        ]);
    }
}
```

### Step 2: Create View

**File:** `resources/views/livewire/captain-dashboard.blade.php`

Create a comprehensive dashboard with:
- Crew list with filters
- Certificate expiry calendar
- Work hours compliance table
- Export buttons

### Step 3: Add Route

**File:** `routes/web.php`

```php
Route::get('/captain/dashboard', CaptainDashboard::class)
    ->name('captain.dashboard')
    ->middleware(['auth', 'verified', 'subscribed', 'role:captain']);
```

---

## 4. Waitlist Admin Interface

### Step 1: Create Waitlist Admin Component

**File:** `app/Livewire/WaitlistAdmin.php`

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Waitlist;
use Illuminate\Support\Facades\Mail;

class WaitlistAdmin extends Component
{
    use WithPagination;

    public $statusFilter = 'all';
    public $search = '';

    public function approve($waitlistId)
    {
        $waitlist = Waitlist::findOrFail($waitlistId);
        $waitlist->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        // Send invitation email
        // Mail::to($waitlist->email)->send(new WaitlistInvitationMail($waitlist));

        session()->flash('message', 'Waitlist entry approved and invitation sent.');
    }

    public function invite($waitlistId)
    {
        $waitlist = Waitlist::findOrFail($waitlistId);
        $waitlist->update([
            'status' => 'invited',
            'invited_at' => now(),
        ]);

        session()->flash('message', 'Invitation sent.');
    }

    public function render()
    {
        $query = Waitlist::query();

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('email', 'like', "%{$this->search}%")
                  ->orWhere('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.waitlist-admin', [
            'waitlistEntries' => $query->orderBy('created_at', 'desc')->paginate(20),
        ]);
    }
}
```

### Step 2: Add Route

```php
Route::get('/admin/waitlist', WaitlistAdmin::class)
    ->name('admin.waitlist')
    ->middleware(['auth', 'role:super_admin']);
```

---

## 🚀 Quick Implementation Commands

```bash
# 1. Run main thread seeder
php artisan db:seed --class=MainCommunityThreadSeeder

# 2. Subscribe all users
php artisan forum:subscribe-main-thread

# 3. Run migrations for new review sections
php artisan migrate

# 4. Clear cache
php artisan config:clear
php artisan cache:clear
```

---

## 📝 Notes

1. **Forum Subscription**: The forum package uses `forum_threads_read` table to track subscriptions. When a user is in this table for a thread, they receive notifications.

2. **Review Sections**: Follow the same pattern as Yacht and Marina reviews. Create models, migrations, Livewire components, and views.

3. **Captain Dashboard**: Only accessible to users with 'captain' role. Filter crew by `current_yacht` field.

4. **Waitlist Admin**: Super admin only. Can approve, invite, or manage waitlist entries.

---

**Last Updated:** November 27, 2025

