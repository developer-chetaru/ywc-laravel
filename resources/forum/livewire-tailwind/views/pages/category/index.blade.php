<div>
    @role('super_admin')
    <main class="flex-1">

        <div class="flex gap-[16px] mb-6" id="forumTabs">
            <!-- Tab 1 -->
            <div data-tab="forums" 
                class="tab-btn cursor-pointer relative flex-1 flex-col justify-center flex border border-[#BDBDBD] 
                bg-white rounded-lg py-4 px-6 pr-12 hover:bg-[#F8F9FA] transition">
                <p class="font-normal text-[#808080] text-sm">Total Forums</p>
                <span class="font-medium text-[#1B1B1B] text-xl">{{ $totalForums }}</span>
                <img src="{{ asset('images/message-multiple-01.svg') }}" alt="" class="!-translate-y-1/2 absolute right-6 top-1/2">
            </div>

            <!-- Tab 2 -->
            <div data-tab="threads"
                class="tab-btn cursor-pointer relative flex-1 flex-col justify-center flex border border-[#BDBDBD] 
                bg-white rounded-lg py-4 px-6 pr-12 hover:bg-[#F8F9FA] transition">
                <p class="font-normal text-[#808080] text-sm">Total Threads</p>
                <span class="font-medium text-[#1B1B1B] text-xl">{{ $totalThreads }}</span>
                <img src="{{ asset('images/wechat.svg') }}" alt="" class="!-translate-y-1/2 absolute right-6 top-1/2">
            </div>

            <!-- Tab 3 -->
            <div data-tab="create-forum"
                class="tab-btn cursor-pointer relative flex-1 flex-col justify-center flex border border-[#BDBDBD] 
                bg-white rounded-lg py-4 px-6 pr-12 hover:bg-[#F8F9FA] transition">
                <p class="font-normal text-[#808080] text-sm">Create New Forum</p>
                <img src="{{ asset('images/add-circle-blue.svg') }}" alt="" class="!-translate-y-1/2 absolute right-6 top-1/2">
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
        <div id="chatLayout" class="flex h-[calc(100vh-200px)] gap-x-[24px] bg-gray-100 mt-4">

            <!-- Left Sidebar -->
            <div class="w-100 bg-white rounded-xl flex flex-col overflow-y-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                <div class="p-4">
                    <div class="flex items-center gap-2 mb-4">
                        <form class="relative flex-1" action="">
                            <input type="search" placeholder="Search Topics"
                                class="w-full py-3 px-4 rounded-lg border border-gray-200 focus:outline-none focus:border focus:!border-blue-200 text-sm !pl-[40px] font-medium !bg-[#F8F9FA]" />
                            <button class="!absolute !left-3 !top-1/2 !transform !-translate-y-1/2" type="submit">
                                <img src="{{ asset('images/search.svg') }}" alt="">
                            </button>
                        </form>
                        <button class="py-3 px-4 border border-gray-200 rounded-lg !bg-[#F8F9FA] hover:bg-gray-200">
                            <img src="{{ asset('images/filter.svg') }}" alt="">
                        </button>
                    </div>
                </div>

                @foreach($categories as $category)
                    <div class="mb-5 pb-4">
                        <h4 class="font-medium text-[#808080] mb-[13px] hover:text-[#1B1B1B] flex justify-between gap-2 items-start cursor-pointer px-5 !pl-[40px]"
                            onclick="toggleSection(this)">
                            <div class="flex flex-col">
                                <span class="capitalize">{{ $category->title }}</span>

                                <div class="flex items-center gap-3 text-gray-500 mt-3">
                                    <p class="flex items-center gap-1 text-[13px] text-[#808080]">
                                        <img src="{{ asset('images/wechat.svg') }}" alt="Chat Icon" class="w-4 h-4">
                                        <span>{{ $category->threads()->count() ?? 0 }}</span>
                                    </p>
                                    <p class="flex items-center gap-1 text-[13px] text-[#808080]">
                                        <img src="{{ asset('images/calendar-03.svg') }}" alt="Calendar Icon" class="w-4 h-4">
                                        <span>{{ $category->created_at->format('M d, Y') }}</span>
                                    </p>
                                    <a href="{{ route('category.edit', ['category_id' => $category->id, 'category_slug' => \Illuminate\Support\Str::slug($category->title)]) }}"
                                        class="flex items-center gap-1 text-[13px] text-[#808080] hover:text-black transition">
                                        <img src="{{ asset('images/pencil-edit-01.svg') }}" class="w-4 h-4">
                                        <span>Edit</span>
                                    </a>

                                    <a href="{{ route('thread.create', ['category_id' => $category->id, 'category_slug' => \Illuminate\Support\Str::slug($category->title)]) }}"
                                        class="flex items-center gap-1 text-[13px] text-[#808080] hover:text-black transition">
                                        <img src="{{ asset('images/add-circle.svg') }}" class="w-4 h-4">
                                        <span>Add New Thread</span>
                                    </a>
                                </div>
                            </div>
                            <img src="{{ asset('images/down-arr.svg') }}" alt="Expand Icon" class="w-3 h-5 transition-transform duration-200 arrow-icon" />
                        </h4>

                        <div class="hidden section-content">
                            @if ($category->accepts_threads && $category->threads->count())
                                <ol class="text-[#808080] mt-6 space-y-2 font-medium text-[16px]">
                                    @foreach($category->threads as $index => $thread)
                                        <li class="group p-3 pl-[25px] hover:bg-[#F8F9FA] rounded-lg border border-transparent hover:!border-[#E5E5E5] flex items-start gap-2 mx-5">
                                            <span class="text-[#808080]">{{ $index + 1 }}.</span>
                                            <a href="javascript:void(0);" 
                                                wire:click="loadThread({{ $thread->id }})" 
                                                class="!text-[#808080] capitalize">
                                                {{ $thread->title }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ol>
                            @else
                                <p class="text-gray-400 text-sm mt-5 px-10 mx-5">No threads available</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Right Content -->
            <section id="main-content" class="flex-1 flex flex-col bg-white rounded-xl shadow-sm !p-0 md:p-8">
                @if($selectedThread)
                    @livewire('forum::categories.thread-view', ['threadId' => $selectedThread->id], key($selectedThread->id . '-' . Str::random(8)))
                @else
                    <div id="welcome-section" class="flex flex-col items-center justify-center h-full text-center">
                        <div class="bg-blue-600 rounded-full mb-6 shadow-lg">
                            <img src="{{ asset('images/department-forums-image-02-blue.svg') }}" class="w-[80px] h-[80px]" alt="WeChat Icon" />
                        </div>
                        <h1 class="text-3xl font-semibold text-blue-600 mb-2">Welcome to Department Forums</h1>
                    </div>
                @endif
            </section>
        </div>

    </main>
    @endrole
    <main class="flex-1">
    <div class="flex user-forum h-[calc(100vh-100px)] gap-x-[24px] bg-gray-100">

        <!-- Left Sidebar -->
        <div class="w-90 bg-white rounded-xl flex flex-col">
            <div class="p-4">
                <!-- Search -->
                <div class="flex items-center gap-2 mb-4">
                    <form class="relative flex-1" action="">
                        <input type="search" placeholder="Search Topics"
                            class="w-full py-3 px-4 rounded-lg border border-gray-200 focus:outline-none focus:border focus:!border-blue-200 text-sm !pl-[40px] font-medium !bg-[#F8F9FA]" />
                        <button class="!absolute !left-3 !top-1/2 !transform !-translate-y-1/2" type="submit">
                            <img src="{{ asset('images/search.svg') }}" alt="">
                        </button>
                    </form>
                </div>
            </div>

            <!-- Forum List -->
            <nav class="flex-1 overflow-y-auto px-0 pb-4 [scrollbar-width:none]">
                @foreach($categories as $category)
                    <div class="mb-5 pb-4">
                        <h4 class="font-medium text-[#808080] mb-[13px] hover:text-[#1B1B1B] flex justify-between gap-2 items-start cursor-pointer px-5 !pl-[40px]"
                            onclick="toggleSection(this)">
                            
                            <div class="flex flex-col">
                                <span class="capitalize">{{ $category->title }}</span>

                                <div class="flex items-center gap-4 text-xs text-gray-500 mt-3">
                                    {{-- Chat Icon + Thread Count --}}
                                    <p class="flex items-center gap-1 text-sm">
                                        <img src="{{ asset('images/wechat.svg') }}" alt="Chat Icon" class="w-4 h-4" />
                                        <span>{{ $category->threads()->count() ?? 0 }}</span>
                                    </p>

                                    {{-- Calendar Icon + Created Date --}}
                                    <p class="flex items-center gap-1 text-sm">
                                        <img src="{{ asset('images/calendar-03.svg') }}" alt="Calendar Icon" class="w-4 h-4" />
                                        <span>{{ $category->created_at->format('M d, Y') }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Arrow icon -->
                            <img src="{{ asset('images/down-arr.svg') }}" alt="Expand Icon" 
                                class="w-3 h-5 transition-transform duration-200 arrow-icon" />
                        </h4>

                        <div class="hidden section-content">
                            @if ($category->accepts_threads && $category->threads->count())
                                <ol class="text-[#808080] mt-6 space-y-2 font-medium text-[16px]">
                                    @foreach($category->threads as $index => $thread)
                                        @php
                                            $isActive = isset($selectedThread) && $selectedThread->id === $thread->id;
                                        @endphp

                                        <li wire:key="thread-{{ $thread->id }}" 
                                            class="group p-3 pl-[25px] hover:bg-[#F8F9FA] rounded-lg border border-transparent hover:!border-[#E5E5E5] flex items-start gap-2 mx-5">

                                            <!-- Thread Title -->
                                            <div class="flex items-center gap-2">
                                                <span class="text-[#808080]">{{ $index + 1 }}.</span>
                                                <a href="javascript:void(0)" wire:click="openThread({{ $thread->id }})"
                                                    class="{{ $isActive ? '!text-[#0053FF]' : 'text-blue-600' }} capitalize">
                                                    {{ $thread->title }}
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ol>
                            @else
                                <p class="text-gray-400 text-sm mt-2">No threads available</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </nav>
        </div>

        <!-- Right Content -->
        <section id="main-content" class="flex-1 flex flex-col bg-white rounded-xl shadow-sm">
            @if($selectedThread)
                <div x-data="{ threadKey: '{{ $selectedThread->id }}-{{ Str::random(8) }}' }"
                    x-init="$watch('selectedThread', value => {
                        threadKey = '{{ $selectedThread->id }}-' + Math.random().toString(36).substring(2, 10);
                    })"
                    @request-updated.window="if ($event.detail.threadId === {{ $selectedThread->id }}) {
                        threadKey = '{{ $selectedThread->id }}-' + Math.random().toString(36).substring(2, 10);
                    }">

                    {{-- Livewire Thread View --}}
                    @livewire('forum::categories.thread-view', ['threadId' => $selectedThread->id], key($selectedThread->id . '-' . Str::random(8)))
                </div>
            @else
                <div id="welcome-section" class="flex flex-col items-center justify-center h-full text-center">
                    <div class="bg-blue-600 rounded-full mb-6 shadow-lg">
                        <img src="{{ asset('images/department-forums-image-02-blue.svg') }}" class="w-[80px] h-[80px] text-white" alt="WeChat Icon" />
                    </div>
                    <h1 class="text-3xl font-semibold text-blue-600 mb-2">
                        Welcome to Department Forums
                    </h1>
                </div>
            @endif
        </section>

    </div>
</main>


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
