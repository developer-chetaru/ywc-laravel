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

        {{-- Marina Header --}}
        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
            <div class="flex flex-col md:flex-row gap-6">
                @if($marina->cover_image)
                    <img src="{{ str_starts_with($marina->cover_image, 'http') ? $marina->cover_image : Storage::url($marina->cover_image) }}" alt="{{ $marina->name }}" class="w-full md:w-64 h-48 object-cover rounded-lg">
                @endif
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $marina->name }}</h1>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="text-gray-600">
                            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ $marina->city }}, {{ $marina->country }}
                        </div>
                        @if($marina->rating_avg > 0)
                            <div class="flex items-center gap-2">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-6 h-6 {{ $i <= round($marina->rating_avg) ? 'text-yellow-500 fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"></path>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-xl font-bold text-gray-900">{{ number_format($marina->rating_avg, 1) }}</span>
                                <span class="text-gray-600">({{ $marina->reviews_count }} {{ Str::plural('review', $marina->reviews_count) }})</span>
                            </div>
                        @endif
                    </div>
                    <div class="mt-4 flex gap-3">
                        <a href="{{ route('marina-reviews.create', $marina->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Write a Review
                        </a>
                        @if($marina->gallery && $marina->gallery->count() > 0)
                            <a href="{{ route('marina-reviews.gallery', $marina->slug) }}" class="inline-flex items-center px-4 py-2 bg-white border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
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

        {{-- Filters --}}
        <div class="bg-white shadow-lg rounded-xl p-4 border border-gray-200">
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <select wire:model.live="sortBy" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="helpful">Most Helpful</option>
                    <option value="recent">Most Recent</option>
                    <option value="rating">Highest Rating</option>
                </select>
            </div>
        </div>

        {{-- Reviews List --}}
        @if($reviews->count() > 0)
            @foreach($reviews as $review)
                <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200 mb-6">
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
                                    <div class="text-sm text-gray-500">{{ $review->visit_date?->format('M Y') }}</div>
                                </div>
                            @else
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold">{{ Str::substr($review->user->first_name, 0, 1) }}{{ Str::substr($review->user->last_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $review->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $review->visit_date?->format('M Y') }}</div>
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

                    @if($review->tips_tricks)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <h4 class="font-semibold text-blue-900 mb-2">Tips & Tricks</h4>
                            <p class="text-blue-800 whitespace-pre-line">{{ $review->tips_tricks }}</p>
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
                        </div>
                        <div class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</div>
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
                <a href="{{ route('marina-reviews.create', $marina->id) }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Write the First Review
                </a>
            </div>
        @endif

    </div>
</div>

