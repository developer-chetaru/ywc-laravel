<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Verification Appeals Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Total Appeals</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                </div>
                <div class="bg-yellow-50 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-yellow-800">Pending</div>
                    <div class="text-2xl font-bold text-yellow-900">{{ $stats['pending'] }}</div>
                </div>
                <div class="bg-blue-50 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-blue-800">Under Review</div>
                    <div class="text-2xl font-bold text-blue-900">{{ $stats['under_review'] }}</div>
                </div>
                <div class="bg-green-50 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-green-800">Resolved</div>
                    <div class="text-2xl font-bold text-green-900">{{ $stats['resolved'] }}</div>
                </div>
                <div class="bg-red-50 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-red-800">High Priority</div>
                    <div class="text-2xl font-bold text-red-900">{{ $stats['high_priority'] }}</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 mb-6">
                <form method="GET" class="flex gap-4">
                    <select name="status" class="rounded-md border-gray-300">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>Under Review</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    <select name="priority" class="rounded-md border-gray-300">
                        <option value="">All Priority</option>
                        <option value="1" {{ request('priority') === '1' ? 'selected' : '' }}>High</option>
                        <option value="2" {{ request('priority') === '2' ? 'selected' : '' }}>Medium</option>
                        <option value="3" {{ request('priority') === '3' ? 'selected' : '' }}>Low</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Appeals List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Document</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($appeals as $appeal)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $appeal->appeal_reference }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $appeal->user->first_name }} {{ $appeal->user->last_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $appeal->document->document_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $appeal->status_color }}-100 text-{{ $appeal->status_color }}-800">
                                    {{ ucfirst($appeal->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $appeal->priority_color }}-100 text-{{ $appeal->priority_color }}-800">
                                    {{ $appeal->priority_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $appeal->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('verification.appeals.show', $appeal->id) }}" class="text-blue-600 hover:text-blue-900">
                                    View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No appeals found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="px-6 py-4">
                    {{ $appeals->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
