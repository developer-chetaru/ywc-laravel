<div class="[&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] h-[calc(100vh-200px)] overflow-y-auto">
    <div class="flex gap-4 mb-6">
        <button 
            type="button"  class="backToChat cursor-pointer bg-white border border-[#808080] flex gap-2 justify-center items-center px-4 py-2 rounded-md !text-[#808080] hover:!text-blue-600 hover:!border-blue-600 transition text-sm">
            <img class="h-3" src="/images/left-arr.svg" alt="">
            Back to Department Forums
        </button>

        <button type="button" class="cursor-pointer bg-white border border-[#808080] flex gap-2 justify-center items-center px-4 py-2 rounded-md text-[#808080] hover:text-blue-600 hover:border-blue-600 transition text-sm"
        >
            <span class="bg-[#0066FF] h-[10px] rounded-full w-[10px] "></span>
            Active Forums 
            <span class="font-medium text-[#000]">{{ $activeForumsCount }}</span>
        </button>
    </div>

    <div class="bg-white rounded-lg border border-[#808080]">
        <div class="p-4 flex gap-4">
            <!-- Search -->
            <div class="relative w-[40%]">
                <input 
                    type="search"
                    wire:model.live="search"
                    placeholder="Search forum by title"
                    class="text-[#616161] placeholder-[#616161] w-full py-3 px-4 rounded-lg border border-gray-200 focus:outline-none focus:border focus:!border-blue-200 text-sm !pl-[40px] font-medium bg-white"
                >
                <img class="!absolute !left-3 !top-1/2 !transform !-translate-y-1/2" src="/images/search.svg" alt="">
            </div>

            <!-- Status Filter -->
            <div class="relative">
                <select 
                    wire:model.live="status" 
                    class="min-w-[180px] appearance-none !bg-none status text-[#616161] py-3 rounded-lg border border-gray-200 focus:outline-none focus:border focus:!border-blue-200 text-sm font-medium bg-white cursor-pointer px-3 pr-12"
                >
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                <img class="!absolute !right-3 !top-1/2 !transform !-translate-y-1/2 w-[10px] cursor-pointer" src="/images/down-arr.svg" alt="">
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto bg-white rounded-b-lg">
            <table class="min-w-full border-collapse">
                <thead>
                    <tr class="bg-[#F8F9FA] text-[#020202] text-sm font-semibold">
                        <th class="py-6 px-4 text-center w-12 font-medium">#</th>
                        <th class="py-6 px-4 text-left font-medium">Forum Name</th>
                        <th class="py-6 px-4 text-left font-medium">Forum Description</th>
                        <th class="py-6 px-4 text-center w-40 font-medium">Total Threads</th>
                        <th class="py-6 px-4 text-center w-40 font-medium">Status</th>
                        <th class="py-6 px-4 text-center w-40 font-medium">Created Date</th>
                        <th class="py-6 px-4 text-center w-70 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-[#1B1B1B]">
                    @forelse($forums as $index => $forum)
                        <tr class="{{ $loop->even ? 'bg-[#F8F9FA]' : '' }} hover:bg-gray-50">
                            <td class="py-6 px-4 text-center">{{ $index + 1 }}</td>
                            <td class="py-6 px-4 font-normal">{{ $forum->title }}</td>
                            <td class="py-6 px-4">
                                {{ $Str::limit($forum->description, 80) }}
                                @if(strlen($forum->description) > 80)
                                    <a href="{{ Forum::route('category.show', $forum) }}" class="block hover:no-underline capitalize mt-2 text-[#808080] underline w-fit">
                                        Read More
                                    </a>
                                @endif
                            </td>
                            <td class="py-6 px-4 text-center">{{ $forum->threads()->count() }}</td>
                            <td class="py-6 px-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <input 
                                        type="checkbox" 
                                        wire:click="toggleStatus({{ $forum->id }})" 
                                        {{ $forum->status ? 'checked' : '' }}
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    >
                                    <span class="text-sm font-medium {{ $forum->status ? 'text-green-600' : 'text-red-500' }}">
                                        {{ $forum->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-6 px-4 text-center">{{ $forum->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-6">
                                <div class="justify-center flex gap-2">
                                    <a href="#" 
                                    class="cursor-pointer border border-[#616161] hover:text-blue-800 px-6 py-2 rounded-md text-[#616161] text-sm">
                                        View Thread
                                    </a>
                                    @can('edit', $forum)
                                        <button class="cursor-pointer">
                                            <img class="w-[37px] h-[37px]" src="/images/edit.svg" alt="">
                                        </button>
                                        <button class="cursor-pointer">
                                            <img class="w-[37px] h-[37px]" src="/images/del.svg" alt="">
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-[#808080]">No forums found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
