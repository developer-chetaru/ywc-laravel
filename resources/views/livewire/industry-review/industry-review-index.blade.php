<div>
    <div class="max-w-8xl mx-auto px-3 sm:px-4 lg:px-4">
        <div class="bg-white p-4 sm:p-6 lg:p-10 rounded-lg shadow-md">
            {{-- Header --}}
            <div class="mb-4 sm:mb-6 lg:mb-8">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2 sm:mb-3 leading-tight">Industry Review System</h1>
                <p class="text-sm sm:text-base lg:text-lg text-gray-600 leading-relaxed">
                    Transparent reviews from yacht crew. Share your experiences and help others make informed decisions.
                </p>
            </div>

            {{-- Category Buttons --}}
            <div class="mb-4 sm:mb-6">
                <div class="flex flex-wrap gap-3 sm:gap-4">
                    <a href="{{ route('yacht-reviews.index') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm transition-all bg-blue-600 text-white border-2 border-blue-600 shadow-md hover:bg-blue-700 hover:shadow-lg">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span>Yacht Reviews</span>
                    </a>
                    <a href="{{ route('marina-reviews.index') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm transition-all bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200 shadow-sm">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Marina Reviews</span>
                    </a>
                    <a href="{{ route('contractor-reviews.index') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm transition-all bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200 shadow-sm">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Contractor Reviews</span>
                    </a>
                    <a href="{{ route('broker-reviews.index') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm transition-all bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200 shadow-sm">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Broker Reviews</span>
                    </a>
                    <a href="{{ route('restaurant-reviews.index') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm transition-all bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200 shadow-sm">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                        <span>Restaurant Reviews</span>
                    </a>
                    @auth
                    <a href="{{ route('my-reviews.index') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm transition-all bg-green-600 text-white border-2 border-green-600 shadow-md hover:bg-green-700 hover:shadow-lg">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>My Reviews</span>
                    </a>
                    @endauth
                </div>
            </div>

            {{-- Overview Content --}}
            <div class="mb-6">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 sm:p-8 border border-blue-100">
                    <p class="text-base sm:text-lg text-gray-700 leading-relaxed">
                        Select a category above to browse and review yachts, marinas, contractors, brokers, and restaurants. Share your experiences and help the yacht crew community make informed decisions.
                    </p>
                </div>
            </div>


            {{-- Features Overview --}}
            <div class="mt-6 sm:mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-6 border border-green-200">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">5-Star Rating System</h4>
                    <p class="text-sm text-gray-600">Comprehensive rating system with category-specific ratings for detailed feedback.</p>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-lg p-6 border border-blue-200">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Photo Uploads</h4>
                    <p class="text-sm text-gray-600">Upload photos with reviews to provide visual evidence of conditions.</p>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg p-6 border border-purple-200">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Community Voting</h4>
                    <p class="text-sm text-gray-600">Helpful/Not Helpful voting system to surface the most useful reviews.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Yacht Creation Modal --}}
    @if($showYachtModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-2 sm:p-4" wire:click="closeModal">
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-[95vh] sm:max-h-[90vh] overflow-y-auto" @click.stop>
            <div class="p-4 sm:p-6 lg:p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Add New Yacht</h2>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                @if($yachtMessage)
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-green-800">{{ $yachtMessage }}</p>
                    </div>
                @endif

                @if($yachtError)
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-800">{{ $yachtError }}</p>
                    </div>
                @endif

                <form wire:submit.prevent="saveYacht" class="space-y-6">
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Yacht Name *</label>
                        <input type="text" wire:model="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>

                    {{-- Type and Status --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                            <select wire:model="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <option value="">Select Type</option>
                                @foreach($yachtTypes as $yachtType)
                                    <option value="{{ $yachtType->code }}">{{ $yachtType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select wire:model="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Length --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Length (meters)</label>
                            <input type="number" step="0.1" wire:model="length_meters" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Length (feet)</label>
                            <input type="number" step="0.1" wire:model="length_feet" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    {{-- Year Built and Flag --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Year Built</label>
                            <input type="number" min="1900" max="{{ date('Y') + 1 }}" wire:model="year_built" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Flag Registry</label>
                            <input type="text" wire:model="flag_registry" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    {{-- Home Port --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Home Port</label>
                        <input type="text" wire:model="home_port" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    {{-- Capacity --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Crew Capacity</label>
                            <input type="number" min="1" wire:model="crew_capacity" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Guest Capacity</label>
                            <input type="number" min="1" wire:model="guest_capacity" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    {{-- Cover Image --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>
                        <input type="file" wire:model="cover_image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @if($cover_image_preview)
                            <div class="mt-4">
                                <img src="{{ $cover_image_preview }}" alt="Preview" class="w-48 h-32 object-cover rounded-lg border border-gray-300">
                            </div>
                        @endif
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-4 pt-4 border-t">
                        <button type="button" wire:click="closeModal" class="px-4 sm:px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm sm:text-base">
                            Cancel
                        </button>
                        <button type="button" wire:click="resetYachtForm" class="px-4 sm:px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm sm:text-base">
                            Reset
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="px-4 sm:px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base">
                            <span wire:loading.remove wire:target="saveYacht">Create Yacht</span>
                            <span wire:loading wire:target="saveYacht" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Creating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Detail Modal --}}
    @if($showDetailModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-2 sm:p-4" wire:click="closeDetailModal" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999;">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[95vh] sm:max-h-[90vh] overflow-y-auto" @click.stop style="position: relative; z-index: 10000; background: white;">
            @if($detailLoading)
                <div class="p-6 sm:p-12 text-center">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    <p class="mt-4 text-gray-600">Loading details...</p>
                </div>
            @elseif($detailError)
                <div class="p-6">
                    <div class="text-center">
                        <p class="text-red-600 mb-4">{{ $detailError }}</p>
                        <button wire:click="closeDetailModal" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                            Close
                        </button>
                    </div>
                </div>
            @elseif($detailData)
                <div class="p-4 sm:p-6">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                        <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 break-words pr-2">{{ $detailData['name'] ?? 'Unknown' }}</h2>
                        <button wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Cover Image --}}
                    @if(isset($detailData['cover_image_url']))
                        <div class="mb-6 rounded-lg overflow-hidden">
                            <img src="{{ $detailData['cover_image_url'] }}" alt="{{ $detailData['name'] }}" class="w-full h-64 object-cover">
                        </div>
                    @endif

                    {{-- Details Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-4 sm:mb-6">
                        @if($detailType === 'yacht')
                            <div class="bg-gray-50 rounded-lg p-4">
                                <dt class="text-xs font-semibold text-gray-500 uppercase mb-1">Type</dt>
                                <dd class="text-gray-900 font-medium">{{ ucfirst(str_replace('_', ' ', $detailData['type'] ?? '—')) }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <dt class="text-xs font-semibold text-gray-500 uppercase mb-1">Length</dt>
                                <dd class="text-gray-900 font-medium">
                                    @if(isset($detailData['length_meters']))
                                        {{ number_format($detailData['length_meters'], 1) }}m
                                    @elseif(isset($detailData['length_feet']))
                                        {{ number_format($detailData['length_feet'], 0) }}ft
                                    @else
                                        —
                                    @endif
                                </dd>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <dt class="text-xs font-semibold text-gray-500 uppercase mb-1">Status</dt>
                                <dd class="text-gray-900 font-medium">{{ ucfirst($detailData['status'] ?? '—') }}</dd>
                            </div>
                            @if(isset($detailData['home_port']))
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <dt class="text-xs font-semibold text-gray-500 uppercase mb-1">Home Port</dt>
                                    <dd class="text-gray-900 font-medium">{{ $detailData['home_port'] }}</dd>
                                </div>
                            @endif
                        @else
                            <div class="bg-gray-50 rounded-lg p-4">
                                <dt class="text-xs font-semibold text-gray-500 uppercase mb-1">Location</dt>
                                <dd class="text-gray-900 font-medium">
                                    @if(isset($detailData['city']) && isset($detailData['country']))
                                        {{ $detailData['city'] }}, {{ $detailData['country'] }}
                                    @elseif(isset($detailData['country']))
                                        {{ $detailData['country'] }}
                                    @else
                                        —
                                    @endif
                                </dd>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <dt class="text-xs font-semibold text-gray-500 uppercase mb-1">Type</dt>
                                <dd class="text-gray-900 font-medium">{{ ucfirst(str_replace('_', ' ', $detailData['type'] ?? '—')) }}</dd>
                            </div>
                        @endif
                        <div class="bg-gray-50 rounded-lg p-4">
                            <dt class="text-xs font-semibold text-gray-500 uppercase mb-1">Reviews</dt>
                            <dd class="text-gray-900 font-medium">{{ $detailData['reviews_count'] ?? 0 }} reviews</dd>
                        </div>
                        @if(isset($detailData['rating_avg']) && $detailData['rating_avg'] > 0)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <dt class="text-xs font-semibold text-gray-500 uppercase mb-1">Rating</dt>
                                <dd class="text-gray-900 font-medium">{{ number_format($detailData['rating_avg'], 1) }} ⭐</dd>
                            </div>
                        @endif
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 mb-4 sm:mb-6">
                        @auth
                            <button 
                                wire:click="openReviewForm" 
                                wire:loading.attr="disabled"
                                class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium disabled:opacity-50 text-sm sm:text-base">
                                <span wire:loading.remove wire:target="openReviewForm">Write a Review</span>
                                <span wire:loading wire:target="openReviewForm">Opening...</span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm sm:text-base text-center">
                                Login to Review
                            </a>
                        @endauth
                    </div>

                    {{-- Reviews Section --}}
                    <div class="border-t pt-4 sm:pt-6">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-3 sm:mb-4">Reviews ({{ $detailData['reviews_count'] ?? 0 }})</h3>
                        
                        @if(count($reviews) > 0)
                            <div class="space-y-4">
                                @foreach($reviews as $review)
                                    <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                                        {{-- Review Header --}}
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                    @if(isset($review['user']) && !($review['is_anonymous'] ?? false))
                                                        <span class="text-blue-600 font-semibold text-sm">
                                                            {{ substr($review['user']['first_name'] ?? 'A', 0, 1) }}{{ substr($review['user']['last_name'] ?? '', 0, 1) }}
                                                        </span>
                                                    @else
                                                        <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-900">{{ $review['user_name'] ?? 'Anonymous' }}</div>
                                                    <div class="text-xs text-gray-500">{{ $review['created_at_formatted'] ?? '' }}</div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @php
                                                        $rating = (int)($review['overall_rating'] ?? 0);
                                                        $isFilled = $i <= $rating;
                                                    @endphp
                                                    <svg class="w-5 h-5 {{ $isFilled ? 'text-yellow-400 fill-current' : 'text-gray-300' }}" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                    </svg>
                                                @endfor
                                                <span class="ml-2 text-sm font-semibold text-gray-600">{{ $rating }}/5</span>
                                            </div>
                                        </div>

                                        {{-- Review Title --}}
                                        @if(isset($review['title']))
                                            <h4 class="font-semibold text-gray-900 mb-2">{{ $review['title'] }}</h4>
                                        @endif

                                        {{-- Review Content --}}
                                        @if(isset($review['review']))
                                            <p class="text-gray-700 mb-3 whitespace-pre-wrap">{{ $review['review'] }}</p>
                                        @endif

                                        {{-- Pros and Cons --}}
                                        @if(isset($review['pros']) || isset($review['cons']))
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                                @if(isset($review['pros']) && !empty($review['pros']))
                                                    <div class="bg-green-50 rounded p-3">
                                                        <div class="text-xs font-semibold text-green-700 uppercase mb-1">PROS (Things you liked):</div>
                                                        <p class="text-sm text-gray-700">{{ $review['pros'] }}</p>
                                                    </div>
                                                @endif
                                                @if(isset($review['cons']) && !empty($review['cons']))
                                                    <div class="bg-red-50 rounded p-3">
                                                        <div class="text-xs font-semibold text-red-700 uppercase mb-1">CONS (Things you didn't like):</div>
                                                        <p class="text-sm text-gray-700">{{ $review['cons'] }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Tips & Tricks (for marinas) --}}
                                        @if(isset($review['tips_tricks']) && !empty($review['tips_tricks']))
                                            <div class="bg-blue-50 rounded p-3 mb-3">
                                                <div class="text-xs font-semibold text-blue-700 uppercase mb-1">Tips & Tricks</div>
                                                <p class="text-sm text-gray-700">{{ $review['tips_tricks'] }}</p>
                                            </div>
                                        @endif

                                        {{-- Recommend Badge --}}
                                        @if(isset($review['would_recommend']) && $review['would_recommend'])
                                            <div class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold mb-2">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                Recommends
                                            </div>
                                        @endif

                                        {{-- Helpful Count --}}
                                        @if(isset($review['helpful_count']) && $review['helpful_count'] > 0)
                                            <div class="text-xs text-gray-500 mt-2">
                                                {{ $review['helpful_count'] }} {{ ($review['helpful_count'] == 1) ? 'person' : 'people' }} found this helpful
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 bg-gray-50 rounded-lg">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <p class="text-gray-600">No reviews yet. Be the first to review!</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif

    {{-- Review Form Modal --}}
    @if($showReviewForm && $detailData)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[10000] p-2 sm:p-4" wire:click="closeReviewForm" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 10000;">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[95vh] sm:max-h-[90vh] overflow-y-auto" @click.stop style="position: relative; z-index: 10001; background: white;">
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Write a Review</h2>
                    <button wire:click="closeReviewForm" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                @if($reviewMessage)
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-green-800">{{ $reviewMessage }}</p>
                    </div>
                @endif

                @if($reviewError)
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-800">{{ $reviewError }}</p>
                    </div>
                @endif

                <form wire:submit.prevent="submitReview" class="space-y-4">
                    {{-- Rating --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Overall Rating *</label>
                        <div class="flex gap-2 items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <button 
                                    type="button" 
                                    wire:click="$set('reviewRating', {{ $i }})" 
                                    class="relative transition-all duration-200 hover:scale-125 {{ $reviewRating >= $i ? 'text-yellow-400 scale-110' : 'text-gray-300 hover:text-yellow-300' }}"
                                    style="cursor: pointer; background: none; border: none; padding: 4px;">
                                    <svg class="w-10 h-10 {{ $reviewRating >= $i ? 'fill-current' : '' }}" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    @if($reviewRating >= $i)
                                        <div class="absolute inset-0 bg-yellow-400 rounded-full opacity-20 blur-sm"></div>
                                    @endif
                                </button>
                            @endfor
                            <span class="ml-4 text-lg font-semibold text-gray-700">
                                {{ $reviewRating }}/5
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            @if($reviewRating == 5)
                                <span class="text-green-600 font-semibold">Excellent!</span>
                            @elseif($reviewRating == 4)
                                <span class="text-blue-600 font-semibold">Very Good</span>
                            @elseif($reviewRating == 3)
                                <span class="text-yellow-600 font-semibold">Good</span>
                            @elseif($reviewRating == 2)
                                <span class="text-orange-600 font-semibold">Fair</span>
                            @elseif($reviewRating == 1)
                                <span class="text-red-600 font-semibold">Poor</span>
                            @endif
                        </p>
                    </div>

                    {{-- Title --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Review Title *</label>
                        <input type="text" wire:model="reviewTitle" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    {{-- Content --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Review *</label>
                        <textarea wire:model="reviewContent" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required></textarea>
                    </div>

                    {{-- Pros --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">PROS (Things you liked):</label>
                        <textarea wire:model="reviewPros" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Enter things you liked about this {{ $detailType }}..."></textarea>
                    </div>

                    {{-- Cons --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CONS (Things you didn't like):</label>
                        <textarea wire:model="reviewCons" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Enter things you didn't like about this {{ $detailType }}..."></textarea>
                    </div>

                    {{-- Options --}}
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="reviewRecommend" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">I recommend this {{ $detailType }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="reviewAnonymous" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Post as anonymous</span>
                        </label>
                    </div>

                    {{-- Submit --}}
                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-4 pt-4 border-t">
                        <button type="button" wire:click="closeReviewForm" class="px-4 sm:px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm sm:text-base">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="px-4 sm:px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 text-sm sm:text-base">
                            <span wire:loading.remove wire:target="submitReview">Submit Review</span>
                            <span wire:loading wire:target="submitReview">Submitting...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
