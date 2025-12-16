<div>
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
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
            
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Temporary Work Availability</h1>

            <form wire:submit="save" class="space-y-6">
                <!-- Status Toggle -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold mb-2">Availability Status</h2>
                            <p class="text-sm text-gray-600">Toggle your availability for temporary work</p>
                        </div>
                        <button type="button" wire:click="toggleAvailability" 
                            class="px-6 py-3 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $status === 'available_now' ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-gray-300 text-gray-700 hover:bg-gray-400' }}">
                            {{ $status === 'available_now' ? '🟢 Available Now' : '⚫ Not Available' }}
                        </button>
                    </div>
                </div>

                @if($status !== 'not_available')
                <!-- Work Types -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4">Work Types</h2>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="dayWork" class="mr-2">
                            Day work (single days)
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="shortContracts" class="mr-2">
                            Short contracts (2-7 days)
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="emergencyCover" class="mr-2">
                            Emergency cover (immediate start)
                        </label>
                    </div>
                </div>

                <!-- Rates -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4">Day Rates</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Min Day Rate (€)</label>
                            <input type="number" wire:model="dayRateMin" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Day Rate (€)</label>
                            <input type="number" wire:model="dayRateMax" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none">
                        </div>
                    </div>
                </div>
                @endif

                <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 font-medium">
                    Save Settings
                </button>
            </form>
        </div>
    </div>
</div>
