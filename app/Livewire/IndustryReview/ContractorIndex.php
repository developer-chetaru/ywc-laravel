<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\Contractor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class ContractorIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public ?string $category = null;

    #[Url]
    public ?int $min_rating = null;

    #[Url]
    public bool $showMyReviews = false;

    public array $categories = [
        'technical_services' => 'Technical Services',
        'refit_repair' => 'Refit & Repair',
        'equipment_supplier' => 'Equipment Supplier',
        'professional_services' => 'Professional Services',
        'crew_services' => 'Crew Services',
    ];

    public function updating($name, $value): void
    {
        if (in_array($name, ['search', 'category', 'min_rating', 'showMyReviews'], true)) {
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
        $this->category = null;
        $this->min_rating = null;
        $this->showMyReviews = false;
        $this->resetPage();
    }

    public function render()
    {
        $query = Contractor::query()
            ->withCount('reviews')
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('name', 'like', "%{$this->search}%")
                        ->orWhere('business_name', 'like', "%{$this->search}%")
                        ->orWhere('location', 'like', "%{$this->search}%")
                        ->orWhere('city', 'like', "%{$this->search}%")
                        ->orWhere('country', 'like', "%{$this->search}%");
                });
            })
            ->when($this->category, fn ($q) => $q->where('category', $this->category))
            ->when($this->min_rating, fn ($q) => $q->where('rating_avg', '>=', $this->min_rating))
            ->when($this->showMyReviews && Auth::check(), function ($q) {
                $q->whereHas('reviews', function ($reviewQuery) {
                    $reviewQuery->where('user_id', Auth::id());
                });
            })
            ->orderByDesc('rating_avg')
            ->orderByDesc('reviews_count')
            ->orderBy('name');

        $contractors = $query->paginate(12);

        // Add logo_url (only for local storage, external URLs are used directly)
        $contractors->getCollection()->transform(function ($contractor) {
            if ($contractor->logo && !str_starts_with($contractor->logo, 'http')) {
                $contractor->logo_url = Storage::disk('public')->url($contractor->logo);
            }
            return $contractor;
        });

        return view('livewire.industry-review.contractor-index', [
            'contractors' => $contractors,
        ]);
    }
}
