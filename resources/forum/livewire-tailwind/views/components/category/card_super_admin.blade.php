<div>
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