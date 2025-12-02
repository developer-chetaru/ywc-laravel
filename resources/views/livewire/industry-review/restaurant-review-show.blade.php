<div class="py-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $restaurant->name }}</h1>
                    <p class="text-gray-600">{{ $restaurant->city }}, {{ $restaurant->country }}</p>
                </div>
                <a href="{{ route('restaurant-reviews.create', $restaurant->id) }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Write Review
                </a>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= round($restaurant->rating_avg) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    @endfor
                </div>
                <span class="text-lg font-semibold">{{ number_format($restaurant->rating_avg, 1) }}</span>
                <span class="text-gray-600">({{ $restaurant->reviews_count }} reviews)</span>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-xl p-6">
            <div class="flex gap-4 mb-6">
                <select wire:model.live="sortBy" class="px-4 py-2 border rounded-lg">
                    <option value="helpful">Most Helpful</option>
                    <option value="recent">Most Recent</option>
                    <option value="rating">Highest Rating</option>
                </select>
                <select wire:model.live="filterRating" class="px-4 py-2 border rounded-lg">
                    <option value="">All Ratings</option>
                    <option value="5">5 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="2">2 Stars</option>
                    <option value="1">1 Star</option>
                </select>
            </div>

            <div class="space-y-6">
                @foreach($reviews as $review)
                    <div class="border-b pb-6 last:border-b-0">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <h3 class="font-semibold text-lg">{{ $review->title }}</h3>
                                <p class="text-sm text-gray-600">
                                    {{ $review->is_anonymous ? 'Anonymous' : ($review->user->first_name . ' ' . $review->user->last_name) }}
                                    • {{ $review->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->overall_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endfor
                            </div>
                        </div>
                        <p class="text-gray-700 mb-3">{{ $review->review }}</p>
                        @if($review->crew_tips)
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-3 mb-3">
                                <p class="text-sm text-blue-800"><strong>Crew Tips:</strong> {{ $review->crew_tips }}</p>
                            </div>
                        @endif
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <button wire:click="voteHelpful({{ $review->id }})" class="hover:text-blue-600">
                                Helpful ({{ $review->helpful_count }})
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
</div>
