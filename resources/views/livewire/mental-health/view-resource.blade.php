<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('mental-health.resources') }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-800">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Resources
            </a>
        </div>

        @if($resource)
            <!-- Resource Header -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                @if($resource->thumbnail_path)
                    <img src="{{ asset('storage/' . $resource->thumbnail_path) }}" 
                         alt="{{ $resource->title }}" 
                         class="w-full h-64 object-cover">
                @else
                    <div class="w-full h-64 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                        <span class="text-6xl">
                            @if($resource->resource_type == 'video') 🎥
                            @elseif($resource->resource_type == 'audio') 🎵
                            @elseif($resource->resource_type == 'worksheet') 📄
                            @elseif($resource->resource_type == 'pdf') 📑
                            @else 📖
                            @endif
                        </span>
                    </div>
                @endif

                <div class="p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst($resource->category) }}
                        </span>
                        @if($resource->difficulty_level)
                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-800">
                                {{ ucfirst($resource->difficulty_level) }}
                            </span>
                        @endif
                        @if($resource->resource_type)
                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-purple-100 text-purple-800">
                                {{ ucfirst($resource->resource_type) }}
                            </span>
                        @endif
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $resource->title }}</h1>
                    
                    <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                        @if($resource->reading_time_minutes)
                            <span>⏱ {{ $resource->reading_time_minutes }} min read</span>
                        @endif
                        @if($resource->view_count > 0)
                            <span>👁 {{ number_format($resource->view_count) }} views</span>
                        @endif
                        @if($resource->author)
                            <span>✍️ {{ $resource->author }}</span>
                        @endif
                        @if($resource->publication_date)
                            <span>📅 {{ $resource->publication_date->format('M d, Y') }}</span>
                        @endif
                    </div>

                    @if($resource->description)
                        <p class="text-lg text-gray-700 mb-6">{{ $resource->description }}</p>
                    @endif

                    @if($resource->tags && count($resource->tags) > 0)
                        <div class="flex flex-wrap gap-2 mb-6">
                            @foreach($resource->tags as $tag)
                                <span class="px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-700">
                                    #{{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Resource Content -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                @if($resource->resource_type == 'video' && $resource->file_path)
                    <div class="mb-6">
                        <video controls class="w-full rounded-lg" src="{{ asset('storage/' . $resource->file_path) }}">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                @elseif($resource->resource_type == 'audio' && $resource->file_path)
                    <div class="mb-6">
                        <audio controls class="w-full">
                            <source src="{{ asset('storage/' . $resource->file_path) }}" type="audio/mpeg">
                            Your browser does not support the audio tag.
                        </audio>
                    </div>
                @elseif($resource->resource_type == 'pdf' && $resource->file_path)
                    <div class="mb-6">
                        <iframe src="{{ asset('storage/' . $resource->file_path) }}" 
                                class="w-full h-96 rounded-lg border" 
                                frameborder="0">
                            <p>Your browser does not support iframes. 
                               <a href="{{ asset('storage/' . $resource->file_path) }}" 
                                  target="_blank" 
                                  class="text-blue-600 hover:underline">
                                  Click here to download the PDF
                               </a>
                            </p>
                        </iframe>
                        <div class="mt-4">
                            <a href="{{ asset('storage/' . $resource->file_path) }}" 
                               target="_blank" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download PDF
                            </a>
                        </div>
                    </div>
                @elseif($resource->resource_type == 'worksheet' && $resource->file_path)
                    <div class="mb-6">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                            <p class="text-gray-600 mb-4">Worksheet File Available</p>
                            <a href="{{ asset('storage/' . $resource->file_path) }}" 
                               target="_blank" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download Worksheet
                            </a>
                        </div>
                    </div>
                @endif

                @if($resource->content)
                    <div class="prose max-w-none">
                        {!! nl2br(e($resource->content)) !!}
                    </div>
                @endif
            </div>

            <!-- Related Resources (if any) -->
            @if($resource->related_resources && count($resource->related_resources) > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Related Resources</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($resource->related_resources as $relatedId)
                            @php
                                $related = \App\Models\MentalHealthResource::where('id', $relatedId)
                                    ->where('status', 'published')
                                    ->first();
                            @endphp
                            @if($related)
                                <a href="{{ route('mental-health.resources.view', $related->id) }}" 
                                   class="block p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md transition">
                                    <h3 class="font-semibold text-gray-900 mb-1">{{ $related->title }}</h3>
                                    @if($related->description)
                                        <p class="text-sm text-gray-600 line-clamp-2">{{ $related->description }}</p>
                                    @endif
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <p class="text-gray-500 text-lg">Resource not found or is not published.</p>
                <a href="{{ route('mental-health.resources') }}" 
                   class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                    Back to Resources
                </a>
            </div>
        @endif
    </div>
</div>

