<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Yacht;
use App\Models\Marina;
use App\Models\Contractor;
use App\Models\Broker;
use App\Models\Restaurant;
use App\Models\YachtReview;
use App\Models\MarinaReview;
use App\Models\ContractorReview;
use App\Models\BrokerReview;
use App\Models\RestaurantReview;
use App\Models\MasterData;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

#[Layout('layouts.app')]
class IndustryReviewIndex extends Component
{
    use WithFileUploads, WithPagination;

    public $activeTab = 'yachts';
    public $yachts = [];
    public $marinas = [];
    public $contractors = [];
    public $brokers = [];
    public $restaurants = [];
    public $yachtsTotalCount = 0;
    public $marinasTotalCount = 0;
    public $contractorsTotalCount = 0;
    public $brokersTotalCount = 0;
    public $restaurantsTotalCount = 0;
    public $loading = false;
    public $showYachtModal = false;
    public $searchQuery = '';
    public $showAll = false; // Toggle to show all items with pagination
    
    // Yacht form fields
    public $name = '';
    public $type = '';
    public $length_meters = '';
    public $length_feet = '';
    public $year_built = '';
    public $flag_registry = '';
    public $home_port = '';
    public $crew_capacity = '';
    public $guest_capacity = '';
    public $status = 'charter';
    public $cover_image;
    public $cover_image_preview = null;
    public $yachtLoading = false;
    public $yachtMessage = '';
    public $yachtError = '';

    // Detail view properties
    public $showDetailModal = false;
    public $detailType = 'yacht'; // 'yacht' or 'marina'
    public $detailData = null;
    public $detailLoading = false;
    public $loadingItemId = null; // Track which specific item is loading
    public $loadingItemType = null; // Track the type of item being loaded
    public $detailError = null;
    public $reviews = [];

    // Review form properties
    public $showReviewForm = false;
    public $reviewRating = 5;
    public $reviewTitle = '';
    public $reviewContent = '';
    public $reviewPros = '';
    public $reviewCons = '';
    public $reviewRecommend = true;
    public $reviewAnonymous = false;
    public $reviewPhotos = [];
    public $reviewLoading = false;
    public $reviewError = '';
    public $reviewMessage = '';

    public $statuses = [
        'charter' => 'Charter',
        'private' => 'Private',
        'both' => 'Both',
    ];

    public function mount()
    {
        // Check if tab is specified in query string
        $tab = request()->query('tab', 'yachts');
        $this->activeTab = $tab;
        $this->loadTabData($tab);
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->showAll = false; // Reset showAll when switching tabs
        $this->resetPage(); // Reset pagination
        // Reset total counts
        $this->yachtsTotalCount = 0;
        $this->marinasTotalCount = 0;
        $this->contractorsTotalCount = 0;
        $this->brokersTotalCount = 0;
        $this->restaurantsTotalCount = 0;
        $this->loadTabData($tab);
    }

    public function toggleShowAll()
    {
        $this->showAll = !$this->showAll;
        $this->resetPage(); // Reset pagination when toggling
        $this->loadTabData($this->activeTab);
    }

    public function loadTabData($tab)
    {
        switch ($tab) {
            case 'yachts':
                $this->loadYachts();
                break;
            case 'marinas':
                $this->loadMarinas();
                break;
            case 'contractors':
                $this->loadContractors();
                break;
            case 'brokers':
                $this->loadBrokers();
                break;
            case 'restaurants':
                $this->loadRestaurants();
                break;
        }
    }

    public function search()
    {
        $this->loadTabData($this->activeTab);
    }

    public function clearSearch()
    {
        $this->searchQuery = '';
        $this->loadTabData($this->activeTab);
    }

    public function updatedSearchQuery()
    {
        // Debounce search - search automatically after user stops typing
        $this->search();
    }

