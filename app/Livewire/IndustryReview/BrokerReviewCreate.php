<?php

namespace App\Livewire\IndustryReview;

use App\Models\Broker;
use App\Models\BrokerReview;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class BrokerReviewCreate extends Component
{
    use WithFileUploads;

    public $brokerId;
    public Broker $broker;
    public $editId = null;

    // Form fields
    public $title = '';
    public $review = '';
    public $overall_rating = 5;
    public $job_quality_rating = null;
    public $communication_rating = null;
    public $professionalism_rating = null;
    public $fees_transparency_rating = null;
    public $support_rating = null;
    public $would_use_again = true;
    public $would_recommend = true;
    public $is_anonymous = false;
    public $placement_date = null;
    public $position_placed = '';
    public $yacht_name = '';
    public $placement_timeframe = '';
    public $photos = [];
    public $existingPhotos = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'review' => 'required|string|min:50',
        'overall_rating' => 'required|integer|min:1|max:5',
        'job_quality_rating' => 'nullable|integer|min:1|max:5',
        'communication_rating' => 'nullable|integer|min:1|max:5',
        'professionalism_rating' => 'nullable|integer|min:1|max:5',
        'fees_transparency_rating' => 'nullable|integer|min:1|max:5',
        'support_rating' => 'nullable|integer|min:1|max:5',
        'would_use_again' => 'boolean',
        'would_recommend' => 'boolean',
        'is_anonymous' => 'boolean',
        'placement_date' => 'nullable|date',
        'position_placed' => 'nullable|string|max:255',
        'yacht_name' => 'nullable|string|max:255',
        'placement_timeframe' => 'nullable|string|max:255',
        'photos.*' => 'nullable|image|max:5120',
    ];

    public function mount($brokerId = null, $reviewId = null)
    {
        if ($reviewId) {
            $this->editId = $reviewId;
            $this->loadReview($reviewId);
        } else {
            $this->brokerId = $brokerId;
            if ($brokerId) {
                $this->broker = Broker::findOrFail($brokerId);
            }
        }
    }

    public function loadReview($reviewId)
    {
        $review = BrokerReview::where('id', $reviewId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $this->title = $review->title;
        $this->review = $review->review;
        $this->overall_rating = $review->overall_rating;
        $this->job_quality_rating = $review->job_quality_rating;
        $this->communication_rating = $review->communication_rating;
        $this->professionalism_rating = $review->professionalism_rating;
        $this->fees_transparency_rating = $review->fees_transparency_rating;
        $this->support_rating = $review->support_rating;
        $this->would_use_again = $review->would_use_again;
        $this->would_recommend = $review->would_recommend;
        $this->is_anonymous = $review->is_anonymous;
        $this->placement_date = $review->placement_date?->format('Y-m-d');
        $this->position_placed = $review->position_placed;
        $this->yacht_name = $review->yacht_name;
        $this->placement_timeframe = $review->placement_timeframe;
        $this->brokerId = $review->broker_id;
        $this->broker = $review->broker;
        $this->existingPhotos = $review->photos;
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();
        if (!$user) {
            session()->flash('error', 'Please login to submit a review.');
            return;
        }

        if ($this->editId) {
            $review = BrokerReview::where('id', $this->editId)
                ->where('user_id', $user->id)
                ->firstOrFail();
        } else {
            $review = new BrokerReview();
            $review->broker_id = $this->brokerId;
            $review->user_id = $user->id;
        }

        $review->title = $this->title;
        $review->review = $this->review;
        $review->overall_rating = $this->overall_rating;
        $review->job_quality_rating = $this->job_quality_rating;
        $review->communication_rating = $this->communication_rating;
        $review->professionalism_rating = $this->professionalism_rating;
        $review->fees_transparency_rating = $this->fees_transparency_rating;
        $review->support_rating = $this->support_rating;
        $review->would_use_again = $this->would_use_again;
        $review->would_recommend = $this->would_recommend;
        $review->is_anonymous = $this->is_anonymous;
        $review->placement_date = $this->placement_date;
        $review->position_placed = $this->position_placed;
        $review->yacht_name = $this->yacht_name;
        $review->placement_timeframe = $this->placement_timeframe;
        $review->is_verified = true;
        $review->save();

        // Handle photo uploads
        if (!empty($this->photos)) {
            foreach ($this->photos as $photo) {
                $path = $photo->store('review-photos', 'public');
                $review->photos()->create([
                    'reviewable_type' => BrokerReview::class,
                    'reviewable_id' => $review->id,
                    'review_id' => $review->id,
                    'photo_path' => $path,
                ]);
            }
        }

        session()->flash('success', $this->editId ? 'Review updated successfully!' : 'Review submitted successfully!');
        return $this->redirect(route('broker-reviews.show', $this->broker->slug));
    }

    public function removePhoto($photoId)
    {
        $photo = \App\Models\ReviewPhoto::findOrFail($photoId);
        if (Storage::disk('public')->exists($photo->photo_path)) {
            Storage::disk('public')->delete($photo->photo_path);
        }
        $photo->delete();
        $this->existingPhotos = $this->existingPhotos->filter(fn($p) => $p->id !== $photoId);
    }

    public function render()
    {
        return view('livewire.industry-review.broker-review-create');
    }
}
