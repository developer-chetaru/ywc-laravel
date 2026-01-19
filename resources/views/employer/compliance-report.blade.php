@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-6 flex justify-between items-center">
            <div>
                <a href="{{ route('employer.dashboard') }}" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Compliance Report</h1>
                <p class="text-gray-600 mt-1">Active crew compliance status</p>
            </div>
            <a href="{{ route('employer.export-compliance') }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                <i class="fas fa-download mr-2"></i>Export CSV
            </a>
        </div>

        {{-- Compliance Summary --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @php
                    $totalCrew = count($complianceData);
                    $compliant = collect($complianceData)->where('compliance.status', 'compliant')->count();
                    $warning = collect($complianceData)->where('compliance.status', 'warning')->count();
                    $nonCompliant = collect($complianceData)->where('compliance.status', 'non_compliant')->count();
                @endphp

                <div class="text-center">
                    <p class="text-3xl font-bold text-gray-900">{{ $totalCrew }}</p>
                    <p class="text-sm text-gray-600 mt-1">Total Active Crew</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $compliant }}</p>
                    <p class="text-sm text-gray-600 mt-1">Fully Compliant</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-yellow-600">{{ $warning }}</p>
                    <p class="text-sm text-gray-600 mt-1">Needs Attention</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-red-600">{{ $nonCompliant }}</p>
                    <p class="text-sm text-gray-600 mt-1">Non-Compliant</p>
                </div>
            </div>
        </div>

        {{-- Crew Compliance List --}}
        <div class="space-y-4">
            @foreach($complianceData as $data)
                @php
                    $crew = $data['crew'];
                    $employerCrew = $data['employer_crew'];
                    $compliance = $data['compliance'];
                @endphp

                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between">
                        
                        {{-- Crew Info --}}
                        <div class="flex items-start flex-1">
                            <div class="flex-shrink-0 h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-semibold">{{ substr($crew->first_name, 0, 1) }}{{ substr($crew->last_name, 0, 1) }}</span>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex items-center gap-3">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $crew->first_name }} {{ $crew->last_name }}</h3>
                                    <span class="px-3 py-1 text-xs rounded-full font-medium
                                        {{ $compliance['status'] === 'compliant' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $compliance['status'] === 'warning' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $compliance['status'] === 'non_compliant' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ round($compliance['score']) }}% Compliant
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $employerCrew->position ?? 'N/A' }} • {{ $employerCrew->vessel_name ?? 'N/A' }}
                                </p>

                                {{-- Compliance Progress Bar --}}
                                <div class="mt-3">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $compliance['status'] === 'compliant' ? 'bg-green-600' : ($compliance['status'] === 'warning' ? 'bg-yellow-600' : 'bg-red-600') }}" 
                                             style="width: {{ $compliance['score'] }}%"></div>
                                    </div>
                                </div>

                                {{-- Document Stats --}}
                                <div class="flex gap-6 mt-3 text-sm">
                                    <span class="text-gray-600">
                                        <i class="fas fa-file-alt mr-1"></i>{{ $compliance['total'] }} documents
                                    </span>
                                    @if($compliance['verified'] > 0)
                                    <span class="text-green-600">
                                        <i class="fas fa-check-circle mr-1"></i>{{ $compliance['verified'] }} verified
                                    </span>
                                    @endif
                                    @if($compliance['expired'] > 0)
                                    <span class="text-red-600 font-medium">
                                        <i class="fas fa-times-circle mr-1"></i>{{ $compliance['expired'] }} expired
                                    </span>
                                    @endif
                                    @if($compliance['expiring_soon'] > 0)
                                    <span class="text-orange-600 font-medium">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>{{ $compliance['expiring_soon'] }} expiring soon
                                    </span>
                                    @endif
                                </div>

                                {{-- Action Required --}}
                                @if($compliance['status'] !== 'compliant')
                                <div class="mt-3 p-3 {{ $compliance['status'] === 'warning' ? 'bg-yellow-50' : 'bg-red-50' }} rounded-md">
                                    <p class="text-sm font-medium {{ $compliance['status'] === 'warning' ? 'text-yellow-800' : 'text-red-800' }}">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        @if($compliance['expired'] > 0)
                                            Action Required: {{ $compliance['expired'] }} document(s) expired
                                        @elseif($compliance['expiring_soon'] > 0)
                                            Attention: {{ $compliance['expiring_soon'] }} document(s) expiring within 30 days
                                        @else
                                            Review required for compliance
                                        @endif
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="ml-6">
                            <a href="{{ route('employer.crew-details', $crew->id) }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm">
                                <i class="fas fa-eye mr-2"></i>View Details
                            </a>
                        </div>

                    </div>
                </div>
            @endforeach

            @if(empty($complianceData))
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Active Crew Members</h3>
                <p class="text-gray-500">Add crew members to see their compliance status.</p>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
