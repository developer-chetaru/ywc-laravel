<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Post;
use Illuminate\Support\Facades\Auth;
use App\Services\Forum\ModerationService;

class ReportContent extends Component
{
    public $reportableType; // 'thread' or 'post'
    public $reportableId;
    public bool $showReportModal = false;
    public string $reason = '';
    public string $explanation = '';

    protected ModerationService $moderationService;

    public function boot(ModerationService $moderationService)
    {
        $this->moderationService = $moderationService;
    }

    public function mount($reportableType, $reportableId)
    {
        $this->reportableType = $reportableType;
        $this->reportableId = $reportableId;
    }

    public function openReportModal()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to report content.');
            return;
        }

        $this->showReportModal = true;
        $this->reset(['reason', 'explanation']);
    }

    public function closeReportModal()
    {
        $this->showReportModal = false;
        $this->reset(['reason', 'explanation']);
    }

    public function submitReport()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to report content.');
            return;
        }

        $this->validate([
            'reason' => 'required|in:spam,harassment,off_topic,inappropriate,libel,privacy',
            'explanation' => 'required|min:20|max:1000',
        ], [
            'reason.required' => 'Please select a reason for reporting.',
            'explanation.required' => 'Please provide an explanation (minimum 20 characters).',
            'explanation.min' => 'Explanation must be at least 20 characters.',
            'explanation.max' => 'Explanation must not exceed 1000 characters.',
        ]);

        try {
            $this->moderationService->createReport(
                Auth::user(),
                $this->reportableType,
                $this->reportableId,
                $this->reason,
                $this->explanation
            );

            session()->flash('success', 'Thank you for your report. Our moderators will review it shortly.');
            $this->closeReportModal();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.forum.report-content');
    }
}
