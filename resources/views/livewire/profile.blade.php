<div class="h-full w-full flex flex-col" 
     x-data
     @refresh-page.window="setTimeout(() => window.location.reload(), 500)">
    <!-- Flash Messages -->
    @if (session()->has('profile-message'))
        <div x-data="{ show: true }" 
             x-init="setTimeout(() => show = false, 5000)" 
             x-show="show" 
             x-transition
             class="fixed top-4 right-4 z-50 bg-[#0043EF] text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
            <span>{{ session('profile-message') }}</span>
            <button @click="show = false" class="text-white hover:text-gray-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <div class="flex-1 min-h-0 w-full flex gap-x-[18px] px-2 pt-3" style="padding-bottom: 0px;">
        <!-- Sidebar -->
        <div class="w-72 max-[1750px]:w-64 bg-white rounded-xl flex flex-col p-5 max-[1750px]:p-4 overflow-y-auto [scrollbar-width:none] shrink-0 h-full">
            @php
                $currentRoute = Route::currentRouteName();
            @endphp
            
            <div class="mb-4">
                <h4 class="mb-4 text-[#616161] text-sm">Personal account</h4>
                <ul>
                    <li class="flex items-center gap-2 cursor-pointer group hover:bg-[#F5F6FA] rounded-md text-md text-[#1B1B1B] hover:text-[#0043EF] py-3 px-3 mb-3 {{ $currentRoute == 'profile' ? 'bg-[#F5F6FA] text-[#0043EF]' : '' }}">
                        <svg class="w-6 h-6 block {{ $currentRoute == 'profile' ? 'text-[#0043EF]' : 'text-[#1B1B1B] group-hover:hidden' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <svg class="w-6 h-6 hidden {{ $currentRoute == 'profile' ? 'block text-[#0043EF]' : 'group-hover:block text-[#0043EF]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <a href="{{ route('profile') }}" class="flex-1">Your profile</a>
                    </li>
                    <li class="flex items-center gap-2 cursor-pointer group hover:bg-[#F5F6FA] rounded-md text-md text-[#1B1B1B] hover:text-[#0043EF] py-3 px-3 mb-3 {{ $currentRoute == 'profile.password' ? 'bg-[#F5F6FA] text-[#0043EF]' : '' }}">
                        <svg class="w-6 h-6 block {{ $currentRoute == 'profile.password' ? 'text-[#0043EF]' : 'text-[#1B1B1B] group-hover:hidden' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <svg class="w-6 h-6 hidden {{ $currentRoute == 'profile.password' ? 'block text-[#0043EF]' : 'group-hover:block text-[#0043EF]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <a href="{{ route('profile.password') }}" class="flex-1">Change Password</a>
                    </li>
                </ul>
            </div>

            @unlessrole('super_admin')
            <div>
                <h4 class="mb-4 text-[#616161] text-sm">Payment and plans</h4>
                <ul>
                    <li class="flex items-center gap-2 cursor-pointer group hover:bg-[#F5F6FA] rounded-md text-md text-[#1B1B1B] hover:text-[#0043EF] py-3 px-3 mb-3 {{ $currentRoute == 'subscription.page' ? 'bg-[#F5F6FA] text-[#0043EF]' : '' }}">
                        <svg class="w-6 h-6 block {{ $currentRoute == 'subscription.page' ? 'text-[#0043EF]' : 'text-[#1B1B1B] group-hover:hidden' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <svg class="w-6 h-6 hidden {{ $currentRoute == 'subscription.page' ? 'block text-[#0043EF]' : 'group-hover:block text-[#0043EF]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <a href="{{ route('subscription.page') }}" class="flex-1">Subscription</a>
                    </li>
                    <li class="flex items-center gap-2 cursor-pointer group hover:bg-[#F5F6FA] rounded-md text-md text-[#1B1B1B] hover:text-[#0043EF] py-3 px-3 mb-3 {{ $currentRoute == 'purchase.history' ? 'bg-[#F5F6FA] text-[#0043EF]' : '' }}">
                        <svg class="w-6 h-6 block {{ $currentRoute == 'purchase.history' ? 'text-[#0043EF]' : 'text-[#1B1B1B] group-hover:hidden' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <svg class="w-6 h-6 hidden {{ $currentRoute == 'purchase.history' ? 'block text-[#0043EF]' : 'group-hover:block text-[#0043EF]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <a href="{{ route('purchase.history') }}" class="flex-1">Purchase History</a>
                    </li>
                </ul>
            </div>
            @endunlessrole
        </div>

        <!-- Main Content -->
        <div class="flex-1 min-w-0 overflow-y-auto flex flex-col gap-[11px] [scrollbar-width:none] h-full">
            @php
                $user = Auth::user();
                $initials = strtoupper(substr($user->first_name ?? '', 0, 1) . substr($user->last_name ?? '', 0, 1));
                $profilePhoto = $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : null;
            @endphp

            <!-- Header -->
            <div class="flex max-[1550px]:flex-col items-center justify-between max-[1550px]:items-start max-[1550px]:gap-4 bg-white p-6 rounded-xl py-8 max-[1750px]:py-6">
                <div class="flex items-center gap-[20px] max-[1750px]:gap-[15px]">
                    <div class="relative">
                        @if($profilePhoto)
                            <img src="{{ $profilePhoto }}" alt="Profile" class="w-22 h-22 rounded-full object-cover max-[1750px]:w-16 max-[1750px]:h-16 border-4 border-white shadow-lg ring-2 ring-[#0043EF] ring-offset-2">
                        @else
                            <div class="w-22 h-22 max-[1750px]:w-16 max-[1750px]:h-16 rounded-full bg-[#EBF4FF] flex items-center justify-center text-2xl font-semibold text-[#0043EF] border-4 border-white shadow-lg ring-2 ring-[#0043EF] ring-offset-2">
                                {{ $initials }}
                            </div>
                        @endif
                        <!-- Edit Photo Button -->
                        <label for="profile-photo-upload" class="absolute bottom-0 right-0 bg-white border border-[#0043EF] text-[#0043EF] p-1 rounded-full cursor-pointer hover:bg-[#0043EF] hover:text-white transition-all duration-200 shadow-md hover:shadow-lg group z-10">
                            <svg class="w-3 h-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </label>
                        <input id="profile-photo-upload" type="file" class="hidden" wire:model="photo" accept="image/*">
                        @error('photo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <div class="flex gap-2 items-center mb-1">
                            <h1 class="text-2xl max-[1750px]:text-xl font-medium text-[#0043EF]">
                                @if($editingProfile)
                                    <form wire:submit.prevent="updateProfile" class="flex gap-2 items-center">
                                        <input type="text" wire:model.defer="first_name" class="border border-gray-300 rounded px-2 py-1 text-xl font-medium text-[#0043EF] w-32">
                                        <input type="text" wire:model.defer="last_name" class="border border-gray-300 rounded px-2 py-1 text-xl font-medium text-[#0043EF] w-32">
                                        <button type="submit" class="text-green-600 hover:text-green-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                        <button type="button" wire:click="$set('editingProfile', false)" class="text-red-600 hover:text-red-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    {{ $first_name }} {{ $last_name }}
                                @endif
                            </h1>
                            @if(!$editingProfile)
                                <button wire:click="$set('editingProfile', true)" class="cursor-pointer p-1.5 rounded-md hover:bg-[#EEF6FF] transition-all duration-200 group">
                                    <img class="w-[16px] h-[16px] group-hover:scale-110 transition-transform" src="{{ asset('images/edit.svg') }}" alt="Edit" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <svg class="w-[16px] h-[16px] hidden group-hover:text-[#0043EF] text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                        <div class="flex gap-[14px]">
                            <p class="text-sm flex items-center text-[#616161] gap-1">
                                <span class="text-[#21B36A] w-[10px] h-[10px] rounded-full bg-[#21B36A]"></span>
                                {{ ucfirst($availability_status ?? 'Available') }}
                            </p>
                            <a class="text-sm text-[#616161]" href="mailto:{{ $email }}">{{ $email }}</a>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-4 text-sm">
                    <div class="bg-[#F5F6FA] p-6 max-[1750px]:p-4 rounded-md text-center flex flex-col justify-center gap-1">
                        <p class="text-sm max-[1750px]:text-xs text-[#616161]">Current Position</p>
                        <p class="text-sm max-[1750px]:text-xs font-semibold text-[#1B1B1B]">{{ $user->roles->first()->name ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-[#F5F6FA] p-6 max-[1750px]:p-4 rounded-md text-center flex flex-col justify-center gap-1">
                        <p class="text-sm max-[1750px]:text-xs text-[#616161]">Current Yacht</p>
                        <p class="text-sm max-[1750px]:text-xs font-semibold text-[#1B1B1B]">{{ $current_yacht ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-[#F5F6FA] p-6 max-[1750px]:p-4 rounded-md text-center flex flex-col justify-center gap-1">
                        <p class="text-sm max-[1750px]:text-xs text-[#616161]">Team Experience</p>
                        <p class="text-sm max-[1750px]:text-xs font-semibold text-[#1B1B1B]">{{ $years_experience ?? 0 }} Years</p>
                    </div>
                    <div class="bg-[#F5F6FA] p-6 max-[1750px]:p-4 rounded-md text-center flex flex-col justify-center gap-1">
                        <p class="text-sm max-[1750px]:text-xs text-[#616161]">Available From</p>
                        <p class="text-sm max-[1750px]:text-xs font-semibold text-[#1B1B1B]">{{ $available_from ? \Carbon\Carbon::parse($available_from)->format('d M Y') : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Professional Summary -->
            <section class="bg-white p-6 py-7 rounded-xl">
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-semibold mb-2 text-[#1B1B1B]">Professional Summary</h2>
                    @if(!$editingProfile && !$editingSummary)
                        <button wire:click="$set('editingSummary', true)" class="cursor-pointer">
                            <img class="w-[16px] h-[16px]" src="{{ asset('images/edit-03.svg') }}" alt="Edit" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <svg class="w-[16px] h-[16px] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                    @endif
                </div>
                @if($editingProfile || $editingSummary)
                    <form wire:submit.prevent="updateProfessionalSummary" class="space-y-3">
                        <textarea wire:model.defer="professional_summary" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-[#616161] text-md focus:outline-none focus:ring-2 focus:ring-[#0043EF]"></textarea>
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-[#0043EF] text-white rounded-lg hover:bg-[#003399]">Save</button>
                            <button type="button" wire:click="$set('editingSummary', false); $set('editingProfile', false)" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
                        </div>
                    </form>
                @else
                    <p class="text-[#616161] text-md">{{ $professional_summary ?? 'No professional summary available. Click edit to add one.' }}</p>
                @endif
            </section>

            <!-- Career Profile -->
            <section class="bg-white p-5 rounded-xl space-y-4">
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-semibold text-[#1B1B1B]">Career Profile</h2>
                    @if(!$editingProfile && !$editingCareerProfile)
                        <button wire:click="$set('editingCareerProfile', true)" class="cursor-pointer p-1.5 rounded-md hover:bg-[#EEF6FF] transition-all duration-200 group">
                            <img class="w-[16px] h-[16px] group-hover:scale-110 transition-transform" src="{{ asset('images/edit.svg') }}" alt="Edit" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <svg class="w-[16px] h-[16px] hidden group-hover:text-[#0043EF] text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                    @endif
                </div>
                @if($editingProfile || $editingCareerProfile)
                    <form wire:submit.prevent="updateCareerProfile" class="space-y-4">
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div class="flex flex-col gap-1">
                                <label class="text-[#616161] text-sm">Current Position</label>
                                <input type="text" value="{{ $user->roles->first()->name ?? 'N/A' }}" disabled class="border border-gray-300 rounded px-3 py-2 font-semibold text-[16px] text-[#1B1B1B] bg-gray-100 cursor-not-allowed">
                                <p class="text-xs text-[#616161] mt-1">Role-based (cannot be edited here)</p>
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[#616161] text-sm">Employment Type</label>
                                <input type="text" wire:model.defer="employment_type" class="border border-gray-300 rounded px-3 py-2 font-semibold text-[16px] text-[#1B1B1B]">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[#616161] text-sm">Expected Salary</label>
                                <input type="text" wire:model.defer="expected_salary" class="border border-gray-300 rounded px-3 py-2 font-semibold text-[16px] text-[#1B1B1B]">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[#616161] text-sm">Current Yacht</label>
                                <input type="text" wire:model.defer="current_yacht" class="border border-gray-300 rounded px-3 py-2 font-semibold text-[16px] text-[#1B1B1B]">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[#616161] text-sm">Vessel Preference</label>
                                <input type="text" wire:model.defer="vessel_preference" class="border border-gray-300 rounded px-3 py-2 font-semibold text-[16px] text-[#1B1B1B]">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[#616161] text-sm">Available Status</label>
                                <select wire:model.defer="availability_status" class="border border-gray-300 rounded px-3 py-2 font-semibold text-[16px] text-[#1B1B1B]">
                                    <option value="">Select Status</option>
                                    <option value="available">Available</option>
                                    <option value="busy">Busy</option>
                                    <option value="looking_for_work">Looking for Work</option>
                                    <option value="on_leave">On Leave</option>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[#616161] text-sm">Team Experience (Years)</label>
                                <input type="number" wire:model.defer="years_experience" min="0" max="100" class="border border-gray-300 rounded px-3 py-2 font-semibold text-[16px] text-[#1B1B1B]">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[#616161] text-sm">Special Services</label>
                                <input type="text" wire:model.defer="special_services" class="border border-gray-300 rounded px-3 py-2 font-semibold text-[16px] text-[#1B1B1B]">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[#616161] text-sm">Available From</label>
                                <input type="date" wire:model.defer="available_from" class="border border-gray-300 rounded px-3 py-2 font-semibold text-[16px] text-[#1B1B1B]">
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-[#0043EF] text-white rounded-lg hover:bg-[#003399]">Save</button>
                            <button type="button" wire:click="$set('editingCareerProfile', false); $set('editingProfile', false)" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
                        </div>
                    </form>
                @else
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div class="flex flex-col gap-1">
                            <p class="text-[#616161] text-sm">Current Position</p>
                            <p class="font-semibold text-[16px] text-[#1B1B1B]">{{ $user->roles->first()->name ?? 'N/A' }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-[#616161] text-sm">Employment Type</p>
                            <p class="font-semibold text-[16px] text-[#1B1B1B]">{{ $employment_type ?? 'N/A' }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-[#616161] text-sm">Expected Salary</p>
                            <p class="font-semibold text-[16px] text-[#1B1B1B]">{{ $expected_salary ?? 'N/A' }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-[#616161] text-sm">Current Yacht</p>
                            <p class="font-semibold text-[16px] text-[#1B1B1B]">{{ $current_yacht ?? 'N/A' }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-[#616161] text-sm">Vessel Preference</p>
                            <p class="font-semibold text-[16px] text-[#1B1B1B]">{{ $vessel_preference ?? 'N/A' }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-[#616161] text-sm">Available Status</p>
                            <p class="font-semibold text-[16px] text-[#1B1B1B]">{{ ucfirst($availability_status ?? 'N/A') }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-[#616161] text-sm">Team Experience</p>
                            <p class="font-semibold text-[16px] text-[#1B1B1B]">{{ $years_experience ?? 0 }} Years</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-[#616161] text-sm">Special Services</p>
                            <p class="font-semibold text-[16px] text-[#1B1B1B]">{{ $special_services ?? 'N/A' }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-[#616161] text-sm">Available From</p>
                            <p class="font-semibold text-[16px] text-[#1B1B1B]">{{ $available_from ? \Carbon\Carbon::parse($available_from)->format('d M Y') : 'N/A' }}</p>
                        </div>
                    </div>
                @endif
            </section>

            <!-- Career History -->
            <section class="bg-white p-5 rounded-xl space-y-4">
                <div class="flex gap-4 justify-between">
                    <div class="flex items-center gap-2 mb-5">
                        <h2 class="text-xl font-semibold text-[#1B1B1B]">Career History</h2>
                    </div>
                    <a href="{{ route('career-history.manage') }}" class="cursor-pointer h-fit flex items-center gap-[12px] bg-[#F5F6FA] px-4 py-2 rounded-lg text-sm text-[#0043EF] hover:bg-gray-200 font-medium">
                        <img src="{{ asset('images/add-circle-blue.svg') }}" alt="" class="h-[11px] w-[11px]">
                        Add Career History
                    </a>
                </div>
                @if(count($careerHistoryEntries) > 0)
                    @foreach($careerHistoryEntries as $entry)
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-semibold text-md text-[#1B1B1B]">{{ $entry->vessel_name }}</h3>
                                <a href="{{ route('career-history.manage') }}" class="cursor-pointer">
                                    <img class="w-[16px] h-[16px]" src="{{ asset('images/edit.svg') }}" alt="Edit">
                                </a>
                            </div>
                            @php
                                $start = $entry->start_date;
                                $end = $entry->end_date ?? now();
                                $duration = $entry->getFormattedDuration();
                            @endphp
                            <p class="text-sm text-[#616161]">
                                {{ $entry->position_title }} | 
                                {{ $start->format('M Y') }} – 
                                {{ $entry->end_date ? $end->format('M Y') : 'Present' }} 
                                ({{ $duration }})
                            </p>
                            @if($entry->key_duties)
                                <p class="mt-1 text-md text-[#616161]">{{ $entry->key_duties }}</p>
                            @endif
                            @if($entry->notable_achievements)
                                <p class="mt-1 text-sm text-[#616161]"><strong>Achievements:</strong> {{ $entry->notable_achievements }}</p>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p class="text-[#616161]">No career history available. Click "Add Career History" to add one.</p>
                @endif
            </section>

            <!-- Certifications -->
            <section class="bg-white p-5 rounded-xl space-y-4">
                <div class="flex gap-4 justify-between">
                    <div class="flex items-center gap-2 mb-5">
                        <h2 class="text-xl font-semibold text-[#1B1B1B]">Certifications</h2>
                    </div>
                    <button wire:click="openCertificationModal()" class="cursor-pointer h-fit flex items-center gap-[12px] bg-[#F5F6FA] px-4 py-2 rounded-lg text-sm text-[#0043EF] hover:bg-gray-200 font-medium">
                        <img src="{{ asset('images/add-circle-blue.svg') }}" alt="" class="h-[11px] w-[11px]">
                        {{ $showCertificationModal ? 'Cancel' : 'Add Certifications' }}
                    </button>
                </div>
                
                <!-- Certification Form (Inline) -->
                @if($showCertificationModal)
                    <div class="border border-gray-300 rounded-lg p-5 bg-gray-50">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-[#1B1B1B]">
                                {{ $editingCertificationIndex !== null ? 'Edit Certification' : 'Add Certification' }}
                            </h3>
                        </div>
                        
                        <form wire:submit.prevent="saveCertification" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-[#616161] mb-2">Certification Name *</label>
                                <input type="text" wire:model.defer="certificationName" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#0043EF]"
                                    placeholder="e.g., STCW, ENG1, Food & Hygiene">
                                @error('certificationName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-[#616161] mb-2">Issued By</label>
                                <input type="text" wire:model.defer="certificationIssuedBy" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#0043EF]"
                                    placeholder="e.g., Whitehorse Academy, Abu Dhabi Maritime Academy">
                                @error('certificationIssuedBy') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-[#616161] mb-2">Expiry Date</label>
                                <input type="date" wire:model.defer="certificationExpiryDate" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#0043EF]">
                                @error('certificationExpiryDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-[#616161] mb-2">Status *</label>
                                <select wire:model.defer="certificationStatus" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#0043EF]">
                                    <option value="pending">Pending</option>
                                    <option value="verified">Verified</option>
                                    <option value="expired">Expired</option>
                                </select>
                                @error('certificationStatus') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="flex gap-2 pt-2">
                                <button type="submit" class="px-4 py-2 bg-[#0043EF] text-white rounded-lg hover:bg-[#003399] font-medium">
                                    Save
                                </button>
                                <button type="button" wire:click="closeCertificationModal" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
                
                @if(count($certifications) > 0)
                    @foreach($certifications as $index => $cert)
                        <div class="border border-[#616161] rounded-lg p-4 flex justify-between items-center">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="font-semibold text-md text-[#1B1B1B]">{{ is_array($cert) ? ($cert['name'] ?? 'N/A') : $cert }}</h3>
                                    <button wire:click="openCertificationModal({{ $index }})" class="cursor-pointer p-1.5 rounded-md hover:bg-[#EEF6FF] transition-all duration-200 group">
                                        <img class="w-[16px] h-[16px] group-hover:scale-110 transition-transform" src="{{ asset('images/edit.svg') }}" alt="Edit" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <svg class="w-[16px] h-[16px] hidden group-hover:text-[#0043EF] text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="removeCertification({{ $index }})" class="cursor-pointer text-red-600 hover:text-red-800">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                                @if(is_array($cert))
                                    <p class="text-sm text-[#616161]">
                                        Issued: {{ $cert['issued_by'] ?: 'N/A' }} • 
                                        Expiry: {{ isset($cert['expiry_date']) && $cert['expiry_date'] ? \Carbon\Carbon::parse($cert['expiry_date'])->format('d M Y') : 'N/A' }}
                                    </p>
                                @else
                                    <p class="text-sm text-[#616161]">Click edit to add details</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                @php
                                    $status = is_array($cert) ? ($cert['status'] ?? 'pending') : 'pending';
                                    $statusColor = $status === 'verified' ? '#21B36A' : ($status === 'expired' ? '#EF4444' : '#E9A561');
                                @endphp
                                <span class="w-[10px] h-[10px] rounded-full" style="background-color: {{ $statusColor }}"></span>
                                <span class="text-sm" style="color: {{ $statusColor }}">{{ ucfirst($status) }}</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-[#616161]">No certifications available. Click "Add Certifications" to add one.</p>
                @endif
            </section>

            <!-- Skills -->
            <section class="bg-white p-5 rounded-xl">
                <div class="flex items-center gap-2 mb-5">
                    <h2 class="text-xl font-semibold text-[#1B1B1B]">Skills & Competencies</h2>
                    @if(!$editingSkills)
                        <button wire:click="$set('editingSkills', true)" class="cursor-pointer p-1.5 rounded-md hover:bg-[#EEF6FF] transition-all duration-200 group">
                            <img class="w-[16px] h-[16px] group-hover:scale-110 transition-transform" src="{{ asset('images/edit.svg') }}" alt="Edit" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <svg class="w-[16px] h-[16px] hidden group-hover:text-[#0043EF] text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                    @endif
                </div>
                @if($editingSkills)
                    <div class="space-y-3">
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <input type="text" wire:model="newSpecialization" wire:keydown.enter.prevent="addSpecialization" 
                                    placeholder="Add skill" 
                                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0043EF] {{ $errors->has('newSpecialization') ? 'border-red-500' : 'border-gray-300' }}">
                                @error('newSpecialization')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <button wire:click="addSpecialization" class="px-4 py-2 bg-[#0043EF] text-white rounded-lg hover:bg-[#003399]">Add</button>
                        </div>
                        <div class="flex flex-wrap gap-[12px]">
                            @foreach($specializations as $index => $skill)
                                <span class="px-4 py-2 bg-[#EEF6FF] text-[#0043EF] rounded-full text-sm flex items-center gap-2">
                                    {{ $skill }}
                                    <button wire:click="removeSpecialization({{ $index }})" class="text-red-600 hover:text-red-800">×</button>
                                </span>
                            @endforeach
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="updateSkills" class="px-4 py-2 bg-[#0043EF] text-white rounded-lg hover:bg-[#003399]">Save</button>
                            <button wire:click="$set('editingSkills', false)" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
                        </div>
                    </div>
                @else
                    <div class="flex flex-wrap gap-[12px]">
                        @if(count($specializations) > 0)
                            @foreach($specializations as $skill)
                                <span class="px-4 py-2 bg-[#EEF6FF] text-[#0043EF] rounded-full text-sm">{{ $skill }}</span>
                            @endforeach
                        @else
                            <span class="text-[#616161]">No skills added yet. Click edit to add skills.</span>
                        @endif
                    </div>
                @endif
            </section>

            <!-- Personal Details -->
            <section class="bg-white p-5 rounded-xl space-y-4">
                <div class="flex items-center gap-2 mb-5">
                    <h2 class="text-xl font-semibold text-[#1B1B1B]">Personal Details</h2>
                    @if(!$editingPersonalDetails)
                        <button wire:click="$set('editingPersonalDetails', true)" class="cursor-pointer p-1.5 rounded-md hover:bg-[#EEF6FF] transition-all duration-200 group">
                            <img class="w-[16px] h-[16px] group-hover:scale-110 transition-transform" src="{{ asset('images/edit.svg') }}" alt="Edit" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <svg class="w-[16px] h-[16px] hidden group-hover:text-[#0043EF] text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                    @endif
                </div>
                @if($editingPersonalDetails)
                    <form wire:submit.prevent="updatePersonalDetails" class="space-y-4">
                        <div class="grid max-w-2xl grid-cols-2 gap-6 text-sm">
                            <div class="flex flex-col gap-1">
                                <label class="text-[#616161] text-sm">Nationality</label>
                                <input type="text" wire:model.defer="nationality" class="border border-gray-300 rounded px-3 py-2 font-semibold text-[16px] text-[#1B1B1B]">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[#616161] text-sm">Passport Validity</label>
                                <input type="date" wire:model.defer="passport_validity" class="border border-gray-300 rounded px-3 py-2 font-semibold text-[16px] text-[#1B1B1B]">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[#616161] text-sm">Date of Birth</label>
                                <input type="date" wire:model.defer="date_of_birth" class="border border-gray-300 rounded px-3 py-2 font-semibold text-[16px] text-[#1B1B1B]">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[#616161] text-sm">Visas</label>
                                <input type="text" wire:model.defer="visas" class="border border-gray-300 rounded px-3 py-2 font-semibold text-[16px] text-[#1B1B1B]">
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-[#0043EF] text-white rounded-lg hover:bg-[#003399]">Save</button>
                            <button type="button" wire:click="$set('editingPersonalDetails', false)" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
                        </div>
                    </form>
                @else
                    <div class="grid max-w-2xl grid-cols-2 gap-6 text-sm">
                        <div class="flex flex-col gap-1">
                            <p class="text-[#616161] text-sm">Nationality</p>
                            <p class="font-semibold text-[16px] text-[#1B1B1B]">{{ $nationality ?? 'N/A' }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-[#616161] text-sm">Passport Validity</p>
                            <p class="font-semibold text-[16px] text-[#1B1B1B]">{{ $passport_validity ? 'Valid until ' . \Carbon\Carbon::parse($passport_validity)->format('d M Y') : 'N/A' }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-[#616161] text-sm">Date of Birth</p>
                            <p class="font-semibold text-[16px] text-[#1B1B1B]">{{ $date_of_birth ? \Carbon\Carbon::parse($date_of_birth)->format('d M Y') : 'N/A' }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-[#616161] text-sm">Visas</p>
                            <p class="font-semibold text-[16px] text-[#1B1B1B]">{{ $visas ?? 'N/A' }}</p>
                        </div>
                    </div>
                @endif

                <!-- Languages Section -->
                <div class="space-y-4 border-t border-gray-300 mt-5 pt-5">
                    <div class="flex gap-4 justify-between">
                        <div class="flex items-center gap-2 mb-5">
                            <h2 class="text-xl font-semibold text-[#1B1B1B]">Languages</h2>
                            @if(!$editingLanguages)
                                <button wire:click="$set('editingLanguages', true)" class="cursor-pointer p-1.5 rounded-md hover:bg-[#EEF6FF] transition-all duration-200 group">
                                    <img class="w-[16px] h-[16px] group-hover:scale-110 transition-transform" src="{{ asset('images/edit.svg') }}" alt="Edit" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <svg class="w-[16px] h-[16px] hidden group-hover:text-[#0043EF] text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                        @if($editingLanguages)
                            <div class="flex items-center gap-2">
                                <div class="flex-1">
                                    <input type="text" wire:model="newLanguage" wire:keydown.enter.prevent="addLanguage" 
                                        placeholder="Language name" 
                                        class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0043EF] {{ $errors->has('newLanguage') ? 'border-red-500' : 'border-gray-300' }}">
                                    @error('newLanguage')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button wire:click="addLanguage" class="cursor-pointer h-fit flex items-center gap-[12px] bg-[#F5F6FA] px-4 py-2 rounded-lg text-sm text-[#0043EF] hover:bg-gray-200 font-medium">
                                    <img src="{{ asset('images/add-circle-blue.svg') }}" alt="" class="h-[11px] w-[11px]">
                                    Add Languages
                                </button>
                            </div>
                        @endif
                    </div>
                    @if(count($languages) > 0)
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-300 text-left text-[#616161]">
                                    <th class="text-sm font-normal pb-3 w-20">Language</th>
                                    <th class="text-sm font-normal pb-3 w-20">Proficiency</th>
                                    <th class="text-sm font-normal pb-3 w-15">Read</th>
                                    <th class="text-sm font-normal pb-3 w-15">Write</th>
                                    <th class="text-sm font-normal pb-3 w-20">Speak</th>
                                    @if($editingLanguages)
                                        <th class="text-sm font-normal pb-3">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($languages as $index => $lang)
                                    @php
                                        $langData = is_array($lang) ? $lang : ['name' => $lang, 'proficiency' => 'Proficient', 'read' => true, 'write' => true, 'speak' => true];
                                    @endphp
                                    <tr>
                                        <td class="py-3 font-medium text-[16px]">{{ $langData['name'] ?? $lang }}</td>
                                        <td class="py-3 font-medium text-[16px]">{{ $langData['proficiency'] ?? 'Proficient' }}</td>
                                        <td class="py-3 font-medium text-[16px]">
                                            <label class="inline-flex items-center cursor-pointer relative">
                                                <input type="checkbox" wire:model.live="languages.{{ $index }}.read" class="peer hidden">
                                                <span class="w-5 h-5 rounded-full border border-[#616161]"></span>
                                                <svg class="hidden peer-checked:block w-5 h-5 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-[#0043EF]" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </label>
                                        </td>
                                        <td class="py-3 font-medium text-[16px]">
                                            <label class="inline-flex items-center cursor-pointer relative">
                                                <input type="checkbox" wire:model.live="languages.{{ $index }}.write" class="peer hidden">
                                                <span class="w-5 h-5 rounded-full border border-[#616161]"></span>
                                                <svg class="hidden peer-checked:block w-5 h-5 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-[#0043EF]" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </label>
                                        </td>
                                        <td class="py-3 font-medium text-[16px]">
                                            <label class="inline-flex items-center cursor-pointer relative">
                                                <input type="checkbox" wire:model.live="languages.{{ $index }}.speak" class="peer hidden">
                                                <span class="w-5 h-5 rounded-full border border-[#616161]"></span>
                                                <svg class="hidden peer-checked:block w-5 h-5 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-[#0043EF]" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </label>
                                        </td>
                                        @if($editingLanguages)
                                            <td class="py-3">
                                                <button wire:click="removeLanguage({{ $index }})" class="text-red-600 hover:text-red-800">Remove</button>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($editingLanguages)
                            <div class="flex gap-2">
                                <button wire:click="updateLanguages" class="px-4 py-2 bg-[#0043EF] text-white rounded-lg hover:bg-[#003399]">Save Languages</button>
                                <button wire:click="$set('editingLanguages', false)" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
                            </div>
                        @endif
                    @else
                        <p class="text-[#616161]">No languages added yet. Click edit to add languages.</p>
                    @endif
                </div>
            </section>
        </div>
        </div>
    </div>
</div>
