@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="py-10">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white shadow rounded-lg p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Yacht Route Planner</h1>
                    <p class="text-sm text-gray-500">Build a voyage by adding stops, coordinates, and notes. Distance & legs update automatically when coordinates are provided.</p>
                </div>
                <button type="button" wire:click="save"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Route
                </button>
            </div>

            @if($alert)
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                    {{ $alert }}
                    @if($route)
                        <a href="{{ route('itinerary.routes.show', $route->id ?? null) }}" class="underline ml-2 text-green-800">View route</a>
                    @endif
                </div>
            @endif

            <div class="space-y-6">
                <div class="space-y-4">
                    {{-- Cover Image Upload --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cover Image</label>
                        <div class="mt-1 space-y-2">
                            @if($coverImage)
                                <div class="relative inline-block">
                                    <img src="{{ $coverImage->temporaryUrl() }}" alt="Cover preview" class="h-32 w-auto rounded-md object-cover">
                                    <button type="button" wire:click="removeCoverImage" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 text-xs hover:bg-red-600">
                                        ×
                                    </button>
                                </div>
                            @elseif($coverImagePath)
                                <div class="relative inline-block">
                                    <img src="{{ Storage::url($coverImagePath) }}" alt="Cover" class="h-32 w-auto rounded-md object-cover">
                                    <button type="button" wire:click="removeCoverImage" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 text-xs hover:bg-red-600">
                                        ×
                                    </button>
                                </div>
                            @endif
                            <input type="file" wire:model="coverImage" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            @error('coverImage') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            <p class="text-xs text-gray-500">Upload a cover image for your route (max 5MB)</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Route Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model.defer="form.title" 
                                placeholder="e.g., Mumbai to Goa Coastal Route"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-gray-400">
                            @error('form.title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea wire:model.defer="form.description" 
                                rows="3"
                                placeholder="Describe your route, highlights, and what makes it special..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-gray-400"></textarea>
                            @error('form.description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            <p class="text-xs text-gray-500 mt-1">Provide a brief description of your route (optional)</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Region</label>
                            <input type="text" wire:model.defer="form.region" 
                                placeholder="e.g., Indian Ocean, Mediterranean"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-gray-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Difficulty</label>
                            <input type="text" wire:model.defer="form.difficulty" 
                                placeholder="e.g., Easy, Moderate, Challenging"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-gray-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Season</label>
                            <input type="text" wire:model.defer="form.season" 
                                placeholder="e.g., Summer, Winter, Year-round"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-gray-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Visibility</label>
                            <select wire:model.defer="form.visibility" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($routeVisibility as $visibility)
                                    <option value="{{ $visibility->code }}">{{ $visibility->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select wire:model.defer="form.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($routeStatus as $status)
                                    <option value="{{ $status->code }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" wire:model.defer="form.start_date" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" wire:model.defer="form.end_date" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="space-y-4" wire:key="stops-list">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-800">Stops</h2>
                            <button type="button" 
                                wire:click="addStop"
                                wire:loading.attr="disabled"
                                wire:target="addStop"
                                class="inline-flex items-center px-3 py-2 bg-indigo-100 text-indigo-700 text-sm font-semibold rounded-md hover:bg-indigo-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="addStop">+ Add Stop</span>
                                <span wire:loading wire:target="addStop" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-indigo-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Adding...
                                </span>
                            </button>
                        </div>

                        {{-- Interactive Map --}}
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-4 py-3 border-b border-indigo-800">
                                <h3 class="text-lg font-semibold text-white flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                    </svg>
                                    Route Map
                                    <span class="ml-2 text-sm font-normal text-indigo-200">(Click on map to add/select stops)</span>
                                </h3>
                            </div>
                            <script>
                                // Define selectStopOnMap function early so it's available for buttons
                                window.selectStopOnMap = window.selectStopOnMap || function(index) {
                                    console.log('selectStopOnMap called with index:', index);
                                    // This is a placeholder - full function will be defined in scripts section
                                    if (typeof window.mapReady !== 'undefined' && !window.mapReady) {
                                        alert('Please wait for the map to finish loading.');
                                        return;
                                    }
                                };
                            </script>
                            <div class="relative" style="height: 500px;" id="route-map" wire:ignore>
                                <div class="absolute inset-0 flex items-center justify-center text-gray-500 text-sm z-10 bg-gray-50" id="map-loading" style="z-index: 10;" wire:ignore>
                                    <div class="text-center">
                                        <svg class="animate-spin h-10 w-10 text-indigo-600 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <p class="text-gray-700 font-medium">Loading map...</p>
                                    </div>
                                </div>
                                <script>
                                    // Immediate script to hide loading overlay when map canvas appears
                                    (function() {
                                        const mapContainer = document.getElementById('route-map');
                                        const loadingEl = document.getElementById('map-loading');
                                        
                                        function hideOverlay() {
                                            if (loadingEl) {
                                                loadingEl.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important; height: 0 !important; width: 0 !important; pointer-events: none !important; z-index: -9999 !important; position: absolute !important;';
                                                loadingEl.classList.add('hidden');
                                                loadingEl.setAttribute('hidden', 'true');
                                                loadingEl.innerHTML = '';
                                                try {
                                                    if (loadingEl.parentNode) {
                                                        loadingEl.remove();
                                                    }
                                                } catch(e) {}
                                                return true;
                                            }
                                            return false;
                                        }
                                        
                                        function checkAndHide() {
                                            if (mapContainer) {
                                                const canvas = mapContainer.querySelector('.mapboxgl-canvas');
                                                if (canvas && canvas.offsetWidth > 0) {
                                                    hideOverlay();
                                                    return true;
                                                }
                                            }
                                            return false;
                                        }
                                        
                                        // Check immediately
                                        if (checkAndHide()) return;
                                        
                                        // Check periodically
                                        const interval = setInterval(function() {
                                            if (checkAndHide()) {
                                                clearInterval(interval);
                                            }
                                        }, 100);
                                        
                                        // Stop checking after 10 seconds
                                        setTimeout(function() {
                                            clearInterval(interval);
                                            // Force hide anyway
                                            hideOverlay();
                                        }, 10000);
                                        
                                        // Also listen for clicks on map container
                                        if (mapContainer) {
                                            mapContainer.addEventListener('click', function() {
                                                hideOverlay();
                                            }, { once: true });
                                        }
                                    })();
                                </script>
                                <div class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-95 z-20 hidden" id="map-updating">
                                    <div class="text-center bg-white rounded-lg shadow-lg p-4">
                                        <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <p class="text-sm text-gray-700 font-medium">Updating route...</p>
                                    </div>
                                </div>
                                <!-- Map Legend -->
                                <div class="absolute top-4 right-4 z-30 bg-white rounded-lg shadow-lg p-3 border border-gray-200" id="map-legend" style="display: none;">
                                    <h4 class="text-xs font-semibold text-gray-700 mb-2">Route Information</h4>
                                    <div class="space-y-1 text-xs text-gray-600" id="legend-content">
                                        <p><span class="font-medium">Stops:</span> <span id="legend-stops">0</span></p>
                                        <p><span class="font-medium">Distance:</span> <span id="legend-distance">-</span></p>
                                    </div>
                                </div>
                                <!-- Selected Stop Indicator -->
                                <div class="absolute top-4 left-4 z-30 bg-white rounded-lg shadow-lg p-3 border border-indigo-300 hidden" id="selected-stop-indicator">
                                    <p class="text-xs font-semibold text-indigo-600 mb-1">Selected Stop</p>
                                    <p class="text-sm font-medium text-gray-800" id="selected-stop-name">-</p>
                                    <p class="text-xs text-gray-500 mt-1">Click on map to set coordinates</p>
                                </div>
                            </div>
                        </div>

                        <script>
                            // Define selectStopOnMap function early - MUST be before buttons
                            if (typeof window.selectStopOnMap === 'undefined') {
                                window.selectStopOnMap = function(index) {
                                    console.log('selectStopOnMap (early) called with index:', index);
                                    // Full implementation will be loaded from scripts section
                                    // This is just a placeholder to prevent errors
                                    if (window.mapReady !== undefined && !window.mapReady) {
                                        alert('Please wait for the map to finish loading.');
                                    }
                                };
                            }
                        </script>

                        @foreach($stops as $index => $stop)
                            <div wire:key="stop-{{ $index }}" class="border border-gray-200 rounded-lg p-4 bg-gray-50 space-y-3 stop-container" data-stop-index="{{ $index }}">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <button type="button" 
                                            class="select-stop-btn px-3 py-1 text-xs font-medium rounded-md border border-indigo-300 text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                            data-stop-index="{{ $index }}"
                                            onclick="event.preventDefault(); event.stopPropagation(); if(typeof window.selectStopOnMap === 'function') { window.selectStopOnMap({{ $index }}); } else { console.error('selectStopOnMap function not available'); } return false;"
                                            disabled>
                                            Select on Map
                                        </button>
                                        Stop {{ $index + 1 }}
                                    </h3>
                                    <button type="button" wire:click="removeStop({{ $index }})" class="text-red-500 text-sm hover:underline">Remove</button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div class="relative">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Stop Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                            id="stop-name-{{ $index }}"
                                            wire:model.defer="stops.{{ $index }}.name" 
                                            placeholder="e.g., Mumbai Port, Goa Marina"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-gray-400 stop-name-autocomplete"
                                            autocomplete="off">
                                        <div id="autocomplete-dropdown-{{ $index }}" class="autocomplete-dropdown hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto"></div>
                                        @error("stops.$index.name") <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Location Label</label>
                                        <input type="text" wire:model.defer="stops.{{ $index }}.location_label" 
                                            placeholder="e.g., Port, Marina, Anchorage"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-gray-400">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Latitude
                                            <span class="text-xs font-normal text-gray-500 ml-1">(-90 to 90)</span>
                                        </label>
                                        <input type="text" 
                                            id="stop-latitude-{{ $index }}"
                                            wire:model.defer="stops.{{ $index }}.latitude" 
                                            placeholder="e.g., 19.07598 (Mumbai)"
                                            class="stop-coordinate-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-gray-400"
                                            autocomplete="off"
                                            inputmode="decimal">
                                        <p class="text-xs text-gray-500 mt-1">Examples: Mumbai (19.07598), Goa (15.2993), Monaco (43.7384)</p>
                                        @error("stops.$index.latitude") <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Longitude
                                            <span class="text-xs font-normal text-gray-500 ml-1">(-180 to 180)</span>
                                        </label>
                                        <input type="text" 
                                            id="stop-longitude-{{ $index }}"
                                            wire:model.defer="stops.{{ $index }}.longitude" 
                                            placeholder="e.g., 72.87766 (Mumbai)"
                                            class="stop-coordinate-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-gray-400"
                                            autocomplete="off"
                                            inputmode="decimal">
                                        <p class="text-xs text-gray-500 mt-1">Examples: Mumbai (72.87766), Goa (74.1240), Monaco (7.4246)</p>
                                        @error("stops.$index.longitude") <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Stay Duration (hours)</label>
                                        <input type="number" wire:model.defer="stops.{{ $index }}.stay_duration_hours" 
                                            placeholder="e.g., 24, 48, 72"
                                            min="0"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-gray-400">
                                        <p class="text-xs text-gray-500 mt-1">How long you plan to stay at this stop</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                        <textarea wire:model.defer="stops.{{ $index }}.notes" rows="2" 
                                            placeholder="Add any notes about this stop..."
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-gray-400"></textarea>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Photos</label>
                                        <div class="space-y-2">
                                            <input type="file" 
                                                wire:model.live="stopPhotos.{{ $index }}" 
                                                accept="image/*" 
                                                multiple
                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                            @error("stopPhotos.{$index}") <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                            @if(session()->has('upload_error'))
                                                <p class="text-xs text-red-600 mt-1">{{ session('upload_error') }}</p>
                                            @endif
                                            
                                            @if(isset($stops[$index]['photos']) && is_array($stops[$index]['photos']) && count($stops[$index]['photos']) > 0)
                                                <div class="grid grid-cols-4 gap-2 mt-2">
                                                    @foreach($stops[$index]['photos'] as $photoIndex => $photo)
                                                        <div class="relative group">
                                                            <img src="{{ Storage::url($photo) }}" alt="Photo {{ $photoIndex + 1 }}" class="w-full h-20 object-cover rounded-md border border-gray-200">
                                                            <button type="button" 
                                                                wire:click="removeStopPhoto({{ $index }}, {{ $photoIndex }})"
                                                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 text-xs opacity-0 group-hover:opacity-100 hover:bg-red-600 transition-opacity">
                                                                ×
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <p class="text-xs text-gray-500">Upload multiple photos for this stop</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        #route-map {
            z-index: 1;
            height: 100%;
            width: 100%;
            min-height: 500px;
            border-radius: 0 0 0.5rem 0.5rem;
            position: relative;
        }
        
        #route-map .mapboxgl-map {
            height: 100% !important;
            width: 100% !important;
        }
        
        /* Force hide loading overlay when map canvas exists */
        #route-map:has(.mapboxgl-canvas) #map-loading {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            width: 0 !important;
            pointer-events: none !important;
        }
        
        /* Also hide loading overlay when it has the hidden class - HIGH PRIORITY */
        #map-loading.hidden,
        #map-loading[hidden],
        #map-loading[style*="display: none"],
        #route-map #map-loading.hidden {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            width: 0 !important;
            pointer-events: none !important;
            z-index: -9999 !important;
            position: absolute !important;
        }
        
        /* Ultra-aggressive: Hide if map canvas exists anywhere */
        #route-map:has(.mapboxgl-canvas) #map-loading,
        #route-map .mapboxgl-canvas ~ #map-loading {
            display: none !important;
            visibility: hidden !important;
        }
        
        /* Force hide loading overlay if map is ready - ULTRA AGGRESSIVE */
        #route-map:has(.mapboxgl-canvas) #map-loading,
        body:has(.mapboxgl-canvas) #map-loading,
        #route-map:has(.mapboxgl-canvas) > #map-loading {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            width: 0 !important;
            pointer-events: none !important;
            z-index: -9999 !important;
            position: absolute !important;
        }
        
        /* Hide loading overlay permanently once map canvas exists */
        #route-map .mapboxgl-canvas ~ #map-loading,
        #route-map:has(.mapboxgl-canvas) > #map-loading,
        #route-map:has(.mapboxgl-canvas) #map-loading {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            width: 0 !important;
        }
        
        /* Hide overlay if mapReady class exists on body */
        body.map-ready #map-loading,
        body[data-map-ready="true"] #map-loading {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
        }
        
        /* Mapbox GL JS Custom Styling */
        .mapboxgl-ctrl {
            font-family: inherit;
        }
        
        .mapboxgl-marker {
            cursor: pointer;
        }
        
        .custom-stop-marker {
            background: transparent;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.4);
            border: 3px solid white;
        }
        
        .route-arrow {
            background: transparent;
            border: none;
        }
        
        /* Custom Map Controls */
        .map-controls {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .map-control-button {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 8px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.2s;
        }
        
        .map-control-button:hover {
            background: #f9fafb;
            box-shadow: 0 4px 6px rgba(0,0,0,0.15);
        }
        
        .map-control-button svg {
            width: 20px;
            height: 20px;
            color: #4f46e5;
        }
        .autocomplete-dropdown {
            z-index: 1000;
        }
        .autocomplete-item {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f3f4f6;
            transition: background-color 0.2s;
        }
        .autocomplete-item:hover,
        .autocomplete-item.active {
            background-color: #f3f4f6;
        }
        .autocomplete-item:last-child {
            border-bottom: none;
        }
        .autocomplete-item-name {
            font-weight: 500;
            color: #111827;
            margin-bottom: 2px;
        }
        .autocomplete-item-details {
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .stop-container {
            transition: all 0.2s;
        }
        
        .stop-container.ring-2 {
            border-color: #4f46e5;
        }
        
        .select-stop-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed !important;
        }
        
        .select-stop-btn:not(:disabled):hover {
            background-color: #e0e7ff;
        }
    </style>
    <!-- Mapbox GL JS CSS -->
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.css" rel="stylesheet">
@endpush

@push('scripts')
    <!-- Mapbox GL JS -->
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.js"></script>
    <script>
        // Immediate check to verify Mapbox loaded and trigger initialization
        function triggerMapInitWhenReady() {
            console.log('[MAP] triggerMapInitWhenReady called');
            if (typeof mapboxgl === 'undefined') {
                console.log('[MAP] Mapbox not ready yet, will retry...');
                setTimeout(triggerMapInitWhenReady, 200);
                return;
            }
            
            console.log('[MAP] ✓ Mapbox GL JS is available');
            
            // Wait for functions to be defined, then trigger
            function tryInit() {
                if (typeof window.checkAndInit === 'function') {
                    console.log('[MAP] Calling checkAndInit...');
                    window.checkAndInit();
                } else if (typeof window.initPlannerMap === 'function') {
                    console.log('[MAP] Calling initPlannerMap...');
                    window.initPlannerMap();
                } else {
                    console.log('[MAP] Functions not ready yet, retrying...');
                    setTimeout(tryInit, 200);
                }
            }
            
            setTimeout(tryInit, 300);
        }
        
        window.addEventListener('load', function() {
            console.log('[MAP] Page loaded, checking Mapbox availability...');
            console.log('[MAP] typeof mapboxgl:', typeof mapboxgl);
            triggerMapInitWhenReady();
        });
        
        // Also try immediately if DOM is ready
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            console.log('[MAP] DOM already ready, checking for Mapbox...');
            triggerMapInitWhenReady();
        }
    </script>
    <script>
        // Global variables for map (outside IIFE, like route-show)
        let plannerMap = null;
        let plannerMarkers = [];
        let plannerPolyline = null;
        let selectedStopIndex = null;
        let clickMarker = null;
        
        // Global function to aggressively hide loading overlay - callable from anywhere
        window.forceHideMapLoading = function() {
            const loadingEl = document.getElementById('map-loading');
            if (loadingEl) {
                console.log('[MAP] forceHideMapLoading called - hiding overlay');
                // Multiple aggressive methods - use !important inline styles
                loadingEl.style.setProperty('display', 'none', 'important');
                loadingEl.style.setProperty('visibility', 'hidden', 'important');
                loadingEl.style.setProperty('opacity', '0', 'important');
                loadingEl.style.setProperty('height', '0', 'important');
                loadingEl.style.setProperty('width', '0', 'important');
                loadingEl.style.setProperty('pointer-events', 'none', 'important');
                loadingEl.style.setProperty('z-index', '-9999', 'important');
                loadingEl.style.setProperty('position', 'absolute', 'important');
                loadingEl.classList.add('hidden');
                loadingEl.setAttribute('hidden', 'true');
                loadingEl.setAttribute('aria-hidden', 'true');
                loadingEl.innerHTML = '';
                // Try removing from DOM
                try {
                    if (loadingEl.parentNode) {
                        loadingEl.remove();
                    }
                } catch(e) {
                    console.warn('[MAP] Could not remove element:', e);
                }
                return true;
            }
            return false;
        };
        
        // Also hide loading overlay when map canvas is detected - make it globally accessible
        window.checkAndHideLoading = function() {
            const mapContainer = document.getElementById('route-map');
            const loadingEl = document.getElementById('map-loading');
            
            if (!mapContainer) return false;
            
            // Check if map canvas exists and is visible
            const canvas = mapContainer.querySelector('.mapboxgl-canvas');
            if (canvas && canvas.offsetWidth > 0 && canvas.offsetHeight > 0) {
                console.log('[MAP] Canvas detected, hiding loading overlay');
                window.forceHideMapLoading();
                return true;
            }
            
            // Also check if plannerMap exists and is ready
            if (typeof mapReady !== 'undefined' && mapReady && typeof plannerMap !== 'undefined' && plannerMap) {
                console.log('[MAP] Map is ready, hiding loading overlay');
                window.forceHideMapLoading();
                return true;
            }
            
            return false;
        };
        
        const checkAndHideLoading = window.checkAndHideLoading; // Local reference
        
        // Run check periodically until overlay is hidden
        const loadingCheckInterval = setInterval(() => {
            const loadingEl = document.getElementById('map-loading');
            if (loadingEl) {
                // Check multiple conditions to see if overlay should be hidden
                const isHidden = loadingEl.style.display === 'none' || 
                               loadingEl.classList.contains('hidden') ||
                               loadingEl.hasAttribute('hidden') ||
                               loadingEl.offsetHeight === 0 ||
                               loadingEl.style.visibility === 'hidden';
                
                if (!isHidden) {
                    const hidden = checkAndHideLoading();
                    if (hidden) {
                        clearInterval(loadingCheckInterval);
                    }
                } else {
                    clearInterval(loadingCheckInterval);
                }
            } else {
                // Element doesn't exist, stop checking
                clearInterval(loadingCheckInterval);
            }
        }, 300); // Check more frequently
        
        // Stop checking after 30 seconds
        setTimeout(() => clearInterval(loadingCheckInterval), 30000);
        
        // Periodic check to ensure buttons stay enabled if map is ready
        const buttonEnableCheckInterval = setInterval(() => {
            if (typeof mapReady !== 'undefined' && mapReady && typeof plannerMap !== 'undefined' && plannerMap) {
                document.querySelectorAll('.select-stop-btn').forEach(btn => {
                    // Only enable if it's currently disabled but map is ready
                    if (btn.disabled || btn.hasAttribute('disabled')) {
                        btn.disabled = false;
                        btn.removeAttribute('disabled');
                        btn.style.cursor = 'pointer';
                        btn.style.opacity = '1';
                        btn.style.pointerEvents = 'auto';
                        btn.title = 'Click to select this stop on the map';
                    }
                });
            }
        }, 1000); // Check every second
        
        // Stop checking after 5 minutes (should be enough time)
        setTimeout(() => clearInterval(buttonEnableCheckInterval), 300000);
        
        // MutationObserver to watch for overlay being added/recreated and immediately hide it
        const overlayObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            // Check if the added node is the loading overlay
                            if (node.id === 'map-loading' || (node.querySelector && node.querySelector('#map-loading'))) {
                                console.log('[MAP] Loading overlay detected via MutationObserver, hiding immediately');
                                // Hide immediately, no timeout
                                window.forceHideMapLoading();
                                window.checkAndHideLoading();
                                // Also add CSS class to ensure it stays hidden
                                const el = node.id === 'map-loading' ? node : node.querySelector('#map-loading');
                                if (el) {
                                    el.style.setProperty('display', 'none', 'important');
                                    el.style.setProperty('visibility', 'hidden', 'important');
                                }
                            }
                            // Also check if any child is the loading overlay
                            const loadingEl = node.querySelector ? node.querySelector('#map-loading') : null;
                            if (loadingEl) {
                                console.log('[MAP] Loading overlay child detected, hiding immediately');
                                window.forceHideMapLoading();
                                window.checkAndHideLoading();
                                loadingEl.style.setProperty('display', 'none', 'important');
                                loadingEl.style.setProperty('visibility', 'hidden', 'important');
                            }
                        }
                    });
                }
                // Also check for attribute changes that might make it visible
                if (mutation.type === 'attributes' && mutation.target.id === 'map-loading') {
                    const el = mutation.target;
                    const isVisible = el.style.display !== 'none' && 
                                     !el.classList.contains('hidden') && 
                                     !el.hasAttribute('hidden') &&
                                     el.offsetHeight > 0;
                    if (isVisible) {
                        console.log('[MAP] Loading overlay became visible, hiding immediately');
                        window.forceHideMapLoading();
                        window.checkAndHideLoading();
                    }
                }
            });
        });
        
        // Start observing the map container for changes
        const mapContainer = document.getElementById('route-map');
        if (mapContainer) {
            overlayObserver.observe(mapContainer, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['style', 'class', 'hidden']
            });
            console.log('[MAP] MutationObserver started to watch for loading overlay');
        }
        
        // Also observe the document body in case overlay is added elsewhere
        overlayObserver.observe(document.body, {
            childList: true,
            subtree: true
        });
        
            // Immediately hide overlay if map is already ready
            setTimeout(() => {
                if (typeof mapReady !== 'undefined' && mapReady) {
                    console.log('[MAP] Map already ready, hiding overlay immediately');
                    window.forceHideMapLoading();
                    window.checkAndHideLoading();
                    // Also refresh stops if map is ready
                    if (typeof window.refreshMapStops === 'function') {
                        setTimeout(() => window.refreshMapStops(), 500);
                    }
                }
            }, 100);
            
            // Also refresh stops after a delay when page loads (for editing routes) - multiple attempts
            const refreshStopsAttempts = [1000, 2000, 3000, 5000];
            refreshStopsAttempts.forEach((delay, index) => {
                setTimeout(() => {
                    if (typeof window.refreshMapStops === 'function' && typeof mapReady !== 'undefined' && mapReady) {
                        console.log('[MAP] Auto-refreshing stops on page load (attempt', index + 1, ')');
                        window.refreshMapStops();
                    }
                }, delay);
            });
        
        // Define updateStopCoordinates globally EARLY so it's available when map initializes
        window.updateStopCoordinates = window.updateStopCoordinates || function(index, lat, lng) {
            console.log('[STOP] updateStopCoordinates called for stop', index, 'lat:', lat, 'lng:', lng);
            
            // Mark this as a manual map coordinate update to prevent duplicate markers
            if (typeof window !== 'undefined') {
                window.skipLivewireRedraw = true;
                window.lastMapCoordinateUpdate = {
                    index: index,
                    timestamp: Date.now(),
                    lat: lat,
                    lng: lng
                };
            }
            
            // Hide loading overlay immediately
            if (typeof window.hideMapUpdating === 'function') {
                window.hideMapUpdating();
            }
            
            const latInput = document.getElementById('stop-latitude-' + index);
            const lngInput = document.getElementById('stop-longitude-' + index);
            const stopNameInput = document.getElementById('stop-name-' + index);
            
            console.log('[STOP] Input elements found - latInput:', !!latInput, 'lngInput:', !!lngInput, 'nameInput:', !!stopNameInput);
            
            if (latInput && lngInput) {
                // Update input values first
                const latValue = lat.toFixed(6);
                const lngValue = lng.toFixed(6);
                
                console.log('[STOP] Setting values - Latitude:', latValue, 'Longitude:', lngValue);
                
                latInput.value = latValue;
                lngInput.value = lngValue;
                
                // Trigger Livewire updates - use $wire API for immediate updates
                if (typeof Livewire !== 'undefined' && typeof window.Livewire !== 'undefined') {
                    try {
                        // Find the Livewire component
                        const component = Livewire.find(latInput.closest('[wire\\:id]')?.getAttribute('wire:id') || 
                            document.querySelector('[wire\\:id]')?.getAttribute('wire:id'));
                        
                        if (component) {
                            // Use Livewire's $wire API to update values directly
                            component.set(`stops.${index}.latitude`, latValue);
                            component.set(`stops.${index}.longitude`, lngValue);
                            console.log('[STOP] ✓ Livewire values updated via $wire API');
                        } else {
                            // Fallback: trigger events
                            latInput.dispatchEvent(new Event('input', { bubbles: true }));
                            latInput.dispatchEvent(new Event('change', { bubbles: true }));
                            lngInput.dispatchEvent(new Event('input', { bubbles: true }));
                            lngInput.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    } catch (error) {
                        console.warn('[STOP] Livewire update failed, using event fallback:', error);
                        // Fallback: trigger events
                        latInput.dispatchEvent(new Event('input', { bubbles: true }));
                        latInput.dispatchEvent(new Event('change', { bubbles: true }));
                        lngInput.dispatchEvent(new Event('input', { bubbles: true }));
                        lngInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                } else {
                    // Fallback if Livewire not available
                    latInput.dispatchEvent(new Event('input', { bubbles: true }));
                    latInput.dispatchEvent(new Event('change', { bubbles: true }));
                    lngInput.dispatchEvent(new Event('input', { bubbles: true }));
                    lngInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                
                console.log('[STOP] Input events dispatched');
                
                // If marker update is called, map is definitely loaded - hide loading overlay NOW
                console.log('[STOP] Map is functional - hiding loading overlay');
                if (typeof window.forceHideMapLoading === 'function') {
                    window.forceHideMapLoading();
                }
                // Also trigger the periodic check
                if (typeof checkAndHideLoading === 'function') {
                    checkAndHideLoading();
                }
                
                // Update marker immediately (visual feedback)
                if (typeof window.updateMarkerForStop === 'function') {
                    console.log('[STOP] Updating marker on map...');
                    window.updateMarkerForStop(index, lat, lng);
                } else if (typeof updateMarkerForStop === 'function') {
                    updateMarkerForStop(index, lat, lng);
                } else {
                    console.warn('[STOP] updateMarkerForStop function not found');
                }
                
                // Reverse geocode to get address name automatically
                console.log('[STOP] Starting reverse geocoding for lat:', lat, 'lng:', lng);
                
                // Wait a moment for inputs to be ready, then reverse geocode
                setTimeout(() => {
                    const nameInput = document.getElementById('stop-name-' + index);
                    console.log('[STOP] Reverse geocoding - nameInput found:', !!nameInput);
                    
                    if (typeof window.reverseGeocode === 'function') {
                        console.log('[STOP] Calling reverseGeocode...');
                        window.reverseGeocode(lat, lng).then((address) => {
                            console.log('[STOP] Reverse geocoding result:', address);
                            if (address && nameInput) {
                                // Auto-fill the stop name
                                nameInput.value = address;
                                
                                // Trigger Livewire updates for name using $wire API
                                if (typeof Livewire !== 'undefined' && typeof window.Livewire !== 'undefined') {
                                    try {
                                        // Find the Livewire component
                                        const component = Livewire.find(nameInput.closest('[wire\\:id]')?.getAttribute('wire:id') || 
                                            document.querySelector('[wire\\:id]')?.getAttribute('wire:id'));
                                        
                                        if (component) {
                                            // Use Livewire's $wire API to update name directly
                                            component.set(`stops.${index}.name`, address);
                                            console.log('[STOP] ✓ Stop name updated via Livewire $wire API:', address);
                                        } else {
                                            // Fallback: trigger events
                                            nameInput.dispatchEvent(new Event('input', { bubbles: true }));
                                            nameInput.dispatchEvent(new Event('change', { bubbles: true }));
                                        }
                                    } catch (error) {
                                        console.warn('[STOP] Livewire name update failed, using event fallback:', error);
                                        // Fallback: trigger events
                                        nameInput.dispatchEvent(new Event('input', { bubbles: true }));
                                        nameInput.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                } else {
                                    // Fallback if Livewire not available
                                    nameInput.dispatchEvent(new Event('input', { bubbles: true }));
                                    nameInput.dispatchEvent(new Event('change', { bubbles: true }));
                                }
                                
                                console.log('[STOP] ✓ Stop name auto-filled:', address);
                            } else {
                                if (!address && nameInput) {
                                    // For ocean/remote locations where Nominatim returns empty, provide a default name
                                    const defaultName = `Stop ${index + 1} (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
                                    nameInput.value = defaultName;
                                    
                                    // Trigger Livewire updates
                                    if (typeof Livewire !== 'undefined' && typeof window.Livewire !== 'undefined') {
                                        try {
                                            const component = Livewire.find(nameInput.closest('[wire\\:id]')?.getAttribute('wire:id') || 
                                                document.querySelector('[wire\\:id]')?.getAttribute('wire:id'));
                                            if (component) {
                                                component.set(`stops.${index}.name`, defaultName);
                                            }
                                        } catch (error) {
                                            // Fallback to events
                                            nameInput.dispatchEvent(new Event('input', { bubbles: true }));
                                            nameInput.dispatchEvent(new Event('change', { bubbles: true }));
                                        }
                                    } else {
                                        nameInput.dispatchEvent(new Event('input', { bubbles: true }));
                                        nameInput.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                    console.log('[STOP] ✓ Using default name for ocean/remote location:', defaultName);
                                } else {
                                    console.log('[STOP] No address returned or nameInput not found. address:', address, 'nameInput:', !!nameInput);
                                }
                            }
                        }).catch(error => {
                            console.error('[STOP] Error in reverse geocoding:', error);
                        });
                    } else {
                        console.error('[STOP] window.reverseGeocode function not found!');
                    }
                }, 200);
                
                // Clear skip flag after a delay to allow future Livewire updates
                setTimeout(() => {
                    if (typeof window !== 'undefined') {
                        window.skipLivewireRedraw = false;
                    }
                }, 3000);
            } else {
                console.error('[STOP] ✗ Input elements not found for stop', index);
            }
        };
        
        console.log('[STOP] ✓ updateStopCoordinates function registered globally (early)');
        
        // Color array for different stops - define EARLY before updateMarkerForStop
        const stopColors = [
            '#10b981', // Stop 1 - Green
            '#3b82f6', // Stop 2 - Blue
            '#f59e0b', // Stop 3 - Orange
            '#ef4444', // Stop 4 - Red
            '#8b5cf6', // Stop 5 - Purple
            '#ec4899', // Stop 6 - Pink
            '#06b6d4', // Stop 7 - Cyan
            '#84cc16', // Stop 8 - Lime
            '#f97316', // Stop 9 - Orange-red
            '#6366f1'  // Stop 10 - Indigo
        ];
        
        // Function to get color for a stop index - define EARLY before updateMarkerForStop
        function getStopColor(index) {
            return stopColors[index % stopColors.length];
        }
        
        // Define reverseGeocode globally EARLY so it's available when updateStopCoordinates calls it
        window.fetchNominatimReverse = window.fetchNominatimReverse || async function(lat, lng) {
            try {
                console.log('[GEOCODE] Fetching from Nominatim for lat:', lat, 'lng:', lng);
                const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1&zoom=18`;
                const response = await fetch(url, {
                    headers: {
                        'User-Agent': 'YachtWorkersCouncil/1.0',
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    const address = data.display_name || '';
                    console.log('[GEOCODE] ✓ Nominatim result:', address);
                    return address;
                } else {
                    console.warn('[GEOCODE] Nominatim request failed with status:', response.status);
                }
            } catch (error) {
                console.error('[GEOCODE] ✗ Reverse geocoding error:', error);
            }
            return '';
        };
        
        window.reverseGeocode = window.reverseGeocode || async function(lat, lng) {
            console.log('[GEOCODE] reverseGeocode called:', lat, lng);
            if (typeof window.fetchNominatimReverse === 'function') {
                return await window.fetchNominatimReverse(lat, lng);
            }
            return '';
        };
        
        console.log('[GEOCODE] ✓ reverseGeocode function registered globally (early)');
        
        // Define updateMarkerForStop globally EARLY - it will use plannerMap and plannerMarkers which are global
        window.updateMarkerForStop = window.updateMarkerForStop || function(index, lat, lng) {
            console.log('[MARKER] updateMarkerForStop called for index:', index, 'lat:', lat, 'lng:', lng);
            
            if (!plannerMap || typeof mapboxgl === 'undefined') {
                console.warn('[MARKER] Map not initialized yet or Mapbox not available');
                return;
            }
            
            // Remove existing marker for this stop if any
            if (plannerMarkers[index] && plannerMarkers[index] !== null) {
                try {
                    plannerMarkers[index].remove();
                } catch(e) {
                    console.error('[MARKER] Error removing old marker:', e);
                }
                plannerMarkers[index] = null;
            }
            
            // Get stop color
            const markerColor = stopColors[index % stopColors.length];
            const stopNumber = index + 1;
            
            // Get stop name from input
            const stopNameInput = document.getElementById('stop-name-' + index);
            const stopName = stopNameInput ? stopNameInput.value : `Stop ${stopNumber}`;
            
            // Create popup content
            const container = document.querySelector(`.stop-container[data-stop-index="${index}"]`);
            const stayDuration = container ? 
                (container.querySelector('input[wire\\:model*="stay_duration_hours"]')?.value || 'Not specified') + ' hours' : 
                'Not specified';
            const locationLabel = container ? 
                (container.querySelector('input[wire\\:model*="location_label"]')?.value || '') : '';
            const notes = container ? 
                (container.querySelector('textarea[wire\\:model*="notes"]')?.value || '') : '';
            
            const popupContent = `
                <div style="padding: 8px; min-width: 200px;">
                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                        <div style="width: 12px; height: 12px; background: ${markerColor}; border-radius: 50%; margin-right: 8px;"></div>
                        <strong style="font-size: 14px; color: #111827;">${stopName}</strong>
                    </div>
                    ${locationLabel ? `<div style="font-size: 12px; color: #4b5563; margin-bottom: 4px;">📍 ${locationLabel}</div>` : ''}
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">⏱️ Stay: ${stayDuration}</div>
                    ${notes ? `<div style="font-size: 12px; color: #4b5563; margin-top: 8px; padding-top: 8px; border-top: 1px solid #e5e7eb;">${notes}</div>` : ''}
                    <div style="font-size: 11px; color: #9ca3af; margin-top: 8px;">Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}</div>
                </div>
            `;
            
            // Create custom marker element
            const el = document.createElement('div');
            el.className = 'custom-stop-marker';
            el.style.backgroundColor = markerColor;
            el.style.width = '35px';
            el.style.height = '35px';
            el.style.borderRadius = '50%';
            el.style.border = '3px solid white';
            el.style.display = 'flex';
            el.style.alignItems = 'center';
            el.style.justifyContent = 'center';
            el.style.color = 'white';
            el.style.fontWeight = 'bold';
            el.style.fontSize = '16px';
            el.style.boxShadow = '0 2px 8px rgba(0,0,0,0.4)';
            el.style.cursor = 'pointer';
            el.textContent = stopNumber;
            
            // Create Mapbox marker
            const marker = new mapboxgl.Marker(el)
                .setLngLat([lng, lat])
                .setPopup(new mapboxgl.Popup({ offset: 25 }).setHTML(popupContent))
                .addTo(plannerMap);
            
            // Store marker in array
            while (plannerMarkers.length <= index) {
                plannerMarkers.push(null);
            }
            plannerMarkers[index] = marker;
            marker.index = index; // Store index for identification
            
            // Update polyline
            if (typeof window.updatePolyline === 'function') {
                window.updatePolyline();
            }
            
            console.log('[MARKER] ✓ Marker updated for stop', index);
        };
        
        console.log('[MARKER] ✓ updateMarkerForStop function registered globally (early)');
        
        let mapReady = false;
        
        // stopColors and getStopColor are already defined above (before updateMarkerForStop)
        // No need to redefine them here
        
        // Define selectStopOnMap function IMMEDIATELY so it's available for buttons
        window.selectStopOnMap = function(index) {
            console.log('selectStopOnMap called with index:', index);
            
            // Check if map is ready
            if (!mapReady || !plannerMap) {
                console.log('Map is not ready yet. mapReady:', mapReady, 'plannerMap:', !!plannerMap);
                alert('Please wait for the map to finish loading.');
                return;
            }
            
            // Validate index
            if (isNaN(index) && index !== 0) {
                console.error('Invalid stop index:', index);
                return;
            }
            
            // Remove ALL previous markers for this stop index (Mapbox)
            if (plannerMarkers[index] && plannerMarkers[index] !== null) {
                const oldMarker = plannerMarkers[index];
                try {
                    if (oldMarker && oldMarker.remove) {
                        oldMarker.remove(); // Mapbox marker removal
                    }
                } catch(e) {
                    console.error('Error removing marker:', e);
                }
            }
            plannerMarkers[index] = null;
            
            // Update polyline after removing marker
            if (typeof updatePolyline === 'function') {
                updatePolyline();
            }
            
            // Clear existing coordinates
            const latInput = document.getElementById('stop-latitude-' + index);
            const lngInput = document.getElementById('stop-longitude-' + index);
            if (latInput) {
                latInput.value = '';
                latInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
            if (lngInput) {
                lngInput.value = '';
                lngInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
            
            selectedStopIndex = index;
        
            // Update UI
            document.querySelectorAll('.stop-container').forEach(container => {
                container.classList.remove('ring-2', 'ring-indigo-500');
            });
            
            const selectedContainer = document.querySelector(`.stop-container[data-stop-index="${index}"]`);
            if (selectedContainer) {
                selectedContainer.classList.add('ring-2', 'ring-indigo-500');
            }
            
            // Show selected stop indicator
            const indicator = document.getElementById('selected-stop-indicator');
            const stopNameEl = document.getElementById('selected-stop-name');
            if (indicator && stopNameEl) {
                const stopNameInput = document.getElementById('stop-name-' + index);
                const stopName = stopNameInput ? stopNameInput.value : `Stop ${index + 1}`;
                stopNameEl.textContent = stopName || `Stop ${index + 1}`;
                
                const stopColor = getStopColor(index);
                indicator.style.borderColor = stopColor;
                indicator.classList.remove('hidden');
            }
            
            // Update button text
            document.querySelectorAll('.select-stop-btn').forEach(btn => {
                btn.textContent = 'Select on Map';
                btn.classList.remove('bg-indigo-600', 'text-white');
                btn.classList.add('bg-indigo-50', 'text-indigo-700');
            });
            
            const button = document.querySelector(`.select-stop-btn[data-stop-index="${index}"]`);
            if (button) {
                button.textContent = 'Selected - Click Map';
                button.classList.remove('bg-indigo-50', 'text-indigo-700');
                button.classList.add('bg-indigo-600', 'text-white');
            }
        };
        
        // Define drawPlannerStops as a working implementation (not just a stub)
        // This will be the actual implementation that draws stops on the map
        window.drawPlannerStops = window.drawPlannerStops || function(stops = [], showLoading = true) {
            console.log('[MAP] ========================================');
            console.log('[MAP] ===== drawPlannerStops CALLED =====');
            console.log('[MAP] ========================================');
            console.log('[MAP] Stops count:', stops.length);
            console.log('[MAP] Stops data:', JSON.stringify(stops, null, 2));
            console.log('[MAP] showLoading:', showLoading);
            console.log('[MAP] plannerMap exists:', !!plannerMap);
            console.log('[MAP] plannerMap type:', typeof plannerMap);
            console.log('[MAP] mapboxgl exists:', typeof mapboxgl !== 'undefined');
            
            if (!plannerMap || typeof mapboxgl === 'undefined') {
                console.warn('[MAP] ✗ Map or Mapbox GL JS not initialized, cannot draw stops');
                console.warn('[MAP] plannerMap:', plannerMap);
                console.warn('[MAP] mapboxgl:', typeof mapboxgl);
                // Retry after a short delay if map is not ready
                if (typeof plannerMap === 'undefined' || !plannerMap) {
                    setTimeout(() => {
                        if (plannerMap && typeof mapboxgl !== 'undefined' && stops.length > 0) {
                            console.log('[MAP] Retrying to draw stops after map initialization');
                            window.drawPlannerStops(stops, showLoading);
                        }
                    }, 1000);
                }
                return;
            }
            
            // Check if map is loaded
            try {
                const isLoaded = plannerMap.loaded();
                console.log('[MAP] Map loaded status:', isLoaded);
                if (!isLoaded) {
                    console.warn('[MAP] Map not loaded yet, waiting for load event');
                    plannerMap.once('load', () => {
                        console.log('[MAP] Map loaded, retrying drawPlannerStops');
                        window.drawPlannerStops(stops, showLoading);
                    });
                    return;
                }
            } catch (loadError) {
                console.error('[MAP] Error checking map loaded status:', loadError);
                // Continue anyway - map might be ready
            }

            // Skip redrawing if we just manually updated coordinates via map click
            if (typeof window !== 'undefined' && window.skipLivewireRedraw) {
                console.log('[MAP] Skipping drawPlannerStops - manual coordinate update in progress');
                return;
            }
            
            console.log('[MAP] ✓ All checks passed, proceeding to draw stops');

            // Helper functions for showing/hiding map updating indicator
            function showMapUpdating() {
                const updatingEl = document.getElementById('map-updating');
                if (updatingEl) {
                    updatingEl.classList.remove('hidden');
                }
            }

            function hideMapUpdating() {
                const updatingEl = document.getElementById('map-updating');
                if (updatingEl) {
                    updatingEl.classList.add('hidden');
                }
            }

            if (showLoading) {
                showMapUpdating();
            }

            setTimeout(() => {
                // Remove ALL existing markers completely before creating new ones
                plannerMarkers.forEach((marker, idx) => {
                    if (marker) {
                        try {
                            marker.remove();
                        } catch(e) {
                            console.error('Error removing marker:', e);
                        }
                    }
                    plannerMarkers[idx] = null;
                });
                plannerMarkers = [];
                
                // Remove existing polyline
                if (plannerMap.getSource('route-line')) {
                    if (plannerMap.getLayer('route-line')) {
                        plannerMap.removeLayer('route-line');
                    }
                    plannerMap.removeSource('route-line');
                    plannerPolyline = null;
                }

                const coordinates = [];
                const distances = [];
                
                // Helper function to calculate distance between two coordinates (Haversine formula)
                function calculateDistance(lat1, lng1, lat2, lng2) {
                    const R = 6371000; // Earth radius in meters
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLng = (lng2 - lng1) * Math.PI / 180;
                    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                            Math.sin(dLng/2) * Math.sin(dLng/2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                    return R * c; // Distance in meters
                }
                
                console.log('[MAP] Processing', stops.length, 'stops to draw markers');
                let validStopsCount = 0;
                
                stops.forEach((stop, index) => {
                    console.log('[MAP] Processing stop', index, ':', stop);
                    
                    if (stop.latitude && stop.longitude) {
                        const lat = parseFloat(stop.latitude);
                        const lng = parseFloat(stop.longitude);
                        console.log('[MAP] Stop', index, '- Parsed coordinates: lat=', lat, 'lng=', lng);
                        
                        if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                            validStopsCount++;
                            console.log('[MAP] ✓ Stop', index, 'has valid coordinates, creating marker');
                            
                            // Calculate distance from previous stop (nautical miles)
                            let distanceText = '';
                            if (coordinates.length > 0) {
                                const prevPosition = coordinates[coordinates.length - 1];
                                // prevPosition is [lng, lat] in Mapbox format, calculateDistance expects (lat, lng)
                                const distanceMeters = calculateDistance(prevPosition[1], prevPosition[0], lat, lng);
                                const distanceNm = (distanceMeters / 1852).toFixed(2); // Convert to nautical miles
                                distances.push(parseFloat(distanceNm));
                                distanceText = `<div style="font-size: 12px; color: #6b7280; margin-top: 4px;">Distance from previous: ${distanceNm} NM</div>`;
                            }
                            
                            // Create Mapbox marker with custom icon
                            const stopName = stop.name || 'Stop ' + (index + 1);
                            const markerColor = getStopColor(index);
                            const stopNumber = index + 1;
                            
                            // Create custom marker element
                            const el = document.createElement('div');
                            el.className = 'custom-stop-marker';
                            el.style.backgroundColor = markerColor;
                            el.style.width = '35px';
                            el.style.height = '35px';
                            el.style.borderRadius = '50%';
                            el.style.border = '3px solid white';
                            el.style.display = 'flex';
                            el.style.alignItems = 'center';
                            el.style.justifyContent = 'center';
                            el.style.color = 'white';
                            el.style.fontWeight = 'bold';
                            el.style.fontSize = '16px';
                            el.style.boxShadow = '0 2px 8px rgba(0,0,0,0.4)';
                            el.style.cursor = 'pointer';
                            el.textContent = stopNumber;
                            
                            // Create popup content
                            const stayDuration = stop.stay_duration_hours ? `${stop.stay_duration_hours} hours` : 'Not specified';
                            const popupContent = `
                                <div style="padding: 8px; min-width: 200px;">
                                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                        <div style="width: 12px; height: 12px; background: ${markerColor}; border-radius: 50%; margin-right: 8px;"></div>
                                        <strong style="font-size: 14px; color: #111827;">${stop.name || 'Stop ' + (index + 1)}</strong>
                                    </div>
                                    ${stop.location_label ? `<div style="font-size: 12px; color: #4b5563; margin-bottom: 4px;">📍 ${stop.location_label}</div>` : ''}
                                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">⏱️ Stay: ${stayDuration}</div>
                                    ${distanceText}
                                    ${stop.notes ? `<div style="font-size: 12px; color: #4b5563; margin-top: 8px; padding-top: 8px; border-top: 1px solid #e5e7eb;">${stop.notes}</div>` : ''}
                                    <div style="font-size: 11px; color: #9ca3af; margin-top: 8px;">Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}</div>
                                </div>
                            `;
                            
                            // Create Mapbox marker
                            try {
                                const marker = new mapboxgl.Marker(el)
                                    .setLngLat([lng, lat])
                                    .setPopup(new mapboxgl.Popup({ offset: 25 }).setHTML(popupContent))
                                    .addTo(plannerMap);
                                
                                console.log('[MAP] ✓ Marker object created for stop', index + 1);

                                // Store marker at index position
                                while (plannerMarkers.length <= index) {
                                    plannerMarkers.push(null);
                                }
                                
                                // Remove old marker if exists
                                if (plannerMarkers[index] && plannerMarkers[index] !== null) {
                                    try {
                                        plannerMarkers[index].remove();
                                    } catch(e) {
                                        console.error('[MAP] Error removing old marker:', e);
                                    }
                                }
                                
                                plannerMarkers[index] = marker;
                                marker.index = index; // Store index for identification
                                // Mapbox format: [longitude, latitude]
                                coordinates.push([lng, lat]);
                                console.log('[MAP] ✓ Marker created and added for stop', index + 1, 'at', lat, ',', lng);
                                console.log('[MAP] Marker element:', el);
                                console.log('[MAP] Marker position:', marker.getLngLat());
                            } catch (markerError) {
                                console.error('[MAP] ✗ Error creating marker for stop', index + 1, ':', markerError);
                            }
                        } else {
                            console.warn('[MAP] ✗ Stop', index, 'has invalid coordinates (NaN or out of range):', lat, lng);
                        }
                    } else {
                        console.warn('[MAP] ✗ Stop', index, 'missing latitude or longitude. Stop data:', stop);
                    }
                });
                
                console.log('[MAP] ===== Marker creation complete =====');
                console.log('[MAP] Created', validStopsCount, 'valid markers out of', stops.length, 'total stops');
                console.log('[MAP] Coordinates array length:', coordinates.length);
                console.log('[MAP] plannerMarkers array:', plannerMarkers);
                console.log('[MAP] plannerMarkers length:', plannerMarkers.length);
                
                // Fit map bounds to show all markers
                if (coordinates.length > 0 && plannerMap && plannerMap.loaded()) {
                    try {
                        const bounds = new mapboxgl.LngLatBounds();
                        coordinates.forEach(coord => {
                            bounds.extend(coord); // coord is [lng, lat]
                        });
                        
                        // Fit bounds with padding
                        plannerMap.fitBounds(bounds, {
                            padding: { top: 100, bottom: 100, left: 100, right: 100 },
                            maxZoom: 15,
                            duration: 1000
                        });
                        
                        console.log('[MAP] ✓ Map bounds fitted to show all markers');
                    } catch (boundsError) {
                        console.error('[MAP] ✗ Error fitting map bounds:', boundsError);
                    }
                }

                // Draw polyline if we have multiple stops - Mapbox version
                if (coordinates.length > 1 && plannerMap.loaded()) {
                    const geojson = {
                        type: 'Feature',
                        properties: {},
                        geometry: {
                            type: 'LineString',
                            coordinates: coordinates
                        }
                    };
                    
                    // Check if source exists
                    if (plannerMap.getSource('route-line')) {
                        plannerMap.getSource('route-line').setData(geojson);
                    } else {
                        plannerMap.addSource('route-line', {
                            type: 'geojson',
                            data: geojson
                        });
                        
                        plannerMap.addLayer({
                            id: 'route-line',
                            type: 'line',
                            source: 'route-line',
                            layout: {
                                'line-join': 'round',
                                'line-cap': 'round'
                            },
                            paint: {
                                'line-color': '#4f46e5',
                                'line-width': 4,
                                'line-opacity': 0.7
                            }
                        });
                    }
                    plannerPolyline = true;
                }

                // Calculate total distance
                let totalDistance = 0;
                if (distances.length > 0) {
                    totalDistance = distances.reduce((a, b) => a + b, 0);
                }

                // Update legend
                const legendEl = document.getElementById('map-legend');
                const legendStopsEl = document.getElementById('legend-stops');
                const legendDistanceEl = document.getElementById('legend-distance');
                
                if (legendEl && coordinates.length > 0) {
                    legendEl.style.display = 'block';
                    if (legendStopsEl) legendStopsEl.textContent = coordinates.length;
                    if (legendDistanceEl) {
                        if (coordinates.length < 2) {
                            legendDistanceEl.textContent = '-';
                        } else if (totalDistance > 0) {
                            legendDistanceEl.textContent = totalDistance.toFixed(2) + ' NM';
                        } else {
                            legendDistanceEl.textContent = 'Calculating...';
                        }
                    }
                } else if (legendEl) {
                    legendEl.style.display = 'none';
                }

                if (showLoading) {
                    hideMapUpdating();
                }
                
                console.log('[MAP] ===== drawPlannerStops COMPLETE =====');
            }, 100);
        };
        
        // Define refreshMapStops as a working implementation (not just a stub)
        // This will be the actual implementation that reads stops from DOM and draws them
        window.refreshMapStops = window.refreshMapStops || function() {
            console.log('[MAP] ========================================');
            console.log('[MAP] refreshMapStops called - reading stops from DOM');
            console.log('[MAP] ========================================');
            
            if (!plannerMap || typeof mapboxgl === 'undefined') {
                console.warn('[MAP] Map not ready, cannot refresh stops. plannerMap:', !!plannerMap, 'mapboxgl:', typeof mapboxgl);
                setTimeout(() => {
                    if (plannerMap && typeof mapboxgl !== 'undefined') {
                        console.log('[MAP] Retrying refreshMapStops after map is ready');
                        window.refreshMapStops();
                    }
                }, 1000);
                return;
            }
            
            if (!plannerMap.loaded()) {
                console.warn('[MAP] Map not loaded yet, waiting...');
                plannerMap.once('load', () => {
                    console.log('[MAP] Map loaded, retrying refreshMapStops');
                    window.refreshMapStops();
                });
                return;
            }
            
            const stops = [];
            const stopContainers = document.querySelectorAll('.stop-container');
            console.log('[MAP] Found', stopContainers.length, 'stop containers in DOM');
            
            stopContainers.forEach((container, idx) => {
                try {
                    const latInput = document.getElementById('stop-latitude-' + idx);
                    const lngInput = document.getElementById('stop-longitude-' + idx);
                    const nameInput = document.getElementById('stop-name-' + idx);
                    
                    const lat = latInput?.value?.trim() || '';
                    const lng = lngInput?.value?.trim() || '';
                    const name = nameInput?.value?.trim() || `Stop ${idx + 1}`;
                    
                    if (lat && lng && !isNaN(parseFloat(lat)) && !isNaN(parseFloat(lng))) {
                        const latNum = parseFloat(lat);
                        const lngNum = parseFloat(lng);
                        
                        if (latNum >= -90 && latNum <= 90 && lngNum >= -180 && lngNum <= 180) {
                            const locationLabel = container.querySelector('input[wire\\:model*="location_label"]')?.value || '';
                            const stayDuration = container.querySelector('input[wire\\:model*="stay_duration_hours"]')?.value || '';
                            const notes = container.querySelector('textarea[wire\\:model*="notes"]')?.value || '';
                            
                            stops.push({
                                latitude: latNum,
                                longitude: lngNum,
                                name: name,
                                location_label: locationLabel,
                                stay_duration_hours: stayDuration,
                                notes: notes
                            });
                            
                            console.log('[MAP] ✓ Added stop', idx + 1, ':', name, 'at', latNum, ',', lngNum);
                        }
                    }
                } catch (err) {
                    console.error('[MAP] Error reading stop', idx, ':', err);
                }
            });
            
            console.log('[MAP] Total stops collected:', stops.length);
            
            if (stops.length > 0) {
                console.log('[MAP] Refreshing map with', stops.length, 'stops');
                if (typeof window.drawPlannerStops === 'function') {
                    window.drawPlannerStops(stops, false);
                } else {
                    console.error('[MAP] ✗ drawPlannerStops function not available');
                }
            } else {
                console.warn('[MAP] ⚠ No stops with valid coordinates found in DOM');
            }
        };
        
        console.log('[MAP] Function stubs defined - drawPlannerStops:', typeof window.drawPlannerStops, 'refreshMapStops:', typeof window.refreshMapStops);
        
        // Global callback for Mapbox map initialization - marine/nautical map
        // Initialize Mapbox GL JS with marine layers
        window.initPlannerMap = function(retryCount = 0) {
            const MAX_RETRIES = 100; // 10 seconds max
            console.log('initPlannerMap called (Mapbox), retry count:', retryCount);
            
            // Update loading status
            const statusEl = document.getElementById('map-loading-status');
            if (statusEl) {
                const attemptNum = retryCount + 1;
                statusEl.textContent = 'Checking map container... (attempt ' + attemptNum + ')';
            }
            
            const mapEl = document.getElementById('route-map');
            if (!mapEl) {
                if (retryCount < MAX_RETRIES) {
                    setTimeout(() => initPlannerMap(retryCount + 1), 100);
                    return;
                }
                const loadingEl = document.getElementById('map-loading');
                if (loadingEl) {
                    loadingEl.innerHTML = '<div class="text-center p-4 max-w-md mx-auto"><p class="text-red-600 font-medium mb-2">Map Container Not Found</p><button onclick="location.reload()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Refresh Page</button></div>';
                }
                return;
            }
            
            if (typeof mapboxgl === 'undefined') {
                if (statusEl) {
                    const attemptNum = retryCount + 1;
                    statusEl.textContent = 'Waiting for Mapbox GL JS library... (attempt ' + attemptNum + ')';
                }
                if (retryCount < MAX_RETRIES) {
                    setTimeout(() => initPlannerMap(retryCount + 1), 100);
                    return;
                }
                const loadingEl = document.getElementById('map-loading');
                if (loadingEl) {
                    loadingEl.innerHTML = '<div class="text-center p-4 max-w-md mx-auto"><p class="text-red-600 font-medium mb-2">Mapbox GL JS Failed to Load</p><p class="text-sm text-gray-700 mb-3">Please check your internet connection.</p><button onclick="location.reload()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Refresh Page</button></div>';
                }
                return;
            }
            
            if (statusEl) {
                statusEl.textContent = 'Checking access token...';
            }
            
            // Check for Mapbox access token
            let mapboxToken = '';
            try {
                @php
                    $tokenValue = config('services.mapbox.access_token');
                    if ($tokenValue) {
                        $tokenJson = json_encode($tokenValue, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                    } else {
                        $tokenJson = 'null';
                    }
                @endphp
                var mapboxTokenRaw = {!! $tokenJson !!};
                if (mapboxTokenRaw && mapboxTokenRaw !== null) {
                    mapboxToken = String(mapboxTokenRaw);
                    if (mapboxToken === 'null' || mapboxToken === '') {
                        mapboxToken = '';
                    } else {
                        console.log('Mapbox token check: Token found, length:', mapboxToken.length);
                    }
                }
                if (!mapboxToken) {
                    console.log('Mapbox token check: Token missing');
                }
            } catch (e) {
                console.error('Error reading mapbox token:', e);
                mapboxToken = '';
            }
            
            // More robust check for missing token
            if (!mapboxToken || mapboxToken === '' || mapboxToken.trim() === '') {
                console.error('Mapbox access token is missing or invalid');
                const loadingEl = document.getElementById('map-loading');
                if (loadingEl) {
                    loadingEl.style.display = 'block';
                    loadingEl.innerHTML = '<div class="text-center p-4 max-w-md mx-auto bg-white rounded-lg shadow-lg m-4">' +
                        '<p class="text-red-600 font-medium mb-2 text-lg">⚠️ Mapbox Access Token Required</p>' +
                        '<p class="text-sm text-gray-700 mb-3">The map cannot load without a Mapbox access token.</p>' +
                        '<div class="text-xs text-left text-gray-600 mb-4 space-y-2 bg-gray-50 p-3 rounded">' +
                        '<p class="font-semibold">Steps to fix:</p>' +
                        '<ol class="list-decimal list-inside space-y-1">' +
                        '<li>Get a free token at: <a href="https://account.mapbox.com/access-tokens/" target="_blank" class="text-indigo-600 underline">mapbox.com/access-tokens</a></li>' +
                        '<li>Add to your .env file:<br><code class="bg-gray-200 px-2 py-1 rounded block mt-1">MAPBOX_ACCESS_TOKEN=pk.your_token_here</code></li>' +
                        '<li>Run: <code class="bg-gray-200 px-2 py-1 rounded">php artisan config:clear</code></li>' +
                        '<li>Refresh this page</li>' +
                        '</ol>' +
                        '</div>' +
                        '<button onclick="location.reload()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Refresh Page</button>' +
                        '</div>';
                    console.log('Error message displayed to user');
                }
                return;
            }
            
            console.log('Token validation passed, proceeding with map initialization');
            
            console.log('Mapbox GL JS loaded, initializing marine map...');
            const loadingEl = document.getElementById('map-loading');
            
            if (statusEl) {
                statusEl.textContent = 'Initializing map...';
            }
            
            // Set Mapbox access token
            mapboxgl.accessToken = mapboxToken;
            
            // Timeout fallback
            const loadingTimeout = setTimeout(() => {
                if (loadingEl && loadingEl.style.display !== 'none') {
                    loadingEl.innerHTML = '<div class="text-center p-4 max-w-md mx-auto"><p class="text-yellow-600 font-medium mb-2">Map Loading Timeout</p><button onclick="location.reload()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Refresh Page</button></div>';
                }
            }, 10000);
            
            try {
                // Check if map container has dimensions
                if (mapEl.offsetWidth === 0 || mapEl.offsetHeight === 0) {
                    console.warn('Map container appears hidden or has zero dimensions');
                }
                
                // Initialize Mapbox GL JS map with marine/nautical style
                // Using Mapbox Navigation style (good for marine navigation) or custom style
                plannerMap = new mapboxgl.Map({
                    container: mapEl,
                    style: 'mapbox://styles/mapbox/navigation-day-v1', // Good for navigation/marine use
                    center: [0, 20], // [longitude, latitude]
                    zoom: 2,
                    minZoom: 1,
                    maxZoom: 20,
                    pitch: 0,
                    bearing: 0
                });
                
                console.log('[MAP] Mapbox map object created:', plannerMap);
                
                if (statusEl) {
                    statusEl.textContent = 'Map created, loading tiles...';
                }
                
                // Watch for Mapbox canvas to appear - when it does, hide loading overlay
                const mapContainer = mapEl;
                const observer = new MutationObserver(function(mutations) {
                    const canvas = mapContainer.querySelector('.mapboxgl-canvas');
                    if (canvas && canvas.offsetWidth > 0) {
                        console.log('[MAP] ✓ Mapbox canvas detected via observer, hiding loading overlay');
                        const loadingEl = document.getElementById('map-loading');
                        if (loadingEl) {
                            loadingEl.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important; height: 0 !important; width: 0 !important; pointer-events: none !important; z-index: -1 !important;';
                            loadingEl.classList.add('hidden');
                            loadingEl.setAttribute('hidden', 'true');
                            loadingEl.innerHTML = '';
                            setTimeout(() => {
                                if (loadingEl.parentNode) {
                                    loadingEl.remove();
                                    console.log('[MAP] ✓ Loading overlay removed by observer');
                                }
                            }, 100);
                        }
                        observer.disconnect();
                    }
                });
                
                // Start observing for canvas appearance
                observer.observe(mapContainer, {
                    childList: true,
                    subtree: true,
                    attributes: false
                });
                
                // Also check immediately and periodically in case canvas already exists
                function checkForCanvas() {
                    const canvas = mapContainer.querySelector('.mapboxgl-canvas');
                    if (canvas && canvas.offsetWidth > 0) {
                        console.log('[MAP] ✓ Mapbox canvas found, hiding loading overlay');
                        observer.disconnect();
                        const loadingEl = document.getElementById('map-loading');
                        if (loadingEl) {
                            loadingEl.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important; height: 0 !important; width: 0 !important; pointer-events: none !important; z-index: -1 !important;';
                            loadingEl.classList.add('hidden');
                            loadingEl.setAttribute('hidden', 'true');
                            loadingEl.innerHTML = '';
                            setTimeout(() => {
                                if (loadingEl.parentNode) {
                                    loadingEl.remove();
                                }
                            }, 100);
                        }
                        return true;
                    }
                    return false;
                }
                
                // Check immediately
                setTimeout(checkForCanvas, 100);
                setTimeout(checkForCanvas, 500);
                setTimeout(checkForCanvas, 1000);
                
                // Disconnect observer after 10 seconds to prevent memory leaks
                setTimeout(function() {
                    observer.disconnect();
                }, 10000);
                
                // Add error event listeners
                plannerMap.on('error', function(e) {
                    console.error('Mapbox map error:', e);
                    clearTimeout(loadingTimeout);
                    if (loadingEl) {
                        let errorMsg = 'Map initialization failed.';
                        if (e.error && e.error.message) {
                            errorMsg = e.error.message;
                        } else if (e.message) {
                            errorMsg = e.message;
                        }
                        loadingEl.innerHTML = '<div class="text-center p-4 max-w-md mx-auto"><p class="text-red-600 font-medium mb-2">Map Error</p><p class="text-sm text-gray-700 mb-3">' + errorMsg + '</p><p class="text-xs text-gray-600 mb-3">Check browser console for details.</p><button onclick="location.reload()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Refresh Page</button></div>';
                    }
                });
                
                // Listen for style loading errors
                plannerMap.on('style.loading', function() {
                    console.log('Map style loading...');
                });
                
                plannerMap.on('style.error', function(e) {
                    console.error('Map style error:', e);
                    clearTimeout(loadingTimeout);
                    if (loadingEl) {
                        loadingEl.innerHTML = '<div class="text-center p-4 max-w-md mx-auto"><p class="text-red-600 font-medium mb-2">Map Style Error</p><p class="text-sm text-gray-700 mb-3">Failed to load map style. This may indicate an invalid access token.</p><p class="text-xs text-gray-600 mb-3">Please verify your MAPBOX_ACCESS_TOKEN in .env file.</p><button onclick="location.reload()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Refresh Page</button></div>';
                    }
                });
                
                // Function to complete map initialization
                let mapInitCompleted = false;
                function completeMapInit() {
                    if (mapInitCompleted) {
                        console.log('[MAP] Init already completed, skipping');
                        return;
                    }
                    mapInitCompleted = true;
                    console.log('[MAP] === Completing map initialization ===');
                    console.log('[MAP] plannerMap exists:', !!plannerMap);
                    console.log('[MAP] plannerMap.loaded():', plannerMap ? plannerMap.loaded() : 'N/A');
                    clearTimeout(loadingTimeout);
                    mapReady = true;
                    console.log('[MAP] mapReady set to true');
                    
                    // Mark body as map-ready for CSS
                    document.body.classList.add('map-ready');
                    document.body.setAttribute('data-map-ready', 'true');
                    
                    // Hide loading overlay immediately - use multiple methods
                    const loadingElToHide = document.getElementById('map-loading');
                    if (loadingElToHide) {
                        loadingElToHide.style.display = 'none';
                        loadingElToHide.style.visibility = 'hidden';
                        loadingElToHide.style.opacity = '0';
                        loadingElToHide.style.height = '0';
                        loadingElToHide.innerHTML = '';
                        // Remove from DOM after a moment
                        setTimeout(function() {
                            if (loadingElToHide && loadingElToHide.parentNode) {
                                loadingElToHide.remove();
                            }
                        }, 100);
                        console.log('[MAP] ✓ Loading overlay hidden and removed');
                    } else {
                        console.warn('[MAP] Loading element not found');
                    }
                    
                    // Also trigger the periodic check to ensure overlay is hidden
                    if (typeof window.checkAndHideLoading === 'function') {
                        setTimeout(() => window.checkAndHideLoading(), 100);
                    }
                    
                    // IMMEDIATELY draw stops from PHP data when map is ready
                    @php
                        $stopsJson = json_encode($stops ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                    @endphp
                    var initialStopsData = {!! $stopsJson !!};
                    console.log('[MAP] ===== Map ready - attempting to draw stops =====');
                    console.log('[MAP] Initial stops data from PHP:', initialStopsData);
                    console.log('[MAP] Number of stops:', initialStopsData.length);
                    
                    if (initialStopsData && initialStopsData.length > 0) {
                        const stopsWithCoords = initialStopsData.filter(s => {
                            const hasLat = s.latitude !== null && s.latitude !== undefined && s.latitude !== '';
                            const hasLng = s.longitude !== null && s.longitude !== undefined && s.longitude !== '';
                            const latValid = hasLat && !isNaN(parseFloat(s.latitude)) && parseFloat(s.latitude) >= -90 && parseFloat(s.latitude) <= 90;
                            const lngValid = hasLng && !isNaN(parseFloat(s.longitude)) && parseFloat(s.longitude) >= -180 && parseFloat(s.longitude) <= 180;
                            const isValid = latValid && lngValid;
                            if (!isValid) {
                                console.warn('[MAP] Stop filtered out - lat:', s.latitude, 'lng:', s.longitude, 'valid:', latValid, lngValid);
                            }
                            return isValid;
                        });
                        
                        console.log('[MAP] Found', stopsWithCoords.length, 'stops with valid coordinates out of', initialStopsData.length);
                        console.log('[MAP] Valid stops:', stopsWithCoords);
                        
                        if (stopsWithCoords.length > 0) {
                            // Wait for drawPlannerStops to be available, then draw
                            const waitAndDraw = (attempt = 0, maxAttempts = 10) => {
                                if (typeof window.drawPlannerStops === 'function') {
                                    console.log('[MAP] drawPlannerStops is now available, drawing stops');
                                    // Try multiple times to ensure it works
                                    [500, 1000, 2000].forEach((delay, idx) => {
                                        setTimeout(() => {
                                            console.log('[MAP] Attempt', idx + 1, '- Drawing stops after', delay, 'ms');
                                            window.drawPlannerStops(stopsWithCoords, false);
                                        }, delay);
                                    });
                                } else if (attempt < maxAttempts) {
                                    console.log('[MAP] Waiting for drawPlannerStops function (attempt', attempt + 1, '/', maxAttempts, ')');
                                    setTimeout(() => waitAndDraw(attempt + 1, maxAttempts), 200);
                                } else {
                                    console.error('[MAP] ✗ drawPlannerStops function not available after', maxAttempts, 'attempts');
                                }
                            };
                            waitAndDraw();
                        } else {
                            console.warn('[MAP] No stops with valid coordinates to draw');
                        }
                    } else {
                        console.warn('[MAP] No stops data from PHP or empty array');
                    }
                    
                    // Also refresh stops from DOM after delays (in case PHP data wasn't complete)
                    if (typeof window.refreshMapStops === 'function') {
                        [1500, 3000, 5000].forEach((delay, idx) => {
                            setTimeout(() => {
                                console.log('[MAP] DOM refresh attempt', idx + 1, 'after', delay, 'ms');
                                window.refreshMapStops();
                            }, delay);
                        });
                    }
                    
                    // Ensure map container is visible and has proper dimensions
                    if (mapEl) {
                        mapEl.style.display = 'block';
                        mapEl.style.visibility = 'visible';
                        mapEl.style.opacity = '1';
                        if (mapEl.offsetHeight < 400) {
                            mapEl.style.height = '500px';
                        }
                        console.log('[MAP] Map container dimensions:', mapEl.offsetWidth, 'x', mapEl.offsetHeight);
                    }
                    
                    // Ensure map is properly sized
                    setTimeout(() => {
                        if (plannerMap && plannerMap.resize) {
                            plannerMap.resize();
                            console.log('[MAP] Map resized');
                        }
                    }, 300);
                    
                    // Update button states using the centralized function
                    if (typeof window.updateButtonStates === 'function') {
                        window.updateButtonStates();
                    } else {
                        // Fallback: Enable all "Select on Map" buttons manually
                        document.querySelectorAll('.select-stop-btn').forEach(btn => {
                            btn.disabled = false;
                            btn.style.cursor = 'pointer';
                            btn.style.opacity = '1';
                            btn.title = 'Click to select this stop on the map';
                        });
                    }
                    
                    // Setup button handlers
                    if (typeof window.setupSelectButtonHandlers === 'function') {
                        setTimeout(() => {
                            window.setupSelectButtonHandlers();
                        }, 300);
                    }
                    
                    // Draw stops after map is ready - with multiple retries
                    const drawStopsOnInit = () => {
                        if (typeof window.drawPlannerStops === 'function' && plannerMap && plannerMap.loaded()) {
                            // First try using PHP data
                            @php
                                $stopsJson = json_encode($stops ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                            @endphp
                            var stopsData = {!! $stopsJson !!};
                            console.log('[MAP] Drawing stops on map initialization:', stopsData.length, 'stops');
                            // Filter stops that have coordinates
                            const stopsWithCoords = stopsData.filter(s => s.latitude && s.longitude && 
                                !isNaN(parseFloat(s.latitude)) && !isNaN(parseFloat(s.longitude)));
                            if (stopsWithCoords.length > 0) {
                                console.log('[MAP] Drawing', stopsWithCoords.length, 'stops with coordinates from PHP data');
                                window.drawPlannerStops(stopsWithCoords, false);
                            } else {
                                // If no stops from PHP data, try reading from DOM
                                console.log('[MAP] No stops from PHP data, trying to read from DOM');
                                if (typeof window.refreshMapStops === 'function') {
                                    setTimeout(() => window.refreshMapStops(), 500);
                                }
                            }
                            return true;
                        }
                        return false;
                    };
                    
                    // Try immediately
                    setTimeout(() => {
                        if (!drawStopsOnInit()) {
                            // Retry after 1 second
                            setTimeout(() => {
                                if (!drawStopsOnInit()) {
                                    // Final retry after 2 seconds, and also try reading from DOM
                                    setTimeout(() => {
                                        drawStopsOnInit();
                                        // Also try reading from DOM as fallback
                                        if (typeof window.refreshMapStops === 'function') {
                                            setTimeout(() => window.refreshMapStops(), 500);
                                        }
                                    }, 2000);
                                }
                            }, 1000);
                        }
                    }, 800);
                }
                
                // Map ready callback - primary
                plannerMap.once('load', function() {
                    console.log('[MAP] ✓ Map "load" event fired');
                    completeMapInit();
                    
                    // Also try to draw stops immediately when map loads
                    setTimeout(() => {
                        @php
                            $stopsJson = json_encode($stops ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                        @endphp
                        var loadEventStops = {!! $stopsJson !!};
                        console.log('[MAP] Map load event - trying to draw', loadEventStops.length, 'stops');
                        console.log('[MAP] Stops data:', JSON.stringify(loadEventStops, null, 2));
                        console.log('[MAP] drawPlannerStops function exists:', typeof window.drawPlannerStops === 'function');
                        console.log('[MAP] plannerMap exists:', !!plannerMap);
                        console.log('[MAP] plannerMap.loaded():', plannerMap ? plannerMap.loaded() : 'N/A');
                        
                        if (loadEventStops && loadEventStops.length > 0) {
                            const validStops = loadEventStops.filter(s => {
                                const lat = parseFloat(s.latitude);
                                const lng = parseFloat(s.longitude);
                                const isValid = !isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180;
                                if (!isValid) {
                                    console.warn('[MAP] Filtered out stop:', s, 'lat:', lat, 'lng:', lng);
                                }
                                return isValid;
                            });
                            console.log('[MAP] Valid stops after filtering:', validStops.length, 'out of', loadEventStops.length);
                            console.log('[MAP] Valid stops data:', JSON.stringify(validStops, null, 2));
                            
                            if (validStops.length > 0) {
                                // Wait for drawPlannerStops to be available
                                const waitAndDraw = (attempt = 0, maxAttempts = 10) => {
                                    if (typeof window.drawPlannerStops === 'function') {
                                        console.log('[MAP] drawPlannerStops is now available, calling with', validStops.length, 'stops');
                                        try {
                                            window.drawPlannerStops(validStops, false);
                                            console.log('[MAP] drawPlannerStops call completed');
                                        } catch (error) {
                                            console.error('[MAP] ✗ Error calling drawPlannerStops:', error);
                                            console.error('[MAP] Error stack:', error.stack);
                                        }
                                    } else if (attempt < maxAttempts) {
                                        console.log('[MAP] Waiting for drawPlannerStops function (attempt', attempt + 1, '/', maxAttempts, ')');
                                        setTimeout(() => waitAndDraw(attempt + 1, maxAttempts), 200);
                                    } else {
                                        console.error('[MAP] ✗ drawPlannerStops function not available after', maxAttempts, 'attempts');
                                    }
                                };
                                waitAndDraw();
                            } else {
                                console.warn('[MAP] ⚠ No valid stops to draw');
                            }
                        } else {
                            console.warn('[MAP] ⚠ No stops data or empty array');
                        }
                        
                        // Also try reading from DOM - wait for function to be available
                        const waitAndRefresh = (attempt = 0, maxAttempts = 10) => {
                            if (typeof window.refreshMapStops === 'function') {
                                console.log('[MAP] refreshMapStops is now available, calling from DOM');
                                try {
                                    window.refreshMapStops();
                                } catch (error) {
                                    console.error('[MAP] ✗ Error calling refreshMapStops:', error);
                                }
                            } else if (attempt < maxAttempts) {
                                console.log('[MAP] Waiting for refreshMapStops function (attempt', attempt + 1, '/', maxAttempts, ')');
                                setTimeout(() => waitAndRefresh(attempt + 1, maxAttempts), 200);
                            } else {
                                console.warn('[MAP] ⚠ refreshMapStops function not available after', maxAttempts, 'attempts');
                            }
                        };
                        waitAndRefresh();
                    }, 1000);
                });
                
                // Fallback: Also listen for style.load event
                plannerMap.once('style.load', function() {
                    console.log('[MAP] ✓ Map "style.load" event fired');
                    if (!mapInitCompleted) {
                        completeMapInit();
                    }
                });
                
                // Listen for data event as well
                plannerMap.once('data', function() {
                    console.log('[MAP] ✓ Map "data" event fired');
                    if (!mapInitCompleted) {
                        completeMapInit();
                    }
                });
                
                // Aggressive fallback: Check immediately if map is already loaded
                setTimeout(function() {
                    if (!mapInitCompleted && plannerMap) {
                        console.log('[MAP] Checking if map is already loaded...');
                        if (plannerMap.loaded && plannerMap.loaded()) {
                            console.log('[MAP] ✓ Map is already loaded, completing init');
                            completeMapInit();
                        } else if (plannerMap.style && plannerMap.style.loaded && plannerMap.style.loaded()) {
                            console.log('[MAP] ✓ Map style is loaded, completing init');
                            completeMapInit();
                        } else if (plannerMap.isStyleLoaded && plannerMap.isStyleLoaded()) {
                            console.log('[MAP] ✓ Map style loaded (isStyleLoaded), completing init');
                            completeMapInit();
                        }
                    }
                }, 500);
                
                // Fallback: If map doesn't fire events within 2 seconds, force completion
                setTimeout(function() {
                    if (!mapInitCompleted) {
                        console.warn('[MAP] ⚠ Map events not fired within 2 seconds, forcing completion');
                        if (plannerMap) {
                            console.log('[MAP] Map exists, forcing init completion');
                            completeMapInit();
                        } else {
                            console.error('[MAP] ✗ plannerMap is null, cannot complete init');
                        }
                    }
                }, 2000);
                
                // Absolute fallback: Hide loading overlay after 5 seconds no matter what
                setTimeout(function() {
                    const loadingElFinal = document.getElementById('map-loading');
                    if (loadingElFinal && loadingElFinal.style.display !== 'none') {
                        console.warn('[MAP] ⚠ Absolute timeout: Hiding loading overlay after 5 seconds');
                        if (typeof window.forceHideMapLoading === 'function') {
                            window.forceHideMapLoading();
                        } else {
                            loadingElFinal.style.display = 'none';
                            loadingElFinal.style.visibility = 'hidden';
                            loadingElFinal.classList.add('hidden');
                            loadingElFinal.setAttribute('style', 'display: none !important; visibility: hidden !important;');
                            try {
                                if (loadingElFinal.parentNode) {
                                    loadingElFinal.remove();
                                }
                            } catch(e) {
                                console.warn('[MAP] Could not remove loading element:', e);
                            }
                        }
                    }
                }, 5000);
                
                // Add click listener to map for selecting stops
                plannerMap.on('click', function(e) {
                    console.log('[MAP] Map clicked, selectedStopIndex:', selectedStopIndex);
                    
                    // If map is clickable, it's loaded - force hide loading overlay immediately
                    console.log('[MAP] Map click detected - hiding loading overlay');
                    const loadingElCheck = document.getElementById('map-loading');
                    if (loadingElCheck) {
                        console.log('[MAP] Loading element found, hiding it...');
                        // Multiple methods to ensure it's hidden
                        loadingElCheck.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important; height: 0 !important; width: 0 !important; position: absolute !important; pointer-events: none !important;';
                        loadingElCheck.classList.add('hidden');
                        loadingElCheck.setAttribute('hidden', 'true');
                        loadingElCheck.innerHTML = '';
                        // Also try removing from DOM
                        setTimeout(function() {
                            if (loadingElCheck.parentNode) {
                                loadingElCheck.remove();
                                console.log('[MAP] ✓ Loading overlay removed from DOM on map click');
                            }
                        }, 50);
                        console.log('[MAP] ✓ Loading overlay hidden on map click');
                    } else {
                        console.warn('[MAP] Loading element (#map-loading) not found on click');
                    }
                    
                    // Hide loading overlay immediately when map is clicked (map is definitely functional)
                    if (typeof window.forceHideMapLoading === 'function') {
                        window.forceHideMapLoading();
                    }
                    // Also trigger the periodic check
                    if (typeof checkAndHideLoading === 'function') {
                        checkAndHideLoading();
                    }
                    
                    if (selectedStopIndex !== null) {
                        const lat = e.lngLat.lat;
                        const lng = e.lngLat.lng;
                        
                        console.log('[MAP] Updating stop', selectedStopIndex, 'with coordinates:', lat, lng);
                        
                        // Store the selected index before clearing
                        const stopIndexToUpdate = selectedStopIndex;
                        
                        // Update the selected stop's coordinates (this will also update the marker)
                        if (typeof window.updateStopCoordinates === 'function') {
                            console.log('[MAP] Calling updateStopCoordinates for stop', stopIndexToUpdate);
                            window.updateStopCoordinates(stopIndexToUpdate, lat, lng);
                        } else if (typeof updateStopCoordinates === 'function') {
                            updateStopCoordinates(stopIndexToUpdate, lat, lng);
                        } else {
                            console.error('[MAP] ✗ updateStopCoordinates function not found!');
                        }
                        
                        // Remove any existing temporary click marker
                        if (clickMarker) {
                            try {
                                clickMarker.remove();
                            } catch(e) {
                                console.warn('[MAP] Error removing click marker:', e);
                            }
                            clickMarker = null;
                        }
                        
                        // Add a temporary visual feedback marker to show where user clicked
                        // This will be removed after the actual stop marker is created
                        const stopColor = getStopColor(stopIndexToUpdate);
                        const stopNumber = stopIndexToUpdate + 1;
                        
                        // Create custom marker element for temporary feedback
                        const el = document.createElement('div');
                        el.className = 'custom-stop-marker-temp';
                        el.style.backgroundColor = stopColor;
                        el.style.width = '30px';
                        el.style.height = '30px';
                        el.style.borderRadius = '50%';
                        el.style.border = '3px solid white';
                        el.style.display = 'flex';
                        el.style.alignItems = 'center';
                        el.style.justifyContent = 'center';
                        el.style.color = 'white';
                        el.style.fontWeight = 'bold';
                        el.style.fontSize = '14px';
                        el.style.boxShadow = '0 2px 5px rgba(0,0,0,0.3)';
                        el.style.cursor = 'pointer';
                        el.style.opacity = '0.7'; // Make it slightly transparent to indicate it's temporary
                        el.textContent = stopNumber;
                        
                        clickMarker = new mapboxgl.Marker(el)
                            .setLngLat([lng, lat])
                            .addTo(plannerMap);
                        
                        // Remove temporary marker after 2 seconds (actual marker should be created by then)
                        setTimeout(() => {
                            if (clickMarker) {
                                try {
                                    clickMarker.remove();
                                } catch(e) {
                                    console.warn('[MAP] Error removing temporary click marker:', e);
                                }
                                clickMarker = null;
                            }
                        }, 2000);
                        
                        // Clear selection immediately
                        selectedStopIndex = null;
                        
                        // Hide selected stop indicator
                        const indicator = document.getElementById('selected-stop-indicator');
                        if (indicator) {
                            indicator.classList.add('hidden');
                        }
                        
                        // Remove highlight from all stop containers
                        document.querySelectorAll('.stop-container').forEach(container => {
                            container.classList.remove('ring-2', 'ring-indigo-500');
                        });
                        
                        // Reset button states - do this immediately and also after delays
                        const resetButtonStates = () => {
                            document.querySelectorAll('.select-stop-btn').forEach(btn => {
                                // Reset button text and styling if in selected state
                                if (btn.classList.contains('bg-indigo-600') || btn.textContent.includes('Selected')) {
                                    btn.textContent = 'Select on Map';
                                    btn.classList.remove('bg-indigo-600', 'text-white');
                                    btn.classList.add('bg-indigo-50', 'text-indigo-700');
                                }
                                // Always ensure button is enabled if map is ready
                                if (mapReady && plannerMap) {
                                    btn.disabled = false;
                                    btn.style.cursor = 'pointer';
                                    btn.style.opacity = '1';
                                }
                            });
                        };
                        
                        // Reset immediately
                        resetButtonStates();
                        
                        // Reset again after a short delay (in case Livewire updates reset them)
                        setTimeout(resetButtonStates, 300);
                        
                        // Reset again after Livewire typically finishes updating
                        setTimeout(resetButtonStates, 1500);
                        
                        console.log('[MAP] ✓ Selection cleared and button states reset');
                    }
                });
                
                // Additional fallback: If map doesn't fire events within 2 seconds, assume it's ready
                setTimeout(function() {
                    if (!mapReady && loadingEl && loadingEl.style.display !== 'none') {
                        console.warn('Map ready event not fired, using timeout fallback');
                        completeMapInit();
                    }
                }, 2000);
                
            } catch (error) {
                console.error('Error creating map:', error);
                clearTimeout(loadingTimeout);
                if (loadingEl) {
                    let errorDetails = error.message || 'Unknown error occurred';
                    if (error.stack) {
                        console.error('Error stack:', error.stack);
                    }
                    // Check if it's a token-related error
                    if (error.message && (error.message.includes('token') || error.message.includes('Token') || error.message.includes('401') || error.message.includes('Unauthorized'))) {
                        errorDetails = 'Invalid or missing Mapbox access token. Please check your MAPBOX_ACCESS_TOKEN in .env file.';
                    }
                    loadingEl.innerHTML = '<div class="text-center p-4 max-w-md mx-auto"><p class="text-red-600 font-medium mb-2">Map Initialization Error</p><p class="text-sm text-gray-700 mb-3">' + errorDetails + '</p><p class="text-xs text-gray-600 mb-3">Check browser console (F12) for more details.</p><button onclick="location.reload()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Refresh Page</button></div>';
                }
            }
        };
    </script>
    <script>
        // OpenStreetMap Nominatim Autocomplete (No API key required)
        (function() {
            let searchTimeouts = {};
            let activeDropdown = null;
            let selectedIndex = -1;

            function initAutocompleteForStop(input, index) {
                const dropdown = document.getElementById('autocomplete-dropdown-' + index);
                if (!dropdown) return;

                // Debounce search function
                function performSearch(query) {
                    if (query.length < 3) {
                        dropdown.classList.add('hidden');
                        return;
                    }

                    // Clear previous timeout
                    if (searchTimeouts[index]) {
                        clearTimeout(searchTimeouts[index]);
                    }

                    // Debounce the search
                    searchTimeouts[index] = setTimeout(() => {
                        searchNominatim(query, index, dropdown);
                    }, 300);
                }

                // Handle input events
                input.addEventListener('input', function(e) {
                    const query = e.target.value.trim();
                    selectedIndex = -1;
                    performSearch(query);
                });

                // Handle focus
                input.addEventListener('focus', function(e) {
                    const query = e.target.value.trim();
                    if (query.length >= 3) {
                        performSearch(query);
                    }
                });

                // Handle keyboard navigation
                input.addEventListener('keydown', function(e) {
                    const items = dropdown.querySelectorAll('.autocomplete-item');
                    
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                        updateSelection(items);
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        selectedIndex = Math.max(selectedIndex - 1, -1);
                        updateSelection(items);
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        if (selectedIndex >= 0 && items[selectedIndex]) {
                            items[selectedIndex].click();
                        }
                    } else if (e.key === 'Escape') {
                        dropdown.classList.add('hidden');
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            }

            function updateSelection(items) {
                items.forEach((item, index) => {
                    if (index === selectedIndex) {
                        item.classList.add('active');
                        item.scrollIntoView({ block: 'nearest' });
                    } else {
                        item.classList.remove('active');
                    }
                });
            }

            async function searchNominatim(query, index, dropdown) {
                try {
                    // Use Nominatim API (free, no key required)
                    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&addressdetails=1`;
                    
                    const response = await fetch(url, {
                        headers: {
                            'User-Agent': 'YachtWorkersCouncil/1.0'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Search failed');
                    }

                    const results = await response.json();
                    displayResults(results, index, dropdown);
                } catch (error) {
                    console.error('Geocoding error:', error);
                    dropdown.classList.add('hidden');
                }
            }

            function displayResults(results, index, dropdown) {
                dropdown.innerHTML = '';

                if (results.length === 0) {
                    dropdown.innerHTML = '<div class="autocomplete-item"><div class="autocomplete-item-details">No results found</div></div>';
                    dropdown.classList.remove('hidden');
                    return;
                }

                results.forEach((result, idx) => {
                    const item = document.createElement('div');
                    item.className = 'autocomplete-item';
                    item.dataset.lat = result.lat;
                    item.dataset.lng = result.lon;
                    item.dataset.name = result.display_name;

                    const name = result.display_name.split(',')[0];
                    const details = result.display_name.split(',').slice(1, 3).join(',').trim();

                    item.innerHTML = `
                        <div class="autocomplete-item-name">${name}</div>
                        <div class="autocomplete-item-details">${details || result.display_name}</div>
                    `;

                    item.addEventListener('click', function() {
                        selectPlace(result, index);
                    });

                    dropdown.appendChild(item);
                });

                dropdown.classList.remove('hidden');
            }

            function selectPlace(place, index) {
                const input = document.getElementById('stop-name-' + index);
                const dropdown = document.getElementById('autocomplete-dropdown-' + index);
                const latInput = document.getElementById('stop-latitude-' + index);
                const lngInput = document.getElementById('stop-longitude-' + index);
                const labelInput = document.querySelector(`input[wire\\:model*="stops.${index}.location_label"]`);

                if (!input || !latInput || !lngInput) return;

                // Ensure fields are editable (remove readonly if any)
                latInput.removeAttribute('readonly');
                lngInput.removeAttribute('readonly');
                latInput.removeAttribute('disabled');
                lngInput.removeAttribute('disabled');

                // Update stop name
                input.value = place.display_name;
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));

                // Update latitude and longitude
                const lat = parseFloat(place.lat).toFixed(6);
                const lng = parseFloat(place.lon).toFixed(6);

                latInput.value = lat;
                latInput.dispatchEvent(new Event('input', { bubbles: true }));
                latInput.dispatchEvent(new Event('change', { bubbles: true }));

                lngInput.value = lng;
                lngInput.dispatchEvent(new Event('input', { bubbles: true }));
                lngInput.dispatchEvent(new Event('change', { bubbles: true }));

                // Optionally update location label
                if (labelInput && !labelInput.value) {
                    const type = place.type || place.class || '';
                    if (type) {
                        labelInput.value = type.charAt(0).toUpperCase() + type.slice(1);
                        labelInput.dispatchEvent(new Event('input', { bubbles: true }));
                        labelInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }

                // Hide dropdown
                dropdown.classList.add('hidden');
                selectedIndex = -1;

                // Ensure fields remain editable - trigger Livewire update
                if (typeof Livewire !== 'undefined') {
                    // Small delay to ensure Livewire processes the update
                    setTimeout(() => {
                        latInput.focus();
                        latInput.blur();
                        lngInput.focus();
                        lngInput.blur();
                    }, 100);
                }
            }

            // Ensure coordinate fields are always editable
            function ensureCoordinateFieldsEditable() {
                document.querySelectorAll('.stop-coordinate-input').forEach(function(input) {
                    // Remove any readonly or disabled attributes
                    input.removeAttribute('readonly');
                    input.removeAttribute('disabled');
                    
                    // Ensure input type is text (not number which might have restrictions)
                    if (input.type === 'number') {
                        input.type = 'text';
                    }
                    
                    // Make sure it's not blocked by any pointer-events
                    input.style.pointerEvents = 'auto';
                    input.style.cursor = 'text';
                });
            }

            // Initialize autocomplete for existing fields
            function initAllAutocompletes() {
                document.querySelectorAll('.stop-name-autocomplete').forEach(function(input) {
                    const index = input.id.replace('stop-name-', '');
                    initAutocompleteForStop(input, index);
                });
                
                // Ensure coordinate fields are editable
                ensureCoordinateFieldsEditable();
            }

            // Initialize on page load
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initAllAutocompletes);
            } else {
                initAllAutocompletes();
            }

            // Re-initialize when Livewire updates the DOM
            if (typeof Livewire !== 'undefined') {
                document.addEventListener('livewire:init', () => {
                    Livewire.hook('morph.updated', ({ el, component }) => {
                        setTimeout(() => {
                            initAllAutocompletes();
                            ensureCoordinateFieldsEditable();
                        }, 100);
                    });
                });

                Livewire.hook('morph.updated', ({ el, component }) => {
                    setTimeout(() => {
                        initAllAutocompletes();
                        ensureCoordinateFieldsEditable();
                    }, 100);
                });
            }
            
            // Also ensure fields are editable on any input event
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('stop-coordinate-input')) {
                    e.target.removeAttribute('readonly');
                    e.target.removeAttribute('disabled');
                }
            }, true);
        })();

        // Handle "Select on Map" button clicks - Attach directly to each button
        const buttonHandlers = new WeakMap();

        function setupSelectButtonHandlers() {
            document.querySelectorAll('.select-stop-btn').forEach(button => {
                // Skip if handler already attached
                if (buttonHandlers.has(button)) {
                    // But still enable the button in case it was disabled
                    if (typeof mapReady !== 'undefined' && mapReady && typeof plannerMap !== 'undefined' && plannerMap) {
                        button.disabled = false;
                        button.removeAttribute('disabled');
                        button.style.cursor = 'pointer';
                        button.style.opacity = '1';
                    }
                    return;
                }

                // Enable button if map is ready BEFORE attaching handler
                if (typeof mapReady !== 'undefined' && mapReady && typeof plannerMap !== 'undefined' && plannerMap) {
                    button.disabled = false;
                    button.removeAttribute('disabled');
                    button.style.cursor = 'pointer';
                    button.style.opacity = '1';
                    button.style.pointerEvents = 'auto';
                    button.title = 'Click to select this stop on the map';
                }

                // Create handler function
                const handler = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Check if button is disabled
                    if (this.disabled) {
                        console.log('Button is disabled');
                    return;
                }

                    // Check if map is ready
                    if (!mapReady || !plannerMap) {
                        console.log('Map is not ready yet. mapReady:', mapReady, 'plannerMap:', !!plannerMap);
                        alert('Please wait for the map to finish loading.');
                        return;
                    }
                    
                    const index = parseInt(this.getAttribute('data-stop-index'));
                    if (isNaN(index)) {
                        console.error('Invalid stop index:', this.getAttribute('data-stop-index'));
                        return;
                    }
                    
                    console.log('Select on Map clicked for stop index:', index);
                    selectStopOnMap(index);
                };
                
                // Store handler reference and attach
                buttonHandlers.set(button, handler);
                button.addEventListener('click', handler);
            });
            console.log('Select button handlers attached to', document.querySelectorAll('.select-stop-btn').length, 'buttons');
        }
        
        // Function is already defined above - remove duplicate
        
        // Make functions globally accessible
        window.setupSelectButtonHandlers = setupSelectButtonHandlers;
        window.selectStopOnMap = selectStopOnMap;
        
        // Initialize button handlers when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(setupSelectButtonHandlers, 500);
            });
                } else {
            setTimeout(setupSelectButtonHandlers, 500);
        }
        
        // Re-setup handlers after Livewire updates
        if (typeof Livewire !== 'undefined') {
            document.addEventListener('livewire:init', () => {
                Livewire.hook('morph.updated', () => {
                    // Enable buttons immediately for newly added stops
                    if (typeof mapReady !== 'undefined' && mapReady && typeof plannerMap !== 'undefined' && plannerMap) {
                        document.querySelectorAll('.select-stop-btn').forEach(btn => {
                            btn.disabled = false;
                            btn.removeAttribute('disabled');
                            btn.style.cursor = 'pointer';
                            btn.style.opacity = '1';
                        });
                    }
                    setTimeout(setupSelectButtonHandlers, 200);
                });
            });
            
            Livewire.hook('morph.updated', () => {
                // Enable buttons immediately for newly added stops
                if (typeof mapReady !== 'undefined' && mapReady && typeof plannerMap !== 'undefined' && plannerMap) {
                    document.querySelectorAll('.select-stop-btn').forEach(btn => {
                        btn.disabled = false;
                        btn.removeAttribute('disabled');
                        btn.style.cursor = 'pointer';
                        btn.style.opacity = '1';
                    });
                }
                setTimeout(setupSelectButtonHandlers, 200);
            });
            
            // Also listen for Livewire commit events (when actions complete)
            document.addEventListener('livewire:commit', () => {
                setTimeout(() => {
                    if (typeof mapReady !== 'undefined' && mapReady && typeof plannerMap !== 'undefined' && plannerMap) {
                        document.querySelectorAll('.select-stop-btn').forEach(btn => {
                            btn.disabled = false;
                            btn.removeAttribute('disabled');
                            btn.style.cursor = 'pointer';
                            btn.style.opacity = '1';
                            btn.style.pointerEvents = 'auto';
                        });
                        if (typeof window.setupSelectButtonHandlers === 'function') {
                            window.setupSelectButtonHandlers();
                        }
                        // Also call the ensure function
                        if (typeof window.ensureButtonsEnabled === 'function') {
                            window.ensureButtonsEnabled();
                        }
                    }
                }, 100);
            });
            
            // Listen for all Livewire events that might affect buttons
            ['livewire:update', 'livewire:message', 'livewire:commit'].forEach(eventName => {
                document.addEventListener(eventName, () => {
                    setTimeout(() => {
                        if (typeof window.ensureButtonsEnabled === 'function') {
                            window.ensureButtonsEnabled();
                        }
                    }, 150);
                });
            });
        }

        (function() {
            console.log('[MAP] ===== IIFE STARTING - This should define full implementations =====');
            // Use global variables defined above
            // All map functions will use plannerMap, plannerMarkers, etc.
            
            // Local reverse geocoding function - using Nominatim (OpenStreetMap)
            async function reverseGeocodeLocal(lat, lng) {
                // Use Nominatim for reverse geocoding (free, no API key required)
                return fetchNominatimReverseLocal(lat, lng);
            }
            
            // Fallback: Use Nominatim for reverse geocoding
            async function fetchNominatimReverseLocal(lat, lng) {
                try {
                    const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`;
                    const response = await fetch(url, {
                        headers: {
                            'User-Agent': 'YachtWorkersCouncil/1.0'
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        return data.display_name || '';
                    }
                } catch (error) {
                    console.error('Reverse geocoding error:', error);
                }
                return '';
            }
            
            // Helper functions that use global variables (plannerMap, plannerMarkers, etc.) - Mapbox version
            function updateMarkerForStop(index, lat, lng) {
                console.log('updateMarkerForStop called for index:', index, 'lat:', lat, 'lng:', lng);
                
                if (!plannerMap) {
                    console.warn('Map not initialized yet');
                    return;
                }
                
                // Remove ALL existing markers for this index first to prevent duplicates
                plannerMarkers.forEach((marker, idx) => {
                    if (marker && (idx === index || (marker.index && marker.index === index))) {
                        try {
                            marker.remove();
                        } catch(e) {
                            console.error('Error removing marker:', e);
                        }
                        plannerMarkers[idx] = null;
                    }
                });
                
                // Remove marker at specific index
                if (plannerMarkers[index] && plannerMarkers[index] !== null) {
                    try {
                        plannerMarkers[index].remove();
                    } catch(e) {
                        console.error('Error removing existing marker:', e);
                    }
                    plannerMarkers[index] = null;
                }
                
                // Create marker with Mapbox
                const stopNameInput = document.getElementById('stop-name-' + index);
                const stopName = stopNameInput ? stopNameInput.value : `Stop ${index + 1}`;
                const markerColor = getStopColor(index);
                const stopNumber = index + 1;
                
                // Create popup content
                const container = document.querySelector(`.stop-container[data-stop-index="${index}"]`);
                const stayDuration = container ? 
                    (container.querySelector('input[wire\\:model*="stay_duration_hours"]')?.value || 'Not specified') + ' hours' : 
                    'Not specified';
                const locationLabel = container ? 
                    (container.querySelector('input[wire\\:model*="location_label"]')?.value || '') : '';
                const notes = container ? 
                    (container.querySelector('textarea[wire\\:model*="notes"]')?.value || '') : '';
                
                const popupContent = `
                    <div style="padding: 8px; min-width: 200px;">
                        <div style="display: flex; align-items: center; margin-bottom: 8px;">
                            <div style="width: 12px; height: 12px; background: ${markerColor}; border-radius: 50%; margin-right: 8px;"></div>
                            <strong style="font-size: 14px; color: #111827;">${stopName}</strong>
                        </div>
                        ${locationLabel ? `<div style="font-size: 12px; color: #4b5563; margin-bottom: 4px;">📍 ${locationLabel}</div>` : ''}
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">⏱️ Stay: ${stayDuration}</div>
                        ${notes ? `<div style="font-size: 12px; color: #4b5563; margin-top: 8px; padding-top: 8px; border-top: 1px solid #e5e7eb;">${notes}</div>` : ''}
                        <div style="font-size: 11px; color: #9ca3af; margin-top: 8px;">Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}</div>
                    </div>
                `;
                
                // Create custom marker element
                const el = document.createElement('div');
                el.className = 'custom-stop-marker';
                el.style.backgroundColor = markerColor;
                el.style.width = '35px';
                el.style.height = '35px';
                el.style.borderRadius = '50%';
                el.style.border = '3px solid white';
                el.style.display = 'flex';
                el.style.alignItems = 'center';
                el.style.justifyContent = 'center';
                el.style.color = 'white';
                el.style.fontWeight = 'bold';
                el.style.fontSize = '16px';
                el.style.boxShadow = '0 2px 8px rgba(0,0,0,0.4)';
                el.style.cursor = 'pointer';
                el.textContent = stopNumber;
                
                // Create Mapbox marker
                const marker = new mapboxgl.Marker(el)
                    .setLngLat([lng, lat])
                    .setPopup(new mapboxgl.Popup({ offset: 25 }).setHTML(popupContent))
                    .addTo(plannerMap);
                
                // Store marker in array
                while (plannerMarkers.length <= index) {
                    plannerMarkers.push(null);
                }
                plannerMarkers[index] = marker;
                marker.index = index; // Store index for identification
                
                // Update polyline
                if (typeof updatePolyline === 'function') {
                    updatePolyline();
                }
            }
                    
            // Make updateMarkerForStop globally accessible
            window.updateMarkerForStop = updateMarkerForStop;
            
            // Helper function to update polyline - Mapbox version
            function updatePolyline() {
                if (!plannerMap || !plannerMap.loaded()) return;
                
                const validMarkers = plannerMarkers.filter(m => m !== null && m !== undefined);
                if (validMarkers.length < 2) {
                    // Remove route line if less than 2 markers
                    if (plannerMap.getSource('route-line')) {
                        if (plannerMap.getLayer('route-line')) {
                            plannerMap.removeLayer('route-line');
                        }
                        plannerMap.removeSource('route-line');
                        plannerPolyline = null;
                    }
                    return;
                }
                
                // Get coordinates from Mapbox markers - Mapbox uses [lng, lat] format
                const coordinates = validMarkers.map(m => {
                    if (m && m.getLngLat) {
                        const ll = m.getLngLat();
                        return [ll.lng, ll.lat]; // Mapbox format: [longitude, latitude]
                    }
                    return null;
                }).filter(p => p !== null);
                
                if (coordinates.length > 1) {
                    const geojson = {
                        type: 'Feature',
                        properties: {},
                        geometry: {
                            type: 'LineString',
                            coordinates: coordinates
                        }
                    };
                    
                    // Check if source exists
                    if (plannerMap.getSource('route-line')) {
                        // Update existing source
                        plannerMap.getSource('route-line').setData(geojson);
                    } else {
                        // Create new source
                        plannerMap.addSource('route-line', {
                            type: 'geojson',
                            data: geojson
                        });
                        
                        // Add layer
                        plannerMap.addLayer({
                            id: 'route-line',
                            type: 'line',
                            source: 'route-line',
                            layout: {
                                'line-join': 'round',
                                'line-cap': 'round'
                            },
                            paint: {
                                'line-color': '#4f46e5',
                                'line-width': 4,
                                'line-opacity': 0.7
                            }
                        });
                    }
                    plannerPolyline = true; // Mark as existing
                } else if (plannerMap.getSource('route-line')) {
                    // Remove route line
                    if (plannerMap.getLayer('route-line')) {
                        plannerMap.removeLayer('route-line');
                    }
                    plannerMap.removeSource('route-line');
                    plannerPolyline = null;
                }
            }
                    
            // Make updatePolyline globally accessible
            window.updatePolyline = updatePolyline;
            
            
            // Fallback: Use Nominatim for reverse geocoding - GLOBAL (define first)
            window.fetchNominatimReverse = window.fetchNominatimReverse || async function(lat, lng) {
                try {
                    console.log('[GEOCODE] Fetching from Nominatim for lat:', lat, 'lng:', lng);
                    const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1&zoom=18`;
                    const response = await fetch(url, {
                        headers: {
                            'User-Agent': 'YachtWorkersCouncil/1.0',
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        const address = data.display_name || '';
                        console.log('[GEOCODE] ✓ Nominatim result:', address);
                        return address;
                    } else {
                        console.warn('[GEOCODE] Nominatim request failed with status:', response.status);
                    }
                } catch (error) {
                    console.error('[GEOCODE] ✗ Reverse geocoding error:', error);
                }
                return '';
            };
            
            // Make reverse geocoding globally accessible - using Nominatim (OpenStreetMap)
            window.reverseGeocode = window.reverseGeocode || async function(lat, lng) {
                console.log('reverseGeocode called:', lat, lng);
                // Use Nominatim (OpenStreetMap) for reverse geocoding
                if (typeof window.fetchNominatimReverse === 'function') {
                    return await window.fetchNominatimReverse(lat, lng);
                }
                return '';
            };
            
            // updateStopCoordinates is already defined globally above (outside this IIFE)
            // No need to redefine it here - the global function is already available
            console.log('[STOP] updateStopCoordinates available (defined globally)');
            
            console.log('[MAP] ===== DEFINING FULL IMPLEMENTATIONS =====');
            console.log('[MAP] Current drawPlannerStops type:', typeof window.drawPlannerStops);
            console.log('[MAP] Current refreshMapStops type:', typeof window.refreshMapStops);

            function showMapUpdating() {
                const updatingEl = document.getElementById('map-updating');
                if (updatingEl) {
                    updatingEl.classList.remove('hidden');
                }
            }

            function hideMapUpdating() {
                const updatingEl = document.getElementById('map-updating');
                if (updatingEl) {
                    updatingEl.classList.add('hidden');
                }
            }

            // Draw stops on Leaflet map - nautical/sea map version
            // FORCE REPLACE the stub with full implementation
            console.log('[MAP] Replacing drawPlannerStops stub with full implementation');
            window.drawPlannerStops = function drawStops(stops = [], showLoading = true) {
                console.log('[MAP] ========================================');
                console.log('[MAP] ===== drawPlannerStops CALLED =====');
                console.log('[MAP] ========================================');
                console.log('[MAP] Stops count:', stops.length);
                console.log('[MAP] Stops data:', JSON.stringify(stops, null, 2));
                console.log('[MAP] showLoading:', showLoading);
                console.log('[MAP] plannerMap exists:', !!plannerMap);
                console.log('[MAP] plannerMap type:', typeof plannerMap);
                console.log('[MAP] mapboxgl exists:', typeof mapboxgl !== 'undefined');
                
                if (!plannerMap || typeof mapboxgl === 'undefined') {
                    console.warn('[MAP] ✗ Map or Mapbox GL JS not initialized, cannot draw stops');
                    console.warn('[MAP] plannerMap:', plannerMap);
                    console.warn('[MAP] mapboxgl:', typeof mapboxgl);
                    // Retry after a short delay if map is not ready
                    if (typeof plannerMap === 'undefined' || !plannerMap) {
                        setTimeout(() => {
                            if (plannerMap && typeof mapboxgl !== 'undefined' && stops.length > 0) {
                                console.log('[MAP] Retrying to draw stops after map initialization');
                                window.drawPlannerStops(stops, showLoading);
                            }
                        }, 1000);
                    }
                    return;
                }
                
                // Check if map is loaded
                try {
                    const isLoaded = plannerMap.loaded();
                    console.log('[MAP] Map loaded status:', isLoaded);
                    if (!isLoaded) {
                        console.warn('[MAP] Map not loaded yet, waiting for load event');
                        plannerMap.once('load', () => {
                            console.log('[MAP] Map loaded, retrying drawPlannerStops');
                            window.drawPlannerStops(stops, showLoading);
                        });
                        return;
                    }
                } catch (loadError) {
                    console.error('[MAP] Error checking map loaded status:', loadError);
                    // Continue anyway - map might be ready
                }

                // Skip redrawing if we just manually updated coordinates via map click
                if (typeof window !== 'undefined' && window.skipLivewireRedraw) {
                    console.log('[MAP] Skipping drawPlannerStops - manual coordinate update in progress');
                    return;
                }
                
                console.log('[MAP] ✓ All checks passed, proceeding to draw stops');

                if (showLoading) {
                    showMapUpdating();
                }

                setTimeout(() => {
                    // Remove ALL existing markers completely before creating new ones
                    plannerMarkers.forEach((marker, idx) => {
                        if (marker) {
                            try {
                                marker.remove();
                            } catch(e) {
                                console.error('Error removing marker:', e);
                            }
                        }
                        plannerMarkers[idx] = null;
                    });
                    plannerMarkers = [];
                    
                    // Remove existing polyline
                    if (plannerMap.getSource('route-line')) {
                        if (plannerMap.getLayer('route-line')) {
                            plannerMap.removeLayer('route-line');
                        }
                        plannerMap.removeSource('route-line');
                        plannerPolyline = null;
                    }

                    const coordinates = [];
                    const distances = [];
                    
                    // Helper function to calculate distance between two coordinates (Haversine formula)
                    function calculateDistance(lat1, lng1, lat2, lng2) {
                        const R = 6371000; // Earth radius in meters
                        const dLat = (lat2 - lat1) * Math.PI / 180;
                        const dLng = (lng2 - lng1) * Math.PI / 180;
                        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                                Math.sin(dLng/2) * Math.sin(dLng/2);
                        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                        return R * c; // Distance in meters
                    }
                    
                    console.log('[MAP] Processing', stops.length, 'stops to draw markers');
                    let validStopsCount = 0;
                    
                    stops.forEach((stop, index) => {
                        console.log('[MAP] Processing stop', index, ':', stop);
                        
                        if (stop.latitude && stop.longitude) {
                            const lat = parseFloat(stop.latitude);
                            const lng = parseFloat(stop.longitude);
                            console.log('[MAP] Stop', index, '- Parsed coordinates: lat=', lat, 'lng=', lng);
                            
                            if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                                validStopsCount++;
                                console.log('[MAP] ✓ Stop', index, 'has valid coordinates, creating marker');
                                const position = [lat, lng];
                                
                                // Calculate distance from previous stop (nautical miles)
                                let distanceText = '';
                                if (coordinates.length > 0) {
                                    const prevPosition = coordinates[coordinates.length - 1];
                                    // prevPosition is [lng, lat] in Mapbox format, calculateDistance expects (lat, lng)
                                    const distanceMeters = calculateDistance(prevPosition[1], prevPosition[0], lat, lng);
                                    const distanceNm = (distanceMeters / 1852).toFixed(2); // Convert to nautical miles
                                    distances.push(parseFloat(distanceNm));
                                    distanceText = `<div style="font-size: 12px; color: #6b7280; margin-top: 4px;">Distance from previous: ${distanceNm} NM</div>`;
                                }
                                
                                // Create Mapbox marker with custom icon
                                const stopName = stop.name || 'Stop ' + (index + 1);
                                const markerColor = getStopColor(index);
                                const stopNumber = index + 1;
                                
                                // Create custom marker element
                                const el = document.createElement('div');
                                el.className = 'custom-stop-marker';
                                el.style.backgroundColor = markerColor;
                                el.style.width = '35px';
                                el.style.height = '35px';
                                el.style.borderRadius = '50%';
                                el.style.border = '3px solid white';
                                el.style.display = 'flex';
                                el.style.alignItems = 'center';
                                el.style.justifyContent = 'center';
                                el.style.color = 'white';
                                el.style.fontWeight = 'bold';
                                el.style.fontSize = '16px';
                                el.style.boxShadow = '0 2px 8px rgba(0,0,0,0.4)';
                                el.style.cursor = 'pointer';
                                el.textContent = stopNumber;
                                
                                // Create popup content
                                const stayDuration = stop.stay_duration_hours ? `${stop.stay_duration_hours} hours` : 'Not specified';
                                const popupContent = `
                                    <div style="padding: 8px; min-width: 200px;">
                                        <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                            <div style="width: 12px; height: 12px; background: ${markerColor}; border-radius: 50%; margin-right: 8px;"></div>
                                            <strong style="font-size: 14px; color: #111827;">${stop.name || 'Stop ' + (index + 1)}</strong>
                                        </div>
                                        ${stop.location_label ? `<div style="font-size: 12px; color: #4b5563; margin-bottom: 4px;">📍 ${stop.location_label}</div>` : ''}
                                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">⏱️ Stay: ${stayDuration}</div>
                                        ${distanceText}
                                        ${stop.notes ? `<div style="font-size: 12px; color: #4b5563; margin-top: 8px; padding-top: 8px; border-top: 1px solid #e5e7eb;">${stop.notes}</div>` : ''}
                                        <div style="font-size: 11px; color: #9ca3af; margin-top: 8px;">Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}</div>
                                    </div>
                                `;
                                
                                // Create Mapbox marker
                                try {
                                    const marker = new mapboxgl.Marker(el)
                                        .setLngLat([lng, lat])
                                        .setPopup(new mapboxgl.Popup({ offset: 25 }).setHTML(popupContent))
                                        .addTo(plannerMap);
                                    
                                    console.log('[MAP] ✓ Marker object created for stop', index + 1);

                                    // Store marker at index position
                                    while (plannerMarkers.length <= index) {
                                        plannerMarkers.push(null);
                                    }
                                    
                                    // Remove old marker if exists
                                    if (plannerMarkers[index] && plannerMarkers[index] !== null) {
                                        try {
                                            plannerMarkers[index].remove();
                                        } catch(e) {
                                            console.error('[MAP] Error removing old marker:', e);
                                        }
                                    }
                                    
                                    plannerMarkers[index] = marker;
                                    marker.index = index; // Store index for identification
                                    // Mapbox format: [longitude, latitude]
                                    coordinates.push([lng, lat]);
                                    console.log('[MAP] ✓ Marker created and added for stop', index + 1, 'at', lat, ',', lng);
                                    console.log('[MAP] Marker element:', el);
                                    console.log('[MAP] Marker position:', marker.getLngLat());
                                } catch (markerError) {
                                    console.error('[MAP] ✗ Error creating marker for stop', index + 1, ':', markerError);
                                }
                            } else {
                                console.warn('[MAP] ✗ Stop', index, 'has invalid coordinates (NaN or out of range):', lat, lng);
                            }
                        } else {
                            console.warn('[MAP] ✗ Stop', index, 'missing latitude or longitude. Stop data:', stop);
                        }
                    });
                    
                    console.log('[MAP] ===== Marker creation complete =====');
                    console.log('[MAP] Created', validStopsCount, 'valid markers out of', stops.length, 'total stops');
                    console.log('[MAP] Coordinates array length:', coordinates.length);
                    console.log('[MAP] plannerMarkers array:', plannerMarkers);
                    console.log('[MAP] plannerMarkers length:', plannerMarkers.length);
                    
                    // Fit map bounds to show all markers
                    if (coordinates.length > 0 && plannerMap && plannerMap.loaded()) {
                        try {
                            const bounds = new mapboxgl.LngLatBounds();
                            coordinates.forEach(coord => {
                                bounds.extend(coord); // coord is [lng, lat]
                            });
                            
                            // Fit bounds with padding
                            plannerMap.fitBounds(bounds, {
                                padding: { top: 100, bottom: 100, left: 100, right: 100 },
                                maxZoom: 15,
                                duration: 1000
                            });
                            
                            console.log('[MAP] ✓ Map bounds fitted to show all markers');
                        } catch (boundsError) {
                            console.error('[MAP] ✗ Error fitting map bounds:', boundsError);
                        }
                    }

                    // Draw polyline if we have multiple stops - Mapbox version
                    if (coordinates.length > 1 && plannerMap.loaded()) {
                        const geojson = {
                            type: 'Feature',
                            properties: {},
                            geometry: {
                                type: 'LineString',
                                coordinates: coordinates
                            }
                        };
                        
                        // Check if source exists
                        if (plannerMap.getSource('route-line')) {
                            plannerMap.getSource('route-line').setData(geojson);
                        } else {
                            plannerMap.addSource('route-line', {
                                type: 'geojson',
                                data: geojson
                            });
                            
                            plannerMap.addLayer({
                                id: 'route-line',
                                type: 'line',
                                source: 'route-line',
                                layout: {
                                    'line-join': 'round',
                                    'line-cap': 'round'
                                },
                                paint: {
                                    'line-color': '#4f46e5',
                                    'line-width': 4,
                                    'line-opacity': 0.7
                                }
                            });
                        }
                        plannerPolyline = true;
                    }

                    // Calculate total distance
                    let totalDistance = 0;
                    if (distances.length > 0) {
                        totalDistance = distances.reduce((a, b) => a + b, 0);
                    }

                    // Update legend
                    const legendEl = document.getElementById('map-legend');
                    const legendStopsEl = document.getElementById('legend-stops');
                    const legendDistanceEl = document.getElementById('legend-distance');
                    
                    if (legendEl && coordinates.length > 0) {
                        legendEl.style.display = 'block';
                        if (legendStopsEl) legendStopsEl.textContent = coordinates.length;
                        if (legendDistanceEl) {
                            if (coordinates.length < 2) {
                                legendDistanceEl.textContent = '-';
                            } else if (totalDistance > 0) {
                                legendDistanceEl.textContent = totalDistance.toFixed(2) + ' NM';
                            } else {
                                legendDistanceEl.textContent = 'Calculating...';
                            }
                        }
                    } else if (legendEl) {
                        legendEl.style.display = 'none';
                    }

                    // Fit bounds to show all markers - Mapbox version
                    if (coordinates.length > 0) {
                        const bounds = coordinates.reduce(function(bounds, coord) {
                            return bounds.extend(coord);
                        }, new mapboxgl.LngLatBounds(coordinates[0], coordinates[0]));
                        
                        plannerMap.fitBounds(bounds, {
                            padding: {top: 50, bottom: 50, left: 50, right: 50}
                        });
                    } else {
                        plannerMap.setCenter([0, 20]);
                        plannerMap.setZoom(2);
                    }

                    hideMapUpdating();
                    
                    // Final check - verify markers are actually on the map
                    setTimeout(() => {
                        const visibleMarkers = plannerMarkers.filter(m => m !== null && m !== undefined);
                        console.log('[MAP] ===== Final Marker Check =====');
                        console.log('[MAP] Visible markers count:', visibleMarkers.length);
                        console.log('[MAP] Total plannerMarkers array length:', plannerMarkers.length);
                        console.log('[MAP] plannerMarkers array:', plannerMarkers);
                        
                        visibleMarkers.forEach((marker, idx) => {
                            try {
                                if (marker && marker.getLngLat) {
                                    const pos = marker.getLngLat();
                                    console.log('[MAP] Marker', idx, 'position:', pos.lat, ',', pos.lng);
                                }
                            } catch(e) {
                                console.error('[MAP] Error checking marker', idx, ':', e);
                            }
                        });
                        
                        // If no markers visible but we should have some, try refreshing
                        if (visibleMarkers.length === 0 && stops.length > 0) {
                            console.warn('[MAP] ⚠ No markers visible but', stops.length, 'stops exist - attempting refresh');
                            if (typeof window.refreshMapStops === 'function') {
                                setTimeout(() => {
                                    console.log('[MAP] Attempting refreshMapStops as fallback');
                                    window.refreshMapStops();
                                }, 500);
                            }
                        } else if (visibleMarkers.length > 0) {
                            console.log('[MAP] ✓ Successfully created', visibleMarkers.length, 'markers');
                        }
                    }, 500);
                }, 100);
            };

            // Function to update button states based on map readiness
            function updateButtonStates() {
                document.querySelectorAll('.select-stop-btn').forEach(btn => {
                    if (mapReady && plannerMap) {
                        // Aggressively enable button - remove all disabled states
                        btn.disabled = false;
                        btn.removeAttribute('disabled');
                        btn.style.cursor = 'pointer';
                        btn.style.opacity = '1';
                        btn.style.pointerEvents = 'auto';
                        btn.title = 'Click to select this stop on the map';
                        
                        // If button is in selected state but selection is cleared, reset it
                        if (selectedStopIndex === null && (btn.classList.contains('bg-indigo-600') || btn.textContent.includes('Selected'))) {
                            btn.textContent = 'Select on Map';
                            btn.classList.remove('bg-indigo-600', 'text-white');
                            btn.classList.add('bg-indigo-50', 'text-indigo-700');
                        }
                    } else {
                        btn.disabled = true;
                        btn.style.cursor = 'not-allowed';
                        btn.style.opacity = '0.5';
                        btn.title = 'Please wait for the map to load';
                    }
                });
            }
            
            // Also create a function that can be called periodically
            window.ensureButtonsEnabled = function() {
                if (typeof mapReady !== 'undefined' && mapReady && typeof plannerMap !== 'undefined' && plannerMap) {
                    document.querySelectorAll('.select-stop-btn').forEach(btn => {
                        if (btn.disabled || btn.hasAttribute('disabled')) {
                            console.log('[BUTTON] Enabling disabled button:', btn.getAttribute('data-stop-index'));
                            btn.disabled = false;
                            btn.removeAttribute('disabled');
                            btn.style.cursor = 'pointer';
                            btn.style.opacity = '1';
                            btn.style.pointerEvents = 'auto';
                            btn.title = 'Click to select this stop on the map';
                        }
                    });
                }
            };
            
            // Make updateButtonStates globally accessible
            window.updateButtonStates = updateButtonStates;
            
            // Function to manually refresh stops from DOM and draw on map
            // FORCE REPLACE the stub with the full implementation
            console.log('[MAP] ========================================');
            console.log('[MAP] INSIDE IIFE - Replacing refreshMapStops stub');
            console.log('[MAP] Before replacement - type:', typeof window.refreshMapStops);
            const funcStrBefore = window.refreshMapStops ? window.refreshMapStops.toString().substring(0, 100) : 'undefined';
            console.log('[MAP] Before replacement - function preview:', funcStrBefore);
            
            // Delete the stub first to ensure replacement
            delete window.refreshMapStops;
            
            // Now define the full implementation
            window.refreshMapStops = function() {
                console.log('[MAP] ========================================');
                console.log('[MAP] refreshMapStops FULL IMPLEMENTATION called');
                console.log('[MAP] ========================================');
                
                if (!plannerMap || typeof mapboxgl === 'undefined') {
                    console.warn('[MAP] Map not ready, cannot refresh stops. plannerMap:', !!plannerMap, 'mapboxgl:', typeof mapboxgl);
                    // Retry after a delay
                    setTimeout(() => {
                        if (plannerMap && typeof mapboxgl !== 'undefined') {
                            console.log('[MAP] Retrying refreshMapStops after map is ready');
                            window.refreshMapStops();
                        }
                    }, 1000);
                    return;
                }
                
                if (!plannerMap.loaded()) {
                    console.warn('[MAP] Map not loaded yet, waiting...');
                    plannerMap.once('load', () => {
                        console.log('[MAP] Map loaded, retrying refreshMapStops');
                        window.refreshMapStops();
                    });
                    return;
                }
                
                const stops = [];
                const stopContainers = document.querySelectorAll('.stop-container');
                console.log('[MAP] Found', stopContainers.length, 'stop containers in DOM');
                
                stopContainers.forEach((container, idx) => {
                    try {
                        const latInput = document.getElementById('stop-latitude-' + idx);
                        const lngInput = document.getElementById('stop-longitude-' + idx);
                        const nameInput = document.getElementById('stop-name-' + idx);
                        
                        console.log('[MAP] Stop', idx, '- latInput:', !!latInput, 'lngInput:', !!lngInput, 'nameInput:', !!nameInput);
                        
                        const lat = latInput?.value?.trim() || '';
                        const lng = lngInput?.value?.trim() || '';
                        const name = nameInput?.value?.trim() || `Stop ${idx + 1}`;
                        
                        console.log('[MAP] Stop', idx, '- lat:', lat, 'lng:', lng, 'name:', name);
                        
                        if (lat && lng && !isNaN(parseFloat(lat)) && !isNaN(parseFloat(lng))) {
                            const latNum = parseFloat(lat);
                            const lngNum = parseFloat(lng);
                            
                            if (latNum >= -90 && latNum <= 90 && lngNum >= -180 && lngNum <= 180) {
                                const locationLabel = container.querySelector('input[wire\\:model*="location_label"]')?.value || '';
                                const stayDuration = container.querySelector('input[wire\\:model*="stay_duration_hours"]')?.value || '';
                                const notes = container.querySelector('textarea[wire\\:model*="notes"]')?.value || '';
                                
                                stops.push({
                                    latitude: latNum,
                                    longitude: lngNum,
                                    name: name,
                                    location_label: locationLabel,
                                    stay_duration_hours: stayDuration,
                                    notes: notes
                                });
                                
                                console.log('[MAP] ✓ Added stop', idx + 1, ':', name, 'at', latNum, ',', lngNum);
                            } else {
                                console.warn('[MAP] Stop', idx, 'has invalid coordinate range:', latNum, lngNum);
                            }
                        } else {
                            console.warn('[MAP] Stop', idx, 'missing or invalid coordinates');
                        }
                    } catch (err) {
                        console.error('[MAP] Error reading stop', idx, ':', err);
                    }
                });
                
                console.log('[MAP] Total stops collected:', stops.length);
                
                if (stops.length > 0) {
                    console.log('[MAP] Refreshing map with', stops.length, 'stops');
                    if (typeof window.drawPlannerStops === 'function') {
                        window.drawPlannerStops(stops, false);
                    } else {
                        console.error('[MAP] ✗ drawPlannerStops function not available');
                    }
                } else {
                    console.warn('[MAP] ⚠ No stops with valid coordinates found in DOM');
                }
            };
            
            // Make checkAndInit globally accessible for the callback - Mapbox version
            window.checkAndInit = function checkAndInit() {
                console.log('[MAP INIT] checkAndInit called');
                const mapEl = document.getElementById('route-map');
                if (!mapEl) {
                    console.log('[MAP INIT] Map element not found, retrying...');
                    setTimeout(checkAndInit, 100);
                    return;
                }
                
                console.log('[MAP INIT] Map element found, checking Mapbox availability...');
                
                // Ensure buttons are disabled initially
                updateButtonStates();
                
                if (typeof mapboxgl !== 'undefined') {
                    console.log('[MAP INIT] Mapbox GL JS is available, calling initPlannerMap...');
                    try {
                        if (typeof window.initPlannerMap === 'function') {
                            console.log('[MAP INIT] initPlannerMap function exists, calling it...');
                            window.initPlannerMap();
                        } else {
                            console.error('[MAP INIT] ✗ initPlannerMap function not found!');
                        }
                    } catch (error) {
                        console.error('[MAP INIT] ✗ Error initializing map:', error);
                        const loadingEl = document.getElementById('map-loading');
                        if (loadingEl) {
                            loadingEl.innerHTML = '<div class="text-center p-4"><p class="text-red-600 font-medium mb-2">Map Initialization Error</p><p class="text-sm text-gray-600">' + error.message + '</p></div>';
                        }
                        updateButtonStates();
                    }
                } else {
                    // Check if we've been waiting too long (15 seconds)
                    if (!window.mapInitStartTime) {
                        window.mapInitStartTime = Date.now();
                    }
                    
                    if (Date.now() - window.mapInitStartTime > 15000) {
                        // Timeout - show error
                        const loadingEl = document.getElementById('map-loading');
                        if (loadingEl) {
                            loadingEl.innerHTML = '<div class="text-center p-4"><p class="text-red-600 font-medium mb-2">Map Loading Timeout</p><p class="text-sm text-gray-600 mb-3">Mapbox GL JS is taking too long to load.</p><p class="text-xs text-gray-500 mb-3">Possible causes:</p><ul class="text-xs text-left text-gray-600 mb-3 space-y-1"><li>• Check your internet connection</li><li>• Check browser console for errors</li><li>• Verify MAPBOX_ACCESS_TOKEN is set in .env</li></ul><button onclick="location.reload()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Refresh Page</button></div>';
                        }
                        mapReady = false;
                        updateButtonStates();
                        return;
                    }
                    
                    setTimeout(checkAndInit, 100);
                }
            };
            
            // Initialize when Mapbox is ready - ensure proper sequencing
            function startMapInitialization() {
                console.log('[MAP INIT] Starting map initialization check...');
                window.mapInitStartTime = Date.now();
                if (document.readyState === 'loading') {
                    console.log('[MAP INIT] DOM is still loading, waiting for DOMContentLoaded');
                    document.addEventListener('DOMContentLoaded', function() {
                        console.log('[MAP INIT] DOMContentLoaded fired, checking map initialization');
                        setTimeout(checkAndInit, 100);
                    });
                } else {
                    console.log('[MAP INIT] DOM already loaded, checking map initialization immediately');
                    setTimeout(checkAndInit, 100);
                }
            }
            
            console.log('[MAP INIT] Checking if Mapbox GL JS is available...');
            console.log('[MAP INIT] typeof mapboxgl:', typeof mapboxgl);
            
            if (typeof mapboxgl !== 'undefined') {
                console.log('[MAP INIT] ✓ Mapbox GL JS already loaded, starting initialization');
                startMapInitialization();
            } else {
                // Start checking for Mapbox with polling
                console.log('[MAP INIT] Waiting for Mapbox GL JS to load...');
                let checkCount = 0;
                const maxChecks = 150; // 15 seconds
                const checkInterval = setInterval(function() {
                    checkCount++;
                    if (checkCount % 10 === 0) {
                        const checkNum = checkCount;
                        console.log('[MAP INIT] Still waiting for Mapbox GL JS... (check ' + checkNum + ')');
                    }
                    if (typeof mapboxgl !== 'undefined') {
                        clearInterval(checkInterval);
                        const finalCheckCount = checkCount;
                        console.log('[MAP INIT] ✓ Mapbox GL JS detected after ' + finalCheckCount + ' checks, starting initialization');
                        startMapInitialization();
                    } else if (checkCount >= maxChecks) {
                        clearInterval(checkInterval);
                        const failedChecks = maxChecks;
                        console.error('[MAP INIT] ✗ Mapbox GL JS failed to load after ' + failedChecks + ' checks');
                        const loadingEl = document.getElementById('map-loading');
                        if (loadingEl) {
                            loadingEl.innerHTML = '<div class="text-center p-4 max-w-md mx-auto"><p class="text-red-600 font-medium mb-2">Mapbox GL JS Failed to Load</p><p class="text-sm text-gray-700 mb-3">Please check your internet connection and refresh the page.</p><p class="text-xs text-gray-600 mb-3">Make sure Mapbox GL JS script is loaded in the page.</p><button onclick="location.reload()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Refresh Page</button></div>';
                        }
                    }
                }, 100);
                
                // Also try on DOMContentLoaded
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function() {
                        console.log('[MAP INIT] DOMContentLoaded fired, checking for Mapbox again');
                        if (typeof mapboxgl !== 'undefined') {
                            clearInterval(checkInterval);
                            console.log('[MAP INIT] ✓ Mapbox GL JS found on DOMContentLoaded');
                            startMapInitialization();
                        }
                    });
                }
            }

            // Listen for Livewire events - but don't show loading for coordinate updates
            if (typeof Livewire !== 'undefined') {
                // Watch for coordinate input changes - update marker directly without loading
                document.addEventListener('input', function(e) {
                    if (e.target.classList.contains('stop-coordinate-input')) {
                        // Hide loading immediately for coordinate updates
                        hideMapUpdating();
                        
                        const inputId = e.target.id;
                        const index = parseInt(inputId.replace('stop-latitude-', '').replace('stop-longitude-', ''));
                        if (!isNaN(index)) {
                            const latInput = document.getElementById('stop-latitude-' + index);
                            const lngInput = document.getElementById('stop-longitude-' + index);
                            
                            if (latInput && lngInput && latInput.value && lngInput.value) {
                                const lat = parseFloat(latInput.value);
                                const lng = parseFloat(lngInput.value);
                                
                                if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                                    // Update marker immediately without showing loading
                                    updateMarkerForStop(index, lat, lng);
                                }
                            }
                        }
                    }
                }, true);
                
                // Prevent loading overlay from showing during Livewire updates
                let isCoordinateUpdate = false;
                
                // Mark coordinate updates before Livewire processes them
                document.addEventListener('input', function(e) {
                    if (e.target.classList.contains('stop-coordinate-input')) {
                        isCoordinateUpdate = true;
                        setTimeout(() => {
                            isCoordinateUpdate = false;
                        }, 500);
                    }
                }, true);
                
                // Simplified morph.updated hook - minimal work, don't block Livewire
                document.addEventListener('livewire:init', () => {
                    Livewire.hook('morph.updated', ({ el, component }) => {
                        // Run completely asynchronously - don't block anything
                        setTimeout(() => {
                            try {
                                // Hide loading overlay if map is ready
                                if (typeof mapReady !== 'undefined' && mapReady && typeof plannerMap !== 'undefined' && plannerMap) {
                                    if (typeof window.forceHideMapLoading === 'function') {
                                        window.forceHideMapLoading();
                                    }
                                    // Also trigger the periodic check
                                    if (typeof checkAndHideLoading === 'function') {
                                        checkAndHideLoading();
                                    }
                                }
                                
                                // CRITICAL: Enable all buttons immediately, especially for newly added stops
                                // Do this BEFORE reattaching handlers to ensure buttons are enabled
                                if (typeof mapReady !== 'undefined' && mapReady && typeof plannerMap !== 'undefined' && plannerMap) {
                                    document.querySelectorAll('.select-stop-btn').forEach(btn => {
                                        // Remove disabled attribute and property
                                        btn.disabled = false;
                                        btn.removeAttribute('disabled');
                                        btn.style.cursor = 'pointer';
                                        btn.style.opacity = '1';
                                        btn.style.pointerEvents = 'auto';
                                        btn.title = 'Click to select this stop on the map';
                                        
                                        // Reset to default state if it's in selected state
                                        if (btn.classList.contains('bg-indigo-600') || btn.textContent.includes('Selected')) {
                                            btn.textContent = 'Select on Map';
                                            btn.classList.remove('bg-indigo-600', 'text-white');
                                            btn.classList.add('bg-indigo-50', 'text-indigo-700');
                                        }
                                    });
                                    console.log('[MAP] ✓ All Select on Map buttons enabled after Livewire update');
                                }
                                
                                // Reattach button handlers for new stops (do this after enabling)
                                if (typeof window.setupSelectButtonHandlers === 'function') {
                                    window.setupSelectButtonHandlers();
                                }
                                
                                // Update button states using centralized function (as backup)
                                if (typeof window.updateButtonStates === 'function') {
                                    window.updateButtonStates();
                                }
                        
                                // Use the global refreshMapStops function if available, otherwise use local function
                                const updateMapWithStops = () => {
                                    if (typeof window.refreshMapStops === 'function') {
                                        console.log('[MAP] Using refreshMapStops function to update map');
                                        window.refreshMapStops();
                                    } else {
                                        // Fallback to local implementation
                                        try {
                                            if (typeof window !== 'undefined' && window.skipLivewireRedraw) {
                                                return;
                                            }
                                            
                                            const stops = [];
                                            document.querySelectorAll('.stop-container').forEach((container, idx) => {
                                                try {
                                                    const latInput = document.getElementById('stop-latitude-' + idx);
                                                    const lngInput = document.getElementById('stop-longitude-' + idx);
                                                    const lat = latInput?.value?.trim() || '';
                                                    const lng = lngInput?.value?.trim() || '';
                                                    
                                                    if (lat && lng && !isNaN(parseFloat(lat)) && !isNaN(parseFloat(lng))) {
                                                        const stopName = document.getElementById('stop-name-' + idx)?.value?.trim() || `Stop ${idx + 1}`;
                                                        stops.push({
                                                            latitude: parseFloat(lat),
                                                            longitude: parseFloat(lng),
                                                            name: stopName,
                                                            location_label: container.querySelector('input[wire\\:model*="location_label"]')?.value || '',
                                                            stay_duration_hours: container.querySelector('input[wire\\:model*="stay_duration_hours"]')?.value || '',
                                                            notes: container.querySelector('textarea[wire\\:model*="notes"]')?.value || ''
                                                        });
                                                    }
                                                } catch (err) {
                                                    console.warn('[MAP] Error reading stop data:', err);
                                                }
                                            });
                                            
                                            if (stops.length > 0 && typeof window.drawPlannerStops === 'function') {
                                                console.log('[MAP] Drawing', stops.length, 'stops on map after Livewire update');
                                                window.drawPlannerStops(stops, false);
                                            } else if (stops.length === 0) {
                                                console.log('[MAP] No stops with coordinates found to draw');
                                            }
                                        } catch (err) {
                                            console.error('[MAP] Error updating map after Livewire update:', err);
                                        }
                                    }
                                };
                                
                                // Update map if needed - but do it asynchronously with delay
                                setTimeout(updateMapWithStops, 500); // First attempt
                                setTimeout(updateMapWithStops, 1500); // Second attempt in case DOM not ready
                                setTimeout(updateMapWithStops, 3000); // Final attempt
                            } catch (err) {
                                // Silently ignore errors - don't block Livewire
                            }
                        }, 0);
                    });
                }
            }
        })();
        
        // CRITICAL: Define refreshMapStops OUTSIDE IIFE to ensure it's always available
        // This MUST execute immediately
        try {
            console.log('[MAP] ========================================');
            console.log('[MAP] ===== DEFINING refreshMapStops OUTSIDE IIFE =====');
            console.log('[MAP] ========================================');
            console.log('[MAP] Current refreshMapStops before delete:', typeof window.refreshMapStops);
        
        // Delete stub first to ensure clean replacement
        if (window.refreshMapStops) {
            const oldFunc = window.refreshMapStops.toString().substring(0, 100);
            console.log('[MAP] Deleting old function (preview):', oldFunc);
            delete window.refreshMapStops;
        }
        
        // Define full implementation in global scope - FORCE assignment
        window.refreshMapStops = function refreshMapStopsGlobal() {
            console.log('[MAP] ========================================');
            console.log('[MAP] refreshMapStops FULL IMPLEMENTATION (GLOBAL) called');
            console.log('[MAP] ========================================');
            
            if (!plannerMap || typeof mapboxgl === 'undefined') {
                console.warn('[MAP] Map not ready, cannot refresh stops. plannerMap:', !!plannerMap, 'mapboxgl:', typeof mapboxgl);
                setTimeout(() => {
                    if (plannerMap && typeof mapboxgl !== 'undefined') {
                        console.log('[MAP] Retrying refreshMapStops after map is ready');
                        window.refreshMapStops();
                    }
                }, 1000);
                return;
            }
            
            if (!plannerMap.loaded()) {
                console.warn('[MAP] Map not loaded yet, waiting...');
                plannerMap.once('load', () => {
                    console.log('[MAP] Map loaded, retrying refreshMapStops');
                    window.refreshMapStops();
                });
                return;
            }
            
            const stops = [];
            const stopContainers = document.querySelectorAll('.stop-container');
            console.log('[MAP] Found', stopContainers.length, 'stop containers in DOM');
            
            stopContainers.forEach((container, idx) => {
                try {
                    const latInput = document.getElementById('stop-latitude-' + idx);
                    const lngInput = document.getElementById('stop-longitude-' + idx);
                    const nameInput = document.getElementById('stop-name-' + idx);
                    
                    const lat = latInput?.value?.trim() || '';
                    const lng = lngInput?.value?.trim() || '';
                    const name = nameInput?.value?.trim() || `Stop ${idx + 1}`;
                    
                    if (lat && lng && !isNaN(parseFloat(lat)) && !isNaN(parseFloat(lng))) {
                        const latNum = parseFloat(lat);
                        const lngNum = parseFloat(lng);
                        
                        if (latNum >= -90 && latNum <= 90 && lngNum >= -180 && lngNum <= 180) {
                            const locationLabel = container.querySelector('input[wire\\:model*="location_label"]')?.value || '';
                            const stayDuration = container.querySelector('input[wire\\:model*="stay_duration_hours"]')?.value || '';
                            const notes = container.querySelector('textarea[wire\\:model*="notes"]')?.value || '';
                            
                            stops.push({
                                latitude: latNum,
                                longitude: lngNum,
                                name: name,
                                location_label: locationLabel,
                                stay_duration_hours: stayDuration,
                                notes: notes
                            });
                            
                            console.log('[MAP] ✓ Added stop', idx + 1, ':', name, 'at', latNum, ',', lngNum);
                        }
                    }
                } catch (err) {
                    console.error('[MAP] Error reading stop', idx, ':', err);
                }
            });
            
            console.log('[MAP] Total stops collected:', stops.length);
            
            if (stops.length > 0) {
                console.log('[MAP] Refreshing map with', stops.length, 'stops');
                if (typeof window.drawPlannerStops === 'function') {
                    window.drawPlannerStops(stops, false);
                } else {
                    console.error('[MAP] ✗ drawPlannerStops function not available');
                }
            } else {
                console.warn('[MAP] ⚠ No stops with valid coordinates found in DOM');
            }
        };
        
        console.log('[MAP] ✓ refreshMapStops defined in global scope');
        console.log('[MAP] refreshMapStops type:', typeof window.refreshMapStops);
        
        // Verify it's not a stub
        const funcStr = window.refreshMapStops.toString();
        const isStub = funcStr.includes('stub') || funcStr.includes('placeholder') || funcStr.includes('waiting for full');
        console.log('[MAP] refreshMapStops is stub:', isStub);
        if (isStub) {
            console.error('[MAP] ✗ ERROR: refreshMapStops is still a stub after definition!');
            console.error('[MAP] Function preview:', funcStr.substring(0, 200));
        } else {
            console.log('[MAP] ✓ SUCCESS: refreshMapStops is the full implementation');
        }
        
        // Force call after a delay to test
        setTimeout(() => {
            if (typeof mapReady !== 'undefined' && mapReady) {
                console.log('[MAP] Testing refreshMapStops after 2 seconds');
                try {
                    window.refreshMapStops();
                } catch(e) {
                    console.error('[MAP] Error calling refreshMapStops:', e);
                }
            }
        }, 2000);
        } catch(e) {
            console.error('[MAP] ✗ ERROR defining refreshMapStops outside IIFE:', e);
            console.error('[MAP] Error stack:', e.stack);
        }
    </script>
@endpush

