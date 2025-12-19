<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\Marina;
use App\Models\MarinaReview;
use App\Models\MasterData;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class MarinaReviewIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public ?string $country = null;

    #[Url]
    public ?string $type = null;

    #[Url]
    public ?int $min_rating = null;

    #[Url]
    public bool $showMyReviews = false;

    public array $countries = [];

    public function mount(): void
    {
        $this->loadCountries();
    }

    public function loadCountries()
    {
        $this->countries = Marina::query()
            ->whereNotNull('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country')
            ->toArray();
    }

    public function updating($name, $value): void
    {
        if (in_array($name, ['search', 'country', 'type', 'min_rating', 'showMyReviews'], true)) {
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
        $this->country = null;
        $this->type = null;
        $this->min_rating = null;
        $this->showMyReviews = false;
        $this->resetPage();
    }

    public function render()
    {
        if ($this->showMyReviews && Auth::check()) {
            // Show user's reviews instead of marinas
            $query = MarinaReview::query()
                ->with(['marina', 'user', 'photos'])
                ->where('user_id', Auth::id())
                ->when($this->search, function ($q) {
                    $q->where(function ($inner) {
                        $inner->where('title', 'like', "%{$this->search}%")
                            ->orWhere('review', 'like', "%{$this->search}%")
                            ->orWhereHas('marina', function ($marinaQuery) {
                                $marinaQuery->where('name', 'like', "%{$this->search}%");
                            });
                    });
                })
                ->when($this->min_rating, fn ($q) => $q->where('overall_rating', '>=', $this->min_rating))
                ->orderByDesc('created_at');

            $reviews = $query->paginate(12);

            $marinaTypes = MasterData::getMarinaTypes();

            return view('livewire.industry-review.marina-review-index', [
                'marinas' => collect(),
                'reviews' => $reviews,
                'marinaTypes' => $marinaTypes,
                'showMyReviews' => true,
            ]);
        }

        // Show marinas (normal view)
        $query = Marina::query()
            ->withCount('reviews')
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('name', 'like', "%{$this->search}%")
                        ->orWhere('city', 'like', "%{$this->search}%")
                        ->orWhere('country', 'like', "%{$this->search}%");
                });
            })
            ->when($this->country, fn ($q) => $q->where('country', $this->country))
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->when($this->min_rating, fn ($q) => $q->where('rating_avg', '>=', $this->min_rating))
            ->orderByDesc('created_at')
            ->orderByDesc('rating_avg')
            ->orderByDesc('reviews_count')
            ->orderBy('name');

        $marinas = $query->paginate(12);

        // Add cover_image_url
        $marinas->getCollection()->transform(function ($marina) {
            if ($marina->cover_image) {
                $marina->cover_image_url = Storage::disk('public')->url($marina->cover_image);
            }
            return $marina;
        });

        $marinaTypes = MasterData::getMarinaTypes();

        return view('livewire.industry-review.marina-review-index', [
            'marinas' => $marinas,
            'reviews' => null,
            'marinaTypes' => $marinaTypes,
            'showMyReviews' => false,
        ]);
    }
}

