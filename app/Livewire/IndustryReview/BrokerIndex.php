<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\Broker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class BrokerIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public ?string $type = null;

    #[Url]
    public ?string $fee_structure = null;

    #[Url]
    public ?int $min_rating = null;

    #[Url]
    public bool $showMyReviews = false;

    public array $types = [
        'crew_placement_agency' => 'Crew Placement Agency',
        'yacht_management' => 'Yacht Management',
        'independent_broker' => 'Independent Broker',
        'charter_broker' => 'Charter Broker',
    ];

    public array $feeStructures = [
        'free_for_crew' => 'Free for Crew',
        'crew_pays' => 'Crew Pays',
        'yacht_pays' => 'Yacht Pays',
    ];

    public function updating($name, $value): void
    {
        if (in_array($name, ['search', 'type', 'fee_structure', 'min_rating', 'showMyReviews'], true)) {
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
        $this->fee_structure = null;
        $this->min_rating = null;
        $this->showMyReviews = false;
        $this->resetPage();
    }

    public function render()
    {
        $query = Broker::query()
            ->withCount('reviews')
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('name', 'like', "%{$this->search}%")
                        ->orWhere('business_name', 'like', "%{$this->search}%")
                        ->orWhere('primary_location', 'like', "%{$this->search}%");
                });
            })
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->when($this->fee_structure, fn ($q) => $q->where('fee_structure', $this->fee_structure))
            ->when($this->min_rating, fn ($q) => $q->where('rating_avg', '>=', $this->min_rating))
            ->when($this->showMyReviews && Auth::check(), function ($q) {
                $q->whereHas('reviews', function ($reviewQuery) {
                    $reviewQuery->where('user_id', Auth::id());
                });
            })
            ->orderByDesc('rating_avg')
            ->orderByDesc('reviews_count')
            ->orderBy('name');

        $brokers = $query->paginate(12);

        // Add logo_url (only for local storage, external URLs are used directly)
        $brokers->getCollection()->transform(function ($broker) {
            if ($broker->logo && !str_starts_with($broker->logo, 'http')) {
                $broker->logo_url = Storage::disk('public')->url($broker->logo);
            }
            return $broker;
        });

        return view('livewire.industry-review.broker-index', [
            'brokers' => $brokers,
        ]);
    }
}
