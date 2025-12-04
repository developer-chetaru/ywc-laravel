@php
    // Prepare gallery images with proper URLs
    $galleryImages = $yacht->gallery->filter(function($img) {
        return !empty($img->image_path);
    })->map(function($img) {
        $url = null;
        if ($img->image_path) {
            if (str_starts_with($img->image_path, 'http')) {
                $url = $img->image_path;
            } else {
                $url = asset('storage/' . $img->image_path);
            }
        }
        return [
            'url' => $url,
            'caption' => $img->caption ?? ''
        ];
    })->filter(function($img) {
        return !empty($img['url']);
    })->values();
@endphp

<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen" x-data="{ 
    selectedImage: null,
    currentIndex: 0,
    images: @js($galleryImages->toArray())
}">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Back Button --}}
        <a href="{{ route('yacht-reviews.show', $yacht->slug) }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to {{ $yacht->name }}</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Photo Gallery - {{ $yacht->name }}</h1>
            
            @if($galleryImages->count() > 0)
                {{-- Main Gallery Layout: Large image on left, smaller images on right --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
                    {{-- Large Main Image (Left) --}}
                    @if($galleryImages->count() > 0)
                        <div class="lg:col-span-2 relative group cursor-pointer" @click="selectedImage = 0; currentIndex = 0">
                            <div class="relative w-full h-[400px] sm:h-[500px] lg:h-[600px] rounded-lg overflow-hidden shadow-lg">
                                <img src="{{ $galleryImages[0]['url'] }}" 
                                     alt="{{ $galleryImages[0]['caption'] ?? 'Gallery image' }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     onerror="this.style.display='none'">
                                <div class="absolute bottom-4 left-4 bg-white/95 backdrop-blur-sm px-4 py-2.5 rounded-lg shadow-xl">
                                    <p class="text-sm font-semibold text-gray-900">
                                        +{{ $galleryImages->count() }} {{ $galleryImages->count() === 1 ? 'Photo' : 'Photos' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Smaller Images Stacked (Right) --}}
                    <div class="lg:col-span-1 space-y-4">
                        @if($galleryImages->count() > 1)
                            <div class="relative group cursor-pointer" @click="selectedImage = 1; currentIndex = 1">
                                <div class="relative w-full h-[240px] sm:h-[280px] lg:h-[290px] rounded-lg overflow-hidden shadow-md">
                                    <img src="{{ $galleryImages[1]['url'] }}" 
                                         alt="{{ $galleryImages[1]['caption'] ?? 'Gallery image' }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         onerror="this.style.display='none'">
                                </div>
                            </div>
                        @endif
                        @if($galleryImages->count() > 2)
                            <div class="relative group cursor-pointer" @click="selectedImage = 2; currentIndex = 2">
                                <div class="relative w-full h-[240px] sm:h-[280px] lg:h-[290px] rounded-lg overflow-hidden shadow-md">
                                    <img src="{{ $galleryImages[2]['url'] }}" 
                                         alt="{{ $galleryImages[2]['caption'] ?? 'Gallery image' }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         onerror="this.style.display='none'">
                                    @if($galleryImages->count() > 3)
                                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center rounded-lg backdrop-blur-sm">
                                            <p class="text-white text-lg font-semibold">
                                                +{{ $galleryImages->count() - 3 }} More
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Remaining Images Grid (if more than 3 images) --}}
                @if($galleryImages->count() > 3)
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($galleryImages->slice(3) as $index => $image)
                            <div class="group cursor-pointer" @click="selectedImage = {{ $index + 3 }}; currentIndex = {{ $index + 3 }}">
                                <div class="relative w-full h-48 rounded-lg overflow-hidden">
                                    <img src="{{ $image['url'] }}" 
                                         alt="{{ $image['caption'] ?? 'Gallery image' }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         onerror="this.style.display='none'">
                                </div>
                                @if($image['caption'])
                                    <p class="mt-2 text-sm text-gray-600 font-medium">{{ $image['caption'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500">No gallery images available.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Full Screen Lightbox --}}
    <div x-show="selectedImage !== null" 
         x-cloak
         @keydown.escape.window="selectedImage = null"
         @keydown.arrow-left.window="if(selectedImage !== null && currentIndex > 0) { currentIndex--; selectedImage = currentIndex; }"
         @keydown.arrow-right.window="if(selectedImage !== null && currentIndex < images.length - 1) { currentIndex++; selectedImage = currentIndex; }"
         class="fixed inset-0 z-50 bg-black bg-opacity-95 flex items-center justify-center p-4"
         style="display: none;">
        <button @click="selectedImage = null" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        
        <div class="relative max-w-7xl w-full h-full flex items-center justify-center">
            <button x-show="currentIndex > 0"
                    @click="currentIndex--; selectedImage = currentIndex"
                    class="absolute left-4 text-white hover:text-gray-300 z-10 bg-black bg-opacity-50 rounded-full p-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            
            <div class="text-center">
                <img :src="images[selectedImage]?.url" 
                     :alt="images[selectedImage]?.caption || 'Gallery image'"
                     class="max-w-full max-h-[90vh] object-contain mx-auto rounded-lg">
                <p x-show="images[selectedImage]?.caption" 
                   x-text="images[selectedImage]?.caption"
                   class="mt-4 text-white text-lg font-medium"></p>
            </div>
            
            <button x-show="currentIndex < images.length - 1"
                    @click="currentIndex++; selectedImage = currentIndex"
                    class="absolute right-4 text-white hover:text-gray-300 z-10 bg-black bg-opacity-50 rounded-full p-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
        
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white text-sm">
            <span x-text="(currentIndex + 1) + ' / ' + images.length"></span>
        </div>
    </div>
</div>

