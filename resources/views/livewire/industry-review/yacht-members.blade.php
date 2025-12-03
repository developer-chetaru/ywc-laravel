<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-4 sm:p-6 border border-gray-200">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Members on {{ $yacht->name }}</h1>
                    <p class="text-sm text-gray-600">{{ count($members) }} {{ count($members) == 1 ? 'member' : 'members' }}</p>
                </div>
                <a href="{{ route('industryreview.yachts.manage') }}" 
                   class="inline-flex items-center px-3 sm:px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span class="hidden sm:inline">Back to Yachts</span>
                    <span class="sm:hidden">Back</span>
                </a>
            </div>

            <div class="space-y-3">
                @if(count($members) > 0)
                    @foreach($members as $member)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    @if($member['profile_photo_path'])
                                        <img src="{{ asset('storage/' . $member['profile_photo_path']) }}" 
                                             alt="{{ $member['first_name'] }} {{ $member['last_name'] }}"
                                             class="w-12 h-12 rounded-full object-cover">
                                    @else
                                        <span class="text-blue-600 font-semibold text-lg">
                                            {{ substr($member['first_name'], 0, 1) }}{{ substr($member['last_name'], 0, 1) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-gray-900">
                                        {{ $member['first_name'] }} {{ $member['last_name'] }}
                                    </div>
                                    <div class="text-sm text-gray-600 truncate">{{ $member['email'] }}</div>
                                    @if($member['current_yacht_start_date'])
                                        <div class="text-xs text-gray-500 mt-1">
                                            Started: {{ \Carbon\Carbon::parse($member['current_yacht_start_date'])->format('M Y') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @if(count($member['roles']) > 0)
                                        @foreach($member['roles'] as $role)
                                            <span class="px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                                {{ ucfirst(str_replace('_', ' ', $role)) }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                                            No Role
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="text-gray-600">No members found for this yacht.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

