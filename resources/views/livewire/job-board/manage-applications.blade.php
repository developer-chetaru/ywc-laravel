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
            
            <h1 class="text-3xl font-bold text-gray-900 mb-6">
                {{ $viewMode === 'captain' ? 'Manage Applications' : 'My Applications' }}
            </h1>

            <!-- Filters -->
            <div class="bg-white shadow rounded-lg p-4 mb-6">
                <div class="flex gap-2 flex-wrap">
                    <button wire:click="$set('filter', 'all')" class="px-4 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $filter === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        All
                    </button>
                    @if($viewMode === 'captain')
                    <button wire:click="$set('filter', 'new')" class="px-4 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $filter === 'new' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        New
                    </button>
                    <button wire:click="$set('filter', 'shortlisted')" class="px-4 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $filter === 'shortlisted' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Shortlisted
                    </button>
                    @else
                    <button wire:click="$set('filter', 'active')" class="px-4 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $filter === 'active' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Active
                    </button>
                    <button wire:click="$set('filter', 'interviewing')" class="px-4 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $filter === 'interviewing' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Interviewing
                    </button>
                    @endif
                </div>
            </div>

            <!-- Applications List -->
            <div class="space-y-4">
                @forelse($applications as $application)
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                @if($application->match_score)
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm font-medium">
                                    {{ number_format($application->match_score, 0) }}% Match
                                </span>
                                @endif
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-sm">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </div>
                            <h3 class="text-xl font-semibold mb-1">
                                @if($viewMode === 'captain')
                                    {{ $application->user->name }}
                                @else
                                    {{ $application->jobPost->position_title }}
                                @endif
                            </h3>
                            @if($viewMode === 'captain')
                            <p class="text-gray-600">{{ $application->jobPost->position_title }}</p>
                            @else
                            <p class="text-gray-600">{{ $application->jobPost->yacht->name ?? 'N/A' }}</p>
                            @endif
                            <p class="text-sm text-gray-500 mt-2">
                                Applied {{ $application->submitted_at->diffForHumans() }}
                                @if($application->viewed_at)
                                • Viewed {{ $application->viewed_at->diffForHumans() }}
                                @endif
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('job-board.detail', $application->job_post_id) }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                View
                            </a>
                            @if($viewMode === 'crew')
                            <button wire:click="withdraw({{ $application->id }})" class="px-4 py-2 text-red-600 border border-red-300 rounded-md shadow-sm bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Withdraw
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white shadow rounded-lg p-12 text-center">
                    <p class="text-gray-500">No applications found.</p>
                </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $applications->links() }}
            </div>
        </div>
    </div>
</div>
