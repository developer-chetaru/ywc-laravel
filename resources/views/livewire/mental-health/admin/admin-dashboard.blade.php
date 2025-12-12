<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('mental-health.dashboard') }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-800">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to User Dashboard
            </a>
        </div>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Mental Health Admin Dashboard</h1>
            <p class="mt-2 text-gray-600">Overview of mental health platform metrics</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Therapists -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Therapists</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_therapists'] }}</p>
                        <p class="text-xs text-green-600 mt-1">{{ $stats['active_therapists'] }} active</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">👥</span>
                    </div>
                </div>
            </div>

            <!-- Sessions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Sessions</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_sessions'] }}</p>
                        <p class="text-xs text-blue-600 mt-1">{{ $stats['upcoming_sessions'] }} upcoming</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">📅</span>
                    </div>
                </div>
            </div>

            <!-- Crisis Sessions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Crisis Sessions</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['crisis_sessions_week'] }}</p>
                        <p class="text-xs text-red-600 mt-1">{{ $stats['crisis_sessions_today'] }} today</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">🚨</span>
                    </div>
                </div>
            </div>

            <!-- Resources -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Resources</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_resources'] }}</p>
                        <p class="text-xs text-purple-600 mt-1">Published</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">📚</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <a href="{{ route('mental-health.admin.therapists') }}" 
               class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <span class="text-2xl">👨‍⚕️</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Manage Therapists</h3>
                        <p class="text-sm text-gray-600">{{ $stats['pending_applications'] }} pending</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('mental-health.admin.resources') }}" 
               class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                        <span class="text-2xl">📖</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Manage Resources</h3>
                        <p class="text-sm text-gray-600">{{ $stats['total_resources'] }} resources</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('mental-health.admin.sessions') }}" 
               class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <span class="text-2xl">📅</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Manage Sessions</h3>
                        <p class="text-sm text-gray-600">{{ $stats['upcoming_sessions'] }} upcoming</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('mental-health.admin.analytics') }}" 
               class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                        <span class="text-2xl">📊</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">View Analytics</h3>
                        <p class="text-sm text-gray-600">{{ $stats['active_users'] }} active users</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Pending Applications -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Pending Applications</h2>
                    <a href="{{ route('mental-health.admin.therapists') }}" class="text-blue-600 hover:text-blue-800 text-sm">View All</a>
                </div>
                @if($pendingApplications->count() > 0)
                    <div class="space-y-3">
                        @foreach($pendingApplications as $therapist)
                            <div class="border-l-4 border-yellow-500 pl-4 py-2">
                                <p class="font-medium text-gray-900">
                                    {{ $therapist->user->first_name }} {{ $therapist->user->last_name }}
                                </p>
                                <p class="text-sm text-gray-600">{{ $therapist->user->email }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Applied {{ $therapist->created_at->diffForHumans() }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No pending applications</p>
                @endif
            </div>

            <!-- Recent Sessions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Sessions</h2>
                </div>
                @if($recentSessions->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentSessions as $session)
                            <div class="border-l-4 border-blue-500 pl-4 py-2">
                                <p class="font-medium text-gray-900">
                                    {{ $session->user->first_name }} with {{ $session->therapist->user->first_name }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($session->scheduled_at)->format('M d, Y g:i A') }}
                                </p>
                                <span class="text-xs px-2 py-1 rounded-full 
                                    {{ $session->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($session->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($session->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No recent sessions</p>
                @endif
            </div>
        </div>
    </div>
</div>
