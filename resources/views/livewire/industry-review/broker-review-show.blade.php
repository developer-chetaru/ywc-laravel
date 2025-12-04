@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp

<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        {{-- Back Button --}}
        <a href="{{ route('industryreview.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to Industry Reviews</span>
        </a>

        {{-- Broker Header --}}
        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
            <div class="flex flex-col md:flex-row gap-6">
                @if($broker->logo)
                    <img src="{{ str_starts_with($broker->logo, 'http') ? $broker->logo : Storage::url($broker->logo) }}" alt="{{ $broker->name }}" class="w-full md:w-64 h-48 object-cover rounded-lg">
                @endif
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $broker->name }}</h1>
                    @if($broker->business_name)
                        <p class="text-gray-600 mb-4">{{ $broker->business_name }}</p>
                    @endif
                    <div class="flex items-center gap-4 mb-4">
                        @if($broker->rating_avg > 0)
                            <div class="flex items-center gap-2">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-6 h-6 {{ $i <= round($broker->rating_avg) ? 'text-yellow-500 fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"></path>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-xl font-bold text-gray-900">{{ number_format($broker->rating_avg, 1) }}</span>
                                <span class="text-gray-600">({{ $broker->reviews_count }} {{ Str::plural('review', $broker->reviews_count) }})</span>
                            </div>
                        @endif
                    </div>
                    <div class="mt-4 flex gap-3">
                        <a href="{{ route('broker-reviews.create', $broker->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Write a Review
                        </a>
                        @if($broker->gallery && $broker->gallery->count() > 0)
                            <a href="{{ route('broker-reviews.gallery', $broker->slug) }}" class="inline-flex items-center px-4 py-2 bg-white border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
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

        {{-- Red Flag Warnings --}}
        @php
            $redFlags = $broker->red_flags;
        @endphp
        @if(count($redFlags) > 0)
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-red-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-red-800 mb-2">⚠️ WARNING - MULTIPLE COMPLAINTS</h3>
                        <p class="text-red-700 mb-3">This broker has the following concerns reported by crew members:</p>
                        <ul class="list-disc list-inside space-y-1 text-red-700">
                            @foreach($redFlags as $flag)
                                <li>
                                    <strong>{{ ucfirst(str_replace('_', ' ', $flag['type'])) }}:</strong> 
                                    {{ $flag['message'] }}
                                    @if(isset($flag['count']))
                                        ({{ $flag['count'] }} reviews)
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        <p class="text-red-800 font-semibold mt-3">PROCEED WITH CAUTION - Read reviews carefully before engaging</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Filters --}}
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
            </div>
        </div>

        {{-- Reviews List --}}
        <div class="space-y-6">

            @if($reviews->count() > 0)
                @foreach($reviews as $review)
                    <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
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
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <button wire:click="voteHelpful({{ $review->id }})" class="hover:text-blue-600">
                                Helpful ({{ $review->helpful_count }})
                            </button>
                        </div>
                    </div>
                @endforeach

                @if($reviews->hasPages())
                    <div class="mt-6">
                        {{ $reviews->links() }}
                    </div>
                @endif
            @else
                <div class="bg-white shadow-lg rounded-xl p-12 text-center">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No reviews yet</h3>
                    <a href="{{ route('broker-reviews.create', $broker->id) }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Write the First Review
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>
