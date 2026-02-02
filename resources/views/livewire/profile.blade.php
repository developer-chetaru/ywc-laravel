<div class="h-full w-full flex flex-col" 
     x-data="{ imageModal: { open: false, src: '', alt: '' }, openImage(src, alt = '') { this.imageModal = { open: true, src: src, alt: alt }; }, closeImage() { this.imageModal = { open: false, src: '', alt: '' }; } }"
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
        @if($showSidebarCard)
        <div class="w-72 max-[1750px]:w-64 bg-[#0066FF] rounded-xl flex flex-col shadow-lg border border-blue-400/30 overflow-hidden shrink-0 h-full">
            <!-- Settings Header -->
            <div class="px-6 py-4 flex items-center justify-between border-b border-blue-300/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Settings</h3>
                </div>
                <button wire:click="$set('showSidebarCard', false)" class="p-1.5 rounded-lg hover:bg-white/10 transition-colors group" title="Hide Settings">
                    <svg class="w-5 h-5 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Settings Content -->
            <div class="flex-1 overflow-y-auto p-5 max-[1750px]:p-4 [scrollbar-width:none]">
                @php
                    $currentRoute = Route::currentRouteName();
                @endphp
                
                <!-- Personal Account Section -->
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-1 h-5 bg-white/30 rounded-full"></div>
                        <h4 class="text-sm font-semibold text-white/80 uppercase tracking-wide">Personal account</h4>
                    </div>
                    <div class="space-y-1">
                        <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 group {{ $currentRoute == 'profile' ? 'bg-white text-black shadow-sm' : 'text-white hover:bg-white/10' }}">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $currentRoute == 'profile' ? 'bg-white/20' : 'bg-white/10 group-hover:bg-white/15' }} transition-colors">
                                <svg class="w-5 h-5 {{ $currentRoute == 'profile' ? 'text-black' : 'text-white' }}" fill="{{ $currentRoute == 'profile' ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <span class="font-medium flex-1">Your profile</span>
                            @if($currentRoute == 'profile')
                                <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            @endif
                        </a>
                        <a href="{{ route('profile.password') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 group {{ $currentRoute == 'profile.password' ? 'bg-white text-black shadow-sm' : 'text-white hover:bg-white/10' }}">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $currentRoute == 'profile.password' ? 'bg-white/20' : 'bg-white/10 group-hover:bg-white/15' }} transition-colors">
                                <svg class="w-5 h-5 {{ $currentRoute == 'profile.password' ? 'text-black' : 'text-white' }}" fill="{{ $currentRoute == 'profile.password' ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <span class="font-medium flex-1">Change Password</span>
                            @if($currentRoute == 'profile.password')
                                <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            @endif
                        </a>
                    </div>
                </div>

                @unlessrole('super_admin')
                <!-- Payment and Plans Section -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-1 h-5 bg-white/30 rounded-full"></div>
                        <h4 class="text-sm font-semibold text-white/80 uppercase tracking-wide">Payment and plans</h4>
                    </div>
                    <div class="space-y-1">
                        <a href="{{ route('subscription.page') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 group {{ $currentRoute == 'subscription.page' ? 'bg-white text-black shadow-sm' : 'text-white hover:bg-white/10' }}">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $currentRoute == 'subscription.page' ? 'bg-white/20' : 'bg-white/10 group-hover:bg-white/15' }} transition-colors">
                                <svg class="w-5 h-5 {{ $currentRoute == 'subscription.page' ? 'text-black' : 'text-white' }}" fill="{{ $currentRoute == 'subscription.page' ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <span class="font-medium flex-1">Subscription</span>
                            @if($currentRoute == 'subscription.page')
                                <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            @endif
                        </a>
                        <a href="{{ route('purchase.history') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 group {{ $currentRoute == 'purchase.history' ? 'bg-white text-black shadow-sm' : 'text-white hover:bg-white/10' }}">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $currentRoute == 'purchase.history' ? 'bg-white/20' : 'bg-white/10 group-hover:bg-white/15' }} transition-colors">
                                <svg class="w-5 h-5 {{ $currentRoute == 'purchase.history' ? 'text-black' : 'text-white' }}" fill="{{ $currentRoute == 'purchase.history' ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <span class="font-medium flex-1">Purchase History</span>
                            @if($currentRoute == 'purchase.history')
                                <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            @endif
                        </a>
                    </div>
                </div>
                @endunlessrole
            </div>
        </div>
        @endif

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
                    <!-- Settings Icon Button -->
                    <button wire:click="$set('showSidebarCard', {{ $showSidebarCard ? 'false' : 'true' }})" class="p-2 rounded-lg hover:bg-gray-100 transition-colors" title="{{ $showSidebarCard ? 'Hide Settings' : 'Show Settings' }}">
                        <svg class="w-6 h-6 text-[#616161] hover:text-[#0043EF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                    <div class="relative">
                        @if($profilePhoto)
                            <img src="{{ $profilePhoto }}" alt="Profile" class="w-22 h-22 rounded-full object-cover max-[1750px]:w-16 max-[1750px]:h-16 border-4 border-white shadow-lg ring-2 ring-[#0043EF] ring-offset-2 cursor-pointer hover:opacity-90 transition-opacity" @click="openImage('{{ $profilePhoto }}', '{{ $first_name }} {{ $last_name }} Profile Photo')">
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

            <!-- Forum Stats Section -->
            @if(!empty($forumStats))
            <section class="bg-white p-6 py-7 rounded-xl">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-[#1B1B1B]">Forum Activity</h2>
                    <a href="{{ route('forum.leaderboard') }}" class="text-sm text-[#0053FF] hover:underline">View Leaderboard →</a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
                        <p class="text-xs text-blue-600 mb-1">Reputation</p>
                        <p class="text-2xl font-bold text-blue-800">{{ $forumStats['reputation']['points'] ?? 0 }}</p>
                        <p class="text-xs text-blue-600 mt-1">
                            <span class="px-2 py-0.5 rounded {{ $forumStats['reputation']['level_color'] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $forumStats['reputation']['level'] ?? 'Newcomer' }}
                            </span>
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
                        <p class="text-xs text-green-600 mb-1">Threads</p>
                        <p class="text-2xl font-bold text-green-800">{{ $forumStats['thread_count'] ?? 0 }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
                        <p class="text-xs text-purple-600 mb-1">Posts</p>
                        <p class="text-2xl font-bold text-purple-800">{{ $forumStats['post_count'] ?? 0 }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-4 rounded-lg border border-yellow-200">
                        <p class="text-xs text-yellow-600 mb-1">Badges</p>
                        <p class="text-2xl font-bold text-yellow-800">{{ count($forumStats['badges'] ?? []) }}</p>
                    </div>
                </div>
                @if($forumStats['warning_count'] > 0)
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <p class="text-sm text-red-800">
                        <span class="font-semibold">{{ $forumStats['warning_count'] }}</span> warning(s) on record
                    </p>
                </div>
                @endif
                @if(count($forumStats['badges'] ?? []) > 0)
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">Earned Badges:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($forumStats['badges'] as $badge)
                        <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2" title="{{ $badge->description }}">
                            @if($badge->icon)
                            <span class="text-lg">{!! $badge->icon !!}</span>
                            @endif
                            <span class="text-sm font-medium text-gray-800">{{ $badge->name }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </section>
            @endif

            <!-- Professional Summary -->
            <section class="bg-white p-6 py-7 rounded-xl">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-semibold text-[#1B1B1B]">Professional Summary</h2>
                        @if(!$editingProfile && !$editingSummary)
                            <button wire:click="$set('editingSummary', true)" class="cursor-pointer">
                                <img class="w-[16px] h-[16px]" src="{{ asset('images/edit-03.svg') }}" alt="Edit" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <svg class="w-[16px] h-[16px] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                    <button wire:click="$set('showProfessionalSummary', {{ $showProfessionalSummary ? 'false' : 'true' }})" class="p-1.5 rounded-md hover:bg-gray-100 transition-colors" title="{{ $showProfessionalSummary ? 'Hide' : 'Show' }}">
                        @if($showProfessionalSummary)
                            <svg class="w-5 h-5 text-[#616161] hover:text-[#0043EF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-[#616161] hover:text-[#0043EF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        @endif
                    </button>
                </div>
                @if($showProfessionalSummary)
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
                @endif
            </section>

            <!-- Reviews & Itineraries Section -->
            <section class="bg-white p-6 rounded-xl">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-[#1B1B1B]">Activity</h2>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-[#616161]">{{ ($yachtReviews->count() ?? 0) + ($marinaReviews->count() ?? 0) + ($itineraryRoutes->count() ?? 0) }} items</span>
                        <button wire:click="$set('showActivityCard', {{ $showActivityCard ? 'false' : 'true' }})" class="p-1.5 rounded-md hover:bg-gray-100 transition-colors" title="{{ $showActivityCard ? 'Hide Activity' : 'Show Activity' }}">
                            @if($showActivityCard)
                                <svg class="w-5 h-5 text-[#616161] hover:text-[#0043EF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-[#616161] hover:text-[#0043EF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            @endif
                        </button>
                    </div>
                </div>

                @if($showActivityCard)

                <!-- Tabs -->
                <div class="flex gap-4 border-b border-gray-200 mb-6">
                    <button wire:click="$set('activeTab', 'reviews')" 
                            class="pb-3 px-2 text-sm font-medium transition-colors {{ $activeTab === 'reviews' ? 'text-[#21B36A] border-b-2 border-[#21B36A]' : 'text-[#616161] hover:text-[#0043EF]' }}">
                        Reviews ({{ ($yachtReviews->count() ?? 0) + ($marinaReviews->count() ?? 0) }})
                    </button>
                    <button wire:click="$set('activeTab', 'itineraries')" 
                            class="pb-3 px-2 text-sm font-medium transition-colors {{ $activeTab === 'itineraries' ? 'text-[#21B36A] border-b-2 border-[#21B36A]' : 'text-[#616161] hover:text-[#0043EF]' }}">
                        Itineraries ({{ $itineraryRoutes->count() ?? 0 }})
                    </button>
                </div>

                <!-- Reviews Tab Content -->
                @if($activeTab === 'reviews')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $allReviews = collect($yachtReviews ?? [])->concat($marinaReviews ?? [])->sortByDesc('created_at');
                            $displayReviews = $showAllReviews ? $allReviews : $allReviews->take(4);
                            $totalReviews = $allReviews->count();
                        @endphp
                        @forelse($displayReviews as $review)
                            @php
                                $isYachtReview = isset($review->yacht_id) || $review->yacht_id !== null;
                                $reviewType = $isYachtReview ? 'Yacht' : 'Marina';
                                if ($isYachtReview) {
                                    $reviewTitle = $review->yacht->name ?? 'Unknown Yacht';
                                } else {
                                    $reviewTitle = $review->marina->name ?? 'Unknown Marina';
                                }
                                $reviewDate = $review->created_at ? \Carbon\Carbon::parse($review->created_at)->diffForHumans() : 'Recently';
                            @endphp
                            <div class="bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow">
                                <!-- Card Header -->
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-[#EBF4FF] flex items-center justify-center text-sm font-semibold text-[#0043EF]">
                                            {{ strtoupper(substr($first_name ?? '', 0, 1) . substr($last_name ?? '', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h3 class="font-semibold text-[#1B1B1B]">{{ $first_name }} {{ $last_name }}</h3>
                                                <span class="text-[#21B36A] text-xs">✓</span>
                                                <span class="text-[#616161] text-xs">• You</span>
                                            </div>
                                            <p class="text-xs text-[#616161]">{{ $user->roles->first()->name ?? 'Member' }}</p>
                                            <p class="text-xs text-[#616161]">{{ $reviewDate }}</p>
                                        </div>
                                    </div>
                                    <button class="text-[#616161] hover:text-[#1B1B1B]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Card Content -->
                                <div class="mb-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <a href="{{ $isYachtReview ? route('yacht-reviews.show', $review->yacht->slug ?? '') : route('marina-reviews.show', $review->marina->slug ?? '') }}" class="text-sm font-medium text-[#0043EF] hover:underline">{{ $reviewType }} Review</a>
                                        <span class="text-sm text-[#616161]">•</span>
                                        <a href="{{ $isYachtReview ? route('yacht-reviews.show', $review->yacht->slug ?? '') : route('marina-reviews.show', $review->marina->slug ?? '') }}" class="text-sm text-[#616161] hover:text-[#0043EF] hover:underline">{{ $reviewTitle }}</a>
                                    </div>
                                    <h4 class="font-semibold text-[#1B1B1B] mb-2">{{ $review->title ?? 'Review' }}</h4>
                                    <div class="flex items-center gap-1 mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= ($review->overall_rating ?? 0) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                        <span class="text-sm text-[#616161] ml-1">({{ $review->overall_rating ?? 0 }}/5)</span>
                                    </div>
                                    <div x-data="{ expanded: false }">
                                        <p class="text-sm text-[#616161]" :class="expanded ? '' : 'line-clamp-3'">{{ $review->review ?? '' }}</p>
                                        @if(strlen($review->review ?? '') > 150)
                                            <button @click="expanded = !expanded" class="text-sm text-[#0043EF] hover:underline mt-1">
                                                <span x-show="!expanded">...more</span>
                                                <span x-show="expanded">...less</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Card Footer -->
                                <div x-data="{ showComments{{ $review->id }}: false, showShare{{ $review->id }}: false }" class="pt-3 border-t border-gray-100">
                                    <div class="flex items-center gap-4 text-[#616161] mb-0">
                                        @php
                                            $hasLiked = $this->hasUserLiked($review->id, $isYachtReview ? 'yacht' : 'marina');
                                            $reviewUrl = $isYachtReview ? route('yacht-reviews.show', $review->yacht->slug ?? '') : route('marina-reviews.show', $review->marina->slug ?? '');
                                        @endphp
                                        <button wire:click="toggleLike({{ $review->id }}, '{{ $isYachtReview ? 'yacht' : 'marina' }}')" class="flex items-center gap-1 hover:text-[#0043EF] transition-colors {{ $hasLiked ? 'text-[#0043EF]' : '' }}">
                                            <svg class="w-4 h-4 {{ $hasLiked ? 'fill-current' : '' }}" fill="{{ $hasLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                            </svg>
                                            <span class="text-xs">{{ $review->helpful_count ?? 0 }}</span>
                                        </button>
                                        <button @click="showComments{{ $review->id }} = !showComments{{ $review->id }}" class="flex items-center gap-1 hover:text-[#0043EF] transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            <span class="text-xs">Comment</span>
                                        </button>
                                        <div class="relative">
                                            <button @click="showShare{{ $review->id }} = !showShare{{ $review->id }}" class="flex items-center gap-1 hover:text-[#0043EF] transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                                </svg>
                                                <span class="text-xs">Share</span>
                                            </button>
                                            <div x-show="showShare{{ $review->id }}" @click.away="showShare{{ $review->id }} = false" x-transition class="absolute bottom-full mb-2 right-0 w-56 bg-white rounded-md shadow-lg border border-gray-200 z-10 p-3">
                                                <div class="space-y-2">
                                                    <button @click="navigator.clipboard.writeText('{{ $reviewUrl }}'); showShare{{ $review->id }} = false; alert('Link copied!')" class="w-full text-left px-3 py-2 text-sm text-[#616161] hover:bg-gray-100 rounded">Copy Link</button>
                                                    <button class="w-full text-left px-3 py-2 text-sm text-[#616161] hover:bg-gray-100 rounded">Share on Facebook</button>
                                                    <button class="w-full text-left px-3 py-2 text-sm text-[#616161] hover:bg-gray-100 rounded">Share on Twitter</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Comment Section (appears below footer buttons when expanded) -->
                                    <div x-show="showComments{{ $review->id }}" x-transition x-init="$watch('showComments{{ $review->id }}', value => { if (value) { $nextTick(() => { const input = $el.querySelector('input'); if (input) input.focus(); }); } })" class="mt-3 pt-3 border-t border-gray-100">
                                        <div class="flex gap-2 mb-2">
                                            <input type="text" placeholder="Write a comment..." class="flex-1 px-3 py-2 border border-[#0043EF] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0043EF]">
                                            <button class="px-4 py-2 bg-[#0043EF] text-white rounded-lg hover:bg-[#003399] text-sm font-medium">Post</button>
                                        </div>
                                        <p class="text-xs text-[#616161]">Comments feature coming soon...</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-8">
                                <p class="text-[#616161]">No reviews yet. Start reviewing to see them here!</p>
                            </div>
                        @endforelse
                    </div>
                    @if($totalReviews > 4 && !$showAllReviews)
                        <div class="mt-6 text-center">
                            <button wire:click="$set('showAllReviews', true)" class="text-sm text-[#0043EF] hover:underline font-medium">Show all →</button>
                        </div>
                    @elseif($totalReviews > 4 && $showAllReviews)
                        <div class="mt-6 text-center">
                            <button wire:click="$set('showAllReviews', false)" class="text-sm text-[#0043EF] hover:underline font-medium">Show less ↑</button>
                        </div>
                    @endif
                @endif

                <!-- Itineraries Tab Content -->
                @if($activeTab === 'itineraries')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $allItineraries = collect($itineraryRoutes ?? []);
                            $displayItineraries = $showAllItineraries ? $allItineraries : $allItineraries->take(4);
                            $totalItineraries = $allItineraries->count();
                        @endphp
                        @forelse($displayItineraries as $itinerary)
                            @php
                                $itineraryDate = $itinerary->created_at ? \Carbon\Carbon::parse($itinerary->created_at)->diffForHumans() : 'Recently';
                                $routeTitle = $itinerary->title ?? 'Untitled Route';
                                $routeDescription = $itinerary->description ?? '';
                                $durationDays = $itinerary->duration_days ?? 0;
                                $region = $itinerary->region ?? '';
                                $itineraryUrl = url('/itinerary/routes/' . ($itinerary->id ?? ''));
                            @endphp
                            <div class="bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow">
                                <!-- Card Header -->
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-[#EBF4FF] flex items-center justify-center text-sm font-semibold text-[#0043EF]">
                                            {{ strtoupper(substr($first_name ?? '', 0, 1) . substr($last_name ?? '', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h3 class="font-semibold text-[#1B1B1B]">{{ $first_name }} {{ $last_name }}</h3>
                                                <span class="text-[#21B36A] text-xs">✓</span>
                                                <span class="text-[#616161] text-xs">• You</span>
                                            </div>
                                            <p class="text-xs text-[#616161]">{{ $user->roles->first()->name ?? 'Member' }}</p>
                                            <p class="text-xs text-[#616161]">{{ $itineraryDate }}</p>
                                        </div>
                                    </div>
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = !open" class="text-[#616161] hover:text-[#1B1B1B]">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                            </svg>
                                        </button>
                                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-10">
                                            <div class="py-1">
                                                <a href="{{ $itineraryUrl }}" class="block px-4 py-2 text-sm text-[#616161] hover:bg-gray-100">View Details</a>
                                                @if($itinerary->user_id === Auth::id())
                                                    <button class="block w-full text-left px-4 py-2 text-sm text-[#616161] hover:bg-gray-100">Edit</button>
                                                    <button class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Delete</button>
                                                @else
                                                    <button class="block w-full text-left px-4 py-2 text-sm text-[#616161] hover:bg-gray-100">Report</button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Content -->
                                <div class="mb-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-sm font-medium text-[#0043EF]">Itinerary Route</span>
                                        @if($region)
                                            <span class="text-sm text-[#616161]">•</span>
                                            <span class="text-sm text-[#616161]">{{ $region }}</span>
                                        @endif
                                    </div>
                                    <h4 class="font-semibold text-[#1B1B1B] mb-2">{{ $routeTitle }}</h4>
                                    @if($durationDays > 0)
                                        <div class="flex items-center gap-2 mb-2">
                                            <svg class="w-4 h-4 text-[#616161]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-sm text-[#616161]">{{ $durationDays }} {{ $durationDays === 1 ? 'Day' : 'Days' }}</span>
                                        </div>
                                    @endif
                                    <div x-data="{ expanded: false }">
                                        <p class="text-sm text-[#616161]" :class="expanded ? '' : 'line-clamp-3'">{{ $routeDescription }}</p>
                                        @if(strlen($routeDescription) > 150)
                                            <button @click="expanded = !expanded" class="text-sm text-[#0043EF] hover:underline mt-1">
                                                <span x-show="!expanded">...more</span>
                                                <span x-show="expanded">...less</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Card Footer -->
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <div class="flex items-center gap-4 text-[#616161]">
                                        <button class="flex items-center gap-1 hover:text-[#0043EF] transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                            </svg>
                                            <span class="text-xs">Save</span>
                                        </button>
                                        <div x-data="{ showShare: false }" class="relative">
                                            <button @click="showShare = !showShare" class="flex items-center gap-1 hover:text-[#0043EF] transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                                </svg>
                                                <span class="text-xs">Share</span>
                                            </button>
                                            <div x-show="showShare" @click.away="showShare = false" x-transition class="absolute bottom-full mb-2 right-0 w-56 bg-white rounded-md shadow-lg border border-gray-200 z-10 p-3">
                                                <div class="space-y-2">
                                                    <button @click="navigator.clipboard.writeText('{{ $itineraryUrl }}'); showShare = false; alert('Link copied!')" class="w-full text-left px-3 py-2 text-sm text-[#616161] hover:bg-gray-100 rounded">Copy Link</button>
                                                    <button class="w-full text-left px-3 py-2 text-sm text-[#616161] hover:bg-gray-100 rounded">Share on Facebook</button>
                                                    <button class="w-full text-left px-3 py-2 text-sm text-[#616161] hover:bg-gray-100 rounded">Share on Twitter</button>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ $itineraryUrl }}" class="flex items-center gap-1 hover:text-[#0043EF] transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span class="text-xs">View</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-8">
                                <p class="text-[#616161]">No itineraries yet. Create your first itinerary to see it here!</p>
                            </div>
                        @endforelse
                    </div>
                    @if($totalItineraries > 4 && !$showAllItineraries)
                        <div class="mt-6 text-center">
                            <button wire:click="$set('showAllItineraries', true)" class="text-sm text-[#0043EF] hover:underline font-medium">Show all →</button>
                        </div>
                    @elseif($totalItineraries > 4 && $showAllItineraries)
                        <div class="mt-6 text-center">
                            <button wire:click="$set('showAllItineraries', false)" class="text-sm text-[#0043EF] hover:underline font-medium">Show less ↑</button>
                        </div>
                    @endif
                @endif
                @endif
            </section>

            <!-- Career Profile -->
            <section class="bg-white p-5 rounded-xl space-y-4">
                <div class="flex items-center justify-between">
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
                    <button wire:click="$set('showCareerProfile', {{ $showCareerProfile ? 'false' : 'true' }})" class="p-1.5 rounded-md hover:bg-gray-100 transition-colors" title="{{ $showCareerProfile ? 'Hide' : 'Show' }}">
                        @if($showCareerProfile)
                            <svg class="w-5 h-5 text-[#616161] hover:text-[#0043EF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-[#616161] hover:text-[#0043EF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        @endif
                    </button>
                </div>
                @if($showCareerProfile)
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
                @endif
            </section>

            <!-- Career History -->
            <section class="bg-white p-5 rounded-xl space-y-4">
                <div class="flex gap-4 justify-between">
                    <div class="flex items-center gap-2 mb-5">
                        <h2 class="text-xl font-semibold text-[#1B1B1B]">Career History</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('career-history.manage') }}" class="cursor-pointer h-fit flex items-center gap-[12px] bg-[#F5F6FA] px-4 py-2 rounded-lg text-sm text-[#0043EF] hover:bg-gray-200 font-medium">
                            <img src="{{ asset('images/add-circle-blue.svg') }}" alt="" class="h-[11px] w-[11px]">
                            Add Career History
                        </a>
                        <button wire:click="$set('showCareerHistory', {{ $showCareerHistory ? 'false' : 'true' }})" class="p-1.5 rounded-md hover:bg-gray-100 transition-colors" title="{{ $showCareerHistory ? 'Hide' : 'Show' }}">
                            @if($showCareerHistory)
                                <svg class="w-5 h-5 text-[#616161] hover:text-[#0043EF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-[#616161] hover:text-[#0043EF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            @endif
                        </button>
                    </div>
                </div>
                @if($showCareerHistory)
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
                @endif
            </section>

            <!-- Certifications -->
            <section class="bg-white p-5 rounded-xl space-y-4">
                <div class="flex gap-4 justify-between">
                    <div class="flex items-center gap-2 mb-5">
                        <h2 class="text-xl font-semibold text-[#1B1B1B]">Certifications</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="openCertificationModal()" class="cursor-pointer h-fit flex items-center gap-[12px] bg-[#F5F6FA] px-4 py-2 rounded-lg text-sm text-[#0043EF] hover:bg-gray-200 font-medium">
                            <img src="{{ asset('images/add-circle-blue.svg') }}" alt="" class="h-[11px] w-[11px]">
                            {{ $showCertificationModal ? 'Cancel' : 'Add Certifications' }}
                        </button>
                        <button wire:click="$set('showCertifications', {{ $showCertifications ? 'false' : 'true' }})" class="p-1.5 rounded-md hover:bg-gray-100 transition-colors" title="{{ $showCertifications ? 'Hide' : 'Show' }}">
                            @if($showCertifications)
                                <svg class="w-5 h-5 text-[#616161] hover:text-[#0043EF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-[#616161] hover:text-[#0043EF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            @endif
                        </button>
                    </div>
                </div>
                @if($showCertifications)
                
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
                @endif
            </section>

            <!-- Skills -->
            <section class="bg-white p-5 rounded-xl">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-2">
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
                    <button wire:click="$set('showSkills', {{ $showSkills ? 'false' : 'true' }})" class="p-1.5 rounded-md hover:bg-gray-100 transition-colors" title="{{ $showSkills ? 'Hide' : 'Show' }}">
                        @if($showSkills)
                            <svg class="w-5 h-5 text-[#616161] hover:text-[#0043EF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-[#616161] hover:text-[#0043EF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        @endif
                    </button>
                </div>
                @if($showSkills)
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
                @endif
            </section>

            <!-- Personal Details -->
            <section class="bg-white p-5 rounded-xl space-y-4">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-2">
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
                    <button wire:click="$set('showPersonalDetails', {{ $showPersonalDetails ? 'false' : 'true' }})" class="p-1.5 rounded-md hover:bg-gray-100 transition-colors" title="{{ $showPersonalDetails ? 'Hide' : 'Show' }}">
                        @if($showPersonalDetails)
                            <svg class="w-5 h-5 text-[#616161] hover:text-[#0043EF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-[#616161] hover:text-[#0043EF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        @endif
                    </button>
                </div>
                @if($showPersonalDetails)
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
                    @endif
        </div>
        </div>
    </div>

    <!-- Image Modal/Lightbox -->
    <div x-show="imageModal.open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="closeImage()"
         @keydown.escape.window="closeImage()"
         class="fixed inset-0 bg-black bg-opacity-90 z-[9999] flex items-center justify-center p-4"
         x-cloak>
        <div class="relative max-w-7xl max-h-full w-full h-full flex items-center justify-center">
            <!-- Close Button -->
            <button @click="closeImage()" 
                    class="absolute top-4 right-4 z-10 bg-white/20 hover:bg-white/30 text-white rounded-full p-2 transition-colors backdrop-blur-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            
            <!-- Image -->
            <img :src="imageModal.src" 
                 :alt="imageModal.alt"
                 class="max-w-full max-h-full object-contain rounded-lg shadow-2xl"
                 @click.self="closeImage()">
            
            <!-- Image Info (optional) -->
            <div x-show="imageModal.alt" 
                 class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black/50 text-white px-4 py-2 rounded-lg backdrop-blur-sm">
                <p class="text-sm" x-text="imageModal.alt"></p>
            </div>
        </div>
    </div>
</div>
