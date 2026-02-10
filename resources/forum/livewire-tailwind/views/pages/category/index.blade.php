@php
    // Ensure selectedThread is always defined, defaulting to null if not set
    // This prevents "Undefined variable" errors when the view is rendered
    if (!isset($selectedThread)) {
        $selectedThread = null;
    }
@endphp

<div>
    {{-- ✅ Success message --}}
    @if (session()->has('success'))
        <div id="successMessage"
            class="mb-4 p-4 text-green-800 bg-green-50 border-l-4 border-green-500 rounded-lg flex items-center gap-3 shadow-sm animate-slide-down">
            <i class="fas fa-check-circle text-green-500 text-xl"></i>
            <div class="flex-1">
                <p class="font-semibold">{{ session('success') }}</p>
            </div>
            <button onclick="document.getElementById('successMessage').remove()" class="text-green-600 hover:text-green-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <script>
            // Auto-hide success message after 5 seconds
            setTimeout(function() {
                const msg = document.getElementById('successMessage');
                if (msg) {
                    msg.style.transition = 'opacity 0.5s';
                    msg.style.opacity = '0';
                    setTimeout(() => msg.remove(), 500);
                }
            }, 5000);
        </script>
    @endif

    {{-- Error message --}}
    @if (session()->has('error'))
        <div class="mb-4 p-4 text-red-800 bg-red-50 border-l-4 border-red-500 rounded-lg flex items-center gap-3 shadow-sm">
            <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
            <div class="flex-1">
                <p class="font-semibold">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif
    @role('super_admin')

   
    <div class="flex-1">

        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mb-4 sm:mb-6" id="forumTabs">
            <!-- Tab 1 -->
            <div data-tab="forums" 
                class="tab-btn cursor-pointer relative flex flex-col justify-center border border-[#BDBDBD] 
                bg-white rounded-lg py-4 sm:py-5 px-4 sm:px-6 pr-10 sm:pr-12 hover:bg-[#F8F9FA] hover:border-blue-500 transition-all shadow-sm flex-1 sm:max-w-[300px]">
                <p class="font-normal text-[#808080] text-xs sm:text-sm mb-1">Total Forums</p>
                <span class="font-semibold text-[#1B1B1B] text-xl sm:text-2xl">{{ $totalForums }}</span>
                <img src="{{ asset('images/message-multiple-01.svg') }}" alt="" class="absolute right-4 sm:right-6 top-1/2 -translate-y-1/2 w-6 h-6 sm:w-8 sm:h-8">
            </div>

            <!-- Tab 2 -->
            <div data-tab="threads"
                class="tab-btn cursor-pointer relative flex flex-col justify-center border border-[#BDBDBD] 
                bg-white rounded-lg py-4 sm:py-5 px-4 sm:px-6 pr-10 sm:pr-12 hover:bg-[#F8F9FA] hover:border-blue-500 transition-all shadow-sm flex-1 sm:max-w-[300px]">
                <p class="font-normal text-[#808080] text-xs sm:text-sm mb-1">Total Threads</p>
                <span class="font-semibold text-[#1B1B1B] text-xl sm:text-2xl">{{ $totalThreads }}</span>
                <img src="{{ asset('images/wechat.svg') }}" alt="" class="absolute right-4 sm:right-6 top-1/2 -translate-y-1/2 w-6 h-6 sm:w-8 sm:h-8">
            </div>
        </div>

        <!-- 🔹 Tab Contents -->
        <div id="forumTabContent" class="hidden">
            <!-- Forums Tab -->
            <div id="tab-forums" class="tab-content hidden">
                @livewire('forum::pages.category.forums-list')
            </div>

            <!-- Threads Tab -->
            <div id="tab-threads" class="tab-content hidden">
                @livewire('forum::pages.category.threads-list')
            </div>
        </div>

        <!-- 🔹 Chat Layout (Left + Right Side) -->
        <div id="chatLayout" class="flex flex-col lg:flex-row h-[calc(100vh-200px)] gap-4 lg:gap-x-[24px] bg-gray-100 mt-4">

            <!-- Left Sidebar -->
            <div class="w-full lg:w-100 bg-white rounded-xl flex flex-col overflow-hidden shadow-sm">
                <div class="p-4 border-b border-gray-100">
                    <!-- Filter & Sort Controls -->
                    <div class="flex flex-col sm:flex-row gap-2">
                        <select wire:model.live="filterBy" 
                                class="flex-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="all">All Forums</option>
                            <option value="active">Active (Last 7 days)</option>
                            <option value="pinned">Pinned</option>
                        </select>
                        <select wire:model.live="sortBy" 
                                class="flex-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="recent">Most Recent</option>
                            <option value="popular">Most Popular</option>
                            <option value="most_threads">Most Threads</option>
                            <option value="oldest">Oldest First</option>
                        </select>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-4 space-y-3">
                    <!-- Search Bar -->
                    <div class="mb-6">
                        <div class="relative group">
                            <input type="search" 
                                wire:model.live.debounce.300ms="search"
                                placeholder="Search forums, threads..."
                                class="w-full py-3.5 px-5 pl-14 pr-12 rounded-xl border-2 border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium bg-white shadow-md hover:shadow-lg hover:border-blue-300 transition-all duration-300 placeholder:text-gray-400" />
                            <div class="absolute left-5 top-1/2 -translate-y-1/2 pointer-events-none">
                                <i class="fas fa-search text-blue-500 text-base"></i>
                            </div>
                            @if(!empty($search))
                                <button wire:click="$set('search', '')" 
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition-all duration-200 p-1.5 rounded-lg hover:bg-gray-100 group"
                                    type="button"
                                    title="Clear search">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    @php
                        $categoriesToDisplay = isset($displayCategories) ? $displayCategories : $categories;
                    @endphp
                    @if(empty($categoriesToDisplay))
                        <div class="text-center py-12">
                            <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-search text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-500 text-lg font-medium">
                                @if(!empty($search))
                                    No forums found matching "{{ $search }}"
                                @else
                                    No forums available
                                @endif
                            </p>
                            @if(!empty($search))
                                <button wire:click="$set('search', '')" 
                                    class="mt-4 text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    Clear search
                                </button>
                            @endif
                        </div>
                    @else
                        @foreach($categoriesToDisplay as $category)
                        @php
                            $threadCount = $category->threads->count();
                            $latestThread = $category->threads->max('updated_at');
                            $isActive = $latestThread && $latestThread->gt(now()->subDays(7));
                            $pinnedThreads = $category->threads->where('pinned', true)->count();
                        @endphp
                        <div class="border border-gray-200 rounded-lg hover:shadow-md transition-all bg-white">
                            <div class="p-4">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h4 class="font-semibold text-gray-900 text-lg capitalize">
                                                {{ $category->title }}
                                            </h4>
                                            @if($pinnedThreads > 0)
                                                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-medium rounded">
                                                    <i class="fas fa-thumbtack mr-1"></i>Pinned
                                                </span>
                                            @endif
                                            @if($isActive)
                                                <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-medium rounded">
                                                    <i class="fas fa-circle text-[8px] mr-1"></i>Active
                                                </span>
                                            @endif
                                        </div>
                                        
                                        @if($category->description)
                                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                                {{ $category->description }}
                                            </p>
                                        @endif

                                        <div class="flex items-center gap-4 flex-wrap text-sm text-gray-600">
                                            <span class="flex items-center gap-1.5">
                                                <i class="fas fa-comments text-blue-500"></i>
                                                <span class="font-medium">{{ $threadCount }} {{ Str::plural('thread', $threadCount) }}</span>
                                            </span>
                                            @if($latestThread)
                                                <span class="flex items-center gap-1.5">
                                                    <i class="fas fa-clock text-gray-400"></i>
                                                    <span>Updated {{ $latestThread->diffForHumans() }}</span>
                                                </span>
                                            @endif
                                            <span class="flex items-center gap-1.5">
                                                <i class="fas fa-calendar text-gray-400"></i>
                                                <span>Created {{ $category->created_at->format('M d, Y') }}</span>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
                                        <a href="{{ Forum::route('category.edit', $category) }}"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Edit Forum">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ Forum::route('thread.create', $category) }}"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Add Thread">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    </div>
                                </div>

                                @if($category->accepts_threads && $threadCount > 0)
                                    <button onclick="toggleSection(this)" 
                                            class="w-full text-left text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <span>View {{ $threadCount }} {{ Str::plural('thread', $threadCount) }}</span>
                                        <i class="fas fa-chevron-down transition-transform arrow-icon"></i>
                                    </button>
                                    
                                    <div class="hidden section-content mt-3 space-y-2">
                                        @foreach($category->threads->take(10) as $index => $thread)
                                            @php
                                                $isThreadActive = isset($selectedThread) && $selectedThread && $selectedThread->id === $thread->id;
                                            @endphp
                                            <a href="{{ Forum::route('thread.show', $thread) }}" 
                                                class="block p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-all {{ $isThreadActive ? 'bg-blue-100 border-blue-400' : '' }}">
                                                <div class="flex items-start gap-3">
                                                    @if($thread->pinned)
                                                        <i class="fas fa-thumbtack text-yellow-500 mt-1"></i>
                                                    @else
                                                        <i class="fas fa-circle text-gray-300 text-[6px] mt-2"></i>
                                                    @endif
                                                    <div class="flex-1 min-w-0">
                                                        <h5 class="font-medium text-gray-900 truncate">
                                                            {{ $thread->title }}
                                                        </h5>
                                                        <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                                            <span>{{ $thread->posts->count() }} {{ Str::plural('reply', $thread->posts->count()) }}</span>
                                                            <span>•</span>
                                                            <span>{{ $thread->updated_at->diffForHumans() }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                        @if($threadCount > 10)
                                            <a href="{{ Forum::route('category.show', $category) }}" 
                                                class="block text-center py-2 text-sm text-blue-600 hover:text-blue-700 font-medium">
                                                View all {{ $threadCount }} threads →
                                            </a>
                                        @endif
                                    </div>
                            @else
                                <div class="text-center py-6 px-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="mb-4">
                                        <i class="fas fa-comments text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-sm font-medium text-gray-600 mb-1">No threads yet</p>
                                        <p class="text-xs text-gray-500">Be the first to start a discussion in this forum</p>
                                    </div>
                                    <a href="{{ Forum::route('thread.create', $category) }}" 
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                        <i class="fas fa-plus-circle"></i>
                                        Create First Thread
                                    </a>
                                </div>
                            @endif
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

     
        </div>

    </div>
    @endrole
    
    @if(!auth()->user() || !auth()->user()->hasRole('super_admin'))
     <style>
        a:not(.link-button):not(.group-button):not(.text-category) {
    
    color: #ffffff;
}
    </style>
    <div class="flex-1">
    <div class="flex flex-col lg:flex-row user-forum h-[calc(100vh-100px)] gap-4 lg:gap-x-[24px] bg-gray-100">

        <!-- Left Sidebar -->
        <div class="w-full lg:w-90 bg-white rounded-xl flex flex-col shadow-sm">
            <div class="p-4 border-b border-gray-100">
                <!-- Create Forum Button -->
                <a href="{{ Forum::route('category.create') }}" 
                   class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm shadow-sm">
                    <i class="fas fa-plus-circle"></i>
                    Create New Forum
                </a>
            </div>

            <!-- Forum List -->
            <nav class="flex-1 overflow-y-auto p-4 space-y-3">
                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="relative group">
                        <input type="search" 
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search forums, threads..."
                            class="w-full py-3.5 px-5 !pl-12 pr-12 rounded-xl border-2 border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium bg-white hover:border-blue-300 transition-all duration-300 placeholder:text-gray-400" />
                        <div class="absolute left-5 top-[15px]">
                            <i class="fas fa-search text-gray-500 text-base"></i>
                        </div>
                        @if(!empty($search))
                            <button wire:click="$set('search', '')" 
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition-all duration-200 p-1.5 rounded-lg hover:bg-gray-100 group"
                                type="button"
                                title="Clear search">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        @endif
                    </div>
                </div>
                
                @php
                    $categoriesToDisplay = isset($displayCategories) ? $displayCategories : $categories;
                @endphp
                @if(empty($categoriesToDisplay))
                    <div class="text-center py-12">
                        <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-search text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 text-lg font-medium">
                            @if(!empty($search))
                                No forums found matching "{{ $search }}"
                            @else
                                No forums available
                            @endif
                        </p>
                        @if(!empty($search))
                            <button wire:click="$set('search', '')" 
                                class="mt-4 text-blue-600 hover:text-blue-700 text-sm font-medium">
                                Clear search
                            </button>
                        @endif
                    </div>
                @else
                    @foreach($categoriesToDisplay as $category)
                    @php
                        $threadCount = $category->threads->count();
                        $latestThread = $category->threads->max('updated_at');
                        $isActive = $latestThread && $latestThread->gt(now()->subDays(7));
                        $pinnedThreads = $category->threads->where('pinned', true)->count();
                    @endphp
                    <div class="border border-gray-200 rounded-lg hover:shadow-md transition-all bg-white">
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h4 class="font-semibold text-gray-900 text-lg capitalize">
                                            {{ $category->title }}
                                        </h4>
                                        @if($pinnedThreads > 0)
                                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-medium rounded">
                                                <i class="fas fa-thumbtack mr-1"></i>Pinned
                                            </span>
                                        @endif
                                        @if($isActive)
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-medium rounded">
                                                <i class="fas fa-circle text-[8px] mr-1"></i>Active
                                            </span>
                                        @endif
                                    </div>
                                    
                                    @if($category->description)
                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                            {{ $category->description }}
                                        </p>
                                    @endif

                                    <div class="flex items-center gap-4 flex-wrap text-sm text-gray-600">
                                        <span class="flex items-center gap-1.5">
                                            <i class="fas fa-comments text-blue-500"></i>
                                            <span class="font-medium">{{ $threadCount }} {{ Str::plural('thread', $threadCount) }}</span>
                                        </span>
                                        @if($latestThread)
                                            <span class="flex items-center gap-1.5">
                                                <i class="fas fa-clock text-gray-400"></i>
                                                <span>Updated {{ $latestThread->diffForHumans() }}</span>
                                            </span>
                                        @endif
                                        <span class="flex items-center gap-1.5">
                                            <i class="fas fa-calendar text-gray-400"></i>
                                            <span>Created {{ $category->created_at->format('M d, Y') }}</span>
                                        </span>
                                    </div>
                                    
                                    @if($category->accepts_threads)
                                        <div class="mt-3">
                                            <a href="{{ Forum::route('thread.create', $category) }}"
                                                class="inline-flex items-center gap-2 px-3 py-1.5 text-sm bg-blue-600 text-white hover:bg-blue-700 rounded-lg transition-colors font-medium">
                                                <i class="fas fa-plus-circle text-xs"></i>
                                                <span>Add Thread</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if($category->accepts_threads && $threadCount > 0)
                                <button onclick="toggleSection(this)" 
                                        class="w-full text-left text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <span>View {{ $threadCount }} {{ Str::plural('thread', $threadCount) }}</span>
                                    <i class="fas fa-chevron-down transition-transform arrow-icon"></i>
                                </button>
                                
                                <div class="hidden section-content mt-3 space-y-2">
                                    @foreach($category->threads->take(10) as $index => $thread)
                                        @php
                                            $isActive = isset($selectedThread) && $selectedThread->id === $thread->id;
                                        @endphp
                                        <a href="{{ Forum::route('thread.show', $thread) }}" 
                                            class="block p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-all {{ $isActive ? 'bg-blue-100 border-blue-400' : '' }}">
                                            <div class="flex items-start gap-3">
                                                @if($thread->pinned)
                                                    <i class="fas fa-thumbtack text-yellow-500 mt-1"></i>
                                                @else
                                                    <i class="fas fa-circle text-gray-300 text-[6px] mt-2"></i>
                                                @endif
                                                <div class="flex-1 min-w-0">
                                                    <h5 class="font-medium text-gray-900 truncate">
                                                        {{ $thread->title }}
                                                    </h5>
                                                    <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                                        <span>{{ $thread->posts->count() }} {{ Str::plural('reply', $thread->posts->count()) }}</span>
                                                        <span>•</span>
                                                        <span>{{ $thread->updated_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                    @if($threadCount > 10)
                                        <a href="{{ Forum::route('category.show', $category) }}" 
                                            class="block text-center py-2 text-sm text-blue-600 hover:text-blue-700 font-medium">
                                            View all {{ $threadCount }} threads →
                                        </a>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-6 px-4 bg-gray-50 rounded-lg border border-gray-200 mt-3">
                                    <div class="mb-4">
                                        <i class="fas fa-comments text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-sm font-medium text-gray-600 mb-1">No threads available</p>
                                        <p class="text-xs text-gray-500">Start a new discussion in this forum</p>
                                    </div>
                                    <a href="{{ Forum::route('thread.create', $category) }}" 
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                        <i class="fas fa-plus-circle"></i>
                                        Create New Thread
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @endif
            </nav>
        </div>



    </div>
    </div>
    @endif

    <!-- Messaging Modal for Direct Chat -->
    @if($showMessageModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-2 sm:p-4" 
            wire:click="closeMessageModal">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl h-[90vh] sm:h-[600px] flex flex-col mx-2 sm:mx-4" wire:click.stop>
                <div class="flex justify-between items-center p-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <img src="{{ $messageUserId ? \App\Models\User::find($messageUserId)->profile_photo_url ?? '/default-avatar.png' : '/default-avatar.png' }}" 
                            alt="{{ $messageUserName }}" 
                            class="w-10 h-10 rounded-full">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">{{ $messageUserName }}</h2>
                            <p class="text-xs text-gray-500">Direct Chat</p>
                        </div>
                    </div>
                    <button wire:click="closeMessageModal" 
                        class="text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-times text-xl"></i>
                    </button>
                </div>
                
                <!-- Messages Area with Real-time Polling -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50" 
                    id="messages-container"
                    wire:poll.3s="refreshMessages"
                    x-data="{ 
                        scrollToBottom() {
                            this.$nextTick(() => {
                                const container = this.$el;
                                container.scrollTop = container.scrollHeight;
                            });
                        }
                    }"
                    x-init="
                        scrollToBottom();
                        $watch('$wire.messages', () => scrollToBottom());
                    ">
                    @if(count($messages) > 0)
                        @foreach($messages as $message)
                            <div class="flex {{ $message['is_sent'] ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-xs lg:max-w-md">
                                    @if(!$message['is_sent'])
                                        <div class="flex items-center gap-2 mb-1">
                                            <img src="{{ $message['sender_photo'] ?? '/default-avatar.png' }}" 
                                                alt="{{ $message['sender_name'] }}" 
                                                class="w-6 h-6 rounded-full">
                                            <span class="text-xs text-gray-600">{{ $message['sender_name'] }}</span>
                                        </div>
                                    @endif
                                    <div class="rounded-lg px-4 py-2 {{ $message['is_sent'] ? 'bg-blue-600 text-white' : 'bg-white text-gray-900 border border-gray-200' }}">
                                        @if(!empty($message['attachment_url']) || !empty($message['attachment_path']))
                                            <div class="mb-2">
                                                @php
                                                    $imageUrl = $message['attachment_url'] ?? asset('storage/' . $message['attachment_path']);
                                                @endphp
                                                <a href="{{ $imageUrl }}" target="_blank" class="block">
                                                    <img src="{{ $imageUrl }}" alt="Image" class="max-w-xs rounded-lg cursor-pointer hover:opacity-90 transition" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                    <div style="display:none;" class="text-sm text-gray-500">Image not available</div>
                                                </a>
                                            </div>
                                        @endif
                                        @if(!empty($message['message']))
                                            <p class="text-sm">{{ $message['message'] }}</p>
                                        @endif
                                        <div class="flex items-center justify-between mt-1">
                                            <p class="text-xs {{ $message['is_sent'] ? 'text-blue-100' : 'text-gray-500' }}">
                                                {{ $message['time_display'] }}
                                            </p>
                                            @if($message['is_sent'])
                                                @if($message['is_read'])
                                                    <i class="fa-solid fa-check-double text-blue-200 text-xs" title="Read"></i>
                                                @else
                                                    <i class="fa-solid fa-check text-blue-200 text-xs" title="Sent"></i>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8">
                            <i class="fa-solid fa-comments text-4xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500">No messages yet. Start the conversation!</p>
                        </div>
                    @endif
                </div>
                
                <!-- Message Input -->
                <div class="p-4 border-t border-gray-200 bg-white">
                    @if($messageImage)
                        <div class="mb-2 relative inline-block">
                            <img src="{{ $messageImage->temporaryUrl() }}" alt="Preview" class="max-w-xs h-32 object-cover rounded-lg border border-gray-300">
                            <button wire:click="$set('messageImage', null)" 
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition">
                                <i class="fa-solid fa-times text-xs"></i>
                            </button>
                        </div>
                    @endif
                    <form wire:submit.prevent="sendMessage" class="flex gap-2">
                        <label class="cursor-pointer px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center justify-center">
                            <i class="fa-solid fa-image text-gray-600"></i>
                            <input type="file" wire:model="messageImage" accept="image/*" class="hidden">
                        </label>
                        <input type="text" 
                            wire:model="messageText" 
                            placeholder="Type a message..." 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            autofocus>
                        <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('scroll-to-bottom', () => {
                    setTimeout(() => {
                        const container = document.getElementById('messages-container');
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    }, 100);
                });
            });
        </script>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tabs = document.querySelectorAll(".tab-btn");
        const tabContents = document.querySelectorAll(".tab-content");
        const forumTabContent = document.getElementById("forumTabContent");
        const chatLayout = document.getElementById("chatLayout");
        const backButtons = document.querySelectorAll(".backToChat");

        // ✅ Default state: show chat layout, hide all tab contents
        forumTabContent.classList.add("hidden");
        chatLayout.classList.remove("hidden");

        // ✅ Tab click logic
        tabs.forEach(tab => {
            tab.addEventListener("click", function () {
                const target = this.getAttribute("data-tab");

                // Remove active highlight from all tabs
                tabs.forEach(t => t.classList.remove("!bg-blue-50", "!border-blue-500"));

                // Hide all tab contents
                tabContents.forEach(content => content.classList.add("hidden"));

                // Hide chat layout when any tab is clicked
                chatLayout.classList.add("hidden");

                // Highlight clicked tab
                this.classList.add("!bg-blue-50", "!border-blue-500");

                // Show tab section container and selected tab content
                forumTabContent.classList.remove("hidden");
                document.getElementById(`tab-${target}`).classList.remove("hidden");
            });
        });

        // ✅ Back button logic: go back to chat layout
        backButtons.forEach(button => {
            button.addEventListener("click", function () {
                forumTabContent.classList.add("hidden");
                tabContents.forEach(content => content.classList.add("hidden"));
                tabs.forEach(t => t.classList.remove("!bg-blue-50", "!border-blue-500"));
                chatLayout.classList.remove("hidden");
            });
        });
    });

    function toggleSection(header) {
        const content = header.nextElementSibling;
        const icon = header.querySelector('.arrow-icon');

        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.src = "{{ asset('images/arrow-up.svg') }}";
        } else {
            content.classList.add('hidden');
            icon.src = "{{ asset('images/down-arr.svg') }}";
        }
    }

    document.addEventListener("livewire:init", () => {
        Livewire.on("request-updated", (event) => {
            Swal.fire({
                title: "Success",
                text: event.message,
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            });

            if (event.threadId) {
                Livewire.emit('openThread', event.threadId);

                // Force User Requests tab active
                const userRequestsTab = document.querySelector('.tab-btn[data-target="user-requests"]');
                if (userRequestsTab) {
                    userRequestsTab.click();
                }
            }
        });
    });


</script>
