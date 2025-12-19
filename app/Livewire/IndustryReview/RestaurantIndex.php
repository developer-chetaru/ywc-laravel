<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\Restaurant;
use App\Models\RestaurantReview;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class RestaurantIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public ?string $type = null;

    #[Url]
    public ?int $min_rating = null;

    #[Url]
    public ?bool $crew_friendly = null;

    #[Url]
    public bool $showMyReviews = false;

    public array $types = [
        'restaurant' => 'Restaurant',
        'bar' => 'Bar',
        'cafe' => 'Cafe',
        'shop' => 'Shop',
        'service' => 'Service',
    ];

    public function updating($name, $value): void
    {
        if (in_array($name, ['search', 'type', 'min_rating', 'crew_friendly', 'showMyReviews'], true)) {
            $this->resetPage();
        }
    }

    public function toggleMyReviews(): void
    {
        $this->showMyReviews = !$this->showMyReviews;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->type = null;
        $this->min_rating = null;
        $this->crew_friendly = null;
        $this->showMyReviews = false;
        $this->resetPage();
    }

    public function render()
    {
        if ($this->showMyReviews && Auth::check()) {
            // Show user's reviews instead of restaurants
            $query = RestaurantReview::query()
                ->with(['restaurant', 'user', 'photos'])
                ->where('user_id', Auth::id())
                ->when($this->search, function ($q) {
                    $q->where(function ($inner) {
                        $inner->where('title', 'like', "%{$this->search}%")
                            ->orWhere('review', 'like', "%{$this->search}%")
                            ->orWhereHas('restaurant', function ($restaurantQuery) {
                                $restaurantQuery->where('name', 'like', "%{$this->search}%");
                            });
                    });
                })
                ->when($this->min_rating, fn ($q) => $q->where('overall_rating', '>=', $this->min_rating))
                ->orderByDesc('created_at');

            $reviews = $query->paginate(12);

            return view('livewire.industry-review.restaurant-index', [
                'restaurants' => collect(),
                'reviews' => $reviews,
                'showMyReviews' => true,
            ]);
        }

        // Show restaurants (normal view)
        $query = Restaurant::query()
            ->withCount('reviews')
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('name', 'like', "%{$this->search}%")
                        ->orWhere('city', 'like', "%{$this->search}%")
                        ->orWhere('country', 'like', "%{$this->search}%")
                        ->orWhere('address', 'like', "%{$this->search}%");
                });
            })
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->when($this->min_rating, fn ($q) => $q->where('rating_avg', '>=', $this->min_rating))
            ->when($this->crew_friendly !== null, fn ($q) => $q->where('crew_friendly', $this->crew_friendly))
            ->orderByDesc('rating_avg')
            ->orderByDesc('reviews_count')
            ->orderBy('name');

        $restaurants = $query->paginate(12);

        // Add cover_image_url (only for local storage, external URLs are used directly)
        $restaurants->getCollection()->transform(function ($restaurant) {
            if ($restaurant->cover_image && !str_starts_with($restaurant->cover_image, 'http')) {
                $restaurant->cover_image_url = Storage::disk('public')->url($restaurant->cover_image);
            }
            return $restaurant;
        });

        return view('livewire.industry-review.restaurant-index', [
            'restaurants' => $restaurants,
            'reviews' => null,
            'showMyReviews' => false,
        ]);
    }
}
