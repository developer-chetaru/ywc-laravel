<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TrainingCertification;
use App\Models\TrainingCertificationCategory;
use App\Models\TrainingProvider;
use App\Models\TrainingProviderCourse;
use App\Models\TrainingCourseLocation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;

class CourseDiscovery extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $search = '';

    #[Url(as: 'category')]
    public $categoryFilter = '';

    #[Url(as: 'provider')]
    public $providerFilter = '';

    #[Url(as: 'duration')]
    public $durationFilter = '';

    #[Url(as: 'price_min')]
    public $priceMin = '';

    #[Url(as: 'price_max')]
    public $priceMax = '';

    #[Url(as: 'location')]
    public $locationFilter = '';

    #[Url(as: 'format')]
    public $formatFilter = '';

    #[Url(as: 'sort')]
    public $sortBy = 'relevance';

    public $showFilters = false;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatedProviderFilter()
    {
        $this->resetPage();
    }

    public function updatedDurationFilter()
    {
        $this->resetPage();
    }

    public function updatedPriceMin()
    {
        $this->resetPage();
    }

    public function updatedPriceMax()
    {
        $this->resetPage();
    }

    public function updatedLocationFilter()
    {
        $this->resetPage();
    }

    public function updatedFormatFilter()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->categoryFilter = '';
        $this->providerFilter = '';
        $this->durationFilter = '';
        $this->priceMin = '';
        $this->priceMax = '';
        $this->locationFilter = '';
        $this->formatFilter = '';
        $this->sortBy = 'relevance';
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $isYwcMember = $user && $user->hasActiveSubscription(); // Adjust based on your subscription logic

        // Build query for provider courses
        $query = TrainingProviderCourse::with(['certification.category', 'provider', 'locations', 'upcomingSchedules'])
            ->where('is_active', true)
            ->whereHas('certification', function ($q) {
                $q->where('is_active', true);
            })
            ->whereHas('provider', function ($q) {
                $q->where('is_active', true);
            });

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('certification', function ($certQuery) {
                    $certQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('official_designation', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('provider', function ($provQuery) {
                    $provQuery->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Category filter
        if ($this->categoryFilter) {
            $query->whereHas('certification', function ($q) {
                $q->where('category_id', $this->categoryFilter);
            });
        }

        // Provider filter
        if ($this->providerFilter) {
            $query->where('provider_id', $this->providerFilter);
        }

        // Duration filter
        if ($this->durationFilter) {
            switch ($this->durationFilter) {
                case 'half-day':
                    $query->where('duration_days', '<=', 0.5);
                    break;
                case '1-day':
                    $query->where('duration_days', '<=', 1);
                    break;
                case '2-3-days':
                    $query->whereBetween('duration_days', [2, 3]);
                    break;
                case '4-5-days':
                    $query->whereBetween('duration_days', [4, 5]);
                    break;
                case '6-plus':
                    $query->where('duration_days', '>=', 6);
                    break;
            }
        }

        // Price filter
        if ($this->priceMin) {
            $query->where('price', '>=', $this->priceMin);
        }
        if ($this->priceMax) {
            $query->where('price', '<=', $this->priceMax);
        }

        // Location filter
        if ($this->locationFilter) {
            $query->whereHas('locations', function ($q) {
                $q->where('country', 'like', '%' . $this->locationFilter . '%')
                    ->orWhere('city', 'like', '%' . $this->locationFilter . '%')
                    ->orWhere('region', 'like', '%' . $this->locationFilter . '%');
            });
        }

        // Format filter
        if ($this->formatFilter) {
            $query->where('format', $this->formatFilter);
        }

        // Sorting
        switch ($this->sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'duration_short':
                $query->orderBy('duration_days', 'asc');
                break;
            case 'duration_long':
                $query->orderBy('duration_days', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating_avg', 'desc');
                break;
            case 'popular':
                $query->orderBy('view_count', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default: // relevance
                $query->orderBy('view_count', 'desc')
                    ->orderBy('rating_avg', 'desc');
        }

        $courses = $query->paginate(12);

        // Get filter options
        $categories = TrainingCertificationCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $providers = TrainingProvider::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get unique countries from locations
        $countries = TrainingCourseLocation::select('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country')
            ->filter()
            ->values();

        return view('livewire.training.course-discovery', [
            'courses' => $courses,
            'categories' => $categories,
            'providers' => $providers,
            'countries' => $countries,
            'isYwcMember' => $isYwcMember,
        ])->layout('layouts.app');
    }
}
