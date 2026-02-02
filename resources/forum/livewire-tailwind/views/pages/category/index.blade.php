@php
    // Ensure selectedThread is always defined, defaulting to null if not set
    // This prevents "Undefined variable" errors when the view is rendered
    if (!isset($selectedThread)) {
        $selectedThread = null;
    }
@endphp

<div>
    @role('super_admin')
    <div class="flex-1">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-4 sm:mb-6" id="forumTabs">
            <!-- Tab 1 -->
            <div data-tab="forums" 
                class="tab-btn cursor-pointer relative flex flex-col justify-center border border-[#BDBDBD] 
                bg-white rounded-lg py-4 sm:py-5 px-4 sm:px-6 pr-10 sm:pr-12 hover:bg-[#F8F9FA] hover:border-blue-500 transition-all shadow-sm">
                <p class="font-normal text-[#808080] text-xs sm:text-sm mb-1">Total Forums</p>
                <span class="font-semibold text-[#1B1B1B] text-xl sm:text-2xl">{{ $totalForums }}</span>
                <img src="{{ asset('images/message-multiple-01.svg') }}" alt="" class="absolute right-4 sm:right-6 top-1/2 -translate-y-1/2 w-6 h-6 sm:w-8 sm:h-8">
            </div>

            <!-- Tab 2 -->
            <div data-tab="threads"
                class="tab-btn cursor-pointer relative flex flex-col justify-center border border-[#BDBDBD] 
                bg-white rounded-lg py-4 sm:py-5 px-4 sm:px-6 pr-10 sm:pr-12 hover:bg-[#F8F9FA] hover:border-blue-500 transition-all shadow-sm">
                <p class="font-normal text-[#808080] text-xs sm:text-sm mb-1">Total Threads</p>
                <span class="font-semibold text-[#1B1B1B] text-xl sm:text-2xl">{{ $totalThreads }}</span>
                <img src="{{ asset('images/wechat.svg') }}" alt="" class="absolute right-4 sm:right-6 top-1/2 -translate-y-1/2 w-6 h-6 sm:w-8 sm:h-8">
            </div>

            <!-- Tab 3 -->
            <div data-tab="create-forum"
                class="tab-btn cursor-pointer relative flex flex-col justify-center border border-[#BDBDBD] 
                bg-white rounded-lg py-4 sm:py-5 px-4 sm:px-6 pr-10 sm:pr-12 hover:bg-[#F8F9FA] hover:border-blue-500 transition-all shadow-sm">
                <p class="font-normal text-[#808080] text-xs sm:text-sm mb-1">Create New Forum</p>
                <span class="font-semibold text-[#1B1B1B] text-xs sm:text-sm">Click to create</span>
                <img src="{{ asset('images/add-circle-blue.svg') }}" alt="" class="absolute right-4 sm:right-6 top-1/2 -translate-y-1/2 w-6 h-6 sm:w-8 sm:h-8">
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

            <!-- Create Forum Tab -->
            <div id="tab-create-forum" class="tab-content hidden">
                @livewire('forum::pages.category.create-forum')
            </div>
        </div>

        <!-- 🔹 Chat Layout (Left + Right Side) -->
        <div id="chatLayout" class="flex flex-col lg:flex-row h-[calc(100vh-200px)] gap-4 lg:gap-x-[24px] bg-gray-100 mt-4">

            <!-- Left Sidebar -->
            <div class="w-full lg:w-100 bg-white rounded-xl flex flex-col overflow-hidden shadow-sm">
                <div class="p-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <input type="search" 
                                wire:model.live.debounce.300ms="search"
                                placeholder="Search Topics"
                                class="w-full py-3 px-4 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm pl-10 font-medium bg-[#F8F9FA]" />
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                <img src="{{ asset('images/search.svg') }}" alt="Search" class="w-5 h-5">
                            </div>
                            @if(!empty($search))
                                <button wire:click="$set('search', '')" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                                    type="button">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] p-4">
                    @php
                        $categoriesToDisplay = isset($displayCategories) ? $displayCategories : $categories;
                    @endphp
                    @if(empty($categoriesToDisplay) && !empty($search))
                        <div class="text-center py-8 text-gray-500">
                            <p>No categories found matching "{{ $search }}"</p>
                        </div>
                    @else
                        @foreach($categoriesToDisplay as $category)
                        <div class="mb-4 pb-4 border-b border-gray-100 last:border-b-0">
                            <h4 class="font-semibold text-gray-700 mb-3 hover:text-blue-600 flex justify-between gap-2 items-start cursor-pointer px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors"
                                onclick="toggleSection(this)">
                                <div class="flex flex-col flex-1">
                                    <span class="capitalize text-base mb-2">{{ $category->title }}</span>

                                    <div class="flex items-center gap-4 flex-wrap text-gray-500">
                                        <p class="flex items-center gap-1.5 text-sm text-gray-600">
                                            <img src="{{ asset('images/wechat.svg') }}" alt="Chat Icon" class="w-4 h-4">
                                            <span class="font-medium">{{ $category->threads()->count() ?? 0 }} threads</span>
                                        </p>
                                        <p class="flex items-center gap-1.5 text-sm text-gray-600">
                                            <img src="{{ asset('images/calendar-03.svg') }}" alt="Calendar Icon" class="w-4 h-4">
                                            <span>{{ $category->created_at->format('M d, Y') }}</span>
                                        </p>
                                        <a href="{{ Forum::route('category.edit', $category) }}"
                                            class="flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-700 transition font-medium">
                                            <img src="{{ asset('images/pencil-edit-01.svg') }}" class="w-4 h-4">
                                            <span>Edit</span>
                                        </a>

                                        <a href="{{ Forum::route('thread.create', $category) }}"
                                            class="flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-700 transition font-medium">
                                            <img src="{{ asset('images/add-circle.svg') }}" class="w-4 h-4">
                                            <span>Add New Thread</span>
                                        </a>
                                    </div>
                                </div>
                                <img src="{{ asset('images/down-arr.svg') }}" alt="Expand Icon" class="w-4 h-5 transition-transform duration-200 arrow-icon flex-shrink-0 mt-1" />
                            </h4>

                            <div class="hidden section-content pl-4">
                                @if ($category->accepts_threads && $category->threads->count())
                                    <ol class="text-gray-600 mt-3 space-y-1.5 font-medium text-sm">
                                        @foreach($category->threads as $index => $thread)
                                            @php
                                                $isActive = isset($selectedThread) && $selectedThread && $selectedThread->id === $thread->id;
                                            @endphp
                                            <li wire:key="thread-{{ $thread->id }}" 
                                                class="group p-2.5 pl-4 hover:bg-blue-50 rounded-lg border border-transparent hover:border-blue-200 flex items-start gap-2 transition-colors">
                                                <span class="text-gray-400 font-medium">{{ $index + 1 }}.</span>
                                                <a href="{{ Forum::route('thread.show', $thread) }}" 
                                                    class="text-gray-700 hover:text-blue-600 capitalize flex-1 transition-colors">
                                                    {{ $thread->title }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ol>
                                @else
                                    <p class="text-gray-400 text-sm mt-3 px-4 py-2 bg-gray-50 rounded-lg">No threads available</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Right Content -->
            <section id="main-content" class="flex-1 flex flex-col bg-white rounded-xl shadow-sm p-4 sm:p-6 md:p-8">
                @if($showDirectChat)
                    <!-- Direct Chat Section -->
                    <div class="flex flex-col h-full">
                        <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">Direct Chat</h2>
                                <p class="text-sm text-gray-600 mt-1">Chat with any user in the system</p>
                            </div>
                            <button wire:click="toggleDirectChat" 
                                class="px-4 py-2 text-gray-600 hover:text-gray-900 transition">
                                <i class="fa-solid fa-arrow-left mr-2"></i>Back to Forums
                            </button>
                        </div>
                        
                        <!-- Search Users -->
                        <div class="mb-4">
                            <div class="relative">
                                <input type="text" 
                                    wire:model.live.debounce.300ms="chatSearch"
                                    placeholder="Search users by name or email..." 
                                    class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                @if(!empty($chatSearch))
                                    <button wire:click="$set('chatSearch', '')" 
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                                        type="button">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex-1 overflow-y-auto">
                            @if(count($allUsers) > 0)
                                <div class="space-y-3">
                                    @foreach($allUsers as $item)
                                        @php
                                            $chatUser = $item['user'];
                                            $unreadCount = $item['unread_count'];
                                            $lastMessage = $item['last_message'];
                                        @endphp
                                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer" 
                                            wire:click="openMessageModal({{ $chatUser->id }})">
                                            <div class="flex items-start gap-4">
                                                <div class="relative">
                                                    <img src="{{ $chatUser->profile_photo_url ?? '/default-avatar.png' }}" 
                                                        alt="{{ $chatUser->name }}" 
                                                        class="w-12 h-12 rounded-full border-2 {{ $unreadCount > 0 ? 'border-red-500' : 'border-blue-500' }}">
                                                    @if($unreadCount > 0)
                                                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                                                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center justify-between">
                                                        <h3 class="font-semibold text-gray-900">
                                                            {{ $chatUser->name }}
                                                            @if($unreadCount > 0)
                                                                <span class="text-red-500 text-sm font-normal">({{ $unreadCount }} unread)</span>
                                                            @endif
                                                        </h3>
                                                    </div>
                                                    @if($lastMessage)
                                                        <p class="text-sm text-gray-500 mt-1 truncate">
                                                            {{ $lastMessage->sender_id === auth()->id() ? 'You: ' : '' }}{{ Str::limit($lastMessage->message, 60) }}
                                                        </p>
                                                        <p class="text-xs text-gray-400 mt-1">
                                                            {{ $lastMessage->created_at->diffForHumans() }}
                                                        </p>
                                                    @else
                                                        <p class="text-sm text-gray-400 mt-1">No messages yet</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <i class="fa-solid fa-users text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500 text-lg">No users available</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif(isset($selectedThread) && $selectedThread)
                    @livewire('forum::categories.thread-view', ['threadId' => $selectedThread->id], key('thread-' . $selectedThread->id))
                @else
                    <div id="welcome-section" class="flex flex-col items-center justify-center min-h-[300px] sm:min-h-[400px] h-full text-center py-6 sm:py-12 px-4">
                        <div class="bg-blue-600 rounded-full p-3 sm:p-4 mb-4 sm:mb-6 shadow-lg flex items-center justify-center">
                            <img src="{{ asset('images/department-forums-image-02-blue.svg') }}" class="w-[60px] h-[60px] sm:w-[80px] sm:h-[80px]" alt="WeChat Icon" />
                        </div>
                        <h1 class="text-2xl sm:text-3xl md:text-4xl font-semibold text-blue-600 mb-3 sm:mb-4">Welcome to Department Forums</h1>
                        <p class="text-gray-600 text-base sm:text-lg max-w-md mb-6 sm:mb-8">Select a forum category from the left to view and participate in discussions.</p>
                        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 items-stretch sm:items-center justify-center w-full sm:w-auto">
                            <a href="{{ route('forum.messages.index') }}" 
                                class="px-6 sm:px-8 py-3 sm:py-3.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium text-sm sm:text-base inline-flex items-center justify-center shadow-md hover:shadow-lg whitespace-nowrap min-w-[160px]">
                                <svg class="w-5 h-5 mr-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span>Start Direct Chat</span>
                            </a>
                            <button wire:click="$dispatch('openSendMessage')" 
                                class="px-6 sm:px-8 py-3 sm:py-3.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-200 font-medium text-sm sm:text-base inline-flex items-center justify-center shadow-md hover:shadow-lg whitespace-nowrap min-w-[160px]">
                                <svg class="w-5 h-5 mr-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span>New Message</span>
                            </button>
                        </div>
                        <livewire:forum.send-message />
                    </div>
                @endif
            </section>
        </div>

    </div>
    @endrole
    
    @if(!auth()->user() || !auth()->user()->hasRole('super_admin'))
    <div class="flex-1">
    <div class="flex flex-col lg:flex-row user-forum h-[calc(100vh-100px)] gap-4 lg:gap-x-[24px] bg-gray-100">

        <!-- Left Sidebar -->
        <div class="w-full lg:w-90 bg-white rounded-xl flex flex-col">
            <div class="p-4">
                <!-- Search -->
                <div class="flex items-center gap-2 mb-4">
                    <div class="relative flex-1">
                        <input type="search" 
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search Topics"
                            class="w-full py-3 px-4 rounded-lg border border-gray-200 focus:outline-none focus:border focus:!border-blue-200 text-sm !pl-[40px] font-medium !bg-[#F8F9FA]" />
                        <div class="!absolute !left-3 !top-1/2 !transform !-translate-y-1/2 pointer-events-none">
                            <img src="{{ asset('images/search.svg') }}" alt="">
                        </div>
                        @if(!empty($search))
                            <button wire:click="$set('search', '')" 
                                class="!absolute !right-3 !top-1/2 !transform !-translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                                type="button">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Forum List -->
            <nav class="flex-1 overflow-y-auto px-4 pb-4 [scrollbar-width:none]">
                @php
                    $categoriesToDisplay = isset($displayCategories) ? $displayCategories : $categories;
                @endphp
                @if(empty($categoriesToDisplay) && !empty($search))
                    <div class="text-center py-8 text-gray-500">
                        <p>No categories found matching "{{ $search }}"</p>
                    </div>
                @else
                    @foreach($categoriesToDisplay as $category)
                    <div class="mb-4 pb-4 border-b border-gray-100 last:border-b-0">
                        <h4 class="font-semibold text-gray-700 mb-3 hover:text-blue-600 flex justify-between gap-2 items-start cursor-pointer px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors"
                            onclick="toggleSection(this)">
                            
                            <div class="flex flex-col flex-1">
                                <span class="capitalize text-base mb-2">{{ $category->title }}</span>

                                <div class="flex items-center gap-4 flex-wrap text-gray-500">
                                    {{-- Chat Icon + Thread Count --}}
                                    <p class="flex items-center gap-1.5 text-sm text-gray-600">
                                        <img src="{{ asset('images/wechat.svg') }}" alt="Chat Icon" class="w-4 h-4" />
                                        <span class="font-medium">{{ $category->threads()->count() ?? 0 }} threads</span>
                                    </p>

                                    {{-- Calendar Icon + Created Date --}}
                                    <p class="flex items-center gap-1.5 text-sm text-gray-600">
                                        <img src="{{ asset('images/calendar-03.svg') }}" alt="Calendar Icon" class="w-4 h-4" />
                                        <span>{{ $category->created_at->format('M d, Y') }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Arrow icon -->
                            <img src="{{ asset('images/down-arr.svg') }}" alt="Expand Icon" 
                                class="w-4 h-5 transition-transform duration-200 arrow-icon flex-shrink-0 mt-1" />
                        </h4>

                        <div class="hidden section-content pl-4">
                            @if ($category->accepts_threads && $category->threads->count())
                                <ol class="text-gray-600 mt-3 space-y-1.5 font-medium text-sm">
                                    @foreach($category->threads as $index => $thread)
                                        @php
                                            $isActive = isset($selectedThread) && $selectedThread->id === $thread->id;
                                        @endphp

                                        <li wire:key="thread-{{ $thread->id }}" 
                                            class="group p-2.5 pl-4 hover:bg-blue-50 rounded-lg border border-transparent hover:border-blue-200 flex items-start gap-2 transition-colors {{ $isActive ? 'bg-blue-50 border-blue-200' : '' }}">

                                            <!-- Thread Title -->
                                            <div class="flex items-center gap-2 flex-1">
                                                <span class="text-gray-400 font-medium">{{ $index + 1 }}.</span>
                                                <a href="{{ Forum::route('thread.show', $thread) }}"
                                                    class="text-gray-700 hover:text-blue-600 capitalize flex-1 transition-colors">
                                                    {{ $thread->title }}
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ol>
                            @else
                                <p class="text-gray-400 text-sm mt-3 px-4 py-2 bg-gray-50 rounded-lg">No threads available</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @endif
            </nav>
        </div>

        <!-- Right Content -->
        <section id="main-content" class="flex-1 flex flex-col bg-white rounded-xl shadow-sm p-6 md:p-8">
            @if($showDirectChat)
                <!-- Direct Chat Section -->
                <div class="flex flex-col h-full">
                    <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Direct Chat</h2>
                            <p class="text-sm text-gray-600 mt-1">Chat with any user in the system</p>
                        </div>
                        <button wire:click="toggleDirectChat" 
                            class="px-4 py-2 text-gray-600 hover:text-gray-900 transition">
                            <i class="fa-solid fa-arrow-left mr-2"></i>Back to Forums
                        </button>
                    </div>
                    
                    <!-- Search Users -->
                    <div class="mb-4">
                        <div class="relative">
                            <input type="text" 
                                wire:model.live.debounce.300ms="chatSearch"
                                placeholder="Search users by name or email..." 
                                class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            @if(!empty($chatSearch))
                                <button wire:click="$set('chatSearch', '')" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                                    type="button">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto">
                        @if(count($allUsers) > 0)
                            <div class="space-y-3">
                                @foreach($allUsers as $item)
                                    @php
                                        $chatUser = $item['user'];
                                        $unreadCount = $item['unread_count'];
                                        $lastMessage = $item['last_message'];
                                    @endphp
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer" 
                                        wire:click="openMessageModal({{ $chatUser->id }})">
                                        <div class="flex items-start gap-4">
                                            <div class="relative">
                                                <img src="{{ $chatUser->profile_photo_url ?? '/default-avatar.png' }}" 
                                                    alt="{{ $chatUser->name }}" 
                                                    class="w-12 h-12 rounded-full border-2 {{ $unreadCount > 0 ? 'border-red-500' : 'border-blue-500' }}">
                                                @if($unreadCount > 0)
                                                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                                                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between">
                                                    <h3 class="font-semibold text-gray-900">
                                                        {{ $chatUser->name }}
                                                        @if($unreadCount > 0)
                                                            <span class="text-red-500 text-sm font-normal">({{ $unreadCount }} unread)</span>
                                                        @endif
                                                    </h3>
                                                </div>
                                                @if($lastMessage)
                                                    <p class="text-sm text-gray-500 mt-1 truncate">
                                                        {{ $lastMessage->sender_id === auth()->id() ? 'You: ' : '' }}{{ Str::limit($lastMessage->message, 60) }}
                                                    </p>
                                                    <p class="text-xs text-gray-400 mt-1">
                                                        {{ $lastMessage->created_at->diffForHumans() }}
                                                    </p>
                                                @else
                                                    <p class="text-sm text-gray-400 mt-1">No messages yet</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fa-solid fa-users text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg">No users available</p>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif(isset($selectedThread) && $selectedThread)
                    {{-- Livewire Thread View --}}
                    @livewire('forum::categories.thread-view', ['threadId' => $selectedThread->id], key('thread-' . $selectedThread->id))
            @else
                <div id="welcome-section" class="flex flex-col items-center justify-center min-h-[300px] sm:min-h-[400px] h-full text-center py-6 sm:py-12 px-4">
                    <div class="bg-blue-600 rounded-full p-3 sm:p-4 mb-4 sm:mb-6 shadow-lg flex items-center justify-center">
                        <img src="{{ asset('images/department-forums-image-02-blue.svg') }}" class="w-[60px] h-[60px] sm:w-[80px] sm:h-[80px]" alt="WeChat Icon" />
                    </div>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-semibold text-blue-600 mb-3 sm:mb-4">
                        Welcome to Department Forums
                    </h1>
                    <p class="text-gray-600 text-base sm:text-lg max-w-md mb-6 sm:mb-8">Select a forum category from the left to view and participate in discussions.</p>
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 items-stretch sm:items-center justify-center w-full sm:w-auto">
                        <a href="{{ route('forum.messages.index') }}" 
                            class="px-6 sm:px-8 py-3 sm:py-3.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium text-sm sm:text-base inline-flex items-center justify-center shadow-md hover:shadow-lg whitespace-nowrap min-w-[160px]">
                            <svg class="w-5 h-5 mr-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <span>Start Direct Chat</span>
                        </a>
                        <button wire:click="$dispatch('openSendMessage')" 
                            class="px-6 sm:px-8 py-3 sm:py-3.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-200 font-medium text-sm sm:text-base inline-flex items-center justify-center shadow-md hover:shadow-lg whitespace-nowrap min-w-[160px]">
                            <svg class="w-5 h-5 mr-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span>New Message</span>
                        </button>
                    </div>
                    <livewire:forum.send-message />
                </div>
            @endif
        </section>

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