    public function loadYachts()
    {
        $this->loading = true;
        try {
            $query = Yacht::query()
                ->withCount('reviews');
            
            // Apply search filter if search query exists
            if (!empty($this->searchQuery)) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('type', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('home_port', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('flag_registry', 'like', '%' . $this->searchQuery . '%');
                });
            }
            
            $query->orderByDesc('created_at') // Latest first
                ->orderByDesc('rating_avg')
                ->orderByDesc('reviews_count')
                ->orderBy('name');
            
            // Don't store paginated collection - return from render() instead
            if ($this->showAll) {
                $this->yachts = [];
            } else {
                $totalCount = $query->count(); // Get total count before limiting
                $yachts = $query->limit(12)->get();
                $this->yachts = $yachts->map(function ($yacht) {
                    return $this->formatYachtData($yacht);
                })->toArray();
                // Store total count for "See All" button display
                $this->yachtsTotalCount = $totalCount;
            }
        } catch (\Exception $e) {
            \Log::error('Error loading yachts: ' . $e->getMessage());
            $this->yachts = [];
            $this->yachtsTotalCount = 0;
        } finally {
            $this->loading = false;
        }
    }

    public function loadMarinas()
    {
        $this->loading = true;
        try {
            $query = Marina::query()
                ->withCount('reviews');
            
            // Apply search filter if search query exists
            if (!empty($this->searchQuery)) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('city', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('country', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('type', 'like', '%' . $this->searchQuery . '%');
                });
            }
            
            $query->orderByDesc('created_at') // Latest first
                ->orderByDesc('rating_avg')
                ->orderByDesc('reviews_count')
                ->orderBy('name');
            
            if ($this->showAll) {
                $this->marinas = [];
            } else {
                $totalCount = $query->count();
                $marinas = $query->limit(12)->get();
                $this->marinas = $marinas->map(function ($marina) {
                    return $this->formatMarinaData($marina);
                })->toArray();
                $this->marinasTotalCount = $totalCount;
            }
        } catch (\Exception $e) {
            $this->marinas = [];
        } finally {
            $this->loading = false;
        }
    }

    public function loadContractors()
    {
        $this->loading = true;
        try {
            $query = Contractor::query()
                ->withCount('reviews');
            
            if (!empty($this->searchQuery)) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('business_name', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('city', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('country', 'like', '%' . $this->searchQuery . '%');
                });
            }
            
            $query->orderByDesc('created_at') // Latest first
                ->orderByDesc('rating_avg')
                ->orderByDesc('reviews_count')
                ->orderBy('name');
            
            if ($this->showAll) {
                $this->contractors = [];
            } else {
                $totalCount = $query->count();
                $contractors = $query->limit(12)->get();
                $this->contractors = $contractors->map(function ($contractor) {
                    return $this->formatContractorData($contractor);
                })->toArray();
                $this->contractorsTotalCount = $totalCount;
            }
        } catch (\Exception $e) {
            $this->contractors = [];
        } finally {
            $this->loading = false;
        }
    }

    public function loadBrokers()
    {
        $this->loading = true;
        try {
            $query = Broker::query()
                ->withCount('reviews');
            
            if (!empty($this->searchQuery)) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('business_name', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('primary_location', 'like', '%' . $this->searchQuery . '%');
                });
            }
            
            $query->orderByDesc('created_at') // Latest first
                ->orderByDesc('rating_avg')
                ->orderByDesc('reviews_count')
                ->orderBy('name');
            
            if ($this->showAll) {
                $this->brokers = [];
            } else {
                $totalCount = $query->count();
                $brokers = $query->limit(12)->get();
                $this->brokers = $brokers->map(function ($broker) {
                    return $this->formatBrokerData($broker);
                })->toArray();
                $this->brokersTotalCount = $totalCount;
            }
        } catch (\Exception $e) {
            $this->brokers = [];
        } finally {
            $this->loading = false;
        }
    }

    public function loadRestaurants()
    {
        $this->loading = true;
        try {
            $query = Restaurant::query()
                ->withCount('reviews');
            
            if (!empty($this->searchQuery)) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('city', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('country', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('type', 'like', '%' . $this->searchQuery . '%');
                });
            }
            
            $query->orderByDesc('created_at') // Latest first
                ->orderByDesc('rating_avg')
                ->orderByDesc('reviews_count')
                ->orderBy('name');
            
            if ($this->showAll) {
                $this->restaurants = [];
            } else {
                $totalCount = $query->count();
                $restaurants = $query->limit(12)->get();
                $this->restaurants = $restaurants->map(function ($restaurant) {
                    return $this->formatRestaurantData($restaurant);
                })->toArray();
                $this->restaurantsTotalCount = $totalCount;
            }
        } catch (\Exception $e) {
            $this->restaurants = [];
        } finally {
            $this->loading = false;
        }
    }

    public function updatedCoverImage()
    {
        if ($this->cover_image) {
            $this->cover_image_preview = $this->cover_image->temporaryUrl();
        }
    }

    public function openYachtModal()
    {
        $this->resetYachtForm();
        $this->showYachtModal = true;
    }

    public function closeModal()
    {
        $this->showYachtModal = false;
        $this->resetYachtForm();
    }

    public function resetYachtForm()
    {
        $this->name = '';
        $this->type = '';
        $this->length_meters = '';
        $this->length_feet = '';
        $this->year_built = '';
        $this->flag_registry = '';
        $this->home_port = '';
        $this->crew_capacity = '';
        $this->guest_capacity = '';
        $this->status = 'charter';
        $this->cover_image = null;
        $this->cover_image_preview = null;
        $this->yachtMessage = '';
        $this->yachtError = '';
    }

    public function saveYacht()
    {
        $this->yachtLoading = true;
        $this->yachtError = '';
        $this->yachtMessage = '';

        try {
            if (!auth()->check()) {
                $this->yachtError = 'You must be logged in to create a yacht.';
                $this->yachtLoading = false;
                return;
            }

            $service = app(\App\Services\YachtService::class);

            $data = [
                'name' => $this->name,
                'type' => $this->type,
                'status' => $this->status,
            ];

            if ($this->length_meters) $data['length_meters'] = $this->length_meters;
            if ($this->length_feet) $data['length_feet'] = $this->length_feet;
            if ($this->year_built) $data['year_built'] = $this->year_built;
            if ($this->flag_registry) $data['flag_registry'] = $this->flag_registry;
            if ($this->home_port) $data['home_port'] = $this->home_port;
            if ($this->crew_capacity) $data['crew_capacity'] = $this->crew_capacity;
            if ($this->guest_capacity) $data['guest_capacity'] = $this->guest_capacity;

            $service->create($data, $this->cover_image);

            $this->yachtMessage = 'Yacht created successfully!';
            $this->resetYachtForm();
            $this->closeModal();
            $this->loadYachts();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->yachtError = collect($e->errors())->flatten()->implode(', ');
        } catch (\Exception $e) {
            $this->yachtError = 'An error occurred: ' . $e->getMessage();
        } finally {
            $this->yachtLoading = false;
        }
    }

    public function viewDetails($type, $identifier)
    {
        try {
            // Convert identifier to integer if it's numeric
            if (is_numeric($identifier)) {
                $identifier = (int) $identifier;
            }

            if ($type === 'yacht') {
                $item = Yacht::where('id', $identifier)->orWhere('slug', $identifier)->first();
                if (!$item) {
                    session()->flash('error', 'Yacht not found.');
                    return;
                }
                $slug = $item->slug ?? (string) $item->id;
                return $this->redirect(route('yacht-reviews.show', $slug), navigate: true);
            } elseif ($type === 'marina') {
                $item = Marina::where('id', $identifier)->orWhere('slug', $identifier)->first();
                if (!$item) {
                    session()->flash('error', 'Marina not found.');
                    return;
                }
                $slug = $item->slug ?? (string) $item->id;
                return $this->redirect(route('marina-reviews.show', $slug), navigate: true);
            } elseif ($type === 'contractor') {
                $item = Contractor::where('id', $identifier)->orWhere('slug', $identifier)->first();
                if (!$item) {
                    session()->flash('error', 'Contractor not found.');
                    return;
                }
                $slug = $item->slug ?? (string) $item->id;
                return $this->redirect(route('contractor-reviews.show', $slug), navigate: true);
            } elseif ($type === 'broker') {
                $item = Broker::where('id', $identifier)->orWhere('slug', $identifier)->first();
                if (!$item) {
                    session()->flash('error', 'Broker not found.');
                    return;
                }
                $slug = $item->slug ?? (string) $item->id;
                return $this->redirect(route('broker-reviews.show', $slug), navigate: true);
            } elseif ($type === 'restaurant') {
                $item = Restaurant::where('id', $identifier)->orWhere('slug', $identifier)->first();
                if (!$item) {
                    session()->flash('error', 'Restaurant not found.');
                    return;
                }
                $slug = $item->slug ?? (string) $item->id;
                return $this->redirect(route('restaurant-reviews.show', $slug), navigate: true);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load details: ' . $e->getMessage());
            \Log::error('View details error: ' . $e->getMessage(), [
                'type' => $type,
                'identifier' => $identifier,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function loadReviews($type, $itemId)
    {
        try {
            if ($type === 'yacht') {
                $this->reviews = YachtReview::where('yacht_id', $itemId)
                    ->where('is_approved', true)
                    ->with(['user:id,first_name,last_name,profile_photo_path'])
                    ->orderByDesc('created_at')
                    ->limit(20)
                    ->get()
                    ->map(function ($review) {
                        $reviewArray = $review->toArray();
                        $reviewArray['created_at_formatted'] = $review->created_at->diffForHumans();
                        $reviewArray['created_at_date'] = $review->created_at->format('M d, Y');
                        if ($review->user) {
                            $reviewArray['user_name'] = $review->is_anonymous 
                                ? 'Anonymous Crew Member' 
                                : ($review->user->first_name . ' ' . $review->user->last_name);
                        } else {
                            $reviewArray['user_name'] = 'Anonymous';
                        }
                        return $reviewArray;
                    })
                    ->toArray();
            } else {
                $this->reviews = MarinaReview::where('marina_id', $itemId)
                    ->where('is_approved', true)
                    ->with(['user:id,first_name,last_name,profile_photo_path'])
                    ->orderByDesc('created_at')
                    ->limit(20)
                    ->get()
                    ->map(function ($review) {
                        $reviewArray = $review->toArray();
                        $reviewArray['created_at_formatted'] = $review->created_at->diffForHumans();
                        $reviewArray['created_at_date'] = $review->created_at->format('M d, Y');
                        if ($review->user) {
                            $reviewArray['user_name'] = $review->is_anonymous 
                                ? 'Anonymous Visitor' 
                                : ($review->user->first_name . ' ' . $review->user->last_name);
                        } else {
                            $reviewArray['user_name'] = 'Anonymous';
                        }
                        return $reviewArray;
                    })
                    ->toArray();
            }
        } catch (\Exception $e) {
            $this->reviews = [];
            \Log::error('Error loading reviews: ' . $e->getMessage());
        }
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailData = null;
        $this->reviews = [];
        $this->showReviewForm = false;
        $this->resetReviewForm();
    }

    public function openReviewForm()
    {
        $this->showReviewForm = true;
        $this->resetReviewForm();
        \Log::info('Review form opened', [
            'showReviewForm' => $this->showReviewForm,
            'detailData' => $this->detailData ? 'exists' : 'null'
        ]);
    }

    public function closeReviewForm()
    {
        $this->showReviewForm = false;
        $this->resetReviewForm();
    }

    public function resetReviewForm()
    {
        $this->reviewRating = 5;
        $this->reviewTitle = '';
        $this->reviewContent = '';
        $this->reviewPros = '';
        $this->reviewCons = '';
        $this->reviewRecommend = true;
        $this->reviewAnonymous = false;
        $this->reviewPhotos = [];
        $this->reviewError = '';
        $this->reviewMessage = '';
    }

    public function submitReview()
    {
        if (!auth()->check()) {
            $this->reviewError = 'You must be logged in to submit a review.';
            return;
        }

        $this->reviewLoading = true;
        $this->reviewError = '';
        $this->reviewMessage = '';

        try {
            $itemId = $this->detailData['id'];
            $user = auth()->user();

            // Validate the data based on type
            if ($this->detailType === 'yacht') {
                $validator = Validator::make([
                    'title' => $this->reviewTitle,
                    'review' => $this->reviewContent,
                    'pros' => $this->reviewPros,
                    'cons' => $this->reviewCons,
                    'overall_rating' => $this->reviewRating,
                    'would_recommend' => $this->reviewRecommend,
                    'is_anonymous' => $this->reviewAnonymous,
                ], [
                    'title' => 'required|string|max:255',
                    'review' => 'required|string|min:10',
                    'pros' => 'nullable|string',
                    'cons' => 'nullable|string',
                    'overall_rating' => 'required|integer|min:1|max:5',
                    'would_recommend' => 'boolean',
                    'is_anonymous' => 'boolean',
                ]);

                if ($validator->fails()) {
                    $this->reviewError = collect($validator->errors())->flatten()->implode(', ');
                    $this->reviewLoading = false;
                    return;
                }

                $data = $validator->validated();
                $data['yacht_id'] = $itemId;
                $data['user_id'] = $user->id;
                $data['is_verified'] = true;
                $data['is_approved'] = true;
                $review = YachtReview::create($data);
            } else {
                // Marina reviews use tips_tricks instead of pros/cons, and no would_recommend
                $validator = Validator::make([
                    'title' => $this->reviewTitle,
                    'review' => $this->reviewContent,
                    'tips_tricks' => $this->reviewPros ?: $this->reviewCons, // Use pros or cons as tips_tricks
                    'overall_rating' => $this->reviewRating,
                    'is_anonymous' => $this->reviewAnonymous,
                ], [
                    'title' => 'required|string|max:255',
                    'review' => 'required|string|min:10',
                    'tips_tricks' => 'nullable|string',
                    'overall_rating' => 'required|integer|min:1|max:5',
                    'is_anonymous' => 'boolean',
                ]);

                if ($validator->fails()) {
                    $this->reviewError = collect($validator->errors())->flatten()->implode(', ');
                    $this->reviewLoading = false;
                    return;
                }

                $data = $validator->validated();
                $data['marina_id'] = $itemId;
                $data['user_id'] = $user->id;
                $data['is_verified'] = true;
                $data['is_approved'] = true;
                $review = MarinaReview::create($data);
            }

            $this->reviewMessage = 'Review submitted successfully!';
            $this->closeReviewForm();
            
            // Reload reviews to show new review
            $this->loadReviews($this->detailType, $itemId);
            
            // Update review count in detail data
            if ($this->detailData) {
                $this->detailData['reviews_count'] = ($this->detailData['reviews_count'] ?? 0) + 1;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->reviewError = collect($e->errors())->flatten()->implode(', ');
        } catch (\Exception $e) {
            $this->reviewError = 'Error: ' . $e->getMessage();
            \Log::error('Review submission error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            $this->reviewLoading = false;
        }
    }

    protected function formatYachtData($yacht)
    {
        $yachtArray = $yacht->toArray();
        if ($yacht->cover_image) {
            if (str_starts_with($yacht->cover_image, 'http')) {
                $yachtArray['cover_image_url'] = $yacht->cover_image;
            } else {
                $yachtArray['cover_image_url'] = asset('storage/' . $yacht->cover_image);
            }
        }
        return $yachtArray;
    }

    protected function formatMarinaData($marina)
    {
        $marinaArray = $marina->toArray();
        if ($marina->cover_image) {
            if (str_starts_with($marina->cover_image, 'http')) {
                $marinaArray['cover_image_url'] = $marina->cover_image;
            } else {
                $marinaArray['cover_image_url'] = asset('storage/' . $marina->cover_image);
            }
        }
        return $marinaArray;
    }

    protected function formatContractorData($contractor)
    {
        $contractorArray = $contractor->toArray();
        if ($contractor->logo && !str_starts_with($contractor->logo, 'http')) {
            $contractorArray['logo_url'] = asset('storage/' . $contractor->logo);
        } elseif ($contractor->logo) {
            $contractorArray['logo_url'] = $contractor->logo;
        }
        return $contractorArray;
    }

    protected function formatBrokerData($broker)
    {
        $brokerArray = $broker->toArray();
        if ($broker->logo && !str_starts_with($broker->logo, 'http')) {
            $brokerArray['logo_url'] = asset('storage/' . $broker->logo);
        } elseif ($broker->logo) {
            $brokerArray['logo_url'] = $broker->logo;
        }
        return $brokerArray;
    }

    protected function formatRestaurantData($restaurant)
    {
        $restaurantArray = $restaurant->toArray();
        if ($restaurant->cover_image && !str_starts_with($restaurant->cover_image, 'http')) {
            $restaurantArray['cover_image_url'] = asset('storage/' . $restaurant->cover_image);
        } elseif ($restaurant->cover_image) {
            $restaurantArray['cover_image_url'] = $restaurant->cover_image;
        }
        return $restaurantArray;
    }

    public function render()
    {
        $yachtTypes = MasterData::getYachtTypes();
        
        // Get paginated data if showAll is true
        $yachtsPaginated = null;
        $marinasPaginated = null;
        $contractorsPaginated = null;
        $brokersPaginated = null;
        $restaurantsPaginated = null;
        
        if ($this->showAll) {
            switch ($this->activeTab) {
                case 'yachts':
                    $query = Yacht::query()->withCount('reviews');
                    if (!empty($this->searchQuery)) {
                        $query->where(function($q) {
                            $q->where('name', 'like', '%' . $this->searchQuery . '%')
                              ->orWhere('type', 'like', '%' . $this->searchQuery . '%')
                              ->orWhere('home_port', 'like', '%' . $this->searchQuery . '%')
                              ->orWhere('flag_registry', 'like', '%' . $this->searchQuery . '%');
                        });
                    }
                    $yachtsPaginated = $query->orderByDesc('created_at')
                        ->orderByDesc('rating_avg')
                        ->orderByDesc('reviews_count')
                        ->orderBy('name')
                        ->paginate(12);
                    break;
                case 'marinas':
                    $query = Marina::query()->withCount('reviews');
                    if (!empty($this->searchQuery)) {
                        $query->where(function($q) {
                            $q->where('name', 'like', '%' . $this->searchQuery . '%')
                              ->orWhere('city', 'like', '%' . $this->searchQuery . '%')
                              ->orWhere('country', 'like', '%' . $this->searchQuery . '%')
                              ->orWhere('type', 'like', '%' . $this->searchQuery . '%');
                        });
                    }
                    $marinasPaginated = $query->orderByDesc('created_at')
                        ->orderByDesc('rating_avg')
                        ->orderByDesc('reviews_count')
                        ->orderBy('name')
                        ->paginate(12);
                    break;
                case 'contractors':
                    $query = Contractor::query()->withCount('reviews');
                    if (!empty($this->searchQuery)) {
                        $query->where(function($q) {
                            $q->where('name', 'like', '%' . $this->searchQuery . '%')
                              ->orWhere('business_name', 'like', '%' . $this->searchQuery . '%')
                              ->orWhere('city', 'like', '%' . $this->searchQuery . '%')
                              ->orWhere('country', 'like', '%' . $this->searchQuery . '%');
                        });
                    }
                    $contractorsPaginated = $query->orderByDesc('created_at')
                        ->orderByDesc('rating_avg')
                        ->orderByDesc('reviews_count')
                        ->orderBy('name')
                        ->paginate(12);
                    break;
                case 'brokers':
                    $query = Broker::query()->withCount('reviews');
                    if (!empty($this->searchQuery)) {
                        $query->where(function($q) {
                            $q->where('name', 'like', '%' . $this->searchQuery . '%')
                              ->orWhere('business_name', 'like', '%' . $this->searchQuery . '%')
                              ->orWhere('primary_location', 'like', '%' . $this->searchQuery . '%');
                        });
                    }
                    $brokersPaginated = $query->orderByDesc('created_at')
                        ->orderByDesc('rating_avg')
                        ->orderByDesc('reviews_count')
                        ->orderBy('name')
                        ->paginate(12);
                    break;
                case 'restaurants':
                    $query = Restaurant::query()->withCount('reviews');
                    if (!empty($this->searchQuery)) {
                        $query->where(function($q) {
                            $q->where('name', 'like', '%' . $this->searchQuery . '%')
                              ->orWhere('city', 'like', '%' . $this->searchQuery . '%')
                              ->orWhere('country', 'like', '%' . $this->searchQuery . '%')
                              ->orWhere('type', 'like', '%' . $this->searchQuery . '%');
                        });
                    }
                    $restaurantsPaginated = $query->orderByDesc('created_at')
                        ->orderByDesc('rating_avg')
                        ->orderByDesc('reviews_count')
                        ->orderBy('name')
                        ->paginate(12);
                    break;
            }
        }
        
        return view('livewire.industry-review.industry-review-index', [
            'yachtTypes' => $yachtTypes,
            'yachtsPaginated' => $yachtsPaginated,
            'marinasPaginated' => $marinasPaginated,
            'contractorsPaginated' => $contractorsPaginated,
            'brokersPaginated' => $brokersPaginated,
            'restaurantsPaginated' => $restaurantsPaginated,
        ]);
    }
}
