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

    @push('scripts')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places,geometry,marker&callback=initViewMap" async defer></script>
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
            
            // Marine Map for Route View
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
                
                if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                    setTimeout(initViewMap, 100);
                    return;
                }
                
                const loadingEl = document.getElementById('map-view-loading');
                
                const mapConfig = {
                    center: { lat: 20, lng: 0 },
                    zoom: 2,
                    mapTypeId: 'roadmap',
                    zoomControl: true,
                    mapTypeControl: true,
                    scaleControl: true,
                    streetViewControl: false,
                    rotateControl: false,
                    fullscreenControl: true,
                    styles: [
                        {
                            featureType: 'water',
                            elementType: 'geometry',
                            stylers: [{ color: '#4a90e2' }, { lightness: -10 }, { saturation: 20 }]
                        },
                        {
                            featureType: 'water',
                            elementType: 'labels',
                            stylers: [{ visibility: 'on' }, { color: '#1a56db' }, { lightness: -20 }]
                        },
                        {
                            featureType: 'landscape',
                            elementType: 'geometry',
                            stylers: [{ color: '#e8f5e9' }, { lightness: 30 }]
                        },
                        {
                            featureType: 'landscape.natural',
                            elementType: 'geometry',
                            stylers: [{ color: '#c8e6c9' }]
                        },
                        {
                            featureType: 'road',
                            elementType: 'geometry',
                            stylers: [{ visibility: 'simplified' }, { color: '#ffffff' }, { lightness: 50 }]
                        },
                        {
                            featureType: 'poi',
                            elementType: 'labels',
                            stylers: [{ visibility: 'off' }]
                        },
                        {
                            featureType: 'transit',
                            elementType: 'all',
                            stylers: [{ visibility: 'off' }]
                        }
                    ]
                };
                
                const mapId = @json(config('services.google_maps.map_id', null));
                if (mapId) {
                    mapConfig.mapId = mapId;
                }
                
                viewMap = new google.maps.Map(mapEl, mapConfig);
                
                // Hide loading overlay once map is ready
                google.maps.event.addListenerOnce(viewMap, 'idle', function() {
                    console.log('Map is ready, initializing route and marine features');
                    if (loadingEl) {
                        loadingEl.style.display = 'none';
                    }
                    
                    // Draw route stops and marine features
                    setTimeout(() => {
                        drawViewRoute();
                        setupViewMarineToggles();
                    }, 200);
                });
            }
            
            function drawViewRoute() {
                if (!viewMap || routeStops.length === 0) return;
                
                const latLngs = [];
                const distances = [];
                
                routeStops.forEach((stop, index) => {
                    if (stop.latitude && stop.longitude) {
                        const lat = parseFloat(stop.latitude);
                        const lng = parseFloat(stop.longitude);
                        if (!isNaN(lat) && !isNaN(lng)) {
                            const position = { lat: lat, lng: lng };
                            const markerColor = index === 0 ? '#10b981' : (index === routeStops.length - 1 ? '#ef4444' : '#4f46e5');
                            
                            // Create marker
                            const hasMapId = viewMap && viewMap.mapId;
                            let marker;
                            
                            if (typeof google.maps.marker !== 'undefined' && 
                                google.maps.marker.AdvancedMarkerElement && 
                                hasMapId) {
                                const pinElement = new google.maps.marker.PinElement({
                                    background: markerColor,
                                    borderColor: '#ffffff',
                                    scale: 1.2,
                                    glyphText: String(stop.sequence || index + 1),
                                    glyphColor: '#ffffff'
                                });
                                
                                marker = new google.maps.marker.AdvancedMarkerElement({
                                    position: position,
                                    map: viewMap,
                                    content: pinElement.element,
                                    title: stop.name,
                                    zIndex: 1000 - index
                                });
                            } else {
                                marker = new google.maps.Marker({
                                    position: position,
                                    map: viewMap,
                                    title: stop.name,
                                    label: {
                                        text: String(stop.sequence || index + 1),
                                        color: 'white',
                                        fontWeight: 'bold',
                                        fontSize: '12px'
                                    },
                                    icon: {
                                        path: google.maps.SymbolPath.CIRCLE,
                                        scale: 12,
                                        fillColor: markerColor,
                                        fillOpacity: 1,
                                        strokeColor: '#ffffff',
                                        strokeWeight: 3
                                    },
                                    zIndex: 1000 - index
                                });
                            }
                            
                            // Info window
                            const infoContent = `
                                <div style="padding: 12px; min-width: 200px;">
                                    <div class="flex items-center mb-2">
                                        <div style="width: 8px; height: 8px; background: ${markerColor}; border-radius: 50%; margin-right: 8px;"></div>
                                        <strong style="font-size: 14px; color: #111827;">${stop.name || 'Stop ' + (index + 1)}</strong>
                                    </div>
                                    ${stop.location_label ? `<div class="text-xs text-gray-600 mb-1">📍 ${stop.location_label}</div>` : ''}
                                    ${stop.stay_duration_hours ? `<div class="text-xs text-gray-500 mb-1">⏱️ Stay: ${stop.stay_duration_hours} hours</div>` : ''}
                                    ${stop.notes ? `<div class="text-xs text-gray-600 mt-2 pt-2 border-t border-gray-200">${stop.notes}</div>` : ''}
                                </div>
                            `;
                            
                            const infoWindow = new google.maps.InfoWindow({ content: infoContent });
                            
                            marker.addListener('click', () => {
                                viewMarkers.forEach(m => {
                                    if (m.infoWindow) m.infoWindow.close();
                                });
                                marker.infoWindow = infoWindow;
                                if (marker.position) {
                                    infoWindow.open({ anchor: marker, map: viewMap });
                                } else {
                                    infoWindow.open(viewMap, marker);
                                }
                            });
                            
                            viewMarkers.push(marker);
                            latLngs.push(position);
                            
                            // Calculate distance
                            if (latLngs.length > 1 && typeof google.maps.geometry !== 'undefined') {
                                const prevPosition = latLngs[latLngs.length - 2];
                                const distance = google.maps.geometry.spherical.computeDistanceBetween(
                                    new google.maps.LatLng(prevPosition.lat, prevPosition.lng),
                                    new google.maps.LatLng(position.lat, position.lng)
                                );
                                distances.push(distance / 1852); // Convert to NM
                            }
                        }
                    }
                });
                
                // Draw polyline
                if (latLngs.length > 1) {
                    const path = latLngs.map(pos => new google.maps.LatLng(pos.lat, pos.lng));
                    viewPolyline = new google.maps.Polyline({
                        path: path,
                        geodesic: true,
                        strokeColor: '#1e40af',
                        strokeOpacity: 0.8,
                        strokeWeight: 4,
                        icons: [{
                            icon: {
                                path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                                scale: 5,
                                strokeColor: '#1e40af',
                                fillColor: '#1e40af',
                                fillOpacity: 1
                            },
                            offset: '100%',
                            repeat: '80px'
                        }],
                        zIndex: 200
                    });
                    viewPolyline.setMap(viewMap);
                }
                
                // Draw marine features after a short delay to ensure markers are ready
                setTimeout(() => {
                    console.log('Drawing marine features for', viewMarkers.length, 'stops');
                    if (viewMarkers.length > 0) {
                        drawViewDepthContours();
                        drawViewNavigationAids();
                        drawViewSeaRoutes();
                        updateMarineFeaturesSummary();
                    } else {
                        console.warn('No markers available for marine features');
                    }
                }, 500);
                
                // Fit bounds
                if (latLngs.length > 0) {
                    const bounds = new google.maps.LatLngBounds();
                    latLngs.forEach(latLng => bounds.extend(latLng));
                    viewMap.fitBounds(bounds, { padding: 50 });
                }
            }
            
            function drawViewDepthContours() {
                viewDepthContours.forEach(contour => {
                    if (contour && contour.setMap) contour.setMap(null);
                });
                viewDepthContours = [];
                
                if (!viewMap || !showViewDepthContours || viewMarkers.length === 0) return;
                
                viewMarkers.forEach((marker, index) => {
                    if (!marker) return;
                    const position = marker.position || marker.getPosition();
                    if (!position) return;
                    const lat = typeof position.lat === 'function' ? position.lat() : position.lat;
                    const lng = typeof position.lng === 'function' ? position.lng() : position.lng;
                    
                    const shallowCircle = new google.maps.Circle({
                        strokeColor: '#60a5fa',
                        strokeOpacity: 0.4,
                        strokeWeight: 2,
                        fillColor: '#93c5fd',
                        fillOpacity: 0.15,
                        map: showViewDepthContours ? viewMap : null,
                        center: { lat: lat, lng: lng },
                        radius: 5000,
                        zIndex: 1
                    });
                    
                    const deepCircle = new google.maps.Circle({
                        strokeColor: '#3b82f6',
                        strokeOpacity: 0.3,
                        strokeWeight: 1,
                        fillColor: '#60a5fa',
                        fillOpacity: 0.1,
                        map: showViewDepthContours ? viewMap : null,
                        center: { lat: lat, lng: lng },
                        radius: 15000,
                        zIndex: 0
                    });
                    
                    viewDepthContours.push(shallowCircle, deepCircle);
                });
            }
            
            function drawViewNavigationAids() {
                viewNavigationAids.forEach(aid => {
                    if (aid && aid.setMap) aid.setMap(null);
                });
                viewNavigationAids = [];
                
                if (!viewMap || !showViewNavigationAids || viewMarkers.length === 0) {
                    console.log('drawViewNavigationAids: Skipping - map:', !!viewMap, 'enabled:', showViewNavigationAids, 'markers:', viewMarkers.length);
                    return;
                }
                
                console.log('drawViewNavigationAids: Drawing aids for', viewMarkers.length, 'markers');
                
                viewMarkers.forEach((marker, index) => {
                    if (!marker) return;
                    const position = marker.position || marker.getPosition();
                    if (!position) return;
                    const lat = typeof position.lat === 'function' ? position.lat() : position.lat;
                    const lng = typeof position.lng === 'function' ? position.lng() : position.lng;
                    
                    if (index === 0) {
                        const lighthousePos = { lat: lat + 0.01, lng: lng + 0.01 };
                        const hasMapId = viewMap && viewMap.mapId;
                        
                        if (typeof google.maps.marker !== 'undefined' && 
                            google.maps.marker.AdvancedMarkerElement && 
                            hasMapId) {
                            const lighthouseIcon = document.createElement('div');
                            lighthouseIcon.innerHTML = '🏭';
                            lighthouseIcon.style.fontSize = '24px';
                            lighthouseIcon.style.textAlign = 'center';
                            
                            const lighthouse = new google.maps.marker.AdvancedMarkerElement({
                                position: lighthousePos,
                                map: showViewNavigationAids ? viewMap : null,
                                content: lighthouseIcon,
                                title: 'Lighthouse',
                                zIndex: 500
                            });
                            viewNavigationAids.push(lighthouse);
                        } else {
                            const lighthouse = new google.maps.Marker({
                                position: lighthousePos,
                                map: showViewNavigationAids ? viewMap : null,
                                icon: {
                                    url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="%23fbbf24" d="M12 2L2 7v2h2v11h2V9h4v11h2V9h2V9h2V7L12 2zm0 2.18l6 2.22v1.4H6v-1.4l6-2.22z"/></svg>'),
                                    scaledSize: new google.maps.Size(24, 24),
                                    anchor: new google.maps.Point(12, 24)
                                },
                                title: 'Lighthouse',
                                zIndex: 500
                            });
                            viewNavigationAids.push(lighthouse);
                        }
                    }
                    
                    if (index > 0 && index < viewMarkers.length - 1) {
                        const buoyPos = {
                            lat: lat + (Math.random() - 0.5) * 0.02,
                            lng: lng + (Math.random() - 0.5) * 0.02
                        };
                        const hasMapId = viewMap && viewMap.mapId;
                        
                        if (typeof google.maps.marker !== 'undefined' && 
                            google.maps.marker.AdvancedMarkerElement && 
                            hasMapId) {
                            const buoyIcon = document.createElement('div');
                            buoyIcon.innerHTML = '🔴';
                            buoyIcon.style.fontSize = '20px';
                            buoyIcon.style.textAlign = 'center';
                            
                            const buoy = new google.maps.marker.AdvancedMarkerElement({
                                position: buoyPos,
                                map: showViewNavigationAids ? viewMap : null,
                                content: buoyIcon,
                                title: 'Navigation Buoy',
                                zIndex: 400
                            });
                            viewNavigationAids.push(buoy);
                        } else {
                            const buoy = new google.maps.Marker({
                                position: buoyPos,
                                map: showViewNavigationAids ? viewMap : null,
                                icon: {
                                    path: google.maps.SymbolPath.CIRCLE,
                                    scale: 8,
                                    fillColor: '#ef4444',
                                    fillOpacity: 1,
                                    strokeColor: '#ffffff',
                                    strokeWeight: 2
                                },
                                title: 'Navigation Buoy',
                                zIndex: 400
                            });
                            viewNavigationAids.push(buoy);
                        }
                    }
                });
            }
            
            function drawViewSeaRoutes() {
                viewSeaRoutes.forEach(route => {
                    if (route && route.setMap) route.setMap(null);
                });
                viewSeaRoutes = [];
                
                if (!viewMap || !showViewSeaRoutes || viewMarkers.length < 2) {
                    console.log('drawViewSeaRoutes: Skipping - map:', !!viewMap, 'enabled:', showViewSeaRoutes, 'markers:', viewMarkers.length);
                    return;
                }
                
                console.log('drawViewSeaRoutes: Drawing routes for', viewMarkers.length, 'markers');
                
                for (let i = 0; i < viewMarkers.length - 1; i++) {
                    const marker1 = viewMarkers[i];
                    const marker2 = viewMarkers[i + 1];
                    if (!marker1 || !marker2) continue;
                    
                    const pos1 = marker1.position || marker1.getPosition();
                    const pos2 = marker2.position || marker2.getPosition();
                    if (!pos1 || !pos2) continue;
                    
                    const lat1 = typeof pos1.lat === 'function' ? pos1.lat() : pos1.lat;
                    const lng1 = typeof pos1.lng === 'function' ? pos1.lng() : pos1.lng;
                    const lat2 = typeof pos2.lat === 'function' ? pos2.lat() : pos2.lat;
                    const lng2 = typeof pos2.lng === 'function' ? pos2.lng() : pos2.lng;
                    
                    const seaRoute = new google.maps.Polyline({
                        path: [{ lat: lat1, lng: lng1 }, { lat: lat2, lng: lng2 }],
                        geodesic: true,
                        strokeColor: '#1e40af',
                        strokeOpacity: 0.6,
                        strokeWeight: 3,
                        map: showViewSeaRoutes ? viewMap : null,
                        icons: [{
                            icon: {
                                path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                                scale: 5,
                                strokeColor: '#1e40af',
                                fillColor: '#1e40af',
                                fillOpacity: 1
                            },
                            offset: '100%',
                            repeat: '50px'
                        }],
                        zIndex: 100
                    });
                    
                    viewSeaRoutes.push(seaRoute);
                }
            }
            
            function setupViewMarineToggles() {
                const depthToggle = document.getElementById('view-toggle-depth-contours');
                const aidsToggle = document.getElementById('view-toggle-navigation-aids');
                const routesToggle = document.getElementById('view-toggle-sea-routes');
                
                if (depthToggle) {
                    depthToggle.addEventListener('change', function(e) {
                        showViewDepthContours = e.target.checked;
                        drawViewDepthContours();
                        updateMarineFeaturesSummary();
                    });
                }
                
                if (aidsToggle) {
                    aidsToggle.addEventListener('change', function(e) {
                        showViewNavigationAids = e.target.checked;
                        drawViewNavigationAids();
                        updateMarineFeaturesSummary();
                    });
                }
                
                if (routesToggle) {
                    routesToggle.addEventListener('change', function(e) {
                        showViewSeaRoutes = e.target.checked;
                        drawViewSeaRoutes();
                        updateMarineFeaturesSummary();
                    });
                }
            }
            
            window.initViewMap = initViewMap;
        </script>
    @endpush
</x-app-layout>

