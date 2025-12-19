<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\YachtReview;
use App\Models\MarinaReview;
use App\Models\ContractorReview;
use App\Models\BrokerReview;
use App\Models\RestaurantReview;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class MyReviews extends Component
{

    #[Url]
    public ?string $category = null;

    #[Url(as: 'page')]
    public int $currentPage = 1;

    public function mount(): void
    {
        if (!Auth::check()) {
            $this->redirect(route('login'), navigate: true);
        }
    }

    public function updating($name, $value): void
    {
        if ($name === 'category') {
            $this->currentPage = 1;
        }
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    public function nextPage()
    {
        $this->currentPage++;
    }

    public function render()
    {
        $user = Auth::user();
        
        $yachtReviews = collect();
        $marinaReviews = collect();
        $contractorReviews = collect();
        $brokerReviews = collect();
        $restaurantReviews = collect();

        if (!$this->category || $this->category === 'yachts') {
            $yachtReviews = YachtReview::where('user_id', $user->id)
                ->with(['yacht:id,name,slug'])
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'type' => 'yacht',
                        'title' => $review->title,
                        'review' => $review->review,
                        'rating' => $review->overall_rating,
                        'created_at' => $review->created_at,
                        'item_name' => $review->yacht->name ?? 'Unknown Yacht',
                        'item_slug' => $review->yacht->slug ?? $review->yacht_id,
                        'item_id' => $review->yacht_id,
                    ];
                });
        }

        if (!$this->category || $this->category === 'marinas') {
            $marinaReviews = MarinaReview::where('user_id', $user->id)
                ->with(['marina:id,name,slug'])
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'type' => 'marina',
                        'title' => $review->title,
                        'review' => $review->review,
                        'rating' => $review->overall_rating,
                        'created_at' => $review->created_at,
                        'item_name' => $review->marina->name ?? 'Unknown Marina',
                        'item_slug' => $review->marina->slug ?? $review->marina_id,
                        'item_id' => $review->marina_id,
                    ];
                });
        }

        if (!$this->category || $this->category === 'contractors') {
            $contractorReviews = ContractorReview::where('user_id', $user->id)
                ->with(['contractor:id,name,slug'])
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'type' => 'contractor',
                        'title' => $review->title,
                        'review' => $review->review,
                        'rating' => $review->overall_rating,
                        'created_at' => $review->created_at,
                        'item_name' => $review->contractor->name ?? 'Unknown Contractor',
                        'item_slug' => $review->contractor->slug ?? $review->contractor_id,
                        'item_id' => $review->contractor_id,
                    ];
                });
        }

        if (!$this->category || $this->category === 'brokers') {
            $brokerReviews = BrokerReview::where('user_id', $user->id)
                ->with(['broker:id,name,slug'])
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'type' => 'broker',
                        'title' => $review->title,
                        'review' => $review->review,
                        'rating' => $review->overall_rating,
                        'created_at' => $review->created_at,
                        'item_name' => $review->broker->name ?? 'Unknown Broker',
                        'item_slug' => $review->broker->slug ?? $review->broker_id,
                        'item_id' => $review->broker_id,
                    ];
                });
        }

        if (!$this->category || $this->category === 'restaurants') {
            $restaurantReviews = RestaurantReview::where('user_id', $user->id)
                ->with(['restaurant:id,name,slug'])
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'type' => 'restaurant',
                        'title' => $review->title,
                        'review' => $review->review,
                        'rating' => $review->overall_rating,
                        'created_at' => $review->created_at,
                        'item_name' => $review->restaurant->name ?? 'Unknown Restaurant',
                        'item_slug' => $review->restaurant->slug ?? $review->restaurant_id,
                        'item_id' => $review->restaurant_id,
                    ];
                });
        }

        // Combine all reviews and sort by created_at
        $allReviews = $yachtReviews
            ->concat($marinaReviews)
            ->concat($contractorReviews)
            ->concat($brokerReviews)
            ->concat($restaurantReviews)
            ->sortByDesc('created_at')
            ->values();

        // Paginate manually
        $perPage = 10;
        $items = $allReviews->slice(($this->currentPage - 1) * $perPage, $perPage)->values();
        $total = $allReviews->count();
        $lastPage = (int) ceil($total / $perPage);

        return view('livewire.industry-review.my-reviews', [
            'reviews' => $items,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $this->currentPage,
            'lastPage' => $lastPage,
            'yachtCount' => $yachtReviews->count(),
            'marinaCount' => $marinaReviews->count(),
            'contractorCount' => $contractorReviews->count(),
            'brokerCount' => $brokerReviews->count(),
            'restaurantCount' => $restaurantReviews->count(),
        ]);
    }
}

