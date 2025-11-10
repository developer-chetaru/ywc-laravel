<div>
    @role('super_admin')
        <!-- === Super Admin Forum Layout === -->
        <main class="flex-1 p-6">
            <div class="flex h-[calc(100vh-100px)] gap-x-[24px] bg-gray-100">

                <!-- Left Sidebar (Search + Tabs + List) -->
                <div class="w-90 bg-white rounded-xl flex flex-col">
                    <div class="p-4">

                        <!-- Search -->
                        <div class="flex items-center gap-2 mb-4">
                            <form class="relative flex-1" action="">
                                <input type="search" placeholder="Search Topics"
                                       class="w-full py-3 px-4 rounded-lg border border-gray-200 focus:outline-none focus:ring focus:ring-blue-200 text-sm pl-[40px] font-medium bg-[#F8F9FA]" />
                                <button class="absolute left-3 top-1/2 transform -translate-y-1/2" type="submit">
                                    <img src="{{ asset('images/search.svg') }}" alt="">
                                </button>
                            </form>
                            <button class="py-3 px-4 border border-gray-200 rounded-lg bg-[#F8F9FA] hover:bg-gray-200">
                                <img src="{{ asset('images/filter.svg') }}" alt="">
                            </button>
                        </div>

                        <!-- Tabs -->
                        <div class="flex border-b border-gray-200 mb-4">
                            <button class="tab-btn px-4 py-2 text-sm font-medium border-b-2 border-blue-600 text-blue-600 font-semibold"
                                    data-target="forum-list">
                                Forum List
                            </button>
                            <button class="tab-btn px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-blue-600 hover:border-blue-600"
                                    data-target="user-requests">
                                Users Request
                            </button>
                        </div>
                    </div>

                    <!-- Forum List -->
                    <nav id="forum-list" class="tab-content flex-1 overflow-y-auto px-4 pb-4 pl-[30px]">
                        <div>
                            <div class="mb-12">
                                <!-- Heading -->
                                <h4 class="font-medium text-[#808080] mb-[13px] flex justify-between gap-2 items-center cursor-pointer"
                                    onclick="toggleSection(this)">
                                    <span>Onboard Safety</span>
                                    <img src="{{ asset('images/down-arr.svg') }}" alt="Expand Icon"
                                        class="h-fit transition-transform duration-200" />
                                </h4>

                                <!-- Hidden Section -->
                                <div class="hidden section-content">
                                    <div class="flex items-center gap-4 text-sm text-gray-500">
                                        <p class="flex items-center gap-1">
                                            <img src="{{ asset('images/wechat.svg') }}" alt="Chat Icon" class="w-4 h-4" />
                                            <span>4</span>
                                        </p>
                                        <p class="flex items-center gap-1">
                                            <img src="{{ asset('images/calendar-03.svg') }}" alt="Calendar Icon" class="w-4 h-4" />
                                            <span>Aug 30, 2025</span>
                                        </p>
                                    </div>

                                    <ol class="list-decimal text-[#808080] mt-8 space-y-6 font-medium pl-5 text-sm">
                                        <li>How often should we conduct fire drills?</li>
                                        <li>Onboard Safety - ship</li>
                                        <li>Case Studies & Incidents</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </nav>

                    <!-- User Requests -->
                    <nav id="user-requests" class="tab-content hidden flex-1 overflow-y-auto px-4 pb-4 pl-[30px]">
                        <div class="mb-12">
                            <h4 class="font-medium text-[#808080] mb-[13px]">Users Requests</h4>
                            <ol class="list-decimal text-[#808080] mt-4 space-y-4 font-medium pl-5 text-sm">
                                <li>Request: Add new category</li>
                                <li>Request: Dark mode support</li>
                                <li>Request: Better notifications</li>
                            </ol>
                        </div>
                    </nav>
                </div>

                <!-- Main Content -->
                <div class="flex-1 flex flex-col bg-white rounded-xl relative">
                    <div class="border-b border-solid border-gray-400">
                        <div class="p-5">
                            <h3 class="font-medium mb-[10px] text-2xl text-[#0053FF]">
                                How often should we conduct fire drills?
                            </h3>
                            <div class="flex gap-x-[24px]">
                                <p class="text-[#808080]">Date: <span>Aug 30, 2025</span></p>
                                <p class="text-[#808080]">Number of Replies: <span>12</span></p>
                                <p class="text-[#808080]">Last Updated: <span>Sept 02, 2025</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 h-[calc(100%-150px)] overflow-y-auto">
                        <!-- Replies Section -->
                    </div>
                    <div class="sticky bottom-0 bg-white p-5 mt-auto">
                        <div class="relative">
                            <textarea placeholder="Write your reply" class="bg-[#F8F9FA] border border-gray-200 text-[#808080] focus:outline-none h-[60px] pt-4 px-4 py-3 resize-none rounded-lg w-full pl-[50px]"></textarea>
                            <img src="{{ asset('images/smile.svg') }}" alt="" class="absolute h-[20px] left-4 object-contain top-[20px] w-[20px]">
                            <button class="top-[20px] absolute right-3 w-[24px] h-[24px] cursor-pointer">
                                <img src="{{ asset('images/sent.svg') }}" alt="">
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    @elserole('user')
        <div class="my-4" style="{{ $category->styleVariables }}">
            <div class="bg-white shadow-md rounded-lg p-4 flex flex-col sm:flex-row sm:items-center sm:content-center dark:bg-slate-700">
                <div class="hidden sm:block self-stretch">
                    <div class="w-3 h-full rounded-full mr-4 bg-category"></div>
                </div>
                <div class="grow text-center sm:text-left">
                    <h2>
                        <a href="{{ $category->route }}" class="text-category">{{ $category->title }}</a>
                    </h2>
                    <h3 class="text-slate-600 dark:text-slate-400">{{ $category->description }}</h3>
                </div>
                <div class="text-center text-base mt-2 sm:mt-0">
                    @if ($category->accepts_threads)
                        <livewire:forum::components.pill
                            icon="chat-bubbles-mini"
                            :text="trans_choice('forum::threads.thread', 2) . ': ' . $category->thread_count" />
                        <livewire:forum::components.pill
                            icon="chat-bubble-text-mini"
                            :text="trans_choice('forum::posts.post', 2) . ': ' . $category->post_count" />
                    @endif
                </div>
                <div class="min-w-30 sm:min-w-48 lg:min-w-96 xl:w-full xl:max-w-lg text-center sm:text-right mt-2 sm:mt-0">
                    @if ($category->accepts_threads)
                        @if ($category->newestThread)
                            <div>
                                @include ("forum::components.icons.chat-bubbles-mini")
                                <a href="{{ $category->newestThread->route }}" class="inline-block max-w-36 md:max-w-48 lg:max-w-64 truncate align-middle">
                                    {{ $category->newestThread->title }}
                                </a>
                                <span class="inline-block align-middle">
                                    <livewire:forum::components.timestamp :carbon="$category->newestThread->created_at" />
                                </span>
                            </div>
                        @endif
                        @if ($category->latestActiveThread && $category->latestActiveThread->reply_count > 1)
                            <div>
                                @include ("forum::components.icons.chat-bubble-text-mini")
                                <a href="{{ $category->latestActiveThread->lastPost->route }}" class="inline-block max-w-36 md:max-w-48 lg:max-w-64 truncate align-middle">
                                    Re: {{ $category->latestActiveThread->title }}
                                </a>
                                <span class="inline-block align-middle">
                                    <livewire:forum::components.timestamp :carbon="$category->latestActiveThread->lastPost->created_at" />
                                </span>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            @if (count($category->children) > 0)
                @foreach ($category->children as $subcategory)
                    <div class="flex mt-4" style="{{ $subcategory->styleVariables }}">
                        <div class="min-w-12 sm:min-w-24 self-center text-center text-slate-300 dark:text-slate-700">
                            @include ('forum::components.icons.subcategory', ['size' => '12'])
                        </div>
                        <div class="grow flex flex-col sm:flex-row sm:items-center sm:content-center items-center justify-items-center bg-white shadow-md rounded-lg p-4 dark:bg-slate-700">
                            <div class="grow text-center sm:text-left">
                                <h3>
                                    <a href="{{ $subcategory->route }}" class="text-category">{{ $subcategory->title }}</a>
                                </h3>
                                <h3 class="text-slate-600 text-base dark:text-slate-400">{{ $subcategory->description }}</h3>
                            </div>
                            <div class="text-center text-base mt-2 sm:mt-0">
                                @if ($subcategory->accepts_threads)
                                    <livewire:forum::components.pill
                                        icon="chat-bubbles-mini"
                                        :text="trans_choice('forum::threads.thread', 2) . ': ' . $subcategory->thread_count" />
                                    <livewire:forum::components.pill
                                        icon="chat-bubble-text-mini"
                                        :text="trans_choice('forum::posts.post', 2) . ': ' . $subcategory->post_count" />
                                @endif
                            </div>
                            <div class="min-w-30 sm:min-w-48 lg:min-w-96 xl:w-full xl:max-w-lg text-center sm:text-right mt-2 sm:mt-0">
                                @if ($subcategory->accepts_threads)
                                    @if ($subcategory->newestThread)
                                        <div>
                                            @include ("forum::components.icons.chat-bubbles-mini")
                                            <a href="{{ $subcategory->newestThread->route }}" class="inline-block max-w-36 md:max-w-48 lg:max-w-64 truncate align-middle">
                                                {{ $subcategory->newestThread->title }}
                                            </a>
                                            <span class="inline-block align-middle">
                                                <livewire:forum::components.timestamp :carbon="$subcategory->newestThread->created_at" />
                                            </span>
                                        </div>
                                    @endif
                                    @if ($subcategory->latestActiveThread && $subcategory->latestActiveThread->reply_count > 1)
                                        <div>
                                            @include ("forum::components.icons.chat-bubble-text-mini")
                                            <a href="{{ $subcategory->latestActiveThread->lastPost->route }}" class="inline-block max-w-36 md:max-w-48 lg:max-w-64 truncate align-middle">
                                                Re: {{ $subcategory->latestActiveThread->title }}
                                            </a>
                                            <span class="inline-block align-middle">
                                                <livewire:forum::components.timestamp :carbon="$subcategory->latestActiveThread->lastPost->created_at" />
                                            </span>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    @endrole
</div>

<!-- Tabs Script -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const buttons = document.querySelectorAll(".tab-btn");
        const contents = document.querySelectorAll(".tab-content");

        buttons.forEach(btn => {
            btn.addEventListener("click", () => {
                buttons.forEach(b => b.classList.remove("border-blue-600", "text-blue-600", "font-semibold"));
                buttons.forEach(b => b.classList.add("border-transparent", "text-gray-500"));

                btn.classList.add("border-blue-600", "text-blue-600", "font-semibold");
                btn.classList.remove("border-transparent", "text-gray-500");

                const target = btn.getAttribute("data-target");
                contents.forEach(c => c.classList.add("hidden"));
                document.getElementById(target).classList.remove("hidden");
            });
        });
    });

    function toggleSection(header) {
        const content = header.nextElementSibling;
        const icon = header.querySelector('img');

        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.src = "{{ asset('images/up-arr.svg') }}"; // Change arrow up
        } else {
            content.classList.add('hidden');
            icon.src = "{{ asset('images/down-arr.svg') }}"; // Change arrow down
        }
    }
</script>
