<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\Forum\ForumReputationService;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Leaderboard extends Component
{
    use WithPagination;

    public $sortBy = 'reputation'; // 'reputation', 'threads', 'posts'
    public $timeframe = 'all'; // 'all', 'week', 'month', 'year'

    protected $paginationTheme = 'tailwind';

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function updatingTimeframe()
    {
        $this->resetPage();
    }

    public function render()
    {
        $reputationService = app(ForumReputationService::class);
        
        $query = User::query();
        
        // Sort by different criteria
        switch ($this->sortBy) {
            case 'threads':
                $dateFilter = null;
                if ($this->timeframe !== 'all') {
                    $dateFilter = match($this->timeframe) {
                        'week' => now()->subWeek(),
                        'month' => now()->subMonth(),
                        'year' => now()->subYear(),
                        default => null,
                    };
                }
                
                if ($dateFilter) {
                    $query->selectRaw('users.*, (SELECT COUNT(*) FROM forum_threads WHERE forum_threads.author_id = users.id AND forum_threads.created_at >= ?) as forum_threads_count', [$dateFilter])
                          ->orderBy('forum_threads_count', 'desc');
                } else {
                    $query->selectRaw('users.*, (SELECT COUNT(*) FROM forum_threads WHERE forum_threads.author_id = users.id) as forum_threads_count')
                          ->orderBy('forum_threads_count', 'desc');
                }
                break;
                
            case 'posts':
                $dateFilter = null;
                if ($this->timeframe !== 'all') {
                    $dateFilter = match($this->timeframe) {
                        'week' => now()->subWeek(),
                        'month' => now()->subMonth(),
                        'year' => now()->subYear(),
                        default => null,
                    };
                }
                
                if ($dateFilter) {
                    $query->selectRaw('users.*, (SELECT COUNT(*) FROM forum_posts WHERE forum_posts.author_id = users.id AND forum_posts.created_at >= ?) as forum_posts_count', [$dateFilter])
                          ->orderBy('forum_posts_count', 'desc');
                } else {
                    $query->selectRaw('users.*, (SELECT COUNT(*) FROM forum_posts WHERE forum_posts.author_id = users.id) as forum_posts_count')
                          ->orderBy('forum_posts_count', 'desc');
                }
                break;
                
            default: // reputation
                $query->where('forum_reputation_points', '>', 0)
                      ->orderBy('forum_reputation_points', 'desc');
        }
        
        $users = $query->paginate(20);
        
        // Add reputation level and stats to each user
        $users->getCollection()->transform(function ($user) use ($reputationService) {
            $user->reputation_level = $reputationService->getReputationLevel($user->forum_reputation_points ?? 0);
            $user->reputation_level_color = $reputationService->getReputationLevelColor($user->reputation_level);
            
            // Get thread count if not already set
            if (!isset($user->forum_threads_count)) {
                $user->forum_threads_count = DB::table('forum_threads')
                    ->where('author_id', $user->id)
                    ->count();
            }
            
            // Get post count if not already set
            if (!isset($user->forum_posts_count)) {
                $user->forum_posts_count = DB::table('forum_posts')
                    ->where('author_id', $user->id)
                    ->count();
            }
            
            // Get badge count
            $user->badge_count = DB::table('forum_user_badges')
                ->where('user_id', $user->id)
                ->join('forum_badges', 'forum_user_badges.badge_id', '=', 'forum_badges.id')
                ->where('forum_badges.is_active', true)
                ->count();
            
            return $user;
        });
        
        return view('livewire.forum.leaderboard', [
            'users' => $users,
        ])->layout('layouts.app');
    }
}
