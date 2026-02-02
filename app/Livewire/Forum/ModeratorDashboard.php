<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Services\Forum\ModerationService;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class ModeratorDashboard extends Component
{
    use WithPagination;

    public string $filter = 'pending'; // 'pending', 'resolved', 'dismissed', 'all'
    public string $selectedReportId = '';
    public string $resolutionAction = 'resolved'; // 'resolved' or 'dismissed'
    public string $moderatorNotes = '';
    public string $quickAction = ''; // 'lock', 'delete', 'warn', etc.
    public int $quickActionTargetId = 0;
    public string $quickActionTargetType = '';

    protected ModerationService $moderationService;

    public function boot(ModerationService $moderationService)
    {
        $this->moderationService = $moderationService;
    }

    public function mount()
    {
        // Check if user is moderator or admin
        if (!Auth::user()->hasRole('super_admin')) {
            abort(403, 'Only moderators and administrators can access this page.');
        }
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function resolveReport($reportId)
    {
        $this->selectedReportId = $reportId;
        $this->moderatorNotes = '';
    }

    public function submitResolution()
    {
        $this->validate([
            'moderatorNotes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->moderationService->resolveReport(
                Auth::user(),
                $this->selectedReportId,
                $this->resolutionAction,
                $this->moderatorNotes
            );

            session()->flash('success', 'Report resolved successfully.');
            $this->reset(['selectedReportId', 'moderatorNotes', 'resolutionAction']);
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to resolve report: ' . $e->getMessage());
        }
    }

    public function cancelResolution()
    {
        $this->reset(['selectedReportId', 'moderatorNotes', 'resolutionAction']);
    }

    public function performQuickAction($action, $targetType, $targetId)
    {
        $this->quickAction = $action;
        $this->quickActionTargetType = $targetType;
        $this->quickActionTargetId = $targetId;
    }

    public function executeQuickAction()
    {
        try {
            switch ($this->quickAction) {
                case 'lock':
                    if ($this->quickActionTargetType === 'thread') {
                        $thread = \TeamTeaTime\Forum\Models\Thread::find($this->quickActionTargetId);
                        if ($thread) {
                            $this->moderationService->lockThread(Auth::user(), $thread);
                            session()->flash('success', 'Thread locked successfully.');
                        }
                    }
                    break;
                case 'unlock':
                    if ($this->quickActionTargetType === 'thread') {
                        $thread = \TeamTeaTime\Forum\Models\Thread::find($this->quickActionTargetId);
                        if ($thread) {
                            $this->moderationService->unlockThread(Auth::user(), $thread);
                            session()->flash('success', 'Thread unlocked successfully.');
                        }
                    }
                    break;
                case 'delete':
                    if ($this->quickActionTargetType === 'post') {
                        $post = \TeamTeaTime\Forum\Models\Post::find($this->quickActionTargetId);
                        if ($post) {
                            $this->moderationService->deletePost(Auth::user(), $post, 'Deleted via moderator dashboard');
                            session()->flash('success', 'Post deleted successfully.');
                        }
                    } elseif ($this->quickActionTargetType === 'thread') {
                        $thread = \TeamTeaTime\Forum\Models\Thread::find($this->quickActionTargetId);
                        if ($thread) {
                            $this->moderationService->deleteThread(Auth::user(), $thread, 'Deleted via moderator dashboard');
                            session()->flash('success', 'Thread deleted successfully.');
                        }
                    }
                    break;
            }
            
            $this->reset(['quickAction', 'quickActionTargetId', 'quickActionTargetType']);
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to perform action: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = DB::table('forum_reports')
            ->join('users as reporters', 'forum_reports.reporter_id', '=', 'reporters.id')
            ->leftJoin('users as moderators', 'forum_reports.moderator_id', '=', 'moderators.id')
            ->select(
                'forum_reports.*',
                'reporters.first_name as reporter_first_name',
                'reporters.last_name as reporter_last_name',
                'moderators.first_name as moderator_first_name',
                'moderators.last_name as moderator_last_name'
            );

        if ($this->filter !== 'all') {
            $query->where('forum_reports.status', $this->filter);
        }

        $reports = $query->orderBy('forum_reports.created_at', 'desc')
            ->paginate(20);

        $stats = $this->moderationService->getModerationStats();

        return view('livewire.forum.moderator-dashboard', [
            'reports' => $reports,
            'stats' => $stats,
        ]);
    }
}
