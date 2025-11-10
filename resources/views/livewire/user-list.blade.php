@role('super_admin')

    <div>
        <h2 class="text-xl border-b border-gray-100 font-medium text-[#0053FF] pb-2">User List</h2>
            
        <div class="h-[calc(100vh-100px)] bg-gray-100">

            <!-- Filters -->
            <div class="flex gap-4 mb-4">

                <!-- Search -->
                <div class="relative w-[40%]">
                    <input type="search" wire:model.live="search" placeholder="Search by name, email, or ID"
                        class="text-[#616161] placeholder-[#616161] w-full py-3 px-4 rounded-lg border border-gray-200 focus:outline-none focus:border-blue-200 text-sm pl-10 bg-white font-medium">
                    <img src="{{ asset('images/search.svg') }}" alt="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4">
                </div>

                <!-- Status Filter -->
                <div class="relative">
                    <select wire:model.live="status"
                        class="appearance-none text-[#616161] py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-blue-200 text-sm font-medium bg-white cursor-pointer px-3 pr-12">
                        <option value="">Status</option>
                        <option value="verified">Verified</option>
                        <option value="unverified">Unverified</option>
                    </select>
                    <img class="absolute right-3 top-1/2 transform -translate-y-1/2 w-3 cursor-pointer"
                        src="{{ asset('images/down-arr.svg') }}" alt="">
                </div>

                <!-- Membership Filter -->
                <div class="relative">
                    <select wire:model.live="membership"
                        class="appearance-none text-[#616161] py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-blue-200 text-sm font-medium bg-white cursor-pointer px-3 pr-12">
                        <option value="">Membership</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">1-Year Plan</option>
                    </select>
                    <img class="absolute right-3 top-1/2 transform -translate-y-1/2 w-3 cursor-pointer"
                        src="{{ asset('images/down-arr.svg') }}" alt="">
                </div>

                <!-- Sort Filter -->
                <div class="relative">
                    <select wire:model.live="sort"
                        class="appearance-none text-[#616161] py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-blue-200 text-sm font-medium bg-white cursor-pointer pr-4 pl-9">
                        <option value="desc">Sort by: New to Old</option>
                        <option value="asc">Sort by: Old to New</option>
                    </select>
                    <img class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 cursor-pointer"
                        src="{{ asset('images/sorting-01.svg') }}" alt="">
                </div>

            </div>


            <!-- User Table -->
            <div>
                <div class="bg-white rounded-lg shadow-sm w-full overflow-hidden">
                    <table class="w-full text-left border-separate border-spacing-y-0">
                        <thead>
                            <tr class="text-sm border-b bg-gray-50">
                                <th class="px-4 py-6"><input type="checkbox" /></th>
                                <th class="px-4 py-6 font-medium text-[#020202]">User Name</th>
                                <th class="px-4 py-6 font-medium text-[#020202]">Email</th>
                                <th class="px-4 py-6 font-medium text-[#020202]">Status</th>
                                <th class="px-4 py-6 font-medium text-[#020202]">Membership</th>
                                <th class="px-4 py-6 font-medium text-[#020202]">Last Login</th>
                                <th class="px-4 py-6 font-medium text-[#020202]">First Login</th>
                                <th class="px-4 py-6"></th>
                            </tr>
                        </thead>

                        <tbody class="text-gray-700 text-sm">
                            @forelse($users as $user)
                                <tr class="odd:bg-gray-50 hover:bg-gray-100">
                                    <td class="px-4 py-6"><input type="checkbox" /></td>

                                    <!-- User info -->
                                    <td class="flex items-center gap-3 px-4 py-6 cursor-pointer"
                                        wire:click="showProfileDetails({{ $user->id }})">
                                        @if($user->profile_photo_path)
                                            <img src="{{ asset('storage/'.$user->profile_photo_path) }}"
                                                class="w-8 h-8 rounded-full object-cover" alt="">
                                        @else
                                            <div class="w-8 h-8 flex items-center justify-center bg-blue-100 text-blue-600 font-medium rounded-full">
                                                {{ strtoupper(substr($user->first_name,0,1).substr($user->last_name,0,1)) }}
                                            </div>
                                        @endif
                                        <span class="text-[#1B1B1B] font-medium">
                                            {{ $user->first_name }} {{ $user->last_name }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-6 text-[#616161]">{{ $user->email }}</td>


                                    <!-- Status -->
                                    <td class="px-4 py-6">
                                        @if($user->is_active)
                                            <span class="flex items-center gap-1 text-[#0C7B24]">
                                                <span class="w-2 h-2 bg-[#0C7B24] rounded-full"></span>
                                                Verified
                                            </span>
                                        @else
                                            <span class="flex items-center gap-1 text-[#EB1C24]">
                                                <span class="w-2 h-2 bg-[#EB1C24] rounded-full"></span>
                                                Unverified
                                            </span>
                                        @endif
                                    </td>
                    
                                    <td class="px-4 py-6 text-[#616161]">
                                        {{ $user->membership_type ?? '–' }}
                                    </td>
                                    <td class="px-4 py-6 text-[#616161]">{{ $user->last_login ?? '–' }}</td>
                                    <td class="px-4 py-6 text-[#616161]">{{ $user->first_login ?? '–' }}</td>
                                    <td class="px-4 py-6 text-[#616161]">⋮</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-gray-500 py-6">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($users->count())
                <div class="flex items-center justify-between border-t border-gray-200 px-6 py-4 text-sm text-[#616161]">

                    <!-- Left: Showing info -->
                    <p class="font-medium">
                        Showing 
                        <span class="font-bold">{{ $users->firstItem() }}</span> 
                        to 
                        <span class="font-bold">{{ $users->lastItem() }}</span> 
                        of 
                        <span class="font-bold">{{ $users->total() }}</span> 
                        Results
                    </p>

                    <!-- Right: Pagination buttons -->
                    <div class="flex items-center gap-2">
                        {{-- Previous Page --}}
                        @if($users->onFirstPage())
                            <button disabled class="flex justify-center items-center rounded-lg border bg-gray-100 text-gray-400 h-[37px] w-[37px] cursor-not-allowed">
                                <img src="{{ asset('images/double-left-arr.svg') }}" alt="">
                            </button>
                        @else
                            <button wire:click="gotoPage(1)" class="flex justify-center items-center rounded-lg border bg-white hover:bg-blue-600 hover:text-white h-[37px] w-[37px]">
                                <img src="{{ asset('images/double-left-arr.svg') }}" alt="">
                            </button>
                        @endif

                        @if($users->onFirstPage())
                            <button disabled class="flex justify-center items-center rounded-lg border bg-gray-100 text-gray-400 h-[37px] w-[37px] cursor-not-allowed">
                                <img src="{{ asset('images/left-arr.svg') }}" alt="">
                            </button>
                        @else
                            <button wire:click="previousPage" class="flex justify-center items-center rounded-lg border bg-white hover:bg-blue-600 hover:text-white h-[37px] w-[37px]">
                                <img src="{{ asset('images/left-arr.svg') }}" alt="">
                            </button>
                        @endif

                        {{-- Page Numbers --}}
                        @foreach ($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                            @if ($page == $users->currentPage())
                                <button class="px-[15px] py-2 rounded-lg border bg-blue-600 text-white">{{ $page }}</button>
                            @else
                                <button wire:click="gotoPage({{ $page }})" class="px-[15px] py-2 rounded-lg border bg-white hover:bg-blue-600 hover:text-white">{{ $page }}</button>
                            @endif
                        @endforeach

                        {{-- Next Page --}}
                        @if($users->hasMorePages())
                            <button wire:click="nextPage" class="flex justify-center items-center rounded-lg border bg-white hover:bg-blue-600 hover:text-white h-[37px] w-[37px]">
                                <img src="{{ asset('images/right-arr.svg') }}" alt="">
                            </button>
                            <button wire:click="gotoPage({{ $users->lastPage() }})" class="flex justify-center items-center rounded-lg border bg-white hover:bg-blue-600 hover:text-white h-[37px] w-[37px]">
                                <img src="{{ asset('images/double-right-arr.svg') }}" alt="">
                            </button>
                        @else
                            <button disabled class="flex justify-center items-center rounded-lg border bg-gray-100 text-gray-400 h-[37px] w-[37px] cursor-not-allowed">
                                <img src="{{ asset('images/right-arr.svg') }}" alt="">
                            </button>
                            <button disabled class="flex justify-center items-center rounded-lg border bg-gray-100 text-gray-400 h-[37px] w-[37px] cursor-not-allowed">
                                <img src="{{ asset('images/double-right-arr.svg') }}" alt="">
                            </button>
                        @endif
                    </div>
                </div>
                @endif

            </div>
        </div>
        <!-- Popup Modal -->
        @if($showProfilePopup && $selectedUser)
            <div class="fixed inset-0 flex justify-end z-40">

                <div class="absolute inset-0 bg-black/60 transition-opacity duration-300" wire:click="closeProfilePopup"></div>

                <!-- Popup Panel -->
                <div class="relative bg-[#F5F6FA] h-screen overflow-y-auto shadow-xl p-7 py-10 w-[32%]">

                    <!-- Close Button -->
                    <button type="button" wire:click="closeProfilePopup" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-50">
                        X
                    </button>

                    <!-- BASIC INFO SECTION -->
                    <div class="pb-4">
                        <div class="flex gap-[13px] items-center mb-6">
                            <h3 class="text-sm text-[#0053FF] font-normal">Basic Info</h3>
                            <div class="bg-[#808080] flex-1 h-[1px] relative top-[2px]"></div>
                        </div>

                        <div class="flex items-center gap-[16px] mb-6">
                            @if($selectedUser->profile_photo_path)
                                <img src="{{ asset('storage/'.$selectedUser->profile_photo_path) }}"
                                    alt="User Avatar" class="w-14 h-14 rounded-full object-cover" />
                            @else
                                <div class="w-14 h-14 flex items-center justify-center bg-blue-100 text-blue-600 font-bold text-lg rounded-full">
                                    {{ strtoupper(substr($selectedUser->first_name,0,1).substr($selectedUser->last_name,0,1)) }}
                                </div>
                            @endif

                            <div>
                                <h4 class="text-[#1B1B1B] font-semibold text-lg">
                                    {{ $selectedUser->first_name }} {{ $selectedUser->last_name }}
                                </h4>
                                <p class="text-sm text-[#616161]">{{ $selectedUser->email }}</p>
                            </div>
                        </div>

                        @php
                            $membership = $selectedUser->latestSubscription ?? null;
                            $statusText = $selectedUser->email_verified_at ? 'Verified' : 'Unverified';
                            $membershipPlan = $membership ? ($membership->plan_type == 'yearly' ? '1-Year Plan' : 'Monthly') : 'None';
                            $lastLogin = $selectedUser->last_login ? \Carbon\Carbon::parse($selectedUser->last_login)->format('d-m-Y') : '–';
                            $firstLogin = $selectedUser->created_at ? \Carbon\Carbon::parse($selectedUser->created_at)->format('d-m-Y') : '–';
                        @endphp

                        <div class="space-y-4 text-sm max-w-[70%]">
                            <div class="flex justify-between"><span class="text-[#616161]">Status</span><span class="text-[#1B1B1B] font-medium">{{ $statusText }}</span></div>
                            <div class="flex justify-between"><span class="text-[#616161]">Membership</span><span class="text-[#1B1B1B]">{{ $membershipPlan }}</span></div>
                            <div class="flex justify-between"><span class="text-[#616161]">Last Login</span><span class="text-[#1B1B1B]">{{ $lastLogin }}</span></div>
                            <div class="flex justify-between"><span class="text-[#616161]">First Login</span><span class="text-[#1B1B1B]">{{ $firstLogin }}</span></div>
                            <div class="flex justify-between"><span class="text-[#616161]">Phone Number</span><span class="text-[#1B1B1B]">{{ $selectedUser->phone_number ?? '–' }}</span></div>
                            <div class="flex justify-between"><span class="text-[#616161]">Nationality</span><span class="text-[#1B1B1B]">{{ $selectedUser->nationality ?? '–' }}</span></div>
                            <div class="flex justify-between"><span class="text-[#616161]">Marital Status</span><span class="text-[#1B1B1B]">{{ $selectedUser->marital_status ?? '–' }}</span></div>
                            <div class="flex justify-between"><span class="text-[#616161]">Birth Country / Province</span><span class="text-[#1B1B1B]">{{ $selectedUser->birth_country ?? '–' }}</span></div>
                        </div>
                    </div>

                    <!-- MEMBERSHIP STATUS -->
                    @php
                        $subStatus = $membership ? ucfirst($membership->status) : 'Inactive';
                        $startDate = $membership && $membership->start_date ? \Carbon\Carbon::parse($membership->start_date)->format('d-m-Y') : '–';
                        $expiryDate = $membership && $membership->end_date ? \Carbon\Carbon::parse($membership->end_date)->format('d-m-Y') : '–';
                        $paymentMethod = $membership ? ucfirst($membership->payment_method ?? 'Stripe') : '–';
                    @endphp

                    <div class="py-4">
                        <div class="flex gap-[13px] items-center mb-6">
                            <h3 class="text-sm text-[#0053FF] font-normal">Membership Status</h3>
                            <div class="bg-[#808080] flex-1 h-[1px] relative top-[2px]"></div>
                        </div>

                        <div class="space-y-4 text-sm max-w-[70%]">
                            <div class="flex justify-between"><span class="text-gray-500">Membership Plan</span><span class="text-[#1B1B1B] font-medium">{{ $membershipPlan }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="text-[#1B1B1B]">{{ $subStatus }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Start Date</span><span class="text-[#1B1B1B]">{{ $startDate }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Expiry Date</span><span class="text-[#1B1B1B]">{{ $expiryDate }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Payment Method</span><span class="text-[#1B1B1B]">{{ $paymentMethod }}</span></div>
                        </div>

                        <div class="flex mt-7 gap-[16px]">
                            <button class="bg-white border border-[#0053FF] text-[#0053FF] text-sm px-8 py-2 rounded-lg hover:bg-gray-100 leading-[15px]">Invoice</button>
                            <button class="bg-white border border-[#0053FF] text-[#0053FF] text-sm px-8 py-2 rounded-lg hover:bg-gray-100 leading-[15px]">Renewal Now</button>
                            <button class="bg-white border border-[#0053FF] text-[#0053FF] text-sm px-8 py-2 rounded-lg hover:bg-gray-100 leading-[15px]">More Info</button>
                        </div>
                    </div>

                    <!-- LEGAL SUPPORT -->
                    <div class="pt-4 flex gap-[13px] items-center">
                        <h3 class="text-sm text-blue-600 font-medium">Legal Support</h3>
                        <div class="bg-[#808080] flex-1 h-[1px] relative top-[2px]"></div>
                    </div>
                </div>
            </div>
        @endif
    </div>
            

@elserole('user')
    <div class="flex items-center justify-center min-h-screen">
        <p class="text-gray-600 text-lg">⛔ You don’t have permission to view this page.</p>
    </div>
@endrole