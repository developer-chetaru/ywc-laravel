<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">YWC Job Board</h1>
            <p class="text-lg text-gray-600 mb-8">The Complete Maritime Employment Platform</p>

            @if($isAdmin)
            <!-- Admin Stats -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-blue-900">Admin Panel</h3>
                        <p class="text-sm text-blue-700">Manage verifications, jobs, applications, and bookings</p>
                    </div>
                    <a href="{{ route('job-board.admin') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 font-medium">
                        Go to Admin Panel
                    </a>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
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
                                    <dd class="text-lg font-medium text-gray-900">{{ $adminStats['pending_verifications'] ?? 0 }}</dd>
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
                                    <dd class="text-lg font-medium text-gray-900">{{ $adminStats['total_jobs'] ?? 0 }}</dd>
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
                                    <dd class="text-lg font-medium text-gray-900">{{ $adminStats['total_applications'] ?? 0 }}</dd>
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
                                    <dd class="text-lg font-medium text-gray-900">{{ $adminStats['pending_bookings'] ?? 0 }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <!-- User Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Applications</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $activeApplicationsCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                @if($isVerified)
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">My Job Posts</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $myJobPostsCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Available Jobs</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $recentJobs->count() }}+</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($pendingPaymentCount) && $pendingPaymentCount > 0)
                <div class="bg-yellow-50 border border-yellow-200 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-yellow-800 truncate">Pending Payments</dt>
                                    <dd class="text-lg font-medium text-yellow-900">{{ $pendingPaymentCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Quick Actions -->
            @if($isAdmin)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <a href="{{ route('job-board.admin') }}" class="block bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Admin Panel</h3>
                        <p class="text-gray-600">Manage verifications, jobs, applications, and bookings</p>
                        <div class="mt-4">
                            <span class="inline-flex items-center text-indigo-600 font-medium">
                                Manage System
                                <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
                <a href="{{ route('job-board.browse') }}" class="block bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">View All Jobs</h3>
                        <p class="text-gray-600">Browse all job postings in the system</p>
                        <div class="mt-4">
                            <span class="inline-flex items-center text-indigo-600 font-medium">
                                View Jobs
                                <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <a href="{{ route('job-board.browse') }}" class="block bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Browse Jobs</h3>
                        <p class="text-gray-600">Search for permanent positions and temporary work opportunities</p>
                        <div class="mt-4">
                            <span class="inline-flex items-center text-indigo-600 font-medium">
                                View Jobs
                                <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>

                @if($isVerified)
                <a href="{{ route('job-board.post') }}" class="block bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Post a Job</h3>
                        <p class="text-gray-600">Create a new job posting for permanent or temporary positions</p>
                        <div class="mt-4">
                            <span class="inline-flex items-center text-indigo-600 font-medium">
                                Post Job
                                <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
                @else
                <div class="block bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-yellow-900 mb-2">Get Verified</h3>
                    <p class="text-yellow-700">Verify your vessel/captain account to post jobs and hire crew</p>
                    <a href="{{ route('job-board.verify') }}" class="mt-3 inline-block text-yellow-800 font-medium hover:text-yellow-900">
                        Start Verification →
                    </a>
                </div>
                @endif

                @if(!$isAdmin)
                <a href="{{ route('job-board.bookings') }}" class="block bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">My Bookings</h3>
                        <p class="text-gray-600">View and manage your temporary work bookings</p>
                        <div class="mt-4">
                            <span class="inline-flex items-center text-indigo-600 font-medium">
                                View Bookings
                                <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
                @endif
            </div>
            @endif

            <!-- Recent Jobs -->
            @if($recentJobs->count() > 0)
            <div class="mt-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Recent Job Postings</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($recentJobs as $job)
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $job->job_type === 'permanent' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($job->job_type) }}
                                </span>
                                @if($job->featured_posting)
                                <span class="text-yellow-500">⭐ Featured</span>
                                @endif
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $job->position_title }}</h3>
                            @if($job->yacht)
                            <p class="text-sm text-gray-600 mb-2">{{ $job->yacht->name }} ({{ $job->vessel_size }}m)</p>
                            @endif
                            <p class="text-sm text-gray-600 mb-4">{{ $job->location }}</p>
                            <div class="flex items-center justify-between">
                                @if($job->salary_min || $job->day_rate_min)
                                <span class="text-lg font-semibold text-gray-900">
                                    @if($job->job_type === 'permanent')
                                        €{{ number_format($job->salary_min ?? 0, 0) }}-{{ number_format($job->salary_max ?? 0, 0) }}/month
                                    @else
                                        €{{ number_format($job->day_rate_min ?? 0, 0) }}-{{ number_format($job->day_rate_max ?? 0, 0) }}/day
                                    @endif
                                </span>
                                @endif
                                <a href="{{ route('job-board.detail', $job->id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                    View Details →
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
