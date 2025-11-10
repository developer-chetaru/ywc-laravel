<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
/>

<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-blue-600 px-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-sm p-8 relative">
            
            <!-- Logo Circle -->
            <div class="absolute -top-12 left-1/2 -translate-x-1/2 flex items-center justify-center w-24 h-24 rounded-full border-[3.5px] border-blue-600 bg-white shadow-md">
                <img src="{{ asset('images/ywc-logo.svg') }}" alt="Logo" class="w-14 h-14" />
            </div>

            <h1 class="text-xl font-semibold text-center mt-6">Reset Password</h1>
            <p class="text-sm text-gray-500 text-center mt-2 mb-4">
                Enter your new password below.
            </p>

            

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4 mt-4">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email -->
                <div>
                    <x-label for="email" value="{{ __('Email') }}" class="text-gray-700" />
                    <x-input 
                        id="email" 
                        class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        type="email" 
                        name="email" 
                        :value="old('email', $request->email)" 
                        required 
                        autofocus 
                        autocomplete="username" readonly
                    />
                </div>

               <!-- Password -->
<div class="relative mb-4" x-data="{ show: false }">
    <label class="flex items-center bg-[#fafafa] border border-gray-300 rounded px-2 w-full focus-within:ring-2 focus-within:ring-[#0053FF]">
        <input
            :type="show ? 'text' : 'password'"
            id="password"
            name="password"
            placeholder="Password"
            class="w-full bg-transparent border-none outline-none appearance-none focus:ring-0 text-gray-900 placeholder-gray-400 text-base pr-8"
            required
            autocomplete="new-password"
        />
        <button
            type="button"
            @click="show = !show"
            class="ml-2 text-gray-500 focus:outline-none"
            tabindex="-1"
            aria-label="Toggle password visibility"
        >
            <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
        </button>
    </label>
</div>

<!-- Confirm Password -->
<div class="relative mb-4" x-data="{ show: false }">
    <label class="flex items-center bg-[#fafafa] border border-gray-300 rounded px-2 w-full focus-within:ring-2 focus-within:ring-[#0053FF]">
        <input
            :type="show ? 'text' : 'password'"
            id="password_confirmation"
            name="password_confirmation"
            placeholder="Confirm Password"
            class="w-full bg-transparent border-none outline-none appearance-none focus:ring-0 text-gray-900 placeholder-gray-400 text-base pr-8"
            required
            autocomplete="new-password"
        />
        <button
            type="button"
            @click="show = !show"
            class="ml-2 text-gray-500 focus:outline-none"
            tabindex="-1"
            aria-label="Toggle password visibility"
        >
            <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
        </button>
    </label>
</div>
<!-- Validation Errors -->
            <x-validation-errors class="mb-4 text-sm text-blue-600" />

                <!-- Submit -->
                <div class="pt-2">
                    <x-button class="w-full justify-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-md transition">
                        {{ __('Reset Password') }}
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
