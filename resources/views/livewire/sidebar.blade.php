
@php
    use Spatie\Permission\Models\Role;
    // Get all roles except super_admin
    $nonAdminRoles = Role::where('name', '!=', 'super_admin')->pluck('name')->toArray();
@endphp


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
                    <span class="text-xl">         <img src="{{ asset('images/right-icon.svg') }}" alt="">
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
        @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
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

        @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
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


         @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
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

         @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
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

         @hasanyrole('super_admin|' . implode('|', $nonAdminRoles))
        <li>
            <div class="flex items-center space-x-3 px-4 py-3 rounded-lg transition opacity-50 cursor-not-allowed">
                <img src="/images/itinerarySystemWhite.svg" alt="Itinerary System" class="w-5 h-5">
                <span x-show="isOpen" class="text-base font-medium">Itinerary System</span>
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

		@role('super_admin')
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
    </ul>
</div>
