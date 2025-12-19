@php
    use Illuminate\Support\Str;
@endphp

<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        {{-- Navigation Links --}}
        <div class="bg-white shadow-lg rounded-xl p-4 border border-gray-200 mb-6">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('yacht-reviews.index') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm transition-all bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200 shadow-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Yacht Reviews</span>
                </a>
                <a href="{{ route('marina-reviews.index') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm transition-all bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200 shadow-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Marina Reviews</span>
                </a>
                <a href="{{ route('contractor-reviews.index') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm transition-all bg-blue-600 text-white border-2 border-blue-600 shadow-md hover:bg-blue-700 hover:shadow-lg">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Contractor Reviews</span>
                </a>
                <a href="{{ route('broker-reviews.index') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm transition-all bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200 shadow-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Broker Reviews</span>
                </a>
                <a href="{{ route('restaurant-reviews.index') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm transition-all bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200 shadow-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                    <span>Restaurant Reviews</span>
                </a>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Contractor Reviews</h1>
                    <p class="text-sm text-gray-600">Find and review marine contractors and service providers</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    @auth
                    <button wire:click="toggleMyReviews"
                       class="inline-flex items-center justify-center px-6 py-3 font-semibold rounded-lg shadow-md transition-all transform hover:scale-105 {{ $showMyReviews ? 'bg-gradient-to-r from-green-600 to-green-700 text-white hover:from-green-700 hover:to-green-800' : 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 hover:from-gray-200 hover:to-gray-300 border-2 border-gray-300' }}">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        My Reviews
                    </button>
                    @endauth
                    <a href="{{ route('contractor-reviews.create') }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg shadow-md hover:from-blue-700 hover:to-blue-800 transition-all transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Write a Review
                    </a>
                    <button wire:click="clearFilters"
                            class="inline-flex items-center justify-center px-4 py-3 border-2 border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Reset Filters
                    </button>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-100 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Search</label>
                        <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search contractors..."
                               class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 bg-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Category</label>
                        <select wire:model.live="category" class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 bg-white text-sm">
                            <option value="">All Categories</option>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Min Rating</label>
                        <select wire:model.live="min_rating" class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 bg-white text-sm">
                            <option value="">Any</option>
                            <option value="4">4+ Stars</option>
                            <option value="3">3+ Stars</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Contractors Grid --}}
        {{-- My Reviews Section --}}
        @if(isset($showMyReviews) && $showMyReviews && isset($reviews))
            @if($reviews->count() > 0)
                <div class="space-y-6">
                    @foreach($reviews as $review)
                        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    @if($review->is_anonymous)
                                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">Anonymous</div>
                                            <div class="text-sm text-gray-500">{{ $review->created_at->format('M Y') }}</div>
                                        </div>
                                    @else
                                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold">{{ substr($review->user->first_name, 0, 1) }}{{ substr($review->user->last_name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $review->user->first_name }} {{ $review->user->last_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $review->created_at->format('M Y') }}</div>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= $review->overall_rating ? 'text-yellow-500 fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"></path>
                                        </svg>
                                    @endfor
                                </div>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $review->title }}</h3>
                                <div class="flex items-center gap-4 mb-3">
                                    @if($review->contractor)
                                        <a href="{{ route('contractor-reviews.show', $review->contractor->slug) }}" class="text-blue-600 hover:text-blue-700 font-medium">
                                            {{ $review->contractor->name }}
                                        </a>
                                    @else
                                        <span class="text-gray-500 font-medium">Contractor Deleted</span>
                                    @endif
                                    @if($review->service_type)
                                        <span class="text-sm text-gray-500">• {{ $review->service_type }}</span>
                                    @endif
                                </div>
                                <p class="text-gray-700 leading-relaxed">{{ Str::limit($review->review, 300) }}</p>
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <div class="flex items-center gap-4 text-sm text-gray-600">
                                    @if($review->service_cost)
                                        <span>Cost: {{ $review->service_cost }}</span>
                                    @endif
                                </div>
                                @if($review->contractor)
                                    <a href="{{ route('contractor-reviews.show', $review->contractor->slug) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        View Full Review →
                                    </a>
                                @else
                                    <span class="text-gray-400 text-sm">Contractor no longer available</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $reviews->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No reviews found</h3>
                    <p class="text-gray-600 mb-6">You haven't written any contractor reviews yet.</p>
                    <a href="{{ route('contractor-reviews.create') }}" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Write Your First Review
                    </a>
                </div>
            @endif
        @elseif($contractors->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($contractors as $contractor)
                        <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col">
                            <div class="relative h-48 bg-gradient-to-br from-blue-400 to-indigo-600 overflow-hidden group">
                                @if($contractor->logo)
                                    @if(str_starts_with($contractor->logo, 'http'))
                                        <img src="{{ $contractor->logo }}" alt="{{ $contractor->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    @elseif($contractor->logo)
                                        @php
                                            $logoUrl = str_starts_with($contractor->logo, 'http') 
                                                ? $contractor->logo 
                                                : asset('storage/' . $contractor->logo);
                                        @endphp
                                        <img src="{{ $logoUrl }}" alt="{{ $contractor->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" onerror="this.style.display='none'">
                                    @endif
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-5 flex-1 flex flex-col">
                                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-1">{{ $contractor->name }}</h3>
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $contractor->business_name }}</p>
                                <div class="flex items-center mb-3">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= round($contractor->rating_avg) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-sm font-semibold text-gray-700">{{ number_format($contractor->rating_avg, 1) }}</span>
                                    <span class="ml-1 text-xs text-gray-500">({{ $contractor->reviews_count }})</span>
                                </div>
                                <div class="mt-auto">
                                    <a href="{{ route('contractor-reviews.show', $contractor->slug) }}"
                                       class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $contractors->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-600">No contractors found.</p>
                </div>
            @endif
        </div>
    </div>
</div>
