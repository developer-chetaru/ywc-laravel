<div>
    <main class="flex-1">
        <div class="w-full bg-white p-5 rounded-md pb-10">
            <h2 class="text-[#0053FF] text-[30px] font-semibold mb-6">Provider Portal</h2>

            <!-- Provider Info -->
            <div class="bg-gray-50 border rounded-lg p-6 mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-bold mb-2">{{ $provider->name }}</h3>
                        <div class="space-y-1 text-sm text-gray-600">
                            @if($provider->email)
                                <div>Email: {{ $provider->email }}</div>
                            @endif
                            @if($provider->phone)
                                <div>Phone: {{ $provider->phone }}</div>
                            @endif
                            @if($provider->website)
                                <div>Website: <a href="{{ $provider->website }}" target="_blank" class="text-[#0053FF] hover:underline">{{ $provider->website }}</a></div>
                            @endif
                        </div>
                        <div class="mt-3">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @if($provider->is_active) bg-green-100 text-green-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ $provider->is_active ? 'Active' : 'Pending Approval' }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-[#0053FF]">{{ $courses->total() }}</div>
                        <div class="text-sm text-gray-600">Total Courses</div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold">My Courses</h3>
                <div class="flex gap-2">
                    <a href="{{ route('training.schedule.calendar.provider', $provider->id) }}" 
                       class="px-6 py-2 border border-[#0053FF] text-[#0053FF] rounded-lg hover:bg-blue-50 font-semibold">
                        📅 Schedule Calendar
                    </a>
                    <button wire:click="openLocationModal" 
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold">
                        📍 Manage Locations
                    </button>
                    <button wire:click="openCourseModal" 
                            class="px-6 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-blue-700 font-semibold">
                        + Add Course
                    </button>
                </div>
            </div>

            <!-- Courses List -->
            <div class="space-y-4">
                @forelse($courses as $course)
                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-[#0053FF] mb-2">
                                    {{ $course->certification->name }}
                                </h4>
                                <div class="grid md:grid-cols-3 gap-4 text-sm text-gray-600">
                                    <div>
                                        <span class="font-semibold">Price:</span> £{{ number_format($course->price, 2) }}
                                        <span class="text-green-600">(YWC: £{{ number_format($course->ywc_price, 2) }})</span>
                                    </div>
                                    <div>
                                        <span class="font-semibold">Duration:</span> {{ $course->duration_days }} day(s)
                                    </div>
                                    <div>
                                        <span class="font-semibold">Format:</span> {{ ucfirst(str_replace('-', ' ', $course->format)) }}
                                    </div>
                                    <div>
                                        <span class="font-semibold">Locations:</span> {{ $course->locations->count() }}
                                    </div>
                                    <div>
                                        <span class="font-semibold">Schedules:</span> {{ $course->schedules->count() }}
                                    </div>
                                    <div>
                                        <span class="font-semibold">Rating:</span> 
                                        @if($course->rating_avg > 0)
                                            {{ number_format($course->rating_avg, 1) }}/5 ({{ $course->review_count }} reviews)
                                        @else
                                            No ratings yet
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if($course->is_active) bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $course->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button wire:click="openCourseModal({{ $course->id }})" 
                                        class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Edit
                                </button>
                                <button wire:click="openScheduleModal(null, {{ $course->id }})" 
                                        class="px-4 py-2 text-sm border border-blue-300 text-blue-700 rounded-lg hover:bg-blue-50">
                                    Add Schedule
                                </button>
                                <button wire:click="deleteCourse({{ $course->id }})" 
                                        wire:confirm="Are you sure you want to delete this course?"
                                        class="px-4 py-2 text-sm bg-red-100 text-red-700 rounded-lg hover:bg-red-200">
                                    Delete
                                </button>
                            </div>
                            
                            <!-- Course Schedules -->
                            @if($course->schedules->count() > 0)
                                <div class="mt-4 pt-4 border-t">
                                    <h5 class="font-semibold mb-2">Upcoming Schedules:</h5>
                                    <div class="space-y-2">
                                        @foreach($course->schedules->take(3) as $schedule)
                                            <div class="flex justify-between items-center text-sm bg-gray-50 p-2 rounded">
                                                <div>
                                                    <span class="font-semibold">{{ $schedule->start_date->format('M d, Y') }}</span>
                                                    @if($schedule->end_date && !$schedule->start_date->isSameDay($schedule->end_date))
                                                        <span> - {{ $schedule->end_date->format('M d, Y') }}</span>
                                                    @endif
                                                    @if($schedule->location)
                                                        <span class="text-gray-600"> at {{ $schedule->location->name }}</span>
                                                    @endif
                                                </div>
                                                <div class="flex gap-2">
                                                    <span class="text-gray-600">
                                                        {{ $schedule->available_spots - $schedule->booked_spots }} spots
                                                    </span>
                                                    <button wire:click="openScheduleModal({{ $schedule->id }}, {{ $course->id }})" 
                                                            class="text-blue-600 hover:underline text-xs">
                                                        Edit
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 border rounded-lg">
                        <p class="text-gray-500 text-lg mb-4">No courses added yet.</p>
                        <button wire:click="openCourseModal" 
                                class="px-6 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-blue-700">
                            Add Your First Course
                        </button>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $courses->links() }}
            </div>
        </div>

        <!-- Course Modal -->
        @if($showCourseModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" 
                 wire:click="closeCourseModal">
                <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto" 
                     wire:click.stop>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-2xl font-bold">
                                {{ $selectedCourse ? 'Edit Course' : 'Add Course' }}
                            </h3>
                            <button wire:click="closeCourseModal" class="text-gray-500 hover:text-gray-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form wire:submit.prevent="saveCourse">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-2">Certification *</label>
                                    <select wire:model="certification_id" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]">
                                        <option value="">Select Certification</option>
                                        @foreach($certifications as $cert)
                                            <option value="{{ $cert->id }}">{{ $cert->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('certification_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Price (£) *</label>
                                        <input type="number" step="0.01" wire:model="price" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]">
                                        @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-2">YWC Discount (%)</label>
                                        <input type="number" step="0.01" wire:model="ywc_discount_percentage" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Duration (Days) *</label>
                                        <input type="number" wire:model="duration_days" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]">
                                        @error('duration_days') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="grid md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Format *</label>
                                        <select wire:model="format" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]">
                                            <option value="in-person">In-Person</option>
                                            <option value="online">Online</option>
                                            <option value="hybrid">Hybrid</option>
                                            <option value="self-paced">Self-Paced</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Language</label>
                                        <input type="text" wire:model="language_of_instruction" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Max Class Size</label>
                                        <input type="number" wire:model="class_size_max" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-2">Booking URL</label>
                                    <input type="url" wire:model="booking_url" 
                                           placeholder="https://..."
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-2">Course Structure</label>
                                    <textarea wire:model="course_structure" rows="4" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]"></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-2">Special Features</label>
                                    <textarea wire:model="special_features" rows="3" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]"></textarea>
                                </div>

                                <div class="grid md:grid-cols-2 gap-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" wire:model="accommodation_included" 
                                               class="mr-2">
                                        <label>Accommodation Included</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" wire:model="meals_included" 
                                               class="mr-2">
                                        <label>Meals Included</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" wire:model="parking_included" 
                                               class="mr-2">
                                        <label>Parking Included</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" wire:model="transport_included" 
                                               class="mr-2">
                                        <label>Transport Included</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" wire:model="re_sits_included" 
                                               class="mr-2">
                                        <label>Re-sits Included</label>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-4 mt-6">
                                <button type="submit" 
                                        class="flex-1 px-6 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-blue-700 font-semibold">
                                    {{ $selectedCourse ? 'Update' : 'Add' }} Course
                                </button>
                                <button type="button" wire:click="closeCourseModal" 
                                        class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Schedule Modal -->
        @if($showScheduleModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" 
                 wire:click="closeScheduleModal">
                <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto" 
                     wire:click.stop>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-2xl font-bold">
                                {{ $selectedSchedule ? 'Edit Schedule' : 'Add Schedule' }}
                            </h3>
                            <button wire:click="closeScheduleModal" class="text-gray-500 hover:text-gray-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form wire:submit.prevent="saveSchedule">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-2">Location *</label>
                                    <select wire:model="schedule_location_id" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        <option value="">Select Location</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}">{{ $location->name }} - {{ $location->city }}, {{ $location->country }}</option>
                                        @endforeach
                                    </select>
                                    @error('schedule_location_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Start Date *</label>
                                        <input type="date" wire:model="schedule_start_date" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        @error('schedule_start_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold mb-2">End Date</label>
                                        <input type="date" wire:model="schedule_end_date" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                </div>

                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Start Time</label>
                                        <input type="time" wire:model="schedule_start_time" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold mb-2">End Time</label>
                                        <input type="time" wire:model="schedule_end_time" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-2">Available Spots</label>
                                    <input type="number" wire:model="schedule_available_spots" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>

                            <div class="flex gap-4 mt-6">
                                <button type="submit" 
                                        class="flex-1 px-6 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-blue-700 font-semibold">
                                    {{ $selectedSchedule ? 'Update' : 'Add' }} Schedule
                                </button>
                                <button type="button" wire:click="closeScheduleModal" 
                                        class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Location Modal -->
        @if($showLocationModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" 
                 wire:click="closeLocationModal">
                <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto" 
                     wire:click.stop>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-2xl font-bold">
                                {{ $selectedLocation ? 'Edit Location' : 'Add Location' }}
                            </h3>
                            <button wire:click="closeLocationModal" class="text-gray-500 hover:text-gray-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form wire:submit.prevent="saveLocation">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-2">Location Name *</label>
                                    <input type="text" wire:model="location_name" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    @error('location_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-2">Address</label>
                                    <input type="text" wire:model="location_address" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div>

                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold mb-2">City *</label>
                                        <input type="text" wire:model="location_city" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        @error('location_city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Country *</label>
                                        <input type="text" wire:model="location_country" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        @error('location_country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-2">Region</label>
                                    <input type="text" wire:model="location_region" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>

                            <div class="flex gap-4 mt-6">
                                <button type="submit" 
                                        class="flex-1 px-6 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-blue-700 font-semibold">
                                    {{ $selectedLocation ? 'Update' : 'Add' }} Location
                                </button>
                                <button type="button" wire:click="closeLocationModal" 
                                        class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </main>
</div>
