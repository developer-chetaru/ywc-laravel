<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
/>

<x-guest-layout>
    <div class="min-h-screen flex bg-[#0053FF]">
        {{-- LEFT PANEL --}}
        <div class="flex-1 hidden md:flex flex-col justify-center pl-16 pr-10 text-white relative overflow-hidden">
            <img src="{{ asset('images/ywc-member-updated-image.svg') }}" 
                 alt="Captain"
                 class="absolute left-0 bottom-0 h-[85%] z-0 select-none" 
                 draggable="false" />
            <div class="relative z-10 flex flex-col items-end mr-[-44px]">
                <h1 class="text-3xl md:text-4xl font-semibold leading-snug mb-2">
                    Your personal yacht
                    <br />
                    <span class="bg-white text-[#0053FF] px-2 mx-1 rounded font-semibold">
                        career dashboard
                    </span> 
                    awaits.
                </h1>
                <div class="mt-6">
                    <div class="font-semibold mb-2 text-lg">
                        Easily manage and access everything you need
                    </div>
                    <ul class="list-disc pl-5 space-y-1 mb-2 text-base font-normal">
                        <li>Centralized document storage</li>
                        <li>Certificate expiry reminders</li>
                        <li>Smart CV generation tools</li>
                        <li>Exclusive legal &amp; career support</li>
                    </ul>
                    <div class="mb-1 mt-4">
                        Set sail toward your next opportunity with confidence.
                    </div>
                    <div class="font-bold text-white">Built for crew, by crew.</div>
                </div>
            </div>
        </div>

        {{-- RIGHT PANEL --}}
        <div class="flex-1 flex items-center justify-center relative overflow-hidden bg-[#0053FF]">
          	<img src="{{ asset('images/right.svg') }}" alt="background" class="absolute inset-0 w-full h-full object-cover object-right z-0"
                draggable="false"/>
            <div class="w-full max-w-md relative px-5">
                {{-- Floating Logo --}}
                <div class="absolute left-1/2 -translate-x-1/2 -top-14 z-10 flex items-center justify-center">
                    <div class="rounded-full border-[3.5px] border-[#0053FF] bg-white flex items-center justify-center" style="width:90px; height:90px;">
                        <img src="{{ asset('images/ywc-logo.svg') }}" 
                             alt="Logo"
                             class="w-[60px] h-[60px] object-contain" />
                    </div>
                </div>

                {{-- Form Card --}}
                <div class="bg-white rounded-2xl shadow-xl pt-16 pb-6 px-5 flex flex-col items-center">
                    <h1 class="text-2xl font-semibold mb-2 text-center">Login</h1>
                    <!-- <p class="text-sm text-center text-gray-500 mb-6">
                        Need to create an account? 
                        <a href="{{ route('register') }}" class="text-[#0053FF] font-medium">Sign up</a>
                    </p> -->

                    {{-- Laravel Login Form --}}
                    <form method="POST" action="{{ route('login') }}" class="w-full" x-data="{ show: false }">
                        @csrf
                        
                        <input type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               placeholder="Enter your email"
                               class="w-full px-4 py-2 mb-2 borde text-gray-900 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0053FF] text-gray-900" 
                               autofocus autocomplete="username" />
                        @error('email')
                            <p class="text-red-500 text-sm mb-3">{{ $message }}</p>
                        @enderror

              {{-- Password field (match lower design) --}}
<div class="relative mb-1">
  <label class="flex items-center bg-[#fafafa] border border-gray-300 rounded px-2 w-full focus-within:ring-2 focus-within:ring-[#0053FF]">
    <input
      :type="show ? 'text' : 'password'"
      id="password"
      name="password"
      placeholder="Password"
      class="w-full bg-transparent border-none outline-none appearance-none focus:ring-0 text-gray-900 placeholder-gray-400 text-base pr-8"
      autocomplete="current-password"
      data-lpignore="true"
      data-1p-ignore
      data-bwignore="true"
      data-dashlane-skip="true"
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


                      
                        @error('password')
                            <p class="text-red-500 text-sm mb-3">{{ $message }}</p>
                        @enderror

                        <div class="mb-3 mt-1 text-[13px] text-left text-gray-500">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-[#0053FF] font-medium">
                                    Forgot your password?
                                </a>
                            @endif
                        </div>

                        <button type="submit" 
                                class="w-full bg-[#0053FF] text-white py-2 rounded-md font-medium hover:bg-blue-700 transition mb-3">
                            Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
