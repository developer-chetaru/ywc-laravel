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
            
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Temporary Work Marketplace</h1>
                @if(auth()->user() && !auth()->user()->hasRole('super_admin') && (auth()->user()->hasRole('Captain') || (auth()->user()->vesselVerification && auth()->user()->vesselVerification->isVerified())))
                <a href="{{ route('job-board.post-temporary-work') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Post Temporary Work
                </a>
                @endif
            </div>

            <!-- Filters -->
            <div class="bg-white shadow rounded-lg p-4 mb-6">
                <div class="flex gap-2 flex-wrap">
                    <button wire:click="$set('filter', 'all')" class="px-4 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $filter === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        All
                    </button>
                    <button wire:click="$set('filter', 'day_work')" class="px-4 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $filter === 'day_work' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Day Work
                    </button>
                    <button wire:click="$set('filter', 'short_contract')" class="px-4 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $filter === 'short_contract' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Short Contracts
                    </button>
                    <button wire:click="$set('filter', 'emergency_cover')" class="px-4 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $filter === 'emergency_cover' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Emergency
                    </button>
                </div>
            </div>

            <!-- Job Listings -->
            <div class="space-y-4">
                @forelse($jobs as $job)
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            @if($job->urgency_level === 'emergency')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mb-2">
                                🚨 Emergency
                            </span>
                            @endif
                            <h3 class="text-xl font-semibold mb-2">
                                <a href="{{ route('job-board.detail', $job->id) }}" class="hover:text-indigo-600">
                                    {{ $job->position_title }}
                                </a>
                            </h3>
                            <p class="text-gray-600 mb-2">{{ $job->location }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $job->work_start_date->format('M d') }} - {{ $job->work_end_date->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-semibold text-gray-900 mb-2">
                                €{{ number_format($job->day_rate_min ?? 0, 0) }}/day
                            </div>
                            <a href="{{ route('job-board.detail', $job->id) }}" class="text-indigo-600 hover:text-indigo-800">
                                View Details →
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white shadow rounded-lg p-12 text-center">
                    <p class="text-gray-500">No temporary work found.</p>
                </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $jobs->links() }}
            </div>
        </div>
    </div>
</div>
