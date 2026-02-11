<nav class="bg-white border-b border-gray-100 flex items-center justify-between px-4 py-3 relative z-10">

    {{-- Mobile Menu Button (Left Side) --}}
    <button 
        x-data="{ 
            isMobile: window.innerWidth < 768,
            toggleSidebar() {
                if (this.$store && this.$store.sidebar) {
                    this.$store.sidebar.isOpen = !this.$store.sidebar.isOpen;
                } else if (window.Alpine && window.Alpine.store('sidebar')) {
                    window.Alpine.store('sidebar').isOpen = !window.Alpine.store('sidebar').isOpen;
                }
            }
        }"
        x-init="
            window.addEventListener('resize', () => isMobile = window.innerWidth < 768);
            // Ensure store exists
            $nextTick(() => {
                if (!$store.sidebar) {
                    $store.sidebar = { isOpen: false };
                }
            });
        "
        @click="toggleSidebar()"
        x-show="isMobile"
        class="md:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 mr-3 z-50 relative"
        aria-label="Toggle sidebar"
        type="button">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    {{-- Right Side Navigation --}}
    <div class="flex items-center space-x-4 ml-auto">
        
        {{-- 🌐 Language Switcher --}}
        <!-- <div>
            <livewire:language-switcher />
        </div> -->
        
        {{-- 🔔 Forum Notifications Bell --}}
        @auth
            @if(request()->is('forum*'))
                <livewire:forum.notification-bell />
            @endif
        @endauth
   
        {{-- Right Side Navigation (Profile Dropdown or Login) --}}
        @auth
            <div x-data="{ 
                open: false,
                photoUrl: '{{ Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : '' }}',
                firstName: '{{ Auth::user()->first_name }}',
                lastName: '{{ Auth::user()->last_name }}',
                initials: '{{ strtoupper(substr(Auth::user()->first_name ?? '', 0, 1) . substr(Auth::user()->last_name ?? '', 0, 1)) }}'
            }" class="relative ml-auto">
                <!-- Trigger: Profile Picture + Arrow -->
                <div @click="open = !open" class="flex items-center space-x-2">
                    <!-- Profile Picture -->
                    <div class="relative">
                        <img x-show="photoUrl" 
                             :src="photoUrl"
                             :alt="firstName + ' ' + lastName"
                             class="w-8 h-8 rounded-full object-cover border cursor-pointer"
                             @profile-photo-updated.window="photoUrl = $event.detail.photo_url">
                        <div x-show="!photoUrl" 
                             class="absolute top-0 left-0 w-8 h-8 rounded-full bg-[#EBF4FF] flex items-center justify-center text-xs font-semibold text-[#0043EF] cursor-pointer"
                             x-text="initials"
                             @profile-photo-updated.window="photoUrl = $event.detail.photo_url">
                        </div>
                    </div>

                    <!-- User name (not clickable, no pointer, not selectable) -->
                    <span class="text-sm font-medium text-gray-800 select-none cursor-default"
                          x-text="firstName && lastName ? firstName + ' ' + lastName : '{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}'"
                          @profile-updated.window="
                            firstName = $event.detail.first_name || '{{ Auth::user()->first_name }}';
                            lastName = $event.detail.last_name || '{{ Auth::user()->last_name }}';
                            initials = (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
                          ">
                        {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                    </span>
                </div>

                <!-- Dropdown -->
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-md py-2 z-50">

                    <!-- Profile -->
                    <a href="{{ route('profile') }}"
                    class="flex items-center px-4 py-2 hover:bg-gray-100 space-x-2">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                            stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.5 21a8.25 8.25 0 0115 0"/>
                        </svg>
                        <span class="text-sm text-gray-800">Profile</span>
                    </a>

                    <!-- Change Password -->
                    <a href="{{ route('profile.password') }}"
                    class="flex items-center px-4 py-2 hover:bg-gray-100 space-x-2">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                            stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 15v2m0 4h.01M21 12.3V10a9 9 0 10-18 0v2.3a2.25 2.25 0 01-1.5 2.122V17.25A2.25 2.25 0 003.75 19.5h16.5a2.25 2.25 0 002.25-2.25v-2.828a2.25 2.25 0 01-1.5-2.122z"/>
                        </svg>
                        <span class="text-sm text-gray-800">Change Password</span>
                    </a>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left flex items-center px-4 py-2 hover:bg-gray-100 space-x-2">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0013.5 3H4.5A2.25 2.25 0 002.25 5.25v13.5A2.25 2.25 0 004.5 21h9a2.25 2.25 0 002.25-2.25V15M18.75 15l3-3m0 0l-3-3m3 3H9"/>
                            </svg>
                            <span class="text-sm text-gray-800">Log Out</span>
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="h-[2px] bg-blue-500 w-full mt-2"></div>
                </div>
            </div>
        @else
            {{-- Guest User: Show Login/Register Links --}}
            <div class="flex items-center space-x-3">
                <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900 font-medium">
                    Log in
                </a>
                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    Sign up
                </a>
            </div>
        @endauth
    </div>
</nav>
