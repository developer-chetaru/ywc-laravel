<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📚 Educational Resources</h1>
                    <p class="text-gray-600 mt-1">Learn about investing, retirement planning, and financial management</p>
                </div>
                <a href="{{ route('financial.dashboard') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Back to Dashboard
                </a>
            </div>

            <div class="mb-6">
                <select wire:model="filterType" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="all">All Content</option>
                    <option value="course">Courses</option>
                    <option value="guide">Guides</option>
                    <option value="strategy_template">Strategy Templates</option>
                </select>
            </div>

            @if($content->count() > 0)
            <div class="grid md:grid-cols-3 gap-6">
                @foreach($content as $item)
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer" 
                     wire:click="viewContent({{ $item->id }})">
                    <div class="text-4xl mb-4">
                        @if($item->type === 'course') 📖
                        @elseif($item->type === 'guide') 📄
                        @else 🎯
                        @endif
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $item->title }}</h3>
                    <p class="text-sm text-gray-600 mb-4">{{ Str::limit($item->description, 100) }}</p>
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>{{ ucfirst($item->difficulty ?? 'All levels') }}</span>
                        @if($item->duration_minutes)
                        <span>{{ $item->duration_minutes }} min</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $content->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <p class="text-gray-500">No educational content available yet.</p>
            </div>
            @endif

            {{-- Content Viewer Modal --}}
            @if($selectedContent)
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" wire:click="closeContent">
                <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto" wire:click.stop>
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $selectedContent->title }}</h2>
                        <button wire:click="closeContent" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="prose max-w-none">
                            {!! nl2br(e($selectedContent->content ?? $selectedContent->description)) !!}
                        </div>
                        @if($selectedContent->file_path)
                        <div class="mt-6">
                            <a href="{{ asset('storage/' . $selectedContent->file_path) }}" 
                               target="_blank"
                               class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                Download Resource
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

