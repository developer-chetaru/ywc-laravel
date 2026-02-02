<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Moderator Dashboard</h1>
        <p class="text-gray-600 mt-1">Manage reports and moderate forum content</p>
    </div>

    {{-- Statistics --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
            <div class="text-sm text-gray-600">Pending Reports</div>
            <div class="text-2xl font-bold text-red-600">{{ $stats['pending_reports'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
            <div class="text-sm text-gray-600">Resolved Today</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['resolved_reports_today'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
            <div class="text-sm text-gray-600">Active Bans</div>
            <div class="text-2xl font-bold text-orange-600">{{ $stats['active_bans'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
            <div class="text-sm text-gray-600">Warnings Today</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['warnings_today'] }}</div>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="mb-4 border-b border-gray-200">
        <div class="flex gap-4">
            <button wire:click="$set('filter', 'pending')" 
                class="px-4 py-2 border-b-2 {{ $filter === 'pending' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-600' }} font-medium">
                Pending ({{ $stats['pending_reports'] }})
            </button>
            <button wire:click="$set('filter', 'resolved')" 
                class="px-4 py-2 border-b-2 {{ $filter === 'resolved' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-600' }} font-medium">
                Resolved
            </button>
            <button wire:click="$set('filter', 'dismissed')" 
                class="px-4 py-2 border-b-2 {{ $filter === 'dismissed' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-600' }} font-medium">
                Dismissed
            </button>
            <button wire:click="$set('filter', 'all')" 
                class="px-4 py-2 border-b-2 {{ $filter === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-600' }} font-medium">
                All
            </button>
        </div>
    </div>

    {{-- Reports List --}}
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Content</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reporter</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($reports as $report)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <span class="font-medium">{{ ucfirst($report->reportable_type) }}</span>
                                    <span class="text-gray-500">#{{ $report->reportable_id }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $report->reporter_first_name }} {{ $report->reporter_last_name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                    {{ ucfirst(str_replace('_', ' ', $report->reason)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($report->created_at)->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($report->status === 'pending')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif ($report->status === 'resolved')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Resolved</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Dismissed</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if ($report->status === 'pending')
                                    <div class="flex gap-2">
                                        <button wire:click="resolveReport({{ $report->id }})" 
                                            class="text-blue-600 hover:text-blue-800 font-medium">
                                            Review
                                        </button>
                                        @if ($report->reportable_type === 'thread')
                                            <button wire:click="performQuickAction('lock', 'thread', {{ $report->reportable_id }})"
                                                class="text-orange-600 hover:text-orange-800 text-xs">
                                                Lock
                                            </button>
                                            <button wire:click="performQuickAction('delete', 'thread', {{ $report->reportable_id }})"
                                                class="text-red-600 hover:text-red-800 text-xs">
                                                Delete
                                            </button>
                                        @else
                                            <button wire:click="performQuickAction('delete', 'post', {{ $report->reportable_id }})"
                                                class="text-red-600 hover:text-red-800 text-xs">
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">Resolved by {{ $report->moderator_first_name ?? 'N/A' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No reports found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $reports->links() }}
        </div>
    </div>

    {{-- Resolution Modal --}}
    @if ($selectedReportId)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click="cancelResolution">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4" wire:click.stop>
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Resolve Report</h3>
                    
                    <form wire:submit.prevent="submitResolution" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                            <select wire:model="resolutionAction" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                <option value="resolved">Resolve (Action Taken)</option>
                                <option value="dismissed">Dismiss (No Action Needed)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Moderator Notes (Optional)</label>
                            <textarea wire:model="moderatorNotes" rows="4"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                placeholder="Add notes about the resolution..."></textarea>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="button" wire:click="cancelResolution"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
