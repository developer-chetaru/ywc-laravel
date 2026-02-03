<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Forum Search</h1>
            
            <!-- Search Bar -->
            <div class="flex gap-3 mb-4">
                <div class="flex-1 relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.500ms="query"
                        placeholder="Search threads and posts..."
                        class="w-full px-4 py-3 pl-10 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button 
                    wire:click="toggleFilters"
                    class="px-4 py-3 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 flex items-center gap-2"
                >
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filters
                </button>
            </div>

            <!-- Advanced Filters -->
            @if($showFilters)
            <div class="border-t border-gray-200 pt-4 mt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Author Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                        <input 
                            type="text" 
                            wire:model.live.debounce.500ms="author"
                            placeholder="Search by author..."
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select 
                            wire:model.live="categoryId"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input 
                            type="date" 
                            wire:model.live="dateFrom"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>

                    <!-- Date To -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                        <input 
                            type="date" 
                            wire:model.live="dateTo"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select 
                            wire:model.live="status"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="all">All Status</option>
                            <option value="open">Open</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>

                    <!-- Has Answers Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Has Answers</label>
                        <select 
                            wire:model.live="hasAnswers"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">All</option>
                            <option value="1">Has Answers</option>
                            <option value="0">No Answers</option>
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                        <select 
                            wire:model.live="sortBy"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="relevance">Relevance</option>
                            <option value="date">Newest First</option>
                            <option value="popularity">Most Popular</option>
                        </select>
                    </div>

                    <!-- Search In -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search In</label>
                        <select 
                            wire:model.live="searchIn"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="all">All</option>
                            <option value="threads">Threads Only</option>
                            <option value="posts">Posts Only</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button 
                        wire:click="clearFilters"
                        class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200"
                    >
                        Clear All Filters
                    </button>
                </div>
            </div>
            @endif
        </div>

        <!-- Results -->
        @if(!empty($query))
            <div class="mb-4">
                <p class="text-gray-600">
                    Found <span class="font-semibold text-gray-900">{{ $totalResults }}</span> result(s) for "<span class="font-semibold text-gray-900">{{ $query }}</span>"
                </p>
            </div>

            @if($totalResults > 0)
                <!-- Thread Results -->
                @if(isset($searchResults['threads']) && $searchResults['threads']->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Threads ({{ $searchResults['threads']->count() }})</h2>
                        <div class="space-y-4">
                            @foreach($searchResults['threads'] as $thread)
                                <div class="border-b border-gray-200 pb-4 last:border-b-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <a 
                                                href="{{ route('forum.thread.show', ['thread_id' => $thread->id, 'thread_slug' => \Illuminate\Support\Str::slug($thread->title)]) }}"
                                                class="text-lg font-semibold text-blue-600 hover:text-blue-800 mb-2 block"
                                            >
                                                {!! $thread->highlighted_title ?? $thread->title !!}
                                            </a>
                                            
                                            @if(isset($thread->highlighted_content))
                                                <p class="text-gray-600 text-sm mb-2">{!! $thread->highlighted_content !!}</p>
                                            @endif

                                            <div class="flex items-center gap-4 text-sm text-gray-500">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                    {{ $thread->author->first_name }} {{ $thread->author->last_name }}
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                    </svg>
                                                    {{ $thread->category->title }}
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                                    </svg>
                                                    {{ $thread->reply_count }} replies
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $thread->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        @if($thread->pinned)
                                            <span class="ml-4 px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded">Pinned</span>
                                        @endif
                                        @if($thread->locked)
                                            <span class="ml-4 px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded">Locked</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Post Results -->
                @if(isset($searchResults['posts']) && $searchResults['posts']->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Posts ({{ $searchResults['posts']->count() }})</h2>
                        <div class="space-y-4">
                            @foreach($searchResults['posts'] as $post)
                                <div class="border-b border-gray-200 pb-4 last:border-b-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <a 
                                                href="{{ route('forum.thread.show', ['thread_id' => $post->thread->id, 'thread_slug' => \Illuminate\Support\Str::slug($post->thread->title)]) }}#post-{{ $post->id }}"
                                                class="text-lg font-semibold text-blue-600 hover:text-blue-800 mb-2 block"
                                            >
                                                Re: {{ $post->thread->title }}
                                            </a>
                                            
                                            <p class="text-gray-600 text-sm mb-2">{!! $post->highlighted_content ?? \Illuminate\Support\Str::limit(strip_tags($post->content), 200) !!}</p>

                                            <div class="flex items-center gap-4 text-sm text-gray-500">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                    {{ $post->author->first_name }} {{ $post->author->last_name }}
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                    </svg>
                                                    {{ $post->thread->category->title }}
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $post->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Similar Threads -->
                @if(isset($similarThreads) && $similarThreads->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Similar Threads</h2>
                        <div class="space-y-3">
                            @foreach($similarThreads as $thread)
                                <a 
                                    href="{{ route('forum.thread.show', ['thread_id' => $thread->id, 'thread_slug' => \Illuminate\Support\Str::slug($thread->title)]) }}"
                                    class="block p-3 rounded-lg hover:bg-gray-50 border border-gray-200"
                                >
                                    <div class="font-medium text-gray-900">{{ $thread->title }}</div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        {{ $thread->category->title }} • {{ $thread->reply_count }} replies • {{ $thread->created_at->diffForHumans() }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No results found</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Try adjusting your search terms or filters to find what you're looking for.
                    </p>
                </div>
            @endif
        @else
            <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Start your search</h3>
                <p class="mt-2 text-sm text-gray-500">
                    Enter keywords in the search box above to find threads and posts.
                </p>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    mark {
        background-color: #FEF08A;
        padding: 2px 4px;
        border-radius: 3px;
        font-weight: 600;
    }
</style>
@endpush
