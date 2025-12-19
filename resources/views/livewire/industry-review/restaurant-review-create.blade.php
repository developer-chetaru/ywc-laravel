<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        {{-- Back Button --}}
        <a href="{{ isset($restaurant) && $restaurant ? route('restaurant-reviews.show', $restaurant->slug) : route('restaurant-reviews.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to {{ isset($restaurant) && $restaurant ? $restaurant->name : 'Restaurant Reviews' }}</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Write a Review for {{ $restaurant->name ?? 'Restaurant' }}</h1>

            <form wire:submit.prevent="save">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                        <input type="text" wire:model="title" class="w-full px-4 py-2 border rounded-lg">
                        @error('title') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Review *</label>
                        <textarea wire:model="review" rows="6" class="w-full px-4 py-2 border rounded-lg"></textarea>
                        @error('review') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Overall Rating *</label>
                            <select wire:model="overall_rating" class="w-full px-4 py-2 border rounded-lg">
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Food Rating</label>
                            <select wire:model="food_rating" class="w-full px-4 py-2 border rounded-lg">
                                <option value="">Select</option>
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Service Rating</label>
                            <select wire:model="service_rating" class="w-full px-4 py-2 border rounded-lg">
                                <option value="">Select</option>
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Value Rating</label>
                            <select wire:model="value_rating" class="w-full px-4 py-2 border rounded-lg">
                                <option value="">Select</option>
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Crew Tips</label>
                        <textarea wire:model="crew_tips" rows="3" class="w-full px-4 py-2 border rounded-lg" placeholder="Share tips for other crew members..."></textarea>
                    </div>

                    <div class="flex items-center gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="would_recommend" class="mr-2">
                            <span class="text-sm text-gray-700">Would recommend</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="is_anonymous" class="mr-2">
                            <span class="text-sm text-gray-700">Post anonymously</span>
                        </label>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Submit Review
                        </button>
                        <a href="{{ isset($restaurant) && $restaurant ? route('restaurant-reviews.show', $restaurant->slug) : route('restaurant-reviews.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
