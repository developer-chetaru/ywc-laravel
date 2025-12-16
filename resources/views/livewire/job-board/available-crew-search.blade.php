<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('job-board.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Job Board
                </a>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Find Available Crew</h1>

            <!-- Filters -->
            <div class="bg-white shadow rounded-lg p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                        <input type="text" wire:model.live.debounce.300ms="position" placeholder="e.g. Deckhand" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search Radius</label>
                        <select wire:model.live="radius" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none bg-white">
                            <option value="10">10km</option>
                            <option value="20">20km</option>
                            <option value="50">50km</option>
                            <option value="100">100km</option>
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center mt-6">
                            <input type="checkbox" wire:model.live="availableNow" class="mr-2">
                            Available Now Only
                        </label>
                    </div>
                </div>
            </div>

            <!-- Crew List -->
            <div class="space-y-4">
                @forelse($availabilities as $availability)
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold mb-2">{{ $availability->user->name }}</h3>
                            @if($availability->average_rating)
                            <p class="text-sm text-gray-600 mb-2">
                                ⭐ {{ number_format($availability->average_rating, 1) }} 
                                ({{ $availability->total_jobs_completed }} jobs completed)
                            </p>
                            @endif
                            @if($availability->available_positions)
                            <p class="text-sm text-gray-600 mb-2">
                                Available for: {{ implode(', ', $availability->available_positions) }}
                            </p>
                            @endif
                            <p class="text-sm text-gray-600">
                                Day Rate: €{{ number_format($availability->day_rate_min ?? 0, 0) }}-{{ number_format($availability->day_rate_max ?? 0, 0) }}
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('job-board.book-crew', $availability->user_id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 font-medium">
                                Book Now
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white shadow rounded-lg p-12 text-center">
                    <p class="text-gray-500">No available crew found.</p>
                </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $availabilities->links() }}
            </div>
        </div>
    </div>
</div>
