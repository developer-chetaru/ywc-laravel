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

    <!-- Tailwind CSS (browser version) -->
    <script src="https://cdn.tailwindcss.com"></script>

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
            class="flex-1 transition-all duration-300"
            :class="{ 'ml-72': $store.sidebar?.isOpen && window.innerWidth >= 768, 'ml-16': !$store.sidebar?.isOpen && window.innerWidth >= 768 }"
            x-data>
            <div>
                <nav class="bg-white border-b border-gray-100 flex items-center justify-between px-4 py-3 relative z-10">

                    {{-- Right Side Navigation --}}
                    <div class="flex items-center space-x-4 ml-auto">
                        
                        {{-- 🌐 Language Switcher --}}
                        <div>
                            <livewire:language-switcher />
                        </div>
                
                        {{-- Right Side Navigation (Profile Dropdown) --}}
                        <div x-data="{ open: false }" class="relative ml-auto">
                            <!-- Trigger: Profile Picture + Arrow -->
                            <div @click="open = !open" class="flex items-center space-x-2">
                                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos() && Auth::user()->profile_photo_path)
                                    <img src="{{ Auth::user()->profile_photo_url }}"
                                        alt="{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}"
                                        class="w-8 h-8 rounded-full object-cover border cursor-pointer">
                                @else
                                    <svg class="w-8 h-8 text-gray-600 cursor-pointer" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.67 0 8 1.34 8 4v2H4v-2c0-2.66 5.33-4 8-4z"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                @endif

                                <!-- User name (not clickable, no pointer, not selectable) -->
                                <span class="text-sm font-medium text-gray-800 select-none cursor-default">
                                    {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                                </span>

                                <!-- Arrow (clickable) -->
                                <svg @click="open = !open"
                                    class="w-4 h-4 text-gray-600 cursor-pointer"
                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                </svg>
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
                @yield('content')
            </main>





        </div>
    </div>

    @stack('modals')
    @livewireScripts
</body>
</html>

