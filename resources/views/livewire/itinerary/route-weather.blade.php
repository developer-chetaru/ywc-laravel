<div class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-800">Weather Forecast</h3>
        <button
            wire:click="syncWeather"
            wire:loading.attr="disabled"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <span wire:loading.remove wire:target="syncWeather">Refresh Weather</span>
            <span wire:loading wire:target="syncWeather">Syncing...</span>
        </button>
    </div>

    @if($message)
        <div class="p-3 text-sm rounded-lg {{ str_contains($message, 'Failed') ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' }}">
            {{ $message }}
        </div>
    @endif

    @if($route->stops->isEmpty())
        <p class="text-sm text-gray-500">Add stops to see weather forecasts.</p>
    @else
        <div class="space-y-6">
            @foreach($route->stops as $stop)
                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $stop->name }}</h4>
                            @if($stop->location_label)
                                <p class="text-sm text-gray-600">{{ $stop->location_label }}</p>
                            @endif
                            @if($stop->latitude && $stop->longitude)
                                <p class="text-xs text-gray-500 mt-1">{{ $stop->latitude }}, {{ $stop->longitude }}</p>
                            @endif
                        </div>
                    </div>

                    @if($stop->weatherSnapshots->isEmpty())
                        <p class="text-sm text-gray-500">No weather data available. Click "Refresh Weather" to fetch forecasts.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mt-4">
                            @foreach($stop->weatherSnapshots->sortBy('forecast_date') as $snapshot)
                                @php
                                    $payload = $snapshot->payload;
                                    $temp = $payload['temperature'] ?? [];
                                    $wind = $payload['wind'] ?? [];
                                    $conditions = $payload['conditions'] ?? [];
                                    $icon = $conditions['icon'] ?? null;
                                    $iconUrl = $icon ? "https://openweathermap.org/img/wn/{$icon}@2x.png" : null;
                                @endphp
                                <div class="bg-white border border-gray-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ \Carbon\Carbon::parse($snapshot->forecast_date)->format('M d, Y') }}
                                        </span>
                                        @if($iconUrl)
                                            <img src="{{ $iconUrl }}" alt="{{ $conditions['description'] ?? 'Weather' }}" class="w-10 h-10">
                                        @endif
                                    </div>
                                    
                                    <div class="space-y-1 text-sm text-gray-600">
                                        @if(!empty($conditions['description']))
                                            <p class="capitalize font-medium">{{ $conditions['description'] }}</p>
                                        @endif
                                        
                                        @if(!empty($temp['min']) && !empty($temp['max']))
                                            <p>🌡️ {{ round($temp['min']) }}°C - {{ round($temp['max']) }}°C</p>
                                        @endif
                                        
                                        @if(!empty($wind['speed']))
                                            <p>💨 Wind: {{ round($wind['speed'], 1) }} m/s
                                                @if(!empty($wind['deg']))
                                                    ({{ round($wind['deg']) }}°)
                                                @endif
                                            </p>
                                        @endif
                                        
                                        @if(isset($payload['precipitation_probability']))
                                            <p>🌧️ Precip: {{ round($payload['precipitation_probability'] * 100) }}%</p>
                                        @endif
                                        
                                        @if(!empty($wind['gust']))
                                            <p class="text-xs text-orange-600">⚠️ Gusts: {{ round($wind['gust'], 1) }} m/s</p>
                                        @endif
                                        
                                        @if(!empty($payload['sunrise']) && !empty($payload['sunset']))
                                            <p class="text-xs text-gray-500">
                                                ☀️ {{ $payload['sunrise'] }} / 🌙 {{ $payload['sunset'] }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

