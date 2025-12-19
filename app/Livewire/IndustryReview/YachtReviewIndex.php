<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\Yacht;
use App\Models\YachtReview;
use App\Models\MasterData;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class YachtReviewIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public ?string $type = null;

    #[Url]
    public ?string $status = null;

    #[Url]
    public ?int $min_rating = null;

    #[Url]
    public ?int $min_recommendation = null;

    #[Url]
    public bool $showMyReviews = false;

    public array $statuses = [];

    public function mount(): void
    {
        $this->statuses = ['charter', 'private', 'both'];
    }

    public function updating($name, $value): void
    {
        if (in_array($name, ['search', 'type', 'status', 'min_rating', 'min_recommendation', 'showMyReviews'], true)) {
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
        $this->status = null;
        $this->min_rating = null;
        $this->min_recommendation = null;
        $this->showMyReviews = false;
        $this->resetPage();
    }

    public function render()
    {
        if ($this->showMyReviews && Auth::check()) {
            // Show user's reviews instead of yachts
            $query = YachtReview::query()
                ->with(['yacht', 'user', 'photos'])
                ->where('user_id', Auth::id())
                ->when($this->search, function ($q) {
                    $q->where(function ($inner) {
                        $inner->where('title', 'like', "%{$this->search}%")
                            ->orWhere('review', 'like', "%{$this->search}%")
                            ->orWhereHas('yacht', function ($yachtQuery) {
                                $yachtQuery->where('name', 'like', "%{$this->search}%");
                            });
                    });
                })
                ->when($this->min_rating, fn ($q) => $q->where('overall_rating', '>=', $this->min_rating))
                ->orderByDesc('created_at');

            $reviews = $query->paginate(12);

            $yachtTypes = MasterData::getYachtTypes();

            return view('livewire.industry-review.yacht-review-index', [
                'yachts' => collect(),
                'reviews' => $reviews,
                'yachtTypes' => $yachtTypes,
                'showMyReviews' => true,
            ]);
        }

        // Show yachts (normal view)
        $query = Yacht::query()
            ->withCount('reviews')
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('name', 'like', "%{$this->search}%")
                        ->orWhere('home_port', 'like', "%{$this->search}%")
                        ->orWhere('builder', 'like', "%{$this->search}%");
                });
            })
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->min_rating, fn ($q) => $q->where('rating_avg', '>=', $this->min_rating))
            ->when($this->min_recommendation, fn ($q) => $q->where('recommendation_percentage', '>=', $this->min_recommendation))
            ->orderByDesc('created_at')
            ->orderByDesc('rating_avg')
            ->orderByDesc('reviews_count')
            ->orderBy('name');

        $yachts = $query->paginate(12);

        // Add cover_image_url
        $yachts->getCollection()->transform(function ($yacht) {
            if ($yacht->cover_image) {
                $yacht->cover_image_url = Storage::disk('public')->url($yacht->cover_image);
            }
            return $yacht;
        });

        $yachtTypes = MasterData::getYachtTypes();

        return view('livewire.industry-review.yacht-review-index', [
            'yachts' => $yachts,
            'reviews' => null,
            'yachtTypes' => $yachtTypes,
            'showMyReviews' => false,
        ]);
    }
}

