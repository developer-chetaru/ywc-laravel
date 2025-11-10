<div class="flex flex-col h-full">
    <div class="border-b border-gray-200 p-5">
        <h3 class="font-semibold text-2xl text-[#0053FF] mb-2">
            {{ $thread->title }}
        </h3>
        <div class="flex gap-6 text-sm text-[#808080]">
            <p>Date: <span class="font-medium">{{ $thread->created_at->format('M d, Y') }}</span></p>
            <p>Replies: <span class="font-medium">{{ $thread->reply_count }}</span></p>
            <p>Last Updated: <span class="font-medium">{{ $thread->updated_at->format('M d, Y') }}</span></p>
        </div>
    </div>

    <div class="flex-1 p-5 overflow-y-auto">
        {{-- Thread posts go here --}}
    </div>

    <div class="border-t border-gray-200 bg-white p-5">
        <div class="relative">
            <textarea placeholder="Write your reply..."
                      class="w-full resize-none bg-[#F8F9FA] border border-gray-200 text-sm text-[#606060] rounded-lg h-[70px] p-3 pl-11 focus:outline-none focus:ring focus:ring-blue-200"></textarea>
            <img src="{{ asset('images/smile.svg') }}" alt="Emoji"
                 class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 opacity-70">
            <button class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-blue-600 hover:bg-blue-500 transition">
                <img src="{{ asset('images/sent.svg') }}" alt="Send" class="w-4 h-4 invert">
            </button>
        </div>
    </div>
</div>
