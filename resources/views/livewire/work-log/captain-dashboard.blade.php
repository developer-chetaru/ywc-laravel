<div class="w-full">
    <div class="w-full max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Captain's Dashboard</h1>
            <p class="text-gray-600 mt-1">Oversight of crew schedules, hours, and compliance</p>
        </div>

        <!-- Today's Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600">Pending Schedules</div>
                <div class="text-2xl font-bold text-yellow-600">{{ $todayOverview['pending_schedules'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600">Confirmed Today</div>
                <div class="text-2xl font-bold text-green-600">{{ $todayOverview['confirmed_schedules'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600">Crew on Duty</div>
                <div class="text-2xl font-bold text-blue-600">{{ $todayOverview['crew_on_duty'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600">Compliance Issues</div>
                <div class="text-2xl font-bold text-red-600">{{ $todayOverview['violation_logs'] + $todayOverview['warning_logs'] }}</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                    <select wire:model.live="dateRange" class="w-full border-gray-300 rounded-md">
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
                @if($dateRange === 'custom')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" wire:model.live="customStartDate" class="w-full border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" wire:model.live="customEndDate" class="w-full border-gray-300 rounded-md">
                    </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Crew Member</label>
                    <select wire:model.live="filterUserId" class="w-full border-gray-300 rounded-md">
                        <option value="">All Crew</option>
                        @foreach($crewMembers as $crew)
                            <option value="{{ $crew->id }}">{{ $crew->first_name }} {{ $crew->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model.live="filterStatus" class="w-full border-gray-300 rounded-md">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="modified">Modified</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Compliance Summary Table -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Crew Compliance Summary</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Crew Member</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days Worked</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Compliant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Warnings</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Violations</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Weekly Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pending</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($complianceSummary as $summary)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $summary['user']->first_name }} {{ $summary['user']->last_name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($summary['total_hours'], 1) }}h
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $summary['total_days'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-green-600 font-medium">{{ $summary['compliant_days'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-yellow-600 font-medium">{{ $summary['warning_days'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-red-600 font-medium">{{ $summary['violation_days'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $weekly = $summary['weekly_compliance'];
                                        $workHours = $weekly['total_work_hours'] ?? 0;
                                        $remaining = $weekly['remaining_work_hours'] ?? 0;
                                    @endphp
                                    <div class="text-sm">
                                        <div class="font-medium {{ $weekly['is_compliant'] ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($workHours, 1) }}h / 72h
                                        </div>
                                        @if($remaining !== null)
                                            <div class="text-xs text-gray-500">Remaining: {{ number_format($remaining, 1) }}h</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($summary['pending_schedules'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ $summary['pending_schedules'] }} pending
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">None</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Schedules List -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Crew Schedules</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Crew</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($crewSchedules as $schedule)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $schedule->schedule_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $schedule->user->first_name }} {{ $schedule->user->last_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($schedule->start_time && $schedule->end_time)
                                        {{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($schedule->planned_hours, 1) }}h
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ucfirst(str_replace('_', ' ', $schedule->location_status)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($schedule->status === 'confirmed') bg-green-100 text-green-800
                                        @elseif($schedule->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($schedule->status === 'modified') bg-blue-100 text-blue-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ $schedule->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No schedules found for the selected period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

