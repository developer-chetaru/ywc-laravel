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
            
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Job Board Admin Panel</h1>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Verifications</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['pending_verifications'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Jobs</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['total_jobs'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Jobs</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['active_jobs'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Applications</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['total_applications'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Bookings</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['pending_bookings'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button wire:click="$set('activeTab', 'verifications')" 
                            class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'verifications' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Verifications ({{ $stats['pending_verifications'] }})
                        </button>
                        <button wire:click="$set('activeTab', 'jobs')" 
                            class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'jobs' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            All Jobs
                        </button>
                        <button wire:click="$set('activeTab', 'applications')" 
                            class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'applications' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Applications
                        </button>
                        <button wire:click="$set('activeTab', 'bookings')" 
                            class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'bookings' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Pending Bookings ({{ $stats['pending_bookings'] }})
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Verifications Tab -->
            @if($activeTab === 'verifications')
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold">Pending Vessel Verifications</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($verifications as $verification)
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-medium text-gray-900">{{ $verification->user->name }}</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    Vessel: {{ $verification->vessel_name }}
                                    @if($verification->yacht)
                                    ({{ $verification->yacht->name }})
                                    @endif
                                </p>
                                <p class="text-sm text-gray-600">Role: {{ $verification->role_on_vessel }}</p>
                                <p class="text-sm text-gray-500 mt-2">{{ $verification->authority_description }}</p>
                                <p class="text-xs text-gray-400 mt-2">Submitted: {{ $verification->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex gap-2 ml-4">
                                <button wire:click="approveVerification({{ $verification->id }})" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 font-medium">
                                    Approve
                                </button>
                                <button wire:click="rejectVerification({{ $verification->id }})" 
                                    class="px-4 py-2 bg-red-600 text-white rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 font-medium">
                                    Reject
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <p class="text-gray-500">No pending verifications.</p>
                    </div>
                    @endforelse
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $verifications->links() }}
                </div>
            </div>
            @endif

            <!-- Jobs Tab -->
            @if($activeTab === 'jobs')
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold">All Job Posts</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($jobs as $job)
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-2 py-1 rounded text-xs font-medium {{ $job->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($job->status) }}
                                    </span>
                                    <span class="px-2 py-1 rounded text-xs font-medium {{ $job->job_type === 'permanent' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ ucfirst($job->job_type) }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">{{ $job->position_title }}</h3>
                                <p class="text-sm text-gray-600">Posted by: {{ $job->user->name }}</p>
                                <p class="text-sm text-gray-600">Location: {{ $job->location }}</p>
                                <p class="text-xs text-gray-400 mt-2">Created: {{ $job->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex gap-2 ml-4">
                                <a href="{{ route('job-board.detail', $job->id) }}" 
                                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    View
                                </a>
                                <button wire:click="toggleJobStatus({{ $job->id }})" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    {{ $job->status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                                <button wire:click="deleteJob({{ $job->id }})" 
                                    onclick="return confirm('Are you sure you want to delete this job post?')"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <p class="text-gray-500">No job posts found.</p>
                    </div>
                    @endforelse
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $jobs->links() }}
                </div>
            </div>
            @endif

            <!-- Applications Tab -->
            @if($activeTab === 'applications')
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold">All Applications</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($applications as $application)
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-medium text-gray-900">{{ $application->user->name }}</h3>
                                <p class="text-sm text-gray-600">Applied for: {{ $application->jobPost->position_title }}</p>
                                <span class="inline-block px-2 py-1 mt-2 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($application->status) }}
                                </span>
                                @if($application->match_score)
                                <span class="inline-block px-2 py-1 mt-2 ml-2 rounded text-xs font-medium bg-green-100 text-green-800">
                                    {{ number_format($application->match_score, 0) }}% Match
                                </span>
                                @endif
                                <p class="text-xs text-gray-400 mt-2">Applied: {{ $application->submitted_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex gap-2 ml-4">
                                <a href="{{ route('job-board.detail', $application->job_post_id) }}" 
                                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    View Job
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <p class="text-gray-500">No applications found.</p>
                    </div>
                    @endforelse
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $applications->links() }}
                </div>
            </div>
            @endif

            <!-- Bookings Tab -->
            @if($activeTab === 'bookings')
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold">Pending Bookings</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($bookings as $booking)
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-medium text-gray-900">{{ $booking->user->name }}</h3>
                                <p class="text-sm text-gray-600">Job: {{ $booking->jobPost->position_title }}</p>
                                <p class="text-sm text-gray-600">Booked by: {{ $booking->bookedBy->name }}</p>
                                <p class="text-sm text-gray-600">Date: {{ $booking->work_date->format('M d, Y') }}</p>
                                <p class="text-sm text-gray-600">Total: €{{ number_format($booking->total_payment, 0) }}</p>
                                <p class="text-xs text-gray-400 mt-2">Created: {{ $booking->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex gap-2 ml-4">
                                <a href="{{ route('job-board.detail', $booking->job_post_id) }}" 
                                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <p class="text-gray-500">No pending bookings.</p>
                    </div>
                    @endforelse
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $bookings->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

