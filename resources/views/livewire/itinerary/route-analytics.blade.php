<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-800">Analytics Dashboard</h3>
        <select wire:model.live="period" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="7">Last 7 days</option>
            <option value="30">Last 30 days</option>
            <option value="90">Last 90 days</option>
            <option value="365">Last year</option>
        </select>
    </div>

    {{-- Key Metrics --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 font-medium">Total Views</p>
                    <p class="text-2xl font-bold text-blue-800 mt-1">{{ number_format($totalViews) }}</p>
                </div>
                <div class="text-blue-400 text-3xl">👁️</div>
            </div>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 font-medium">Copies</p>
                    <p class="text-2xl font-bold text-green-800 mt-1">{{ number_format($totalCopies) }}</p>
                </div>
                <div class="text-green-400 text-3xl">📋</div>
            </div>
        </div>

        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-purple-600 font-medium">Reviews</p>
                    <p class="text-2xl font-bold text-purple-800 mt-1">{{ number_format($totalReviews) }}</p>
                </div>
                <div class="text-purple-400 text-3xl">⭐</div>
            </div>
        </div>

        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-orange-600 font-medium">Avg Rating</p>
                    <p class="text-2xl font-bold text-orange-800 mt-1">
                        {{ $averageRating > 0 ? number_format($averageRating, 1) : '—' }}
                    </p>
                </div>
                <div class="text-orange-400 text-3xl">⭐</div>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Daily Views Chart --}}
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <h4 class="font-semibold text-gray-800 mb-4">Daily Views</h4>
            @if(empty($dailyViews))
                <p class="text-sm text-gray-500 text-center py-8">No view data available for this period.</p>
            @else
                <div class="h-64 flex items-end justify-between gap-1">
                    @php
                        $maxViews = max($dailyViews) ?: 1;
                    @endphp
                    @foreach($dailyViews as $date => $views)
                        <div class="flex-1 flex flex-col items-center">
                            <div 
                                class="w-full bg-blue-500 rounded-t hover:bg-blue-600 transition-colors cursor-pointer"
                                style="height: {{ ($views / $maxViews) * 100 }}%"
                                title="{{ \Carbon\Carbon::parse($date)->format('M d') }}: {{ $views }} views"
                            ></div>
                            <span class="text-xs text-gray-500 mt-1 transform -rotate-45 origin-top-left whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($date)->format('M d') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Region Distribution --}}
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <h4 class="font-semibold text-gray-800 mb-4">Views by Region</h4>
            @if(empty($regionStats))
                <p class="text-sm text-gray-500 text-center py-8">No regional data available.</p>
            @else
                <div class="space-y-3">
                    @php
                        $maxRegionViews = max($regionStats) ?: 1;
                    @endphp
                    @foreach($regionStats as $region => $views)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ $region ?: 'Unknown' }}</span>
                                <span class="text-sm text-gray-600">{{ number_format($views) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div 
                                    class="bg-green-500 h-2 rounded-full transition-all"
                                    style="width: {{ ($views / $maxRegionViews) * 100 }}%"
                                ></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Reviews Over Time --}}
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <h4 class="font-semibold text-gray-800 mb-4">Reviews Over Time</h4>
        @if(empty($reviewsByMonth))
            <p class="text-sm text-gray-500 text-center py-8">No reviews yet.</p>
        @else
            <div class="h-48 flex items-end justify-between gap-2">
                @php
                    $maxReviews = max($reviewsByMonth) ?: 1;
                @endphp
                @foreach($reviewsByMonth as $month => $count)
                    <div class="flex-1 flex flex-col items-center">
                        <div 
                            class="w-full bg-purple-500 rounded-t hover:bg-purple-600 transition-colors cursor-pointer"
                            style="height: {{ ($count / $maxReviews) * 100 }}%"
                            title="{{ $month }}: {{ $count }} reviews"
                        ></div>
                        <span class="text-xs text-gray-500 mt-1 transform -rotate-45 origin-top-left whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($month . '-01')->format('M Y') }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

