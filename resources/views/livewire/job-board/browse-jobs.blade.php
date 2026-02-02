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
                <h1 class="text-3xl font-bold text-gray-900">Browse Jobs</h1>
                @if(auth()->user() && !auth()->user()->hasRole('super_admin') && (auth()->user()->hasRole('Captain') || (auth()->user()->vesselVerification && auth()->user()->vesselVerification->isVerified())))
                <a href="{{ route('job-board.post') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Post a Job
                </a>
                @endif
            </div>

            <!-- Filters -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Job Type</label>
                        <select wire:model.live="jobType" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none bg-white">
                            <option value="">All Types</option>
                            <option value="permanent">Permanent</option>
                            <option value="temporary">Temporary</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                        <input type="text" wire:model.live.debounce.300ms="position" placeholder="e.g. Deckhand" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <input type="text" wire:model.live.debounce.300ms="location" placeholder="e.g. Antibes" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <select wire:model.live="department" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none bg-white">
                            <option value="">All Departments</option>
                            <option value="deck">Deck</option>
                            <option value="interior">Interior</option>
                            <option value="engine">Engine</option>
                            <option value="galley">Galley</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search jobs..." class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none">
                </div>
                <div class="mt-4 flex justify-between items-center">
                    <button wire:click="clearFilters" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Clear Filters</button>
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-700">Sort by:</label>
                        <select wire:model.live="sort" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none bg-white">
                            <option value="newest">Newest First</option>
                            <option value="salary_high">Highest Salary</option>
                            <option value="salary_low">Lowest Salary</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Job Listings -->
            <div class="space-y-4">
                @forelse($jobs as $job)
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $job->job_type === 'permanent' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($job->job_type) }}
                                </span>
                                @if($job->featured_posting)
                                <span class="text-yellow-500">⭐ Featured</span>
                                @endif
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                <a href="{{ route('job-board.detail', $job->id) }}" class="hover:text-indigo-600">
                                    {{ $job->position_title }}
                                </a>
                            </h3>
                            @if($job->yacht)
                            <p class="text-gray-600 mb-1">{{ $job->yacht->name }} ({{ $job->vessel_size }}m)</p>
                            @endif
                            <p class="text-gray-600 mb-2">{{ $job->location }}</p>
                            <div class="flex items-center gap-4 text-sm text-gray-500">
                                <span>📅 Posted {{ $job->published_at?->diffForHumans() }}</span>
                                <span>👁️ {{ $job->views_count }} views</span>
                                <span>📝 {{ $job->applications_count }} applications</span>
                            </div>
                        </div>
                        <div class="text-right">
                            @if($job->salary_min || $job->day_rate_min)
                            <div class="text-lg font-semibold text-gray-900 mb-2">
                                @if($job->job_type === 'permanent')
                                    €{{ number_format($job->salary_min ?? 0, 0) }}-{{ number_format($job->salary_max ?? 0, 0) }}/month
                                @else
                                    €{{ number_format($job->day_rate_min ?? 0, 0) }}-{{ number_format($job->day_rate_max ?? 0, 0) }}/day
                                @endif
                            </div>
                            @endif
                            <a href="{{ route('job-board.detail', $job->id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium mb-2 block">
                                View Details →
                            </a>
                            @php
                                $discussionService = app(\App\Services\Forum\ForumDiscussionService::class);
                                $activeDiscussion = $discussionService->getActiveDiscussion('job_board', $job->id, 'job_post');
                            @endphp
                            @if($activeDiscussion)
                                <a href="{{ $activeDiscussion->route }}" 
                                   class="text-sm text-blue-600 hover:text-blue-800 font-medium hover:underline">
                                    View Discussion →
                                </a>
                            @else
                                <x-start-discussion-button 
                                    module="job_board" 
                                    :itemId="$job->id" 
                                    itemType="job_post" 
                                    :itemTitle="$job->position_title . ($job->yacht ? ' - ' . $job->yacht->name : '')"
                                    :itemUrl="route('job-board.detail', $job->id)" />
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white shadow rounded-lg p-12 text-center">
                    <p class="text-gray-500 text-lg">No jobs found matching your criteria.</p>
                    <button wire:click="clearFilters" class="mt-4 text-indigo-600 hover:text-indigo-800">Clear filters</button>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $jobs->links() }}
            </div>
        </div>
    </div>
</div>
