<div>
    @role('super_admin')
    <main class="flex-1 overflow-y-auto p-4">
        <div class="h-[calc(100vh-100px)] bg-gray-100">
            <h2 class="text-[#0053FF] text-[30px] font-semibold mb-4">Manage Certifications</h2>

            <form class="flex gap-[16px] mb-4">
                <div class="relative w-[39%]">
                    <input type="text" placeholder="Search certifications..." 
                        wire:model.live.debounce.300ms="search"
                        class="text-[#616161] placeholder-[#616161] w-full py-3 px-4 rounded-lg border border-gray-200 focus:outline-none focus:border focus:!border-blue-200 text-sm !pl-[40px] font-medium !bg-[#ffffff]">
                    <button class="!absolute !left-3 !top-1/2 !transform !-translate-y-1/2" type="button">
                        <img src="/images/search.svg" alt="">
                    </button>
                </div>

                <div class="relative">
                    <select wire:model.live="categoryFilter"
                        class="appearance-none text-[#616161] py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-blue-200 text-sm font-medium bg-white cursor-pointer px-3 pr-10 min-w-[150px]">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="relative">
                    <select wire:model.live="statusFilter"
                        class="appearance-none text-[#616161] py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-blue-200 text-sm font-medium bg-white cursor-pointer px-3 pr-10 min-w-[130px]">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">Pending Approval</option>
                    </select>
                </div>

                <a href="{{ route('training.admin.certifications.create') }}" 
                    class="cursor-pointer bg-[#0053FF] flex gap-2 justify-center items-center px-5 py-2 rounded-md text-white text-sm leading-[0px] ml-auto">
                    <img class="h-[18px] w-[18px]" src="/images/add-circle-white.svg" alt="">
                    Add Certification
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
                            <th class="px-4 py-6 font-medium text-[#020202]">Category</th>
                            <th class="px-4 py-6 font-medium text-[#020202]">Providers</th>
                            <th class="px-4 py-6 font-medium text-[#020202] text-center">Status</th>
                            <th class="px-4 py-6 font-medium text-[#020202] text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        @forelse($certifications as $cert)
                        <tr class="bg-gray-50 hover:bg-gray-100">
                            <td class="px-4 py-6">
                                <div class="font-semibold">{{ $cert->name }}</div>
                                @if($cert->official_designation)
                                    <div class="text-xs text-gray-500">{{ $cert->official_designation }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-6 text-[#616161]">{{ $cert->category->name }}</td>
                            <td class="px-4 py-6 text-[#0053FF] font-medium">{{ $cert->provider_count }} providers</td>
                            <td class="px-4 py-6 text-center">
                                @if($cert->requires_admin_approval && !$cert->is_active)
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Pending</span>
                                @elseif($cert->is_active)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Active</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-6 text-center">
                                <div class="justify-center flex gap-2">
                                    @if($cert->requires_admin_approval && !$cert->is_active)
                                        <button wire:click="approve({{ $cert->id }})" 
                                            class="px-3 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700">
                                            Approve
                                        </button>
                                    @endif
                                    <a href="{{ route('training.admin.certifications.edit', $cert->id) }}" class="cursor-pointer">
                                        <img class="w-[37px] h-[37px]" src="/images/edit.svg" alt="">
                                    </a>
                                    <button wire:click="toggleStatus({{ $cert->id }})" 
                                            class="cursor-pointer w-[37px] h-[37px] rounded border-2 flex items-center justify-center transition-colors {{ $cert->is_active ? 'bg-green-500 border-green-600' : 'bg-gray-300 border-gray-400' }}"
                                            title="{{ $cert->is_active ? 'Deactivate' : 'Activate' }}">
                                        <svg class="w-5 h-5 {{ $cert->is_active ? 'text-white' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $cert->id }})" 
                                        wire:confirm="Are you sure?" 
                                        class="cursor-pointer">
                                        <img class="w-[37px] h-[37px]" src="/images/del.svg" alt="">
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No certifications found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $certifications->links() }}
            </div>
        </div>
    </main>
    @endrole
</div>
