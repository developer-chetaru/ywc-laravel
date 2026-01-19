@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('employer.dashboard') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>

        {{-- Page Header --}}
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Crew Member</h1>
            <p class="text-gray-600 mb-2">Update crew member details</p>
            <p class="text-sm text-gray-500 mb-8">
                <strong>{{ $employerCrew->crew->first_name }} {{ $employerCrew->crew->last_name }}</strong> 
                ({{ $employerCrew->crew->email }})
            </p>

            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-md">
                    <p class="text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-md">
                    <p class="text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Edit Crew Form --}}
            <form method="POST" action="{{ route('employer.update-crew', $employerCrew->id) }}" class="space-y-6">
                @csrf

                {{-- Position and Status Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                        <input type="text" 
                               name="position" 
                               value="{{ old('position', $employerCrew->position) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="e.g., Captain, Engineer">
                        @error('position')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-600">*</span>
                        </label>
                        <select name="status" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="pending" {{ old('status', $employerCrew->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="active" {{ old('status', $employerCrew->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $employerCrew->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="terminated" {{ old('status', $employerCrew->status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                        </select>
                        @error('status')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Vessel Information Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vessel Name</label>
                        <input type="text" 
                               name="vessel_name" 
                               value="{{ old('vessel_name', $employerCrew->vessel_name) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="e.g., MS Ocean Star">
                        @error('vessel_name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vessel IMO</label>
                        <input type="text" 
                               name="vessel_imo" 
                               value="{{ old('vessel_imo', $employerCrew->vessel_imo) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="e.g., IMO 1234567">
                        @error('vessel_imo')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Contract Dates Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contract Start Date</label>
                        <input type="date" 
                               name="contract_start_date" 
                               value="{{ old('contract_start_date', $employerCrew->contract_start_date ? $employerCrew->contract_start_date->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('contract_start_date')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contract End Date</label>
                        <input type="date" 
                               name="contract_end_date" 
                               value="{{ old('contract_end_date', $employerCrew->contract_end_date ? $employerCrew->contract_end_date->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('contract_end_date')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                    <textarea name="notes" 
                              rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Add any additional notes...">{{ old('notes', $employerCrew->notes) }}</textarea>
                    @error('notes')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit Buttons --}}
                <div class="flex gap-4 pt-4">
                    <button type="submit" 
                            class="flex-1 px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Update Crew Member
                    </button>
                    <a href="{{ route('employer.dashboard') }}" 
                       class="px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors text-center">
                        Cancel
                    </a>
                </div>
            </form>

            {{-- Danger Zone: Remove Crew --}}
            <div class="mt-8 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-red-600 mb-3">Danger Zone</h3>
                <p class="text-sm text-gray-600 mb-4">Remove this crew member from your team. This action cannot be undone.</p>
                <form method="POST" action="{{ route('employer.remove-crew', $employerCrew->id) }}" 
                      onsubmit="return confirm('Are you sure you want to remove this crew member?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>Remove Crew Member
                    </button>
                </form>
            </div>

        </div>

    </div>
</div>
@endsection
