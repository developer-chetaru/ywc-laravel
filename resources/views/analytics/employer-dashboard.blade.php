@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Employer Analytics</h1>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-500">Total Crew</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $crewStats['total'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-500">Active Crew</p>
                <p class="text-3xl font-bold text-green-600 mt-2">{{ $crewStats['active'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-500">Compliance Rate</p>
                <p class="text-3xl font-bold text-blue-600 mt-2">{{ $complianceMetrics['compliance_rate'] }}%</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-500">Expiring Soon</p>
                <p class="text-3xl font-bold text-orange-600 mt-2">{{ $complianceMetrics['expiring_soon'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            {{-- Crew Growth Timeline --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Crew Growth (12 Months)</h2>
                @if($crewGrowth->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($crewGrowth as $growth)
                            <div class="flex items-center">
                                <span class="text-sm text-gray-600 w-24">{{ \Carbon\Carbon::parse($growth->month . '-01')->format('M Y') }}</span>
                                <div class="flex-1 mx-3">
                                    <div class="bg-gray-200 rounded-full h-6">
                                        @php
                                            $maxCount = $crewGrowth->max('count');
                                            $percentage = $maxCount > 0 ? ($growth->count / $maxCount) * 100 : 0;
                                        @endphp
                                        <div class="bg-green-600 h-6 rounded-full flex items-center justify-end pr-2" 
                                             style="width: {{ $percentage }}%">
                                            <span class="text-white text-xs font-semibold">{{ $growth->count }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No crew history</p>
                @endif
            </div>

            {{-- Top Vessels --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Top Vessels by Crew</h2>
                @if($topVessels->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($topVessels as $vessel)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-700">{{ $vessel->vessel_name }}</span>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                    {{ $vessel->crew_count }} crew
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No vessel data</p>
                @endif
            </div>

        </div>

        {{-- Position Distribution --}}
        <div class="bg-white rounded-lg shadow p-6 mt-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Position Distribution</h2>
            @if($positionDistribution->isNotEmpty())
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($positionDistribution as $position)
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-blue-600">{{ $position->count }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $position->position }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No position data</p>
            @endif
        </div>

        {{-- Expiring Contracts --}}
        @if($expiringContracts->isNotEmpty())
        <div class="bg-white rounded-lg shadow p-6 mt-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-exclamation-triangle text-orange-600 mr-2"></i>
                Contracts Expiring Soon (Next 30 Days)
            </h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Crew Member</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vessel</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days Left</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($expiringContracts as $contract)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $contract->crew->first_name }} {{ $contract->crew->last_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $contract->position }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $contract->vessel_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $contract->contract_end_date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $contract->days_remaining < 15 ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800' }}">
                                        {{ $contract->days_remaining }} days
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
