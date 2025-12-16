<?php

namespace App\Livewire\JobBoard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class MyApplications extends Component
{
    use WithPagination;

    public $filter = 'all';

    public function mount()
    {
        // Block admins from accessing user applications
        if (Auth::user()->hasRole('super_admin')) {
            return redirect()->route('job-board.admin');
        }
    }

    public function render()
    {
        $query = JobApplication::with('jobPost')
            ->where('user_id', Auth::id());

        if ($this->filter !== 'all') {
            switch ($this->filter) {
                case 'active':
                    $query->whereIn('status', ['submitted', 'viewed', 'reviewed', 'shortlisted', 'interview_requested']);
                    break;
                case 'interviewing':
                    $query->whereIn('status', ['interview_requested', 'interview_scheduled', 'interviewed']);
                    break;
                case 'offers':
                    $query->where('status', 'offer_sent');
                    break;
                case 'closed':
                    $query->whereIn('status', ['declined', 'withdrawn', 'hired']);
                    break;
            }
        }

        $applications = $query->orderBy('submitted_at', 'desc')->paginate(15);

        return view('livewire.job-board.my-applications', [
            'applications' => $applications,
        ]);
    }
}
