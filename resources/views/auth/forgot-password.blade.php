<x-guest-layout>
  <div class="min-h-screen flex items-center justify-center bg-[#0053FF] relative overflow-hidden">

    <!-- Right Background SVG -->
    <img
      src="/images/right.svg"
      alt="Background"
      class="fixed right-0 top-0 h-full w-auto object-contain z-0 min-w-[600px]"
      draggable="false"
    />

    <!-- Container -->
    <div class="relative z-10 w-full max-w-sm px-4">

      <!-- Logo Above Card -->
      <div class="absolute left-1/2 -translate-x-1/2 -top-14 flex justify-center">
        <div
          class="w-[90px] h-[90px] rounded-full border-[3.5px] border-[#0053FF] bg-white flex items-center justify-center shadow-md"
        >
          <img
            src="/images/ywc-logo.svg"
            alt="Logo"
            class="w-[60px] h-[60px] object-contain"
            draggable="false"
          />
        </div>
      </div>

      <!-- Card -->
      <div class="bg-white rounded-xl shadow-lg pt-16 pb-8 px-7 mt-14">
        <h1 class="text-xl font-semibold text-center mb-2">Forgot Password</h1>
        <p class="text-sm text-gray-600 text-center mb-6">
          Enter your registered email address and we&apos;ll send you a link to reset your password.
        </p>

        

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
          @csrf

          <!-- Email Input -->
          <div class="flex items-center border border-gray-300 rounded-md px-3 bg-white focus-within:ring-2 focus-within:ring-[#0053FF]">
            <span class="pr-2">
              <svg xmlns="http://www.w3.org/2000/svg" 
                   class="h-5 w-5 text-gray-500" 
                   fill="currentColor" 
                   viewBox="0 0 20 20">
                <path d="M2.94 6.34a2 2 0 0 1 1.41-.58h11.3a2 2 0 0 1 1.41.58l-6.06 4.91-6.06-4.91zM18 8.14v6.36a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8.14l7.06 5.73a1 1 0 0 0 1.28 0L18 8.14z"/>
              </svg>
            </span>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="Enter your email"
              class="w-full bg-transparent mt- border-none outline-none focus:ring-0 text-gray-500 placeholder-gray-400 text-base"
              required
            />
          </div>
          
          {{-- Success Message --}}
          @if (session('status'))
            <p class="text-[13px] text-center text-blue-600 mb-3">
              {{ session('status') }}
            </p>
          @endif

          {{-- Error Messages --}}
          @if ($errors->any())
            <div class="text-blue-600 text-[13px] mb-3">
              <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <!-- Submit Button -->
          <button
            type="submit"
            class="w-full bg-[#0053FF] text-white font-medium py-2 rounded-md hover:bg-blue-700 transition-colors"
          >
            Reset Password
          </button>

          <!-- Back to Login -->
          <a
            href="{{ route('login') }}"
            class="block text-center text-sm text-[#0053FF] hover:underline"
          >
            Back to Login
          </a>
        </form>
      </div>
    </div>
  </div>
</x-guest-layout>
