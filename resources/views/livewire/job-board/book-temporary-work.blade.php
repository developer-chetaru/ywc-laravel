<div>
    @if($crewUser)
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('job-board.available-crew') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Available Crew
                </a>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Book Temporary Work</h1>

            <!-- Crew Member Info -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Crew Member</h2>
                <div class="flex items-center gap-4">
                    <div>
                        <h3 class="text-lg font-medium">{{ $crewUser->name }}</h3>
                        @if($crewUser->crewAvailability)
                        <p class="text-gray-600">
                            Day Rate: €{{ number_format($crewUser->crewAvailability->day_rate_min ?? 0, 0) }}-{{ number_format($crewUser->crewAvailability->day_rate_max ?? 0, 0) }}
                        </p>
                        @if($crewUser->rating)
                        <p class="text-sm text-gray-600">⭐ {{ number_format($crewUser->rating, 1) }} rating</p>
                        @endif
                        @endif
                    </div>
                </div>
            </div>

            <form wire:submit="confirmBooking" class="space-y-6">
                <!-- Work Details -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Work Details</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Work Date *</label>
                            <input type="date" wire:model="workDate" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Time *</label>
                                <input type="time" wire:model="startTime" wire:change="calculateHours" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Time *</label>
                                <input type="time" wire:model="endTime" wire:change="calculateHours" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Hours</label>
                            <input type="number" value="{{ $totalHours }}" readonly class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                            <input type="text" wire:model="location" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Berth Details</label>
                            <input type="text" wire:model="berthDetails" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none" placeholder="e.g. Berth A24">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Work Description *</label>
                            <textarea wire:model="workDescription" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none" required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Payment Details</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Day Rate (€) *</label>
                            <input type="number" wire:model="dayRate" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                            <select wire:model="paymentMethod" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none bg-white" required>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="company_process">Company Payment Process</option>
                            </select>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-medium">Total Payment:</span>
                                <span class="text-2xl font-bold text-indigo-600">€{{ number_format($this->calculateTotal(), 2) }}</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">Based on {{ $totalHours }} hours × €{{ number_format($dayRate / 10, 2) }}/hour</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('job-board.available-crew') }}" class="px-6 py-2 border border-gray-300 rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 font-medium">
                        Confirm Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

