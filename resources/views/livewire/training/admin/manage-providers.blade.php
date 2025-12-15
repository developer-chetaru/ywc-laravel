<div>
    @role('super_admin')
    <main class="flex-1 overflow-y-auto p-4">
        <div class="h-[calc(100vh-100px)] bg-gray-100">
            <h2 class="text-[#0053FF] text-[30px] font-semibold mb-4">Manage Providers</h2>

            <form class="flex gap-[16px] mb-4">
                <div class="relative w-[39%]">
                    <input type="text" placeholder="Search providers..." 
                        wire:model.live.debounce.300ms="search"
                        class="text-[#616161] placeholder-[#616161] w-full py-3 px-4 rounded-lg border border-gray-200 focus:outline-none focus:border focus:!border-blue-200 text-sm !pl-[40px] font-medium !bg-[#ffffff]">
                </div>

                <div class="relative">
                    <select wire:model.live="statusFilter"
                        class="appearance-none text-[#616161] py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-blue-200 text-sm font-medium bg-white cursor-pointer px-3 pr-10 min-w-[130px]">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <a href="{{ route('training.admin.providers.create') }}" 
                    class="cursor-pointer bg-[#0053FF] flex gap-2 justify-center items-center px-5 py-2 rounded-md text-white text-sm leading-[0px] ml-auto">
                    <img class="h-[18px] w-[18px]" src="/images/add-circle-white.svg" alt="">
                    Add Provider
                </a>
            </form>

            @if (session()->has('success'))
                <div class="w-full bg-blue-500 text-white text-center py-2 rounded-md mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm w-full overflow-hidden">
                <table class="w-full text-left border-separate border-spacing-y-0">
                    <thead>
                        <tr class="text-sm text-white-500 border-b">
                            <th class="px-4 py-6 font-medium text-[#020202]">Name</th>
                            <th class="px-4 py-6 font-medium text-[#020202]">Email</th>
                            <th class="px-4 py-6 font-medium text-[#020202]">Courses</th>
                            <th class="px-4 py-6 font-medium text-[#020202]">Rating</th>
                            <th class="px-4 py-6 font-medium text-[#020202] text-center">Status</th>
                            <th class="px-4 py-6 font-medium text-[#020202] text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        @forelse($providers as $provider)
                        <tr class="bg-gray-50 hover:bg-gray-100">
                            <td class="px-4 py-6">
                                <div class="font-semibold">{{ $provider->name }}</div>
                                @if($provider->is_verified_partner)
                                    <span class="text-xs text-green-600">✓ Verified Partner</span>
                                @endif
                            </td>
                            <td class="px-4 py-6 text-[#616161]">{{ $provider->email ?? 'N/A' }}</td>
                            <td class="px-4 py-6 text-[#0053FF] font-medium">
                                {{ $provider->active_courses_count }} / {{ $provider->courses_count }}
                            </td>
                            <td class="px-4 py-6">
                                @if($provider->rating_avg > 0)
                                    <div class="flex items-center gap-1">
                                        <span>{{ number_format($provider->rating_avg, 1) }}</span>
                                        <span class="text-yellow-400">★</span>
                                        <span class="text-gray-500 text-xs">({{ $provider->total_reviews }})</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">No ratings</span>
                                @endif
                            </td>
                            <td class="px-4 py-6 text-center">
                                @if($provider->is_active)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Active</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-6 text-center">
                                <div class="justify-center flex gap-2">
                                    <a href="{{ route('training.admin.providers.edit', $provider->id) }}" class="cursor-pointer">
                                        <img class="w-[37px] h-[37px]" src="/images/edit.svg" alt="">
                                    </a>
                                    <button wire:click="toggleVerified({{ $provider->id }})" 
                                        class="px-2 py-1 text-xs rounded {{ $provider->is_verified_partner ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        Verify
                                    </button>
                                    <button wire:click="toggleStatus({{ $provider->id }})" 
                                            class="cursor-pointer w-[37px] h-[37px] rounded border-2 flex items-center justify-center transition-colors {{ $provider->is_active ? 'bg-green-500 border-green-600' : 'bg-gray-300 border-gray-400' }}"
                                            title="{{ $provider->is_active ? 'Deactivate' : 'Activate' }}">
                                        <svg class="w-5 h-5 {{ $provider->is_active ? 'text-white' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $provider->id }})" 
                                        wire:confirm="Are you sure?" 
                                        class="cursor-pointer">
                                        <img class="w-[37px] h-[37px]" src="/images/del.svg" alt="">
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">No providers found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $providers->links() }}
            </div>
        </div>
    </main>
    @endrole
</div>
