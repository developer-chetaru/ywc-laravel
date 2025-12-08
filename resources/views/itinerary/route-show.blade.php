@php
    /** @var \App\Models\ItineraryRoute $route */
    use Illuminate\Support\Facades\Storage;
    
    // Collect all images: cover image + all stop photos
    $allImages = [];
    if ($route->cover_image) {
        $allImages[] = [
            'url' => Storage::url($route->cover_image),
            'type' => 'cover',
            'label' => 'Cover Image'
        ];
    }
    
    foreach ($route->stops as $stop) {
        // Ensure photos is an array
        $stopPhotos = $stop->photos;
        if (is_string($stopPhotos)) {
            $stopPhotos = json_decode($stopPhotos, true) ?? [];
        }
        if (!is_array($stopPhotos)) {
            $stopPhotos = [];
        }
        // Filter out empty/null values
        $stopPhotos = array_filter($stopPhotos, function($photo) {
            return !empty($photo) && is_string($photo);
        });
        
        // Add each photo to the gallery
        foreach ($stopPhotos as $photo) {
            $allImages[] = [
                'url' => Storage::url($photo),
                'type' => 'stop',
                'label' => $stop->name . ' - Photo'
            ];
        }
    }
    
    // Prepare route stops data for JavaScript
    $routeStopsData = $route->stops->map(function($stop) {
        return [
            'name' => $stop->name,
            'latitude' => $stop->latitude,
            'longitude' => $stop->longitude,
            'location_label' => $stop->location_label,
            'stay_duration_hours' => $stop->stay_duration_hours,
            'notes' => $stop->notes,
            'sequence' => $stop->sequence
        ];
    })->values();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 sm:gap-4 flex-1 min-w-0">
                <a href="{{ route('itinerary.routes.index') }}" 
                   class="inline-flex items-center px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors flex-shrink-0">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span class="hidden sm:inline">Back to Routes</span>
                    <span class="sm:hidden">Back</span>
                </a>
                <h2 class="font-semibold text-base sm:text-xl text-gray-800 leading-tight truncate">
                    {{ $route->title }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-10">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">
            {{-- Image Gallery Section --}}
            @if(count($allImages) > 0)
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" 
                     x-data="imageGallery">
                    {{-- Main Image Display --}}
                    <div class="relative bg-gray-900" style="min-height: 250px;">
                        <img :src="allImages[selectedImage].url" 
                             :alt="allImages[selectedImage].label"
                             class="w-full"
                             :class="{
                                 'h-64 sm:h-96 object-cover': displayMode === 'cover',
                                 'h-64 sm:h-96 object-contain bg-gray-800': displayMode === 'contain',
                                 'h-auto max-h-screen object-contain bg-gray-800': displayMode === 'full'
                             }"
                             x-ref="mainImage">
                        
                        {{-- Navigation Arrows --}}
                        @if(count($allImages) > 1)
                            <button @click="selectedImage = (selectedImage - 1 + allImages.length) % allImages.length"
                                    class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white p-2 sm:p-3 rounded-full transition-all">
                                <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <button @click="selectedImage = (selectedImage + 1) % allImages.length"
                                    class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white p-2 sm:p-3 rounded-full transition-all">
                                <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        @endif
                        
                        {{-- Image Counter --}}
                        @if(count($allImages) > 1)
                            <div class="absolute bottom-2 sm:bottom-4 left-1/2 -translate-x-1/2 bg-black bg-opacity-50 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm">
                                <span x-text="selectedImage + 1"></span> / <span x-text="allImages.length"></span>
                            </div>
                        @endif
                        
                        {{-- Display Mode Controls --}}
                        <div class="absolute top-2 right-2 sm:top-4 sm:right-4 bg-black bg-opacity-50 rounded-lg p-1.5 sm:p-2">
                            <div class="flex gap-0.5 sm:gap-1">
                                <button @click="displayMode = 'cover'" 
                                        :class="displayMode === 'cover' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'"
                                        class="px-1.5 sm:px-2 py-0.5 sm:py-1 text-[10px] sm:text-xs rounded hover:bg-indigo-500" 
                                        title="Cover (crop to fit)">
                                    <span class="hidden sm:inline">Cover</span>
                                    <span class="sm:hidden">C</span>
                                </button>
                                <button @click="displayMode = 'contain'" 
                                        :class="displayMode === 'contain' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'"
                                        class="px-1.5 sm:px-2 py-0.5 sm:py-1 text-[10px] sm:text-xs rounded hover:bg-indigo-500"
                                        title="Contain (show full image)">
                                    <span class="hidden sm:inline">Contain</span>
                                    <span class="sm:hidden">Fit</span>
                                </button>
                                <button @click="displayMode = 'full'" 
                                        :class="displayMode === 'full' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'"
                                        class="px-1.5 sm:px-2 py-0.5 sm:py-1 text-[10px] sm:text-xs rounded hover:bg-indigo-500"
                                        title="Full size">
                                    <span class="hidden sm:inline">Full</span>
                                    <span class="sm:hidden">F</span>
                                </button>
                            </div>
                        </div>
                        
                        {{-- Fullscreen Button --}}
                        <a :href="allImages[selectedImage].url" target="_blank"
                           class="absolute top-2 left-2 sm:top-4 sm:left-4 bg-black bg-opacity-50 hover:bg-opacity-75 text-white p-1.5 sm:p-2 rounded-full transition-all"
                           title="View full size">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                            </svg>
                        </a>
                    </div>
                    
                    {{-- Thumbnail Gallery --}}
                    @if(count($allImages) > 1)
                        <div class="p-2 sm:p-4 bg-gray-50 border-t border-gray-200">
                            <div class="flex gap-1.5 sm:gap-2 overflow-x-auto pb-2">
                                <template x-for="(image, index) in allImages" :key="index">
                                    <button @click="selectedImage = index"
                                            :class="selectedImage === index ? 'ring-2 ring-indigo-500 ring-offset-2' : 'opacity-60 hover:opacity-100'"
                                            class="flex-shrink-0 w-16 h-16 sm:w-20 sm:h-20 rounded-md overflow-hidden border-2 transition-all"
                                            :class="selectedImage === index ? 'border-indigo-500' : 'border-gray-300'">
                                        <img :src="image.url" :alt="image.label" class="w-full h-full object-cover">
                                    </button>
                                </template>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
            
            {{-- Route Information Card --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4 sm:p-6 space-y-4 sm:space-y-6">
                {{-- Header with Actions --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-200 pb-3 sm:pb-4">
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800">Route Overview</h3>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 sm:px-3 py-1 text-[10px] sm:text-xs font-semibold rounded-full capitalize"
                              :class="{
                                  'bg-yellow-100 text-yellow-800': '{{ $route->status }}' === 'draft',
                                  'bg-green-100 text-green-800': '{{ $route->status }}' === 'active',
                                  'bg-blue-100 text-blue-800': '{{ $route->status }}' === 'completed',
                                  'bg-gray-100 text-gray-800': '{{ $route->status }}' === 'archived'
                              }">
                            {{ ucfirst($route->status) }}
                        </span>
                        <span class="px-2.5 sm:px-3 py-1 text-[10px] sm:text-xs font-semibold rounded-full capitalize"
                              :class="{
                                  'bg-purple-100 text-purple-800': '{{ $route->visibility }}' === 'private',
                                  'bg-indigo-100 text-indigo-800': '{{ $route->visibility }}' === 'crew',
                                  'bg-green-100 text-green-800': '{{ $route->visibility }}' === 'public'
                              }">
                            {{ ucfirst($route->visibility) }}
                        </span>
                    </div>
                </div>
                
                @if($route->description)
                    <div class="border-t border-gray-200 pt-3 sm:pt-4">
                        <h4 class="text-xs sm:text-sm font-semibold text-gray-700 mb-2">Description</h4>
                        <p class="text-xs sm:text-sm text-gray-600 whitespace-pre-wrap">{{ $route->description }}</p>
                    </div>
                @endif
                
                <div>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4 text-xs sm:text-sm">
                        <div>
                            <dt class="font-medium text-gray-700">Region</dt>
                            <dd>{{ $route->region ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-700">Difficulty</dt>
                            <dd>{{ $route->difficulty ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-700">Season</dt>
                            <dd>{{ $route->season ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-700">Duration</dt>
                            <dd>{{ $route->duration_days }} days</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-700">Distance</dt>
                            <dd>{{ number_format($route->distance_nm, 2) }} NM</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-700">Visibility</dt>
                            <dd class="capitalize">{{ $route->visibility }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Stops Section --}}
                <div>
                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-800">Route Stops</h3>
                        <span class="text-xs sm:text-sm text-gray-500">{{ count($route->stops) }} stop(s)</span>
                    </div>
                    <div class="space-y-3 sm:space-y-4">
                        @foreach($route->stops as $stop)
                            <div class="border border-gray-200 rounded-lg p-3 sm:p-4 hover:shadow-md transition-shadow bg-white">
                                <div class="flex items-start justify-between mb-2 sm:mb-3">
                                    <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0">
                                        <div class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center font-semibold text-xs sm:text-sm">
                                            {{ $stop->sequence }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h4 class="font-semibold text-sm sm:text-base text-gray-800 truncate">{{ $stop->name }}</h4>
                                            @if($stop->location_label)
                                                <p class="text-xs sm:text-sm text-gray-500 truncate">{{ $stop->location_label }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @if($stop->stay_duration_hours)
                                        <span class="px-2 sm:px-3 py-1 bg-blue-50 text-blue-700 text-[10px] sm:text-xs font-medium rounded-full flex-shrink-0 ml-2">
                                            {{ $stop->stay_duration_hours }} hrs
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mt-2 sm:mt-3 text-xs sm:text-sm">
                                    @if($stop->latitude && $stop->longitude)
                                        <div>
                                            <span class="font-medium text-gray-700">Coordinates:</span>
                                            <span class="text-gray-600 ml-2">{{ $stop->latitude }}, {{ $stop->longitude }}</span>
                                        </div>
                                    @endif
                                    @if($stop->notes)
                                        <div>
                                            <span class="font-medium text-gray-700">Notes:</span>
                                            <p class="text-gray-600 mt-1">{{ $stop->notes }}</p>
                                        </div>
                                    @endif
                                </div>
                                
                                @php
                                    // Ensure photos is an array
                                    $stopPhotos = $stop->photos;
                                    if (is_string($stopPhotos)) {
                                        $stopPhotos = json_decode($stopPhotos, true) ?? [];
                                    }
                                    if (!is_array($stopPhotos)) {
                                        $stopPhotos = [];
                                    }
                                    // Filter out empty/null values
                                    $stopPhotos = array_filter($stopPhotos, function($photo) {
                                        return !empty($photo) && is_string($photo);
                                    });
                                @endphp
                                @if(count($stopPhotos) > 0)
                                    <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-200">
                                        <p class="text-[10px] sm:text-xs font-medium text-gray-600 mb-2">Photos ({{ count($stopPhotos) }}):</p>
                                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-1.5 sm:gap-2">
                                            @foreach($stopPhotos as $photo)
                                                <a href="{{ Storage::url($photo) }}" target="_blank" class="block group">
                                                    <img src="{{ Storage::url($photo) }}" 
                                                         alt="{{ $stop->name }} - Photo {{ $loop->iteration }}" 
                                                         class="w-full h-16 sm:h-20 object-cover rounded-md border-2 border-gray-200 group-hover:border-indigo-400 group-hover:shadow-lg transition-all">
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <p class="text-xs text-gray-400 italic">No photos uploaded for this stop</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Legs Section --}}
                <div>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2 sm:mb-3">Route Legs</h3>
                    @if($route->legs->isEmpty())
                        <div class="border border-dashed border-gray-300 rounded-lg p-4 sm:p-6 text-center text-gray-500">
                            <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                            <p class="text-xs sm:text-sm">Leg metrics will appear after coordinates are provided for stops.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            @foreach($route->legs as $leg)
                                <div class="border border-gray-200 rounded-lg p-3 sm:p-4 bg-gradient-to-br from-indigo-50 to-blue-50 hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[10px] sm:text-xs font-semibold text-indigo-700 bg-indigo-100 px-2 py-1 rounded">Leg {{ $leg->sequence }}</span>
                                        <span class="text-xs sm:text-sm font-bold text-gray-800">{{ number_format($leg->distance_nm, 2) }} NM</span>
                                    </div>
                                    <div class="text-xs sm:text-sm text-gray-700 space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-500">From:</span>
                                            <span class="font-medium">{{ optional($leg->from)->name ?? '—' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-500">To:</span>
                                            <span class="font-medium">{{ optional($leg->to)->name ?? '—' }}</span>
                                        </div>
                                        @if($leg->estimated_hours)
                                            <div class="flex items-center gap-2 mt-2 pt-2 border-t border-gray-200">
                                                <span class="text-gray-500">ETA:</span>
                                                <span class="font-medium text-indigo-600">{{ $leg->estimated_hours }} hrs</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Marine Map Section --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-4 py-3 border-b border-indigo-800">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                            Route Map with Marine Details
                        </h3>
                    </div>
                    <div class="relative" style="height: 600px;" id="route-view-map">
                        <!-- Marine Map Controls - Always visible -->
                        <div class="absolute top-4 right-4 z-40 bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden" style="max-width: 280px;">
                            <!-- Route Information -->
                            <div class="p-3 border-b border-gray-200">
                                <h4 class="text-xs font-semibold text-gray-700 mb-2">Route Information</h4>
                                <div class="space-y-1 text-xs text-gray-600">
                                    <p><span class="font-medium">Stops:</span> <span id="view-legend-stops">{{ count($route->stops) }}</span></p>
                                    <p><span class="font-medium">Distance:</span> <span id="view-legend-distance">{{ number_format($route->distance_nm, 2) }} NM</span></p>
                                </div>
                            </div>
                            <!-- Marine Features Toggle -->
                            <div class="p-3 space-y-2">
                                <h4 class="text-xs font-semibold text-gray-700 mb-2">Marine Features</h4>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" id="view-toggle-depth-contours" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-xs text-gray-600">Depth Contours</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" id="view-toggle-navigation-aids" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-xs text-gray-600">Navigation Aids</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" id="view-toggle-sea-routes" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-xs text-gray-600">Sea Routes</span>
                                </label>
                            </div>
                            <!-- Marine Legend -->
                            <div class="p-3 bg-gray-50 border-t border-gray-200">
                                <h4 class="text-xs font-semibold text-gray-700 mb-2">Legend</h4>
                                <div class="space-y-1.5 text-xs">
                                    <div class="font-semibold text-gray-700 mb-1 text-[10px] uppercase tracking-wide">Route Markers</div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 rounded-full bg-green-500 border-2 border-white shadow-sm"></div>
                                        <span class="text-gray-600">Start</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 rounded-full bg-indigo-500 border-2 border-white shadow-sm"></div>
                                        <span class="text-gray-600">Stop</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 rounded-full bg-red-500 border-2 border-white shadow-sm"></div>
                                        <span class="text-gray-600">End</span>
                                    </div>
                                    
                                    <div class="font-semibold text-gray-700 mb-1 mt-2 pt-2 border-t border-gray-300 text-[10px] uppercase tracking-wide">Depth Zones</div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-4 h-2 bg-blue-300 rounded border border-blue-400"></div>
                                        <span class="text-gray-600">Shallow (&lt;10m)</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-4 h-2 bg-blue-500 rounded border border-blue-600"></div>
                                        <span class="text-gray-600">Deep (&gt;10m)</span>
                                    </div>
                                    
                                    <div class="font-semibold text-gray-700 mb-1 mt-2 pt-2 border-t border-gray-300 text-[10px] uppercase tracking-wide">Navigation Aids</div>
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 2L3 7v11h4v-6h6v6h4V7l-7-5z"/>
                                        </svg>
                                        <span class="text-gray-600">Lighthouse</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 rounded-full bg-red-500 border-2 border-white shadow-sm"></div>
                                        <span class="text-gray-600">Buoy</span>
                                    </div>
                                    
                                    <div class="font-semibold text-gray-700 mb-1 mt-2 pt-2 border-t border-gray-300 text-[10px] uppercase tracking-wide">Routes</div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-1 bg-blue-600 rounded"></div>
                                        <span class="text-gray-600">Sea Route</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Marine Features Summary -->
                            <div class="p-3 bg-gradient-to-r from-indigo-50 to-blue-50 border-t border-gray-200">
                                <h4 class="text-xs font-semibold text-indigo-700 mb-2">Active Features</h4>
                                <div id="marine-features-summary" class="text-center text-gray-500 text-[10px]">
                                    Loading...
                                </div>
                            </div>
                            
                            <!-- Marine Features Info -->
                            <div class="p-3 bg-indigo-50 border-t border-gray-200">
                                <h4 class="text-xs font-semibold text-indigo-700 mb-2">About Marine Features</h4>
                                <div class="space-y-1.5 text-[10px] text-indigo-600">
                                    <div>
                                        <span class="font-semibold">Depth Contours:</span>
                                        <span class="ml-1">Shows water depth zones around stops for navigation safety.</span>
                                    </div>
                                    <div>
                                        <span class="font-semibold">Navigation Aids:</span>
                                        <span class="ml-1">Lighthouses and buoys marking safe navigation points.</span>
                                    </div>
                                    <div>
                                        <span class="font-semibold">Sea Routes:</span>
                                        <span class="ml-1">Optimal nautical routes between stops with directional indicators.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="absolute inset-0 flex items-center justify-center text-gray-500 text-sm z-10 bg-gray-50" id="map-view-loading">
                            <div class="text-center">
                                <svg class="animate-spin h-10 w-10 text-indigo-600 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-gray-700 font-medium">Loading marine map...</p>
                            </div>
                        </div>
                        
                        <!-- Marine Map Controls - Moved before loading overlay to ensure visibility -->
                        <div class="absolute top-4 right-4 z-40 bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden" style="max-width: 280px; display: block !important;">
                            <!-- Route Information -->
                            <div class="p-3 border-b border-gray-200">
                                <h4 class="text-xs font-semibold text-gray-700 mb-2">Route Information</h4>
                                <div class="space-y-1 text-xs text-gray-600">
                                    <p><span class="font-medium">Stops:</span> <span id="view-legend-stops">{{ count($route->stops) }}</span></p>
                                    <p><span class="font-medium">Distance:</span> <span id="view-legend-distance">{{ number_format($route->distance_nm, 2) }} NM</span></p>
                                </div>
                            </div>
                            <!-- Marine Features Toggle -->
                            <div class="p-3 space-y-2">
                                <h4 class="text-xs font-semibold text-gray-700 mb-2">Marine Features</h4>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" id="view-toggle-depth-contours" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-xs text-gray-600">Depth Contours</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" id="view-toggle-navigation-aids" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-xs text-gray-600">Navigation Aids</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" id="view-toggle-sea-routes" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-xs text-gray-600">Sea Routes</span>
                                </label>
                            </div>
                            <!-- Marine Legend -->
                            <div class="p-3 bg-gray-50 border-t border-gray-200">
                                <h4 class="text-xs font-semibold text-gray-700 mb-2">Legend</h4>
                                <div class="space-y-1.5 text-xs">
                                    <div class="font-semibold text-gray-700 mb-1 text-[10px] uppercase tracking-wide">Route Markers</div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 rounded-full bg-green-500 border-2 border-white shadow-sm"></div>
                                        <span class="text-gray-600">Start</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 rounded-full bg-indigo-500 border-2 border-white shadow-sm"></div>
                                        <span class="text-gray-600">Stop</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 rounded-full bg-red-500 border-2 border-white shadow-sm"></div>
                                        <span class="text-gray-600">End</span>
                                    </div>
                                    
                                    <div class="font-semibold text-gray-700 mb-1 mt-2 pt-2 border-t border-gray-300 text-[10px] uppercase tracking-wide">Depth Zones</div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-4 h-2 bg-blue-300 rounded border border-blue-400"></div>
                                        <span class="text-gray-600">Shallow (&lt;10m)</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-4 h-2 bg-blue-500 rounded border border-blue-600"></div>
                                        <span class="text-gray-600">Deep (&gt;10m)</span>
                                    </div>
                                    
                                    <div class="font-semibold text-gray-700 mb-1 mt-2 pt-2 border-t border-gray-300 text-[10px] uppercase tracking-wide">Navigation Aids</div>
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 2L3 7v11h4v-6h6v6h4V7l-7-5z"/>
                                        </svg>
                                        <span class="text-gray-600">Lighthouse</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 rounded-full bg-red-500 border-2 border-white shadow-sm"></div>
                                        <span class="text-gray-600">Buoy</span>
                                    </div>
                                    
                                    <div class="font-semibold text-gray-700 mb-1 mt-2 pt-2 border-t border-gray-300 text-[10px] uppercase tracking-wide">Routes</div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-1 bg-blue-600 rounded"></div>
                                        <span class="text-gray-600">Sea Route</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Marine Features Summary -->
                            <div class="p-3 bg-gradient-to-r from-indigo-50 to-blue-50 border-t border-gray-200">
                                <h4 class="text-xs font-semibold text-indigo-700 mb-2">Active Features</h4>
                                <div id="marine-features-summary" class="text-center text-gray-500 text-[10px]">
                                    Loading...
                                </div>
                            </div>
                            
                            <!-- Marine Features Info -->
                            <div class="p-3 bg-indigo-50 border-t border-gray-200">
                                <h4 class="text-xs font-semibold text-indigo-700 mb-2">About Marine Features</h4>
                                <div class="space-y-1.5 text-[10px] text-indigo-600">
                                    <div>
                                        <span class="font-semibold">Depth Contours:</span>
                                        <span class="ml-1">Shows water depth zones around stops for navigation safety.</span>
                                    </div>
                                    <div>
                                        <span class="font-semibold">Navigation Aids:</span>
                                        <span class="ml-1">Lighthouses and buoys marking safe navigation points.</span>
                                    </div>
                                    <div>
                                        <span class="font-semibold">Sea Routes:</span>
                                        <span class="ml-1">Optimal nautical routes between stops with directional indicators.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Additional Sections --}}
                <div class="space-y-6">
                    @livewire('itinerary.route-weather', ['route' => $route], key('weather-'.$route->id))
                    @livewire('itinerary.route-exports', ['route' => $route], key('exports-'.$route->id))
                </div>

                @livewire('itinerary.route-crew-manager', ['route' => $route], key('crew-'.$route->id))

                @livewire('itinerary.route-discussion', ['route' => $route], key('discussion-'.$route->id))

                @livewire('itinerary.route-reviews', ['route' => $route], key('reviews-'.$route->id))

                @livewire('itinerary.route-analytics', ['route' => $route], key('analytics-'.$route->id))
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.css" rel="stylesheet">
        <style>
            #route-view-map {
                position: relative;
                width: 100%;
                height: 600px;
                min-height: 600px;
            }
            #route-view-map .mapboxgl-canvas {
                position: absolute;
                top: 0;
                left: 0;
                width: 100% !important;
                height: 100% !important;
            }
            .mapboxgl-popup-content {
                padding: 12px;
                min-width: 200px;
            }
        </style>
    @endpush
    @push('scripts')
        <script src="https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('imageGallery', () => ({
                    allImages: @json($allImages),
                    selectedImage: 0,
                    displayMode: 'cover',
                    init() {
                        // Keyboard navigation
                        const handleKeydown = (e) => {
                            if (e.key === 'ArrowLeft') {
                                this.selectedImage = (this.selectedImage - 1 + this.allImages.length) % this.allImages.length;
                            } else if (e.key === 'ArrowRight') {
                                this.selectedImage = (this.selectedImage + 1) % this.allImages.length;
                            }
                        };
                        document.addEventListener('keydown', handleKeydown);
                        // Cleanup on component destroy
                        this.$el.addEventListener('alpine:destroyed', () => {
                            document.removeEventListener('keydown', handleKeydown);
                        });
                    }
                }));
            });
            
            // Marine Map for Route View - Mapbox GL JS
            let viewMap = null;
            let viewMarkers = [];
            let viewDepthContours = [];
            let viewNavigationAids = [];
            let viewSeaRoutes = [];
            let viewPolyline = null;
            let showViewDepthContours = true;
            let showViewNavigationAids = true;
            let showViewSeaRoutes = true;
            
            const routeStops = @json($routeStopsData);
            
            function initViewMap() {
                const mapEl = document.getElementById('route-view-map');
                if (!mapEl) {
                    setTimeout(initViewMap, 100);
                    return;
                }
                
                if (typeof mapboxgl === 'undefined') {
                    setTimeout(initViewMap, 100);
                    return;
                }
                
                const loadingEl = document.getElementById('map-view-loading');
                
                // Get Mapbox access token
                @php
                    $tokenValue = config('services.mapbox.access_token');
                    if ($tokenValue) {
                        $tokenJson = json_encode($tokenValue, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                    } else {
                        $tokenJson = 'null';
                    }
                @endphp
                var mapboxTokenRaw = {!! $tokenJson !!};
                var mapboxToken = '';
                if (mapboxTokenRaw && mapboxTokenRaw !== null) {
                    mapboxToken = String(mapboxTokenRaw);
                    if (mapboxToken === 'null' || mapboxToken === '') {
                        mapboxToken = '';
                    }
                }
                
                if (!mapboxToken) {
                    if (loadingEl) {
                        loadingEl.innerHTML = '<div class="text-center p-4"><p class="text-red-600 font-medium mb-2">Mapbox Access Token Required</p><p class="text-sm text-gray-600">Please configure MAPBOX_ACCESS_TOKEN in .env file</p></div>';
                    }
                    return;
                }
                
                mapboxgl.accessToken = mapboxToken;
                
                // Initialize Mapbox map with marine navigation style
                viewMap = new mapboxgl.Map({
                    container: mapEl,
                    style: 'mapbox://styles/mapbox/navigation-day-v1', // Marine-friendly style
                    center: [0, 20], // [longitude, latitude]
                    zoom: 2,
                    minZoom: 1,
                    maxZoom: 20
                });
                
                // Hide loading overlay once map is ready
                viewMap.once('load', function() {
                    console.log('[MAP] Mapbox map loaded, initializing route and marine features');
                    if (loadingEl) {
                        loadingEl.style.display = 'none';
                    }
                    
                    // Ensure map container has proper dimensions
                    mapEl.style.height = '600px';
                    mapEl.style.width = '100%';
                    viewMap.resize();
                    
                    // Draw route stops and marine features
                    setTimeout(() => {
                        drawViewRoute();
                        setupViewMarineToggles();
                        
                        // Ensure bounds are fitted after everything is drawn
                        setTimeout(() => {
                            if (viewMarkers.length > 0) {
                                const coords = viewMarkers.map(m => {
                                    const lngLat = m.getLngLat();
                                    return [lngLat.lng, lngLat.lat];
                                });
                                
                                if (coords.length > 0) {
                                    const bounds = coords.reduce(function(bounds, coord) {
                                        return bounds.extend(coord);
                                    }, new mapboxgl.LngLatBounds(coords[0], coords[0]));
                                    
                                    if (bounds && bounds.getNorth() !== bounds.getSouth() && bounds.getEast() !== bounds.getWest()) {
                                        viewMap.fitBounds(bounds, {
                                            padding: {top: 100, bottom: 100, left: 100, right: 400},
                                            maxZoom: 15,
                                            duration: 1000
                                        });
                                        console.log('[MAP VIEW] Final bounds fitted to show all', coords.length, 'stops');
                                    }
                                }
                            }
                        }, 1000);
                    }, 200);
                });
                
                // Handle style errors
                viewMap.on('style.error', function(e) {
                    console.error('[MAP] Style error, trying fallback:', e);
                    viewMap.setStyle('mapbox://styles/mapbox/streets-v12');
                });
            }
            
            function drawViewRoute() {
                if (!viewMap || routeStops.length === 0) return;
                
                // Clear existing markers
                viewMarkers.forEach(marker => {
                    if (marker && marker.remove) marker.remove();
                });
                viewMarkers = [];
                
                // Remove existing polyline layer
                if (viewMap.getLayer('route-line')) {
                    viewMap.removeLayer('route-line');
                }
                if (viewMap.getSource('route-line')) {
                    viewMap.removeSource('route-line');
                }
                
                const coordinates = [];
                const distances = [];
                let validStopsCount = 0;
                
                routeStops.forEach((stop, index) => {
                    if (stop.latitude && stop.longitude) {
                        const lat = parseFloat(stop.latitude);
                        const lng = parseFloat(stop.longitude);
                        if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                            validStopsCount++;
                            console.log('[MAP VIEW] Adding stop', index + 1, ':', stop.name, 'at', lat, lng);
                            const markerColor = index === 0 ? '#10b981' : (index === routeStops.length - 1 ? '#ef4444' : '#4f46e5');
                            const stopNumber = stop.sequence || index + 1;
                            
                            // Create custom marker element
                            const el = document.createElement('div');
                            el.className = 'custom-stop-marker';
                            el.style.width = '35px';
                            el.style.height = '35px';
                            el.style.borderRadius = '50%';
                            el.style.backgroundColor = markerColor;
                            el.style.border = '3px solid white';
                            el.style.display = 'flex';
                            el.style.alignItems = 'center';
                            el.style.justifyContent = 'center';
                            el.style.color = 'white';
                            el.style.fontWeight = 'bold';
                            el.style.fontSize = '14px';
                            el.style.boxShadow = '0 2px 5px rgba(0,0,0,0.3)';
                            el.style.cursor = 'pointer';
                            el.textContent = stopNumber;
                            
                            // Create Mapbox marker
                            const marker = new mapboxgl.Marker(el)
                                .setLngLat([lng, lat])
                                .addTo(viewMap);
                            
                            // Create popup
                            const popupContent = `
                                <div style="padding: 12px; min-width: 200px;">
                                    <div class="flex items-center mb-2">
                                        <div style="width: 8px; height: 8px; background: ${markerColor}; border-radius: 50%; margin-right: 8px;"></div>
                                        <strong style="font-size: 14px; color: #111827;">${stop.name || 'Stop ' + stopNumber}</strong>
                                    </div>
                                    ${stop.location_label ? `<div class="text-xs text-gray-600 mb-1">📍 ${stop.location_label}</div>` : ''}
                                    ${stop.stay_duration_hours ? `<div class="text-xs text-gray-500 mb-1">⏱️ Stay: ${stop.stay_duration_hours} hours</div>` : ''}
                                    ${stop.notes ? `<div class="text-xs text-gray-600 mt-2 pt-2 border-t border-gray-200">${stop.notes}</div>` : ''}
                                </div>
                            `;
                            
                            const popup = new mapboxgl.Popup({ offset: 25 })
                                .setHTML(popupContent);
                            
                            marker.setPopup(popup);
                            
                            viewMarkers.push(marker);
                            coordinates.push([lng, lat]);
                            
                            // Calculate distance (nautical miles)
                            if (coordinates.length > 1) {
                                const prevCoord = coordinates[coordinates.length - 2];
                                const currCoord = coordinates[coordinates.length - 1];
                                const distance = calculateDistanceNM(prevCoord[1], prevCoord[0], currCoord[1], currCoord[0]);
                                distances.push(distance);
                            }
                        } else {
                            console.warn('[MAP VIEW] Invalid coordinates for stop', index + 1, ':', lat, lng);
                        }
                    } else {
                        console.warn('[MAP VIEW] Missing coordinates for stop', index + 1, ':', stop.name);
                    }
                });
                
                console.log('[MAP VIEW] Total valid stops added:', validStopsCount, 'out of', routeStops.length);
                
                // Draw polyline using GeoJSON
                if (coordinates.length > 1) {
                    const geojson = {
                        type: 'Feature',
                        properties: {},
                        geometry: {
                            type: 'LineString',
                            coordinates: coordinates
                        }
                    };
                    
                    viewMap.addSource('route-line', {
                        type: 'geojson',
                        data: geojson
                    });
                    
                    viewMap.addLayer({
                        id: 'route-line',
                        type: 'line',
                        source: 'route-line',
                        layout: {
                            'line-join': 'round',
                            'line-cap': 'round'
                        },
                        paint: {
                            'line-color': '#1e40af',
                            'line-width': 4,
                            'line-opacity': 0.8
                        }
                    });
                }
                
                // Draw marine features after a short delay
                setTimeout(() => {
                    console.log('[MAP VIEW] Drawing marine features for', viewMarkers.length, 'stops');
                    if (viewMarkers.length > 0) {
                        drawViewDepthContours();
                        drawViewNavigationAids();
                        drawViewSeaRoutes();
                        updateMarineFeaturesSummary();
                    } else {
                        console.warn('[MAP VIEW] No markers available for marine features');
                    }
                }, 500);
                
                // Update legend with actual stop count
                const legendStopsEl = document.getElementById('view-legend-stops');
                if (legendStopsEl) {
                    legendStopsEl.textContent = validStopsCount || coordinates.length;
                }
                
                // Fit bounds to show all stops - defer to allow markers to render first
                setTimeout(() => {
                    if (coordinates.length > 0) {
                        console.log('[MAP VIEW] Fitting bounds for', coordinates.length, 'stops');
                        const bounds = coordinates.reduce(function(bounds, coord) {
                            return bounds.extend(coord);
                        }, new mapboxgl.LngLatBounds(coordinates[0], coordinates[0]));
                        
                        // Ensure bounds are valid
                        if (bounds && bounds.getNorth() !== bounds.getSouth() && bounds.getEast() !== bounds.getWest()) {
                            viewMap.fitBounds(bounds, {
                                padding: {top: 100, bottom: 100, left: 100, right: 400}, // Extra right padding for info panel
                                maxZoom: 15, // Prevent zooming in too much
                                duration: 1000 // Smooth animation
                            });
                            console.log('[MAP VIEW] Bounds fitted successfully for', coordinates.length, 'stops');
                        } else {
                            // If bounds are invalid (same point), just center on first stop
                            console.warn('[MAP VIEW] Invalid bounds, centering on first stop');
                            viewMap.setCenter(coordinates[0]);
                            viewMap.setZoom(10);
                        }
                    } else {
                        console.warn('[MAP VIEW] No coordinates to fit bounds');
                    }
                }, 300);
            }
            
            // Helper function to calculate distance in nautical miles
            function calculateDistanceNM(lat1, lon1, lat2, lon2) {
                const R = 3440.065; // Earth radius in nautical miles
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLon = (lon2 - lon1) * Math.PI / 180;
                const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                    Math.sin(dLon/2) * Math.sin(dLon/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                return R * c;
            }
            
            function drawViewDepthContours() {
                // Remove existing depth contour layers
                if (viewMap.getLayer('depth-contours-shallow')) {
                    viewMap.removeLayer('depth-contours-shallow');
                }
                if (viewMap.getLayer('depth-contours-deep')) {
                    viewMap.removeLayer('depth-contours-deep');
                }
                if (viewMap.getSource('depth-contours-shallow')) {
                    viewMap.removeSource('depth-contours-shallow');
                }
                if (viewMap.getSource('depth-contours-deep')) {
                    viewMap.removeSource('depth-contours-deep');
                }
                
                if (!viewMap || !showViewDepthContours || viewMarkers.length === 0) return;
                
                const shallowFeatures = [];
                const deepFeatures = [];
                
                viewMarkers.forEach((marker, index) => {
                    if (!marker) return;
                    const lngLat = marker.getLngLat();
                    if (!lngLat) return;
                    
                    const lat = lngLat.lat;
                    const lng = lngLat.lng;
                    
                    // Create circle GeoJSON features (approximate circles using polygons)
                    const shallowCircle = createCircleGeoJSON(lng, lat, 5000); // 5km radius
                    const deepCircle = createCircleGeoJSON(lng, lat, 15000); // 15km radius
                    
                    shallowFeatures.push(shallowCircle);
                    deepFeatures.push(deepCircle);
                });
                
                if (shallowFeatures.length > 0) {
                    viewMap.addSource('depth-contours-shallow', {
                        type: 'geojson',
                        data: {
                            type: 'FeatureCollection',
                            features: shallowFeatures
                        }
                    });
                    
                    viewMap.addLayer({
                        id: 'depth-contours-shallow',
                        type: 'fill',
                        source: 'depth-contours-shallow',
                        paint: {
                            'fill-color': '#93c5fd',
                            'fill-opacity': 0.15
                        }
                    });
                    
                    viewMap.addLayer({
                        id: 'depth-contours-shallow-outline',
                        type: 'line',
                        source: 'depth-contours-shallow',
                        paint: {
                            'line-color': '#60a5fa',
                            'line-opacity': 0.4,
                            'line-width': 2
                        }
                    });
                }
                
                if (deepFeatures.length > 0) {
                    viewMap.addSource('depth-contours-deep', {
                        type: 'geojson',
                        data: {
                            type: 'FeatureCollection',
                            features: deepFeatures
                        }
                    });
                    
                    viewMap.addLayer({
                        id: 'depth-contours-deep',
                        type: 'fill',
                        source: 'depth-contours-deep',
                        paint: {
                            'fill-color': '#60a5fa',
                            'fill-opacity': 0.1
                        }
                    });
                    
                    viewMap.addLayer({
                        id: 'depth-contours-deep-outline',
                        type: 'line',
                        source: 'depth-contours-deep',
                        paint: {
                            'line-color': '#3b82f6',
                            'line-opacity': 0.3,
                            'line-width': 1
                        }
                    });
                }
            }
            
            // Helper function to create circle GeoJSON
            function createCircleGeoJSON(lng, lat, radiusMeters) {
                const points = 64;
                const coordinates = [];
                for (let i = 0; i < points; i++) {
                    const angle = (i * 360) / points;
                    const dx = radiusMeters * Math.cos(angle * Math.PI / 180);
                    const dy = radiusMeters * Math.sin(angle * Math.PI / 180);
                    const latOffset = dy / 111320; // meters to degrees (approximate)
                    const lngOffset = dx / (111320 * Math.cos(lat * Math.PI / 180));
                    coordinates.push([lng + lngOffset, lat + latOffset]);
                }
                coordinates.push(coordinates[0]); // Close the circle
                
                return {
                    type: 'Feature',
                    geometry: {
                        type: 'Polygon',
                        coordinates: [coordinates]
                    }
                };
            }
            
            function drawViewNavigationAids() {
                // Remove existing navigation aid markers
                viewNavigationAids.forEach(aid => {
                    if (aid && aid.remove) aid.remove();
                });
                viewNavigationAids = [];
                
                if (!viewMap || !showViewNavigationAids || viewMarkers.length === 0) {
                    console.log('drawViewNavigationAids: Skipping - map:', !!viewMap, 'enabled:', showViewNavigationAids, 'markers:', viewMarkers.length);
                    return;
                }
                
                console.log('drawViewNavigationAids: Drawing aids for', viewMarkers.length, 'markers');
                
                viewMarkers.forEach((marker, index) => {
                    if (!marker) return;
                    const lngLat = marker.getLngLat();
                    if (!lngLat) return;
                    
                    const lat = lngLat.lat;
                    const lng = lngLat.lng;
                    
                    if (index === 0) {
                        // Lighthouse at first stop
                        const lighthousePos = [lng + 0.01, lat + 0.01];
                        const lighthouseIcon = document.createElement('div');
                        lighthouseIcon.innerHTML = '🏭';
                        lighthouseIcon.style.fontSize = '24px';
                        lighthouseIcon.style.textAlign = 'center';
                        lighthouseIcon.style.cursor = 'pointer';
                        
                        const lighthouse = new mapboxgl.Marker(lighthouseIcon)
                            .setLngLat(lighthousePos)
                            .setPopup(new mapboxgl.Popup().setText('Lighthouse'))
                            .addTo(showViewNavigationAids ? viewMap : null);
                        
                        viewNavigationAids.push(lighthouse);
                    }
                    
                    if (index > 0 && index < viewMarkers.length - 1) {
                        // Buoy at intermediate stops
                        const buoyPos = [
                            lng + (Math.random() - 0.5) * 0.02,
                            lat + (Math.random() - 0.5) * 0.02
                        ];
                        
                        const buoyIcon = document.createElement('div');
                        buoyIcon.style.width = '16px';
                        buoyIcon.style.height = '16px';
                        buoyIcon.style.borderRadius = '50%';
                        buoyIcon.style.backgroundColor = '#ef4444';
                        buoyIcon.style.border = '2px solid white';
                        buoyIcon.style.boxShadow = '0 2px 4px rgba(0,0,0,0.3)';
                        buoyIcon.style.cursor = 'pointer';
                        
                        const buoy = new mapboxgl.Marker(buoyIcon)
                            .setLngLat(buoyPos)
                            .setPopup(new mapboxgl.Popup().setText('Navigation Buoy'))
                            .addTo(showViewNavigationAids ? viewMap : null);
                        
                        viewNavigationAids.push(buoy);
                    }
                });
            }
            
            function drawViewSeaRoutes() {
                // Remove existing sea route layers
                if (viewMap.getLayer('sea-routes')) {
                    viewMap.removeLayer('sea-routes');
                }
                if (viewMap.getSource('sea-routes')) {
                    viewMap.removeSource('sea-routes');
                }
                
                if (!viewMap || !showViewSeaRoutes || viewMarkers.length < 2) {
                    console.log('drawViewSeaRoutes: Skipping - map:', !!viewMap, 'enabled:', showViewSeaRoutes, 'markers:', viewMarkers.length);
                    return;
                }
                
                console.log('drawViewSeaRoutes: Drawing routes for', viewMarkers.length, 'markers');
                
                const routeFeatures = [];
                
                for (let i = 0; i < viewMarkers.length - 1; i++) {
                    const marker1 = viewMarkers[i];
                    const marker2 = viewMarkers[i + 1];
                    if (!marker1 || !marker2) continue;
                    
                    const lngLat1 = marker1.getLngLat();
                    const lngLat2 = marker2.getLngLat();
                    if (!lngLat1 || !lngLat2) continue;
                    
                    routeFeatures.push({
                        type: 'Feature',
                        geometry: {
                            type: 'LineString',
                            coordinates: [
                                [lngLat1.lng, lngLat1.lat],
                                [lngLat2.lng, lngLat2.lat]
                            ]
                        }
                    });
                }
                
                if (routeFeatures.length > 0) {
                    viewMap.addSource('sea-routes', {
                        type: 'geojson',
                        data: {
                            type: 'FeatureCollection',
                            features: routeFeatures
                        }
                    });
                    
                    viewMap.addLayer({
                        id: 'sea-routes',
                        type: 'line',
                        source: 'sea-routes',
                        layout: {
                            'line-join': 'round',
                            'line-cap': 'round'
                        },
                        paint: {
                            'line-color': '#1e40af',
                            'line-width': 3,
                            'line-opacity': 0.6
                        }
                    });
                }
            }
            
            function setupViewMarineToggles() {
                const depthToggle = document.getElementById('view-toggle-depth-contours');
                const aidsToggle = document.getElementById('view-toggle-navigation-aids');
                const routesToggle = document.getElementById('view-toggle-sea-routes');
                
                if (depthToggle) {
                    depthToggle.addEventListener('change', function(e) {
                        showViewDepthContours = e.target.checked;
                        drawViewDepthContours(); // Redraw to add/remove layers
                        updateMarineFeaturesSummary();
                    });
                }
                
                if (aidsToggle) {
                    aidsToggle.addEventListener('change', function(e) {
                        showViewNavigationAids = e.target.checked;
                        drawViewNavigationAids(); // Redraw to add/remove markers
                        updateMarineFeaturesSummary();
                    });
                }
                
                if (routesToggle) {
                    routesToggle.addEventListener('change', function(e) {
                        showViewSeaRoutes = e.target.checked;
                        drawViewSeaRoutes(); // Redraw to add/remove layers
                        updateMarineFeaturesSummary();
                    });
                }
            }
            
            function updateMarineFeaturesSummary() {
                const summaryEl = document.getElementById('marine-features-summary');
                if (!summaryEl) return;
                
                const activeFeatures = [];
                if (showViewDepthContours) activeFeatures.push('Depth Contours');
                if (showViewNavigationAids) activeFeatures.push('Navigation Aids');
                if (showViewSeaRoutes) activeFeatures.push('Sea Routes');
                
                if (activeFeatures.length === 0) {
                    summaryEl.textContent = 'No features active';
                } else {
                    summaryEl.textContent = activeFeatures.join(', ');
                }
            }
            
            window.initViewMap = initViewMap;
            
            // Initialize map when Mapbox is ready
            function startMapInit() {
                if (typeof mapboxgl !== 'undefined') {
                    console.log('[MAP VIEW] Mapbox GL JS available, initializing...');
                    initViewMap();
                } else {
                    console.log('[MAP VIEW] Waiting for Mapbox GL JS...');
                    setTimeout(startMapInit, 100);
                }
            }
            
            // Start initialization when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', startMapInit);
            } else {
                // DOM already loaded, start immediately
                setTimeout(startMapInit, 500); // Give a moment for scripts to load
            }
            
            // Also try on window load as fallback
            window.addEventListener('load', function() {
                if (!viewMap) {
                    console.log('[MAP VIEW] Window loaded, trying to initialize map...');
                    setTimeout(startMapInit, 500);
                }
            });
        </script>
    @endpush
</x-app-layout>


