<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\Attributes\Layout;
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
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class StatisticsDashboard extends Component
{
    public function mount()
    {
        // Only admins can access
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized access');
        }
    }

    public function render()
    {
        $stats = [
            'yachts' => [
                'total' => Yacht::count(),
                'reviews' => YachtReview::where('is_approved', true)->count(),
                'avg_rating' => round(YachtReview::where('is_approved', true)->avg('overall_rating') ?? 0, 2),
                'recommend_rate' => round((YachtReview::where('is_approved', true)->where('would_recommend', true)->count() / max(YachtReview::where('is_approved', true)->count(), 1)) * 100, 1),
            ],
            'marinas' => [
                'total' => Marina::count(),
                'reviews' => MarinaReview::where('is_approved', true)->count(),
                'avg_rating' => round(MarinaReview::where('is_approved', true)->avg('overall_rating') ?? 0, 2),
            ],
            'contractors' => [
                'total' => Contractor::count(),
                'reviews' => ContractorReview::where('is_approved', true)->count(),
                'avg_rating' => round(ContractorReview::where('is_approved', true)->avg('overall_rating') ?? 0, 2),
                'recommend_rate' => round((ContractorReview::where('is_approved', true)->where('would_recommend', true)->count() / max(ContractorReview::where('is_approved', true)->count(), 1)) * 100, 1),
            ],
            'brokers' => [
                'total' => Broker::count(),
                'reviews' => BrokerReview::where('is_approved', true)->count(),
                'avg_rating' => round(BrokerReview::where('is_approved', true)->avg('overall_rating') ?? 0, 2),
                'recommend_rate' => round((BrokerReview::where('is_approved', true)->where('would_recommend', true)->count() / max(BrokerReview::where('is_approved', true)->count(), 1)) * 100, 1),
                'use_again_rate' => round((BrokerReview::where('is_approved', true)->where('would_use_again', true)->count() / max(BrokerReview::where('is_approved', true)->count(), 1)) * 100, 1),
            ],
            'restaurants' => [
                'total' => Restaurant::count(),
                'reviews' => RestaurantReview::where('is_approved', true)->count(),
                'avg_rating' => round(RestaurantReview::where('is_approved', true)->avg('overall_rating') ?? 0, 2),
                'recommend_rate' => round((RestaurantReview::where('is_approved', true)->where('would_recommend', true)->count() / max(RestaurantReview::where('is_approved', true)->count(), 1)) * 100, 1),
            ],
            'moderation' => [
                'flagged' => YachtReview::where('is_flagged', true)->count() +
                            MarinaReview::where('is_flagged', true)->count() +
                            ContractorReview::where('is_flagged', true)->count() +
                            BrokerReview::where('is_flagged', true)->count() +
                            RestaurantReview::where('is_flagged', true)->count(),
                'pending' => YachtReview::where('is_approved', false)->where('is_flagged', false)->count() +
                            MarinaReview::where('is_approved', false)->where('is_flagged', false)->count() +
                            ContractorReview::where('is_approved', false)->where('is_flagged', false)->count() +
                            BrokerReview::where('is_approved', false)->where('is_flagged', false)->count() +
                            RestaurantReview::where('is_approved', false)->where('is_flagged', false)->count(),
            ],
        ];

        $totalReviews = $stats['yachts']['reviews'] + $stats['marinas']['reviews'] + 
                       $stats['contractors']['reviews'] + $stats['brokers']['reviews'] + 
                       $stats['restaurants']['reviews'];

        return view('livewire.industry-review.statistics-dashboard', [
            'stats' => $stats,
            'totalReviews' => $totalReviews,
        ]);
    }
}
