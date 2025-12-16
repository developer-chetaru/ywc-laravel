<?php

namespace App\Livewire\JobBoard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\JobPost;
use App\Models\TemporaryWorkBooking;

#[Layout('layouts.app')]
class TemporaryWorkMarketplace extends Component
{
    use WithPagination;

    public $filter = 'all'; // all, day_work, short_contract, emergency

    public function render()
    {
        $query = JobPost::published()
            ->where('job_type', 'temporary');

        if ($this->filter !== 'all') {
            $query->where('temporary_work_type', $this->filter);
        }

        $jobs = $query->latest('published_at')->paginate(15);

        return view('livewire.job-board.temporary-work-marketplace', [
            'jobs' => $jobs,
        ]);
    }
}
