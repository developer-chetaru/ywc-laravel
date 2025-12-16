<div>
    @if($job)
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('job-board.browse') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Browse Jobs
                </a>
            </div>
            
            <!-- Header -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $job->job_type === 'permanent' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($job->job_type) }}
                        </span>
                        <h1 class="text-3xl font-bold text-gray-900 mt-2">{{ $job->position_title }}</h1>
                        @if($job->yacht)
                        <p class="text-xl text-gray-600 mt-1">{{ $job->yacht->name }} ({{ $job->vessel_size }}m)</p>
                        @endif
                        <p class="text-gray-600 mt-2">📍 {{ $job->location }}</p>
                    </div>
                    <div class="flex gap-2">
                        @if(Auth::check())
                        <button wire:click="toggleSave" class="px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $isSaved ? 'bg-yellow-50 border-yellow-300 text-yellow-800' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
                            {{ $isSaved ? '⭐ Saved' : '💾 Save' }}
                        </button>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <p class="text-sm text-gray-500">Compensation</p>
                        <p class="text-lg font-semibold">
                            @if($job->job_type === 'permanent')
                                €{{ number_format($job->salary_min ?? 0, 0) }}-{{ number_format($job->salary_max ?? 0, 0) }}/month
                            @else
                                €{{ number_format($job->day_rate_min ?? 0, 0) }}-{{ number_format($job->day_rate_max ?? 0, 0) }}/day
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Posted</p>
                        <p class="text-lg font-semibold">{{ $job->published_at?->diffForHumans() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Applications</p>
                        <p class="text-lg font-semibold">{{ $job->applications_count }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Views</p>
                        <p class="text-lg font-semibold">{{ $job->views_count }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    @if($job->about_position)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-xl font-semibold mb-4">About the Position</h2>
                        <p class="text-gray-700 whitespace-pre-line">{{ $job->about_position }}</p>
                    </div>
                    @endif

                    @if($job->responsibilities)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-xl font-semibold mb-4">Responsibilities</h2>
                        <p class="text-gray-700 whitespace-pre-line">{{ $job->responsibilities }}</p>
                    </div>
                    @endif

                    @if($job->required_certifications || $job->min_years_experience)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-xl font-semibold mb-4">Requirements</h2>
                        <ul class="space-y-2">
                            @if($job->min_years_experience)
                            <li class="text-gray-700">• Minimum {{ $job->min_years_experience }} years experience</li>
                            @endif
                            @if($job->required_certifications)
                                @foreach($job->required_certifications as $cert)
                                <li class="text-gray-700">• {{ $cert }}</li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Apply/Book Button -->
                    @if(Auth::check() && !Auth::user()->hasRole('super_admin') && !$hasApplied && $job->user_id !== Auth::id())
                    <div class="bg-white shadow rounded-lg p-6">
                        @if($job->job_type === 'permanent')
                        <a href="{{ route('job-board.apply', $job->id) }}" class="w-full bg-indigo-600 text-white text-center py-3 rounded-md hover:bg-indigo-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 block font-medium">
                            Apply Now
                        </a>
                        @else
                        <a href="{{ route('job-board.apply-temporary', $job->id) }}" class="w-full bg-indigo-600 text-white text-center py-3 rounded-md hover:bg-indigo-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 block font-medium">
                            Apply for This Work
                        </a>
                        @endif
                    </div>
                    @elseif($hasApplied && !Auth::user()->hasRole('super_admin'))
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        @if($job->job_type === 'permanent')
                        <p class="text-blue-800 font-medium">✓ Application Submitted</p>
                        <p class="text-blue-700 text-sm mt-1">Your application has been sent to the captain for review.</p>
                        <a href="{{ route('job-board.my-applications') }}" class="text-blue-600 text-sm mt-2 inline-block font-medium">View Application Status →</a>
                        @else
                        <p class="text-blue-800 font-medium">✓ Application Submitted</p>
                        <p class="text-blue-700 text-sm mt-1">Your booking request has been sent. The captain will confirm shortly.</p>
                        <a href="{{ route('job-board.bookings') }}" class="text-blue-600 text-sm mt-2 inline-block font-medium">View Booking Details →</a>
                        @endif
                    </div>
                    @endif

                    <!-- Job Details -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="font-semibold mb-4">Job Details</h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Department:</dt>
                                <dd class="font-medium">{{ ucfirst($job->department) }}</dd>
                            </div>
                            @if($job->contract_type)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Contract:</dt>
                                <dd class="font-medium">{{ ucfirst(str_replace('_', ' ', $job->contract_type)) }}</dd>
                            </div>
                            @endif
                            @if($job->start_date)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Start Date:</dt>
                                <dd class="font-medium">{{ $job->start_date->format('M d, Y') }}</dd>
                            </div>
                            @endif
                            @if($job->work_start_date && $job->isTemporary())
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Work Date:</dt>
                                <dd class="font-medium">{{ $job->work_start_date->format('M d, Y') }}</dd>
                            </div>
                            @endif
                            @if($job->day_rate_min && $job->isTemporary())
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Day Rate:</dt>
                                <dd class="font-medium">€{{ number_format($job->day_rate_min, 0) }}/day</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
