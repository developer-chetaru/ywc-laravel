<div class="w-full">
    <div class="w-full max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Work Schedule Management</h1>
                <p class="text-gray-600 mt-1">Plan and manage crew work schedules with quick confirmation</p>
            </div>
            @if($viewMode === 'list')
                <button wire:click="createSchedule" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                    + Create Schedule
                </button>
            @endif
        </div>

        <!-- Flash Message -->
        @if (session()->has('message'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('message') }}
            </div>
        @endif

        <!-- List View -->
        @if($viewMode === 'list')
            <!-- Filters -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                        <select wire:model.live="dateRange" class="w-full border-gray-300 rounded-md shadow-sm">
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
                    @if(auth()->user()->hasRole(['captain', 'super_admin']))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Crew Member</label>
                            <select wire:model.live="filterUserId" class="w-full border-gray-300 rounded-md">
                                <option value="">All Crew</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model.live="filterStatus" class="w-full border-gray-300 rounded-md">
                            <option value="all">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="modified">Modified</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Schedules List -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Crew</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($schedules as $schedule)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $schedule->schedule_date->format('M d, Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $schedule->schedule_date->format('D') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $schedule->user->first_name }} {{ $schedule->user->last_name }}</div>
                                        @if($schedule->department)
                                            <div class="text-sm text-gray-500">{{ $schedule->department }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if($schedule->start_time && $schedule->end_time)
                                                {{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}
                                            @else
                                                <span class="text-gray-400">Not set</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ number_format($schedule->planned_hours, 1) }}h</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $schedule->location_status)) }}</div>
                                        @if($schedule->location_name)
                                            <div class="text-sm text-gray-500">{{ $schedule->location_name }}</div>
                                        @endif
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            @if($schedule->user_id === auth()->id() && $schedule->status === 'pending')
                                                <button wire:click="quickConfirm({{ $schedule->id }})" 
                                                    class="text-green-600 hover:text-green-900" 
                                                    title="Quick Confirm">
                                                    ✓
                                                </button>
                                            @endif
                                            @if($schedule->user_id === auth()->id() && $schedule->status === 'pending')
                                                <button wire:click="modifySchedule({{ $schedule->id }})" 
                                                    class="text-blue-600 hover:text-blue-900" 
                                                    title="Modify">
                                                    ✏️
                                                </button>
                                            @endif
                                            @if(auth()->user()->hasRole(['captain', 'super_admin']) || $schedule->user_id === auth()->id())
                                                <button wire:click="editSchedule({{ $schedule->id }})" 
                                                    class="text-indigo-600 hover:text-indigo-900" 
                                                    title="Edit">
                                                    Edit
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No schedules found for the selected period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Create/Edit Form -->
        @if($viewMode === 'create' || $viewMode === 'edit')
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">
                    {{ $viewMode === 'create' ? 'Create New Schedule' : 'Edit Schedule' }}
                </h2>

                <form wire:submit.prevent="saveSchedule" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- User Selection (Captain only) -->
                        @if(auth()->user()->hasRole(['captain', 'super_admin']))
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Crew Member *</label>
                                <select wire:model="userId" class="w-full border-gray-300 rounded-md shadow-sm">
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                    @endforeach
                                </select>
                                @error('userId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <!-- Yacht Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Yacht</label>
                            <select wire:model="yachtId" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Select Yacht</option>
                                @foreach($yachts as $yacht)
                                    <option value="{{ $yacht->id }}">{{ $yacht->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Schedule Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Schedule Date *</label>
                            <input type="date" wire:model="scheduleDate" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            @error('scheduleDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Work Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Work Type *</label>
                            <select wire:model="workType" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="regular_duties">Regular Duties</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="guest_service">Guest Service</option>
                                <option value="emergency_standby">Emergency Standby</option>
                                <option value="shore_leave">Shore Leave</option>
                                <option value="rest_period">Rest Period</option>
                            </select>
                            @error('workType') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Start Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Time *</label>
                            <input type="time" wire:model="startTime" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            @error('startTime') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- End Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Time *</label>
                            <input type="time" wire:model="endTime" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            @error('endTime') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Break Minutes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Break Minutes</label>
                            <input type="number" wire:model="breakMinutes" min="0" class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Location Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location Status *</label>
                            <select wire:model="locationStatus" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="in_port">In Port</option>
                                <option value="at_sea">At Sea</option>
                                <option value="in_shipyard">In Shipyard</option>
                                <option value="at_anchor">At Anchor</option>
                            </select>
                            @error('locationStatus') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Location Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location Name</label>
                            <input type="text" wire:model="locationName" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="e.g., Monaco, Barcelona">
                        </div>

                        <!-- Department -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                            <input type="text" wire:model="department" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="e.g., Deck, Engine, Interior">
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea wire:model="notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Additional notes or instructions..."></textarea>
                    </div>

                    <!-- Template Selection -->
                    @if($templates->count() > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apply Template (Optional)</label>
                            <select wire:model="templateId" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">None</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->name }} ({{ $template->category_label }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="$set('viewMode', 'list')" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            {{ $viewMode === 'create' ? 'Create Schedule' : 'Update Schedule' }}
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Modification Modal -->
        @if($showModificationModal)
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="modification-modal">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Modify Schedule</h3>
                    
                    <form wire:submit.prevent="saveModification" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Time *</label>
                            <input type="time" wire:model="modificationForm.start_time" class="w-full border-gray-300 rounded-md" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Time *</label>
                            <input type="time" wire:model="modificationForm.end_time" class="w-full border-gray-300 rounded-md" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Break Minutes</label>
                            <input type="number" wire:model="modificationForm.break_minutes" min="0" class="w-full border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reason *</label>
                            <select wire:model="modificationForm.reason_code" class="w-full border-gray-300 rounded-md" required>
                                <option value="">Select Reason</option>
                                <option value="weather_delay">Weather Delay</option>
                                <option value="guest_request">Guest Request</option>
                                <option value="maintenance_priority">Maintenance Priority</option>
                                <option value="emergency">Emergency</option>
                                <option value="crew_request">Crew Request</option>
                                <option value="itinerary_change">Itinerary Change</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea wire:model="modificationForm.reason_description" rows="2" class="w-full border-gray-300 rounded-md" placeholder="Additional details..."></textarea>
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-4">
                            <button type="button" wire:click="$set('showModificationModal', false)" 
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Save Modification
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

