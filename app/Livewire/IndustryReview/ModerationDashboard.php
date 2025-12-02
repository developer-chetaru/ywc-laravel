<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\YachtReview;
use App\Models\MarinaReview;
use App\Models\ContractorReview;
use App\Models\BrokerReview;
use App\Models\RestaurantReview;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class ModerationDashboard extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $filterType = 'all'; // all, yacht, marina, contractor, broker, restaurant
    public $filterStatus = 'flagged'; // flagged, pending, resolved
    public $selectedReview = null;
    public $showReviewModal = false;
    public $moderationAction = '';
    public $moderationNotes = '';

    public function mount()
    {
        // Only admins can access
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized access');
        }
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function viewReview($type, $id)
    {
        $this->selectedReview = $this->getReview($type, $id);
        $this->showReviewModal = true;
    }

    public function getReview($type, $id)
    {
        return match($type) {
            'yacht' => YachtReview::with(['yacht', 'user'])->find($id),
            'marina' => MarinaReview::with(['marina', 'user'])->find($id),
            'contractor' => ContractorReview::with(['contractor', 'user'])->find($id),
            'broker' => BrokerReview::with(['broker', 'user'])->find($id),
            'restaurant' => RestaurantReview::with(['restaurant', 'user'])->find($id),
            default => null,
        };
    }

    public function approveReview($type, $id)
    {
        $review = $this->getReview($type, $id);
        if ($review) {
            $review->update([
                'is_approved' => true,
                'is_flagged' => false,
                'flag_reason' => null,
            ]);
            
            // Update rating stats
            if (method_exists($review, 'yacht') && $review->yacht) {
                $review->yacht->updateRatingStats();
            } elseif (method_exists($review, 'marina') && $review->marina) {
                $review->marina->updateRatingStats();
            } elseif (method_exists($review, 'contractor') && $review->contractor) {
                $review->contractor->updateRatingStats();
            } elseif (method_exists($review, 'broker') && $review->broker) {
                $review->broker->updateRatingStats();
            } elseif (method_exists($review, 'restaurant') && $review->restaurant) {
                $review->restaurant->updateRatingStats();
            }

            session()->flash('success', 'Review approved successfully.');
            $this->showReviewModal = false;
        }
    }

    public function rejectReview($type, $id)
    {
        $review = $this->getReview($type, $id);
        if ($review) {
            $review->update([
                'is_approved' => false,
                'is_flagged' => true,
                'flag_reason' => $this->moderationNotes ?: 'Rejected by moderator',
            ]);

            session()->flash('success', 'Review rejected.');
            $this->showReviewModal = false;
            $this->moderationNotes = '';
        }
    }

    public function deleteReview($type, $id)
    {
        $review = $this->getReview($type, $id);
        if ($review) {
            $review->delete();
            session()->flash('success', 'Review deleted successfully.');
            $this->showReviewModal = false;
        }
    }

    public function closeModal()
    {
        $this->showReviewModal = false;
        $this->selectedReview = null;
        $this->moderationNotes = '';
    }

    public function render()
    {
        $flaggedReviews = collect();

        if ($this->filterType === 'all' || $this->filterType === 'yacht') {
            $yachtReviews = YachtReview::where('is_flagged', true)
                ->with(['yacht', 'user'])
                ->latest()
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'type' => 'yacht',
                        'entity_name' => $review->yacht->name ?? 'Unknown Yacht',
                        'user_name' => $review->is_anonymous ? 'Anonymous' : ($review->user->first_name . ' ' . $review->user->last_name),
                        'title' => $review->title,
                        'rating' => $review->overall_rating,
                        'flag_reason' => $review->flag_reason,
                        'created_at' => $review->created_at,
                        'review' => $review,
                    ];
                });
            $flaggedReviews = $flaggedReviews->merge($yachtReviews);
        }

        if ($this->filterType === 'all' || $this->filterType === 'marina') {
            $marinaReviews = MarinaReview::where('is_flagged', true)
                ->with(['marina', 'user'])
                ->latest()
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'type' => 'marina',
                        'entity_name' => $review->marina->name ?? 'Unknown Marina',
                        'user_name' => $review->is_anonymous ? 'Anonymous' : ($review->user->first_name . ' ' . $review->user->last_name),
                        'title' => $review->title,
                        'rating' => $review->overall_rating,
                        'flag_reason' => $review->flag_reason,
                        'created_at' => $review->created_at,
                        'review' => $review,
                    ];
                });
            $flaggedReviews = $flaggedReviews->merge($marinaReviews);
        }

        if ($this->filterType === 'all' || $this->filterType === 'contractor') {
            $contractorReviews = ContractorReview::where('is_flagged', true)
                ->with(['contractor', 'user'])
                ->latest()
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'type' => 'contractor',
                        'entity_name' => $review->contractor->name ?? 'Unknown Contractor',
                        'user_name' => $review->is_anonymous ? 'Anonymous' : ($review->user->first_name . ' ' . $review->user->last_name),
                        'title' => $review->title,
                        'rating' => $review->overall_rating,
                        'flag_reason' => $review->flag_reason,
                        'created_at' => $review->created_at,
                        'review' => $review,
                    ];
                });
            $flaggedReviews = $flaggedReviews->merge($contractorReviews);
        }

        if ($this->filterType === 'all' || $this->filterType === 'broker') {
            $brokerReviews = BrokerReview::where('is_flagged', true)
                ->with(['broker', 'user'])
                ->latest()
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'type' => 'broker',
                        'entity_name' => $review->broker->name ?? 'Unknown Broker',
                        'user_name' => $review->is_anonymous ? 'Anonymous' : ($review->user->first_name . ' ' . $review->user->last_name),
                        'title' => $review->title,
                        'rating' => $review->overall_rating,
                        'flag_reason' => $review->flag_reason,
                        'created_at' => $review->created_at,
                        'review' => $review,
                    ];
                });
            $flaggedReviews = $flaggedReviews->merge($brokerReviews);
        }

        if ($this->filterType === 'all' || $this->filterType === 'restaurant') {
            $restaurantReviews = RestaurantReview::where('is_flagged', true)
                ->with(['restaurant', 'user'])
                ->latest()
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'type' => 'restaurant',
                        'entity_name' => $review->restaurant->name ?? 'Unknown Restaurant',
                        'user_name' => $review->is_anonymous ? 'Anonymous' : ($review->user->first_name . ' ' . $review->user->last_name),
                        'title' => $review->title,
                        'rating' => $review->overall_rating,
                        'flag_reason' => $review->flag_reason,
                        'created_at' => $review->created_at,
                        'review' => $review,
                    ];
                });
            $flaggedReviews = $flaggedReviews->merge($restaurantReviews);
        }

        // Sort by created_at descending
        $flaggedReviews = $flaggedReviews->sortByDesc('created_at')->values();

        // Paginate manually
        $perPage = 15;
        $currentPage = request()->get('page', 1);
        $items = $flaggedReviews->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $flaggedReviews->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.industry-review.moderation-dashboard', [
            'flaggedReviews' => $paginated,
        ]);
    }
}
