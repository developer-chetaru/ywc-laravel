@if($show_form)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-data="{ show: true }" x-show="show" @click.self="show = false">
        <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4 shadow-2xl" @click.stop>
            <div class="text-center mb-6">
                <div class="text-4xl mb-4">🎯</div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Save Your Results!</h3>
                <p class="text-gray-600">Enter your email to save this calculation and get personalized financial advice.</p>
            </div>

            @if(session('lead_saved'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4">
                    Thank you! Check your email for your results.
                </div>
            @endif

            <form wire:submit.prevent="saveLead" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                    <input type="email" wire:model="email" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="your@email.com" required>
                    @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name (Optional)</label>
                    <input type="text" wire:model="name" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Your name">
                    @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit" 
                            class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Save & Create Account
                    </button>
                    <button type="button" wire:click="skip" 
                            class="px-4 py-3 text-gray-600 hover:text-gray-800 transition-colors">
                        Skip
                    </button>
                </div>

                <p class="text-xs text-gray-500 text-center">
                    By continuing, you agree to receive financial planning updates and tips.
                </p>
            </form>
        </div>
    </div>
@endif

