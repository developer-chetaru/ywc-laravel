<div>
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Rate Your Experience</h1>

            @if($booking)
            <form wire:submit="submitRating" class="bg-white shadow rounded-lg p-6 space-y-6">
                <div>
                    <p class="text-lg font-medium mb-4">Job: {{ $booking->jobPost->position_title }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Overall Rating *</label>
                    <div class="flex gap-2">
                        @for($i = 1; $i <= 5; $i++)
                        <button type="button" wire:click="$set('overallRating', {{ $i }})" 
                            class="text-3xl {{ $overallRating >= $i ? 'text-yellow-400' : 'text-gray-300' }}">
                            ⭐
                        </button>
                        @endfor
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Professionalism Rating *</label>
                    <select wire:model="professionalismRating" class="w-full border-gray-300 rounded-md" required>
                        <option value="">Select rating</option>
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Very Good</option>
                        <option value="3">3 - Good</option>
                        <option value="2">2 - Fair</option>
                        <option value="1">1 - Poor</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Rating *</label>
                    <select wire:model="paymentRating" class="w-full border-gray-300 rounded-md" required>
                        <option value="">Select rating</option>
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Very Good</option>
                        <option value="3">3 - Good</option>
                        <option value="2">2 - Fair</option>
                        <option value="1">1 - Poor</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Review (Optional)</label>
                    <textarea wire:model="reviewText" rows="5" class="w-full border-gray-300 rounded-md"></textarea>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-md hover:bg-indigo-700">
                    Submit Rating
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
