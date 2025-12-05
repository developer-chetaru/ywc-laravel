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
                            <button type="button" wire:click="addStop"
                                class="inline-flex items-center px-3 py-2 bg-indigo-100 text-indigo-700 text-sm font-semibold rounded-md hover:bg-indigo-200">
                                + Add Stop
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
                            <div class="relative" style="height: 500px;" id="route-map">
                                <div class="absolute inset-0 flex items-center justify-center text-gray-500 text-sm z-10 bg-gray-50" id="map-loading">
                                    <div class="text-center">
                                        <svg class="animate-spin h-10 w-10 text-indigo-600 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <p class="text-gray-700 font-medium">Loading map...</p>
                                    </div>
                                </div>
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

                        @foreach($stops as $index => $stop)
                            <div wire:key="stop-{{ $index }}" class="border border-gray-200 rounded-lg p-4 bg-gray-50 space-y-3 stop-container" data-stop-index="{{ $index }}">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <button type="button" 
                                            class="select-stop-btn px-3 py-1 text-xs font-medium rounded-md border border-indigo-300 text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors"
                                            data-stop-index="{{ $index }}">
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
            border-radius: 0 0 0.5rem 0.5rem;
        }
        
        /* Google Maps Custom Styling */
        .gm-style .gm-style-cc {
            display: none;
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
    </style>
@endpush

@push('scripts')
    <script>
        // Global callback for Google Maps initialization
        window.initGoogleMaps = function() {
            // This will be called when Google Maps API loads successfully
            if (typeof window.onGoogleMapsReady === 'function') {
                window.onGoogleMapsReady();
            }
        };
        
        // Error handler for Google Maps API authentication failures
        window.gm_authFailure = function() {
            const loadingEl = document.getElementById('map-loading');
            if (loadingEl) {
                loadingEl.innerHTML = '<div class="text-center p-4"><p class="text-red-600 font-medium mb-2">Google Maps API Authentication Error</p><p class="text-sm text-gray-600 mb-2">Please check your API key configuration.</p><p class="text-xs text-gray-500">Make sure the API key is valid and has the Maps JavaScript API enabled.</p></div>';
            }
        };
        
        // Error handler for billing and other Google Maps errors
        window.addEventListener('error', function(e) {
            if (e.message && e.message.includes('BillingNotEnabledMapError')) {
                e.preventDefault();
                const loadingEl = document.getElementById('map-loading');
                if (loadingEl) {
                    loadingEl.innerHTML = '<div class="text-center p-4 max-w-md mx-auto"><p class="text-red-600 font-medium mb-2">Google Maps Billing Not Enabled</p><p class="text-sm text-gray-700 mb-3">Billing must be enabled for your Google Cloud project to use Google Maps.</p><div class="text-left bg-gray-50 p-3 rounded text-xs text-gray-600 space-y-1"><p><strong>To fix this:</strong></p><ol class="list-decimal list-inside space-y-1 ml-2"><li>Go to <a href="https://console.cloud.google.com/" target="_blank" class="text-blue-600 hover:underline">Google Cloud Console</a></li><li>Select your project</li><li>Enable billing for the project</li><li>Enable the Maps JavaScript API</li><li>Refresh this page</li></ol></div></div>';
                }
            }
        }, true);
        
        // Also listen for console errors (Google Maps errors are often logged there)
        const originalConsoleError = console.error;
        console.error = function(...args) {
            originalConsoleError.apply(console, args);
            const errorMsg = args.join(' ');
            if (errorMsg.includes('BillingNotEnabledMapError') || errorMsg.includes('billing-not-enabled')) {
                const loadingEl = document.getElementById('map-loading');
                if (loadingEl && !loadingEl.innerHTML.includes('Billing Not Enabled')) {
                    loadingEl.innerHTML = '<div class="text-center p-4 max-w-md mx-auto"><p class="text-red-600 font-medium mb-2">Google Maps Billing Not Enabled</p><p class="text-sm text-gray-700 mb-3">Billing must be enabled for your Google Cloud project to use Google Maps.</p><div class="text-left bg-gray-50 p-3 rounded text-xs text-gray-600 space-y-1"><p><strong>To fix this:</strong></p><ol class="list-decimal list-inside space-y-1 ml-2"><li>Go to <a href="https://console.cloud.google.com/" target="_blank" class="text-blue-600 hover:underline">Google Cloud Console</a></li><li>Select your project</li><li>Enable billing for the project</li><li>Enable the Maps JavaScript API</li><li>Refresh this page</li></ol></div></div>';
                }
            }
        };
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAGxLMNvIuc8XpG21cOF3VkxbK1EkTpuzQ&libraries=places,geometry,marker&callback=initGoogleMaps" async defer></script>
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

        (function() {
            let map = null;
            let markers = [];
            let polyline = null;
            let selectedStopIndex = null;
            let clickMarker = null;

            function initMap() {
                const mapEl = document.getElementById('route-map');
                if (!mapEl) {
                    setTimeout(initMap, 100);
                    return;
                }

                // Wait for Google Maps API to load
                if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                    setTimeout(initMap, 100);
                    return;
                }

                // Clear loading message
                const loadingEl = document.getElementById('map-loading');
                if (loadingEl) {
                    loadingEl.style.display = 'none';
                }

                // Initialize Google Map with custom styling
                // Note: mapId is required for Advanced Markers. Create one in Google Cloud Console:
                // 1. Go to Google Cloud Console > Maps > Map Styles
                // 2. Create a new map style or use default
                // 3. Copy the Map ID and add it here or in .env as GOOGLE_MAPS_MAP_ID
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
                    mapTypeControlOptions: {
                        style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                        position: google.maps.ControlPosition.TOP_RIGHT
                    },
                    zoomControlOptions: {
                        position: google.maps.ControlPosition.RIGHT_CENTER
                    },
                    styles: [
                        {
                            featureType: 'water',
                            elementType: 'geometry',
                            stylers: [{ color: '#e9e9e9' }, { lightness: 17 }]
                        },
                        {
                            featureType: 'landscape',
                            elementType: 'geometry',
                            stylers: [{ color: '#f5f5f5' }, { lightness: 20 }]
                        },
                        {
                            featureType: 'road.highway',
                            elementType: 'geometry.fill',
                            stylers: [{ color: '#ffffff' }, { lightness: 17 }]
                        },
                        {
                            featureType: 'road.highway',
                            elementType: 'geometry.stroke',
                            stylers: [{ color: '#ffffff' }, { lightness: 29 }, { weight: 0.2 }]
                        }
                    ]
                };
                
                // Add Map ID if available (required for Advanced Markers)
                // You can set this in .env as GOOGLE_MAPS_MAP_ID or create one in Google Cloud Console
                // To create a Map ID: Google Cloud Console > Maps > Map Styles > Create new style
                const mapId = @json(config('services.google_maps.map_id', null));
                if (mapId) {
                    mapConfig.mapId = mapId;
                    console.log('Map ID loaded:', mapId);
                } else {
                    // If no Map ID is provided, we'll fall back to old Marker API
                    // Advanced Markers require a Map ID, so we'll use regular markers instead
                    console.warn('No Map ID provided. Advanced Markers will not be available. Using standard markers instead.');
                }
                
                try {
                    map = new google.maps.Map(mapEl, mapConfig);
                    console.log('Google Map instance created successfully');
                    
                    // Add a listener to detect when map is ready
                    google.maps.event.addListenerOnce(map, 'idle', function() {
                        console.log('Map is fully loaded and ready');
                        const loadingEl = document.getElementById('map-loading');
                        if (loadingEl) {
                            loadingEl.style.display = 'none';
                        }
                    });
                    
                    drawStops(@json($stops));
                } catch (mapError) {
                    console.error('Error creating map instance:', mapError);
                    const loadingEl = document.getElementById('map-loading');
                    if (loadingEl) {
                        let errorMsg = mapError.message || 'Failed to initialize map';
                        if (errorMsg.includes('BillingNotEnabled') || errorMsg.includes('billing')) {
                            errorMsg = 'Billing Not Enabled - Please enable billing in Google Cloud Console';
                        } else if (errorMsg.includes('InvalidKeyMapError') || errorMsg.includes('RefererNotAllowedMapError')) {
                            errorMsg = 'API Key Error - Please check your Google Maps API key configuration';
                        }
                        loadingEl.innerHTML = '<div class="text-center p-4"><p class="text-red-600 font-medium mb-2">Map Error</p><p class="text-sm text-gray-600">' + errorMsg + '</p></div>';
                    }
                    throw mapError;
                }
                
                // Add click listener to map for selecting stops
                map.addListener('click', function(event) {
                    if (selectedStopIndex !== null) {
                        // Hide loading overlay immediately
                        hideMapUpdating();
                        
                        const lat = event.latLng.lat();
                        const lng = event.latLng.lng();
                        
                        // Update the selected stop's coordinates
                        updateStopCoordinates(selectedStopIndex, lat, lng);
                        
                        // Remove temporary click marker
                        if (clickMarker) {
                            clickMarker.setMap(null);
                        }
                        
                        // Add temporary marker to show where user clicked
                        const hasMapId = map && map.mapId;
                        if (typeof google.maps.marker !== 'undefined' && 
                            google.maps.marker.AdvancedMarkerElement && 
                            hasMapId) {
                            const pinElement = new google.maps.marker.PinElement({
                                background: '#10b981',
                                borderColor: '#ffffff',
                                scale: 1.2
                            });
                            
                            clickMarker = new google.maps.marker.AdvancedMarkerElement({
                                position: event.latLng,
                                map: map,
                                content: pinElement.element,
                                zIndex: 10000
                            });
                            
                            // Remove marker after 2 seconds with fade effect
                            setTimeout(() => {
                                if (clickMarker) {
                                    clickMarker.map = null;
                                    clickMarker = null;
                                }
                            }, 2000);
                        } else {
                            // Fallback to old Marker API if AdvancedMarkerElement not available
                            clickMarker = new google.maps.Marker({
                                position: event.latLng,
                                map: map,
                                icon: {
                                    path: google.maps.SymbolPath.CIRCLE,
                                    scale: 10,
                                    fillColor: '#10b981',
                                    fillOpacity: 0.8,
                                    strokeColor: '#ffffff',
                                    strokeWeight: 3
                                },
                                animation: google.maps.Animation.BOUNCE,
                                zIndex: 10000
                            });
                            
                            setTimeout(() => {
                                if (clickMarker) {
                                    clickMarker.setAnimation(null);
                                }
                            }, 2000);
                        }
                    }
                });
            }
            
            function updateMarkerForStop(index, lat, lng) {
                // Find and update the existing marker for this stop
                const marker = markers[index];
                if (marker && marker !== null) {
                    const newPosition = { lat: lat, lng: lng };
                    
                    // Update marker position (works for both AdvancedMarkerElement and old Marker)
                    if (marker.position) {
                        // AdvancedMarkerElement
                        marker.position = newPosition;
                    } else if (marker.setPosition) {
                        // Old Marker API
                        marker.setPosition(newPosition);
                    }
                    
                    // Update marker title/label if possible
                    const stopNameInput = document.getElementById('stop-name-' + index);
                    const stopName = stopNameInput ? stopNameInput.value : `Stop ${index + 1}`;
                    if (marker.title !== undefined) {
                        marker.title = stopName;
                    } else if (marker.setTitle) {
                        marker.setTitle(stopName);
                    }
                    
                    // Update polyline with all valid marker positions
                    const validMarkers = markers.filter(m => m !== null && m !== undefined);
                    if (validMarkers.length > 1) {
                        const latLngs = validMarkers.map(m => {
                            if (m.position) {
                                // AdvancedMarkerElement
                                return m.position;
                            } else if (m.getPosition) {
                                // Old Marker API
                                return m.getPosition();
                            }
                            return null;
                        }).filter(p => p !== null);
                        
                        if (latLngs.length > 1) {
                            if (polyline) {
                                polyline.setPath(latLngs);
                            } else {
                                polyline = new google.maps.Polyline({
                                    path: latLngs,
                                    geodesic: true,
                                    strokeColor: '#4f46e5',
                                    strokeOpacity: 0.7,
                                    strokeWeight: 4,
                                    icons: [{
                                        icon: {
                                            path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                                            scale: 4,
                                            strokeColor: '#4f46e5',
                                            fillColor: '#4f46e5',
                                            fillOpacity: 1
                                        },
                                        offset: '100%',
                                        repeat: '100px'
                                    }],
                                    map: map
                                });
                            }
                        }
                    }
                    
                    // Smoothly pan to updated marker (optional - can be removed if too jarring)
                    // map.panTo(newPosition);
                } else {
                    // Marker doesn't exist yet, need to redraw all stops
                    // But do it without showing loading
                    setTimeout(() => {
                        const stops = [];
                        document.querySelectorAll('.stop-container').forEach((container, idx) => {
                            const latInput = document.getElementById('stop-latitude-' + idx);
                            const lngInput = document.getElementById('stop-longitude-' + idx);
                            if (latInput && lngInput && latInput.value && lngInput.value) {
                                stops.push({
                                    latitude: latInput.value,
                                    longitude: lngInput.value,
                                    name: document.getElementById('stop-name-' + idx)?.value || `Stop ${idx + 1}`,
                                    location_label: container.querySelector('input[wire\\:model*="location_label"]')?.value || '',
                                    stay_duration_hours: container.querySelector('input[wire\\:model*="stay_duration_hours"]')?.value || '',
                                    notes: container.querySelector('textarea[wire\\:model*="notes"]')?.value || ''
                                });
                            }
                        });
                        drawStops(stops, false); // Don't show loading
                    }, 100);
                }
            }
            
            function updateStopCoordinates(index, lat, lng) {
                // Hide loading overlay immediately
                hideMapUpdating();
                
                const latInput = document.getElementById('stop-latitude-' + index);
                const lngInput = document.getElementById('stop-longitude-' + index);
                
                if (latInput && lngInput) {
                    // Update input values first
                    latInput.value = lat.toFixed(6);
                    lngInput.value = lng.toFixed(6);
                    
                    // Update marker immediately (visual feedback)
                    updateMarkerForStop(index, lat, lng);
                    
                    // Mark as coordinate update to prevent loading
                    if (typeof window !== 'undefined') {
                        window.isCoordinateUpdate = true;
                        setTimeout(() => {
                            window.isCoordinateUpdate = false;
                        }, 1000);
                    }
                    
                    // Trigger Livewire update using the event system (safer than direct component.set)
                    // This will call the updateStopCoordinates method in RoutePlanner component
                    setTimeout(() => {
                        if (typeof Livewire !== 'undefined') {
                            // Use Livewire event to update coordinates (this is the safe way)
                            Livewire.dispatch('update-stop-coordinates', {
                                index: index,
                                latitude: lat.toFixed(6),
                                longitude: lng.toFixed(6)
                            });
                        }
                        
                        // Also trigger input events for wire:model.defer to sync
                        latInput.dispatchEvent(new Event('input', { bubbles: true }));
                        lngInput.dispatchEvent(new Event('input', { bubbles: true }));
                        latInput.dispatchEvent(new Event('change', { bubbles: true }));
                        lngInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }, 50);
                }
            }
            
            // Handle "Select on Map" button clicks
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('select-stop-btn')) {
                    const index = parseInt(e.target.getAttribute('data-stop-index'));
                    selectedStopIndex = index;
                    
                    // Update UI to show which stop is selected
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
                        indicator.classList.remove('hidden');
                    }
                    
                    // Update button text
                    document.querySelectorAll('.select-stop-btn').forEach(btn => {
                        btn.textContent = 'Select on Map';
                        btn.classList.remove('bg-indigo-600', 'text-white');
                        btn.classList.add('bg-indigo-50', 'text-indigo-700');
                    });
                    e.target.textContent = 'Selected - Click Map';
                    e.target.classList.remove('bg-indigo-50', 'text-indigo-700');
                    e.target.classList.add('bg-indigo-600', 'text-white');
                }
            });

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

            function drawStops(stops = [], showLoading = true) {
                if (!map || typeof google === 'undefined' || typeof google.maps === 'undefined') return;

                if (showLoading) {
                    showMapUpdating();
                }

                // Use setTimeout to allow UI to update
                setTimeout(() => {
                    // Remove existing markers (works for both AdvancedMarkerElement and old Marker)
                    markers.forEach(marker => {
                        if (marker) {
                            if (marker.map !== undefined) {
                                // AdvancedMarkerElement
                                marker.map = null;
                            } else if (marker.setMap) {
                                // Old Marker API
                                marker.setMap(null);
                            }
                        }
                    });
                    markers = [];
                    
                    // Remove existing polyline
                    if (polyline) {
                        polyline.setMap(null);
                        polyline = null;
                    }

                    const latLngs = [];
                    const distances = [];
                    
                    stops.forEach((stop, index) => {
                        if (stop.latitude && stop.longitude) {
                            const lat = parseFloat(stop.latitude);
                            const lng = parseFloat(stop.longitude);
                            if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                                const position = { lat: lat, lng: lng };
                                
                                // Calculate distance from previous stop
                                let distanceText = '';
                                if (latLngs.length > 0 && typeof google.maps.geometry !== 'undefined') {
                                    const prevPosition = latLngs[latLngs.length - 1];
                                    const distance = google.maps.geometry.spherical.computeDistanceBetween(
                                        new google.maps.LatLng(prevPosition.lat, prevPosition.lng),
                                        new google.maps.LatLng(position.lat, position.lng)
                                    );
                                    const distanceNm = (distance / 1852).toFixed(2); // Convert meters to nautical miles
                                    distances.push(parseFloat(distanceNm));
                                    distanceText = `<div class="text-xs text-gray-500 mt-1">Distance from previous: ${distanceNm} NM</div>`;
                                }
                                
                                // Create marker using AdvancedMarkerElement (new API)
                                let marker;
                                const stopName = stop.name || 'Stop ' + (index + 1);
                                const markerColor = index === 0 ? '#10b981' : (index === stops.length - 1 ? '#ef4444' : '#4f46e5');
                                
                                // Check if Advanced Markers are available and map has a Map ID
                                const hasMapId = map && map.mapId;
                                if (typeof google.maps.marker !== 'undefined' && 
                                    google.maps.marker.AdvancedMarkerElement && 
                                    hasMapId) {
                                    // Use new AdvancedMarkerElement API
                                    const pinElement = new google.maps.marker.PinElement({
                                        background: markerColor,
                                        borderColor: '#ffffff',
                                        scale: 1.2,
                                        glyphText: String(index + 1),
                                        glyphColor: '#ffffff'
                                    });
                                    
                                    marker = new google.maps.marker.AdvancedMarkerElement({
                                        position: position,
                                        map: map,
                                        content: pinElement.element,
                                        title: stopName,
                                        zIndex: 1000 - index
                                    });
                                } else {
                                    // Fallback to old Marker API
                                    const markerIcon = {
                                        path: google.maps.SymbolPath.CIRCLE,
                                        scale: 12,
                                        fillColor: markerColor,
                                        fillOpacity: 1,
                                        strokeColor: '#ffffff',
                                        strokeWeight: 3
                                    };
                                    
                                    marker = new google.maps.Marker({
                                        position: position,
                                        map: map,
                                        title: stopName,
                                        label: {
                                            text: String(index + 1),
                                            color: 'white',
                                            fontWeight: 'bold',
                                            fontSize: '12px'
                                        },
                                        icon: markerIcon,
                                        animation: google.maps.Animation.DROP,
                                        zIndex: 1000 - index
                                    });
                                }

                                // Create enhanced info window
                                const stayDuration = stop.stay_duration_hours ? `${stop.stay_duration_hours} hours` : 'Not specified';
                                const infoContent = `
                                    <div style="padding: 12px; min-width: 200px;">
                                        <div class="flex items-center mb-2">
                                            <div style="width: 8px; height: 8px; background: ${index === 0 ? '#10b981' : (index === stops.length - 1 ? '#ef4444' : '#4f46e5')}; border-radius: 50%; margin-right: 8px;"></div>
                                            <strong style="font-size: 14px; color: #111827;">${stop.name || 'Stop ' + (index + 1)}</strong>
                                        </div>
                                        ${stop.location_label ? `<div class="text-xs text-gray-600 mb-1">📍 ${stop.location_label}</div>` : ''}
                                        <div class="text-xs text-gray-500 mb-1">⏱️ Stay: ${stayDuration}</div>
                                        ${distanceText}
                                        ${stop.notes ? `<div class="text-xs text-gray-600 mt-2 pt-2 border-t border-gray-200">${stop.notes}</div>` : ''}
                                    </div>
                                `;
                                
                                const infoWindow = new google.maps.InfoWindow({
                                    content: infoContent
                                });

                                // Add click listener for info window (works for both marker types)
                                marker.addListener('click', () => {
                                    // Close all other info windows
                                    markers.forEach(m => {
                                        if (m && m.infoWindow) {
                                            m.infoWindow.close();
                                        }
                                    });
                                    marker.infoWindow = infoWindow;
                                    
                                    // Open info window (different methods for different marker types)
                                    if (marker.position) {
                                        // AdvancedMarkerElement - use position directly
                                        infoWindow.open({
                                            anchor: marker,
                                            map: map
                                        });
                                    } else {
                                        // Old Marker API
                                        infoWindow.open(map, marker);
                                    }
                                });

                                // Store marker at index position for easy updates
                                while (markers.length <= index) {
                                    markers.push(null);
                                }
                                markers[index] = marker;
                                latLngs.push(position);
                            }
                        }
                    });

                    // Draw polyline if we have multiple stops
                    if (latLngs.length > 1) {
                        // Convert positions to LatLng objects if needed
                        const path = latLngs.map(pos => {
                            if (pos instanceof google.maps.LatLng) {
                                return pos;
                            }
                            return new google.maps.LatLng(pos.lat, pos.lng);
                        });
                        
                        polyline = new google.maps.Polyline({
                            path: path,
                            geodesic: true,
                            strokeColor: '#4f46e5',
                            strokeOpacity: 0.7,
                            strokeWeight: 4,
                            icons: [{
                                icon: {
                                    path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                                    scale: 4,
                                    strokeColor: '#4f46e5',
                                    fillColor: '#4f46e5',
                                    fillOpacity: 1
                                },
                                offset: '100%',
                                repeat: '100px'
                            }]
                        });
                        polyline.setMap(map);
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
                    
                    if (legendEl && latLngs.length > 0) {
                        legendEl.style.display = 'block';
                        if (legendStopsEl) legendStopsEl.textContent = latLngs.length;
                        if (legendDistanceEl) {
                            legendDistanceEl.textContent = totalDistance > 0 ? totalDistance.toFixed(2) + ' NM' : 'Calculating...';
                        }
                    } else if (legendEl) {
                        legendEl.style.display = 'none';
                    }

                    // Fit bounds to show all markers
                    if (latLngs.length > 0) {
                        const bounds = new google.maps.LatLngBounds();
                        latLngs.forEach(latLng => bounds.extend(latLng));
                        map.fitBounds(bounds, { padding: 50 });
                    } else {
                        map.setCenter({ lat: 20, lng: 0 });
                        map.setZoom(2);
                    }

                    hideMapUpdating();
                }, 100);
            }

            // Initialize when DOM and Google Maps are ready
            function checkAndInit() {
                const mapEl = document.getElementById('route-map');
                if (!mapEl) {
                    setTimeout(checkAndInit, 100);
                    return;
                }
                
                if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
                    try {
                        initMap();
                    } catch (error) {
                        console.error('Error initializing map:', error);
                        const loadingEl = document.getElementById('map-loading');
                        if (loadingEl) {
                            let errorMsg = error.message || 'Unknown error';
                            if (errorMsg.includes('BillingNotEnabled') || errorMsg.includes('billing')) {
                                errorMsg = 'Billing Not Enabled - Please enable billing in Google Cloud Console';
                            }
                            loadingEl.innerHTML = '<div class="text-center p-4"><p class="text-red-600 font-medium mb-2">Map Initialization Error</p><p class="text-sm text-gray-600">' + errorMsg + '</p></div>';
                        }
                    }
                } else {
                    // Check if we've been waiting too long (10 seconds)
                    if (!window.mapInitStartTime) {
                        window.mapInitStartTime = Date.now();
                    }
                    
                    if (Date.now() - window.mapInitStartTime > 10000) {
                        // Timeout - show error
                        const loadingEl = document.getElementById('map-loading');
                        if (loadingEl) {
                            loadingEl.innerHTML = '<div class="text-center p-4"><p class="text-red-600 font-medium mb-2">Map Loading Timeout</p><p class="text-sm text-gray-600">Google Maps API is taking too long to load. Please refresh the page.</p></div>';
                        }
                        return;
                    }
                    
                    setTimeout(checkAndInit, 100);
                }
            }
            
            // Set up callback for when Google Maps API loads
            window.onGoogleMapsReady = function() {
                window.mapInitStartTime = Date.now();
                setTimeout(checkAndInit, 100);
            };
            
            // Check if Google Maps is already loaded (in case script loaded before this code)
            if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
                window.mapInitStartTime = Date.now();
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', checkAndInit);
                } else {
                    setTimeout(checkAndInit, 100);
                }
            } else {
                // Start checking periodically
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', checkAndInit);
                } else {
                    checkAndInit();
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
                
                // Only redraw with loading when structure changes (stops added/removed)
                document.addEventListener('livewire:init', () => {
                    Livewire.hook('morph.updated', ({ el, component }) => {
                        // Always hide loading for coordinate updates
                        if (isCoordinateUpdate || (typeof window !== 'undefined' && window.isCoordinateUpdate)) {
                            hideMapUpdating();
                            return;
                        }
                        
                        // Check if this is just a coordinate update
                        const isCoordinateOnly = el.classList && el.classList.contains('stop-coordinate-input');
                        const isCoordinateParent = el.querySelector && el.querySelector('.stop-coordinate-input');
                        
                        if (isCoordinateOnly || isCoordinateParent) {
                            hideMapUpdating();
                            return;
                        }
                        
                        // Check if the updated element is inside a stop container (likely coordinate update)
                        const stopContainer = el.closest && el.closest('.stop-container');
                        if (stopContainer) {
                            const hasCoordinateInputs = stopContainer.querySelector('.stop-coordinate-input');
                            if (hasCoordinateInputs) {
                                hideMapUpdating();
                                return;
                            }
                        }
                        
                        // Structural change - redraw but without loading for smooth UX
                        setTimeout(() => {
                            const stops = [];
                            document.querySelectorAll('.stop-container').forEach((container, idx) => {
                                const latInput = document.getElementById('stop-latitude-' + idx);
                                const lngInput = document.getElementById('stop-longitude-' + idx);
                                if (latInput && lngInput && latInput.value && lngInput.value) {
                                    stops.push({
                                        latitude: latInput.value,
                                        longitude: lngInput.value,
                                        name: document.getElementById('stop-name-' + idx)?.value || `Stop ${idx + 1}`,
                                        location_label: container.querySelector('input[wire\\:model*="location_label"]')?.value || '',
                                        stay_duration_hours: container.querySelector('input[wire\\:model*="stay_duration_hours"]')?.value || '',
                                        notes: container.querySelector('textarea[wire\\:model*="notes"]')?.value || ''
                                    });
                                }
                            });
                            if (stops.length > 0) {
                                drawStops(stops, false); // Don't show loading
                            }
                        }, 100);
                    });
                });
            }
        })();
    </script>
@endpush

