<?php

namespace App\Livewire\JobBoard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\TemporaryWorkBooking;
use App\Models\JobRating;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class TemporaryWorkBookingManagement extends Component
{
    use WithPagination;

    public $filter = 'all'; // all, upcoming, completed, pending_payment
    public $viewMode = 'captain'; // captain or crew

    public function mount()
    {
        $user = Auth::user();
        
        // Admins see empty state with redirect to admin panel
        if ($user->hasRole('super_admin')) {
            $this->viewMode = 'admin';
            return;
        }
        
        // Determine if user is captain or crew based on Captain role or vessel verification
        $hasCaptainRole = $user->hasRole('Captain');
        $hasVerifiedVessel = $user->vesselVerification && $user->vesselVerification->isVerified();
        $isCaptain = $hasCaptainRole || $hasVerifiedVessel;
        $this->viewMode = $isCaptain ? 'captain' : 'crew';
    }

    public function render()
    {
        if ($this->viewMode === 'admin') {
            return view('livewire.job-board.temporary-work-booking-management', [
                'bookings' => collect(),
                'viewMode' => 'admin',
            ]);
        } elseif ($this->viewMode === 'captain') {
            return $this->renderCaptainView();
        } else {
            return $this->renderCrewView();
        }
    }

    private function renderCaptainView()
    {
        $query = TemporaryWorkBooking::with(['user', 'jobPost'])
            ->where('booked_by_user_id', Auth::id());

        if ($this->filter !== 'all') {
            switch ($this->filter) {
                case 'upcoming':
                    $query->whereIn('status', ['confirmed', 'pending'])
                        ->whereDate('work_date', '>=', now());
                    break;
                case 'completed':
                    $query->where('status', 'completed');
                    break;
                case 'pending_payment':
                    $query->where('status', 'completed')
                        ->where('payment_received', false);
                    break;
                case 'pending':
                    $query->where('status', 'pending');
                    break;
            }
        }

        $bookings = $query->orderBy('work_date', 'desc')->paginate(20);

        return view('livewire.job-board.temporary-work-booking-management', [
            'bookings' => $bookings,
            'viewMode' => 'captain',
        ]);
    }

    private function renderCrewView()
    {
        $query = TemporaryWorkBooking::with(['jobPost', 'bookedBy'])
            ->where('user_id', Auth::id());

        if ($this->filter !== 'all') {
            switch ($this->filter) {
                case 'upcoming':
                    $query->whereIn('status', ['confirmed', 'pending'])
                        ->whereDate('work_date', '>=', now());
                    break;
                case 'completed':
                    $query->where('status', 'completed');
                    break;
                case 'pending_payment':
                    $query->where('status', 'completed')
                        ->where('payment_received', false);
                    break;
                case 'pending':
                    $query->where('status', 'pending');
                    break;
            }
        }

        $bookings = $query->orderBy('work_date', 'desc')->paginate(20);

        return view('livewire.job-board.temporary-work-booking-management', [
            'bookings' => $bookings,
            'viewMode' => 'crew',
        ]);
    }

    public function markAsPaid($bookingId)
    {
        $booking = TemporaryWorkBooking::findOrFail($bookingId);
        
        // Verify captain owns this booking
        if ($booking->booked_by_user_id !== Auth::id()) {
            abort(403);
        }

        $booking->update([
            'payment_received' => true,
            'payment_received_at' => now(),
        ]);

        session()->flash('success', 'Payment marked as received');
    }

    public function markAsCompleted($bookingId)
    {
        $booking = TemporaryWorkBooking::findOrFail($bookingId);
        
        // Verify ownership
        if ($this->viewMode === 'captain' && $booking->booked_by_user_id !== Auth::id()) {
            abort(403);
        }
        if ($this->viewMode === 'crew' && $booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        session()->flash('success', 'Work marked as completed');
    }

    public function confirmPayment($bookingId)
    {
        $booking = TemporaryWorkBooking::findOrFail($bookingId);
        
        // Verify crew owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->update([
            'payment_received' => true,
            'payment_received_at' => now(),
        ]);

        session()->flash('success', 'Payment confirmed');
    }

    public function confirmBooking($bookingId)
    {
        $booking = TemporaryWorkBooking::findOrFail($bookingId);
        
        // Verify captain owns this booking
        if ($booking->booked_by_user_id !== Auth::id()) {
            abort(403);
        }

        $booking->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        // Notify crew member
        app(JobNotificationService::class)->notifyBookingConfirmed($booking);

        session()->flash('success', 'Booking confirmed! Crew member has been notified.');
    }

    public function cancelBooking($bookingId, $reason = null)
    {
        $booking = TemporaryWorkBooking::findOrFail($bookingId);
        
        // Verify ownership
        $isCaptain = $booking->booked_by_user_id === Auth::id();
        $isCrew = $booking->user_id === Auth::id();

        if (!$isCaptain && !$isCrew) {
            abort(403);
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_by' => $isCaptain ? 'vessel' : 'crew',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
            'hours_before_start' => $booking->work_date ? now()->diffInHours($booking->work_date) : null,
        ]);

        session()->flash('success', 'Booking cancelled');
    }
}

