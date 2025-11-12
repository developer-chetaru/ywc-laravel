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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-4">
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
                                <option value="private">Private - Only you</option>
                                <option value="crew">Crew - Shared with crew members</option>
                                <option value="public">Public - Visible to everyone</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select wire:model.defer="form.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="draft">Draft - Work in progress</option>
                                <option value="active">Active - Currently sailing</option>
                                <option value="completed">Completed - Journey finished</option>
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

                        @foreach($stops as $index => $stop)
                            <div wire:key="stop-{{ $index }}" class="border border-gray-200 rounded-lg p-4 bg-gray-50 space-y-3">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-semibold text-gray-700">Stop {{ $index + 1 }}</h3>
                                    <button type="button" wire:click="removeStop({{ $index }})" class="text-red-500 text-sm hover:underline">Remove</button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Stop Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" wire:model.defer="stops.{{ $index }}.name" 
                                            placeholder="e.g., Mumbai Port, Goa Marina"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-gray-400">
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
                                        <input type="text" wire:model.live="stops.{{ $index }}.latitude" 
                                            placeholder="e.g., 19.07598 (Mumbai)"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-gray-400">
                                        <p class="text-xs text-gray-500 mt-1">Examples: Mumbai (19.07598), Goa (15.2993), Monaco (43.7384)</p>
                                        @error("stops.$index.latitude") <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Longitude
                                            <span class="text-xs font-normal text-gray-500 ml-1">(-180 to 180)</span>
                                        </label>
                                        <input type="text" wire:model.live="stops.{{ $index }}.longitude" 
                                            placeholder="e.g., 72.87766 (Mumbai)"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-gray-400">
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

                <div class="space-y-4">
                    <div class="bg-gray-100 border border-gray-200 rounded-lg h-80 relative overflow-hidden" id="route-map" style="min-height: 320px;">
                        <div class="absolute inset-0 flex items-center justify-center text-gray-500 text-sm z-10 bg-gray-100" id="map-loading">
                            <div class="text-center">
                                <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p>Loading map...</p>
                            </div>
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-90 z-20 hidden" id="map-updating">
                            <div class="text-center">
                                <svg class="animate-spin h-6 w-6 text-indigo-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-sm text-gray-600">Updating map...</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg p-4 space-y-2 shadow-sm">
                        <h3 class="text-sm font-semibold text-gray-700">Route Summary</h3>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p><span class="font-medium text-gray-800">Total Stops:</span> {{ count($stops) }}</p>
                            @if($route)
                                <p><span class="font-medium text-gray-800">Total Distance:</span> {{ number_format($route->distance_nm, 2) }} NM</p>
                                <p><span class="font-medium text-gray-800">Average Leg:</span> {{ number_format($route->average_leg_nm, 2) }} NM</p>
                                <p><span class="font-medium text-gray-800">Status:</span> {{ ucfirst($route->status) }}</p>
                            @else
                                <p class="text-gray-500">Distance metrics available after saving.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="anonymous" />
    <style>
        #route-map {
            z-index: 1;
        }
        .leaflet-container {
            height: 100%;
            width: 100%;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin="anonymous"></script>
    <script>
        (function() {
            let map = null;
            let markers = [];
            let polyline = null;

            function initMap() {
                const mapEl = document.getElementById('route-map');
                if (!mapEl || typeof L === 'undefined') {
                    setTimeout(initMap, 100);
                    return;
                }

                // Clear loading message
                const loadingEl = document.getElementById('map-loading');
                if (loadingEl) {
                    loadingEl.style.display = 'none';
                }

                map = L.map(mapEl, { 
                    zoomControl: true,
                    preferCanvas: true
                }).setView([20, 0], 2);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19
                }).addTo(map);

                drawStops(@json($stops));
            }

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

            function drawStops(stops = []) {
                if (!map || typeof L === 'undefined') return;

                showMapUpdating();

                // Use setTimeout to allow UI to update
                setTimeout(() => {
                    // Remove existing markers and polyline
                    markers.forEach(marker => map.removeLayer(marker));
                    markers = [];
                    if (polyline) {
                        map.removeLayer(polyline);
                        polyline = null;
                    }

                    const latLngs = [];
                    stops.forEach((stop, index) => {
                        if (stop.latitude && stop.longitude) {
                            const lat = parseFloat(stop.latitude);
                            const lng = parseFloat(stop.longitude);
                            if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                                const marker = L.marker([lat, lng])
                                    .bindPopup(`<strong>${stop.name || 'Stop ' + (index + 1)}</strong><br>${stop.location_label || ''}`)
                                    .addTo(map);
                                markers.push(marker);
                                latLngs.push([lat, lng]);
                            }
                        }
                    });

                    if (latLngs.length > 1) {
                        polyline = L.polyline(latLngs, { 
                            color: '#4f46e5', 
                            weight: 3, 
                            opacity: 0.8 
                        }).addTo(map);
                    }

                    if (latLngs.length > 0) {
                        const bounds = L.latLngBounds(latLngs);
                        map.fitBounds(bounds, { padding: [30, 30] });
                    } else {
                        map.setView([20, 0], 2);
                    }

                    hideMapUpdating();
                }, 100);
            }

            // Initialize when DOM and Leaflet are ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initMap);
            } else {
                initMap();
            }

            // Listen for Livewire events
            if (typeof Livewire !== 'undefined') {
                document.addEventListener('livewire:init', () => {
                    Livewire.on('stops-updated', (data) => {
                        // Handle Livewire 3 event format
                        const stops = data?.stops || (Array.isArray(data) ? data[0]?.stops : null) || [];
                        if (stops && Array.isArray(stops)) {
                            drawStops(stops);
                        }
                    });
                });

                // Also listen after Livewire is loaded
                Livewire.on('stops-updated', (data) => {
                    const stops = data?.stops || (Array.isArray(data) ? data[0]?.stops : null) || [];
                    if (stops && Array.isArray(stops)) {
                        drawStops(stops);
                    }
                });
            }
        })();
    </script>
@endpush

