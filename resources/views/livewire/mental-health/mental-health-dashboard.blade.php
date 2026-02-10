<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Mental Health & Wellness Support</h1>
                <p class="mt-2 text-gray-600">Your comprehensive mental wellness dashboard</p>
            </div>
            @role('super_admin')
            <a href="{{ route('mental-health.admin.dashboard') }}" 
               class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition">
                Admin Dashboard
            </a>
            @endrole
        </div>

        <!-- Quick Actions -->
        <div class="grid  md:grid-cols-3 gap-4 mb-8 max-[992px]:!grid-cols-1">
            <a href="{{ route('mental-health.crisis') }}" class="bg-red-600 hover:bg-red-700 text-white p-6 rounded-lg shadow-md transition">
                <div class="flex items-center">
                    <svg class="w-8 h-8 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold">Crisis Support</h3>
                        <p class="text-sm opacity-90">24/7 Immediate Help</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('mental-health.therapists') }}" class="bg-blue-600 hover:bg-blue-700 text-white p-6 rounded-lg shadow-md transition">
                <div class="flex items-center">
                    <svg class="w-8 h-8 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold">Find a Therapist</h3>
                        <p class="text-sm opacity-90">Browse Available Therapists</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('mental-health.book-session') }}" class="bg-green-600 hover:bg-green-700 text-white p-6 rounded-lg shadow-md transition">
                <div class="flex items-center">
                    <svg class="w-8 h-8 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold">Book Session</h3>
                        <p class="text-sm opacity-90">Schedule Your Session</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Upcoming Sessions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Upcoming Sessions</h2>
                        <a href="{{ route('mental-health.book-session') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Book New Session</a>
                    </div>
                    
                    @if($upcomingSessions->count() > 0)
                        <div class="space-y-4">
                            @foreach($upcomingSessions as $session)
                                <div class="border-l-4 border-blue-500 pl-4 py-2">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-gray-900">
                                                {{ $session->therapist->user->first_name }} {{ $session->therapist->user->last_name }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ \Carbon\Carbon::parse($session->scheduled_at)->format('M d, Y g:i A') }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ ucfirst($session->session_type) }} • {{ $session->duration_minutes }} minutes
                                            </p>
                                            @if($session->session_cost > 0)
                                                <p class="text-xs text-gray-500">£{{ number_format($session->session_cost, 2) }}</p>
                                            @endif
                                        </div>
                                        <div class="flex flex-col items-end gap-1">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                {{ $session->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($session->status) }}
                                            </span>
                                            <a href="{{ route('mental-health.sessions') }}" class="text-xs text-blue-600 hover:underline">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No upcoming sessions. <a href="{{ route('mental-health.book-session') }}" class="text-blue-600 hover:underline">Book your first session</a></p>
                    @endif
                </div>

                <!-- Recent Mood Tracking -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Recent Mood Tracking</h2>
                        <a href="{{ route('mental-health.mood-tracking') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
                    </div>
                    
                    @if($recentMoodEntries->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentMoodEntries as $entry)
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <div class="flex items-center">
                                        <span class="text-2xl mr-3">
                                            @if($entry->mood_rating >= 7) 😊
                                            @elseif($entry->mood_rating >= 4) 😐
                                            @else 😔
                                            @endif
                                        </span>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ \Carbon\Carbon::parse($entry->tracked_date)->format('M d, Y') }}
                                            </p>
                                            <p class="text-xs text-gray-500">Rating: {{ $entry->mood_rating }}/10</p>
                                        </div>
                                    </div>
                                    @if($entry->primary_mood)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($entry->primary_mood) }}
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No mood entries yet. <a href="{{ route('mental-health.mood-tracking') }}" class="text-blue-600 hover:underline">Start tracking</a></p>
                    @endif
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Credit Balance -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Credit Balance</h2>
                    <div class="text-3xl font-bold text-green-600 mb-2">
                        £{{ number_format($creditBalance, 2) }}
                    </div>
                    <p class="text-sm text-gray-500">Available for therapy sessions</p>
                </div>

                <!-- Active Goals -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Active Goals</h2>
                    </div>
                    
                    @if($activeGoals->count() > 0)
                        <div class="space-y-4">
                            @foreach($activeGoals as $goal)
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $goal->title }}</p>
                                        <span class="text-xs text-gray-500">{{ number_format($goal->progress_percentage, 0) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $goal->progress_percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm text-center py-4">No active goals</p>
                    @endif
                </div>

                <!-- Recommended Resources -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Recommended Resources</h2>
                        <a href="{{ route('mental-health.resources') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
                    </div>
                    
                    @if($recommendedResources->count() > 0)
                        <div class="space-y-3">
                            @foreach($recommendedResources as $resource)
                                <div class="border-l-4 border-green-500 pl-3 py-2">
                                    <p class="text-sm font-medium text-gray-900">{{ $resource->title }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ ucfirst($resource->category) }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm text-center py-4">No resources available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
