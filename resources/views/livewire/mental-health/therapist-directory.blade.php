<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('mental-health.dashboard') }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-800">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Find a Therapist</h1>
            <p class="mt-2 text-gray-600">Browse our network of qualified mental health professionals</p>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           placeholder="Name, specialization..." 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Specialization</label>
                    <select wire:model.live="specialization" class="w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All</option>
                        <option value="anxiety">Anxiety</option>
                        <option value="depression">Depression</option>
                        <option value="trauma">Trauma</option>
                        <option value="relationships">Relationships</option>
                        <option value="career">Career Stress</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                    <select wire:model.live="language" class="w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All</option>
                        <option value="English">English</option>
                        <option value="Spanish">Spanish</option>
                        <option value="French">French</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select wire:model.live="sortBy" class="w-full rounded-md border-gray-300 shadow-sm">
                        <option value="relevance">Relevance</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                        <option value="experience">Most Experience</option>
                        <option value="rating">Highest Rated</option>
                        <option value="newest">Newest</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Results -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($therapists as $therapist)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                @if($therapist->profile_photo_path)
                                    <img src="{{ asset('storage/' . $therapist->profile_photo_path) }}" 
                                         alt="{{ $therapist->user->first_name }}" 
                                         class="w-16 h-16 rounded-full object-cover">
                                @else
                                    <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-2xl">{{ substr($therapist->user->first_name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $therapist->user->first_name }} {{ $therapist->user->last_name }}
                                    </h3>
                                    @if($therapist->years_experience)
                                        <p class="text-sm text-gray-500">{{ $therapist->years_experience }} years experience</p>
                                    @endif
                                </div>
                            </div>
                            @if($therapist->is_featured)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Featured</span>
                            @endif
                        </div>

                        @if($therapist->specializations)
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach(array_slice($therapist->specializations, 0, 3) as $spec)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($spec) }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        @if($therapist->biography)
                            <p class="text-sm text-gray-600 mb-4 line-clamp-3">
                                {{ Str::limit($therapist->biography, 120) }}
                            </p>
                        @endif

                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-2xl font-bold text-gray-900">£{{ number_format($therapist->base_hourly_rate ?? 0, 2) }}</p>
                                <p class="text-xs text-gray-500">per hour</p>
                            </div>
                            @if($therapist->rating > 0)
                                <div class="text-right">
                                    <div class="flex items-center">
                                        <span class="text-yellow-400">★</span>
                                        <span class="ml-1 font-semibold">{{ number_format($therapist->rating, 1) }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500">{{ $therapist->total_reviews }} reviews</p>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-between mb-2">
                            @if($therapist->languages_spoken)
                                <div class="flex flex-wrap gap-1">
                                    @foreach(array_slice($therapist->languages_spoken, 0, 2) as $lang)
                                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">
                                            {{ $lang }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('mental-health.book-session') }}?therapist={{ $therapist->id }}" 
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-md transition">
                                Book Session
                            </a>
                            <button wire:click="addToFavorites({{ $therapist->id }})" 
                                    class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition"
                                    title="Add to favorites">
                                ♡
                            </button>
                        </div>
                        
                        <div class="mt-2 text-center">
                            <span class="text-xs text-green-600 font-medium">✓ Available now</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12">
                    <p class="text-gray-500 text-lg">No therapists found matching your criteria.</p>
                    <button wire:click="$set('search', ''); $set('specialization', ''); $set('language', '')" 
                            class="mt-4 text-blue-600 hover:text-blue-800">
                        Clear filters
                    </button>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $therapists->links() }}
        </div>
    </div>
</div>
