<?php

namespace App\Livewire\Itinerary;

use App\Models\ItineraryRoute;
use App\Models\ItineraryRouteReview;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class RouteReviews extends Component
{
    use WithPagination;

    public ItineraryRoute $route;
    public bool $showForm = false;
    public ?int $editingId = null;
    public int $rating = 5;
    public string $comment = '';
    public ?array $photos = [];

    protected $listeners = ['reviewAdded' => '$refresh'];

    public function mount(ItineraryRoute $route): void
    {
        $this->route = $route->load(['reviews.user']);
    }

    public function openForm(): void
    {
        Gate::authorize('view', $this->route);
        $this->showForm = true;
        $this->resetForm();
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->resetForm();
    }

    public function editReview(ItineraryRouteReview $review): void
    {
        Gate::authorize('update', $review);
        $this->editingId = $review->id;
        $this->rating = $review->rating;
        $this->comment = $review->comment;
        $this->photos = $review->photos ?? [];
        $this->showForm = true;
    }

    public function saveReview(): void
    {
        Gate::authorize('view', $this->route);

        $this->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:5000'],
            'photos' => ['nullable', 'array', 'max:5'],
        ]);

        if ($this->editingId) {
            $review = ItineraryRouteReview::findOrFail($this->editingId);
            Gate::authorize('update', $review);
            $review->update([
                'rating' => $this->rating,
                'comment' => $this->comment,
                'photos' => $this->photos,
            ]);
            session()->flash('review_message', 'Review updated successfully.');
        } else {
            $review = $this->route->reviews()->create([
                'user_id' => auth()->id(),
                'rating' => $this->rating,
                'comment' => $this->comment,
                'photos' => $this->photos,
            ]);
            session()->flash('review_message', 'Review added successfully.');
        }

        $this->route->refresh();
        $this->route->load(['reviews.user']);
        $this->closeForm();
        $this->dispatch('reviewAdded');
    }

    public function deleteReview(ItineraryRouteReview $review): void
    {
        Gate::authorize('delete', $review);
        $review->delete();
        $this->route->refresh();
        $this->route->load(['reviews.user']);
        session()->flash('review_message', 'Review deleted successfully.');
    }

    private function resetForm(): void
    {
        $this->rating = 5;
        $this->comment = '';
        $this->photos = [];
    }

    public function render()
    {
        Gate::authorize('view', $this->route);

        $reviews = $this->route->reviews()
            ->with('user:id,first_name,last_name,email,profile_photo_path')
            ->latest()
            ->paginate(10);

        $averageRating = $this->route->reviews()->avg('rating') ?? 0;
        $ratingCounts = $this->route->reviews()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        return view('livewire.itinerary.route-reviews', [
            'reviews' => $reviews,
            'averageRating' => round($averageRating, 1),
            'ratingCounts' => $ratingCounts,
            'totalReviews' => $this->route->reviews()->count(),
        ]);
    }
}

