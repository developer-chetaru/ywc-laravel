<?php

namespace App\Livewire\IndustryReview;

use App\Models\Contractor;
use App\Models\ContractorReview;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class ContractorReviewCreate extends Component
{
    use WithFileUploads;

    public $contractorId;
    public Contractor $contractor;
    public $editId = null;

    // Form fields
    public $title = '';
    public $review = '';
    public $service_type = '';
    public $service_cost = null;
    public $timeframe = '';
    public $overall_rating = 5;
    public $quality_rating = null;
    public $professionalism_rating = null;
    public $pricing_rating = null;
    public $timeliness_rating = null;
    public $would_recommend = true;
    public $would_hire_again = true;
    public $is_anonymous = false;
    public $service_date = null;
    public $yacht_name = '';
    public $photos = [];
    public $existingPhotos = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'review' => 'required|string|min:50',
        'service_type' => 'nullable|string|max:255',
        'service_cost' => 'nullable|numeric|min:0',
        'timeframe' => 'nullable|string|max:255',
        'overall_rating' => 'required|integer|min:1|max:5',
        'quality_rating' => 'nullable|integer|min:1|max:5',
        'professionalism_rating' => 'nullable|integer|min:1|max:5',
        'pricing_rating' => 'nullable|integer|min:1|max:5',
        'timeliness_rating' => 'nullable|integer|min:1|max:5',
        'would_recommend' => 'boolean',
        'would_hire_again' => 'boolean',
        'is_anonymous' => 'boolean',
        'service_date' => 'nullable|date',
        'yacht_name' => 'nullable|string|max:255',
        'photos.*' => 'nullable|image|max:5120',
    ];

    public function mount($contractorId = null, $reviewId = null)
    {
        // Get contractorId from query parameter if not provided as route parameter
        if (!$contractorId && request()->has('contractorId')) {
            $contractorId = request()->query('contractorId');
        }
        
        if ($reviewId) {
            $this->editId = $reviewId;
            $this->loadReview($reviewId);
        } else {
            $this->contractorId = $contractorId;
            if ($contractorId) {
                $this->contractor = Contractor::findOrFail($contractorId);
            }
        }
    }

    public function loadReview($reviewId)
    {
        $review = ContractorReview::where('id', $reviewId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $this->title = $review->title;
        $this->review = $review->review;
        $this->service_type = $review->service_type;
        $this->service_cost = $review->service_cost;
        $this->timeframe = $review->timeframe;
        $this->overall_rating = $review->overall_rating;
        $this->quality_rating = $review->quality_rating;
        $this->professionalism_rating = $review->professionalism_rating;
        $this->pricing_rating = $review->pricing_rating;
        $this->timeliness_rating = $review->timeliness_rating;
        $this->would_recommend = $review->would_recommend;
        $this->would_hire_again = $review->would_hire_again;
        $this->is_anonymous = $review->is_anonymous;
        $this->service_date = $review->service_date?->format('Y-m-d');
        $this->yacht_name = $review->yacht_name;
        $this->contractorId = $review->contractor_id;
        $this->contractor = $review->contractor;
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
            $review = ContractorReview::where('id', $this->editId)
                ->where('user_id', $user->id)
                ->firstOrFail();
        } else {
            $review = new ContractorReview();
            $review->contractor_id = $this->contractorId;
            $review->user_id = $user->id;
        }

        $review->title = $this->title;
        $review->review = $this->review;
        $review->service_type = $this->service_type;
        $review->service_cost = $this->service_cost;
        $review->timeframe = $this->timeframe;
        $review->overall_rating = $this->overall_rating;
        $review->quality_rating = $this->quality_rating;
        $review->professionalism_rating = $this->professionalism_rating;
        $review->pricing_rating = $this->pricing_rating;
        $review->timeliness_rating = $this->timeliness_rating;
        $review->would_recommend = $this->would_recommend;
        $review->would_hire_again = $this->would_hire_again;
        $review->is_anonymous = $this->is_anonymous;
        $review->service_date = $this->service_date;
        $review->yacht_name = $this->yacht_name;
        $review->is_verified = true;
        $review->save();

        // Handle photo uploads
        if (!empty($this->photos)) {
            foreach ($this->photos as $photo) {
                $path = $photo->store('review-photos', 'public');
                $review->photos()->create([
                    'reviewable_type' => ContractorReview::class,
                    'reviewable_id' => $review->id,
                    'review_id' => $review->id,
                    'photo_path' => $path,
                ]);
            }
        }

        session()->flash('success', $this->editId ? 'Review updated successfully!' : 'Review submitted successfully!');
        return $this->redirect(route('contractor-reviews.show', $this->contractor->slug));
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
        return view('livewire.industry-review.contractor-review-create');
    }
}
