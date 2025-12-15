<div>
    @role('super_admin')
    <main class="flex-1 overflow-y-auto p-4">
        <div class="h-[calc(100vh-100px)] bg-gray-100">
            <h2 class="text-[#0053FF] text-[30px] font-semibold mb-4">Manage Courses</h2>

            <form class="flex gap-[16px] mb-4 items-center">
                <div class="relative w-[30%]">
                    <input type="text" placeholder="Search courses..." 
                        wire:model.live.debounce.300ms="search"
                        class="text-[#616161] placeholder-[#616161] w-full py-3 px-4 rounded-lg border border-gray-200 focus:outline-none focus:border focus:!border-blue-200 text-sm !pl-[40px] font-medium !bg-[#ffffff]">
                </div>

                <div class="relative">
                    <select wire:model.live="providerFilter"
                        class="appearance-none text-[#616161] py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-blue-200 text-sm font-medium bg-white cursor-pointer px-3 pr-10 min-w-[150px]">
                        <option value="">All Providers</option>
                        @foreach($providers as $provider)
                            <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="relative">
                    <select wire:model.live="certificationFilter"
                        class="appearance-none text-[#616161] py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-blue-200 text-sm font-medium bg-white cursor-pointer px-3 pr-10 min-w-[150px]">
                        <option value="">All Certifications</option>
                        @foreach($certifications as $cert)
                            <option value="{{ $cert->id }}">{{ $cert->name }}</option>
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

                <a href="{{ route('training.admin.courses.create') }}" 
                    class="ml-auto cursor-pointer bg-[#0053FF] flex gap-2 justify-center items-center px-5 py-2 rounded-md text-white text-sm leading-[0px]">
                    <img class="h-[18px] w-[18px]" src="/images/add-circle-white.svg" alt="">
                    Add Course
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
                            <th class="px-4 py-6 font-medium text-[#020202]">Certification</th>
                            <th class="px-4 py-6 font-medium text-[#020202]">Provider</th>
                            <th class="px-4 py-6 font-medium text-[#020202]">Price</th>
                            <th class="px-4 py-6 font-medium text-[#020202]">Duration</th>
                            <th class="px-4 py-6 font-medium text-[#020202]">Reviews</th>
                            <th class="px-4 py-6 font-medium text-[#020202] text-center">Status</th>
                            <th class="px-4 py-6 font-medium text-[#020202] text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        @forelse($courses as $course)
                        <tr class="bg-gray-50 hover:bg-gray-100">
                            <td class="px-4 py-6">
                                <div class="font-semibold">{{ $course->certification->name }}</div>
                                <div class="text-xs text-gray-500">{{ $course->certification->category->name }}</div>
                            </td>
                            <td class="px-4 py-6 text-[#616161]">{{ $course->provider->name }}</td>
                            <td class="px-4 py-6">
                                <div class="font-semibold">£{{ number_format($course->price, 2) }}</div>
                                <div class="text-xs text-green-600">YWC: £{{ number_format($course->ywc_price, 2) }}</div>
                            </td>
                            <td class="px-4 py-6">{{ $course->duration_days }} day(s)</td>
                            <td class="px-4 py-6">
                                @if($course->rating_avg > 0)
                                    <div class="flex items-center gap-1">
                                        <span>{{ number_format($course->rating_avg, 1) }}</span>
                                        <span class="text-yellow-400">★</span>
                                        <span class="text-gray-500 text-xs">({{ $course->review_count }})</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">No reviews</span>
                                @endif
                            </td>
                            <td class="px-4 py-6 text-center">
                                @if($course->requires_admin_approval && !$course->is_active)
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Pending</span>
                                @elseif($course->is_active)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Active</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-6 text-center">
                                <div class="justify-center flex gap-2">
                                    @if($course->requires_admin_approval && !$course->is_active)
                                        <button wire:click="approve({{ $course->id }})" 
                                            class="px-3 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700">
                                            Approve
                                        </button>
                                    @endif
                                    <a href="{{ route('training.admin.courses.edit', $course->id) }}" 
                                       class="cursor-pointer" 
                                       title="Edit Course">
                                        <img class="w-[37px] h-[37px]" src="/images/edit.svg" alt="Edit">
                                    </a>
                                    <button wire:click="toggleStatus({{ $course->id }})" 
                                            class="cursor-pointer w-[37px] h-[37px] rounded border-2 flex items-center justify-center transition-colors {{ $course->is_active ? 'bg-green-500 border-green-600' : 'bg-gray-300 border-gray-400' }}"
                                            title="{{ $course->is_active ? 'Deactivate' : 'Activate' }}">
                                        <svg class="w-5 h-5 {{ $course->is_active ? 'text-white' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $course->id }})" 
                                        wire:confirm="Are you sure?" 
                                        class="cursor-pointer">
                                        <img class="w-[37px] h-[37px]" src="/images/del.svg" alt="">
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">No courses found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $courses->links() }}
            </div>
        </div>
    </main>
    @endrole
</div>
