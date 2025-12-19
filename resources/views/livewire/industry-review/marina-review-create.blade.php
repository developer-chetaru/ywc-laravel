<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        {{-- Back Button --}}
        <a href="{{ $marinaId ? route('marina-reviews.show', $marina->slug) : route('marina-reviews.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to {{ $marinaId ? $marina->name : 'Marina Reviews' }}</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-8 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $editId ? 'Edit Review' : 'Review for Marina' }}</h1>

            @if (session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 text-green-700 px-4 py-3 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <form wire:submit="save" class="space-y-6">
                @if(!$marinaId)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Marina</label>
                        <select wire:model="marinaId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Choose a marina...</option>
                            @foreach(\App\Models\Marina::orderBy('name')->get() as $m)
                                <option value="{{ $m->id }}">{{ $m->name }}, {{ $m->city }}, {{ $m->country }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Review Title *</label>
                    <input type="text" wire:model="title" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('title') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Overall Rating *</label>
                    <div class="flex gap-2">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" wire:click="$set('overall_rating', {{ $i }})" class="focus:outline-none">
                                <svg class="w-10 h-10 {{ $i <= $overall_rating ? 'text-yellow-500 fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"></path>
                                </svg>
                            </button>
                        @endfor
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Review *</label>
                    <textarea wire:model="review" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    @error('review') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tips & Tricks</label>
                    <textarea wire:model="tips_tricks" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Share insider knowledge, warnings, money-saving tips, etc."></textarea>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Visit Date</label>
                        <input type="date" wire:model="visit_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Yacht Length</label>
                        <input type="text" wire:model="yacht_length_meters" placeholder="e.g., 15m" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="is_anonymous" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Post anonymously</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Photos (optional)</label>
                    <input type="file" wire:model="photos" multiple accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        {{ $editId ? 'Update Review' : 'Submit Review' }}
                    </button>
                    <a href="{{ $marinaId ? route('marina-reviews.show', $marina->slug) : route('marina-reviews.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

