<?php

namespace App\Livewire\IndustryReview;

use App\Models\Yacht;
use App\Models\YachtReview;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class YachtReviewCreate extends Component
{
    use WithFileUploads;

    public $yachtId;
    public Yacht $yacht;
    public $editId = null;

    // Form fields
    public $title = '';
    public $review = '';
    public $pros = '';
    public $cons = '';
    public $overall_rating = 5;
    // New 5-category rating system
    public $yacht_quality_rating = null;
    public $crew_culture_rating = null;
    public $management_rating = null;
    public $benefits_rating = null;
    // Legacy fields (kept for backward compatibility)
    public $working_conditions_rating = null;
    public $compensation_rating = null;
    public $crew_welfare_rating = null;
    public $yacht_condition_rating = null;
    public $career_development_rating = null;
    public $would_recommend = true;
    public $is_anonymous = false;
    public $work_start_date = null;
    public $work_end_date = null;
    public $position_held = '';
    public $photos = [];
    public $existingPhotos = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'review' => 'required|string|min:50',
        'pros' => 'nullable|string',
        'cons' => 'nullable|string',
        'overall_rating' => 'required|integer|min:1|max:5',
        // New 5-category rating system
        'yacht_quality_rating' => 'nullable|integer|min:1|max:5',
        'crew_culture_rating' => 'nullable|integer|min:1|max:5',
        'management_rating' => 'nullable|integer|min:1|max:5',
        'benefits_rating' => 'nullable|integer|min:1|max:5',
        // Legacy fields
        'working_conditions_rating' => 'nullable|integer|min:1|max:5',
        'compensation_rating' => 'nullable|integer|min:1|max:5',
        'crew_welfare_rating' => 'nullable|integer|min:1|max:5',
        'yacht_condition_rating' => 'nullable|integer|min:1|max:5',
        'career_development_rating' => 'nullable|integer|min:1|max:5',
        'would_recommend' => 'boolean',
        'is_anonymous' => 'boolean',
        'work_start_date' => 'nullable|date',
        'work_end_date' => 'nullable|date|after_or_equal:work_start_date',
        'position_held' => 'nullable|string|max:255',
        'photos.*' => 'nullable|image|max:5120',
    ];

    public function mount($yachtId = null, $reviewId = null)
    {
        if ($reviewId) {
            $this->editId = $reviewId;
            $this->loadReview($reviewId);
        } else {
            $this->yachtId = $yachtId;
            if ($yachtId) {
                $this->yacht = Yacht::findOrFail($yachtId);
            }
        }
    }

    public function loadReview($reviewId)
    {
        $review = YachtReview::where('id', $reviewId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $this->title = $review->title;
        $this->review = $review->review;
        $this->pros = $review->pros;
        $this->cons = $review->cons;
        $this->overall_rating = $review->overall_rating;
        // New 5-category rating system
        $this->yacht_quality_rating = $review->yacht_quality_rating ?? $review->yacht_condition_rating;
        $this->crew_culture_rating = $review->crew_culture_rating;
        $this->management_rating = $review->management_rating;
        $this->benefits_rating = $review->benefits_rating ?? ($review->compensation_rating && $review->crew_welfare_rating ? round(($review->compensation_rating + $review->crew_welfare_rating) / 2) : null);
        // Legacy fields
        $this->working_conditions_rating = $review->working_conditions_rating;
        $this->compensation_rating = $review->compensation_rating;
        $this->crew_welfare_rating = $review->crew_welfare_rating;
        $this->yacht_condition_rating = $review->yacht_condition_rating;
        $this->career_development_rating = $review->career_development_rating;
        $this->would_recommend = $review->would_recommend;
        $this->is_anonymous = $review->is_anonymous;
        $this->work_start_date = $review->work_start_date?->format('Y-m-d');
        $this->work_end_date = $review->work_end_date?->format('Y-m-d');
        $this->position_held = $review->position_held;
        $this->yachtId = $review->yacht_id;
        $this->yacht = $review->yacht;
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
            $review = YachtReview::where('id', $this->editId)
                ->where('user_id', $user->id)
                ->firstOrFail();
        } else {
            $review = new YachtReview();
            $review->yacht_id = $this->yachtId;
            $review->user_id = $user->id;
        }

        $review->title = $this->title;
        $review->review = $this->review;
        $review->pros = $this->pros;
        $review->cons = $this->cons;
        $review->overall_rating = $this->overall_rating;
        // New 5-category rating system
        $review->yacht_quality_rating = $this->yacht_quality_rating;
        $review->crew_culture_rating = $this->crew_culture_rating;
        $review->management_rating = $this->management_rating;
        $review->benefits_rating = $this->benefits_rating;
        // Legacy fields (kept for backward compatibility)
        $review->working_conditions_rating = $this->working_conditions_rating;
        $review->compensation_rating = $this->compensation_rating;
        $review->crew_welfare_rating = $this->crew_welfare_rating;
        $review->yacht_condition_rating = $this->yacht_condition_rating;
        $review->career_development_rating = $this->career_development_rating;
        $review->would_recommend = $this->would_recommend;
        $review->is_anonymous = $this->is_anonymous;
        $review->work_start_date = $this->work_start_date;
        $review->work_end_date = $this->work_end_date;
        $review->position_held = $this->position_held;
        $review->is_verified = true; // Can add verification logic later
        $review->save();

        // Handle photo uploads
        if (!empty($this->photos)) {
            foreach ($this->photos as $photo) {
                $path = $photo->store('review-photos', 'public');
                $review->photos()->create([
                    'reviewable_type' => YachtReview::class,
                    'reviewable_id' => $review->id,
                    'review_id' => $review->id,
                    'photo_path' => $path,
                ]);
            }
        }

        session()->flash('success', $this->editId ? 'Review updated successfully!' : 'Review submitted successfully!');
        return $this->redirect(route('yacht-reviews.show', $this->yacht->slug));
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
        return view('livewire.industry-review.yacht-review-create');
    }
}

