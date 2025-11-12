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
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('itinerary.routes.index') }}" 
                   class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Routes
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $route->title }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Image Gallery Section --}}
            @if(count($allImages) > 0)
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" 
                     x-data="imageGallery">
                    {{-- Main Image Display --}}
                    <div class="relative bg-gray-900" style="min-height: 400px;">
                        <img :src="allImages[selectedImage].url" 
                             :alt="allImages[selectedImage].label"
                             class="w-full"
                             :class="{
                                 'h-96 object-cover': displayMode === 'cover',
                                 'h-96 object-contain bg-gray-800': displayMode === 'contain',
                                 'h-auto max-h-screen object-contain bg-gray-800': displayMode === 'full'
                             }"
                             x-ref="mainImage">
                        
                        {{-- Navigation Arrows --}}
                        @if(count($allImages) > 1)
                            <button @click="selectedImage = (selectedImage - 1 + allImages.length) % allImages.length"
                                    class="absolute left-4 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white p-3 rounded-full transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <button @click="selectedImage = (selectedImage + 1) % allImages.length"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white p-3 rounded-full transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        @endif
                        
                        {{-- Image Counter --}}
                        @if(count($allImages) > 1)
                            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black bg-opacity-50 text-white px-4 py-2 rounded-full text-sm">
                                <span x-text="selectedImage + 1"></span> / <span x-text="allImages.length"></span>
                            </div>
                        @endif
                        
                        {{-- Display Mode Controls --}}
                        <div class="absolute top-4 right-4 bg-black bg-opacity-50 rounded-lg p-2">
                            <div class="flex gap-1">
                                <button @click="displayMode = 'cover'" 
                                        :class="displayMode === 'cover' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'"
                                        class="px-2 py-1 text-xs rounded hover:bg-indigo-500" 
                                        title="Cover (crop to fit)">
                                    Cover
                                </button>
                                <button @click="displayMode = 'contain'" 
                                        :class="displayMode === 'contain' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'"
                                        class="px-2 py-1 text-xs rounded hover:bg-indigo-500"
                                        title="Contain (show full image)">
                                    Contain
                                </button>
                                <button @click="displayMode = 'full'" 
                                        :class="displayMode === 'full' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'"
                                        class="px-2 py-1 text-xs rounded hover:bg-indigo-500"
                                        title="Full size">
                                    Full
                                </button>
                            </div>
                        </div>
                        
                        {{-- Fullscreen Button --}}
                        <a :href="allImages[selectedImage].url" target="_blank"
                           class="absolute top-4 left-4 bg-black bg-opacity-50 hover:bg-opacity-75 text-white p-2 rounded-full transition-all"
                           title="View full size">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                            </svg>
                        </a>
                    </div>
                    
                    {{-- Thumbnail Gallery --}}
                    @if(count($allImages) > 1)
                        <div class="p-4 bg-gray-50 border-t border-gray-200">
                            <div class="flex gap-2 overflow-x-auto pb-2">
                                <template x-for="(image, index) in allImages" :key="index">
                                    <button @click="selectedImage = index"
                                            :class="selectedImage === index ? 'ring-2 ring-indigo-500 ring-offset-2' : 'opacity-60 hover:opacity-100'"
                                            class="flex-shrink-0 w-20 h-20 rounded-md overflow-hidden border-2 transition-all"
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
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 space-y-6">
                {{-- Header with Actions --}}
                <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                    <h3 class="text-xl font-semibold text-gray-800">Route Overview</h3>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full capitalize"
                              :class="{
                                  'bg-yellow-100 text-yellow-800': '{{ $route->status }}' === 'draft',
                                  'bg-green-100 text-green-800': '{{ $route->status }}' === 'active',
                                  'bg-blue-100 text-blue-800': '{{ $route->status }}' === 'completed',
                                  'bg-gray-100 text-gray-800': '{{ $route->status }}' === 'archived'
                              }">
                            {{ ucfirst($route->status) }}
                        </span>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full capitalize"
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
                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Description</h4>
                        <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ $route->description }}</p>
                    </div>
                @endif
                
                <div>
                    <dl class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
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
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Route Stops</h3>
                        <span class="text-sm text-gray-500">{{ count($route->stops) }} stop(s)</span>
                    </div>
                    <div class="space-y-4">
                        @foreach($route->stops as $stop)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow bg-white">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center font-semibold">
                                            {{ $stop->sequence }}
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">{{ $stop->name }}</h4>
                                            @if($stop->location_label)
                                                <p class="text-sm text-gray-500">{{ $stop->location_label }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @if($stop->stay_duration_hours)
                                        <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-full">
                                            {{ $stop->stay_duration_hours }} hrs
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3 text-sm">
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
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <p class="text-xs font-medium text-gray-600 mb-2">Photos ({{ count($stopPhotos) }}):</p>
                                        <div class="grid grid-cols-4 md:grid-cols-6 gap-2">
                                            @foreach($stopPhotos as $photo)
                                                <a href="{{ Storage::url($photo) }}" target="_blank" class="block group">
                                                    <img src="{{ Storage::url($photo) }}" 
                                                         alt="{{ $stop->name }} - Photo {{ $loop->iteration }}" 
                                                         class="w-full h-20 object-cover rounded-md border-2 border-gray-200 group-hover:border-indigo-400 group-hover:shadow-lg transition-all">
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
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Route Legs</h3>
                    @if($route->legs->isEmpty())
                        <div class="border border-dashed border-gray-300 rounded-lg p-6 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                            <p class="text-sm">Leg metrics will appear after coordinates are provided for stops.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($route->legs as $leg)
                                <div class="border border-gray-200 rounded-lg p-4 bg-gradient-to-br from-indigo-50 to-blue-50 hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-indigo-700 bg-indigo-100 px-2 py-1 rounded">Leg {{ $leg->sequence }}</span>
                                        <span class="text-sm font-bold text-gray-800">{{ number_format($leg->distance_nm, 2) }} NM</span>
                                    </div>
                                    <div class="text-sm text-gray-700 space-y-1">
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

                {{-- Additional Sections --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        @livewire('itinerary.route-weather', ['route' => $route], key('weather-'.$route->id))
                    </div>
                    <div>
                        @livewire('itinerary.route-exports', ['route' => $route], key('exports-'.$route->id))
                    </div>
                </div>

                @livewire('itinerary.route-crew-manager', ['route' => $route], key('crew-'.$route->id))

                @livewire('itinerary.route-discussion', ['route' => $route], key('discussion-'.$route->id))

                @livewire('itinerary.route-reviews', ['route' => $route], key('reviews-'.$route->id))

                @livewire('itinerary.route-analytics', ['route' => $route], key('analytics-'.$route->id))
            </div>
        </div>
    </div>

    @push('scripts')
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
        </script>
    @endpush
</x-app-layout>

