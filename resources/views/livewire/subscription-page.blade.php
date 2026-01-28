<main class="flex-1 min-h-0 w-full flex gap-x-[18px] px-2 pt-3" style="padding-bottom: 0px;" wire:init="loadSubscription">
        <!-- Sidebar -->
        @php
            $currentRoute = Route::currentRouteName();
        @endphp

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
            </div>

            <!-- Settings Content -->
            <div class="flex-1 overflow-y-auto p-5 max-[1750px]:p-4 [scrollbar-width:none]">
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
        
        @unlessrole('super_admin')
        <div class="flex-1 min-w-0 overflow-y-auto flex flex-col gap-[11px] [scrollbar-width:none] h-full">
            <div class="bg-white p-5 rounded-lg shadow-md">
                <h2 class="text-xl border-b border-gray-100 font-medium text-[#0053FF] pb-2">Subscription</h2>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert" x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                    <script>
                        // Refresh subscription after success message
                        setTimeout(function() {
                            @this.call('refreshSubscription');
                        }, 500);
                    </script>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <div>
                    @if(session('failed'))
                        <div 
                            x-data="{ show: true }"
                            x-init="setTimeout(() => show = false, 5000)"
                            x-show="show"
                            class="fixed inset-0 flex items-center justify-center z-50"
                        >
                            <div 
                                class="bg-white border-2 border-red-500 rounded-2xl shadow-lg p-6 max-w-sm w-full text-center"
                                x-transition
                            >
                                <!-- Failed Image -->
                                <img src="{{ asset('images/failed.png') }}" alt="Failed" class="w-24 h-24 mx-auto mb-4">

                                <!-- Failed Title -->
                                <h2 class="text-2xl font-bold text-red-600">Payment Failed</h2>

                                <!-- Failed Subtitle -->
                                <p class="text-gray-600 mt-2">
                                    It seems we have not received money and process is not correct
                                </p>

                                <!-- Try Again Button -->
                                <div class="mt-5">
                                    <a href="{{ route('subscription.page') }}" class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                        Try Again
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>


                <div class="overflow-hidden p-5 mt-6 bg-[#F5F6FA]" style="border-top:8px solid #0053FF; border-radius:1.5rem;">

                    @php
                        // Check if account is suspended (highest priority)
                        $isSuspended = session('account_suspended') || ($subscription && $subscription->status === 'suspended');
                        
                        // Simple check - if subscription exists and status is active, show it
                        // Only show active if NOT suspended
                        $hasActiveSubscription = !$isSuspended 
                            && $subscription 
                            && $subscription->status === 'active';
                    @endphp
                    
                    {{-- SUSPENDED SCREEN - Show this first if account is suspended --}}
                    @if($isSuspended)
                        <div class="bg-white rounded-lg p-8 text-center border-2 border-red-500 shadow-lg">
                            <div class="mb-6">
                                <svg class="w-24 h-24 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            
                            <h2 class="text-3xl font-bold text-red-600 mb-4">Account Suspended</h2>
                            
                            <p class="text-lg text-gray-700 mb-2">
                                Your account has been suspended due to failed payments.
                            </p>
                            
                            <p class="text-md text-gray-600 mb-6">
                                To restore access to all features, please update your payment method below.
                            </p>
                            
                            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 text-left max-w-2xl mx-auto">
                                <div class="flex">
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700">
                                            <strong>What happened?</strong> Your subscription payment failed multiple times. Your account has been suspended and you cannot access any features until payment is resolved.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-8">
                                <h3 class="text-xl font-semibold text-gray-800 mb-4">Update Payment Method</h3>
                                
                                {{-- Plan Selection for Reactivation --}}
                                <div class="flex gap-4 mb-6 justify-center">
                                    <div class="bg-white rounded-md p-5 border-2 {{ $selectedPlan === 'monthly' ? 'border-[#0053FF]' : 'border-gray-200' }} max-w-[300px] cursor-pointer"
                                         wire:click="$set('selectedPlan', 'monthly')">
                                        <h3 class="text-xl font-semibold mb-2">Monthly Plan</h3>
                                        <h2 class="text-[28px] text-[#0053FF] font-semibold mb-2">£10.00<span class="text-sm text-gray-600">/month</span></h2>
                                        <p class="text-sm text-gray-600">Billed monthly</p>
                                    </div>
                                    
                                    <div class="bg-white rounded-md p-5 border-2 {{ $selectedPlan === 'annual' ? 'border-[#0053FF]' : 'border-gray-200' }} max-w-[300px] cursor-pointer"
                                         wire:click="$set('selectedPlan', 'annual')">
                                        <h3 class="text-xl font-semibold mb-2">Annual Plan</h3>
                                        <h2 class="text-[28px] text-[#0053FF] font-semibold mb-2">£71.88<span class="text-sm text-gray-600">/year</span></h2>
                                        <p class="text-sm text-green-600 font-medium">Save £48.12 (40% off)</p>
                                        <p class="text-xs text-gray-600 mt-1">£5.99/month equivalent</p>
                                    </div>
                                </div>
                                
                                <div x-data="{ loading: false }">
                                    <button 
                                        wire:click="checkout('{{ $selectedPlan }}')"
                                        wire:loading.attr="disabled"
                                        @click="loading = true"
                                        class="bg-[#0053FF] text-white py-3 px-8 flex items-center justify-center text-[16px] border rounded-md mt-3 relative mx-auto"
                                    >
                                        <span x-show="!loading">Reactivate Subscription - {{ $selectedPlan === 'annual' ? 'Annual Plan' : 'Monthly Plan' }}</span>
                                        <span x-show="loading" class="flex items-center space-x-2">
                                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                            </svg>
                                            <span>Processing...</span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @elseif(!$hasActiveSubscription)
                        {{-- No Active Subscription --}}
                        <h3 class="text-[#1B1B1B] text-[24px] mb-2 font-semibold">Full Access Membership</h3>
                        <h4 class="text-[#616161] text-[16px] mb-2">Pay securely using Stripe</h4>
                        
                        {{-- Payment Warning for Past Due --}}
                        @if(session('payment_warning'))
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                                <div class="flex">
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            <strong>Payment Failed:</strong> {{ session('payment_warning')['message'] }}
                                            Retry: {{ session('payment_warning')['retry_count'] }}/3
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Account Suspended Warning --}}
                        @if(session('account_suspended'))
                            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                                <div class="flex">
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700">
                                            <strong>Account Suspended:</strong> Your account has been suspended due to failed payments. Please update your payment method.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Plan Selection --}}
                        <div class="flex gap-4 mb-6">
                            <div class="bg-white rounded-md p-5 border-2 {{ $selectedPlan === 'monthly' ? 'border-[#0053FF]' : 'border-gray-200' }} max-w-[300px] cursor-pointer"
                                 wire:click="$set('selectedPlan', 'monthly')">
                                <h3 class="text-xl font-semibold mb-2">Monthly Plan</h3>
                                <h2 class="text-[28px] text-[#0053FF] font-semibold mb-2">£10.00<span class="text-sm text-gray-600">/month</span></h2>
                                <p class="text-sm text-gray-600">Billed monthly</p>
                            </div>
                            
                            <div class="bg-white rounded-md p-5 border-2 {{ $selectedPlan === 'annual' ? 'border-[#0053FF]' : 'border-gray-200' }} max-w-[300px] cursor-pointer"
                                 wire:click="$set('selectedPlan', 'annual')">
                                <h3 class="text-xl font-semibold mb-2">Annual Plan</h3>
                                <h2 class="text-[28px] text-[#0053FF] font-semibold mb-2">£71.88<span class="text-sm text-gray-600">/year</span></h2>
                                <p class="text-sm text-green-600 font-medium">Save £48.12 (40% off)</p>
                                <p class="text-xs text-gray-600 mt-1">£5.99/month equivalent</p>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-md p-5 border border-[#0053FF] max-w-[538px] mt-2 subscription-box">
                            <ul class="mt-6 mb-4 text-gray-700 space-y-2 ">
                                <li class="flex items-start">
                                    <span class="text-[#0053FF] mr-2">✔</span>
                                    <span>Full access to all features</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-[#0053FF] mr-2">✔</span>
                                    <span>Priority support</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-[#0053FF] mr-2">✔</span>
                                    <span>Legal Services available after 3 months of continued payment</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-[#0053FF] mr-2">✔</span>
                                    <span>Document reminders & expiry alerts</span>
                                </li>
                            </ul>

                            <div x-data="{ loading: false }">
                                <button 
                                    wire:click="checkout('{{ $selectedPlan }}')"
                                    wire:loading.attr="disabled"
                                    @click="loading = true"
                                    class="bg-[#0053FF] w-full text-white py-3 flex items-center justify-center text-[16px] border rounded-md mt-3 relative"
                                >
                                    <!-- Normal text -->
                                    <span x-show="!loading">Subscribe Now - {{ $selectedPlan === 'annual' ? 'Annual Plan' : 'Monthly Plan' }}</span>

                                    <!-- Loading State -->
                                    <span x-show="loading" class="flex items-center space-x-2">
                                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                        </svg>
                                        <span>Processing...</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    @else
                        <div>
                            @if(session('success'))
                                <div 
                                    x-data="{ show: true }"
                                    x-init="setTimeout(() => show = false, 4000)"
                                    x-show="show"
                                    class="fixed inset-0 flex items-center justify-center z-50"
                                >
                                    <div 
                                        class="bg-white border-2 border-green-500 rounded-2xl shadow-lg p-6 max-w-sm w-full text-center"
                                        x-transition
                                    >
                                        <!-- Success Image -->
                                        <img src="{{ asset('images/success.png') }}" alt="Success" class="w-24 h-24 mx-auto mb-4">

                                        <!-- Success Title -->
                                        <h2 class="text-2xl font-bold text-green-600">Payment Success!</h2>

                                        <!-- Success Subtitle -->
                                        <p class="text-gray-600 mt-2">It our pleasure to offer you our Product</p>

                                        <!-- Continue Button -->
                                        <div class="mt-5">
                                            <a href="{{ route('subscription.page') }}" 
                                            class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                                                Continue
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>



                        {{-- Active Subscription --}}
                        <div class="flex justify-between items-center mb-5">
                            <h4 class="text-[#1B1B1B] text-[24px] font-medium">Membership Status</h4>
                            <div class="flex gap-2">
                                @php
                                    $portalUrl = $this->getCustomerPortalUrl();
                                @endphp
                                @if($portalUrl)
                                    <a href="{{ $portalUrl }}" target="_blank"
                                    class="flex border rounded-sm px-5 py-2 bg-white text-[16px] text-[#0053FF] border-[#0053FF] hover:bg-blue-50 transition">
                                        Manage Billing
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if($subscription->status === 'past_due')
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                                <p class="text-sm text-yellow-700">
                                    <strong>Payment Failed:</strong> Please update your payment method to avoid service interruption.
                                    Retry attempts: {{ $subscription->payment_retry_count }}/3
                                </p>
                            </div>
                        @endif

                        @if($subscription->status === 'suspended')
                            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                                <p class="text-sm text-red-700">
                                    <strong>Account Suspended:</strong> Your account has been suspended due to failed payments. Please update your payment method to restore access.
                                </p>
                            </div>
                        @endif

                        <div class="flex gap-10 flex-wrap">
                            <div class="grid">
                                <span class="text-[14px] text-[#616161] mb-1">Plan</span> 
                                <span class="text-[18px] text-[#1B1B1B] font-medium">
                                    @php
                                        $planType = $subscription->plan_type ?? ($subscription->interval === 'year' ? 'annual' : 'monthly');
                                        $amount = $subscription->amount ?? 0;
                                    @endphp
                                    @if($planType === 'annual')
                                        £{{ number_format($amount / 100, 2) }}/Year
                                        <span class="text-sm text-gray-600">(£{{ number_format(($amount / 100) / 12, 2) }}/month)</span>
                                    @else
                                        £{{ number_format($amount / 100, 2) }}/Month
                                    @endif
                                </span>
                            </div>
                            <div class="grid">
                                <span class="text-[14px] text-[#616161] mb-1">Status</span>
                                <span class="text-[18px] font-medium 
                                    {{ $subscription->status === 'active' ? 'text-green-600' : '' }}
                                    {{ $subscription->status === 'past_due' ? 'text-yellow-600' : '' }}
                                    {{ $subscription->status === 'suspended' ? 'text-red-600' : '' }}
                                    {{ $subscription->status === 'cancelled' ? 'text-gray-600' : '' }}">
                                    {{ ucfirst($subscription->status) }}
                                    @if($subscription->cancel_at_period_end)
                                        <span class="text-sm text-gray-500">(Cancelling)</span>
                                    @endif
                                </span>
                            </div>
                            <div class="grid">
                                <span class="text-[14px] text-[#616161] mb-1">Billing Cycle</span>
                                <span class="text-[18px] text-[#1B1B1B] font-medium">
                                    @php
                                        $billingCycle = $subscription->plan_type ?? ($subscription->interval === 'year' ? 'annual' : ($subscription->interval ?? 'monthly'));
                                    @endphp
                                    {{ ucfirst($billingCycle) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-10 flex-wrap mt-8">
                            <div class="grid">
                                <span class="text-[14px] text-[#616161] mb-1">Start Date</span>
                                <span class="text-[18px] text-[#1B1B1B] font-medium">
                                    {{ $subscription->start_date ? $subscription->start_date->format('d M Y') : 'N/A' }}
                                </span>
                            </div>
                            <div class="grid">
                                <span class="text-[14px] text-[#616161] mb-1">Next Billing Date</span>
                                @php
                                    $nextBilling = $subscription->current_period_end ?? $subscription->end_date;
                                    $daysLeft = $nextBilling ? now()->diffInDays($nextBilling, false) : null;
                                @endphp

                                <span class="text-[18px] font-medium {{ $daysLeft !== null && $daysLeft < 1 ? 'text-red-500' : 'text-[#1B1B1B]' }}">
                                    {{ $nextBilling ? $nextBilling->format('d M Y') : 'N/A' }}
                                </span>

                                @if($daysLeft !== null && $daysLeft >= 0 && $daysLeft <= 7 && $subscription->status === 'active')
                                    <span class="text-yellow-600 text-sm mt-1">⚠️ Renewing in {{ $daysLeft }} day{{ $daysLeft != 1 ? 's' : '' }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Cancellation/Reactivation Actions --}}
                        {{-- Hidden as per request --}}
                        {{-- <div class="mt-6 flex gap-3">
                            @if($subscription->cancel_at_period_end && $subscription->canReactivate())
                                <button 
                                    wire:click="reactivate"
                                    wire:confirm="Are you sure you want to reactivate your subscription?"
                                    class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition">
                                    Reactivate Subscription
                                </button>
                                <p class="text-sm text-gray-600 self-center">
                                    Your subscription will be cancelled on {{ $subscription->current_period_end->format('d M Y') }}
                                </p>
                            @elseif($subscription->status === 'active' && !$subscription->cancel_at_period_end)
                                <button 
                                    wire:click="cancel"
                                    wire:confirm="Are you sure you want to cancel your subscription? Your access will continue until {{ $subscription->current_period_end ? $subscription->current_period_end->format('d M Y') : 'the end of your billing period' }}."
                                    class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700 transition">
                                    Cancel Subscription
                                </button>
                            @endif
                        </div> --}}
                    @endif

                </div>
            </div>
            </div>
        </div>
        @endunlessrole
</main>
