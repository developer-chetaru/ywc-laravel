<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Crew Discovery</h1>
            <p class="text-gray-600 mt-2">Find and connect with nearby yacht crew members</p>
        </div>

        @if($error)
            <div class="bg-red-50 border-l-4 border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fa-solid fa-exclamation-circle mr-2"></i>{{ $error }}
            </div>
        @endif

        @if($alert)
            <div class="bg-green-50 border-l-4 border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <i class="fa-solid fa-check-circle mr-2"></i>{{ $alert }}
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-900">
                <i class="fa-solid fa-filter mr-2"></i>Search Filters
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fa-solid fa-ruler mr-1"></i>Distance (km)
                    </label>
                    <select wire:model.live="radius" 
                        class="w-full px-4 py-2 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF]">
                        <option value="all">All Locations</option>
                        <option value="1">1 km</option>
                        <option value="5">5 km</option>
                        <option value="10">10 km</option>
                        <option value="50">50 km</option>
                        <option value="100">100 km</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fa-solid fa-briefcase mr-1"></i>Position
                    </label>
                    <input type="text" wire:model.live.debounce.300ms="position" 
                        placeholder="Captain, Chief Stew..." 
                        class="w-full px-4 py-2 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fa-solid fa-star mr-1"></i>Experience
                    </label>
                    <select wire:model.live="experience_level" 
                        class="w-full px-4 py-2 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF]">
                        <option value="">All</option>
                        <option value="new">New (0-2 years)</option>
                        <option value="intermediate">Intermediate (2-5 years)</option>
                        <option value="experienced">Experienced (5-10 years)</option>
                        <option value="senior">Senior (10+ years)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fa-solid fa-circle-check mr-1"></i>Status
                    </label>
                    <select wire:model.live="status" 
                        class="w-full px-4 py-2 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF]">
                        <option value="">All</option>
                        <option value="online">🟢 Online Now</option>
                        <option value="available">✅ Available</option>
                        <option value="looking_for_work">💼 Looking for Work</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fa-solid fa-magnifying-glass mr-1"></i>Search
                    </label>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                        placeholder="Name, email, location..." 
                        class="w-full px-4 py-2 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF]">
                </div>
            </div>

            <div class="mt-4 flex gap-2">
                <button wire:click="$set('view', 'list')" 
                    class="px-4 py-2 rounded-lg transition {{ $view === 'list' ? 'bg-[#0053FF] text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    <i class="fa-solid fa-list mr-2"></i>List View
                </button>
                <button wire:click="$set('view', 'map')" 
                    class="px-4 py-2 rounded-lg transition {{ $view === 'map' ? 'bg-[#0053FF] text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    <i class="fa-solid fa-map mr-2"></i>Map View
                </button>
            </div>
        </div>

        <!-- Online Crew Section -->
        @if(count($onlineCrew) > 0)
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-900">
                    <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-2"></span>Online Now
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach($onlineCrew as $crew)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md cursor-pointer transition" 
                            wire:click="showProfile({{ $crew['id'] }})">
                            <div class="flex items-center gap-3">
                                <img src="{{ $crew['profile_photo_url'] ?? '/default-avatar.png' }}" 
                                    alt="{{ $crew['name'] }}" 
                                    class="w-12 h-12 rounded-full border-2 border-green-500">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-sm truncate">{{ $crew['name'] }}</p>
                                    <p class="text-xs text-gray-600 truncate">{{ $crew['position'] }}</p>
                                    @if($crew['distance'])
                                        <p class="text-xs text-gray-500">{{ $crew['distance'] }} km away</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($view === 'map')
            <!-- Map View -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">
                            Crew Locations Map <span class="text-[#0053FF]">({{ count($allCrew) }})</span>
                            <span class="text-sm font-normal text-gray-500 ml-2" id="last-update">
                                <i class="fa-solid fa-clock mr-1"></i>Updating every minute...
                            </span>
                        </h2>
                        <div class="flex items-center gap-2">
                            @if(!$latitude || !$longitude)
                                <button onclick="requestUserLocation()" 
                                    class="px-4 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-[#0046CC] transition text-sm">
                                    <i class="fa-solid fa-location-crosshairs mr-1"></i>Set My Location
                                </button>
                            @else
                                <button onclick="toggleContinuousTracking()" 
                                    id="tracking-toggle-btn"
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm">
                                    <i class="fa-solid fa-location-dot mr-1"></i><span id="tracking-status">Enable Auto-Update</span>
                                </button>
                            @endif
                        </div>
                    </div>
                    <div wire:ignore id="crew-map" class="w-full h-[600px] rounded-lg border border-gray-200 relative overflow-hidden bg-gray-100">
                        <div class="absolute inset-0 flex items-center justify-center z-10 transition-opacity duration-300" id="map-loading">
                            <div class="text-center">
                                <svg class="animate-spin h-8 w-8 text-[#0053FF] mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-gray-600">Loading map...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Crew List -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-900">
                        Nearby Crew <span class="text-[#0053FF]">({{ count($nearbyCrew) }})</span>
                    </h2>

                @if(count($nearbyCrew) > 0)
                    <div class="space-y-4">
                        @foreach($nearbyCrew as $crew)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition cursor-pointer" 
                                wire:click="showProfile({{ $crew['id'] }})">
                                <div class="flex items-start gap-4">
                                    <img src="{{ $crew['profile_photo_url'] ?? '/default-avatar.png' }}" 
                                        alt="{{ $crew['name'] }}" 
                                        class="w-16 h-16 rounded-full border-2 border-[#0053FF]">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 class="font-semibold text-lg text-gray-900">
                                                    {{ $crew['name'] }}
                                                    @if(isset($crew['is_self']) && $crew['is_self'])
                                                        <span class="text-[#0053FF] text-sm font-normal">(You)</span>
                                                    @endif
                                                </h3>
                                                <p class="text-gray-600">{{ $crew['position'] }}</p>
                                            </div>
                                            <div class="text-right">
                                                @if($crew['distance'])
                                                    <p class="font-medium text-[#0053FF]">{{ $crew['distance'] }} km</p>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="mt-2 flex flex-wrap gap-2 text-sm">
                                            @if($crew['years_experience'])
                                                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded">
                                                    <i class="fa-solid fa-star mr-1"></i>{{ $crew['years_experience'] }} years
                                                </span>
                                            @endif
                                            @if($crew['is_online'])
                                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded">
                                                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-1"></span>Online
                                                </span>
                                            @endif
                                            @if($crew['looking_to_meet'])
                                                <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded">
                                                    <i class="fa-solid fa-handshake mr-1"></i>Looking to meet
                                                </span>
                                            @endif
                                        </div>

                                        @if($crew['availability_message'])
                                            <p class="text-sm text-gray-600 mt-2">
                                                <i class="fa-solid fa-comment mr-1"></i>{{ $crew['availability_message'] }}
                                            </p>
                                        @endif

                                        <div class="mt-3 flex gap-2">
                                            @if(!isset($crew['is_self']) || !$crew['is_self'])
                                                <button wire:click.stop="showProfile({{ $crew['id'] }})" 
                                                    class="px-4 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-[#0046CC] transition">
                                                    <i class="fa-solid fa-user mr-1"></i>View Profile
                                                </button>
                                                @if($crew['connection_status'] === 'accepted')
                                                    <span class="px-4 py-2 bg-green-100 text-green-700 rounded-lg">
                                                        <i class="fa-solid fa-check-circle mr-1"></i>Connected
                                                    </span>
                                                @elseif($crew['connection_status'] === 'pending')
                                                    <span class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg">
                                                        <i class="fa-solid fa-clock mr-1"></i>Request Sent
                                                    </span>
                                                @else
                                                    <button wire:click.stop="sendConnectionRequest({{ $crew['id'] }})" 
                                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                                        <i class="fa-solid fa-user-plus mr-1"></i>Connect
                                                    </button>
                                                @endif
                                            @else
                                                <span class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg">
                                                    <i class="fa-solid fa-user mr-1"></i>This is you
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fa-solid fa-users-slash text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">No crew members found nearby. Try adjusting your filters.</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Profile Modal -->
        @if($showProfileModal && $selectedUser)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" 
                wire:click="closeProfileModal">
                <div class="bg-white rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto shadow-2xl" 
                    wire:click.stop>
                    <div class="flex justify-between items-center mb-4 pb-4 border-b">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $selectedUser->name }}</h2>
                        <button wire:click="closeProfileModal" 
                            class="text-gray-500 hover:text-gray-700 text-xl">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <img src="{{ $selectedUser->profile_photo_url ?? '/default-avatar.png' }}" 
                                alt="{{ $selectedUser->name }}" 
                                class="w-24 h-24 rounded-full border-4 border-[#0053FF]">
                            <div>
                                <p class="font-semibold text-lg">{{ $selectedUser->roles->pluck('name')->first() }}</p>
                                <p class="text-gray-600">
                                    <i class="fa-solid fa-star mr-1"></i>{{ $selectedUser->years_experience }} years experience
                                </p>
                                @if($selectedUser->current_yacht)
                                    <p class="text-gray-600">
                                        <i class="fa-solid fa-ship mr-1"></i>{{ $selectedUser->current_yacht }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        @if($selectedUser->languages)
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">
                                    <i class="fa-solid fa-language mr-1"></i>Languages
                                </h3>
                                <p class="text-gray-700">{{ implode(', ', $selectedUser->languages) }}</p>
                            </div>
                        @endif

                        @if($selectedUser->availability_message)
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">
                                    <i class="fa-solid fa-info-circle mr-1"></i>Status
                                </h3>
                                <p class="text-gray-700">{{ $selectedUser->availability_message }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="anonymous"/>
<style>
    #crew-map { z-index: 1; }
    .leaflet-popup-content-wrapper { border-radius: 8px; }
    .crew-marker-online { border: 3px solid #10b981 !important; }
    .crew-marker-offline { border: 3px solid #6b7280 !important; }
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.9; }
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin="anonymous"></script>
<script>
    (function() {
        let map = null;
        let markers = [];
        let updateInterval = null;
        let isUpdating = false;
        
        // Store initial crew data from Livewire (with safe defaults)
        let currentCrewData = {!! json_encode($allCrew ?? []) !!};
        let currentView = {!! json_encode($view ?? 'list') !!};
        let currentRadius = {!! json_encode($radius ?? 'all') !!};
        let currentPosition = {!! json_encode($position ?? '') !!};
        let currentExperienceLevel = {!! json_encode($experience_level ?? '') !!};
        let currentStatus = {!! json_encode($status ?? '') !!};
        let currentSearch = {!! json_encode($search ?? '') !!};
        let currentLatitude = {!! json_encode($latitude ?? null) !!};
        let currentLongitude = {!! json_encode($longitude ?? null) !!};

        function initMap() {
            const mapEl = document.getElementById('crew-map');
            
            // Check prerequisites
            if (!mapEl) {
                console.log('Map element not found, retrying...');
                setTimeout(initMap, 100);
                return;
            }
            
            if (typeof L === 'undefined') {
                console.log('Leaflet not loaded, retrying...');
                setTimeout(initMap, 100);
                return;
            }

            // Destroy existing map if it exists
            if (map) {
                try {
                    map.remove();
                    map = null;
                    markers = [];
                } catch (e) {
                    console.error('Error removing existing map:', e);
                }
            }

            // Hide loading immediately
            const loadingEl = document.getElementById('map-loading');
            if (loadingEl) {
                loadingEl.style.opacity = '0';
                setTimeout(() => {
                    loadingEl.style.display = 'none';
                }, 100);
            }

            try {
                // Initialize map immediately
                map = L.map(mapEl, { 
                    zoomControl: true,
                    preferCanvas: true,
                    loadingControl: false
                }).setView([20, 0], 2);

                // Add tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19
                }).addTo(map);

                // Force map to invalidate size after a short delay (ensures proper rendering)
                setTimeout(() => {
                    if (map) {
                        map.invalidateSize();
                    }
                }, 100);

                // Load crew locations from Livewire data first (instant)
                if (currentCrewData && Array.isArray(currentCrewData) && currentCrewData.length > 0) {
                    updateMapMarkers(currentCrewData);
                }
                
                // Then refresh data
                setTimeout(() => loadCrewLocations(), 500);
                startPolling();
            } catch (e) {
                console.error('Error initializing map:', e);
                // Show error message
                if (loadingEl) {
                    loadingEl.style.display = 'flex';
                    loadingEl.innerHTML = '<div class="text-center"><p class="text-red-600">Error loading map. Please refresh the page.</p></div>';
                }
            }
        }

        function loadCrewLocations() {
            if (isUpdating) return;
            isUpdating = true;

            const updateEl = document.getElementById('last-update');
            if (updateEl) {
                updateEl.innerHTML = '<i class="fa-solid fa-sync fa-spin mr-1"></i>Updating...';
            }

            // Try to get user's current location from browser if not set
            let userLat = currentLatitude;
            let userLng = currentLongitude;
            
            // If no location, try to get from browser (but don't auto-save)
            if (!userLat || !userLng) {
                // Just use for distance calculation, don't auto-save
                makeApiCall(null, null);
            } else {
                makeApiCall(userLat, userLng);
            }
        }

        function makeApiCall(userLat, userLng) {
            // Use stored filter values (updated via Livewire events)
            const filters = {
                radius: currentRadius === 'all' ? null : currentRadius,
                position: currentPosition,
                experience_level: currentExperienceLevel,
                status: currentStatus,
                search: currentSearch,
                latitude: userLat || currentLatitude,
                longitude: userLng || currentLongitude,
            };

            // Build query string
            const params = new URLSearchParams();
            if (filters.radius) params.append('radius', filters.radius);
            if (filters.position) params.append('position', filters.position);
            if (filters.experience_level) params.append('experience_level', filters.experience_level);
            if (filters.status) params.append('status', filters.status);
            if (filters.search) params.append('search', filters.search);
            if (filters.latitude) params.append('latitude', filters.latitude);
            if (filters.longitude) params.append('longitude', filters.longitude);

            // Use fetch with timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout

            const updateEl = document.getElementById('last-update');

            // Use Livewire to refresh data (avoids API auth issues)
            @this.call('refreshCrewData');
            
            // The map will be updated via the 'crew-data-updated' event listener
            // This avoids timing issues with JavaScript directive
            isUpdating = false;
            
            return; // Skip the fetch call below
            
            // OLD API CALL - Keeping for reference but not used
            fetch(`/api/crew-discovery/all-locations?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                credentials: 'same-origin',
                signal: controller.signal
            })
            .then(response => {
                clearTimeout(timeoutId);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status && data.data && data.data.crew) {
                    updateMapMarkers(data.data.crew);
                    if (updateEl) {
                        const now = new Date();
                        updateEl.innerHTML = `<i class="fa-solid fa-clock mr-1"></i>Last updated: ${now.toLocaleTimeString()}`;
                    }
                } else {
                    console.warn('No crew data received:', data);
                    // Still hide loading even if no data
                    const loadingEl = document.getElementById('map-loading');
                    if (loadingEl) {
                        loadingEl.style.opacity = '0';
                        setTimeout(() => {
                            loadingEl.style.display = 'none';
                        }, 300);
                    }
                    if (updateEl) {
                        updateEl.innerHTML = '<i class="fa-solid fa-info-circle mr-1"></i>No locations found';
                    }
                }
                isUpdating = false;
            })
            .catch(error => {
                clearTimeout(timeoutId);
                if (error.name !== 'AbortError') {
                    console.error('Error loading crew locations:', error);
                }
                isUpdating = false;
                
                // Hide loading overlay even on error
                const loadingEl = document.getElementById('map-loading');
                if (loadingEl) {
                    loadingEl.style.opacity = '0';
                    setTimeout(() => {
                        loadingEl.style.display = 'none';
                    }, 300);
                }
                
                if (updateEl) {
                    updateEl.innerHTML = '<i class="fa-solid fa-exclamation-triangle mr-1"></i>Update failed';
                }
            });
        }

        function updateMapMarkers(crew) {
            if (!map) return;

            // Hide loading overlay if still visible
            const loadingEl = document.getElementById('map-loading');
            if (loadingEl) {
                loadingEl.style.opacity = '0';
                setTimeout(() => {
                    loadingEl.style.display = 'none';
                }, 300);
            }

            // Remove existing markers efficiently
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];

            const latLngs = [];
            const markerPromises = [];
            
            crew.forEach(crewMember => {
                if (crewMember.latitude && crewMember.longitude) {
                    const lat = parseFloat(crewMember.latitude);
                    const lng = parseFloat(crewMember.longitude);
                    
                    if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                        // Create custom icon - different style for self
                        let iconColor, borderWidth, iconSize, imgSize;
                        if (crewMember.is_self) {
                            iconColor = '#0053FF';
                            borderWidth = '4px';
                            iconSize = '48px';
                            imgSize = '40px';
                        } else {
                            iconColor = crewMember.is_online ? '#10b981' : '#6b7280';
                            borderWidth = '3px';
                            iconSize = '40px';
                            imgSize = '32px';
                        }
                        
                        const iconHtml = `
                            <div style="
                                width: ${iconSize};
                                height: ${iconSize};
                                border-radius: 50%;
                                background: white;
                                border: ${borderWidth} solid ${iconColor};
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                box-shadow: 0 2px 6px rgba(0,0,0,0.3);
                                ${crewMember.is_self ? 'animation: pulse 2s infinite;' : ''}
                            ">
                                <img src="${crewMember.profile_photo_url || '/default-avatar.png'}" 
                                     style="width: ${imgSize}; height: ${imgSize}; border-radius: 50%; object-fit: cover;"
                                     onerror="this.src='/default-avatar.png'">
                            </div>
                        `;
                        
                        const customIcon = L.divIcon({
                            html: iconHtml,
                            className: 'crew-marker',
                            iconSize: crewMember.is_self ? [48, 48] : [40, 40],
                            iconAnchor: crewMember.is_self ? [24, 24] : [20, 20],
                            popupAnchor: [0, crewMember.is_self ? -24 : -20]
                        });

                        // Format last active time
                        let lastActiveText = '';
                        if (crewMember.is_online) {
                            lastActiveText = '<p style="margin: 4px 0; color: #10b981; font-size: 0.875rem;"><span style="display: inline-block; width: 8px; height: 8px; background: #10b981; border-radius: 50%; margin-right: 4px;"></span>Online now</p>';
                        } else if (crewMember.last_seen_at) {
                            const lastSeen = new Date(crewMember.last_seen_at);
                            const now = new Date();
                            const diffMs = now - lastSeen;
                            const diffMins = Math.floor(diffMs / 60000);
                            const diffHours = Math.floor(diffMs / 3600000);
                            const diffDays = Math.floor(diffMs / 86400000);
                            
                            let timeAgo = '';
                            if (diffMins < 1) {
                                timeAgo = 'Just now';
                            } else if (diffMins < 60) {
                                timeAgo = `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
                            } else if (diffHours < 24) {
                                timeAgo = `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
                            } else if (diffDays < 7) {
                                timeAgo = `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
                            } else {
                                timeAgo = lastSeen.toLocaleDateString();
                            }
                            
                            lastActiveText = `<p style="margin: 4px 0; color: #6b7280; font-size: 0.875rem;"><i class="fa-solid fa-clock"></i> Last active: ${timeAgo}</p>`;
                        }
                        
                        // Format location update time
                        let locationUpdateText = '';
                        if (crewMember.location_updated_at) {
                            try {
                                // Parse ISO 8601 date string
                                const locationUpdated = new Date(crewMember.location_updated_at);
                                
                                // Validate date
                                if (isNaN(locationUpdated.getTime())) {
                                    locationUpdateText = '';
                                } else {
                                    const now = new Date();
                                    const diffMs = now.getTime() - locationUpdated.getTime();
                                    
                                    // Handle negative differences (future dates) or very large differences (invalid dates)
                                    if (diffMs < 0 || diffMs > 86400000 * 365) {
                                        locationUpdateText = '';
                                    } else {
                                        const diffMins = Math.floor(diffMs / 60000);
                                        const diffHours = Math.floor(diffMs / 3600000);
                                        const diffDays = Math.floor(diffMs / 86400000);
                                        
                                        let locationTimeAgo = '';
                                        if (diffMins < 1) {
                                            locationTimeAgo = 'Just now';
                                        } else if (diffMins < 60) {
                                            locationTimeAgo = `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
                                        } else if (diffHours < 24) {
                                            locationTimeAgo = `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
                                        } else if (diffDays < 7) {
                                            locationTimeAgo = `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
                                        } else {
                                            locationTimeAgo = locationUpdated.toLocaleDateString() + ' ' + locationUpdated.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                                        }
                                        
                                        locationUpdateText = `<p style="margin: 4px 0; color: #6b7280; font-size: 0.875rem;"><i class="fa-solid fa-map-marker-alt"></i> Location updated: ${locationTimeAgo}</p>`;
                                    }
                                }
                            } catch (e) {
                                console.error('Error parsing location_updated_at:', e, crewMember.location_updated_at);
                                locationUpdateText = '';
                            }
                        }
                        
                        const popupContent = `
                            <div style="min-width: 200px;">
                                <div class="flex items-center gap-3 mb-2">
                                    <img src="${crewMember.profile_photo_url || '/default-avatar.png'}" 
                                         style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;"
                                         onerror="this.src='/default-avatar.png'">
                                    <div>
                                        <strong>${crewMember.name}${crewMember.is_self ? ' (You)' : ''}</strong>
                                        <p style="margin: 0; color: #6b7280; font-size: 0.875rem;">${crewMember.position || 'Crew Member'}</p>
                                    </div>
                                </div>
                                ${crewMember.location_name ? `<p style="margin: 4px 0; color: #6b7280; font-size: 0.875rem;"><i class="fa-solid fa-location-dot"></i> ${crewMember.location_name}</p>` : ''}
                                ${locationUpdateText}
                                ${crewMember.distance && !crewMember.is_self ? `<p style="margin: 4px 0; color: #0053FF; font-size: 0.875rem;"><i class="fa-solid fa-ruler"></i> ${crewMember.distance} km away</p>` : ''}
                                ${lastActiveText}
                                ${!crewMember.is_self ? `<button onclick="window.dispatchEvent(new CustomEvent('show-profile', {detail: {userId: ${crewMember.id}}}))" 
                                        style="margin-top: 8px; padding: 6px 12px; background: #0053FF; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.875rem;">
                                    View Profile
                                </button>` : ''}
                            </div>
                        `;

                        const marker = L.marker([lat, lng], { icon: customIcon })
                            .bindPopup(popupContent)
                            .addTo(map);
                        
                        markers.push(marker);
                        latLngs.push([lat, lng]);
                    }
                }
            });

            // Fit map to show all markers (use requestAnimationFrame for smooth update)
            requestAnimationFrame(() => {
                if (latLngs.length > 0) {
                    const bounds = L.latLngBounds(latLngs);
                    map.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });
                } else {
                    map.setView([20, 0], 2);
                }
            });
        }

        function startPolling() {
            // Poll every 1 minute (60000 ms) - refresh Livewire data
            updateInterval = setInterval(() => {
                if (document.getElementById('crew-map') && currentView === 'map') {
                    // Refresh data via Livewire (will trigger 'crew-data-updated' event)
                    @this.call('refreshCrewData');
                }
            }, 60000);
        }

        function stopPolling() {
            if (updateInterval) {
                clearInterval(updateInterval);
                updateInterval = null;
            }
        }

        // Helper function to get component
        function getComponent() {
            try {
                return Livewire.find('{{ $this->getId() }}');
            } catch (e) {
                return null;
            }
        }
        
        // Listen for Livewire updates
        document.addEventListener('livewire:init', () => {
            // Listen for crew data updated event
            Livewire.on('crew-data-updated', () => {
                if (map && currentView === 'map') {
                    // Get fresh data from Livewire component (synchronous in Livewire v3)
                    setTimeout(() => {
                        try {
                            const component = getComponent();
                            if (component) {
                                const crewData = component.get('allCrew');
                                if (crewData && Array.isArray(crewData) && crewData.length > 0) {
                                    currentCrewData = crewData;
                                    updateMapMarkers(crewData);
                                    const updateEl = document.getElementById('last-update');
                                    if (updateEl) {
                                        const now = new Date();
                                        updateEl.innerHTML = `<i class="fa-solid fa-clock mr-1"></i>Last updated: ${now.toLocaleTimeString()}`;
                                    }
                                } else {
                                    // Fallback: use stored data
                                    if (currentCrewData && currentCrewData.length > 0) {
                                        updateMapMarkers(currentCrewData);
                                    }
                                }
                            } else {
                                // Fallback: use stored data
                                if (currentCrewData && currentCrewData.length > 0) {
                                    updateMapMarkers(currentCrewData);
                                }
                            }
                        } catch (e) {
                            console.error('Error getting crew data:', e);
                            // Fallback: use stored data
                            if (currentCrewData && currentCrewData.length > 0) {
                                updateMapMarkers(currentCrewData);
                            }
                        }
                    }, 300);
                }
            });
            
            // Listen for when elements are added (after morphing)
            Livewire.hook('morph.added', ({ el }) => {
                if (el.id === 'crew-map' || el.querySelector('#crew-map')) {
                    // Map container was added, initialize map
                    setTimeout(() => {
                        if (currentView === 'map') {
                            checkAndInitMap();
                        }
                    }, 100);
                }
            });
            
            // Listen for view property changes
            Livewire.hook('morph.updated', ({ el }) => {
                // Check if view buttons were clicked
                const viewButtons = el.querySelectorAll('[wire\\:click*="view"]');
                if (viewButtons.length > 0) {
                    setTimeout(() => {
                        const component = getComponent();
                        if (component) {
                            const newView = component.get('view');
                            if (newView === 'map' && currentView !== 'map') {
                                currentView = 'map';
                                // Wait for DOM to update
                                setTimeout(() => {
                                    checkAndInitMap();
                                }, 300);
                            } else if (newView !== 'map' && currentView === 'map') {
                                currentView = newView;
                                stopPolling();
                                // Destroy map when switching away
                                if (map) {
                                    try {
                                        map.remove();
                                        map = null;
                                        markers = [];
                                    } catch (e) {
                                        console.error('Error removing map:', e);
                                    }
                                }
                            }
                        }
                    }, 150);
                }
                
                // Update crew data if map exists
                if (map && currentView === 'map') {
                    try {
                        const component = getComponent();
                        if (component) {
                            const data = component.get('allCrew');
                            currentCrewData = (data && Array.isArray(data)) ? data : [];
                            if (currentCrewData.length > 0) {
                                updateMapMarkers(currentCrewData);
                            }
                        }
                    } catch (e) {
                        console.error('Error updating map data:', e);
                    }
                }
            });
        });

        // Listen for view changes
        window.addEventListener('livewire:navigated', () => {
            try {
                const component = getComponent();
                if (component) {
                    currentView = component.get('view') || currentView;
                    if (currentView === 'map') {
                        if (!map) {
                            checkAndInitMap();
                        } else {
                            // Use stored data for instant display
                            if (currentCrewData && currentCrewData.length > 0) {
                                updateMapMarkers(currentCrewData);
                            }
                        }
                    } else {
                        stopPolling();
                    }
                }
            } catch (e) {
                console.error('Error getting view:', e);
            }
        });

        // Listen for profile show event
        window.addEventListener('show-profile', (event) => {
            @this.call('showProfile', event.detail.userId);
        });

        // Initialize map immediately when page loads
        function checkAndInitMap() {
            if (currentView !== 'map') {
                return;
            }
            
            const mapEl = document.getElementById('crew-map');
            if (!mapEl) {
                // Map element doesn't exist yet, retry
                setTimeout(checkAndInitMap, 200);
                return;
            }
            
            // Check if map is already initialized
            if (map && mapEl._leaflet_id) {
                // Map already exists, just update it
                try {
                    map.invalidateSize();
                    if (currentCrewData && currentCrewData.length > 0) {
                        updateMapMarkers(currentCrewData);
                    }
                } catch (e) {
                    console.error('Error updating existing map:', e);
                }
                return;
            }
            
            // Check if Leaflet is loaded
            if (typeof L === 'undefined') {
                // Wait for Leaflet to load
                let attempts = 0;
                const checkLeaflet = setInterval(() => {
                    attempts++;
                    if (typeof L !== 'undefined') {
                        clearInterval(checkLeaflet);
                        initMap();
                    } else if (attempts > 60) { // 3 seconds timeout
                        clearInterval(checkLeaflet);
                        console.error('Leaflet failed to load after 3 seconds');
                        const loadingEl = document.getElementById('map-loading');
                        if (loadingEl) {
                            loadingEl.innerHTML = '<div class="text-center"><p class="text-red-600">Failed to load map library. Please refresh the page.</p></div>';
                        }
                    }
                }, 50);
            } else {
                // Leaflet is loaded, initialize map
                initMap();
            }
        }

        // Initialize on page load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(checkAndInitMap, 300);
            });
        } else {
            setTimeout(checkAndInitMap, 300);
        }
        
        // Also check when Livewire finishes loading
        document.addEventListener('livewire:init', () => {
            setTimeout(checkAndInitMap, 400);
        });
        
        // Check when Livewire finishes initial render
        document.addEventListener('livewire:load', () => {
            setTimeout(checkAndInitMap, 500);
        });

        // Continuous location tracking
        let locationWatchId = null;
        let isTrackingEnabled = false;
        let locationUpdateInterval = null;

        // Function to update location
        function updateLocation(position = null) {
            return new Promise((resolve, reject) => {
                if (position) {
                    // Use provided position
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    @this.call('updateMyLocation', lat, lng, 'Current Location')
                        .then(() => {
                            resolve();
                        })
                        .catch((error) => {
                            console.error('Error updating location:', error);
                            reject(error);
                        });
                } else {
                    // Get current position
                    if (!navigator.geolocation) {
                        reject(new Error('Geolocation is not supported'));
                        return;
                    }
                    
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            const lat = pos.coords.latitude;
                            const lng = pos.coords.longitude;
                            
                            @this.call('updateMyLocation', lat, lng, 'Current Location')
                                .then(() => {
                                    resolve();
                                })
                                .catch((error) => {
                                    console.error('Error updating location:', error);
                                    reject(error);
                                });
                        },
                        (error) => {
                            console.error('Geolocation error:', error);
                            reject(error);
                        },
                        { timeout: 10000, enableHighAccuracy: true, maximumAge: 0 }
                    );
                }
            });
        }

        // Function to request user location (one-time)
        function requestUserLocation() {
            if (navigator.geolocation) {
                const updateEl = document.getElementById('last-update');
                if (updateEl) {
                    updateEl.innerHTML = '<i class="fa-solid fa-sync fa-spin mr-1"></i>Getting your location...';
                }
                
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        updateLocation(position)
                            .then(() => {
                                // Reload map after a short delay
                                setTimeout(() => {
                                    loadCrewLocations();
                                }, 500);
                                
                                if (updateEl) {
                                    updateEl.innerHTML = '<i class="fa-solid fa-check-circle mr-1"></i>Location updated';
                                }
                                
                                // Show option to enable continuous tracking
                                setTimeout(() => {
                                    if (updateEl) {
                                        updateEl.innerHTML = '<i class="fa-solid fa-clock mr-1"></i>Updating every minute...';
                                    }
                                }, 2000);
                            })
                            .catch((error) => {
                                console.error('Error updating location:', error);
                                if (updateEl) {
                                    updateEl.innerHTML = '<i class="fa-solid fa-exclamation-triangle mr-1"></i>Failed to save location';
                                }
                            });
                    },
                    (error) => {
                        console.error('Geolocation error:', error);
                        const updateEl = document.getElementById('last-update');
                        if (updateEl) {
                            updateEl.innerHTML = '<i class="fa-solid fa-exclamation-triangle mr-1"></i>Location access denied';
                        }
                        
                        if (error.code === error.PERMISSION_DENIED) {
                            alert('Please enable location access in your browser settings to see your location on the map.\n\nTo enable:\n1. Click the lock icon in your browser address bar\n2. Allow location access\n3. Refresh the page');
                        } else {
                            alert('Unable to get your location. Please check your browser settings.');
                        }
                    },
                    { timeout: 10000, enableHighAccuracy: true, maximumAge: 0 }
                );
            } else {
                alert('Geolocation is not supported by your browser.');
            }
        }

        // Function to toggle continuous location tracking
        function toggleContinuousTracking() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser.');
                return;
            }

            if (isTrackingEnabled) {
                // Stop tracking
                stopContinuousTracking();
            } else {
                // Start tracking
                startContinuousTracking();
            }
        }

        // Start continuous location tracking
        function startContinuousTracking() {
            if (isTrackingEnabled) return;

            // Request permission first
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    // Permission granted, start tracking
                    isTrackingEnabled = true;
                    updateTrackingButton();
                    
                    // Update location immediately
                    updateLocation(position).then(() => {
                        loadCrewLocations();
                    });
                    
                    // Set up watchPosition for continuous updates
                    locationWatchId = navigator.geolocation.watchPosition(
                        (position) => {
                            updateLocation(position).then(() => {
                                // Refresh map data every minute
                                if (currentView === 'map') {
                                    loadCrewLocations();
                                }
                            }).catch((error) => {
                                console.error('Error updating location:', error);
                            });
                        },
                        (error) => {
                            console.error('Geolocation watch error:', error);
                            if (error.code === error.PERMISSION_DENIED) {
                                stopContinuousTracking();
                                alert('Location tracking stopped. Please enable location access in your browser settings.');
                            }
                        },
                        { 
                            enableHighAccuracy: true, 
                            timeout: 10000, 
                            maximumAge: 60000 // Accept cached position up to 1 minute old
                        }
                    );
                    
                    // Also set up interval as backup (every 1 minute)
                    locationUpdateInterval = setInterval(() => {
                        if (isTrackingEnabled) {
                            updateLocation().catch((error) => {
                                console.error('Interval location update error:', error);
                            });
                        }
                    }, 60000); // 1 minute
                    
                    const updateEl = document.getElementById('last-update');
                    if (updateEl) {
                        updateEl.innerHTML = '<i class="fa-solid fa-sync fa-spin mr-1"></i>Tracking location...';
                    }
                },
                (error) => {
                    console.error('Geolocation permission error:', error);
                    if (error.code === error.PERMISSION_DENIED) {
                        alert('Please enable location access in your browser settings to enable automatic location tracking.\n\nTo enable:\n1. Click the lock icon in your browser address bar\n2. Allow location access\n3. Click "Enable Auto-Update" again');
                    } else {
                        alert('Unable to get your location. Please check your browser settings.');
                    }
                },
                { timeout: 10000, enableHighAccuracy: true, maximumAge: 0 }
            );
        }

        // Stop continuous location tracking
        function stopContinuousTracking() {
            isTrackingEnabled = false;
            
            if (locationWatchId !== null) {
                navigator.geolocation.clearWatch(locationWatchId);
                locationWatchId = null;
            }
            
            if (locationUpdateInterval !== null) {
                clearInterval(locationUpdateInterval);
                locationUpdateInterval = null;
            }
            
            updateTrackingButton();
            
            const updateEl = document.getElementById('last-update');
            if (updateEl) {
                updateEl.innerHTML = '<i class="fa-solid fa-clock mr-1"></i>Auto-update disabled';
            }
        }

        // Update tracking button UI
        function updateTrackingButton() {
            const btn = document.getElementById('tracking-toggle-btn');
            const status = document.getElementById('tracking-status');
            
            if (btn && status) {
                if (isTrackingEnabled) {
                    btn.className = 'px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-sm';
                    status.innerHTML = '<i class="fa-solid fa-location-dot mr-1"></i>Disable Auto-Update';
                } else {
                    btn.className = 'px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm';
                    status.innerHTML = '<i class="fa-solid fa-location-dot mr-1"></i>Enable Auto-Update';
                }
            }
        }

        // Make functions globally available
        window.requestUserLocation = requestUserLocation;
        window.toggleContinuousTracking = toggleContinuousTracking;
        
        // Clean up on page unload
        window.addEventListener('beforeunload', () => {
            stopContinuousTracking();
            stopPolling();
        });
    })();
</script>
@endpush
