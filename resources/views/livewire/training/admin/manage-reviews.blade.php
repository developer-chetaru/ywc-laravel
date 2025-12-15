<div>
    @role('super_admin')
    <main class="flex-1 overflow-y-auto p-4">
        <div class="min-h-[calc(100vh-100px)] bg-gray-100 pb-4">
            <h2 class="text-[#0053FF] text-[30px] font-semibold mb-4">Manage Reviews</h2>

            <form class="flex flex-col sm:flex-row gap-3 sm:gap-[16px] mb-4">
                <div class="relative w-full sm:w-[39%]">
                    <input type="text" placeholder="Search reviews..." 
                        wire:model.live.debounce.300ms="search"
                        class="text-[#616161] placeholder-[#616161] w-full py-3 px-4 rounded-lg border border-gray-200 focus:outline-none focus:border focus:!border-blue-200 text-sm !pl-[40px] font-medium !bg-[#ffffff]">
                    <button class="!absolute !left-3 !top-1/2 !transform !-translate-y-1/2" type="button">
                        <img src="/images/search.svg" alt="">
                    </button>
                </div>

                <div class="relative w-full sm:w-auto">
                    <select wire:model.live="statusFilter"
                        class="appearance-none text-[#616161] py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-blue-200 text-sm font-medium bg-white cursor-pointer px-3 pr-10 w-full sm:min-w-[130px]">
                        <option value="all">All Status</option>
                        <option value="approved">Approved</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                <div class="relative w-full sm:w-auto">
                    <select wire:model.live="ratingFilter"
                        class="appearance-none text-[#616161] py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-blue-200 text-sm font-medium bg-white cursor-pointer px-3 pr-10 w-full sm:min-w-[130px]">
                        <option value="">All Ratings</option>
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                </div>
            </form>

            @if (session()->has('success'))
                <div class="w-full bg-blue-500 text-white text-center py-2 rounded-md mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Card View (All Screen Sizes) -->
            <div class="space-y-4 md:space-y-0 md:grid md:grid-cols-2 xl:grid-cols-3 md:gap-4">
                @forelse($reviews as $review)
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                    <!-- User Info -->
                    <div class="mb-3 pb-3 border-b border-gray-200">
                        <div class="font-semibold text-base text-[#020202]">{{ $review->user->first_name }} {{ $review->user->last_name }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $review->user->email }}</div>
                        @if($review->is_verified_student)
                            <span class="inline-block mt-1 text-xs text-green-600">✓ Verified</span>
                        @endif
                    </div>

                    <!-- Course Info -->
                    <div class="mb-3 pb-3 border-b border-gray-200">
                        <div class="text-xs text-gray-500 mb-1">Course</div>
                        <div class="font-semibold text-sm text-[#020202]">{{ $review->providerCourse->certification->name }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $review->providerCourse->provider->name }}</div>
                    </div>

                    <!-- Rating -->
                    <div class="mb-3 pb-3 border-b border-gray-200">
                        <div class="text-xs text-gray-500 mb-1">Rating</div>
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="{{ $i <= $review->rating_overall ? 'text-yellow-400' : 'text-gray-400' }} text-lg">★</span>
                            @endfor
                            <span class="text-gray-500 text-sm ml-1">({{ $review->rating_overall }}/5)</span>
                        </div>
                    </div>

                    <!-- Review Text -->
                    <div class="mb-3 pb-3 border-b border-gray-200">
                        <div class="text-xs text-gray-500 mb-1">Review</div>
                        <div class="text-sm text-gray-700">{{ $review->review_text ?? 'No text' }}</div>
                    </div>

                    <!-- Status and Actions -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            @if($review->is_approved)
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Approved</span>
                            @else
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Pending</span>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            @if(!$review->is_approved)
                                <button wire:click="approve({{ $review->id }})" 
                                    class="flex-1 px-3 py-2 bg-green-600 text-white rounded text-xs hover:bg-green-700 font-medium">
                                    Approve
                                </button>
                            @else
                                <button wire:click="reject({{ $review->id }})" 
                                    class="flex-1 px-3 py-2 bg-yellow-600 text-white rounded text-xs hover:bg-yellow-700 font-medium">
                                    Reject
                                </button>
                            @endif
                            <button wire:click="delete({{ $review->id }})" 
                                wire:confirm="Are you sure?" 
                                class="flex-1 px-3 py-2 bg-red-600 text-white rounded text-xs hover:bg-red-700 font-medium">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-lg shadow-sm p-8 text-center text-gray-500 md:col-span-2 xl:col-span-3">
                    No reviews found
                </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $reviews->links() }}
            </div>
        </div>
    </main>
    @endrole
</div>
