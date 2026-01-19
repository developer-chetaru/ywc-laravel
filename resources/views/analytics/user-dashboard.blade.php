@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <h1 class="text-3xl font-bold text-gray-900 mb-8">My Analytics Dashboard</h1>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Documents</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $documentStats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Approved</p>
                        <p class="text-2xl font-semibold text-green-600">{{ $documentStats['approved'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending</p>
                        <p class="text-2xl font-semibold text-yellow-600">{{ $documentStats['pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Expired</p>
                        <p class="text-2xl font-semibold text-red-600">{{ $documentStats['expired'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Profile Completion --}}
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Profile Completion</h2>
            <div class="flex items-center">
                <div class="flex-1">
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="bg-blue-600 h-4 rounded-full" style="width: {{ $profileScore['score'] }}%"></div>
                    </div>
                </div>
                <span class="ml-4 text-2xl font-bold text-blue-600">{{ $profileScore['score'] }}%</span>
            </div>
            @if(!empty($profileScore['missing_fields']))
                <div class="mt-4">
                    <p class="text-sm text-gray-600 mb-2">Missing fields:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($profileScore['missing_fields'] as $field)
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">{{ $field }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            {{-- Documents by Type --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Documents by Type</h2>
                @if($documentsByType->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($documentsByType as $typeData)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="text-gray-700 capitalize">{{ $typeData->type }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-gray-900 font-semibold mr-2">{{ $typeData->count }}</span>
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        @php
                                            $percentage = ($typeData->count / $documentStats['total']) * 100;
                                        @endphp
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No documents yet</p>
                @endif
            </div>

            {{-- Verification Status --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Verification Status</h2>
                @if($verificationStats->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($verificationStats as $verif)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-700">{{ $verif->name }}</span>
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                    {{ $verif->count }} verified
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No verifications yet</p>
                @endif
            </div>

        </div>

        {{-- Document Timeline --}}
        <div class="bg-white rounded-lg shadow p-6 mt-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Document Upload Timeline (Last 12 Months)</h2>
            @if($documentTimeline->isNotEmpty())
                <div class="space-y-2">
                    @foreach($documentTimeline as $timeline)
                        <div class="flex items-center">
                            <span class="text-gray-600 text-sm w-24">{{ \Carbon\Carbon::parse($timeline->month . '-01')->format('M Y') }}</span>
                            <div class="flex-1 mx-4">
                                <div class="bg-gray-200 rounded-full h-6 relative">
                                    @php
                                        $maxCount = $documentTimeline->max('count');
                                        $percentage = $maxCount > 0 ? ($timeline->count / $maxCount) * 100 : 0;
                                    @endphp
                                    <div class="bg-blue-600 h-6 rounded-full flex items-center justify-end pr-2" 
                                         style="width: {{ $percentage }}%">
                                        <span class="text-white text-xs font-semibold">{{ $timeline->count }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No document history available</p>
            @endif
        </div>

    </div>
</div>
@endsection
