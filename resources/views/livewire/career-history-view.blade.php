@php
    use Carbon\Carbon;
@endphp

<div class="py-4 sm:py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-4 sm:p-6 border border-gray-200">
            {{-- Header --}}
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Career History</h1>
                        @if($isSuperAdmin && $user)
                            <p class="text-sm text-gray-600 mt-1">
                                Viewing: <span class="font-semibold">{{ $user->first_name }} {{ $user->last_name }}</span> ({{ $user->email }})
                            </p>
                        @else
                            <p class="text-sm text-gray-600 mt-1">View your career history including yachts and experience</p>
                        @endif
                    </div>
                </div>
                
                {{-- Super Admin User Selector --}}
                @if($isSuperAdmin)
                    <div class="mt-4 bg-gray-50 rounded-lg p-3 sm:p-4 border border-gray-200">
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
                            <div class="flex-1 w-full">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Search Users</label>
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.300ms="search" 
                                    placeholder="Search by name or email..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                            </div>
                        </div>
                        
                        @if($users->count() > 0)
                            <div class="mt-4 max-h-60 overflow-y-auto border border-gray-200 rounded-lg bg-white">
                                <!-- Desktop Table -->
                                <div class="hidden md:block">
                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-50 sticky top-0">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Name</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Email</th>
                                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-700">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($users as $userItem)
                                                <tr class="hover:bg-gray-50 {{ $selectedUserId == $userItem->id ? 'bg-blue-50' : '' }}">
                                                    <td class="px-4 py-2">
                                                        <div class="flex items-center gap-2">
                                                            @if($userItem->profile_photo_path)
                                                                <img src="{{ asset('storage/'.$userItem->profile_photo_path) }}" 
                                                                     class="w-8 h-8 rounded-full object-cover" alt="">
                                                            @else
                                                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-medium text-xs">
                                                                    {{ strtoupper(substr($userItem->first_name, 0, 1) . substr($userItem->last_name, 0, 1)) }}
                                                                </div>
                                                            @endif
                                                            <span class="font-medium text-gray-900">{{ $userItem->first_name }} {{ $userItem->last_name }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-2 text-gray-600">{{ $userItem->email }}</td>
                                                    <td class="px-4 py-2 text-center">
                                                        <button 
                                                            wire:click="selectUser({{ $userItem->id }})"
                                                            class="px-3 py-1 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                                            View Career History
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Mobile Cards -->
                                <div class="md:hidden divide-y divide-gray-200">
                                    @foreach($users as $userItem)
                                        <div class="p-3 {{ $selectedUserId == $userItem->id ? 'bg-blue-50' : '' }}">
                                            <div class="flex items-center gap-3 mb-2">
                                                @if($userItem->profile_photo_path)
                                                    <img src="{{ asset('storage/'.$userItem->profile_photo_path) }}" 
                                                         class="w-10 h-10 rounded-full object-cover" alt="">
                                                @else
                                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-medium text-sm">
                                                        {{ strtoupper(substr($userItem->first_name, 0, 1) . substr($userItem->last_name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div class="flex-1">
                                                    <div class="font-medium text-gray-900">{{ $userItem->first_name }} {{ $userItem->last_name }}</div>
                                                    <div class="text-xs text-gray-600 truncate">{{ $userItem->email }}</div>
                                                </div>
                                            </div>
                                            <button 
                                                wire:click="selectUser({{ $userItem->id }})"
                                                class="w-full mt-2 px-3 py-2 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                                View Career History
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mt-2">
                                {{ $users->links() }}
                            </div>
                        @elseif($search)
                            <p class="mt-4 text-sm text-gray-500 text-center">No users found matching your search.</p>
                        @else
                            <p class="mt-4 text-sm text-gray-500 text-center">Start typing to search for users...</p>
                        @endif
                    </div>
                @endif
            </div>

            @if($user && (!$isSuperAdmin || $selectedUserId))
            <div class="space-y-6">
                {{-- Years of Experience --}}
                <div class="bg-gray-50 rounded-lg p-4 sm:p-6 border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Years of Experience
                    </h2>
                    <div class="text-3xl font-bold text-blue-600">
                        {{ $years_experience ?? 'Not specified' }}
                        @if($years_experience)
                            <span class="text-lg text-gray-600 font-normal">years</span>
                        @endif
                    </div>
                </div>

                {{-- Current Yacht --}}
                <div class="bg-gray-50 rounded-lg p-4 sm:p-6 border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Current Yacht
                    </h2>
                    @if($current_yacht)
                        <div class="space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="flex-1">
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">{{ $current_yacht }}</h3>
                                    @if($current_yacht_start_date)
                                        <p class="text-sm text-gray-600 mt-1">
                                            Started: {{ Carbon::parse($current_yacht_start_date)->format('M Y') }}
                                        </p>
                                    @endif
                                </div>
                                @php
                                    $yachtModel = $yachts->firstWhere('name', $current_yacht);
                                @endphp
                                @if($yachtModel)
                                    <div class="text-left sm:text-right">
                                        <span class="inline-block px-3 py-1 text-xs font-medium rounded-full {{ $yachtModel->status === 'charter' ? 'bg-green-100 text-green-800' : ($yachtModel->status === 'private' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                            {{ ucfirst($yachtModel->status) }}
                                        </span>
                                        @if($yachtModel->length_meters)
                                            <p class="text-sm text-gray-600 mt-1">{{ number_format($yachtModel->length_meters, 1) }}m</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 italic">No current yacht specified</p>
                    @endif
                </div>

                {{-- Previous Yachts --}}
                <div class="bg-gray-50 rounded-lg p-4 sm:p-6 border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Previous Yachts
                    </h2>
                    @if(count($previous_yachts) > 0)
                        <div class="space-y-3">
                            @foreach($previous_yachts as $index => $yacht)
                                @php
                                    $yachtName = is_array($yacht) ? ($yacht['name'] ?? '') : $yacht;
                                    $startDate = is_array($yacht) && !empty($yacht['start_date']) ? Carbon::parse($yacht['start_date']) : null;
                                    $endDate = is_array($yacht) && !empty($yacht['end_date']) ? Carbon::parse($yacht['end_date']) : null;
                                    $isInvalid = $startDate && $endDate && $endDate->lt($startDate);
                                    $yachtModel = null;
                                    if (is_array($yacht) && !empty($yacht['yacht_id'])) {
                                        $yachtModel = $yachts->get($yacht['yacht_id']);
                                    } elseif ($yachtName) {
                                        $yachtModel = $yachts->firstWhere('name', $yachtName);
                                    }
                                @endphp
                                <div class="bg-white border {{ $isInvalid ? 'border-red-300' : 'border-gray-200' }} rounded-lg p-3 sm:p-4 hover:shadow-md transition-shadow">
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900 text-lg">{{ $yachtName ?: 'Unknown Yacht' }}</h3>
                                            @if($yachtModel)
                                                <div class="mt-2 flex items-center gap-3 text-sm text-gray-600">
                                                    <span class="inline-block px-2 py-1 text-xs font-medium rounded-full {{ $yachtModel->status === 'charter' ? 'bg-green-100 text-green-800' : ($yachtModel->status === 'private' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                                        {{ ucfirst($yachtModel->status) }}
                                                    </span>
                                                    @if($yachtModel->type)
                                                        <span class="text-gray-500">{{ ucfirst(str_replace('_', ' ', $yachtModel->type)) }}</span>
                                                    @endif
                                                    @if($yachtModel->length_meters)
                                                        <span class="text-gray-500">{{ number_format($yachtModel->length_meters, 1) }}m</span>
                                                    @endif
                                                </div>
                                            @endif
                                            @if($startDate || $endDate)
                                                <div class="mt-3 text-sm {{ $isInvalid ? 'text-red-600' : 'text-gray-600' }}">
                                                    @if($startDate)
                                                        <span class="font-medium">Start:</span> {{ $startDate->format('M Y') }}
                                                    @endif
                                                    @if($startDate && $endDate)
                                                        <span class="mx-2">-</span>
                                                    @endif
                                                    @if($endDate)
                                                        <span class="font-medium">End:</span> {{ $endDate->format('M Y') }}
                                                    @endif
                                                    @if($startDate && $endDate)
                                                        @php
                                                            $duration = $startDate->diffInMonths($endDate);
                                                        @endphp
                                                        <span class="ml-2 text-gray-500">({{ $duration }} months)</span>
                                                    @endif
                                                    @if($isInvalid)
                                                        <span class="ml-2 text-red-600 font-semibold">(Invalid dates)</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">No previous yachts recorded</p>
                    @endif
                </div>
            </div>
            @elseif($isSuperAdmin && !$selectedUserId)
                <div class="text-center py-12 bg-gray-50 rounded-lg border border-gray-200">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-gray-600 text-lg font-medium">Select a user above to view their career history</p>
                </div>
            @endif
        </div>
    </div>
</div>

