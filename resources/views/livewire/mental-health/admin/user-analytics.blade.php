<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('mental-health.admin.dashboard') }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-800">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Admin Dashboard
            </a>
        </div>

        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">User Analytics</h1>
                <p class="mt-2 text-gray-600">Platform usage and engagement metrics</p>
            </div>
            <div>
                <select wire:model.live="timeRange" class="rounded-md border-gray-300 shadow-sm">
                    <option value="week">Last Week</option>
                    <option value="month">Last Month</option>
                    <option value="year">Last Year</option>
                </select>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="text-sm text-gray-600">Active Users</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['active_users'] }}</p>
                <p class="text-xs text-gray-500 mt-1">of {{ $stats['total_users'] }} total</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="text-sm text-gray-600">Total Sessions</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_sessions'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['completed_sessions'] }} completed</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="text-sm text-gray-600">Mood Entries</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_mood_entries']) }}</p>
                <p class="text-xs text-gray-500 mt-1">Avg: {{ number_format($stats['average_mood'], 1) }}/10</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="text-sm text-gray-600">Goal Completion</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['goal_completion_rate'] }}%</p>
                <p class="text-xs text-gray-500 mt-1">Completion rate</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Top Therapists -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Top Therapists</h2>
                @if($topTherapists->count() > 0)
                    <div class="space-y-3">
                        @foreach($topTherapists as $item)
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ $item->therapist->user->first_name }} {{ $item->therapist->user->last_name }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900">{{ $item->session_count }} sessions</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No data available</p>
                @endif
            </div>

            <!-- Popular Resources -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Popular Resources</h2>
                @if($popularResources->count() > 0)
                    <div class="space-y-3">
                        @foreach($popularResources as $resource)
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $resource->title }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst($resource->category) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900">{{ number_format($resource->view_count) }} views</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No data available</p>
                @endif
            </div>
        </div>

        <!-- Revenue -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Revenue</h2>
            <div class="text-center py-8">
                <p class="text-4xl font-bold text-gray-900">£{{ number_format($stats['total_revenue'], 2) }}</p>
                <p class="text-sm text-gray-600 mt-2">Total revenue from completed sessions</p>
            </div>
        </div>
    </div>
</div>
