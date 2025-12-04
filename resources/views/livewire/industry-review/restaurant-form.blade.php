<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-4 sm:p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ $isEditMode ? 'Edit Restaurant' : 'Add New Restaurant' }}</h1>
                    <p class="text-sm text-gray-600">{{ $isEditMode ? 'Update restaurant information' : 'Add a new restaurant to the system' }}</p>
                </div>
                <a href="{{ route('industryreview.restaurants.manage') }}" 
                   class="inline-flex items-center px-3 sm:px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span class="hidden sm:inline">Back to Restaurants</span>
                    <span class="sm:hidden">Back</span>
                </a>
            </div>

            @if (session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 text-green-700 px-4 py-3 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if($error)
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-800">{{ $error }}</p>
                </div>
            @endif

            <form wire:submit.prevent="save" class="space-y-4 sm:space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                        <input type="text" wire:model="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" required>
                        @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                        <select wire:model="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" required>
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                        <input type="text" wire:model="city" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                        <input type="text" wire:model="country" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <input type="text" wire:model="address" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                        <input type="number" step="any" wire:model="latitude" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                        <input type="number" step="any" wire:model="longitude" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="text" wire:model="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" wire:model="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                        <input type="url" wire:model="website" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                        <select wire:model="price_range" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">Select</option>
                            @foreach($priceRanges as $range)
                                <option value="{{ $range }}">{{ $range }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea wire:model="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"></textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cuisine Type (comma-separated)</label>
                        <input type="text" wire:model="cuisine_typeInput" placeholder="e.g., Italian, Seafood, Mediterranean" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Opening Hours (comma-separated)</label>
                        <input type="text" wire:model="opening_hoursInput" placeholder="e.g., Mon-Fri: 9am-10pm, Sat-Sun: 10am-11pm" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="crew_friendly" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label class="ml-2 text-sm text-gray-700">Crew Friendly</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="crew_discount" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label class="ml-2 text-sm text-gray-700">Crew Discount</label>
                    </div>
                    @if($crew_discount)
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Crew Discount Details</label>
                            <textarea wire:model="crew_discount_details" rows="2" placeholder="e.g., 10% off for crew members" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"></textarea>
                        </div>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>
                    @if($cover_image_preview)
                        <img src="{{ $cover_image_preview }}" alt="Preview" class="w-32 h-32 object-cover rounded-lg mb-2">
                    @elseif(!empty($existing_cover_image))
                        <img src="{{ $existing_cover_image }}" alt="Current" class="w-32 h-32 object-cover rounded-lg mb-2" onerror="this.style.display='none'">
                    @endif
                    <input type="file" wire:model="cover_image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                    @error('cover_image') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Gallery Images --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gallery Images (Multiple images allowed)</label>
                    
                    @if(count($existing_gallery) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                            @foreach($existing_gallery as $item)
                                @if(!empty($item['url']))
                                    <div class="border border-gray-200 rounded-lg p-3">
                                        <div class="relative group mb-2">
                                            <img src="{{ $item['url'] }}" 
                                                 alt="{{ $item['caption'] ?? 'Gallery image' }}" 
                                                 class="w-full h-32 object-cover rounded-lg"
                                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\'%3E%3Crect fill=\'%23ddd\' width=\'100\' height=\'100\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\'%3EImage%3C/text%3E%3C/svg%3E';">
                                            <button type="button" wire:click="removeExistingGalleryImage({{ $item['id'] }})" 
                                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <input type="text" 
                                               wire:model="existing_gallery_captions.{{ $item['id'] }}"
                                               placeholder="Image description/caption..."
                                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @if(count($gallery_previews) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                            @foreach($gallery_previews as $index => $preview)
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <div class="relative group mb-2">
                                        <img src="{{ $preview }}" alt="Preview" class="w-full h-32 object-cover rounded-lg">
                                        <button type="button" wire:click="removeGalleryImage({{ $index }})" 
                                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <input type="text" 
                                           wire:model="gallery_captions.{{ $index }}"
                                           placeholder="Image description/caption..."
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex items-center gap-2">
                        <input type="file" wire:model="gallery_images" accept="image/*" multiple 
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm">
                        <span class="text-xs text-gray-500">Max 20 images</span>
                    </div>
                    @error('gallery_images.*') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    <p class="text-xs text-gray-500 mt-1">You can upload multiple images at once. Click the X button on any image to remove it.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 pt-4 border-t border-gray-200">
                    <button type="submit" wire:loading.attr="disabled" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold disabled:opacity-50 text-sm sm:text-base">
                        <span wire:loading.remove wire:target="save">{{ $isEditMode ? 'Update' : 'Create' }} Restaurant</span>
                        <span wire:loading wire:target="save" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ $isEditMode ? 'Updating...' : 'Creating...' }}
                        </span>
                    </button>
                    <a href="{{ route('industryreview.restaurants.manage') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-center text-sm sm:text-base">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

