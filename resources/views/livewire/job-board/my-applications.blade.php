<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">My Applications</h1>

            <div class="bg-white shadow rounded-lg p-4 mb-6">
                <div class="flex gap-2">
                    <button wire:click="$set('filter', 'all')" class="px-4 py-2 rounded-md {{ $filter === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-100' }}">
                        All
                    </button>
                    <button wire:click="$set('filter', 'active')" class="px-4 py-2 rounded-md {{ $filter === 'active' ? 'bg-indigo-600 text-white' : 'bg-gray-100' }}">
                        Active
                    </button>
                    <button wire:click="$set('filter', 'interviewing')" class="px-4 py-2 rounded-md {{ $filter === 'interviewing' ? 'bg-indigo-600 text-white' : 'bg-gray-100' }}">
                        Interviewing
                    </button>
                </div>
            </div>

            <div class="space-y-4">
                @forelse($applications as $application)
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold mb-1">{{ $application->jobPost->position_title }}</h3>
                            <p class="text-gray-600 mb-2">{{ $application->jobPost->location }}</p>
                            <p class="text-sm text-gray-500">
                                Status: <span class="font-medium">{{ ucfirst($application->status) }}</span>
                                • Applied {{ $application->submitted_at->diffForHumans() }}
                            </p>
                        </div>
                        <a href="{{ route('job-board.detail', $application->job_post_id) }}" class="px-4 py-2 border border-gray-300 rounded-md">
                            View
                        </a>
                    </div>
                </div>
                @empty
                <div class="bg-white shadow rounded-lg p-12 text-center">
                    <p class="text-gray-500">No applications yet.</p>
                </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $applications->links() }}
            </div>
        </div>
    </div>
</div>
