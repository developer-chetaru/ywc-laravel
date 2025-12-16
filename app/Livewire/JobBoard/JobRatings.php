<?php

namespace App\Livewire\JobBoard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\JobRating;
use App\Models\TemporaryWorkBooking;
use App\Models\JobPost;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class JobRatings extends Component
{
    public $bookingId = null;
    public $booking = null;
    public $ratingType = 'crew_rates_vessel';
    
    // Rating fields
    public $overallRating = 0;
    public $professionalismRating = 0;
    public $paymentRating = 0;
    public $reviewText = '';
    public $wouldWorkAgain = '';

    public function mount($bookingId = null)
    {
        if ($bookingId) {
            $this->bookingId = $bookingId;
            $this->booking = TemporaryWorkBooking::with(['jobPost', 'user'])->findOrFail($bookingId);
            $this->ratingType = 'crew_rates_vessel';
        }
    }

    public function submitRating()
    {
        $validated = $this->validate([
            'overallRating' => 'required|integer|min:1|max:5',
            'professionalismRating' => 'required|integer|min:1|max:5',
            'reviewText' => 'nullable|string|max:2000',
        ]);

        if ($this->ratingType === 'crew_rates_vessel') {
            JobRating::create([
                'temporary_work_booking_id' => $this->bookingId,
                'rater_user_id' => Auth::id(),
                'rated_yacht_id' => $this->booking->jobPost->yacht_id,
                'rating_type' => 'crew_rates_vessel',
                'professionalism_rating' => $this->professionalismRating,
                'payment_rating' => $this->paymentRating,
                'overall_rating' => $this->overallRating,
                'review_text' => $this->reviewText,
                'would_work_here_again' => $this->wouldWorkAgain,
                'is_verified' => true,
                'is_approved' => true,
            ]);

            $this->booking->update(['crew_rated_vessel' => true]);
        }

        session()->flash('success', 'Rating submitted successfully!');
        return redirect()->route('job-board.index');
    }

    public function render()
    {
        return view('livewire.job-board.job-ratings');
    }
}
