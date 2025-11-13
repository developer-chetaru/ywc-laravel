<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Yacht;
use App\Models\Marina;
use App\Models\YachtReview;
use App\Models\MarinaReview;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

#[Layout('layouts.app')]
class IndustryReviewIndex extends Component
{
    use WithFileUploads;

    public $activeTab = 'yachts';
    public $yachts = [];
    public $marinas = [];
    public $loading = false;
    public $showYachtModal = false;
    
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

    public $types = [
        'motor_yacht' => 'Motor Yacht',
        'sailing_yacht' => 'Sailing Yacht',
        'explorer' => 'Explorer',
        'catamaran' => 'Catamaran',
        'other' => 'Other',
    ];

    public $statuses = [
        'charter' => 'Charter',
        'private' => 'Private',
        'both' => 'Both',
    ];

    public function mount()
    {
        // Check if tab is specified in query string
        $tab = request()->query('tab', 'yachts');
        if ($tab === 'marinas') {
            $this->activeTab = 'marinas';
            $this->loadMarinas();
        } else {
            $this->activeTab = 'yachts';
            $this->loadYachts();
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        if ($tab === 'yachts') {
            $this->loadYachts();
        } else {
            $this->loadMarinas();
        }
    }

    public function loadYachts()
    {
        $this->loading = true;
        try {
            $yachts = Yacht::query()
                ->withCount('reviews')
                ->orderByDesc('rating_avg')
                ->orderByDesc('reviews_count')
                ->orderBy('name')
                ->limit(12)
                ->get();

            $this->yachts = $yachts->map(function ($yacht) {
                $yachtArray = $yacht->toArray();
                if ($yacht->cover_image) {
                    $yachtArray['cover_image_url'] = asset('storage/' . $yacht->cover_image);
                }
                return $yachtArray;
            })->toArray();
        } catch (\Exception $e) {
            $this->yachts = [];
        } finally {
            $this->loading = false;
        }
    }

    public function loadMarinas()
    {
        $this->loading = true;
        try {
            $marinas = Marina::query()
                ->withCount('reviews')
                ->orderByDesc('rating_avg')
                ->orderByDesc('reviews_count')
                ->orderBy('name')
                ->limit(12)
                ->get();

            $this->marinas = $marinas->map(function ($marina) {
                $marinaArray = $marina->toArray();
                if ($marina->cover_image) {
                    $marinaArray['cover_image_url'] = asset('storage/' . $marina->cover_image);
                }
                return $marinaArray;
            })->toArray();
        } catch (\Exception $e) {
            $this->marinas = [];
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
            $this->detailLoading = true;
            $this->detailType = $type;
            $this->showDetailModal = true;
            $this->detailData = null;
            $this->detailError = null;

            // Convert identifier to integer if it's numeric
            if (is_numeric($identifier)) {
                $identifier = (int) $identifier;
            }

            if ($type === 'yacht') {
                // Try to find by ID first (most reliable)
                $item = Yacht::where('id', $identifier)
                    ->withCount('reviews')
                    ->first();
                
                // If not found by ID, try slug
                if (!$item) {
                    $item = Yacht::where('slug', $identifier)
                        ->withCount('reviews')
                        ->first();
                }
            } else {
                // Try to find by ID first (most reliable)
                $item = Marina::where('id', $identifier)
                    ->withCount('reviews')
                    ->first();
                
                // If not found by ID, try slug
                if (!$item) {
                    $item = Marina::where('slug', $identifier)
                        ->withCount('reviews')
                        ->first();
                }
            }
            
            if (!$item) {
                throw new \Exception("{$type} not found with identifier: {$identifier}");
            }
            
            $itemArray = $item->toArray();
            if ($item->cover_image) {
                $itemArray['cover_image_url'] = asset('storage/' . $item->cover_image);
            }
            
            // Ensure slug exists
            if (empty($itemArray['slug'])) {
                $itemArray['slug'] = (string) $item->id;
            }
            
            $this->detailData = $itemArray;
            
            // Load reviews
            $this->loadReviews($type, $item->id);
        } catch (\Exception $e) {
            $this->detailError = 'Failed to load details: ' . $e->getMessage();
            \Log::error('View details error: ' . $e->getMessage(), [
                'type' => $type,
                'identifier' => $identifier,
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            $this->detailLoading = false;
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

    public function render()
    {
        return view('livewire.industry-review.industry-review-index');
    }
}
