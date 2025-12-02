<div class="py-6 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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
                        <a href="{{ route('restaurant-reviews.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
