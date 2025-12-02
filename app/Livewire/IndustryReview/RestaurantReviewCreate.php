<?php

namespace App\Livewire\IndustryReview;

use App\Models\Restaurant;
use App\Models\RestaurantReview;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class RestaurantReviewCreate extends Component
{
    use WithFileUploads;

    public $restaurantId;
    public Restaurant $restaurant;
    public $editId = null;

    // Form fields
    public $title = '';
    public $review = '';
    public $overall_rating = 5;
    public $food_rating = null;
    public $service_rating = null;
    public $atmosphere_rating = null;
    public $value_rating = null;
    public $would_recommend = true;
    public $is_anonymous = false;
    public $visit_date = null;
    public $crew_tips = '';
    public $photos = [];
    public $existingPhotos = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'review' => 'required|string|min:50',
        'overall_rating' => 'required|integer|min:1|max:5',
        'food_rating' => 'nullable|integer|min:1|max:5',
        'service_rating' => 'nullable|integer|min:1|max:5',
        'atmosphere_rating' => 'nullable|integer|min:1|max:5',
        'value_rating' => 'nullable|integer|min:1|max:5',
        'would_recommend' => 'boolean',
        'is_anonymous' => 'boolean',
        'visit_date' => 'nullable|date',
        'crew_tips' => 'nullable|string',
        'photos.*' => 'nullable|image|max:5120',
    ];

    public function mount($restaurantId = null, $reviewId = null)
    {
        if ($reviewId) {
            $this->editId = $reviewId;
            $this->loadReview($reviewId);
        } else {
            $this->restaurantId = $restaurantId;
            if ($restaurantId) {
                $this->restaurant = Restaurant::findOrFail($restaurantId);
            }
        }
    }

    public function loadReview($reviewId)
    {
        $review = RestaurantReview::where('id', $reviewId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $this->title = $review->title;
        $this->review = $review->review;
        $this->overall_rating = $review->overall_rating;
        $this->food_rating = $review->food_rating;
        $this->service_rating = $review->service_rating;
        $this->atmosphere_rating = $review->atmosphere_rating;
        $this->value_rating = $review->value_rating;
        $this->would_recommend = $review->would_recommend;
        $this->is_anonymous = $review->is_anonymous;
        $this->visit_date = $review->visit_date?->format('Y-m-d');
        $this->crew_tips = $review->crew_tips;
        $this->restaurantId = $review->restaurant_id;
        $this->restaurant = $review->restaurant;
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
            $review = RestaurantReview::where('id', $this->editId)
                ->where('user_id', $user->id)
                ->firstOrFail();
        } else {
            $review = new RestaurantReview();
            $review->restaurant_id = $this->restaurantId;
            $review->user_id = $user->id;
        }

        $review->title = $this->title;
        $review->review = $this->review;
        $review->overall_rating = $this->overall_rating;
        $review->food_rating = $this->food_rating;
        $review->service_rating = $this->service_rating;
        $review->atmosphere_rating = $this->atmosphere_rating;
        $review->value_rating = $this->value_rating;
        $review->would_recommend = $this->would_recommend;
        $review->is_anonymous = $this->is_anonymous;
        $review->visit_date = $this->visit_date;
        $review->crew_tips = $this->crew_tips;
        $review->is_verified = true;
        $review->save();

        // Handle photo uploads
        if (!empty($this->photos)) {
            foreach ($this->photos as $photo) {
                $path = $photo->store('review-photos', 'public');
                $review->photos()->create([
                    'reviewable_type' => RestaurantReview::class,
                    'reviewable_id' => $review->id,
                    'review_id' => $review->id,
                    'photo_path' => $path,
                ]);
            }
        }

        session()->flash('success', $this->editId ? 'Review updated successfully!' : 'Review submitted successfully!');
        return $this->redirect(route('restaurant-reviews.show', $this->restaurant->slug));
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
        return view('livewire.industry-review.restaurant-review-create');
    }
}
