<x-form-section submit="updatePassword">
    <x-slot name="title">
        Change Password
    </x-slot>

    <x-slot name="description">
    </x-slot>

    <x-slot name="form">
        <!-- Current Password -->
        <div class="col-span-6 sm:col-span-6" x-data="{ showCurrent: false }">
            <div class="relative mb-1">
                <label class="flex items-center bg-[#fafafa] border border-gray-300 rounded px-2 w-full focus-within:ring-2 focus-within:ring-[#0053FF]">
                    <input
                        :type="showCurrent ? 'text' : 'password'"
                        id="current_password"
                        placeholder="Current Password"
                        class="w-full bg-transparent border-none outline-none appearance-none focus:ring-0 text-gray-900 placeholder-gray-400 text-base pr-8"
                        wire:model="state.current_password"
                        autocomplete="current-password"
                    />
                    <button type="button" @click="showCurrent = !showCurrent"
                        class="ml-2 text-gray-500 focus:outline-none" tabindex="-1" aria-label="Toggle password visibility">
                        <i :class="showCurrent ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                    </button>
                </label>
            </div>
            <x-input-error for="current_password" class="mt-2" />
        </div>

        <!-- New Password -->
        <div class="col-span-6 sm:col-span-6" x-data="{ showNew: false }">
            <div class="relative mb-1">
                <label class="flex items-center bg-[#fafafa] border border-gray-300 rounded px-2 w-full focus-within:ring-2 focus-within:ring-[#0053FF]">
                    <input
                        :type="showNew ? 'text' : 'password'"
                        id="profile_password"
                        placeholder="New Password"
                        class="w-full bg-transparent border-none outline-none appearance-none focus:ring-0 text-gray-900 placeholder-gray-400 text-base pr-8"
                        wire:model="state.password"
                        autocomplete="new-password"
                        minlength="8"
                    />
                    <button type="button" @click="showNew = !showNew"
                        class="ml-2 text-gray-500 focus:outline-none" tabindex="-1" aria-label="Toggle password visibility">
                        <i :class="showNew ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                    </button>
                </label>
            </div>
            <div id="profile_password_requirements" class="password-requirements mt-2 text-sm"></div>
            <div id="profile_password_strength" class="password-strength mt-2"></div>
            <x-input-error for="password" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="col-span-6 sm:col-span-6" x-data="{ showConfirm: false }">
            <div class="relative mb-1">
                <label class="flex items-center bg-[#fafafa] border border-gray-300 rounded px-2 w-full focus-within:ring-2 focus-within:ring-[#0053FF]">
                    <input
                        :type="showConfirm ? 'text' : 'password'"
                        id="profile_password_confirmation"
                        placeholder="Confirm Password"
                        class="w-full bg-transparent border-none outline-none appearance-none focus:ring-0 text-gray-900 placeholder-gray-400 text-base pr-8"
                        wire:model="state.password_confirmation"
                        autocomplete="new-password"
                    />
                    <button type="button" @click="showConfirm = !showConfirm"
                        class="ml-2 text-gray-500 focus:outline-none" tabindex="-1" aria-label="Toggle password visibility">
                        <i :class="showConfirm ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                    </button>
                </label>
            </div>
            <div id="profile_password_match_message" class="mt-2 text-sm"></div>
            <x-input-error for="password_confirmation" class="mt-2" />
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('profile_password');
            const confirmPassword = document.getElementById('profile_password_confirmation');
            const matchMessage = document.getElementById('profile_password_match_message');
            
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
                    initPasswordValidation('profile_password', 'profile_password_requirements', 'profile_password_strength');
                }
            }
        });
        </script>
      	<div class="col-span-6 sm:col-span-6">
            <x-action-message on="saved" type="success">
                Change password updated successfully!
            </x-action-message>
            <x-action-message on="error" type="error">
                Something went wrong.
            </x-action-message>
        </div>
      
    </x-slot>
    <x-slot name="actions">
        <div class="w-full flex justify-start -mt-7">
            <x-button class="bg-blue-600 hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 text-white">
                {{ __('Save') }}
            </x-button>
        </div>
    </x-slot>
</x-form-section>
