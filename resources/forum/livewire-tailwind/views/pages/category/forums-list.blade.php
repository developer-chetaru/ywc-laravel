<div class="[&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] h-[calc(100vh-200px)] overflow-y-auto">
    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mb-4 sm:mb-6">
        <button 
            type="button"  class="backToChat cursor-pointer bg-white border border-[#808080] flex gap-2 justify-center items-center px-3 sm:px-4 py-2 rounded-md !text-[#808080] hover:!text-blue-600 hover:!border-blue-600 transition text-xs sm:text-sm">
            <img class="h-3" src="/images/left-arr.svg" alt="">
            <span class="whitespace-nowrap">Back to Department Forums</span>
        </button>

        <button type="button" class="cursor-pointer bg-white border border-[#808080] flex gap-2 justify-center items-center px-3 sm:px-4 py-2 rounded-md text-[#808080] hover:text-blue-600 hover:border-blue-600 transition text-xs sm:text-sm"
        >
            <span class="bg-[#0066FF] h-[10px] rounded-full w-[10px] "></span>
            <span class="whitespace-nowrap">Active Forums</span>
            <span class="font-medium text-[#000]">{{ $activeForumsCount }}</span>
        </button>
    </div>

    <div class="bg-white rounded-lg border border-[#808080]">
        <div class="p-3 sm:p-4 flex flex-col sm:flex-row gap-3 sm:gap-4">
            <!-- Search -->
            <div class="relative w-full sm:w-[40%]">
                <input 
                    type="search"
                    wire:model.live="search"
                    placeholder="Search forum by title"
                    class="text-[#616161] placeholder-[#616161] w-full py-2 sm:py-3 px-4 rounded-lg border border-gray-200 focus:outline-none focus:border focus:!border-blue-200 text-xs sm:text-sm !pl-[40px] font-medium bg-white"
                >
                <img class="!absolute !left-3 !top-1/2 !transform !-translate-y-1/2" src="/images/search.svg" alt="">
            </div>

            <!-- Status Filter -->
            <div class="relative w-full sm:w-auto">
                <select 
                    wire:model.live="status" 
                    class="w-full sm:min-w-[180px] appearance-none !bg-none status text-[#616161] py-2 sm:py-3 rounded-lg border border-gray-200 focus:outline-none focus:border focus:!border-blue-200 text-xs sm:text-sm font-medium bg-white cursor-pointer px-3 pr-12"
                >
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                <img class="!absolute !right-3 !top-1/2 !transform !-translate-y-1/2 w-[10px] cursor-pointer" src="/images/down-arr.svg" alt="">
            </div>
        </div>

        <!-- Table - Desktop View -->
        <div class="hidden md:block overflow-x-auto bg-white rounded-b-lg">
            <table class="min-w-full border-collapse">
                <thead>
                    <tr class="bg-[#F8F9FA] text-[#020202] text-sm font-semibold">
                        <th class="py-4 sm:py-6 px-3 sm:px-4 text-center w-12 font-medium text-xs sm:text-sm">#</th>
                        <th class="py-4 sm:py-6 px-3 sm:px-4 text-left font-medium text-xs sm:text-sm">Forum Name</th>
                        <th class="py-4 sm:py-6 px-3 sm:px-4 text-left font-medium text-xs sm:text-sm">Forum Description</th>
                        <th class="py-4 sm:py-6 px-3 sm:px-4 text-center w-40 font-medium text-xs sm:text-sm">Total Threads</th>
                        <th class="py-4 sm:py-6 px-3 sm:px-4 text-center w-40 font-medium text-xs sm:text-sm">Status</th>
                        <th class="py-4 sm:py-6 px-3 sm:px-4 text-center w-40 font-medium text-xs sm:text-sm">Created Date</th>
                        <th class="py-4 sm:py-6 px-3 sm:px-4 text-center w-70 font-medium text-xs sm:text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-xs sm:text-sm text-[#1B1B1B]">
                    @forelse($forums as $index => $forum)
                        <tr class="{{ $loop->even ? 'bg-[#F8F9FA]' : '' }} hover:bg-gray-50">
                            <td class="py-4 sm:py-6 px-3 sm:px-4 text-center">{{ $index + 1 }}</td>
                            <td class="py-4 sm:py-6 px-3 sm:px-4 font-normal">{{ $forum->title }}</td>
                            <td class="py-4 sm:py-6 px-3 sm:px-4">
                                {{ Str::limit($forum->description, 80) }}
                                @if(strlen($forum->description) > 80)
                                    <a href="{{ Forum::route('category.show', $forum) }}" class="block hover:no-underline capitalize mt-2 text-[#808080] underline w-fit">
                                        Read More
                                    </a>
                                @endif
                            </td>
                            <td class="py-4 sm:py-6 px-3 sm:px-4 text-center">{{ $forum->threads()->count() }}</td>
                            <td class="py-4 sm:py-6 px-3 sm:px-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <input 
                                        type="checkbox" 
                                        wire:click="toggleStatus({{ $forum->id }})" 
                                        {{ $forum->status ? 'checked' : '' }}
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer"
                                    >
                                    <span class="text-xs sm:text-sm font-medium {{ $forum->status ? 'text-green-600' : 'text-red-500' }}">
                                        {{ $forum->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 sm:py-6 px-3 sm:px-4 text-center">{{ $forum->created_at->format('d/m/Y') }}</td>
                            <td class="px-3 sm:px-4 py-4 sm:py-6">
                                <div class="justify-center flex gap-2">
                                    <a href="{{ Forum::route('category.show', $forum) }}" 
                                    class="cursor-pointer border border-[#616161] hover:text-blue-800 px-3 sm:px-6 py-1.5 sm:py-2 rounded-md text-[#616161] text-xs sm:text-sm hover:border-blue-600 transition whitespace-nowrap">
                                        View Thread
                                    </a>
                                    @can('edit', $forum)
                                        <a href="{{ Forum::route('category.edit', $forum) }}" class="cursor-pointer hover:opacity-80 transition">
                                            <img class="w-7 h-7 sm:w-[37px] sm:h-[37px]" src="/images/edit.svg" alt="Edit">
                                        </a>
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

        <!-- Mobile Card View -->
        <div class="md:hidden bg-white rounded-b-lg divide-y divide-gray-200">
            @forelse($forums as $index => $forum)
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs text-gray-500 font-medium">#{{ $index + 1 }}</span>
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $forum->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $forum->status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">{{ $forum->title }}</h3>
                            <p class="text-xs text-gray-600 mb-2">
                                {{ Str::limit($forum->description, 100) }}
                                @if(strlen($forum->description) > 100)
                                    <a href="{{ Forum::route('category.show', $forum) }}" class="text-blue-600 underline">
                                        Read More
                                    </a>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 mb-3 text-xs">
                        <div>
                            <p class="text-gray-500 mb-1">Total Threads</p>
                            <p class="font-semibold text-gray-900">{{ $forum->threads()->count() }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Created Date</p>
                            <p class="font-semibold text-gray-900">{{ $forum->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                        <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer">
                            <input 
                                type="checkbox" 
                                wire:click="toggleStatus({{ $forum->id }})" 
                                {{ $forum->status ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer"
                            >
                            <span>Toggle Status</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <a href="{{ Forum::route('category.show', $forum) }}" 
                               class="px-3 py-1.5 border border-[#616161] text-[#616161] text-xs rounded-md hover:border-blue-600 hover:text-blue-800 transition">
                                View Thread
                            </a>
                            @can('edit', $forum)
                                <a href="{{ Forum::route('category.edit', $forum) }}" class="p-1.5 hover:opacity-80 transition">
                                    <img class="w-6 h-6" src="/images/edit.svg" alt="Edit">
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-[#808080]">
                    <p>No forums found.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
