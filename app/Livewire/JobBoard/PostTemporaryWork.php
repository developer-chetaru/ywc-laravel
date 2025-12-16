<?php

namespace App\Livewire\JobBoard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\JobPost;
use App\Services\JobBoard\JobNotificationService;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class PostTemporaryWork extends Component
{
    public $temporaryWorkType = 'day_work';
    public $positionTitle = '';
    public $urgencyLevel = 'normal';
    public $workStartDate = '';
    public $workEndDate = '';
    public $workStartTime = '';
    public $workEndTime = '';
    public $dayRate = '';
    public $paymentMethod = 'cash';
    public $location = '';
    public $berthDetails = '';
    public $workDescription = '';
    public $requirements = [];
    public $contactName = '';
    public $contactPhone = '';
    public $whatsappAvailable = false;

    public function mount()
    {
        // Block admins from posting jobs as regular users
        if (Auth::user()->hasRole('super_admin')) {
            abort(403, 'Admins cannot post jobs as regular users. Use the admin panel to manage jobs.');
        }

        // Verify user is captain (has Captain role) or has verified vessel verification
        $user = Auth::user();
        $hasCaptainRole = $user->hasRole('Captain');
        $hasVerifiedVessel = $user->vesselVerification && $user->vesselVerification->isVerified();
        
        if (!$hasCaptainRole && !$hasVerifiedVessel) {
            return redirect()->route('job-board.verify')
                ->with('error', 'You must be a captain or have verified vessel verification to post temporary work.');
        }
    }

    public function save()
    {
        $validated = $this->validate([
            'temporaryWorkType' => 'required|in:day_work,short_contract,medium_contract,emergency_cover',
            'positionTitle' => 'required|string|max:255',
            'workStartDate' => 'required|date',
            'workEndDate' => 'required|date|after_or_equal:workStartDate',
            'workStartTime' => 'required',
            'workEndTime' => 'required',
            'dayRate' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'workDescription' => 'required|string|max:500',
        ]);

        $jobPost = JobPost::create([
            'user_id' => Auth::id(),
            'job_type' => 'temporary',
            'temporary_work_type' => $this->temporaryWorkType,
            'position_title' => $this->positionTitle,
            'urgency_level' => $this->urgencyLevel,
            'work_start_date' => $this->workStartDate,
            'work_end_date' => $this->workEndDate,
            'work_start_time' => $this->workStartTime ? ($this->workStartDate . ' ' . $this->workStartTime . ':00') : null,
            'work_end_time' => $this->workEndTime ? ($this->workEndDate . ' ' . $this->workEndTime . ':00') : null,
            'total_hours' => $this->calculateHours(),
            'day_rate_min' => $this->dayRate,
            'day_rate_max' => $this->dayRate,
            'location' => $this->location,
            'berth_details' => $this->berthDetails,
            'about_position' => $this->workDescription,
            'contact_name' => $this->contactName,
            'contact_phone' => $this->contactPhone,
            'whatsapp_available' => $this->whatsappAvailable,
            'payment_method' => $this->paymentMethod,
            'status' => 'active',
            'notify_matching_crew' => true,
            'published_at' => now(),
        ]);

        // Notify matching crew
        app(JobNotificationService::class)->notifyUrgentTemporaryWork($jobPost);

        session()->flash('success', 'Temporary work posted successfully!');
        return redirect()->route('job-board.detail', $jobPost->id);
    }

    public function calculateHours()
    {
        if ($this->workStartTime && $this->workEndTime) {
            $start = \Carbon\Carbon::parse($this->workStartTime);
            $end = \Carbon\Carbon::parse($this->workEndTime);
            return $start->diffInHours($end);
        }
        return 10; // Default
    }

    public function render()
    {
        return view('livewire.job-board.post-temporary-work');
    }
}
