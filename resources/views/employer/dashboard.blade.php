@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Employer Dashboard</h1>
            <p class="mt-2 text-gray-600">Manage your crew and track document compliance</p>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-md flex items-center justify-between">
                <p class="text-green-800"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</p>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-md flex items-center justify-between">
                <p class="text-red-800"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</p>
                <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            {{-- Total Crew --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Crew</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_crew'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Active Crew --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <i class="fas fa-user-check text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Active</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_crew'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Pending --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_crew'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Expiring Soon --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-orange-100 rounded-md p-3">
                        <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Expiring Soon</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['expiring_soon'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Compliance Rate --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Compliance</p>
                        <p class="text-2xl font-semibold {{ $stats['compliance_rate'] >= 80 ? 'text-green-600' : ($stats['compliance_rate'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $stats['compliance_rate'] }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex justify-between items-center mb-6">
            <div class="flex gap-3">
                <a href="{{ route('employer.add-crew-page') }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Crew Member
                </a>
                <a href="{{ route('employer.compliance-report') }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                    <i class="fas fa-file-alt mr-2"></i>Compliance Report
                </a>
                <a href="{{ route('employer.export-compliance') }}" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>Export CSV
                </a>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" action="{{ route('employer.dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Name or email..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vessel</label>
                    <select name="vessel" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Vessels</option>
                        @foreach($vessels as $vessel)
                            <option value="{{ $vessel }}" {{ request('vessel') == $vessel ? 'selected' : '' }}>{{ $vessel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'vessel']))
                        <a href="{{ route('employer.dashboard') }}" 
                           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Crew List --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Crew Member</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vessel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contract</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documents</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($crew as $employerCrew)
                        @php
                            $crewMember = $employerCrew->crew;
                            $docs = $crewMember->documents;
                            $expired = $docs->filter(fn($d) => $d->expiry_date && $d->expiry_date->isPast())->count();
                            $expiringSoon = $docs->filter(fn($d) => $d->expiry_date && $d->expiry_date->isFuture() && $d->expiry_date->lte(now()->addDays(30)))->count();
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-semibold">{{ substr($crewMember->first_name, 0, 1) }}{{ substr($crewMember->last_name, 0, 1) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $crewMember->first_name }} {{ $crewMember->last_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $crewMember->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $employerCrew->position ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $employerCrew->vessel_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($employerCrew->contract_end_date)
                                    @if($employerCrew->isExpired())
                                        <span class="text-red-600 font-medium">Expired</span>
                                    @else
                                        {{ $employerCrew->days_remaining }} days left
                                    @endif
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-2 text-sm">
                                    <span class="text-gray-600">{{ $docs->count() }} total</span>
                                    @if($expired > 0)
                                        <span class="text-red-600 font-medium">{{ $expired }} expired</span>
                                    @endif
                                    @if($expiringSoon > 0)
                                        <span class="text-orange-600 font-medium">{{ $expiringSoon }} expiring</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $employerCrew->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $employerCrew->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $employerCrew->status === 'inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $employerCrew->status === 'terminated' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($employerCrew->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('employer.crew-details', $crewMember->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('employer.edit-crew-page', $employerCrew->id) }}" 
                                   class="text-green-600 hover:text-green-900 mr-3">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" 
                                      action="{{ route('employer.remove-crew', $employerCrew->id) }}" 
                                      class="inline"
                                      onsubmit="return confirm('Are you sure you want to remove {{ $crewMember->first_name }} {{ $crewMember->last_name }} from your crew?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-users text-4xl mb-4"></i>
                                <p class="text-lg">No crew members found</p>
                                <button onclick="document.getElementById('addCrewModal').classList.remove('hidden')" 
                                        class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Add Your First Crew Member
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($crew->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $crew->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

{{-- Add Crew Modal - REMOVED, now using separate page --}}
{{-- <div id="addCrewModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Add Crew Member</h3>
            <button onclick="document.getElementById('addCrewModal').classList.add('hidden')" 
                    class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('employer.add-crew') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Crew Member Email</label>
                <input type="email" name="crew_email" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter crew member's email">
                <p class="text-xs text-gray-500 mt-1">The crew member must be registered on the platform</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                    <input type="text" name="position" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., Captain, Engineer">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="pending">Pending</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vessel Name</label>
                    <input type="text" name="vessel_name" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., MS Ocean Star">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vessel IMO</label>
                    <input type="text" name="vessel_imo" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., IMO 1234567">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contract Start Date</label>
                    <input type="date" name="contract_start_date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contract End Date</label>
                    <input type="date" name="contract_end_date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                <textarea name="notes" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Add any additional notes..."></textarea>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Add Crew Member
                </button>
                <button type="button" 
                        onclick="document.getElementById('addCrewModal').classList.add('hidden')" 
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div> --}}

@endsection
