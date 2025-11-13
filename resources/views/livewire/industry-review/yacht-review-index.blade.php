@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        {{-- Header Section --}}
        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Yacht Reviews</h1>
                    <p class="text-sm text-gray-600">Browse and review yachts from crew members worldwide</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
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
                            <option value="motor_yacht">Motor Yacht</option>
                            <option value="sailing_yacht">Sailing Yacht</option>
                            <option value="explorer">Explorer</option>
                            <option value="catamaran">Catamaran</option>
                            <option value="other">Other</option>
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

        {{-- Yachts Grid --}}
        @if($yachts->count() > 0)
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
                                <a href="{{ route('yacht-reviews.create', $yacht->id) }}"
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

