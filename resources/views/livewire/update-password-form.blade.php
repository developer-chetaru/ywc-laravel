<main class="flex-1 min-h-0 w-full flex gap-x-[18px] px-2 pt-3" style="padding-bottom: 0px;">
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


        <!-- Main Content -->
        <div class="flex-1 min-w-0 overflow-y-auto flex flex-col gap-[11px] [scrollbar-width:none] h-full">
            <div class="bg-white p-5 rounded-lg shadow-md">
                <h2 class="text-xl border-b border-gray-100 font-medium text-[#0053FF] pb-2">Change Password</h2>
                 <!-- Flash Message -->
                @if (session()->has('message'))
                    <div class="mb-3 text-blue-600 text-sm">{{ session('message') }}</div>
                @endif

                <!-- Change Password Form -->
                <form wire:submit.prevent="updatePassword" onsubmit="return validatePasswordForm(event)" class="mt-6 space-y-6 max-w-[650px]">

                    <!-- Current Password -->
                    <div x-data="{ show: false }" class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <input :type="show ? 'text' : 'password'"
                               wire:model="current_password"
                               placeholder="Enter current password"
                               class="w-full border rounded px-4 py-2 border-[#eaeaea] focus:ring-2 focus:ring-blue-500 pr-10">
                        <button type="button"
                                @click="show = !show"
                                class="absolute right-3 top-9 text-gray-500 hover:text-gray-700">
                            <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                        </button>
                        <x-input-error for="current_password" class="mt-2"/>
                    </div>

                    <!-- New Password -->
                    <div x-data="{ show: false }" class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input :type="show ? 'text' : 'password'"
                               id="update_password"
                               wire:model="password"
                               placeholder="Enter new password"
                               class="w-full border rounded px-4 py-2 border-[#eaeaea] focus:ring-2 focus:ring-blue-500 pr-10"
                               minlength="8">
                        <button type="button"
                                @click="show = !show"
                                class="absolute right-3 top-9 text-gray-500 hover:text-gray-700">
                            <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                        </button>
                        <div id="update_password_requirements" class="password-requirements mt-2 text-sm"></div>
                        <div id="update_password_strength" class="password-strength mt-2"></div>
                        <x-input-error for="password" class="mt-2"/>
                    </div>

                    <!-- Confirm Password -->
                    <div x-data="{ show: false }" class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input :type="show ? 'text' : 'password'"
                               id="update_password_confirmation"
                               wire:model="password_confirmation"
                               placeholder="Confirm new password"
                               class="w-full border rounded px-4 py-2 border-[#eaeaea] focus:ring-2 focus:ring-blue-500 pr-10">
                        <button type="button"
                                @click="show = !show"
                                class="absolute right-3 top-9 text-gray-500 hover:text-gray-700">
                            <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                        </button>
                        <div id="update_password_match_message" class="mt-2 text-sm"></div>
                        <x-input-error for="password_confirmation" class="mt-2"/>
                    </div>
                    
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const password = document.getElementById('update_password');
                        const confirmPassword = document.getElementById('update_password_confirmation');
                        const matchMessage = document.getElementById('update_password_match_message');
                        
                        function checkMatch() {
                            if (confirmPassword && password && confirmPassword.value && password.value) {
                                if (password.value === confirmPassword.value) {
                                    matchMessage.innerHTML = '<span class="text-green-600"><i class="fa-solid fa-check-circle mr-1"></i>Passwords match</span>';
                                } else {
                                    matchMessage.innerHTML = '<span class="text-red-600"><i class="fa-solid fa-times-circle mr-1"></i>Passwords do not match</span>';
                                }
                            } else if (matchMessage) {
                                matchMessage.innerHTML = '';
                            }
                        }
                        
                        if (password && confirmPassword) {
                            password.addEventListener('input', checkMatch);
                            confirmPassword.addEventListener('input', checkMatch);
                            
                            // Initialize password validation
                            if (typeof initPasswordValidation !== 'undefined') {
                                initPasswordValidation('update_password', 'update_password_requirements', 'update_password_strength');
                            }
                        }
                    });
                    
                    function validatePasswordForm(event) {
                        const password = document.getElementById('update_password');
                        const confirmPassword = document.getElementById('update_password_confirmation');
                        
                        if (!password || !confirmPassword) return true;
                        
                        if (typeof validatePassword !== 'undefined') {
                            const validation = validatePassword(password.value);
                            if (!validation.isValid) {
                                event.preventDefault();
                                alert('Password must contain at least 8 characters with uppercase, lowercase, number, and special character.');
                                return false;
                            }
                        }
                        
                        if (password.value !== confirmPassword.value) {
                            event.preventDefault();
                            alert('Passwords do not match.');
                            return false;
                        }
                        
                        return true;
                    }
                    </script>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="bg-[#0053FF] text-white px-6 py-3 flex justify-center items-center text-[16px] border rounded-md shadow hover:bg-blue-600 transition">
                        <i class="fa-solid fa-lock mr-2"></i> Update Password
                    </button>
                </form>
            </div>
            </div>
        </div>
</main>
