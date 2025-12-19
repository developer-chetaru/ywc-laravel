<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\Yacht;
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
            ->when($this->showMyReviews && Auth::check(), function ($q) {
                $q->whereHas('reviews', function ($reviewQuery) {
                    $reviewQuery->where('user_id', Auth::id());
                });
            })
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
            'yachtTypes' => $yachtTypes,
        ]);
    }
}

