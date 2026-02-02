@php
use Spatie\Permission\Models\Role;
// Get all roles except super_admin
$nonAdminRoles = Role::where('name', '!=', 'super_admin')->pluck('name')->toArray();
@endphp

{{-- Mobile Overlay --}}
<div 
    x-show="$store.sidebar?.isOpen && window.innerWidth < 768"
    @click="$store.sidebar.isOpen = false"
    x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 md:hidden"
    x-cloak
    x-init="
        $watch('$store.sidebar.isOpen', (val) => {
            if (val && window.innerWidth < 768) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });
    "
></div>

<div
    x-data="{ 
        isOpen: window.innerWidth >= 768, 
        isMobile: window.innerWidth < 768,
        industryReviewOpen: {{ request()->is('industry-review*') ? 'true' : 'false' }},
        itineraryOpen: {{ request()->is('itinerary*') ? 'true' : 'false' }},
        crewDiscoveryOpen: {{ request()->is('crew-discovery*') || request()->is('connections*') || request()->is('rallies*') ? 'true' : 'false' }},
        documentsCareerOpen: {{ request()->is('documents*') || request()->is('career-history*') || request()->is('share-templates-new*') || request()->is('ocr/accuracy*') || request()->is('verification/appeals*') ? 'true' : 'false' }},
        trainingOpen: {{ request()->is('training*') ? 'true' : 'false' }},
        financialPlanningOpen: {{ request()->is('financial-planning*') || request()->is('pension-investment-advice*') ? 'true' : 'false' }}
    }"
    x-init="
        // Wait for Alpine to be ready
        $nextTick(() => {
            // Initialize store if it doesn't exist
            if (!$store.sidebar) {
                $store.sidebar = { isOpen: isOpen };
            } else {
                // Sync local state with store
                isOpen = $store.sidebar.isOpen;
            }
            
            // Watch for store changes and update local state
            $watch('$store.sidebar.isOpen', (val) => {
                isOpen = val;
            });
            
            // Watch local state and update store
            $watch('isOpen', (val) => {
                if ($store.sidebar) {
                    $store.sidebar.isOpen = val;
                }
            });
        });
        
        // Handle window resize
        window.addEventListener('resize', () => {
            const wasMobile = isMobile;
            isMobile = window.innerWidth < 768;
            if (window.innerWidth >= 768) {
                isOpen = true;
            } else if (!wasMobile && isMobile) {
                isOpen = false;
            }
        });
    "
    class="h-screen bg-[#0066FF] text-white flex flex-col transition-all duration-300 z-50 fixed inset-y-0 left-0 group"
    :class="{
        'w-72': isOpen && !isMobile,
        'w-16': !isOpen && !isMobile,
        'translate-x-0 w-72': isMobile && isOpen,
        '-translate-x-full': isMobile && !isOpen
    }"
    @click.stop
    style="will-change: transform, width;">
    <!-- Header -->
    <div class="flex items-center justify-between p-4 py-5 border-b border-blue-300 flex-shrink-0" :class="{ 'justify-center': !isOpen && !isMobile }">
        <div x-show="isOpen && !isMobile" x-transition class="flex items-center space-x-2">
            <img src="/images/ywc-logo-white.svg" alt="Logo" class="w-8 h-8">
            <span class="text-white font-semibold text-[10px] uppercase tracking-wide mt-2">
                Yacht Workers Council
            </span>
        </div>
        <div class="flex items-center">
            <!-- Mobile close button (always visible on mobile when open) -->
            <button 
                x-show="isMobile && isOpen"
                @click="isOpen = false"
                class="text-white cursor-pointer p-2 hover:bg-white/10 rounded transition-colors"
                aria-label="Close sidebar">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <!-- Desktop toggle buttons -->
            <template x-if="!isMobile">
                <template x-if="isOpen">
                    <button @click="isOpen = !isOpen" class="text-white z-10 p-2 hover:bg-white/10 rounded transition-colors" title="Hide Sidebar">
                        <img src="{{ asset('images/right-icon.svg') }}" alt="Hide Sidebar">
                    </button>
                </template>
                <template x-if="!isOpen">
                    <button @click.stop="isOpen = !isOpen" class="text-white cursor-pointer p-2 hover:bg-white/10 rounded transition-colors flex items-center justify-center" title="Show Sidebar">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </template>
            </template>
        </div>
    </div>

    <!-- Reopen Button (visible when collapsed, always visible for easy access) -->
    <div x-show="!isOpen && !isMobile" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-x-0"
         x-transition:enter-end="opacity-100 translate-x-0"
         class="absolute right-0 top-20 transform translate-x-full bg-[#0066FF] hover:bg-[#0052CC] rounded-r-lg p-3 shadow-lg cursor-pointer transition-all duration-200 z-50 border-l-2 border-blue-400"
         @click.stop="isOpen = true"
         title="Show Sidebar">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </div>

    <!-- Scrollable Navigation -->
    <div class="flex-1 overflow-y-auto min-h-0 sidebar-scrollable" style="scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.3) transparent;">
        <ul class="space-y-1 px-2 mt-2 pb-6">

            {{-- DASHBOARD --}}
            @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
            <li>
                <a href="{{ route('main-dashboard') }}"
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


            {{-- EMPLOYER DASHBOARD --}}
            @role('employer')
            <li>
                <a href="{{ route('employer.dashboard') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                        {{ request()->is('employer*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                    <i class="fas fa-users {{ request()->is('employer*') ? 'text-black' : 'text-white' }}"></i>
                    <span x-show="isOpen" class="text-base font-medium {{ request()->is('employer*') ? 'text-black' : 'text-white' }}">
                        Employer Dashboard
                    </span>
                </a>
            </li>
            @endrole

            {{-- RECRUITMENT AGENCY DASHBOARD --}}
            @role('recruitment_agency')
            <li>
                <a href="{{ route('agency.dashboard') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                        {{ request()->is('agency*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                    <i class="fas fa-briefcase {{ request()->is('agency*') ? 'text-black' : 'text-white' }}"></i>
                    <span x-show="isOpen" class="text-base font-medium {{ request()->is('agency*') ? 'text-black' : 'text-white' }}">
                        Agency Dashboard
                    </span>
                </a>
            </li>
            @endrole

            {{-- ANALYTICS --}}
            <li>
                <a href="{{ route('analytics.user-dashboard') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                        {{ request()->is('analytics*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                    <i class="fas fa-chart-bar {{ request()->is('analytics*') ? 'text-black' : 'text-white' }}"></i>
                    <span x-show="isOpen" class="text-base font-medium {{ request()->is('analytics*') ? 'text-black' : 'text-white' }}">
                        Analytics
                    </span>
                </a>
            </li>


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

            {{-- WAITLIST (Super Admin Only) --}}
            @role('super_admin')
            <li>
                <a href="{{ route('admin.waitlist') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                        {{ request()->is('admin/waitlist*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">

                    <img
                        src="{{ request()->is('admin/waitlist*') ? '/images/user-group-list-active.svg' : '/images/user-group-list-defalt.svg' }}"
                        alt="Waitlist" class="w-5 h-5">

                    <span x-show="isOpen"
                        class="text-base font-medium {{ request()->is('admin/waitlist*') ? 'text-black' : 'text-white' }}">
                        Waitlist
                    </span>
                </a>
            </li>
            @endrole

            {{-- SUBSCRIPTION ADMIN (Super Admin Only) --}}
            @role('super_admin')
            <li>
                <a href="{{ route('admin.subscriptions') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                        {{ request()->is('admin/subscriptions*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>

                    <span x-show="isOpen"
                        class="text-base font-medium {{ request()->is('admin/subscriptions*') ? 'text-black' : 'text-white' }}">
                        Subscription Admin
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
                                {{ request()->is('documents*') && !request()->is('career-history*') && !request()->is('share-templates-new*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="text-sm font-medium">Documents</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('career-history.manage') }}"
                                class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                {{ request()->is('career-history*') && !request()->is('career-history/documents*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm font-medium">Career History</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('share-templates-new.index') }}"
                                class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                {{ request()->is('share-templates-new*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"></path>
                                </svg>
                                <span class="text-sm font-medium">Share Templates</span>
                            </a>
                        </li>
                        
                        {{-- My Appeals --}}
                        <li>
                            <a href="{{ route('verification.appeals.my-appeals') }}"
                                class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                {{ request()->is('verification/appeals/my-appeals') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                <i class="fas fa-gavel w-4 h-4"></i>
                                <span class="text-sm font-medium">My Appeals</span>
                            </a>
                        </li>
                        
                        {{-- OCR Accuracy (Admin Only) --}}
                        @role('super_admin|admin')
                        <li>
                            <a href="{{ route('ocr.accuracy.index') }}"
                                class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                {{ request()->is('ocr/accuracy*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                <i class="fas fa-search w-4 h-4"></i>
                                <span class="text-sm font-medium">OCR Accuracy</span>
                            </a>
                        </li>
                        @endrole
                        
                        {{-- Appeals Management (Admin Only) --}}
                        @role('super_admin|admin|verifier')
                        <li>
                            <a href="{{ route('verification.appeals.index') }}"
                                class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                {{ request()->is('verification/appeals') && !request()->is('verification/appeals/my-appeals') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                <i class="fas fa-tasks w-4 h-4"></i>
                                <span class="text-sm font-medium">Appeals Management</span>
                            </a>
                        </li>
                        @endrole
                        @hasanyrole('super_admin|admin')
                        <li>
                            <a href="{{ route('admin.documents.approval') }}"
                                class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                {{ request()->is('admin/documents/approval*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm font-medium">Document Approval</span>
                            </a>
                        </li>
                        @endhasanyrole
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
            @role('super_admin')
            {{-- Admin Menu for Training --}}
            <li>
                <button @click="trainingOpen = !trainingOpen"
                    class="flex items-center justify-between w-full px-4 py-3 rounded-lg transition
                            {{ request()->is('training*') ? 'bg-[#F2F2F2] text-black' : 'hover:bg-white/10 text-white' }}">
                    <div class="flex items-center space-x-3">
                        <img
                            src="{{ request()->is('training*') 
                                    ? '/images/training-and-resources-active.svg' 
                                    : '/images/training-and-resources-default.svg' }}"
                            alt="Training & Resources"
                            class="w-5 h-5">
                        <span x-show="isOpen" class="text-base font-medium {{ request()->is('training*') ? 'text-black' : 'text-white' }}">
                            Training & Resources
                        </span>
                    </div>
                    <svg x-show="isOpen" :class="{'rotate-180': trainingOpen}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="trainingOpen && isOpen" x-collapse class="ml-4 mt-1 space-y-1">
                    <a href="{{ route('training.courses') }}"
                        class="block px-4 py-2 rounded text-sm {{ request()->is('training/courses') && !request()->is('training/admin*') ? 'bg-blue-100 text-blue-800' : 'text-gray-300 hover:text-white' }}">
                        Browse Courses
                    </a>
                    <a href="{{ route('training.admin.dashboard') }}"
                        class="block px-4 py-2 rounded text-sm {{ request()->is('training/admin') ? 'bg-blue-100 text-blue-800' : 'text-gray-300 hover:text-white' }}">
                        Admin Dashboard
                    </a>
                    <a href="{{ route('training.admin.certifications') }}"
                        class="block px-4 py-2 rounded text-sm {{ request()->is('training/admin/certifications') ? 'bg-blue-100 text-blue-800' : 'text-gray-300 hover:text-white' }}">
                        Manage Certifications
                    </a>
                    <a href="{{ route('training.admin.providers') }}"
                        class="block px-4 py-2 rounded text-sm {{ request()->is('training/admin/providers') ? 'bg-blue-100 text-blue-800' : 'text-gray-300 hover:text-white' }}">
                        Manage Providers
                    </a>
                    <a href="{{ route('training.admin.courses') }}"
                        class="block px-4 py-2 rounded text-sm {{ request()->is('training/admin/courses') ? 'bg-blue-100 text-blue-800' : 'text-gray-300 hover:text-white' }}">
                        Manage Courses
                    </a>
                    <a href="{{ route('training.admin.reviews') }}"
                        class="block px-4 py-2 rounded text-sm {{ request()->is('training/admin/reviews') ? 'bg-blue-100 text-blue-800' : 'text-gray-300 hover:text-white' }}">
                        Manage Reviews
                    </a>
                </div>
            </li>
            @else
            {{-- Regular User Menu --}}
            <li>
                <a href="{{ route('training.resources') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                            {{ request()->is('training-resources*') || request()->is('training/courses*') ? 'bg-[#F2F2F2] text-black' : 'hover:bg-white/10 text-white' }}">

                    <img
                        src="{{ request()->is('training-resources*') || request()->is('training/courses*')
                                ? '/images/training-and-resources-active.svg' 
                                : '/images/training-and-resources-default.svg' }}"
                        alt="Training & Resources"
                        class="w-5 h-5">

                    <span
                        x-show="isOpen"
                        class="text-base font-medium {{ request()->is('training-resources*') || request()->is('training/courses*') ? 'text-black' : 'text-white' }}">
                        Training & Resources
                    </span>
                </a>
            </li>
            @endrole

            {{-- MENTAL HEALTH: Show admin menu for super_admin, regular menu for others --}}
            @role('super_admin')
            <li>
                <a href="{{ route('mental-health.admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                    {{ request()->is('mental-health*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                    <img src="{{ request()->is('mental-health*') ? '/images/brain-02.svg' : '/images/brain-white.svg' }}" alt="Mental Health Admin" class="w-5 h-5">
                    <span x-show="isOpen" class="text-base font-medium {{ request()->is('mental-health*') ? 'text-black' : 'text-white' }}">Mental Health Admin</span>
                </a>
            </li>
            @else
            @hasanyrole(implode('|', $nonAdminRoles))
            <li>
                <a href="{{ route('mental-health.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                    {{ request()->is('mental-health*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                    <img src="{{ request()->is('mental-health*') ? '/images/brain-02.svg' : '/images/brain-white.svg' }}" alt="Mental Health Support" class="w-5 h-5">
                    <span x-show="isOpen" class="text-base font-medium {{ request()->is('mental-health*') ? 'text-black' : 'text-white' }}">Mental Health Support</span>
                </a>
            </li>
            @endhasanyrole
            @endrole


            @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
            <li>
                <a href="/forum" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                    {{ request()->is('forum') && !request()->is('forum/moderator*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                    <img
                        src="{{ request()->is('forum') && !request()->is('forum/moderator*') ? '/images/message-multiple-01 (1).svg' : '/images/message-multiple-white.svg' }}"
                        alt="Department Forums"
                        class="w-5 h-5">
                    <span x-show="isOpen" class="text-base font-medium">Department Forums</span>
                </a>
            </li>
            @endhasanyrole

            {{-- MODERATOR DASHBOARD (Super Admin Only) --}}
            @role('super_admin')
            <li>
                <a href="{{ route('forum.moderator.dashboard') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                    {{ request()->is('forum/moderator*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span x-show="isOpen"
                        class="text-base font-medium {{ request()->is('forum/moderator*') ? 'text-black' : 'text-white' }}">
                        Moderator Dashboard
                    </span>
                </a>
            </li>
            @endrole

            {{-- PRIVATE MESSAGES --}}
            @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
            <li>
                <a href="{{ route('forum.messages.index') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                    {{ request()->is('forum/messages*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span x-show="isOpen"
                        class="text-base font-medium {{ request()->is('forum/messages*') ? 'text-black' : 'text-white' }}">
                        Messages
                        @php
                            $messageService = app(\App\Services\Forum\PrivateMessageService::class);
                            $unreadCount = auth()->check() ? $messageService->getUnreadCount(auth()->user()) : 0;
                        @endphp
                        @if($unreadCount > 0)
                            <span class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                        @endif
                    </span>
                </a>
            </li>
            @endhasanyrole

            @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
            {{-- FINANCIAL FUTURE PLANNING --}}
            <li>
                <button @click="financialPlanningOpen = !financialPlanningOpen"
                    class="flex items-center justify-between w-full px-4 py-3 rounded-lg transition
                            {{ request()->is('financial-planning*') || request()->is('pension-investment-advice*') ? 'bg-[#F2F2F2] text-black' : 'hover:bg-white/10 text-white' }}">
                    <div class="flex items-center space-x-3">
                        <img src="{{ request()->is('financial-planning*') || request()->is('pension-investment-advice*') ? '/images/save-money-dollar.svg' : '/images/save-money-dollar-white.svg' }}" alt="Financial Future Planning" class="w-5 h-5">
                        <span x-show="isOpen" class="text-base font-medium {{ request()->is('financial-planning*') || request()->is('pension-investment-advice*') ? 'text-black' : 'text-white' }}">
                            Financial Future Planning
                        </span>
                    </div>
                    <svg x-show="isOpen" :class="{'rotate-180': financialPlanningOpen}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="financialPlanningOpen && isOpen" x-collapse class="ml-4 mt-1 space-y-1">
                    <a href="{{ url('/financial-planning/dashboard') }}"
                        class="block px-4 py-2 rounded text-sm {{ request()->is('financial-planning/dashboard') ? 'bg-blue-100 text-blue-800' : 'text-gray-300 hover:text-white' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('pension-investment-advice') }}"
                        class="block px-4 py-2 rounded text-sm {{ request()->is('pension-investment-advice*') ? 'bg-blue-100 text-blue-800' : 'text-gray-300 hover:text-white' }}">
                        Pension & Investment
                    </a>
                </div>
            </li>
            @endhasanyrole

            {{-- FINANCIAL PLANNING ADMIN (Super Admin Only) --}}
            @php
                $user = auth()->user();
                $isAdmin = $user && ($user->hasRole('super_admin', 'api') || $user->hasRole('super_admin'));
            @endphp
            @if($isAdmin)
            <li>
                <a href="{{ url('/financial-planning/admin') }}" 
                   @click.prevent.stop="window.location.href = '{{ url('/financial-planning/admin') }}'"
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                    {{ request()->is('financial-planning/admin*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span x-show="isOpen" class="text-base font-medium {{ request()->is('financial-planning/admin*') ? 'text-black' : 'text-white' }}">Financial Admin</span>
                </a>
            </li>
            @endif

            @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
            {{-- INDUSTRY REVIEW SYSTEM --}}
            <li>
                @role('super_admin')
                    {{-- SUPER ADMIN: WITH SUBMENU --}}
                    <div>
                        <div class="w-full flex items-center justify-between space-x-3 px-4 py-3 rounded-lg transition cursor-pointer
                            {{ request()->is('industry-review*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}"
                            @click.stop="industryReviewOpen = !industryReviewOpen">
                            <div class="flex items-center space-x-3 flex-1">
                                <img
                                    src="{{ request()->is('industry-review*') ? '/images/industry-review-active.svg' : '/images/industry-review-default.svg' }}"
                                    alt="Industry Review System"
                                    class="w-5 h-5">
                                <span x-show="isOpen"
                                    class="text-base font-medium {{ request()->is('industry-review*') ? 'text-black' : 'text-white' }}">
                                    Industry Review System
                                </span>
                            </div>
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
                                <a href="{{ route('yacht-reviews.index') }}"
                                    class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                    {{ request()->routeIs('yacht-reviews.index') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Yacht Reviews</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('marina-reviews.index') }}"
                                    class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                    {{ request()->routeIs('marina-reviews.index') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Marina Reviews</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('contractor-reviews.index') }}"
                                    class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                    {{ request()->routeIs('contractor-reviews.index') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Contractor Reviews</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('broker-reviews.index') }}"
                                    class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                    {{ request()->routeIs('broker-reviews.index') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Broker Reviews</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('restaurant-reviews.index') }}"
                                    class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                    {{ request()->routeIs('restaurant-reviews.index') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Restaurant Reviews</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('industryreview.yachts.manage') }}"
                                    class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                    {{ request()->is('industry-review/yachts*') && request()->routeIs('industryreview.yachts.*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
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
                                    {{ request()->routeIs('industryreview.contractors.*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Manage Contractors</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('industryreview.brokers.manage') }}"
                                    class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                    {{ request()->routeIs('industryreview.brokers.*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Manage Brokers</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('industryreview.restaurants.manage') }}"
                                    class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                    {{ request()->routeIs('industryreview.restaurants.*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Manage Restaurants</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                @else
                    @role('Captain')
                        {{-- CAPTAIN: WITH YACHT MANAGEMENT SUBMENU --}}
                        <div>
                            <div class="w-full flex items-center justify-between space-x-3 px-4 py-3 rounded-lg transition cursor-pointer
                                {{ request()->is('industry-review*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}"
                                @click.stop="industryReviewOpen = !industryReviewOpen">
                                <div class="flex items-center space-x-3 flex-1">
                                    <img
                                        src="{{ request()->is('industry-review*') ? '/images/industry-review-active.svg' : '/images/industry-review-default.svg' }}"
                                        alt="Industry Review System"
                                        class="w-5 h-5">
                                    <span x-show="isOpen"
                                        class="text-base font-medium {{ request()->is('industry-review*') ? 'text-black' : 'text-white' }}">
                                        Industry Review System
                                    </span>
                                </div>
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
                                    <a href="{{ route('yacht-reviews.index') }}"
                                        class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                        {{ request()->routeIs('yacht-reviews.index') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span class="text-sm font-medium">Yacht Reviews</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('marina-reviews.index') }}"
                                        class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                        {{ request()->routeIs('marina-reviews.index') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="text-sm font-medium">Marina Reviews</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('contractor-reviews.index') }}"
                                        class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                        {{ request()->routeIs('contractor-reviews.index') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="text-sm font-medium">Contractor Reviews</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('broker-reviews.index') }}"
                                        class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                        {{ request()->routeIs('broker-reviews.index') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <span class="text-sm font-medium">Broker Reviews</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('restaurant-reviews.index') }}"
                                        class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                        {{ request()->routeIs('restaurant-reviews.index') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                        </svg>
                                        <span class="text-sm font-medium">Restaurant Reviews</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('industryreview.yachts.manage') }}"
                                        class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm
                                        {{ request()->is('industry-review/yachts*') && request()->routeIs('industryreview.yachts.*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/80' }}">
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

            {{-- JOB BOARD --}}
            @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
            <li>
                <a href="{{ route('job-board.index') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                            {{ request()->is('job-board*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 7h16M4 12h16M4 17h16M7 7v10M12 7v10M17 7v10" />
                    </svg>

                    <span x-show="isOpen"
                        class="text-base font-medium {{ request()->is('job-board*') ? 'text-black' : 'text-white' }}">
                        Job Board
                    </span>
                </a>
            </li>
            @endhasanyrole

            {{-- WORK SCHEDULES --}}
            @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
            <li>
                <a href="{{ route('work-schedules.index') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                            {{ request()->is('work-schedules*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>

                    <span x-show="isOpen"
                        class="text-base font-medium {{ request()->is('work-schedules*') ? 'text-black' : 'text-white' }}">
                        Work Schedules
                    </span>
                </a>
            </li>
            @endhasanyrole

            {{-- CAPTAIN DASHBOARD --}}
            @hasanyrole('super_admin|captain')
            <li>
                <a href="{{ route('captain-dashboard.index') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                            {{ request()->is('captain-dashboard*') ? 'bg-white text-black' : 'hover:bg-white/10 text-white' }}">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>

                    <span x-show="isOpen"
                        class="text-base font-medium {{ request()->is('captain-dashboard*') ? 'text-black' : 'text-white' }}">
                        Captain Dashboard
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