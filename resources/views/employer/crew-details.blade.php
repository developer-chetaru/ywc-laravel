@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('employer.dashboard') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>

        {{-- Crew Member Header --}}
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-20 w-20 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 font-bold text-2xl">{{ substr($crewMember->first_name, 0, 1) }}{{ substr($crewMember->last_name, 0, 1) }}</span>
                    </div>
                    <div class="ml-6">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $crewMember->first_name }} {{ $crewMember->last_name }}</h1>
                        <p class="text-gray-600 mt-1">{{ $crewMember->email }}</p>
                        <div class="flex gap-4 mt-2">
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-briefcase mr-1"></i>{{ $employerCrew->position ?? 'N/A' }}
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-ship mr-1"></i>{{ $employerCrew->vessel_name ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full 
                        {{ $employerCrew->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $employerCrew->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $employerCrew->status === 'inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                        {{ $employerCrew->status === 'terminated' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($employerCrew->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Left Column: Contract & Compliance --}}
            <div class="space-y-6">
                
                {{-- Contract Information --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Contract Details</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Start Date</p>
                            <p class="text-gray-900 font-medium">{{ $employerCrew->contract_start_date ? $employerCrew->contract_start_date->format('d M Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">End Date</p>
                            <p class="text-gray-900 font-medium">{{ $employerCrew->contract_end_date ? $employerCrew->contract_end_date->format('d M Y') : 'N/A' }}</p>
                        </div>
                        @if($employerCrew->contract_duration)
                        <div>
                            <p class="text-sm text-gray-500">Duration</p>
                            <p class="text-gray-900 font-medium">{{ $employerCrew->contract_duration }} days</p>
                        </div>
                        @endif
                        @if($employerCrew->days_remaining)
                        <div>
                            <p class="text-sm text-gray-500">Days Remaining</p>
                            <p class="font-medium {{ $employerCrew->days_remaining < 30 ? 'text-orange-600' : 'text-green-600' }}">
                                {{ $employerCrew->days_remaining }} days
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Compliance Status --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Compliance Status</h2>
                    
                    {{-- Compliance Score --}}
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Overall Score</span>
                            <span class="text-2xl font-bold {{ $compliance['status'] === 'compliant' ? 'text-green-600' : ($compliance['status'] === 'warning' ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ round($compliance['score']) }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full {{ $compliance['status'] === 'compliant' ? 'bg-green-600' : ($compliance['status'] === 'warning' ? 'bg-yellow-600' : 'bg-red-600') }}" 
                                 style="width: {{ $compliance['score'] }}%"></div>
                        </div>
                    </div>

                    {{-- Document Stats --}}
                    <div class="space-y-2">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Total Documents</span>
                            <span class="font-semibold text-gray-900">{{ $compliance['total'] }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Verified</span>
                            <span class="font-semibold text-green-600">{{ $compliance['verified'] }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Pending</span>
                            <span class="font-semibold text-yellow-600">{{ $compliance['pending'] }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Expired</span>
                            <span class="font-semibold text-red-600">{{ $compliance['expired'] }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm text-gray-600">Expiring Soon</span>
                            <span class="font-semibold text-orange-600">{{ $compliance['expiring_soon'] }}</span>
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <div class="mt-4 p-3 rounded-md {{ $compliance['status'] === 'compliant' ? 'bg-green-50' : ($compliance['status'] === 'warning' ? 'bg-yellow-50' : 'bg-red-50') }}">
                        <p class="text-sm font-medium {{ $compliance['status'] === 'compliant' ? 'text-green-800' : ($compliance['status'] === 'warning' ? 'text-yellow-800' : 'text-red-800') }}">
                            @if($compliance['status'] === 'compliant')
                                <i class="fas fa-check-circle mr-2"></i>Fully Compliant
                            @elseif($compliance['status'] === 'warning')
                                <i class="fas fa-exclamation-triangle mr-2"></i>Needs Attention
                            @else
                                <i class="fas fa-times-circle mr-2"></i>Non-Compliant
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Notes --}}
                @if($employerCrew->notes)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Notes</h2>
                    <p class="text-sm text-gray-600">{{ $employerCrew->notes }}</p>
                </div>
                @endif

            </div>

            {{-- Right Column: Documents --}}
            <div class="lg:col-span-2">
                
                {{-- Identity Documents --}}
                @if($documents['identity']->isNotEmpty())
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Identity Documents</h2>
                    <div class="space-y-3">
                        @foreach($documents['identity'] as $doc)
                            @include('employer.partials.document-card', ['document' => $doc])
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Certificates --}}
                @if($documents['certificates']->isNotEmpty())
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Certificates</h2>
                    <div class="space-y-3">
                        @foreach($documents['certificates'] as $doc)
                            @include('employer.partials.document-card', ['document' => $doc])
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Medical Documents --}}
                @if($documents['medical']->isNotEmpty())
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Medical Documents</h2>
                    <div class="space-y-3">
                        @foreach($documents['medical'] as $doc)
                            @include('employer.partials.document-card', ['document' => $doc])
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Other Documents --}}
                @if($documents['other']->isNotEmpty())
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Other Documents</h2>
                    <div class="space-y-3">
                        @foreach($documents['other'] as $doc)
                            @include('employer.partials.document-card', ['document' => $doc])
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- No Documents --}}
                @if($compliance['total'] === 0)
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Documents Available</h3>
                    <p class="text-gray-500">This crew member hasn't uploaded any documents yet.</p>
                </div>
                @endif

            </div>

        </div>

    </div>
</div>
@endsection
