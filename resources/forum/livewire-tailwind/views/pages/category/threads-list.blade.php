<div class="">

    <!-- 🔹 Header Buttons -->
    <div class="flex gap-4 mb-6">
        <button 
            type="button" class="backToChat cursor-pointer bg-white border border-[#808080] flex gap-2 justify-center items-center px-4 py-2 rounded-md !text-[#808080] hover:!text-blue-600 hover:!border-blue-600 transition text-sm">
            <img class="h-3" src="/images/left-arr.svg" alt="">
            Back to Department Forums
        </button>

        <button 
            type="button"
            class="cursor-pointer bg-white border border-[#808080] flex gap-2 justify-center items-center px-4 py-2 rounded-md text-[#1B1B1B] text-sm">
            <span class="bg-[#0066FF] h-[10px] rounded-full w-[10px]"></span>
            Active Threads 
            <span class="font-medium text-[#000]">{{ $activeThreadsCount }}</span>
        </button>
    </div>

   <div class="bg-white rounded-lg border border-[#808080] [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] h-[calc(100vh-260px)] overflow-y-auto">
     <!-- 🔹 Search + Filter -->
    <div class="p-4 flex gap-4 items-center">
        <div class="relative w-[40%]">
            <input 
                type="search" 
                wire:model.live="search" 
                placeholder="Search thread by title" 
                class="text-[#616161] placeholder-[#616161] w-full py-3 px-4 rounded-lg border border-gray-200 focus:outline-none focus:!border-blue-200 text-sm !pl-[40px] font-medium bg-white">
            <button type="submit" class="!absolute !left-3 !top-1/2 !transform !-translate-y-1/2">
                <img src="/images/search.svg" alt="">
            </button>
        </div>

        <div class="relative">
            <select wire:model.live="status" class="min-w-[180px] appearance-none !bg-none status text-[#616161] py-3 rounded-lg border border-gray-200 focus:outline-none focus:border focus:!border-blue-200 text-sm font-medium !bg-[#ffffff] cursor-pointer px-3 pr-12">
                <option value="">Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
            <img class="!absolute !right-3 !top-1/2 !transform !-translate-y-1/2 w-[10px]" src="/images/down-arr.svg" alt="">
        </div>
    </div>

    <!-- 🔹 Threads Table -->
    <div class="overflow-x-auto bg-white rounded-b-lg">
        <table class="min-w-full border-collapse">
            <thead>
                <tr class="bg-[#F8F9FA] text-[#020202] text-sm font-semibold">
                    <th class="py-6 px-4 text-center w-12 font-medium">#</th>
                    <th class="py-6 px-4 text-left font-medium">Thread Title</th>
                    <th class="py-6 px-4 text-left font-medium">Forum</th>
                    <th class="py-6 px-4 text-center w-40 font-medium">Replies</th>
                    <th class="py-6 px-4 text-center w-40 font-medium">Status</th>
                    <th class="py-6 px-4 text-center w-40 font-medium">Last Activity</th>
                    <th class="py-6 px-4 text-center w-70 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm text-[#1B1B1B]">
                @forelse($threads as $index => $thread)
                    <tr class="{{ $loop->even ? 'bg-[#F8F9FA]' : '' }} hover:bg-gray-50">
                        <td class="py-6 px-4 text-center">{{ $index + 1 }}</td>
                        <td class="py-6 px-4 font-normal">{{ $thread->title }}</td>
                        <td class="py-6 px-4">{{ $thread->category->title ?? '-' }}</td>
                        <td class="py-6 px-4 text-center">{{ $thread->posts()->count() - 1 }}</td>
                        <td class="py-6 px-4 text-center">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       wire:click.prevent="toggleStatus({{ $thread->id }})"
                                       @if($thread->status) checked @endif
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="text-sm font-medium ml-2 {{ $thread->status ? 'text-green-600' : 'text-red-500' }}">{{ $thread->status ? 'Active' : 'Inactive' }}</span>
                            </label>
                        </td>
                        <td class="py-6 px-4 text-center">{{ $thread->updated_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-6">
                            <div class="justify-center flex gap-2">
                               <button class="cursor-pointer">
                                            <img class="w-[37px] h-[37px]" src="/images/chat-new.svg" alt="">
                                        </button>
                                <button class="cursor-pointer">
                                            <img class="w-[37px] h-[37px]" src="/images/edit.svg" alt="">
                                        </button>
                                        <button class="cursor-pointer">
                                            <img class="w-[37px] h-[37px]" src="/images/del.svg" alt="">
                                        </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-[#808080]">No threads found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
   </div>
</div>
