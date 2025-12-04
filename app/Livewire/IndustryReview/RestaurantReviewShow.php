<?php

namespace App\Livewire\IndustryReview;

use App\Models\Restaurant;
use App\Models\RestaurantReview;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class RestaurantReviewShow extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public Restaurant $restaurant;
    public $sortBy = 'helpful';
    public $filterRating = null;

    public function mount($slug)
    {
        $this->restaurant = Restaurant::with('gallery')->where('slug', $slug)->firstOrFail();
    }

    public function updating($name, $value): void
    {
        if (in_array($name, ['sortBy', 'filterRating'])) {
            $this->resetPage();
        }
    }

    public function voteHelpful($reviewId)
    {
        $user = Auth::user();
        if (!$user) {
            session()->flash('error', 'Please login to vote.');
            return;
        }

        $review = RestaurantReview::findOrFail($reviewId);
        $existingVote = $review->votes()->where('user_id', $user->id)->first();

        if ($existingVote) {
            if ($existingVote->is_helpful) {
                $existingVote->delete();
                $review->decrement('helpful_count');
            } else {
                $existingVote->update(['is_helpful' => true]);
                $review->increment('helpful_count');
                $review->decrement('not_helpful_count');
            }
        } else {
            $review->votes()->create([
                'user_id' => $user->id,
                'reviewable_type' => RestaurantReview::class,
                'reviewable_id' => $review->id,
                'review_id' => $review->id,
                'is_helpful' => true,
            ]);
            $review->increment('helpful_count');
        }

        session()->flash('success', 'Thank you for your feedback!');
    }

    public function render()
    {
        $query = $this->restaurant->reviews()
            ->with(['user', 'photos'])
            ->when($this->filterRating, fn ($q) => $q->where('overall_rating', $this->filterRating))
            ->when($this->sortBy === 'recent', fn ($q) => $q->orderByDesc('created_at'))
            ->when($this->sortBy === 'rating', fn ($q) => $q->orderByDesc('overall_rating'))
            ->when($this->sortBy === 'helpful', fn ($q) => $q->orderByDesc('helpful_count'));

        $reviews = $query->paginate(10);

        return view('livewire.industry-review.restaurant-review-show', [
            'reviews' => $reviews,
        ]);
    }
}
