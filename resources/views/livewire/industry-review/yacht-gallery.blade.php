@php
    // Available category labels
    $categoryLabels = [
        'exterior' => 'Exterior',
        'interior' => 'Interior',
        'deck' => 'Deck',
        'cabins' => 'Cabins',
        'salon' => 'Salon',
        'dining' => 'Dining',
        'galley' => 'Galley',
        'engine_room' => 'Engine Room',
        'watersports' => 'Watersports Equipment',
        'other' => 'Other',
    ];
    
    // Prepare gallery images with proper URLs and group by category
    $galleryByCategory = [];
    $allImages = [];
    
    $yacht->gallery->filter(function($img) {
        return !empty($img->image_path);
    })->each(function($img) use (&$galleryByCategory, &$allImages, $categoryLabels) {
        $url = null;
        if ($img->image_path) {
            if (str_starts_with($img->image_path, 'http')) {
                $url = $img->image_path;
            } else {
                $url = asset('storage/' . $img->image_path);
            }
        }
        
        if ($url) {
            $category = $img->category ?? 'other';
            $categoryName = $categoryLabels[$category] ?? ucfirst($category);
            
            if (!isset($galleryByCategory[$category])) {
                $galleryByCategory[$category] = [
                    'name' => $categoryName,
                    'images' => []
                ];
            }
            
            $imageData = [
                'id' => $img->id,
                'url' => $url,
                'caption' => $img->caption ?? '',
                'category' => $category,
            ];
            
            $galleryByCategory[$category]['images'][] = $imageData;
            $allImages[] = $imageData;
        }
    });
    
    // Sort categories by predefined order
    $categoryOrder = array_keys($categoryLabels);
    uksort($galleryByCategory, function($a, $b) use ($categoryOrder) {
        $posA = array_search($a, $categoryOrder);
        $posB = array_search($b, $categoryOrder);
        if ($posA === false) $posA = 999;
        if ($posB === false) $posB = 999;
        return $posA <=> $posB;
    });
@endphp

<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen" x-data="{ 
    selectedImage: null,
    currentIndex: 0,
    images: @js($allImages)
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
            
            @if(count($allImages) > 0)
                @php
                    $imageIndex = 0;
                @endphp
                {{-- Display images grouped by category/label --}}
                @foreach($galleryByCategory as $categoryKey => $categoryData)
                    <div class="mb-8 last:mb-0">
                        {{-- Category Header --}}
                        <div class="flex items-center justify-between mb-4 pb-2 border-b-2 border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-800 uppercase tracking-wide">
                                {{ $categoryData['name'] }}
                            </h2>
                            <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                {{ count($categoryData['images']) }} {{ count($categoryData['images']) === 1 ? 'Photo' : 'Photos' }}
                            </span>
                        </div>
                        
                        {{-- Images Grid for this Category --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            @foreach($categoryData['images'] as $image)
                                <div class="group cursor-pointer relative" 
                                     @click="selectedImage = {{ $imageIndex }}; currentIndex = {{ $imageIndex }}">
                                    <div class="relative w-full h-48 rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow">
                                        <img src="{{ $image['url'] }}" 
                                             alt="{{ $image['caption'] ?? 'Gallery image' }}" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                             onerror="this.style.display='none'">
                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300"></div>
                                    </div>
                                    @if($image['caption'])
                                        <p class="mt-2 text-xs text-gray-600 font-medium line-clamp-2">{{ $image['caption'] }}</p>
                                    @endif
                                </div>
                                @php $imageIndex++; @endphp
                            @endforeach
                        </div>
                    </div>
                @endforeach
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

