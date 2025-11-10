@php
    use Spatie\Permission\Models\Role;

    // Get all roles except 'user' if you want, or just exclude super_admin
    $nonAdminRoles = Role::where('name', '!=', 'super_admin')->pluck('name')->toArray();

    // Add super_admin manually to the list
    $rolesToShow = array_merge($nonAdminRoles, ['super_admin']);
@endphp


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
    </style>

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex-1 flex flex-col overflow-hidden">

    <x-banner />

    <div class="flex min-h-screen bg-gray-100">

        {{-- Sidebar --}}
        <div>
            <div x-data="{ isOpen: true }" 
                class="h-screen bg-[#0066FF] text-white flex flex-col transition-all duration-300"
                :class="isOpen ? 'w-72' : 'w-16'">
                <div class="flex items-center justify-between p-4 py-5 border-b border-blue-300 relative">
                    <div x-show="isOpen" x-transition class="flex items-center space-x-2">
                        <img src="/images/ywc-logo-white.svg" alt="Logo" class="w-8 h-8">
                        <span class="text-white font-semibold text-[10px] uppercase tracking-wide mt-2">
                            Yacht Workers Council
                        </span>
                    </div>
                    <div class="relative">
                        <template x-if="isOpen">
                            <button @click="isOpen = !isOpen" class=" top-0 left-0 text-white z-10">
                                <span class="text-xl"><img src="{{ asset('images/right-icon.svg') }}" alt="">
                                </span>
                            </button>
                        </template>
                        <template x-if="!isOpen">
                            <button @click="isOpen = !isOpen" class="text-white cursor-pointer text-xl">
                                ☰
                            </button>
                        </template>
                    </div>
                </div>
                <ul class="space-y-1 px-2 mt-2">
                    @hasanyrole($rolesToShow)
                        <li>
                            <a href="/dashboard"
                            class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                            {{ request()->is('dashboard') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                            
                                <img 
                                    src="{{ request()->is('dashboard') ? '/images/dashboard-black.svg' : '/images/dashbaord-white.svg' }}" 
                                    alt="Dashboard" 
                                    class="w-5 h-5"
                                >
                                
                                <span x-show="isOpen" 
                                    class="text-base font-medium {{ request()->is('dashboard') ? 'text-black' : 'text-white' }}">
                                    Dashboard
                                </span>
                            </a>
                        </li>
                    @endhasanyrole

                    @role('super_admin')
                        <li>
                            <a href="{{ route('users.index') }}" 
                            class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                            {{ request()->is('users') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                                
                                <img src="{{ request()->is('users') ? '/images/user-group-list-active.svg' : '/images/user-group-list-defalt.svg' }}" 
                                    alt="Users" class="w-5 h-5">
                                
                                <span x-show="isOpen" 
                                    class="text-base font-medium {{ request()->is('users') ? 'text-black' : 'text-white' }}">
                                    User list
                                </span>
                            </a>
                        </li>
                    @endrole

                    @hasanyrole($rolesToShow)
                    <li>
                        <a href="/career-history"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                            {{ request()->is('career-history') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                            <img 
                                src="{{ request()->is('career-history') ? '/images/career.svg' : '/images/manage-dashbaord.svg' }}" 
                                alt="Documents & Career History" 
                                class="w-5 h-5"
                            >
                            <span x-show="isOpen" 
                                class="text-base font-medium {{ request()->is('career-history') ? 'text-black' : 'text-white' }}">
                                Documents & Career History
                            </span>
                        </a>
                    </li>
                    @endhasanyrole
                  
                  	@role('super_admin')
                        <li>
                            <a href="{{ route('certificate-types.index') }}" 
                            class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                            {{ request()->is('certificate-types*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">

                                <img src="{{ request()->is('certificate-types*') ? '/images/certificate-type-active.svg' : '/images/certificate-type-default.svg' }}" 
                                    alt="Certificate Types" class="w-5 h-5">

                                <span x-show="isOpen" 
                                    class="text-base font-medium {{ request()->is('certificate-types*') ? 'text-black' : 'text-white' }}">
                                    Certificate Types
                                </span>
                            </a>
                        </li>
                    @endrole

                    @role('super_admin')
                        <li>
                            <a href="{{ route('certificate.issuers.index') }}" 
                            class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                            {{ request()->routeIs('certificate.issuers.*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">

                                <img src="{{ request()->routeIs('certificate.issuers.*') ? '/images/certificate-issuer-active.svg' : '/images/certificate-issuer-default.svg' }}" 
                                    alt="Certificate Issuer" class="w-5 h-5">

                                <span x-show="isOpen" 
                                    class="text-base font-medium {{ request()->routeIs('certificate.issuers.*') ? 'text-black' : 'text-white' }}">
                                    Certificate Issuer
                                </span>
                            </a>
                        </li>
                    @endrole

                    @hasanyrole($rolesToShow)
                    <li>
                        <a href="{{ route('legal-support.index') }}"
                            class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                                {{ request()->is('legal-support*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                            
                            <img src="{{ request()->is('legal-support*') ? '/images/justice-scale-01.svg' : '/images/justice-scale-white.svg' }}" 
                                alt="Legal Support" class="w-5 h-5">
                            
                            <span x-show="isOpen" 
                                class="text-base font-medium {{ request()->is('legal-support*') ? 'text-black' : 'text-white' }}">
                                Legal Support
                            </span>
                        </a>
                    </li>
                    @endhasanyrole
                  
                  	@hasanyrole($nonAdminRoles)
                        <li>
                            <a href="{{ route('training.resources') }}"
                                class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                                    {{ request()->is('training-resources*') ? 'bg-[#F2F2F2] text-black' : 'hover:bg-white/10 text-white' }}">

                                <img 
                                    src="{{ request()->is('training-resources*') 
                                        ? '/images/training-and-resources-active.svg' 
                                        : '/images/training-and-resources-default.svg' }}" 
                                    alt="Training & Resources" 
                                    class="w-5 h-5">

                                <span 
                                    x-show="isOpen" 
                                    class="text-base font-medium {{ request()->is('training-resources*') ? 'text-black' : 'text-white' }}">
                                    Training & Resources
                                </span>
                            </a>
                        </li>
                    @endhasanyrole                 	

                    @role($nonAdminRoles)
                    <li>
                        <div class="flex items-center space-x-3 px-4 py-3 rounded-lg transition opacity-50 cursor-not-allowed">
                            <img src="/images/brain-white.svg" alt="Mental Health Support" class="w-5 h-5">
                            <span x-show="isOpen" class="text-base font-medium">Mental Health Support</span>
                        </div>
                    </li>
                    @endrole


                    @hasanyrole($rolesToShow)
                    <li>
                        <a href="/forum" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                        {{ request()->is('forum') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                            <img 
                                src="{{ request()->is('forum') ? '/images/message-multiple-01 (1).svg' : '/images/message-multiple-white.svg' }}" 
                                alt="Department Forums" 
                                class="w-5 h-5"
                            >
                            <span x-show="isOpen" class="text-base font-medium">Department Forums</span>
                        </a>
                    </li>
                    @endhasanyrole

                    @role($nonAdminRoles)
                    <li>
                        <div class="flex items-center space-x-3 px-4 py-3 rounded-lg transition opacity-50 cursor-not-allowed">
                            <img src="/images/save-money-dollar-white.svg" alt="Financial Future Planning" class="w-5 h-5">
                            <span x-show="isOpen" class="text-base font-medium">Financial Future Planning</span>
                        </div>
                    </li>
                    @endrole

                    @role($nonAdminRoles)
                    <li>
                        <div class="flex items-center space-x-3 px-4 py-3 rounded-lg transition opacity-50 cursor-not-allowed">
                            <img src="/images/money-bag-white.svg" alt="Pension & Investment Advice" class="w-5 h-5">
                            <span x-show="isOpen" class="text-base font-medium">Pension & Investment Advice</span>
                        </div>
                    </li>
                    @endrole

                    @hasanyrole($rolesToShow)
                    <li>
                        <a href="{{ route('industryreview.index') }}"
                            class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                                {{ request()->is('industry-review*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">

                            <img 
                                src="{{ request()->is('industry-review*') ? '/images/industry-review-active.svg' : '/images/industry-review-default.svg' }}" 
                                alt="Industry Review System" 
                                class="w-5 h-5">

                            <span x-show="isOpen" 
                                class="text-base font-medium {{ request()->is('industry-review*') ? 'text-black' : 'text-white' }}">
                                Industry Review System
                            </span>
                        </a>
                    </li>
                    @endhasanyrole

                    @hasanyrole($rolesToShow)
                    <li>
                        <div class="flex items-center space-x-3 px-4 py-3 rounded-lg transition opacity-50 cursor-not-allowed">
                            <img src="/images/itinerarySystemWhite.svg" alt="Itinerary System" class="w-5 h-5">
                            <span x-show="isOpen" class="text-base font-medium">Itinerary System</span>
                        </div>
                    </li>
                    @endhasanyrole

					@role($rolesToShow)
                    <li>
                      <a href="{{ route('roles.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                                                                  {{ request()->routeIs('roles.index') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">

                        <img 
                             src="{{ request()->routeIs('roles.index') ? '/images/user-role-active.svg' : '/images/user-role-default.svg' }}" 
                             alt="User Roles" 
                             class="w-5 h-5"
                             >

                        <span x-show="isOpen" 
                              class="text-base font-medium {{ request()->routeIs('roles.index') ? 'text-black' : 'text-white' }}">
                          User Roles
                        </span>
                      </a>
                    </li>
                    @endrole

					@hasanyrole($rolesToShow)
                    <li>
                      <a href="{{ route('marketplace.index') }}"
                         class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                                {{ request()->is('marketplace*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">

                        <img 
                             src="{{ request()->is('marketplace*') ? '/images/market-place-active-icon.svg' : '/images/market-place-default-icon.svg' }}" 
                             alt="Marketplace" 
                             class="w-5 h-5">

                        <span x-show="isOpen" 
                              class="text-base font-medium {{ request()->is('marketplace*') ? 'text-black' : 'text-white' }}">
                          Market Place
                        </span>
                      </a>
                    </li>
                    @endhasanyrole


                    @hasanyrole($rolesToShow)
                    <li>
                      <a href="{{ route('worklog.index') }}"
                         class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                                {{ request()->is('work-log*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">

                        <img 
                             src="{{ request()->is('work-log*') ? '/images/work-log-active.svg' : '/images/work-log-defult.svg' }}" 
                             alt="Work Log" 
                             class="w-5 h-5">

                        <span x-show="isOpen" 
                              class="text-base font-medium {{ request()->is('work-log*') ? 'text-black' : 'text-white' }}">
                          Work Log
                        </span>
                      </a>
                    </li>
                    @endhasanyrole
					
                </ul>
            </div>
        </div>

        {{-- Content --}}
        <div class="flex-1 transition-all duration-300">
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

