@php
use Spatie\Permission\Models\Role;
// Get all roles except super_admin
$nonAdminRoles = Role::where('name', '!=', 'super_admin')->pluck('name')->toArray();
@endphp

<div
    x-data="{ 
        isOpen: true, 
        isMobile: window.innerWidth < 768,
        industryReviewOpen: {{ request()->is('industry-review*') ? 'true' : 'false' }},
        itineraryOpen: {{ request()->is('itinerary*') ? 'true' : 'false' }},
        crewDiscoveryOpen: {{ request()->is('crew-discovery*') || request()->is('connections*') || request()->is('rallies*') ? 'true' : 'false' }},
        documentsCareerOpen: {{ request()->is('documents*') || request()->is('career-history*') ? 'true' : 'false' }}
    }"
    x-init="
        window.addEventListener('resize', () => isMobile = window.innerWidth < 768);
        $store.sidebar = { isOpen };
        $watch('isOpen', val => $store.sidebar.isOpen = val);
    "
    class="h-screen bg-[#0066FF] text-white flex flex-col transition-all duration-300 z-50 fixed inset-y-0 left-0"
    :class="{
        'w-72': isOpen && !isMobile,
        'w-16': !isOpen && !isMobile,
        'translate-x-0 w-72': isMobile && isOpen,
        '-translate-x-full': isMobile && !isOpen
    }"
    style="will-change: transform, width;">
    <!-- Header -->
    <div class="flex items-center justify-between p-4 py-5 border-b border-blue-300 flex-shrink-0">
        <div x-show="isOpen && !isMobile" x-transition class="flex items-center space-x-2">
            <img src="/images/ywc-logo-white.svg" alt="Logo" class="w-8 h-8">
            <span class="text-white font-semibold text-[10px] uppercase tracking-wide mt-2">
                Yacht Workers Council
            </span>
        </div>
        <div>
            <template x-if="isOpen">
                <button @click="isOpen = !isOpen" class="text-white z-10">
                    <img src="{{ asset('images/right-icon.svg') }}" alt="">
                </button>
            </template>
            <template x-if="!isOpen">
                <button @click="isOpen = !isOpen" class="text-white cursor-pointer text-xl">
                    ☰
                </button>
            </template>
        </div>
    </div>

    <!-- Scrollable Navigation -->
    <div class="flex-1 overflow-y-auto min-h-0 sidebar-scrollable" style="scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.3) transparent;">
        <ul class="space-y-1 px-2 mt-2 pb-6">

            {{-- DASHBOARD --}}
            @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
            <li>
                <a href="/dashboard"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                        {{ request()->is('dashboard') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">

                    <img
                        src="{{ request()->is('dashboard') ? '/images/dashboard-black.svg' : '/images/dashbaord-white.svg' }}"
                        alt="Dashboard"
                        class="w-5 h-5">
                    <span x-show="isOpen"
                        class="text-base font-medium {{ request()->is('dashboard') ? 'text-black' : 'text-white' }}">
                        Dashboard
                    </span>
                </a>
            </li>
            @endhasanyrole


            {{-- USER LIST (Super Admin Only) --}}
            @role('super_admin')
            <li>
                <a href="{{ route('users.index') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                        {{ request()->is('users') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">

                    <img
                        src="{{ request()->is('users') ? '/images/user-group-list-active.svg' : '/images/user-group-list-defalt.svg' }}"
                        alt="Users" class="w-5 h-5">

                    <span x-show="isOpen"
                        class="text-base font-medium {{ request()->is('users') ? 'text-black' : 'text-white' }}">
                        User list
                    </span>
                </a>
            </li>
            @endrole


            {{-- DOCUMENTS & CAREER HISTORY --}}
            @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
            <li>
                <div>
                    <div class="w-full flex items-center justify-between space-x-3 px-4 py-3 rounded-lg transition
                        {{ request()->is('documents*') || request()->is('career-history*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                        <a href="{{ route('documents') }}" 
                            class="flex items-center space-x-3 flex-1"
                            @click.stop>
                            <img
                                src="{{ request()->is('documents*') || request()->is('career-history*') ? '/images/career.svg' : '/images/manage-dashbaord.svg' }}"
                                alt="Documents & Career History"
                                class="w-5 h-5">
                            <span x-show="isOpen"
                                class="text-base font-medium {{ request()->is('documents*') || request()->is('career-history*') ? 'text-black' : 'text-white' }}">
                                Documents & Career History
                            </span>
                        </a>
                        <button @click.stop="documentsCareerOpen = !documentsCareerOpen" 
                            x-show="isOpen"
                            class="ml-2 p-1 hover:bg-white/10 rounded transition-colors">
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': documentsCareerOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                    
                    {{-- SUBMENU --}}
                    <ul x-show="documentsCareerOpen && isOpen" x-collapse class="ml-4 mt-1 space-y-1">
                        <li>
                            <a href="{{ route('documents') }}"
                                class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                {{ request()->is('documents*') && !request()->is('career-history*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="text-sm font-medium">Documents</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('career-history') }}"
                                class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                {{ request()->is('career-history*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm font-medium">Career History</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endhasanyrole


            {{-- CERTIFICATE TYPES (Super Admin Only) --}}
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


            {{-- CERTIFICATE ISSUERS (Super Admin Only) --}}
            @role('super_admin')
            <li>
                <a href="{{ route('certificate.issuers.index') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                    {{ request()->routeIs('certificate.issuers.*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">

                    <img src="{{ request()->routeIs('certificate.issuers.*') 
                                    ? asset('images/certificate-issuer-active.svg') 
                                    : asset('images/certificate-issuer-default.svg') }}"
                        alt="Certificate Issuer" class="w-5 h-5">

                    <span x-show="isOpen"
                        class="text-base font-medium {{ request()->routeIs('certificate.issuers.*') ? 'text-black' : 'text-white' }}">
                        Certificate Issuer
                    </span>
                </a>
            </li>
            @endrole

            {{-- MASTER DATA (Super Admin Only) --}}
            @role('super_admin')
            <li>
                <a href="{{ route('master-data.index') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                    {{ request()->routeIs('master-data.*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                    </svg>

                    <span x-show="isOpen"
                        class="text-base font-medium {{ request()->routeIs('master-data.*') ? 'text-black' : 'text-white' }}">
                        Master Data
                    </span>
                </a>
            </li>
            @endrole


            {{-- LEGAL SUPPORT --}}
            @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
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


            {{-- TRAINING & RESOURCES --}}
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

            @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
            <li>
                <a href="{{ route('mental-health') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                    {{ request()->is('mental-health*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                    <img src="{{ request()->is('mental-health*') ? '/images/brain-02.svg' : '/images/brain-white.svg' }}" alt="Mental Health Support" class="w-5 h-5">
                    <span x-show="isOpen" class="text-base font-medium {{ request()->is('mental-health*') ? 'text-black' : 'text-white' }}">Mental Health Support</span>
                </a>
            </li>
            @endhasanyrole


            @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
            <li>
                <a href="/forum" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                    {{ request()->is('forum') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                    <img
                        src="{{ request()->is('forum') ? '/images/message-multiple-01 (1).svg' : '/images/message-multiple-white.svg' }}"
                        alt="Department Forums"
                        class="w-5 h-5">
                    <span x-show="isOpen" class="text-base font-medium">Department Forums</span>
                </a>
            </li>
            @endhasanyrole

            @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
            <li>
                <a href="{{ route('financial-future-planning') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                    {{ request()->is('financial-future-planning*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                    <img src="{{ request()->is('financial-future-planning*') ? '/images/save-money-dollar.svg' : '/images/save-money-dollar-white.svg' }}" alt="Financial Future Planning" class="w-5 h-5">
                    <span x-show="isOpen" class="text-base font-medium {{ request()->is('financial-future-planning*') ? 'text-black' : 'text-white' }}">Financial Future Planning</span>
                </a>
            </li>
            @endhasanyrole

            @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
            <li>
                <a href="{{ route('pension-investment-advice') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                    {{ request()->is('pension-investment-advice*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                    <img src="{{ request()->is('pension-investment-advice*') ? '/images/money-bag-ywc.svg' : '/images/money-bag-white.svg' }}" alt="Pension & Investment Advice" class="w-5 h-5">
                    <span x-show="isOpen" class="text-base font-medium {{ request()->is('pension-investment-advice*') ? 'text-black' : 'text-white' }}">Pension & Investment Advice</span>
                </a>
            </li>
            @endhasanyrole

            @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
            {{-- INDUSTRY REVIEW SYSTEM --}}
            <li>
                @role('super_admin')
                    {{-- SUPER ADMIN: WITH SUBMENU --}}
                    <div>
                        <div class="w-full flex items-center justify-between space-x-3 px-4 py-3 rounded-lg transition
                            {{ request()->is('industry-review*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                            <a href="{{ route('industryreview.index') }}" 
                                class="flex items-center space-x-3 flex-1"
                                @click.stop>
                                <img
                                    src="{{ request()->is('industry-review*') ? '/images/industry-review-active.svg' : '/images/industry-review-default.svg' }}"
                                    alt="Industry Review System"
                                    class="w-5 h-5">
                                <span x-show="isOpen"
                                    class="text-base font-medium {{ request()->is('industry-review*') ? 'text-black' : 'text-white' }}">
                                    Industry Review System
                                </span>
                            </a>
                            <button @click.stop="industryReviewOpen = !industryReviewOpen" 
                                x-show="isOpen"
                                class="ml-2 p-1 hover:bg-white/10 rounded transition-colors">
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': industryReviewOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                        
                        {{-- SUBMENU --}}
                        <ul x-show="industryReviewOpen && isOpen" x-collapse class="ml-4 mt-1 space-y-1">
                            <li>
                                <a href="{{ route('industryreview.index') }}"
                                    class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                    {{ request()->is('industry-review') && !request()->is('industry-review/yachts*') && !request()->is('industry-review/marinas*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                    <span class="text-sm font-medium">View All</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('industryreview.yachts.manage') }}"
                                    class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                    {{ request()->is('industry-review/yachts*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Manage Yachts</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('industryreview.marinas.manage') }}"
                                    class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                    {{ request()->is('industry-review/marinas*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Manage Marinas</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('industryreview.contractors.manage') }}"
                                    class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                    {{ request()->is('industry-review/contractors*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Contractors</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('industryreview.brokers.manage') }}"
                                    class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                    {{ request()->is('industry-review/brokers*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Brokers</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('industryreview.restaurants.manage') }}"
                                    class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                    {{ request()->is('industry-review/restaurants*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Restaurants & Services</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                @else
                    @role('Captain')
                        {{-- CAPTAIN: WITH YACHT MANAGEMENT SUBMENU --}}
                        <div>
                            <div class="w-full flex items-center justify-between space-x-3 px-4 py-3 rounded-lg transition
                                {{ request()->is('industry-review*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                                <a href="{{ route('industryreview.index') }}" 
                                    class="flex items-center space-x-3 flex-1"
                                    @click.stop>
                                    <img
                                        src="{{ request()->is('industry-review*') ? '/images/industry-review-active.svg' : '/images/industry-review-default.svg' }}"
                                        alt="Industry Review System"
                                        class="w-5 h-5">
                                    <span x-show="isOpen"
                                        class="text-base font-medium {{ request()->is('industry-review*') ? 'text-black' : 'text-white' }}">
                                        Industry Review System
                                    </span>
                                </a>
                                <button @click.stop="industryReviewOpen = !industryReviewOpen" 
                                    x-show="isOpen"
                                    class="ml-2 p-1 hover:bg-white/10 rounded transition-colors">
                                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': industryReviewOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            {{-- SUBMENU FOR CAPTAINS --}}
                            <ul x-show="industryReviewOpen && isOpen" x-collapse class="ml-4 mt-1 space-y-1">
                                <li>
                                    <a href="{{ route('industryreview.index') }}"
                                        class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                        {{ request()->is('industry-review') && !request()->is('industry-review/yachts*') && !request()->is('industry-review/marinas*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                        <span class="text-sm font-medium">View All</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('industryreview.yachts.manage') }}"
                                        class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                        {{ request()->is('industry-review/yachts*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span class="text-sm font-medium">Manage Yachts</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @else
                        {{-- REGULAR USER: SIMPLE LINK (NO DROPDOWN) --}}
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
                    @endrole
                @endrole
            </li>
            @endhasanyrole

            @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
            <!-- <li>
                <a href="{{ route('itinerary.index') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                                {{ request()->is('itinerary') && !request()->is('itinerary/routes*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">

                    <img
                        src="{{ request()->is('itinerary') && !request()->is('itinerary/routes*') ? '/images/itinerarySystemWhite.svg' : '/images/itinerarySystemWhite.svg' }}"
                        alt="Itinerary"
                        class="w-5 h-5">

                    <span x-show="isOpen"
                        class="text-base font-medium {{ request()->is('itinerary') && !request()->is('itinerary/routes*') ? 'text-black' : 'text-white' }}">
                        Itinerary System
                    </span>
                </a>
            </li> -->
            
            {{-- ITINERARY SYSTEM WITH SUBMENU --}}
            <li>
                <div>
                    <button @click="itineraryOpen = !itineraryOpen"
                        class="w-full flex items-center justify-between space-x-3 px-4 py-3 rounded-lg transition
                        {{ request()->is('itinerary*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                        <div class="flex items-center space-x-3">
                            <img
                                src="{{ request()->is('itinerary*') ? '/images/itinerarySystemWhite.svg' : '/images/itinerarySystemWhite.svg' }}"
                                alt="Itinerary System"
                                class="w-5 h-5">
                            <span x-show="isOpen"
                                class="text-base font-medium {{ request()->is('itinerary*') ? 'text-black' : 'text-white' }}">
                                Itinerary System
                            </span>
                        </div>
                        <svg x-show="isOpen" class="w-4 h-4 transition-transform" :class="{ 'rotate-180': itineraryOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    {{-- SUBMENU --}}
                    <ul x-show="itineraryOpen && isOpen" x-collapse class="ml-4 mt-1 space-y-1">
                        <li>
                            <a href="{{ route('itinerary.routes.index') }}"
                                class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                {{ request()->routeIs('itinerary.routes.index') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                <span class="text-sm font-medium">Itinerary System Library</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('itinerary.routes.planner') }}"
                                class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                {{ request()->routeIs('itinerary.routes.planner') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                <span class="text-sm font-medium">Itinerary Planner</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endhasanyrole

            @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
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


            {{-- WORK LOG --}}
            @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
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

            {{-- CREW DISCOVERY & NETWORKING --}}
            @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
            <li>
                <div>
                    <button @click="crewDiscoveryOpen = !crewDiscoveryOpen"
                        class="w-full flex items-center justify-between space-x-3 px-4 py-3 rounded-lg transition
                        {{ request()->is('crew-discovery*') || request()->is('connections*') || request()->is('rallies*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span x-show="isOpen"
                                class="text-base font-medium {{ request()->is('crew-discovery*') || request()->is('connections*') || request()->is('rallies*') ? 'text-black' : 'text-white' }}">
                                Crew Discovery
                            </span>
                        </div>
                        <svg x-show="isOpen" class="w-4 h-4 transition-transform" :class="{ 'rotate-180': crewDiscoveryOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    {{-- SUBMENU --}}
                    <ul x-show="crewDiscoveryOpen && isOpen" x-collapse class="ml-4 mt-1 space-y-1">
                        <li>
                            <a href="/crew-discovery"
                                class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                {{ request()->is('crew-discovery*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <span class="text-sm font-medium">Discover Crew</span>
                            </a>
                        </li>
                        <li>
                            <a href="/connections"
                                class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                {{ request()->is('connections*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span class="text-sm font-medium">My Connections</span>
                            </a>
                        </li>
                        <li>
                            <a href="/rallies"
                                class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                {{ request()->is('rallies*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm font-medium">Rallies & Events</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endhasanyrole

            {{-- USER ROLES (Super Admin Only) --}}
            @role('super_admin')
            <li>
                <a href="{{ route('roles.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                    {{ request()->routeIs('roles.index') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">

                    <img
                        src="{{ request()->routeIs('roles.index') ? '/images/user-role-active.svg' : '/images/user-role-default.svg' }}"
                        alt="User Roles"
                        class="w-5 h-5">

                    <span x-show="isOpen"
                        class="text-base font-medium {{ request()->routeIs('roles.index') ? 'text-black' : 'text-white' }}">
                        User Roles
                    </span>
                </a>
            </li>
            @endrole

        </ul>
    </div>
</div>