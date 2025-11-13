<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\Marina;
use Illuminate\Support\Facades\Storage;

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

    public array $countries = [];
    public array $types = [];

    public function mount(): void
    {
        $this->types = ['full_service', 'municipal_port', 'yacht_club', 'anchorage', 'mooring_field', 'dry_stack', 'boatyard'];
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
        if (in_array($name, ['search', 'country', 'type', 'min_rating'], true)) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->country = null;
        $this->type = null;
        $this->min_rating = null;
        $this->resetPage();
    }

    public function render()
    {
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

        return view('livewire.industry-review.marina-review-index', [
            'marinas' => $marinas,
        ]);
    }
}

