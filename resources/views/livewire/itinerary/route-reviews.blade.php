<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Reviews & Ratings</h3>
            @if($totalReviews > 0)
                <p class="text-sm text-gray-600 mt-1">
                    {{ $totalReviews }} review{{ $totalReviews !== 1 ? 's' : '' }} • 
                    Average: <span class="font-semibold">{{ $averageRating }}</span>/5.0
                </p>
            @endif
        </div>
        @if(!$showForm)
            <button
                wire:click="openForm"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"
            >
                Write Review
            </button>
        @endif
    </div>

    @if(session('review_message'))
        <div class="p-3 text-sm bg-green-50 text-green-700 rounded-lg">
            {{ session('review_message') }}
        </div>
    @endif

    {{-- Review Form --}}
    @if($showForm)
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
            <h4 class="font-semibold text-gray-800 mb-4">
                {{ $editingId ? 'Edit Review' : 'Write a Review' }}
            </h4>

            <form wire:submit.prevent="saveReview" class="space-y-4">
                {{-- Rating --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                    <div class="flex gap-2">
                        @for($i = 5; $i >= 1; $i--)
                            <button
                                type="button"
                                wire:click="$set('rating', {{ $i }})"
                                class="text-3xl transition-colors {{ $rating >= $i ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400"
                            >
                                ⭐
                            </button>
                        @endfor
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Selected: {{ $rating }}/5</p>
                </div>

                {{-- Comment --}}
                <div>
                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Comment</label>
                    <textarea
                        id="comment"
                        wire:model="comment"
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Share your experience with this route..."
                    ></textarea>
                    @error('comment')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex gap-3">
                    <button
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                    >
                        {{ $editingId ? 'Update Review' : 'Submit Review' }}
                    </button>
                    <button
                        type="button"
                        wire:click="closeForm"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300"
                    >
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Rating Distribution --}}
    @if($totalReviews > 0)
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <h4 class="font-semibold text-gray-800 mb-3">Rating Distribution</h4>
            <div class="space-y-2">
                @for($i = 5; $i >= 1; $i--)
                    @php
                        $count = $ratingCounts[$i] ?? 0;
                        $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                    @endphp
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-700 w-8">{{ $i }}⭐</span>
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div
                                class="bg-yellow-400 h-2 rounded-full transition-all"
                                style="width: {{ $percentage }}%"
                            ></div>
                        </div>
                        <span class="text-sm text-gray-600 w-12 text-right">{{ $count }}</span>
                    </div>
                @endfor
            </div>
        </div>
    @endif

    {{-- Reviews List --}}
    @if($reviews->isEmpty())
        <div class="text-center py-12 bg-gray-50 border border-gray-200 rounded-lg">
            <p class="text-gray-500">No reviews yet. Be the first to review this route!</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($reviews as $review)
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3 flex-1">
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                @if($review->user->profile_photo_path)
                                    <img src="{{ asset('storage/' . $review->user->profile_photo_path) }}" alt="{{ $review->user->first_name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-gray-600 text-sm font-medium">
                                        {{ strtoupper(substr($review->user->first_name, 0, 1) . substr($review->user->last_name, 0, 1)) }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-gray-800">
                                        {{ $review->user->first_name }} {{ $review->user->last_name }}
                                    </span>
                                    <div class="flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}">⭐</span>
                                        @endfor
                                    </div>
                                    <span class="text-sm text-gray-500">
                                        {{ $review->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                @if($review->comment)
                                    <p class="text-gray-700 whitespace-pre-wrap">{{ $review->comment }}</p>
                                @endif
                                @if(!empty($review->photos))
                                    <div class="flex gap-2 mt-3">
                                        @foreach($review->photos as $photo)
                                            <img src="{{ asset('storage/' . $photo) }}" alt="Review photo" class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        @can('update', $review)
                            <div class="flex gap-2">
                                <button
                                    wire:click="editReview({{ $review->id }})"
                                    class="text-sm text-blue-600 hover:text-blue-800"
                                >
                                    Edit
                                </button>
                                <button
                                    wire:click="deleteReview({{ $review->id }})"
                                    wire:confirm="Are you sure you want to delete this review?"
                                    class="text-sm text-red-600 hover:text-red-800"
                                >
                                    Delete
                                </button>
                            </div>
                        @endcan
                    </div>
                </div>
            @endforeach

            {{ $reviews->links() }}
        </div>
    @endif
</div>

