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

        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Therapist Management</h1>
                <p class="mt-2 text-gray-600">Manage therapist applications and profiles</p>
            </div>
            <a href="{{ route('mental-health.admin.therapists.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                + Add New Therapist
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Total Therapists</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-green-50 rounded-lg shadow p-4">
                <p class="text-sm text-green-600">Approved</p>
                <p class="text-2xl font-bold text-green-700">{{ $stats['approved'] }}</p>
            </div>
            <div class="bg-yellow-50 rounded-lg shadow p-4">
                <p class="text-sm text-yellow-600">Pending</p>
                <p class="text-2xl font-bold text-yellow-700">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-red-50 rounded-lg shadow p-4">
                <p class="text-sm text-red-600">Rejected</p>
                <p class="text-2xl font-bold text-red-700">{{ $stats['rejected'] }}</p>
            </div>
            <div class="bg-blue-50 rounded-lg shadow p-4">
                <p class="text-sm text-blue-600">Active</p>
                <p class="text-2xl font-bold text-blue-700">{{ $stats['active'] }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           placeholder="Search by name or email..." 
                           class="w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <select wire:model.live="statusFilter" class="w-full rounded-md border-gray-300 shadow-sm">
                        <option value="all">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Therapists Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Therapist</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Experience</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sessions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($therapists as $therapist)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $therapist->user->first_name }} {{ $therapist->user->last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $therapist->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $therapist->application_status === 'approved' ? 'bg-green-100 text-green-800' : 
                                       ($therapist->application_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($therapist->application_status) }}
                                </span>
                                @if($therapist->is_active)
                                    <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Active</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $therapist->years_experience ?? 'N/A' }} years
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($therapist->rating > 0)
                                    <span class="text-yellow-400">★</span> {{ number_format($therapist->rating, 1) }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $therapist->total_sessions }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('mental-health.admin.therapists.edit', $therapist->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                <button wire:click="viewTherapist({{ $therapist->id }})" 
                                        class="text-gray-600 hover:text-gray-900 mr-3">View</button>
                                @if($therapist->application_status === 'pending')
                                    <button wire:click="approveTherapist({{ $therapist->id }})" 
                                            class="text-green-600 hover:text-green-900 mr-3">Approve</button>
                                    <button wire:click="rejectTherapist({{ $therapist->id }})" 
                                            class="text-red-600 hover:text-red-900">Reject</button>
                                @endif
                                @if($therapist->application_status === 'approved')
                                    <button wire:click="toggleActive({{ $therapist->id }})" 
                                            class="text-gray-600 hover:text-gray-900">
                                        {{ $therapist->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No therapists found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $therapists->links() }}
        </div>

        <!-- Therapist Details Modal -->
        @if($showDetails && $selectedTherapist)
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="$set('showDetails', false)">
                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white" wire:click.stop>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Therapist Details</h3>
                        <button wire:click="$set('showDetails', false)" class="text-gray-400 hover:text-gray-600">✕</button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $selectedTherapist->user->first_name }} {{ $selectedTherapist->user->last_name }}</h4>
                            <p class="text-sm text-gray-600">{{ $selectedTherapist->user->email }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600"><strong>Biography:</strong></p>
                            <p class="text-gray-900">{{ $selectedTherapist->biography ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600"><strong>Specializations:</strong></p>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @foreach($selectedTherapist->specializations ?? [] as $spec)
                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">{{ ucfirst($spec) }}</span>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600"><strong>Languages:</strong></p>
                            <p class="text-gray-900">{{ implode(', ', $selectedTherapist->languages_spoken ?? []) }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600"><strong>Base Rate:</strong></p>
                            <p class="text-gray-900">£{{ number_format($selectedTherapist->base_hourly_rate ?? 0, 2) }}/hour</p>
                        </div>

                        @if($selectedTherapist->application_status === 'pending')
                            <div class="flex gap-2 pt-4">
                                <button wire:click="approveTherapist({{ $selectedTherapist->id }})" 
                                        class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded">
                                    Approve Application
                                </button>
                                <button wire:click="rejectTherapist({{ $selectedTherapist->id }})" 
                                        class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded">
                                    Reject Application
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if(session('message'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
                 class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
                {{ session('message') }}
            </div>
        @endif
    </div>
</div>
