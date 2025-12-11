<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🌟 Success Stories</h1>
                    <p class="text-gray-600 mt-1">Real stories from yacht crew who achieved financial independence</p>
                </div>
                <a href="{{ route('financial.dashboard') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Back to Dashboard
                </a>
            </div>

            <div class="mb-6">
                <select wire:model="filterStrategy" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @foreach($strategies as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            @if($stories->count() > 0)
            <div class="grid md:grid-cols-3 gap-6">
                @foreach($stories as $story)
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer {{ $story->is_featured ? 'bg-yellow-50 border-yellow-300' : '' }}" 
                     wire:click="viewStory({{ $story->id }})">
                    @if($story->is_featured)
                    <div class="text-xs bg-yellow-400 text-yellow-900 px-2 py-1 rounded-full inline-block mb-3">⭐ Featured</div>
                    @endif
                    <div class="text-4xl mb-4">{{ substr($story->name, 0, 1) }}</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $story->name }}</h3>
                    @if($story->position)
                    <p class="text-sm text-gray-600 mb-2">{{ $story->position }}</p>
                    @endif
                    <p class="text-sm text-gray-600 mb-4">{{ Str::limit($story->story, 120) }}</p>
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>{{ ucfirst(str_replace('_', ' ', $story->strategy_type)) }}</span>
                        @if($story->current_status)
                        <span>€{{ number_format($story->current_status, 0) }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $stories->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <p class="text-gray-500">No success stories available yet.</p>
            </div>
            @endif

            {{-- Story Viewer Modal --}}
            @if($selectedStory)
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" wire:click="closeStory">
                <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto" wire:click.stop>
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $selectedStory->name }}'s Story</h2>
                        <button wire:click="closeStory" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            @if($selectedStory->position)
                            <p class="text-gray-600">{{ $selectedStory->position }}</p>
                            @endif
                            @if($selectedStory->age)
                            <p class="text-gray-600">Age: {{ $selectedStory->age }}</p>
                            @endif
                        </div>
                        <div class="prose max-w-none mb-6">
                            {!! nl2br(e($selectedStory->story)) !!}
                        </div>
                        @if($selectedStory->starting_point || $selectedStory->current_status)
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <h3 class="font-semibold text-gray-900 mb-2">The Numbers</h3>
                            <div class="grid md:grid-cols-2 gap-4">
                                @if($selectedStory->starting_point)
                                <div>
                                    <div class="text-sm text-gray-600">Starting Point</div>
                                    <div class="text-xl font-bold text-gray-900">€{{ number_format($selectedStory->starting_point, 0) }}</div>
                                </div>
                                @endif
                                @if($selectedStory->current_status)
                                <div>
                                    <div class="text-sm text-gray-600">Current Status</div>
                                    <div class="text-xl font-bold text-green-600">€{{ number_format($selectedStory->current_status, 0) }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                        @if($selectedStory->advice)
                        <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                            <h3 class="font-semibold text-blue-900 mb-2">💡 Advice</h3>
                            <p class="text-blue-800">{!! nl2br(e($selectedStory->advice)) !!}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

