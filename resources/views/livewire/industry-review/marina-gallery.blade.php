@php
    // Prepare gallery images with proper URLs
    $galleryImages = $marina->gallery->filter(function($img) {
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
        <a href="{{ route('marina-reviews.show', $marina->slug) }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to {{ $marina->name }}</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Photo Gallery - {{ $marina->name }}</h1>
            
            @if($galleryImages->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($galleryImages as $index => $image)
                        <div class="group cursor-pointer" @click="selectedImage = {{ $index }}; currentIndex = {{ $index }}">
                            <img src="{{ $image['url'] }}" 
                                 alt="{{ $image['caption'] ?? 'Gallery image' }}" 
                                 class="w-full h-64 object-cover rounded-lg group-hover:opacity-90 transition-opacity"
                                 onerror="this.style.display='none'">
                            @if($image['caption'])
                                <p class="mt-2 text-sm text-gray-600 font-medium">{{ $image['caption'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
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

