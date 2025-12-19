@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp

<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        {{-- Navigation Links --}}
        <div class="bg-white shadow-lg rounded-xl p-4 border border-gray-200 mb-6">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('yacht-reviews.index') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm transition-all bg-blue-600 text-white border-2 border-blue-600 shadow-md hover:bg-blue-700 hover:shadow-lg">
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
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm transition-all bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200 shadow-sm">
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

        {{-- Header Section --}}
        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Yacht Reviews</h1>
                    <p class="text-sm text-gray-600">Browse and review yachts from crew members worldwide</p>
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
                    <a href="{{ route('yacht-reviews.create') }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg shadow-md hover:from-blue-700 hover:to-blue-800 transition-all transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Write a Review
                    </a>
                    <button wire:click="clearFilters"
                            class="inline-flex items-center justify-center px-4 py-3 border-2 border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset Filters
                    </button>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 text-green-700 px-4 py-3 rounded-md shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filters Section --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-100">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    {{-- Search --}}
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" 
                                   wire:model.live.debounce.500ms="search"
                                   placeholder="Search yachts..."
                                   class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-sm">
                        </div>
                    </div>

                    {{-- Type --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Type</label>
                        <select wire:model.live="type"
                                class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-sm">
                            <option value="">All Types</option>
                            @foreach($yachtTypes as $yachtType)
                                <option value="{{ $yachtType->code }}">{{ $yachtType->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Status</label>
                        <select wire:model.live="status"
                                class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-sm">
                            <option value="">All</option>
                            <option value="charter">Charter</option>
                            <option value="private">Private</option>
                            <option value="both">Both</option>
                        </select>
                    </div>

                    {{-- Min Rating --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Min Rating</label>
                        <select wire:model.live="min_rating"
                                class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-sm">
                            <option value="">Any</option>
                            <option value="4">4+ Stars</option>
                            <option value="3">3+ Stars</option>
                            <option value="2">2+ Stars</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

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
                                    @if($review->yacht)
                                        <a href="{{ route('yacht-reviews.show', $review->yacht->slug) }}" class="text-blue-600 hover:text-blue-700 font-medium">
                                            {{ $review->yacht->name }}
                                        </a>
                                    @else
                                        <span class="text-gray-500 font-medium">Yacht Deleted</span>
                                    @endif
                                    @if($review->position_held)
                                        <span class="text-sm text-gray-500">• {{ $review->position_held }}</span>
                                    @endif
                                </div>
                                <p class="text-gray-700 leading-relaxed">{{ Str::limit($review->review, 300) }}</p>
                            </div>

                            @if($review->pros || $review->cons)
                                <div class="grid md:grid-cols-2 gap-4 mb-4">
                                    @if($review->pros)
                                        <div class="bg-green-50 p-3 rounded-lg">
                                            <div class="text-sm font-semibold text-green-800 mb-1">Pros</div>
                                            <p class="text-sm text-green-700">{{ Str::limit($review->pros, 150) }}</p>
                                        </div>
                                    @endif
                                    @if($review->cons)
                                        <div class="bg-red-50 p-3 rounded-lg">
                                            <div class="text-sm font-semibold text-red-800 mb-1">Cons</div>
                                            <p class="text-sm text-red-700">{{ Str::limit($review->cons, 150) }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <div class="flex items-center gap-4 text-sm text-gray-600">
                                    @if($review->work_start_date && $review->work_end_date)
                                        <span>{{ $review->work_start_date->format('M Y') }} - {{ $review->work_end_date->format('M Y') }}</span>
                                    @endif
                                    @if($review->would_recommend !== null)
                                        <span class="font-semibold {{ $review->would_recommend ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $review->would_recommend ? '✓ Would Recommend' : '✗ Would Not Recommend' }}
                                        </span>
                                    @endif
                                </div>
                                @if($review->yacht)
                                    <a href="{{ route('yacht-reviews.show', $review->yacht->slug) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        View Full Review →
                                    </a>
                                @else
                                    <span class="text-gray-400 text-sm">Yacht no longer available</span>
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
                    <p class="text-gray-600 mb-6">You haven't written any yacht reviews yet.</p>
                    <a href="{{ route('yacht-reviews.create') }}" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Write Your First Review
                    </a>
                </div>
            @endif
        @elseif($yachts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($yachts as $yacht)
                    <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col">
                        {{-- Cover Image --}}
                        <div class="relative h-48 bg-gradient-to-br from-blue-400 to-indigo-600 overflow-hidden group">
                            @if($yacht->cover_image)
                                <img src="{{ Storage::url($yacht->cover_image) }}" 
                                     alt="{{ $yacht->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-20 h-20 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            @endif
                            {{-- Rating Badge --}}
                            @if($yacht->rating_avg > 0)
                                <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-full flex items-center gap-1 shadow-lg">
                                    <svg class="w-4 h-4 text-yellow-500 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"></path>
                                    </svg>
                                    <span class="text-sm font-bold text-gray-900">{{ number_format($yacht->rating_avg, 1) }}</span>
                                </div>
                            @endif
                            {{-- Recommendation Badge --}}
                            @if($yacht->recommendation_percentage >= 90)
                                <div class="absolute top-3 right-3 bg-green-500 text-white px-2 py-1 rounded-full text-xs font-bold shadow-lg">
                                    {{ $yacht->recommendation_percentage }}% Recommend
                                </div>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="p-5 flex-1 flex flex-col space-y-3">
                            <h2 class="text-xl font-bold text-gray-900 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                <a href="{{ route('yacht-reviews.show', $yacht->slug) }}">
                                    {{ $yacht->name }}
                                </a>
                            </h2>

                            {{-- Yacht Details Grid --}}
                            <dl class="grid grid-cols-2 gap-3 text-xs">
                                <div class="bg-gray-50 rounded-lg p-2">
                                    <dt class="font-semibold text-gray-500 uppercase tracking-wide mb-1">Type</dt>
                                    <dd class="text-gray-900 font-medium">{{ ucfirst(str_replace('_', ' ', $yacht->type)) }}</dd>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-2">
                                    <dt class="font-semibold text-gray-500 uppercase tracking-wide mb-1">Length</dt>
                                    <dd class="text-gray-900 font-medium">
                                        @if($yacht->length_meters)
                                            {{ number_format($yacht->length_meters, 1) }}m
                                        @elseif($yacht->length_feet)
                                            {{ number_format($yacht->length_feet, 0) }}ft
                                        @else
                                            —
                                        @endif
                                    </dd>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-2">
                                    <dt class="font-semibold text-gray-500 uppercase tracking-wide mb-1">Built</dt>
                                    <dd class="text-gray-900 font-medium">{{ $yacht->year_built ?: '—' }}</dd>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-2">
                                    <dt class="font-semibold text-gray-500 uppercase tracking-wide mb-1">Crew</dt>
                                    <dd class="text-gray-900 font-medium">{{ $yacht->crew_capacity ?: '—' }}</dd>
                                </div>
                            </dl>

                            {{-- Reviews Count & Recommendation --}}
                            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                <div class="flex items-center gap-2 text-xs text-gray-600">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                                    </svg>
                                    <span class="font-medium">{{ $yacht->reviews_count }} {{ Str::plural('review', $yacht->reviews_count) }}</span>
                                </div>
                                @if($yacht->recommendation_percentage > 0)
                                    <div class="flex items-center gap-1 text-xs font-semibold {{ $yacht->recommendation_percentage >= 80 ? 'text-green-600' : ($yacht->recommendation_percentage >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>{{ $yacht->recommendation_percentage }}%</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="border-t border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 p-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('yacht-reviews.show', $yacht->slug) }}"
                                   class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Reviews
                                </a>
                                <a href="{{ route('yacht-reviews.create', ['yachtId' => $yacht->id]) }}"
                                   class="px-4 py-2 text-sm font-medium text-blue-700 bg-white border border-blue-300 rounded-lg hover:bg-blue-50 transition-all">
                                    Review
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $yachts->links() }}
            </div>
        @else
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No yachts found</h3>
                <p class="text-gray-600 mb-6">Try adjusting your filters or search terms.</p>
                <button wire:click="clearFilters" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Clear Filters
                </button>
            </div>
        @endif
    </div>
</div>

