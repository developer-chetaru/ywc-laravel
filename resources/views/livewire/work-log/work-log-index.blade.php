<div class="w-full">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <div class="w-full max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Work & Rest Hours Log</h1>
            <p class="text-gray-600 mt-1">Track your daily work hours and ensure MLC compliance</p>
        </div>

        <!-- View Mode Tabs -->
        <div class="mb-6 border-b border-gray-200 overflow-x-auto">
            <nav class="-mb-px flex space-x-4 sm:space-x-8 min-w-max">
                <button wire:click="$set('viewMode', 'dashboard')" 
                    class="@if($viewMode === 'dashboard') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Dashboard
                </button>
                <button wire:click="newEntry" 
                    class="@if($viewMode === 'entry') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Log Entry
                </button>
                <button wire:click="$set('viewMode', 'history')" 
                    class="@if($viewMode === 'history') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    History
                </button>
                <button wire:click="$set('viewMode', 'statistics')" 
                    class="@if($viewMode === 'statistics') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Statistics
                </button>
            </nav>
        </div>

        <!-- Flash Message -->
        @if (session()->has('message'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('message') }}
            </div>
        @endif

        <!-- Dashboard View -->
        @if($viewMode === 'dashboard')
            <div class="space-y-6">
                <!-- Compliance Status Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                    <!-- Today's Status -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Today's Status</h3>
                        @php
                            $todayQuery = \App\Models\WorkLog::where('work_date', now()->format('Y-m-d'));
                            if (!auth()->user()->hasRole('super_admin')) {
                                $todayQuery->where('user_id', auth()->id());
                            }
                            $todayLog = $todayQuery->first();
                        @endphp
                        @if($todayLog)
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Work Hours:</span>
                                    <span class="font-semibold">{{ number_format($todayLog->total_hours_worked, 1) }}h</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Rest Hours:</span>
                                    <span class="font-semibold">{{ number_format($todayLog->total_rest_hours, 1) }}h</span>
                                </div>
                                <div class="mt-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if($todayLog->compliance_status === 'compliant') bg-green-100 text-green-800
                                        @elseif($todayLog->compliance_status === 'warning') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($todayLog->compliance_status) }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No entry for today yet</p>
                            <button wire:click="newEntry" class="mt-3 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Log Today's Hours →
                            </button>
                        @endif
                    </div>

                    <!-- Weekly Compliance -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">This Week</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Work Hours:</span>
                                <span class="font-semibold">{{ number_format($weeklyCompliance['total_work_hours'] ?? 0, 1) }}h</span>
                            </div>
                            @if(!($weeklyCompliance['is_aggregate'] ?? false) && isset($weeklyCompliance['remaining_work_hours']))
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Remaining:</span>
                                    <span class="font-semibold text-blue-600">{{ number_format($weeklyCompliance['remaining_work_hours'], 1) }}h</span>
                                </div>
                            @endif
                            @if(($weeklyCompliance['is_aggregate'] ?? false))
                                <div class="text-xs text-gray-500 mt-2">
                                    <p>ℹ️ Aggregate view (all users)</p>
                                    <p>Weekly limit is 72h per user</p>
                                </div>
                            @endif
                            <div class="mt-4">
                                @if(!($weeklyCompliance['is_aggregate'] ?? false))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if(($weeklyCompliance['is_compliant'] ?? true)) bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ($weeklyCompliance['is_compliant'] ?? true) ? 'Compliant' : 'Non-Compliant' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Overall Compliance -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Compliance Rate</h3>
                        <div class="space-y-2">
                            <div class="text-3xl font-bold text-blue-600">
                                {{ number_format($complianceSummary['compliance_percentage'] ?? 100, 1) }}%
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ $complianceSummary['compliant_days'] ?? 0 }} / {{ $complianceSummary['total_days'] ?? 0 }} days compliant
                            </div>
                            @if(($complianceSummary['violation_days'] ?? 0) > 0)
                                <div class="text-sm text-red-600">
                                    {{ $complianceSummary['violation_days'] }} violations
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Last 7 Days</h3>
                    <div class="h-64 w-full overflow-hidden">
                        <canvas id="workRestChart" style="max-width: 100%; height: auto;"></canvas>
                    </div>
                </div>

                <!-- Recent Entries -->
                <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Entries</h3>
                    <div class="overflow-x-auto">
                        <div class="inline-block min-w-full align-middle">
                            <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    @if(auth()->user()->hasRole('super_admin'))
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Work</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rest</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentEntries as $entry)
                                    <tr>
                                        @if(auth()->user()->hasRole('super_admin'))
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $entry->user->first_name ?? '' }} {{ $entry->user->last_name ?? '' }}
                                                <div class="text-xs text-gray-500">{{ $entry->user->email ?? '' }}</div>
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $entry->work_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($entry->total_hours_worked, 1) }}h
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($entry->total_rest_hours, 1) }}h
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $entry->location_status_label }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($entry->compliance_status === 'compliant') bg-green-100 text-green-800
                                                @elseif($entry->compliance_status === 'warning') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($entry->compliance_status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->hasRole('super_admin') ? '6' : '5' }}" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No entries yet. Start logging your hours!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Entry Form View -->
        @if($viewMode === 'entry')
            <div class="bg-white rounded-lg shadow p-6 w-full">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Log Work Hours</h2>
                    <p class="text-gray-600 mt-1">Enter your work and rest hours for the selected date</p>
                </div>

                <form wire:submit.prevent="save" class="space-y-6">
                    <!-- Date Selection -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                            <div class="flex gap-2">
                                <input type="date" wire:model.live="selectedDate" 
                                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @php
                                    $hasEntry = \App\Models\WorkLog::where('work_date', $selectedDate ?? now()->format('Y-m-d'))
                                        ->when(!auth()->user()->hasRole('super_admin'), fn($q) => $q->where('user_id', auth()->id()))
                                        ->exists();
                                @endphp
                                @if($hasEntry && !$isEditing)
                                    <button type="button" wire:click="loadEntryForDate('{{ $selectedDate ?? now()->format('Y-m-d') }}')"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium whitespace-nowrap">
                                        Load Entry
                                    </button>
                                @endif
                            </div>
                            @if($hasEntry && !$isEditing)
                                <p class="text-xs text-blue-600 mt-1">ℹ️ An entry exists for this date. Click "Load Entry" to edit it.</p>
                            @endif
                            @if($isEditing)
                                <p class="text-xs text-green-600 mt-1">✓ Editing existing entry</p>
                            @endif
                        </div>
                        <div class="flex items-end">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="isDayOff" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Mark as Day Off</span>
                            </label>
                        </div>
                    </div>

                    @if(!$isDayOff)
                        <!-- Work Hours Section -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">⏰ Work Information</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                                    <input type="time" wire:model.live="startTime" wire:change="calculateHours"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                                    <input type="time" wire:model.live="endTime" wire:change="calculateHours"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Hours Worked</label>
                                    <input type="number" step="0.1" min="0" max="24" wire:model="totalHoursWorked" readonly
                                        class="w-full rounded-md border-gray-300 shadow-sm bg-gray-50 cursor-not-allowed">
                                    <p class="text-xs text-gray-500 mt-1">Calculated automatically from start/end time</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Break (minutes)</label>
                                    <input type="number" min="0" wire:model.live="breakMinutes" wire:change="calculateHours"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Overtime Hours</label>
                                    <input type="number" step="0.1" min="0" wire:model="overtimeHours" readonly
                                        class="w-full rounded-md border-gray-300 shadow-sm bg-gray-50 cursor-not-allowed">
                                    <p class="text-xs text-gray-500 mt-1">Calculated automatically (hours over 8)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Rest Hours Section -->
                        <div class="border-t pt-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">😴 Rest Information</h3>
                                <button type="button" wire:click="$set('showRestPeriodForm', true)"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">
                                    + Add Rest Period
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Rest Hours (24hr period)</label>
                                    <input type="number" step="0.1" min="0" max="24" wire:model="totalRestHours"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Sleep Hours</label>
                                    <input type="number" min="0" max="24" wire:model="sleepHours"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>

                            <!-- Rest Periods List -->
                            @if(count($restPeriods) > 0)
                                <div class="space-y-2 mb-4">
                                    @foreach($restPeriods as $index => $period)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $period['start_time'] }} - {{ $period['end_time'] }}
                                                    ({{ number_format($period['duration_hours'], 1) }}h)
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ ucfirst(str_replace('_', ' ', $period['type'])) }}
                                                    @if($period['location'])
                                                        • {{ $period['location'] }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button type="button" wire:click="editRestPeriod({{ $index }})"
                                                    class="text-blue-600 hover:text-blue-800 text-sm">Edit</button>
                                                <button type="button" wire:click="removeRestPeriod({{ $index }})"
                                                    class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Rest Period Form Modal -->
                            @if($showRestPeriodForm)
                                <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="restPeriodModal">
                                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                                        <div class="mt-3">
                                            <h3 class="text-lg font-medium text-gray-900 mb-4">Add Rest Period</h3>
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                                                    <input type="time" wire:model="restPeriodStart"
                                                        class="w-full rounded-md border-gray-300 shadow-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                                                    <input type="time" wire:model="restPeriodEnd"
                                                        class="w-full rounded-md border-gray-300 shadow-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                                                    <select wire:model="restPeriodType" class="w-full rounded-md border-gray-300 shadow-sm">
                                                        <option value="night_sleep">Night Sleep</option>
                                                        <option value="afternoon_nap">Afternoon Nap</option>
                                                        <option value="lunch_break">Lunch Break</option>
                                                        <option value="coffee_break">Coffee Break</option>
                                                        <option value="other">Other</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Location (optional)</label>
                                                    <input type="text" wire:model="restPeriodLocation"
                                                        class="w-full rounded-md border-gray-300 shadow-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>
                                                    <textarea wire:model="restPeriodNotes" rows="2"
                                                        class="w-full rounded-md border-gray-300 shadow-sm"></textarea>
                                                </div>
                                            </div>
                                            <div class="flex justify-end space-x-3 mt-6">
                                                <button type="button" wire:click="resetRestPeriodForm"
                                                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                                                    Cancel
                                                </button>
                                                <button type="button" wire:click="addRestPeriod"
                                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                                    {{ $editingRestPeriodIndex !== null ? 'Update' : 'Add' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Location Section -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">📍 Location Status</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select wire:model="locationStatus" class="w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="at_sea">At Sea ⚓</option>
                                        <option value="in_port">In Port 🏖️</option>
                                        <option value="in_yard">In Yard/Dry Dock 🔧</option>
                                        <option value="on_leave">On Leave 🏝️</option>
                                        <option value="shore_leave">Shore Leave 🌆</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Location/Port Name</label>
                                    <input type="text" wire:model="locationName"
                                        class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Port Name</label>
                                    <input type="text" wire:model="portName"
                                        class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                                        <input type="number" step="0.0000001" wire:model="latitude"
                                            class="w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                                        <input type="number" step="0.0000001" wire:model="longitude"
                                            class="w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Yacht Information -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">🚢 Yacht Information</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Yacht</label>
                                    <select wire:model="yachtId" wire:change="updatedYachtId"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">-- Select Yacht --</option>
                                        @foreach($yachts as $yacht)
                                            <option value="{{ $yacht->id }}">{{ $yacht->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Yacht Name</label>
                                    <input type="text" wire:model="yachtName" readonly
                                        class="w-full rounded-md border-gray-300 shadow-sm bg-gray-50 cursor-not-allowed">
                                </div>
                                @if($yachtType)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Yacht Type</label>
                                    <input type="text" wire:model="yachtType" readonly
                                        class="w-full rounded-md border-gray-300 shadow-sm bg-gray-50 cursor-not-allowed">
                                </div>
                                @endif
                                @if($yachtLength)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Length</label>
                                    <input type="text" wire:model="yachtLength" readonly
                                        class="w-full rounded-md border-gray-300 shadow-sm bg-gray-50 cursor-not-allowed">
                                </div>
                                @endif
                                @if($yachtFlag)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Flag Registry</label>
                                    <input type="text" wire:model="yachtFlag" readonly
                                        class="w-full rounded-md border-gray-300 shadow-sm bg-gray-50 cursor-not-allowed">
                                </div>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Position/Rank</label>
                                    <input type="text" wire:model="positionRank"
                                        class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                    <input type="text" wire:model="department"
                                        class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Conditions -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">🌊 Conditions</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Weather</label>
                                    <input type="text" wire:model="weatherConditions"
                                        class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Sea State</label>
                                    <input type="text" wire:model="seaState"
                                        class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Visibility</label>
                                    <input type="text" wire:model="visibility"
                                        class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Activities -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Activities</h3>
                            <div class="flex gap-2 mb-4">
                                <input type="text" wire:model="activityInput" wire:keydown.enter.prevent="addActivity"
                                    placeholder="Enter activity and press Enter"
                                    class="flex-1 rounded-md border-gray-300 shadow-sm">
                                <button type="button" wire:click="addActivity"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Add
                                </button>
                            </div>
                            @if(count($activities) > 0)
                                <div class="space-y-2">
                                    @foreach($activities as $index => $activity)
                                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-md">
                                            <span class="text-sm text-gray-900">{{ $activity }}</span>
                                            <button type="button" wire:click="removeActivity({{ $index }})"
                                                class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Notes -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">📝 Notes</h3>
                            <textarea wire:model="notes" rows="4"
                                class="w-full rounded-md border-gray-300 shadow-sm"
                                placeholder="Additional notes or comments..."></textarea>
                        </div>
                    @endif

                    <!-- Submit Button -->
                    <div class="border-t pt-6">
                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="resetForm"
                                class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Reset
                            </button>
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">
                                Save Entry
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        <!-- History View -->
        @if($viewMode === 'history')
            <div class="bg-white rounded-lg shadow p-6 w-full">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Work Log History</h2>
                <!-- Date Filter -->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filter Period</label>
                        <select wire:model="dateFilter" wire:change="loadComplianceData"
                            class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="week">Last Week</option>
                            <option value="month">Last Month</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    @if($dateFilter === 'custom')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" wire:model="customStartDate" wire:change="loadComplianceData"
                                class="w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" wire:model="customEndDate" wire:change="loadComplianceData"
                                class="w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    @endif
                </div>
                
                <!-- History Table -->
                <div class="overflow-x-auto">
                    <div class="inline-block min-w-full align-middle">
                        <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                @if(auth()->user()->hasRole('super_admin'))
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                @endif
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Work</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rest</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Yacht</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($historyEntries as $entry)
                                <tr>
                                    @if(auth()->user()->hasRole('super_admin'))
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $entry->user->first_name ?? '' }} {{ $entry->user->last_name ?? '' }}
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $entry->work_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($entry->total_hours_worked, 1) }}h
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($entry->total_rest_hours, 1) }}h
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $entry->yacht_name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $entry->location_status_label }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($entry->compliance_status === 'compliant') bg-green-100 text-green-800
                                            @elseif($entry->compliance_status === 'warning') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($entry->compliance_status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button wire:click="loadEntryForDate('{{ $entry->work_date->format('Y-m-d') }}')"
                                            class="text-blue-600 hover:text-blue-800">Edit</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->hasRole('super_admin') ? '8' : '7' }}" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No entries found for the selected period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Statistics View -->
        @if($viewMode === 'statistics')
            <div class="bg-white rounded-lg shadow p-6 w-full">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Statistics & Reports</h2>
                
                <!-- Date Filter -->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filter Period</label>
                        <select wire:model="dateFilter" wire:change="loadComplianceData"
                            class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="month">This Month</option>
                            <option value="week">This Week</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    @if($dateFilter === 'custom')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" wire:model="customStartDate" wire:change="loadComplianceData"
                                class="w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" wire:model="customEndDate" wire:change="loadComplianceData"
                                class="w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    @endif
                </div>
                
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-6">
                    <div class="bg-blue-50 rounded-lg p-6">
                        <h3 class="text-sm font-medium text-blue-600 mb-2">Total Days Worked</h3>
                        <p class="text-3xl font-bold text-blue-900">{{ $statisticsData['total_days'] ?? 0 }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-6">
                        <h3 class="text-sm font-medium text-green-600 mb-2">Total Hours Worked</h3>
                        <p class="text-3xl font-bold text-green-900">{{ number_format($statisticsData['total_hours_worked'] ?? 0, 1) }}h</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-6">
                        <h3 class="text-sm font-medium text-purple-600 mb-2">Average Hours/Day</h3>
                        <p class="text-3xl font-bold text-purple-900">{{ number_format($statisticsData['average_hours_per_day'] ?? 0, 1) }}h</p>
                    </div>
                </div>
                
                <!-- Detailed Statistics -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                    <div class="border rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Location Breakdown</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Days At Sea:</span>
                                <span class="font-semibold">{{ $statisticsData['days_at_sea'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Days In Port:</span>
                                <span class="font-semibold">{{ $statisticsData['days_in_port'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Days On Leave:</span>
                                <span class="font-semibold">{{ $statisticsData['days_on_leave'] ?? 0 }}</span>
        </div>
    </div>
</div>

                    <div class="border rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Compliance Summary</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Compliant Days:</span>
                                <span class="font-semibold text-green-600">{{ $statisticsData['compliant_days'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Violation Days:</span>
                                <span class="font-semibold text-red-600">{{ $statisticsData['violation_days'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Rest Hours:</span>
                                <span class="font-semibold">{{ number_format($statisticsData['total_rest_hours'] ?? 0, 1) }}h</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @endpush

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('workRestChart');
            if (ctx && typeof Chart !== 'undefined') {
                const chartData = @json($chartData);
                const chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.dates || [],
                        datasets: [
                            {
                                label: 'Work Hours',
                                data: chartData.work_hours || [],
                                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Rest Hours',
                                data: chartData.rest_hours || [],
                                backgroundColor: 'rgba(34, 197, 94, 0.5)',
                                borderColor: 'rgba(34, 197, 94, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                left: 10,
                                right: 10,
                                top: 10,
                                bottom: 10
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 24
                            }
                        }
                    }
                });
                
                // Handle window resize
                window.addEventListener('resize', function() {
                    chart.resize();
                });
            }
        });

        // Re-initialize chart when Livewire updates
        Livewire.hook('morph.updated', () => {
            setTimeout(() => {
                const ctx = document.getElementById('workRestChart');
                if (ctx && typeof Chart !== 'undefined') {
                    const chartData = @json($chartData);
                    if (ctx.chart) {
                        ctx.chart.destroy();
                    }
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: chartData.dates || [],
                            datasets: [
                                {
                                    label: 'Work Hours',
                                    data: chartData.work_hours || [],
                                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                                    borderColor: 'rgba(59, 130, 246, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Rest Hours',
                                    data: chartData.rest_hours || [],
                                    backgroundColor: 'rgba(34, 197, 94, 0.5)',
                                    borderColor: 'rgba(34, 197, 94, 1)',
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 24
                                }
                            }
                        }
                    });
                }
            }, 100);
        });
    </script>
</div>
