<div x-data="{ openMenu: null, editPostModal: @entangle('editPostModal') }">
    @if($selectedThread)
        {{-- Success/Error Messages --}}
        @if (session()->has('success'))
            <div class="mb-4 p-3 text-green-800 bg-green-100 border border-green-300 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 p-3 text-red-800 bg-red-100 border border-red-300 rounded-lg">
                {{ session('error') }}
            </div>
        @endif
        <div class="flex-1 flex flex-col chat-box h-[calc(100vh-160px)] bg-white rounded-xl relative" wire:key="thread-{{ $selectedThread->id }}">
            <!-- Thread Title -->
            <div class="border-b border-solid border-gray-400 p-5 bg-[#ffffff] z-1 rounded-t-lg">
                <h3 class="font-semibold mb-2 text-2xl text-[#0053FF]">{{ $selectedThread->title }}</h3>
                <div class="flex gap-x-6 text-[#808080]">
                    <p>Date: <span>{{ $selectedThread->created_at->format('M d, Y') }}</span></p>
                    <p>Replies: <span>{{ $selectedThread->posts->count() }}</span></p>
                    <p>Last Updated: <span>{{ $selectedThread->updated_at->format('M d, Y') }}</span></p>
                </div>
            </div>

            <!-- Posts -->
            <div class="p-5 h-full overflow-y-auto [scrollbar-width:none]">
                @forelse($selectedThread->posts as $post)
                    <div class="pb-4 relative">
                        <div class="flex gap-x-3 items-center mb-4">
                            <img src="{{ $post->user && $post->user->profile_photo_path ? asset('storage/' . $post->user->profile_photo_path) : asset('images/profile.png') }}" 
                                alt="User Avatar" class="w-[30px] h-[30px] rounded-full object-cover" />
                            <p class="leading-normal font-medium">{{ optional($post->user)->first_name ?? 'Unknown' }} {{ optional($post->user)->last_name ?? '' }}</p>
                            <span class="text-[#808080] relative before:content-[''] before:h-[7px] before:w-[7px] before:bg-[#D9D9D9] before:inline-block before:rounded-full before:mr-[10px]">
                                {{ $post->created_at->format('M d, Y') }}
                            </span>
                            @if(auth()->id() === $post->author_id)
                                <!-- Post actions -->
                                <div class="ml-auto relative">
                                    <button class="p-2 hover:bg-gray-100 rounded-full" @click="openMenu = openMenu === {{ $post->id }} ? null : {{ $post->id }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 12h.01M12 12h.01M18 12h.01" />
                                        </svg>
                                    </button>
                                    
                                    <!-- Menu -->
                                    <div x-show="openMenu === {{ $post->id }}" @click.away="openMenu = null" class="post-menu absolute right-0 mt-2 w-28 bg-white border rounded shadow-lg z-50">
                                        <button wire:click="openEditPost({{ $post->id }})" class="block w-full px-4 py-2 hover:bg-gray-100 text-left">Edit</button>
                                        <button wire:click="deletePost({{ $post->id }})" class="block w-full px-4 py-2 hover:bg-red-100 text-left text-red-600">Delete</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <p class="text-[#5A5A5A] whitespace-pre-line">{!! nl2br(e($post->content)) !!}</p>
                    </div>
                @empty
                    <div class="text-center text-gray-400 py-8">
                        <p>No posts yet. Be the first to reply!</p>
                    </div>
                @endforelse
            </div>

            <!-- Reply Box -->
            <div class="sticky bottom-0 mt-auto bg-white p-5 pb-3 rounded-b-lg">
                <form wire:submit.prevent="reply">
                    <div class="relative">
                        <textarea wire:model.defer="replyBody" placeholder="Write your reply" class="!bg-[#F8F9FA] border border-gray-200 text-[#808080] focus:outline-none h-[60px] pt-4 px-4 !resize-none rounded-lg w-full !pl-[50px]"></textarea>
                        <img src="{{ asset('images/smile.svg') }}" alt="" class="absolute h-[20px] left-4 top-[20px] w-[20px] object-contain">
                        <button type="submit" class="absolute right-3 top-[20px] h-[24px] w-[24px]">
                            <img src="{{ asset('images/sent.svg') }}" alt="">
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Post Modal -->
        <div  x-show="editPostModal" class="fixed inset-0 flex items-center justify-center z-50" style="background-color: rgba(0, 0, 0, 0.2); backdrop-filter: blur(4px);"
        >
            <div class="bg-white rounded-lg shadow-lg w-2/4 p-6">
                <h2 class="text-lg font-bold mb-4">Edit Post</h2>
                <textarea wire:model.defer="editPostContent" class="w-full border p-2 rounded h-40"></textarea>
                <div class="mt-4 flex justify-end gap-2">
                    <button @click="editPostModal = false" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                    <button wire:click="updatePost" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
                </div>
            </div>
        </div>
    @else
        <p class="text-gray-400 p-5">No thread selected</p>
    @endif
</div>
