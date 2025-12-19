@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp

<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        {{-- Back Button --}}
        <a href="{{ route('yacht-reviews.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to Yacht Reviews</span>
        </a>

        {{-- Yacht Header --}}
        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
            <div class="flex flex-col md:flex-row gap-6">
                @if($yacht->cover_image)
                    <img src="{{ Storage::url($yacht->cover_image) }}" alt="{{ $yacht->name }}" class="w-full md:w-64 h-48 object-cover rounded-lg">
                @endif
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $yacht->name }}</h1>
                    <div class="flex items-center gap-4 mb-4">
                        @if($yacht->rating_avg > 0)
                            <div class="flex items-center gap-2">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-6 h-6 {{ $i <= round($yacht->rating_avg) ? 'text-yellow-500 fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"></path>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-xl font-bold text-gray-900">{{ number_format($yacht->rating_avg, 1) }}</span>
                                <span class="text-gray-600">({{ $yacht->reviews_count }} {{ Str::plural('review', $yacht->reviews_count) }})</span>
                            </div>
                        @endif
                        @if($yacht->recommendation_percentage > 0)
                            <div class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                {{ $yacht->recommendation_percentage }}% Recommend
                            </div>
                        @endif
                    </div>
                    @php
                        $hasHeaderInfo = $yacht->type || $yacht->length_meters || $yacht->length_feet || $yacht->year_built || $yacht->crew_capacity || $yacht->current_crew_size;
                    @endphp
                    @if($hasHeaderInfo)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            @if($yacht->type)
                                <div>
                                    <span class="text-gray-500">Type:</span>
                                    <span class="font-semibold ml-2">{{ ucfirst(str_replace('_', ' ', $yacht->type)) }}</span>
                                </div>
                            @endif
                            @if($yacht->length_meters || $yacht->length_feet)
                                <div>
                                    <span class="text-gray-500">Length:</span>
                                    <span class="font-semibold ml-2">
                                        @if($yacht->length_meters) {{ number_format($yacht->length_meters, 1) }}m @elseif($yacht->length_feet) {{ number_format($yacht->length_feet, 0) }}ft @endif
                                    </span>
                                </div>
                            @endif
                            @if($yacht->year_built)
                                <div>
                                    <span class="text-gray-500">Built:</span>
                                    <span class="font-semibold ml-2">{{ $yacht->year_built }}</span>
                                </div>
                            @endif
                            @if($yacht->crew_capacity || $yacht->current_crew_size)
                                <div>
                                    <span class="text-gray-500">Crew:</span>
                                    <span class="font-semibold ml-2">{{ $yacht->current_crew_size ?: $yacht->crew_capacity }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                    <div class="mt-4 flex gap-3">
                        <a href="{{ route('yacht-reviews.create', ['yachtId' => $yacht->id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Write a Review
                        </a>
                        @if($yacht->gallery && $yacht->gallery->count() > 0)
                            <a href="{{ route('yacht-reviews.gallery', $yacht->slug) }}" class="inline-flex items-center px-4 py-2 bg-white border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                View Gallery
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Detailed Information Sections --}}
        <div class="space-y-6">
            {{-- Gallery Section --}}
            @if($yacht->gallery && $yacht->gallery->count() > 0)
                <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold text-gray-900">📸 GALLERY</h2>
                        <a href="{{ route('yacht-reviews.gallery', $yacht->slug) }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                            View {{ $yacht->gallery->count() }} {{ $yacht->gallery->count() === 1 ? 'photo' : 'photos' }} →
                        </a>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">Main exterior</span>
                        <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">Interior salon</span>
                        <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">Crew areas</span>
                        <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">More...</span>
                    </div>
                </div>
            @endif

            {{-- Basic Info Section --}}
            @php
                $hasBasicInfo = $yacht->type || $yacht->length_meters || $yacht->length_feet || $yacht->year_built || $yacht->flag_registry || $yacht->home_port || $yacht->builder;
            @endphp
            @if($hasBasicInfo)
                <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">ℹ️ BASIC INFO</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($yacht->type)
                            <div class="flex items-start gap-3">
                                <span class="text-gray-500 font-medium min-w-[120px]">Type:</span>
                                <span class="text-gray-900 font-semibold">{{ ucfirst(str_replace('_', ' ', $yacht->type)) }}</span>
                            </div>
                        @endif
                        @if($yacht->length_meters || $yacht->length_feet)
                            <div class="flex items-start gap-3">
                                <span class="text-gray-500 font-medium min-w-[120px]">Length:</span>
                                <span class="text-gray-900 font-semibold">
                                    @if($yacht->length_meters && $yacht->length_feet)
                                        {{ number_format($yacht->length_meters, 1) }}m / {{ number_format($yacht->length_feet, 0) }}ft
                                    @elseif($yacht->length_meters)
                                        {{ number_format($yacht->length_meters, 1) }}m
                                    @elseif($yacht->length_feet)
                                        {{ number_format($yacht->length_feet, 0) }}ft
                                    @endif
                                </span>
                            </div>
                        @endif
                        @if($yacht->year_built)
                            <div class="flex items-start gap-3">
                                <span class="text-gray-500 font-medium min-w-[120px]">Year Built:</span>
                                <span class="text-gray-900 font-semibold">{{ $yacht->year_built }}</span>
                            </div>
                        @endif
                        @if($yacht->flag_registry)
                            <div class="flex items-start gap-3">
                                <span class="text-gray-500 font-medium min-w-[120px]">Flag:</span>
                                <span class="text-gray-900 font-semibold">{{ $yacht->flag_registry }}</span>
                            </div>
                        @endif
                        @if($yacht->home_port)
                            <div class="flex items-start gap-3">
                                <span class="text-gray-500 font-medium min-w-[120px]">Home Port:</span>
                                <span class="text-gray-900 font-semibold">{{ $yacht->home_port }}</span>
                            </div>
                        @endif
                        @if($yacht->builder)
                            <div class="flex items-start gap-3">
                                <span class="text-gray-500 font-medium min-w-[120px]">Builder:</span>
                                <span class="text-gray-900 font-semibold">{{ $yacht->builder }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Ownership & Management Section --}}
            @php
                $hasOwnershipInfo = $yacht->owner_name || $yacht->ownership_type || $yacht->captain_name || $yacht->management_company || ($yacht->is_charter_available && $yacht->charter_rate);
            @endphp
            @if($hasOwnershipInfo)
                <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">👔 OWNERSHIP & MANAGEMENT</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($yacht->owner_name || $yacht->ownership_type)
                            <div class="flex items-start gap-3">
                                <span class="text-gray-500 font-medium min-w-[140px]">Owner:</span>
                                <span class="text-gray-900 font-semibold">
                                    @if($yacht->owner_name)
                                        {{ $yacht->owner_name }}
                                        @if($yacht->ownership_type === 'private')
                                            (Private Owner)
                                        @elseif($yacht->ownership_type === 'company')
                                            (Company)
                                        @elseif($yacht->ownership_type === 'charter_management')
                                            (Charter Management)
                                        @endif
                                    @elseif($yacht->ownership_type === 'private')
                                        Private Owner (Confidential)
                                    @endif
                                </span>
                            </div>
                        @endif
                        @if($yacht->captain_name)
                            <div class="flex items-start gap-3">
                                <span class="text-gray-500 font-medium min-w-[140px]">Captain:</span>
                                <span class="text-gray-900 font-semibold">{{ $yacht->captain_name }}</span>
                            </div>
                        @endif
                        @if($yacht->management_company)
                            <div class="flex items-start gap-3">
                                <span class="text-gray-500 font-medium min-w-[140px]">Management:</span>
                                <span class="text-gray-900 font-semibold">{{ $yacht->management_company }}</span>
                            </div>
                        @endif
                        @if($yacht->is_charter_available)
                            <div class="flex items-start gap-3">
                                <span class="text-gray-500 font-medium min-w-[140px]">Charter:</span>
                                <span class="text-gray-900 font-semibold">
                                    Yes
                                    @if($yacht->charter_rate)
                                        ({{ $yacht->charter_rate }})
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Crew Section --}}
            @php
                $hasCrewInfo = $yacht->current_crew_size || $yacht->crew_capacity || $yacht->crew_structure || $yacht->rotation_schedule;
            @endphp
            @if($hasCrewInfo)
                <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">👥 CREW</h2>
                    <div class="space-y-4">
                        @if($yacht->current_crew_size || $yacht->crew_capacity)
                            <div class="flex items-start gap-3">
                                <span class="text-gray-500 font-medium min-w-[160px]">Total Positions:</span>
                                <span class="text-gray-900 font-semibold">
                                    {{ $yacht->current_crew_size ?: $yacht->crew_capacity }}
                                </span>
                            </div>
                        @endif
                        @if($yacht->crew_structure)
                            <div class="flex items-start gap-3">
                                <span class="text-gray-500 font-medium min-w-[160px]">Structure:</span>
                                <div class="text-gray-900 font-semibold">
                                    <p class="whitespace-pre-line">{{ $yacht->crew_structure }}</p>
                                </div>
                            </div>
                        @endif
                        @if($yacht->rotation_schedule)
                            <div class="flex items-start gap-3">
                                <span class="text-gray-500 font-medium min-w-[160px]">Rotation:</span>
                                <span class="text-gray-900 font-semibold">{{ $yacht->rotation_schedule }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Filters and Sort --}}
        <div class="bg-white shadow-lg rounded-xl p-4 border border-gray-200">
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <div class="flex gap-4">
                    <select wire:model.live="sortBy" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="helpful">Most Helpful</option>
                        <option value="recent">Most Recent</option>
                        <option value="rating">Highest Rating</option>
                    </select>
                    <select wire:model.live="filterRating" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Ratings</option>
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                </div>
                <div class="text-sm text-gray-600">
                    Showing {{ $reviews->count() }} of {{ $yacht->reviews_count }} reviews
                </div>
            </div>
        </div>

        {{-- Reviews List --}}
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
                                        <div class="font-semibold text-gray-900">Anonymous Crew Member</div>
                                        <div class="text-sm text-gray-500">
                                            @if($review->position_held) {{ $review->position_held }} • @endif
                                            @if($review->work_start_date && $review->work_end_date)
                                                {{ $review->work_start_date->format('M Y') }} - {{ $review->work_end_date->format('M Y') }}
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center overflow-hidden">
                                        @if($review->user->profile_photo_path)
                                            <img src="{{ asset('storage/' . $review->user->profile_photo_path) }}" 
                                                 alt="{{ $review->user->name }}" 
                                                 class="w-full h-full object-cover rounded-full"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <span class="text-blue-600 font-semibold hidden">{{ Str::substr($review->user->first_name, 0, 1) }}{{ Str::substr($review->user->last_name, 0, 1) }}</span>
                                        @else
                                            <span class="text-blue-600 font-semibold">{{ Str::substr($review->user->first_name, 0, 1) }}{{ Str::substr($review->user->last_name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $review->user->name }}</div>
                                        <div class="text-sm text-gray-500">
                                            @if($review->position_held) {{ $review->position_held }} • @endif
                                            @if($review->work_start_date && $review->work_end_date)
                                                {{ $review->work_start_date->format('M Y') }} - {{ $review->work_end_date->format('M Y') }}
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $review->overall_rating ? 'text-yellow-500 fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"></path>
                                    </svg>
                                @endfor
                            </div>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $review->title }}</h3>
                        <p class="text-gray-700 mb-4 whitespace-pre-line">{{ $review->review }}</p>

                        @if($review->pros || $review->cons)
                            <div class="grid md:grid-cols-2 gap-4 mb-4">
                                @if($review->pros)
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <h4 class="font-semibold text-green-900 mb-2 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Pros
                                        </h4>
                                        <p class="text-green-800 whitespace-pre-line">{{ $review->pros }}</p>
                                    </div>
                                @endif
                                @if($review->cons)
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                        <h4 class="font-semibold text-red-900 mb-2 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            Cons
                                        </h4>
                                        <p class="text-red-800 whitespace-pre-line">{{ $review->cons }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if($review->photos->count() > 0)
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                @foreach($review->photos as $photo)
                                    <img src="{{ Storage::url($photo->photo_path) }}" alt="{{ $photo->caption }}" class="w-full h-32 object-cover rounded-lg">
                                @endforeach
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="flex items-center gap-4">
                                <button wire:click="voteHelpful({{ $review->id }})" class="flex items-center gap-2 text-sm text-gray-600 hover:text-green-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                                    </svg>
                                    Helpful ({{ $review->helpful_count }})
                                </button>
                                <button wire:click="voteNotHelpful({{ $review->id }})" class="flex items-center gap-2 text-sm text-gray-600 hover:text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"></path>
                                    </svg>
                                    Not Helpful ({{ $review->not_helpful_count }})
                                </button>
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $review->created_at->diffForHumans() }}
                            </div>
                        </div>

                        @if($review->managementResponse)
                            <div class="mt-4 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                                <h4 class="font-semibold text-blue-900 mb-2">Management Response</h4>
                                <p class="text-blue-800 whitespace-pre-line">{{ $review->managementResponse->response }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $reviews->links() }}
            </div>
        @else
            <div class="bg-white shadow-lg rounded-xl p-12 text-center">
                <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No reviews yet</h3>
                <p class="text-gray-600 mb-6">Be the first to review this yacht!</p>
                <a href="{{ route('yacht-reviews.create', ['yachtId' => $yacht->id]) }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Write the First Review
                </a>
            </div>
        @endif

    </div>
</div>

