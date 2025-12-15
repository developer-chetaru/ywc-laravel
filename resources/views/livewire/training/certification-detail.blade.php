<div x-data="{ activeTab: 'overview' }">
    <main class="flex-1">
        <div class="w-full bg-white p-5 rounded-md pb-10">
            <!-- Breadcrumb -->
            <nav class="mb-4 text-sm">
                <a href="{{ route('training.courses') }}" class="text-[#0053FF] hover:underline">Training & Resources</a>
                <span class="mx-2">/</span>
                <span class="text-gray-600">{{ $certification->name }}</span>
            </nav>

            <!-- Certification Header -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- Certification Image -->
                    <div class="w-full md:w-1/3">
                        @if($certification->cover_image)
                            @php
                                $imagePath = $certification->cover_image;
                                $imageUrl = str_starts_with($imagePath, 'images/') 
                                    ? asset($imagePath) 
                                    : asset('storage/' . $imagePath);
                            @endphp
                            <img src="{{ $imageUrl }}" 
                                 alt="{{ $certification->name }}" 
                                 class="w-full h-64 object-cover rounded-lg">
                        @else
                            <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="w-24 h-24 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2L3 7v11h14V7l-7-5z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Certification Info -->
                    <div class="w-full md:w-2/3">
                        <h1 class="text-[#0053FF] text-3xl font-bold mb-2">{{ $certification->name }}</h1>
                        @if($certification->official_designation)
                            <p class="text-gray-600 mb-4">Official Designation: {{ $certification->official_designation }}</p>
                        @endif
                        <div class="mb-4">
                            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                {{ $certification->category->name }}
                            </span>
                        </div>
                        <p class="text-gray-700 mb-4">{{ $certification->description }}</p>
                        
                        <!-- Key Info -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            @if($certification->validity_period_months)
                                <div>
                                    <div class="text-sm text-gray-600">Validity</div>
                                    <div class="font-semibold">{{ $certification->validity_period_months }} months</div>
                                </div>
                            @endif
                            <div>
                                <div class="text-sm text-gray-600">Providers</div>
                                <div class="font-semibold">{{ $providerCourses->count() }} available</div>
                            </div>
                            @if($certification->prerequisites)
                                    <div>
                                        <div class="text-sm text-gray-600">Prerequisites</div>
                                        <div class="font-semibold text-sm">{{ \Illuminate\Support\Str::limit($certification->prerequisites, 30) }}</div>
                                    </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Certification Details Tabs -->
            <div class="mb-6 border-b border-gray-200">
                <div class="flex space-x-4">
                    <button @click="activeTab = 'overview'" 
                            :class="activeTab === 'overview' ? 'border-b-2 border-[#0053FF] text-[#0053FF] font-semibold' : 'text-gray-600'"
                            class="px-4 py-2">
                        Overview
                    </button>
                    <button @click="activeTab = 'pathways'" 
                            :class="activeTab === 'pathways' ? 'border-b-2 border-[#0053FF] text-[#0053FF] font-semibold' : 'text-gray-600'"
                            class="px-4 py-2">
                        Career Pathways
                    </button>
                </div>
            </div>

            <!-- Career Pathways Tab -->
            <div x-show="activeTab === 'pathways'" x-cloak class="mb-8">
                @php
                    $pathways = \App\Models\TrainingCareerPathway::where('is_active', true)
                        ->whereJsonContains('certification_sequence', (string)$certification->id)
                        ->get();
                @endphp
                
                @if($pathways->count() > 0)
                    @foreach($pathways as $pathway)
                        <div class="mb-6 border rounded-lg p-6">
                            <h3 class="text-xl font-bold text-[#0053FF] mb-2">{{ $pathway->name }}</h3>
                            <p class="text-gray-600 mb-4">{{ $pathway->description }}</p>
                            
                            <div class="flex flex-wrap gap-4 items-center">
                                @php
                                    $certifications = $pathway->getCertifications();
                                @endphp
                                @foreach($certifications as $index => $cert)
                                    <div class="flex items-center">
                                        <div class="w-48 p-3 rounded-lg border-2 
                                            {{ $cert->id == $certification->id ? 'border-[#0053FF] bg-blue-50' : 'border-gray-300 bg-white' }}">
                                            <h5 class="font-semibold text-sm">{{ $cert->name }}</h5>
                                        </div>
                                        @if($index < $certifications->count() - 1)
                                            <svg class="w-6 h-6 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-4">
                                <a href="{{ route('training.career-pathway', $pathway->id) }}" 
                                   class="text-[#0053FF] hover:underline text-sm">
                                    View Full Pathway →
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500 text-center py-8">No career pathways available for this certification.</p>
                @endif
            </div>

            <!-- Detailed Information -->
            <div x-show="activeTab === 'overview'" class="grid md:grid-cols-2 gap-6 mb-8">
                @if($certification->prerequisites)
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Prerequisites</h3>
                        <p class="text-gray-700">{{ $certification->prerequisites }}</p>
                    </div>
                @endif

                @if($certification->renewal_requirements)
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Renewal Requirements</h3>
                        <p class="text-gray-700">{{ $certification->renewal_requirements }}</p>
                    </div>
                @endif

                @if($certification->international_recognition)
                    <div>
                        <h3 class="text-lg font-semibold mb-2">International Recognition</h3>
                        <p class="text-gray-700">{{ $certification->international_recognition }}</p>
                    </div>
                @endif

                @if($certification->career_benefits)
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Career Benefits</h3>
                        <p class="text-gray-700">{{ $certification->career_benefits }}</p>
                    </div>
                @endif

                @if($certification->positions_requiring)
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold mb-2">Positions Requiring This Certification</h3>
                        <p class="text-gray-700">{{ $certification->positions_requiring }}</p>
                    </div>
                @endif
            </div>

            <!-- Provider Comparison Section -->
            <div x-show="activeTab === 'overview'" class="mb-8">
                <h2 class="text-2xl font-bold mb-4">Available Providers ({{ $providerCourses->count() }})</h2>
                
                @if($providerCourses->count() > 0)
                    <!-- Comparison Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border p-3 text-left">Provider</th>
                                    <th class="border p-3 text-left">Price</th>
                                    <th class="border p-3 text-left">Duration</th>
                                    <th class="border p-3 text-left">Format</th>
                                    <th class="border p-3 text-left">Rating</th>
                                    <th class="border p-3 text-left">Next Available</th>
                                    <th class="border p-3 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($providerCourses as $course)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border p-3">
                                            <div class="font-semibold">{{ $course->provider->name }}</div>
                                            @if($course->provider->is_verified_partner)
                                                <span class="text-xs text-green-600">✓ Verified Partner</span>
                                            @endif
                                        </td>
                                        <td class="border p-3">
                                            @if($isYwcMember)
                                                <div class="font-bold text-[#0053FF]">£{{ number_format($course->ywc_price, 2) }}</div>
                                                <div class="text-sm text-gray-500 line-through">£{{ number_format($course->price, 2) }}</div>
                                                <div class="text-xs text-green-600">Save £{{ number_format($course->savings_amount, 2) }}</div>
                                            @else
                                                <div class="font-bold">£{{ number_format($course->price, 2) }}</div>
                                            @endif
                                        </td>
                                        <td class="border p-3">{{ $course->duration_days }} day(s)</td>
                                        <td class="border p-3">{{ ucfirst(str_replace('-', ' ', $course->format)) }}</td>
                                        <td class="border p-3">
                                            @if($course->rating_avg > 0)
                                                <div class="flex items-center gap-1">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-4 h-4 {{ $i <= round($course->rating_avg) ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                             fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                    @endfor
                                                    <span class="text-sm ml-1">({{ $course->review_count }})</span>
                                                </div>
                                            @else
                                                <span class="text-gray-400">No ratings yet</span>
                                            @endif
                                        </td>
                                        <td class="border p-3">
                                            @if($course->upcomingSchedules->count() > 0)
                                                {{ $course->upcomingSchedules->first()->start_date->format('M d, Y') }}
                                            @else
                                                <span class="text-gray-400">Contact provider</span>
                                            @endif
                                        </td>
                                        <td class="border p-3">
                                            <a href="{{ route('training.course.detail', $course->id) }}"
                                               class="px-4 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-blue-700 text-sm">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        No providers currently offering this certification.
                    </div>
                @endif
            </div>

            <!-- Provider Detail Modal -->
            @if($selectedProviderCourse)
                <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" 
                     wire:click="closeProviderDetail">
                    <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto" 
                         wire:click.stop>
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-2xl font-bold">{{ $selectedProviderCourse->certification->name }}</h3>
                                <button wire:click="closeProviderDetail" class="text-gray-500 hover:text-gray-700">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Provider Info -->
                            <div class="mb-6 pb-6 border-b">
                                <div class="flex items-center gap-4 mb-4">
                                    @if($selectedProviderCourse->provider->logo)
                                        <img src="{{ asset('storage/' . $selectedProviderCourse->provider->logo) }}" 
                                             alt="{{ $selectedProviderCourse->provider->name }}" 
                                             class="w-16 h-16 object-contain">
                                    @endif
                                    <div>
                                        <h4 class="text-xl font-semibold">{{ $selectedProviderCourse->provider->name }}</h4>
                                        @if($selectedProviderCourse->provider->rating_avg > 0)
                                            <div class="flex items-center gap-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= round($selectedProviderCourse->provider->rating_avg) ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                         fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                                <span class="text-sm ml-1">({{ $selectedProviderCourse->provider->total_reviews }} reviews)</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Course Details -->
                            <div class="grid md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <h5 class="font-semibold mb-2">Price</h5>
                                    @if($isYwcMember)
                                        <div class="text-2xl font-bold text-[#0053FF]">£{{ number_format($selectedProviderCourse->ywc_price, 2) }}</div>
                                        <div class="text-sm text-gray-500 line-through">£{{ number_format($selectedProviderCourse->price, 2) }}</div>
                                        <div class="text-sm text-green-600">Save £{{ number_format($selectedProviderCourse->savings_amount, 2) }} with YWC</div>
                                    @else
                                        <div class="text-2xl font-bold">£{{ number_format($selectedProviderCourse->price, 2) }}</div>
                                    @endif
                                </div>
                                <div>
                                    <h5 class="font-semibold mb-2">Duration</h5>
                                    <p>{{ $selectedProviderCourse->duration_days }} day(s)</p>
                                    @if($selectedProviderCourse->duration_hours)
                                        <p class="text-sm text-gray-600">{{ $selectedProviderCourse->duration_hours }} hours</p>
                                    @endif
                                </div>
                                <div>
                                    <h5 class="font-semibold mb-2">Format</h5>
                                    <p>{{ ucfirst(str_replace('-', ' ', $selectedProviderCourse->format)) }}</p>
                                </div>
                                <div>
                                    <h5 class="font-semibold mb-2">Language</h5>
                                    <p>{{ $selectedProviderCourse->language_of_instruction }}</p>
                                </div>
                            </div>

                            <!-- What's Included -->
                            @if($selectedProviderCourse->materials_included || $selectedProviderCourse->accommodation_included || $selectedProviderCourse->meals_included)
                                <div class="mb-6">
                                    <h5 class="font-semibold mb-2">What's Included</h5>
                                    <ul class="list-disc list-inside space-y-1">
                                        @if($selectedProviderCourse->materials_included)
                                            @foreach($selectedProviderCourse->materials_included as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        @endif
                                        @if($selectedProviderCourse->accommodation_included)
                                            <li>Accommodation included</li>
                                        @endif
                                        @if($selectedProviderCourse->meals_included)
                                            <li>{{ $selectedProviderCourse->meals_details ?? 'Meals included' }}</li>
                                        @endif
                                        @if($selectedProviderCourse->parking_included)
                                            <li>Parking included</li>
                                        @endif
                                        @if($selectedProviderCourse->transport_included)
                                            <li>Transport included</li>
                                        @endif
                                    </ul>
                                </div>
                            @endif

                            <!-- Course Structure -->
                            @if($selectedProviderCourse->daily_schedule)
                                <div class="mb-6">
                                    <h5 class="font-semibold mb-2">Course Structure</h5>
                                    <div class="space-y-2">
                                        @foreach($selectedProviderCourse->daily_schedule as $day => $schedule)
                                            <div class="border-l-4 border-[#0053FF] pl-4">
                                                <div class="font-semibold">{{ $day }}</div>
                                                <div class="text-sm text-gray-600">{{ $schedule }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Locations -->
                            @if($selectedProviderCourse->locations->count() > 0)
                                <div class="mb-6">
                                    <h5 class="font-semibold mb-2">Locations</h5>
                                    <div class="space-y-2">
                                        @foreach($selectedProviderCourse->locations as $location)
                                            <div>
                                                <div class="font-semibold">{{ $location->name }}</div>
                                                <div class="text-sm text-gray-600">
                                                    {{ $location->city }}, {{ $location->country }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Upcoming Schedules -->
                            @if($selectedProviderCourse->schedules->count() > 0)
                                <div class="mb-6">
                                    <h5 class="font-semibold mb-2">Upcoming Dates</h5>
                                    <div class="space-y-2">
                                        @foreach($selectedProviderCourse->schedules->take(5) as $schedule)
                                            <div class="flex justify-between items-center border p-2 rounded">
                                                <div>
                                                    <div class="font-semibold">{{ $schedule->start_date->format('M d, Y') }}</div>
                                                    @if($schedule->end_date)
                                                        <div class="text-sm text-gray-600">to {{ $schedule->end_date->format('M d, Y') }}</div>
                                                    @endif
                                                </div>
                                                @if($schedule->available_spots)
                                                    <div class="text-sm">
                                                        {{ $schedule->available_spots - $schedule->booked_spots }} spots available
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Reviews -->
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <h5 class="font-semibold">Reviews ({{ $selectedProviderCourse->reviews->count() }})</h5>
                                    @auth
                                        <a href="{{ route('training.review.submit', $selectedProviderCourse->id) }}" 
                                           class="text-sm text-[#0053FF] hover:underline">
                                            Write a Review
                                        </a>
                                    @endauth
                                </div>
                                @if($selectedProviderCourse->reviews->count() > 0)
                                    <div class="space-y-4">
                                        @foreach($selectedProviderCourse->reviews->take(3) as $review)
                                            <div class="border p-4 rounded">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <div class="font-semibold">{{ $review->user->first_name }} {{ $review->user->last_name }}</div>
                                                    @if($review->is_verified_student)
                                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Verified Student</span>
                                                    @endif
                                                    <div class="flex items-center">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <svg class="w-4 h-4 {{ $i <= $review->rating_overall ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                                 fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                            </svg>
                                                        @endfor
                                                    </div>
                                                </div>
                                                @if($review->review_text)
                                                    <p class="text-gray-700">{{ $review->review_text }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 text-sm">No reviews yet. Be the first to review this course!</p>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-4">
                                @if($selectedProviderCourse->booking_url)
                                    <a href="{{ $this->generateBookingUrl($selectedProviderCourse->id) }}" 
                                       target="_blank"
                                       class="flex-1 px-6 py-3 bg-[#0053FF] text-white rounded-lg hover:bg-blue-700 text-center font-semibold">
                                        Book Now
                                    </a>
                                @endif
                                @if($selectedProviderCourse->provider->website)
                                    <a href="{{ $selectedProviderCourse->provider->website }}" 
                                       target="_blank"
                                       class="px-6 py-3 border border-[#0053FF] text-[#0053FF] rounded-lg hover:bg-blue-50 text-center font-semibold">
                                        Visit Provider Website
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </main>
</div>
