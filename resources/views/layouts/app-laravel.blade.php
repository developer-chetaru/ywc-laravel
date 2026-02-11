<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-icon.png') }}">

    <!-- Tailwind CSS - Using Vite build instead of CDN for production -->
    @if(config('app.debug'))
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <!-- Custom Styles -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,600;1,400;1,500;1,600&display=swap');
        body { font-family: 'DM Sans', sans-serif; }

        .main-nav-left ul li { margin-bottom: 10px; }
        .main-nav-left ul li.active a { background-color: #F5F6FA; color: #0053FF; }
        
        /* Sidebar scrollbar styles */
        .sidebar-scrollable::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar-scrollable::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scrollable::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        .sidebar-scrollable::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
        
        /* Alpine.js x-cloak */
        [x-cloak] { display: none !important; }
    </style>

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex-1 flex flex-col overflow-hidden">

    <x-banner />

    <div class="flex h-screen bg-gray-100 overflow-hidden">

        {{-- Sidebar --}}
        @include('livewire.sidebar')

        {{-- Content --}}
        <div
            class="flex-1 transition-all duration-300 overflow-x-hidden ml-72"
            :class="{ 'ml-72': $store.sidebar?.isOpen && window.innerWidth >= 768, 'ml-16': !$store.sidebar?.isOpen && window.innerWidth >= 768 }"
            x-data>
            <div>
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
                
                        {{-- Right Side Navigation (Profile Dropdown) --}}
                        <div x-data="{ 
                            open: false,
                            photoUrl: '{{ Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : '' }}',
                            firstName: '{{ Auth::user()->first_name ?? '' }}',
                            lastName: '{{ Auth::user()->last_name ?? '' }}',
                            initials: '{{ strtoupper(substr(Auth::user()->first_name ?? '', 0, 1) . substr(Auth::user()->last_name ?? '', 0, 1)) }}',
                            updateName(data) {
                                if (data && data.first_name) this.firstName = data.first_name;
                                if (data && data.last_name) this.lastName = data.last_name;
                                if (this.firstName && this.lastName) {
                                    this.initials = (this.firstName.charAt(0) + this.lastName.charAt(0)).toUpperCase();
                                }
                            },
                            updatePhoto(data) {
                                if (data && data.photo_url) {
                                    // Force update with full URL
                                    this.photoUrl = data.photo_url;
                                    // Force image reload by updating src attribute
                                    this.$nextTick(() => {
                                        const img = this.$el.querySelector('img');
                                        if (img) {
                                            img.src = data.photo_url;
                                            img.style.display = 'block';
                                        }
                                    });
                                }
                            }
                        }" 
                        x-init="
                            // Listen for Livewire events
                            Livewire.on('profile-updated', (data) => {
                                updateName(data);
                            });
                            Livewire.on('profile-photo-updated', (data) => {
                                console.log('Livewire event received:', data);
                                updatePhoto(data);
                            });
                            
                            // Also listen for window events (fallback)
                            window.addEventListener('profile-updated', (e) => {
                                if (e.detail) updateName(e.detail);
                            });
                            window.addEventListener('profile-photo-updated', (e) => {
                                console.log('Window event received:', e.detail);
                                if (e.detail) updatePhoto(e.detail);
                            });
                        "
                        class="relative ml-auto">
                            <!-- Trigger: Profile Picture -->
                            <div @click="open = !open" class="flex items-center space-x-2 cursor-pointer">
                                <!-- Profile Picture -->
                                <div class="relative">
                                    <img x-show="photoUrl && photoUrl !== ''" 
                                         :src="photoUrl"
                                         :alt="(firstName || '') + ' ' + (lastName || '')"
                                         class="w-8 h-8 rounded-full object-cover border"
                                         x-on:error="photoUrl = ''"
                                         x-on:profile-photo-updated.window="updatePhoto($event.detail)"
                                         x-on:load="console.log('Image loaded:', photoUrl)">
                                    <div x-show="!photoUrl || photoUrl === ''" 
                                         class="w-8 h-8 rounded-full bg-[#EBF4FF] flex items-center justify-center text-xs font-semibold text-[#0043EF]"
                                         x-text="initials"
                                         x-on:profile-photo-updated.window="updatePhoto($event.detail)">
                                    </div>
                                </div>

                                <!-- User name (not clickable, no pointer, not selectable) -->
                                <span class="text-sm font-medium text-gray-800 select-none cursor-default" 
                                      x-text="(firstName && lastName) ? (firstName + ' ' + lastName) : '{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}'"
                                      x-on:profile-updated.window="updateName($event.detail)">
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
                    </div>
                </nav>

            </div>

            @if (isset($header))
                <header class="bg-white shadow">
                    <div>
                        {{ $header }}
                    </div>
                </header>
            @endif

            @php
                // No spacing only for Change Password page
                $mainClasses = request()->routeIs('password.change')
                    ? 'flex-1 overflow-y-auto !p-0 !m-0'
                    : 'flex-1 overflow-y-auto p-6';
            @endphp

           <main class="{{ $mainClasses }} h-[calc(100vh-74px)]" style="padding: 0;">
                {{ $slot ?? '' }}
                @yield('content')
            </main>





        </div>
    </div>

    @stack('modals')
    @livewireScripts
    @stack('scripts')
    
    <script>
        // Initialize Alpine store for sidebar - ensure it's available early
        if (typeof Alpine !== 'undefined') {
            document.addEventListener('alpine:init', () => {
                Alpine.store('sidebar', {
                    isOpen: window.innerWidth >= 768,
                    toggle() {
                        this.isOpen = !this.isOpen;
                    }
                });
            });
        } else {
            // Fallback: wait for Alpine to load
            document.addEventListener('DOMContentLoaded', () => {
                if (window.Alpine) {
                    window.Alpine.store('sidebar', {
                        isOpen: window.innerWidth >= 768,
                        toggle() {
                            this.isOpen = !this.isOpen;
                        }
                    });
                }
            });
        }
        
        // Listen for Livewire events globally for header updates
        document.addEventListener('livewire:init', () => {
            // Listen for Livewire events and forward to window events
            Livewire.on('profile-updated', (data) => {
                console.log('Global listener - profile-updated:', data);
                window.dispatchEvent(new CustomEvent('profile-updated', { detail: data }));
            });
            Livewire.on('profile-photo-updated', (data) => {
                console.log('Global listener - profile-photo-updated:', data);
                window.dispatchEvent(new CustomEvent('profile-photo-updated', { detail: data }));
            });
        });
        
        // Also listen after Livewire loads (fallback)
        if (window.Livewire) {
            Livewire.on('profile-updated', (data) => {
                window.dispatchEvent(new CustomEvent('profile-updated', { detail: data }));
            });
            Livewire.on('profile-photo-updated', (data) => {
                window.dispatchEvent(new CustomEvent('profile-photo-updated', { detail: data }));
            });
        }
    </script>
</body>
</html>

