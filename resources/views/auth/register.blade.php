<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
/>
<x-guest-layout>
    <div class="min-h-screen flex bg-[#0053FF]">
        {{-- LEFT PANEL --}}
        <div class="flex-1 hidden md:flex flex-col justify-center pl-16 pr-10 text-white relative overflow-hidden">
            <img 
                src="{{ asset('images/lighthouse-ywc.svg') }}" 
                alt="Lighthouse" 
                class="absolute inset-0 w-full h-full object-cover object-left z-0"
                draggable="false"
            />
            <div class="relative z-10 flex flex-col items-end mr-[-44px]">
                <h1 class="text-[40px] font-bold leading-snug mb-4 mt-4">
                    One profile.
                    <span class="bg-white text-[#1768FF] px-2 mx-2 rounded">
                        Endless
                    </span>
                    <br />
                    <span class="bg-white text-[#1768FF] px-2 mt-2 rounded">
                        possibilities.
                    </span>
                </h1>
                <div class="mt-6">
                    <div class="font-semibold mb-2 text-lg">
                        Register now to streamline your maritime career:
                    </div>
                    <ul class="list-disc pl-5 space-y-1 mb-2 text-base font-normal">
                        <li>Centralized document storage</li>
                        <li>Certificate expiry reminders</li>
                        <li>Smart CV generation tools</li>
                        <li>Exclusive legal & career support</li>
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
            <img 
                src="{{ asset('images/right.svg') }}" 
                alt="background" 
                class="absolute inset-0 w-full h-full object-cover object-right z-0"
                draggable="false"
            />

            <div class="w-full max-w-md p-8 rounded-xl shadow-2xl bg-white relative z-10 mt-12">
                {{-- Floating Logo --}}
                <div class="absolute left-1/2 -translate-x-1/2 -top-12 z-20 flex items-center justify-center">
                    <div class="bg-white rounded-full border-4 border-[#0043ef] p-2 flex items-center justify-center">
                        <img 
                            src="{{ asset('images/ywc-logo.svg') }}" 
                            alt="Logo" 
                            width="80" height="80" 
                            class="h-20 w-20"
                        />
                    </div>
                </div>

                <h2 class="text-2xl font-semibold mb-1 text-center mt-8">
                    Sign up for an account
                </h2>
                <p class="text-sm text-center text-gray-500 mb-5">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-[#0053FF] font-medium">
                        Login
                    </a>
                </p>

           
                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <div class="flex gap-2">
                        <div class="w-1/2">
                            <input
                                type="text"
                                name="first_name"
                                placeholder="First name"
                                value="{{ old('first_name') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md 
                                    focus:border-[#0053FF] focus:ring-2 focus:ring-[#0053FF] outline-none"
                                autofocus
                            />
                            @error('first_name')
                                <p class="text-sm text-[#0053FF] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="w-1/2">
                            <input
                                type="text"
                                name="last_name"
                                placeholder="Last name"
                                value="{{ old('last_name') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md 
                                    focus:border-[#0053FF] focus:ring-2 focus:ring-[#0053FF] outline-none"
                            />
                            @error('last_name')
                                <p class="text-sm text-[#0053FF] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <input
                            type="email"
                            name="email"
                            placeholder="Email"
                            value="{{ old('email') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md 
                                focus:border-[#0053FF] focus:ring-2 focus:ring-[#0053FF] outline-none"
                        />
                        @error('email')
                            <p class="text-sm text-[#0053FF] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                  <div class="mt-4">
                      <select id="role" name="role" class="w-full border-gray-300 rounded-md shadow-sm mt-1">
                          @foreach (\Spatie\Permission\Models\Role::whereNotIn('name', ['super_admin', 'user'])->get() as $role)
                              <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                          @endforeach
                      </select>
                      @error('role')
                          <p class="text-sm text-[#0053FF] mt-1">{{ $message }}</p>
                      @enderror
                  </div>

                  {{-- Vessel Flag State with Search --}}
                  <div>
                      <select
                          id="vessel_flag_state"
                          name="vessel_flag_state"
                          class="w-full px-4 py-2 border border-gray-300 rounded-md 
                              focus:border-[#0053FF] focus:ring-2 focus:ring-[#0053FF] outline-none"
                      >
                          <option value="">Select Vessel Flag (Optional)</option>
                          @foreach(config('vessel_flags.flags') as $country => $flag)
                              <option value="{{ $country }}">{{ $flag }} {{ $country }}</option>
                          @endforeach
                      </select>
                      @error('vessel_flag_state')
                          <p class="text-sm text-[#0053FF] mt-1">{{ $message }}</p>
                      @enderror
                  </div>

                  {{-- Password Field --}}
                  <div x-data="{ showPassword: false }" class="relative">
                      <label class="flex items-center bg-[#fafafa] border border-gray-300 rounded px-3 w-full focus-within:ring-2 focus-within:ring-[#0053FF]">
                          <input
                              :type="showPassword ? 'text' : 'password'"
                              id="register_password"
                              name="password"
                              placeholder="Password"
                              class="w-full bg-transparent border-none outline-none appearance-none focus:ring-0 text-gray-900 placeholder-gray-400 text-base pr-8"
                              autocomplete="new-password"
                              required
                              minlength="8"
                              data-lpignore="true"
                              data-1p-ignore
                              data-bwignore="true"
                              data-dashlane-skip="true"
                          />       
                          <button type="button" @click="showPassword = !showPassword" class="ml-2 text-gray-500 focus:outline-none" tabindex="-1">
                              <i :class="showPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                          </button>
                      </label>
                      <div id="register_password_requirements" class="password-requirements mt-2 text-sm"></div>
                      <div id="register_password_strength" class="password-strength mt-2"></div>
                      @error('password')
                          <p class="text-sm text-[#0053FF] mt-1">{{ $message }}</p>
                      @enderror
                  </div>


                  {{-- Confirm Password Field --}}
                  <div x-data="{ showConfirm: false }" class="relative">
                      <label class="flex items-center bg-[#fafafa] border border-gray-300 rounded px-3 w-full focus-within:ring-2 focus-within:ring-[#0053FF]">
                          <input
                              :type="showConfirm ? 'text' : 'password'"
                              id="register_password_confirmation"
                              name="password_confirmation"
                              placeholder="Confirm Password"
                              class="w-full bg-transparent border-none outline-none appearance-none focus:ring-0 text-gray-900 placeholder-gray-400 text-base pr-8"
                              autocomplete="new-password"
                              required
                              data-lpignore="true"
                              data-1p-ignore
                              data-bwignore="true"
                              data-dashlane-skip="true"      
                          />
                          <button type="button" @click="showConfirm = !showConfirm" class="ml-2 text-gray-500 focus:outline-none" tabindex="-1">
                              <i :class="showConfirm ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                          </button>
                      </label>
                      <div id="password_match_message" class="mt-2 text-sm"></div>
                      @error('password_confirmation')
                          <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                      @enderror
                  </div>
                  
                  <script>
                  document.addEventListener('DOMContentLoaded', function() {
                      const password = document.getElementById('register_password');
                      const confirmPassword = document.getElementById('register_password_confirmation');
                      const matchMessage = document.getElementById('password_match_message');
                      
                      function checkMatch() {
                          if (confirmPassword.value && password.value) {
                              if (password.value === confirmPassword.value) {
                                  matchMessage.innerHTML = '<span class="text-green-600"><i class="fa-solid fa-check-circle mr-1"></i>Passwords match</span>';
                                  confirmPassword.classList.remove('border-red-500');
                                  confirmPassword.classList.add('border-green-500');
                              } else {
                                  matchMessage.innerHTML = '<span class="text-red-600"><i class="fa-solid fa-times-circle mr-1"></i>Passwords do not match</span>';
                                  confirmPassword.classList.remove('border-green-500');
                                  confirmPassword.classList.add('border-red-500');
                              }
                          } else {
                              matchMessage.innerHTML = '';
                          }
                      }
                      
                      password.addEventListener('input', checkMatch);
                      confirmPassword.addEventListener('input', checkMatch);
                      
                      // Initialize password validation
                      if (typeof initPasswordValidation !== 'undefined') {
                          initPasswordValidation('register_password', 'register_password_requirements', 'register_password_strength');
                      }
                  });
                  </script>


                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                        <div class="flex text-xs">
                            <x-checkbox name="terms" id="terms" required />
                            <span class="ml-2">
                                By signing up, you accept our
                                <a href="{{ route('terms.show') }}" class="underline text-[#1768FF] ml-1" target="_blank">Terms of Service</a>
                                and
                                <a href="{{ route('policy.show') }}" class="underline text-[#1768FF] ml-1" target="_blank">Privacy Policy</a>
                            </span>
                        </div>
                    @endif

                    <button 
                        type="submit" 
                        class="w-full bg-[#1768FF] text-white py-2 rounded-md font-medium hover:bg-blue-700 transition"
                    >
                        Register
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Select2 for Searchable Dropdown --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <style>
        /* Style Select2 to match form design */
        .select2-container--default .select2-selection--single {
            height: 42px !important;
            padding: 8px 16px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            padding-left: 0 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #0053FF !important;
            outline: 2px solid #0053FF33 !important;
        }
        .select2-dropdown {
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
        }
        .select2-search--dropdown .select2-search__field {
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
            padding: 8px !important;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#vessel_flag_state').select2({
                placeholder: 'Select Vessel Flag (Optional)',
                allowClear: true,
                width: '100%',
                dropdownParent: $('.max-w-md')
            });
        });
    </script>
</x-guest-layout>