<div>
    <div class="max-w-8xl mx-auto px-3 sm:px-4 lg:px-4">
        <div class="bg-white p-4 sm:p-6 lg:p-10 rounded-lg shadow-md">
            {{-- Header --}}
            <div class="mb-4 sm:mb-6 lg:mb-8">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2 sm:mb-3 leading-tight">My Reviews</h1>
                <p class="text-sm sm:text-base lg:text-lg text-gray-600 leading-relaxed">
                    View and manage all your reviews across different categories.
                </p>
            </div>

            {{-- Category Filter --}}
            <div class="mb-6">
                <div class="flex flex-wrap gap-2 sm:gap-3">
                    <button
                        wire:click="$set('category', null)"
                        class="px-4 py-2 rounded-lg font-semibold text-sm transition-all {{ !$category ? 'bg-blue-600 text-white border-2 border-blue-600 shadow-md' : 'bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200' }}">
                        All ({{ $yachtCount + $marinaCount + $contractorCount + $brokerCount + $restaurantCount }})
                    </button>
                    <button
                        wire:click="$set('category', 'yachts')"
                        class="px-4 py-2 rounded-lg font-semibold text-sm transition-all {{ $category === 'yachts' ? 'bg-blue-600 text-white border-2 border-blue-600 shadow-md' : 'bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200' }}">
                        Yachts ({{ $yachtCount }})
                    </button>
                    <button
                        wire:click="$set('category', 'marinas')"
                        class="px-4 py-2 rounded-lg font-semibold text-sm transition-all {{ $category === 'marinas' ? 'bg-blue-600 text-white border-2 border-blue-600 shadow-md' : 'bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200' }}">
                        Marinas ({{ $marinaCount }})
                    </button>
                    <button
                        wire:click="$set('category', 'contractors')"
                        class="px-4 py-2 rounded-lg font-semibold text-sm transition-all {{ $category === 'contractors' ? 'bg-blue-600 text-white border-2 border-blue-600 shadow-md' : 'bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200' }}">
                        Contractors ({{ $contractorCount }})
                    </button>
                    <button
                        wire:click="$set('category', 'brokers')"
                        class="px-4 py-2 rounded-lg font-semibold text-sm transition-all {{ $category === 'brokers' ? 'bg-blue-600 text-white border-2 border-blue-600 shadow-md' : 'bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200' }}">
                        Brokers ({{ $brokerCount }})
                    </button>
                    <button
                        wire:click="$set('category', 'restaurants')"
                        class="px-4 py-2 rounded-lg font-semibold text-sm transition-all {{ $category === 'restaurants' ? 'bg-blue-600 text-white border-2 border-blue-600 shadow-md' : 'bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200' }}">
                        Restaurants ({{ $restaurantCount }})
                    </button>
                </div>
            </div>

            {{-- Reviews List --}}
            @if(count($reviews) > 0)
                <div class="space-y-4">
                    @foreach($reviews as $review)
                        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6 hover:shadow-lg transition-shadow">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold uppercase">
                                            {{ ucfirst($review['type']) }}
                                        </span>
                                        <h3 class="text-lg font-bold text-gray-900">{{ $review['title'] }}</h3>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                                        <span class="font-semibold">{{ $review['item_name'] }}</span>
                                        <span>•</span>
                                        <span>{{ $review['created_at']->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 mb-3">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-5 h-5 {{ $i <= $review['rating'] ? 'text-yellow-400 fill-current' : 'text-gray-300' }}" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        @endfor
                                        <span class="ml-2 text-sm font-semibold text-gray-600">{{ $review['rating'] }}/5</span>
                                    </div>
                                    <p class="text-gray-700 mb-4 line-clamp-3">{{ $review['review'] }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                                @php
                                    $routeName = match($review['type']) {
                                        'yacht' => 'yacht-reviews.show',
                                        'marina' => 'marina-reviews.show',
                                        'contractor' => 'contractor-reviews.show',
                                        'broker' => 'broker-reviews.show',
                                        'restaurant' => 'restaurant-reviews.show',
                                        default => null,
                                    };
                                @endphp
                                @if($routeName)
                                    <a href="{{ route($routeName, $review['item_slug']) }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                        View {{ ucfirst($review['type']) }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($total > $perPage)
                    <div class="mt-6">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div class="text-sm text-gray-600">
                                Showing {{ (($currentPage - 1) * $perPage) + 1 }} to {{ min($currentPage * $perPage, $total) }} of {{ $total }} reviews
                            </div>
                            <div class="flex gap-2">
                                @if($currentPage > 1)
                                    <button wire:click="previousPage" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold text-sm transition-colors">
                                        Previous
                                    </button>
                                @endif
                                @if($currentPage < $lastPage)
                                    <button wire:click="nextPage" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold text-sm transition-colors">
                                        Next
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="bg-white rounded-xl shadow-lg p-6 sm:p-12 text-center">
                    <svg class="w-16 h-16 sm:w-24 sm:h-24 text-gray-300 mx-auto mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                    </svg>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">No reviews found</h3>
                    <p class="text-sm sm:text-base text-gray-600 mb-4">
                        @if($category)
                            You haven't written any reviews for {{ ucfirst($category) }} yet.
                        @else
                            You haven't written any reviews yet. Start sharing your experiences!
                        @endif
                    </p>
                    <a href="{{ route('industryreview.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition-all">
                        Browse Reviews
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

