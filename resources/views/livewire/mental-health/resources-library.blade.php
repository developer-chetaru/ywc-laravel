<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('mental-health.dashboard') }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-800">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Resources Library</h1>
            <p class="mt-2 text-gray-600">Self-help resources, articles, and tools for your mental wellness</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           placeholder="Search resources..." 
                           class="w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <select wire:model.live="category" class="w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All Categories</option>
                        <option value="anxiety">Anxiety</option>
                        <option value="depression">Depression</option>
                        <option value="stress">Stress</option>
                        <option value="relationships">Relationships</option>
                        <option value="trauma">Trauma</option>
                    </select>
                </div>
                <div>
                    <select wire:model.live="resourceType" class="w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All Types</option>
                        <option value="article">Articles</option>
                        <option value="video">Videos</option>
                        <option value="audio">Audio</option>
                        <option value="worksheet">Worksheets</option>
                        <option value="pdf">PDFs</option>
                    </select>
                </div>
                <div>
                    <select wire:model.live="difficultyLevel" class="w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All Levels</option>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Resources Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($resources as $resource)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    @if($resource->thumbnail_path)
                        <img src="{{ asset('storage/' . $resource->thumbnail_path) }}" 
                             alt="{{ $resource->title }}" 
                             class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                            <span class="text-4xl">
                                @if($resource->resource_type == 'video') 🎥
                                @elseif($resource->resource_type == 'audio') 🎵
                                @elseif($resource->resource_type == 'worksheet') 📄
                                @else 📖
                                @endif
                            </span>
                        </div>
                    @endif

                    <div class="p-6">
                        <div class="flex items-start justify-between mb-2">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($resource->category) }}
                            </span>
                            @if($resource->difficulty_level)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                    {{ ucfirst($resource->difficulty_level) }}
                                </span>
                            @endif
                        </div>

                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $resource->title }}</h3>
                        
                        @if($resource->description)
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $resource->description }}</p>
                        @endif

                        <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                            @if($resource->reading_time_minutes)
                                <span>⏱ {{ $resource->reading_time_minutes }} min read</span>
                            @endif
                            @if($resource->view_count > 0)
                                <span>👁 {{ $resource->view_count }} views</span>
                            @endif
                        </div>

                        <a href="{{ route('mental-health.resources') }}/{{ $resource->id }}" 
                           class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md transition">
                            View Resource
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12">
                    <p class="text-gray-500 text-lg">No resources found matching your criteria.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $resources->links() }}
        </div>
    </div>
</div>
