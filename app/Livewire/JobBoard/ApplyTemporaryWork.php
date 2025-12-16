<?php

namespace App\Livewire\JobBoard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\JobPost;
use App\Models\TemporaryWorkBooking;
use App\Services\JobBoard\JobNotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class ApplyTemporaryWork extends Component
{
    public $jobId;
    public $job;
    public $message = '';

    public function mount($id)
    {
        // Block admins from applying
        if (Auth::user()->hasRole('super_admin')) {
            abort(403, 'Admins cannot apply for jobs. Use the admin panel to manage jobs.');
        }

        $this->jobId = $id;
        $this->job = JobPost::with(['user', 'yacht'])->findOrFail($id);
        
        // Verify it's a temporary work job
        if ($this->job->job_type !== 'temporary') {
            return redirect()->route('job-board.apply', $this->jobId);
        }

        // Check if already booked
        $existing = TemporaryWorkBooking::where('user_id', Auth::id())
            ->where('job_post_id', $this->jobId)
            ->first();

        if ($existing) {
            session()->flash('info', 'You have already applied for this temporary work.');
            return redirect()->route('job-board.detail', $this->jobId);
        }
    }

    public function submitApplication()
    {
        $this->validate([
            'message' => 'nullable|string|max:500',
        ]);

        DB::transaction(function() {
            // Calculate payment
            $dayRate = $this->job->day_rate_min ?? 150;
            $hours = $this->job->total_hours ?? 10;
            $hourlyRate = $dayRate / 10; // Standard 10 hour day
            $totalPayment = $hours * $hourlyRate;

            // Check if booking already exists
            $existingBooking = TemporaryWorkBooking::where('user_id', Auth::id())
                ->where('job_post_id', $this->jobId)
                ->first();
            
            if ($existingBooking) {
                session()->flash('info', 'You have already applied for this temporary work.');
                return redirect()->route('job-board.detail', $this->jobId);
            }

            // Create booking
            $booking = TemporaryWorkBooking::create([
                'job_post_id' => $this->jobId,
                'user_id' => Auth::id(),
                'booked_by_user_id' => $this->job->user_id,
                'status' => 'pending', // Pending captain approval
                'work_date' => $this->job->work_start_date ?? now()->addDay(),
                'start_time' => $this->job->work_start_time ?? '08:00:00',
                'end_time' => $this->job->work_end_time ?? '18:00:00',
                'total_hours' => $hours,
                'work_description' => $this->job->about_position ?? '',
                'location' => $this->job->location ?? '',
                'berth_details' => $this->job->berth_details ?? '',
                'day_rate' => $dayRate,
                'hourly_rate' => $hourlyRate,
                'total_payment' => $totalPayment,
                'payment_currency' => $this->job->salary_currency ?? 'EUR',
                'payment_method' => $this->job->payment_method ?? 'cash',
                'payment_timing' => $this->job->payment_timing ?? 'End of day',
                'contact_name' => $this->job->contact_name ?? $this->job->user->name,
                'contact_phone' => $this->job->contact_phone ?? $this->job->user->phone,
                'whatsapp_available' => $this->job->whatsapp_available ?? false,
                'crew_notes' => $this->message,
            ]);

            // Update job post application count
            $this->job->incrementApplications();

            // Notify captain
            app(JobNotificationService::class)->notifyTemporaryWorkApplication($booking);

            session()->flash('success', 'Application submitted! The captain will review and confirm your booking.');
            return redirect()->route('job-board.bookings');
        });
    }

    public function render()
    {
        return view('livewire.job-board.apply-temporary-work');
    }
}

