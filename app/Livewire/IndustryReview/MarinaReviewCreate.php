<?php

namespace App\Livewire\IndustryReview;

use App\Models\Marina;
use App\Models\MarinaReview;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class MarinaReviewCreate extends Component
{
    use WithFileUploads;

    public $marinaId;
    public Marina $marina;
    public $editId = null;

    // Form fields
    public $title = '';
    public $review = '';
    public $tips_tricks = '';
    public $overall_rating = 5;
    public $fuel_rating = null;
    public $water_rating = null;
    public $electricity_rating = null;
    public $wifi_rating = null;
    public $showers_rating = null;
    public $laundry_rating = null;
    public $maintenance_rating = null;
    public $provisioning_rating = null;
    public $staff_rating = null;
    public $value_rating = null;
    public $protection_rating = null;
    public $is_anonymous = false;
    public $visit_date = null;
    public $yacht_length_meters = '';
    public $photos = [];
    public $existingPhotos = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'review' => 'required|string|min:50',
        'tips_tricks' => 'nullable|string',
        'overall_rating' => 'required|integer|min:1|max:5',
        'fuel_rating' => 'nullable|integer|min:1|max:5',
        'water_rating' => 'nullable|integer|min:1|max:5',
        'electricity_rating' => 'nullable|integer|min:1|max:5',
        'wifi_rating' => 'nullable|integer|min:1|max:5',
        'showers_rating' => 'nullable|integer|min:1|max:5',
        'laundry_rating' => 'nullable|integer|min:1|max:5',
        'maintenance_rating' => 'nullable|integer|min:1|max:5',
        'provisioning_rating' => 'nullable|integer|min:1|max:5',
        'staff_rating' => 'nullable|integer|min:1|max:5',
        'value_rating' => 'nullable|integer|min:1|max:5',
        'protection_rating' => 'nullable|integer|min:1|max:5',
        'is_anonymous' => 'boolean',
        'visit_date' => 'nullable|date',
        'yacht_length_meters' => 'nullable|string|max:50',
        'photos.*' => 'nullable|image|max:5120',
    ];

    public function mount($marinaId = null, $reviewId = null)
    {
        // Get marinaId from query parameter if not provided as route parameter
        if (!$marinaId && request()->has('marinaId')) {
            $marinaId = request()->query('marinaId');
        }
        
        if ($reviewId) {
            $this->editId = $reviewId;
            $this->loadReview($reviewId);
        } else {
            $this->marinaId = $marinaId;
            if ($marinaId) {
                $this->marina = Marina::findOrFail($marinaId);
            }
        }
    }

    public function loadReview($reviewId)
    {
        $review = MarinaReview::where('id', $reviewId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $this->title = $review->title;
        $this->review = $review->review;
        $this->tips_tricks = $review->tips_tricks;
        $this->overall_rating = $review->overall_rating;
        $this->fuel_rating = $review->fuel_rating;
        $this->water_rating = $review->water_rating;
        $this->electricity_rating = $review->electricity_rating;
        $this->wifi_rating = $review->wifi_rating;
        $this->showers_rating = $review->showers_rating;
        $this->laundry_rating = $review->laundry_rating;
        $this->maintenance_rating = $review->maintenance_rating;
        $this->provisioning_rating = $review->provisioning_rating;
        $this->staff_rating = $review->staff_rating;
        $this->value_rating = $review->value_rating;
        $this->protection_rating = $review->protection_rating;
        $this->is_anonymous = $review->is_anonymous;
        $this->visit_date = $review->visit_date?->format('Y-m-d');
        $this->yacht_length_meters = $review->yacht_length_meters;
        $this->marinaId = $review->marina_id;
        $this->marina = $review->marina;
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
            $review = MarinaReview::where('id', $this->editId)
                ->where('user_id', $user->id)
                ->firstOrFail();
        } else {
            $review = new MarinaReview();
            $review->marina_id = $this->marinaId;
            $review->user_id = $user->id;
        }

        $review->title = $this->title;
        $review->review = $this->review;
        $review->tips_tricks = $this->tips_tricks;
        $review->overall_rating = $this->overall_rating;
        $review->fuel_rating = $this->fuel_rating;
        $review->water_rating = $this->water_rating;
        $review->electricity_rating = $this->electricity_rating;
        $review->wifi_rating = $this->wifi_rating;
        $review->showers_rating = $this->showers_rating;
        $review->laundry_rating = $this->laundry_rating;
        $review->maintenance_rating = $this->maintenance_rating;
        $review->provisioning_rating = $this->provisioning_rating;
        $review->staff_rating = $this->staff_rating;
        $review->value_rating = $this->value_rating;
        $review->protection_rating = $this->protection_rating;
        $review->is_anonymous = $this->is_anonymous;
        $review->visit_date = $this->visit_date;
        $review->yacht_length_meters = $this->yacht_length_meters;
        $review->is_verified = true;
        $review->save();

        // Handle photo uploads
        if (!empty($this->photos)) {
            foreach ($this->photos as $photo) {
                $path = $photo->store('review-photos', 'public');
                $review->photos()->create([
                    'reviewable_type' => MarinaReview::class,
                    'reviewable_id' => $review->id,
                    'review_id' => $review->id,
                    'photo_path' => $path,
                ]);
            }
        }

        session()->flash('success', $this->editId ? 'Review updated successfully!' : 'Review submitted successfully!');
        return $this->redirect(route('marina-reviews.show', $this->marina->slug));
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
        return view('livewire.industry-review.marina-review-create');
    }
}

