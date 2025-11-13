<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-8 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $editId ? 'Edit Review' : 'Write a Yacht Review' }}</h1>

            @if (session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 text-green-700 px-4 py-3 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <form wire:submit="save" class="space-y-6">
                @if(!$yachtId)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Yacht</label>
                        <select wire:model="yachtId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Choose a yacht...</option>
                            @foreach(\App\Models\Yacht::orderBy('name')->get() as $y)
                                <option value="{{ $y->id }}">{{ $y->name }}</option>
                            @endforeach
                        </select>
                        @error('yachtId') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
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
                    @error('overall_rating') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Review * (minimum 50 characters)</label>
                    <textarea wire:model="review" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    @error('review') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pros</label>
                        <textarea wire:model="pros" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cons</label>
                        <textarea wire:model="cons" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Management Rating</label>
                        <select wire:model="management_rating" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="">N/A</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Working Conditions</label>
                        <select wire:model="working_conditions_rating" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="">N/A</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Compensation</label>
                        <select wire:model="compensation_rating" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="">N/A</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Work Start Date</label>
                        <input type="date" wire:model="work_start_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Work End Date</label>
                        <input type="date" wire:model="work_end_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Position Held</label>
                    <input type="text" wire:model="position_held" placeholder="e.g., Deckhand, Chef, Engineer" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>

                <div class="flex items-center gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="would_recommend" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Would you recommend this yacht?</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="is_anonymous" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Post anonymously</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Photos (optional)</label>
                    <input type="file" wire:model="photos" multiple accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    @error('photos.*') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        {{ $editId ? 'Update Review' : 'Submit Review' }}
                    </button>
                    <a href="{{ $yachtId ? route('yacht-reviews.show', $yacht->slug) : route('yacht-reviews.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

