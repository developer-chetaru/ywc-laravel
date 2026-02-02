<div>
    <main class="flex-1">
        <div class="w-full bg-white p-5 rounded-md pb-10">
            {{-- Breadcrumb --}}
            <nav class="mb-4 text-sm">
                <a href="{{ route('training.courses') }}" class="text-[#0053FF] hover:underline">Training & Resources</a>
                <span class="mx-2">/</span>
                <a href="{{ route('training.certification.detail', $course->certification->slug) }}" class="text-[#0053FF] hover:underline">
                    {{ $course->certification->name }}
                </a>
                <span class="mx-2">/</span>
                <span class="text-gray-600">{{ $course->provider->name }}</span>
            </nav>

            {{-- Header --}}
            <div class="mb-4 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h1 class="text-[#0053FF] text-3xl font-bold mb-1">{{ $course->certification->name }}</h1>
                    <p class="text-gray-600">{{ $course->provider->name }}</p>
                </div>
                <div class="text-left sm:text-right">
                    @if($course->rating_avg > 0)
                        <div class="flex items-center sm:justify-end gap-1 text-sm text-gray-700">
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= round($course->rating_avg) ? 'text-yellow-400' : 'text-gray-300' }}" 
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l-2.8-2.034a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span>({{ $course->review_count }} reviews)</span>
                        </div>
                    @else
                        <span class="text-gray-500 text-sm">No reviews yet</span>
                    @endif
                </div>
            </div>

            <hr class="my-4" />

            {{-- Price / Duration / Format --}}
            <div class="grid md:grid-cols-2 gap-8 mb-8">
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold">Price</h2>
                    @if($isYwcMember)
                        <div class="text-3xl font-bold text-[#0053FF]">£{{ number_format($course->ywc_price, 2) }}</div>
                        <div class="text-sm text-gray-500 line-through">£{{ number_format($course->price, 2) }}</div>
                        <div class="text-sm text-green-600">Save £{{ number_format($course->savings_amount, 2) }} with YWC</div>
                    @else
                        <div class="text-3xl font-bold">£{{ number_format($course->price, 2) }}</div>
                    @endif

                    <div class="grid grid-cols-2 gap-4 mt-6">
                        <div>
                            <div class="text-sm text-gray-600">Duration</div>
                            <div class="font-semibold">{{ $course->duration_days }} day(s)</div>
                            @if($course->duration_hours)
                                <div class="text-sm text-gray-500">{{ $course->duration_hours }} hours</div>
                            @endif
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">Format</div>
                            <div class="font-semibold">{{ ucfirst(str_replace('-', ' ', $course->format)) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">Language</div>
                            <div class="font-semibold">{{ $course->language_of_instruction }}</div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    @if($course->materials_included || $course->accommodation_included || $course->meals_included || $course->parking_included || $course->transport_included)
                        <div>
                            <h2 class="text-lg font-semibold mb-2">What's Included</h2>
                            <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                                @if($course->materials_included)
                                    @foreach($course->materials_included as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                @endif
                                @if($course->accommodation_included)
                                    <li>Accommodation included</li>
                                @endif
                                @if($course->meals_included)
                                    <li>{{ $course->meals_details ?? 'Meals included' }}</li>
                                @endif
                                @if($course->parking_included)
                                    <li>Parking included</li>
                                @endif
                                @if($course->transport_included)
                                    <li>Transport included</li>
                                @endif
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Course Structure --}}
            @if($course->daily_schedule)
                <div class="mb-8">
                    <h2 class="text-lg font-semibold mb-2">Course Structure</h2>
                    <div class="space-y-2">
                        @foreach($course->daily_schedule as $day => $schedule)
                            <div class="border-l-4 border-[#0053FF] pl-4">
                                <div class="font-semibold">{{ $day }}</div>
                                <div class="text-sm text-gray-600">{{ $schedule }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Locations --}}
            @if($course->locations->count() > 0)
                <div class="mb-8">
                    <h2 class="text-lg font-semibold mb-2">Locations</h2>
                    <div class="space-y-2">
                        @foreach($course->locations as $location)
                            <div>
                                <div class="font-semibold">{{ $location->name }}</div>
                                <div class="text-sm text-gray-600">{{ $location->city }}, {{ $location->country }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Upcoming Dates --}}
            @if($course->schedules->count() > 0)
                <div class="mb-8">
                    <h2 class="text-lg font-semibold mb-2">Upcoming Dates</h2>
                    <div class="space-y-2">
                        @foreach($course->schedules->take(5) as $schedule)
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center border p-3 rounded">
                                <div>
                                    <div class="font-semibold">{{ $schedule->start_date->format('M d, Y') }}</div>
                                    @if($schedule->end_date)
                                        <div class="text-sm text-gray-600">to {{ $schedule->end_date->format('M d, Y') }}</div>
                                    @endif
                                </div>
                                @if($schedule->available_spots)
                                    <div class="text-sm mt-2 sm:mt-0">
                                        {{ $schedule->available_spots - $schedule->booked_spots }} spots available
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Reviews --}}
            <div class="mb-8">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-lg font-semibold">Reviews ({{ $course->reviews->count() }})</h2>
                    @auth
                        <a href="{{ route('training.review.submit', $course->id) }}" 
                           class="text-sm text-[#0053FF] hover:underline">
                            Write a Review
                        </a>
                    @endauth
                </div>
                @if($course->reviews->count() > 0)
                    <div class="space-y-4">
                        @foreach($course->reviews as $review)
                            <div class="border p-4 rounded">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <div class="font-semibold">{{ $review->user->first_name }} {{ $review->user->last_name }}</div>
                                        @if($review->is_verified_student)
                                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Verified Student</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review->rating_overall ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l-2.8-2.034a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                </div>
                                @if($review->review_text)
                                    <p class="text-sm text-gray-700">{{ $review->review_text }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No reviews yet. Be the first to review this course!</p>
                @endif
            </div>

            {{-- Discussion Section --}}
            @php
                $discussionService = app(\App\Services\Forum\ForumDiscussionService::class);
                $activeDiscussion = $discussionService->getActiveDiscussion('training', $course->id, 'course');
            @endphp
            @if($activeDiscussion)
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <span class="text-sm font-medium text-blue-800">Active Discussion</span>
                        </div>
                        <a href="{{ $activeDiscussion->route }}" 
                           class="text-sm text-blue-600 hover:text-blue-800 font-medium hover:underline">
                            View Discussion →
                        </a>
                    </div>
                </div>
            @endif

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3">
                @if($bookingUrl)
                    <a href="{{ $bookingUrl }}" 
                       target="_blank"
                       class="flex-1 px-6 py-3 bg-[#0053FF] text-white rounded-lg text-center font-semibold hover:bg-blue-700">
                        Book Now
                    </a>
                @endif
                @if($course->provider->website)
                    <a href="{{ $course->provider->website }}" 
                       target="_blank"
                       class="flex-1 px-6 py-3 border border-[#0053FF] text-[#0053FF] rounded-lg text-center font-semibold hover:bg-blue-50">
                        Visit Provider Website
                    </a>
                @endif
                @php
                    $discussionService = app(\App\Services\Forum\ForumDiscussionService::class);
                    $defaultCategory = \TeamTeaTime\Forum\Models\Category::where('accepts_threads', true)->orderBy('id')->first();
                    $categoryId = $defaultCategory?->id ?? 1;
                    $category = \TeamTeaTime\Forum\Models\Category::find($categoryId);
                    $categorySlug = $category ? \Illuminate\Support\Str::slug($category->title) : 'general';
                    $forumUrl = route('forum.thread.create', [
                        'category_id' => $categoryId,
                        'category_slug' => $categorySlug
                    ]) . '?' . http_build_query([
                        'source_module' => 'training',
                        'source_item_id' => $course->id,
                        'source_item_type' => 'course',
                        'source_item_title' => $course->certification->name . ' - ' . $course->provider->name,
                        'source_item_url' => route('training.course.detail', $course->id),
                    ]);
                @endphp
                <a href="{{ $forumUrl }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium shadow-sm hover:shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    Start Discussion
                </a>
            </div>
        </div>
    </main>
</div>


