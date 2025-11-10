<div>
    @role('super_admin')
    <div class="flex">
        <div class="grow">
        </div>
        <div>
            @can ('createCategories')
                <x-forum::link-button
                    :label="trans('forum::categories.create')"
                    icon="squares-plus-outline"
                    :href="Forum::route('category.create')"
                    class="link-button inline-block rounded-md font-medium text-l text-center text-white bg-blue-600 hover:text-white hover:bg-blue-500 min-w-36 px-4 py-2" />
            @endcan
        </div>
    </div>
    @endrole

    <div>
        @role('super_admin')
            <!-- === Super Admin Forum Layout === -->
            <main class="flex-1 p-6">
                <div class="flex h-[calc(100vh-100px)] gap-6 bg-gray-100">

                    <!-- Sidebar -->
                    <aside class="w-96 bg-white rounded-xl flex flex-col shadow-sm">
                        <div class="p-4 border-b border-gray-100">

                            <!-- Search + Filter -->
                            <div class="flex items-center gap-2 mb-4">
                                <form class="relative flex-1">
                                    <input type="search" placeholder="Search Topics"
                                        class="w-full py-2.5 px-4 rounded-lg border border-gray-200 focus:outline-none focus:ring focus:ring-blue-200 text-sm pl-10 bg-[#F8F9FA] font-medium" />
                                    <button class="absolute left-3 top-1/2 -translate-y-1/2" type="submit">
                                        <img src="{{ asset('images/search.svg') }}" alt="Search">
                                    </button>
                                </form>
                                <button
                                    class="p-2 border border-gray-200 rounded-lg bg-[#F8F9FA] hover:bg-gray-200 transition">
                                    <img src="{{ asset('images/filter.svg') }}" alt="Filter">
                                </button>
                            </div>

                            <!-- Tabs -->
                            <div class="flex border-b border-gray-200">
                                <button
                                    class="tab-btn px-4 py-2 text-sm font-medium border-b-2 border-blue-600 text-blue-600 font-semibold"
                                    data-target="forum-list">
                                    Forum List
                                </button>
                                <button
                                    class="tab-btn px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-blue-600 hover:border-blue-600"
                                    data-target="user-requests">
                                    Users Request
                                </button>
                            </div>
                        </div>

                        <!-- Forum List -->
                        <nav id="forum-list" class="tab-content flex-1 overflow-y-auto p-4 space-y-8">
                            @foreach($categories as $category)
                                <div>
                                    <h4 class="font-medium text-[#606060] flex justify-between items-center cursor-pointer mb-3"
                                        onclick="toggleSection(this)">
                                        <span>{{ $category->title }}</span>
                                        <img src="{{ asset('images/down-arr.svg') }}" alt="Expand"
                                            class="h-4 w-4 transition-transform duration-200" />
                                    </h4>

                                    <div class="hidden section-content pl-4">
                                        <div class="flex items-center gap-6 text-xs text-gray-500 mb-4">
                                            <p class="flex items-center gap-1">
                                                <img src="{{ asset('images/wechat.svg') }}" class="w-4 h-4" alt="Chat">
                                                <span>{{ $category->thread_count ?? 0 }}</span>
                                            </p>
                                            <p class="flex items-center gap-1">
                                                <img src="{{ asset('images/calendar-03.svg') }}" class="w-4 h-4" alt="Date">
                                                <span>{{ $category->created_at?->format('M d, Y') }}</span>
                                            </p>
                                        </div>

                                        <ol class="list-decimal text-[#808080] space-y-3 font-medium pl-5 text-sm">
                                            @foreach($category->threads as $thread)
                                                <li>
                                                    <a href="javascript:void(0);"
                                                    class="text-blue-600 hover:underline thread-link"
                                                    data-thread-id="{{ $thread->id }}"
                                                    data-thread-slug="{{ Str::slug($thread->title) }}">
                                                        {{ $thread->title }}
                                                    </a>
                                                </li>
                                            @endforeach


                                            @if($category->threads->isEmpty())
                                                <li class="text-gray-400">No threads found.</li>
                                            @endif
                                        </ol>
                                    </div>
                                </div>
                            @endforeach
                        </nav>

                        <!-- User Requests -->
                        <nav id="user-requests" class="tab-content hidden flex-1 overflow-y-auto p-4 space-y-6">
                            <h4 class="font-medium text-[#606060]">Users Requests</h4>
                            <ol class="list-decimal text-[#808080] space-y-3 font-medium pl-5 text-sm">
                                <li>Request: Add new category</li>
                                <li>Request: Dark mode support</li>
                                <li>Request: Better notifications</li>
                            </ol>
                        </nav>
                    </aside>

                    <!-- Main Content -->
                    <section id="main-content" class="flex-1 flex flex-col bg-white rounded-xl shadow-sm p-4 md:p-8">
                        <!-- Default Welcome -->
                        <div id="welcome-section" class="flex flex-col items-center justify-center h-full text-center">
                            <div class="bg-blue-600 p-4 rounded-full mb-6 shadow-lg">
                                <img src="{{ asset('images/wechat.svg') }}" class="w-12 h-12 text-white" alt="WeChat Icon" />
                            </div>

                            <h1 class="text-3xl font-semibold text-blue-600 mb-2">
                                Welcome to Department Forums
                            </h1>

                            <p class="text-gray-600 mb-6">
                                You don't have access to any forums yet.
                            </p>

                            <p class="text-gray-500 max-w-lg">
                                Select a **forum category** from the list on the left and **request access** to start joining conversations.
                            </p>
                        </div>
                    </section>
                </div>
            </main>
        @endrole

        @role('user')
            @foreach ($categories as $category)
                <livewire:forum::components.category.card :$category :key="$category->id" />
            @endforeach
        @endrole
    </div>

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

        document.querySelectorAll('.thread-link').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();

                const thread_id = link.dataset.threadId;
                const thread_slug = link.dataset.threadSlug;

                // Construct correct URL for Laravel Forum
                const url = `/forum/thread/${thread_id}-${thread_slug}/view`;

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Thread not found');
                    }
                    return response.text();
                })
                .then(html => {
                    // Replace only the right side content
                    document.querySelector('#main-content').innerHTML = html;
                })
                .catch(error => {
                    console.error(error);
                });
            });
        });
    });

    function toggleSection(header) {
        const content = header.nextElementSibling;
        const icon = header.querySelector('img');

        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.src = "{{ asset('images/up-arr.svg') }}";
        } else {
            content.classList.add('hidden');
            icon.src = "{{ asset('images/down-arr.svg') }}";
        }
    }

</script>
