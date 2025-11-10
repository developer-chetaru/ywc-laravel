<main class="p-2 flex-1 overflow-y-auto">
    <div class="w-full h-screen gap-5 flex flex-wrap md:flex-nowrap grid-cols-4 grid">

        <!-- Sidebar -->
          @php
              $currentRoute = Route::currentRouteName();
          @endphp

          <div class="flex bg-white p-5 rounded-lg shadow-md main-nav-left flex-wrap flex-col">
              <h4 class="text-[14px] text-[#616161] mb-4">Personal account</h4>
              <ul class="mb-4">
                  <li class="{{ $currentRoute == 'profile' ? 'active' : '' }}">
                      <a href="{{ route('profile') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                          bg-white {{ $currentRoute == 'profile' ? 'text-blue-600' : 'text-black' }}">

                          <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                              xmlns="http://www.w3.org/2000/svg"
                              class="{{ $currentRoute == 'profile' ? 'stroke-blue-600' : 'stroke-black' }}">
                              <path d="M15 9C15 7.34315 13.6569 6 12 6C10.3431 6 9 7.34315 9 9C9 10.6569 10.3431 12 12 12C13.6569 12 15 10.6569 15 9Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M17 17C17 14.2386 14.7614 12 12 12C9.23858 12 7 14.2386 7 17" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                          </svg>

                          <span class="text-base font-medium">
                              Your profile
                          </span>
                      </a>
                  </li>

                  <li class="{{ $currentRoute == 'profile.password' ? 'active' : '' }}">
                      <a href="{{ route('profile.password') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                          bg-white {{ $currentRoute == 'profile.password' ? 'text-blue-600' : 'text-black' }}">

                          <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                              xmlns="http://www.w3.org/2000/svg"
                              class="{{ $currentRoute == 'profile.password' ? 'stroke-blue-600' : 'stroke-black' }}">
                              <path d="M4.26781 18.8447C4.49269 20.515 5.87613 21.8235 7.55966 21.9009C8.97627 21.966 10.4153 22 12 22C13.5847 22 15.0237 21.966 16.4403 21.9009C18.1239 21.8235 19.5073 20.515 19.7322 18.8447C19.879 17.7547 20 16.6376 20 15.5C20 14.3624 19.879 13.2453 19.7322 12.1553C19.5073 10.485 18.1239 9.17649 16.4403 9.09909C15.0237 9.03397 13.5847 9 12 9C10.4153 9 8.97627 9.03397 7.55966 9.09909C5.87613 9.17649 4.49269 10.485 4.26781 12.1553C4.12105 13.2453 4 14.3624 4 15.5C4 16.6376 4.12105 17.7547 4.26781 18.8447Z" stroke-width="1.5"/>
                              <path d="M7.5 9V6.5C7.5 4.01472 9.51472 2 12 2C14.4853 2 16.5 4.01472 16.5 6.5V9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M16 15.4902V15.5002" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M12 15.4902V15.5002" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M8 15.4902V15.5002" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                          </svg>

                          <span class="text-base font-medium">
                              Change Password
                          </span>
                      </a>
                  </li>
              </ul>

              @role('user')
              <h4 class="text-[14px] text-[#616161] mb-4">Payment and plans</h4>
              <ul>
                  <li class="{{ $currentRoute == 'subscription.page' ? 'active' : '' }}">
                      <a href="{{ route('subscription.page') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                          bg-white {{ $currentRoute == 'subscription.page' ? 'text-blue-600' : 'text-black' }}">

                          <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                              xmlns="http://www.w3.org/2000/svg"
                              class="{{ $currentRoute == 'subscription.page' ? 'stroke-blue-600' : 'stroke-black' }}">
                              <path d="M2 12C2 8.46252 2 6.69377 3.0528 5.5129C3.22119 5.32403 3.40678 5.14935 3.60746 4.99087C4.86213 4 6.74142 4 10.5 4H13.5C17.2586 4 19.1379 4 20.3925 4.99087C20.5932 5.14935 20.7788 5.32403 20.9472 5.5129C22 6.69377 22 8.46252 22 12C22 15.5375 22 17.3062 20.9472 18.4871C20.7788 18.676 20.5932 18.8506 20.3925 19.0091C19.1379 20 17.2586 20 13.5 20H10.5C6.74142 20 4.86213 20 3.60746 19.0091C3.40678 18.8506 3.22119 18.676 3.0528 18.4871C2 17.3062 2 15.5375 2 12Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M10 16H11.5" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M14.5 16H18" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M2 9H22" stroke-width="1.5" stroke-linejoin="round"/>
                          </svg>

                          <span class="text-base font-medium">
                              Subscription
                          </span>
                      </a>
                  </li>

                  <li>
                      <a href="{{ route('purchase.history') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                                     bg-white text-black">

                          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M4 18.6458V8.05426C4 5.20025 4 3.77325 4.87868 2.88663C5.75736 2 7.17157 2 10 2H14C16.8284 2 18.2426 2 19.1213 2.88663C20 3.77325 20 5.20025 20 8.05426V18.6458C20 20.1575 20 20.9133 19.538 21.2108C18.7831 21.6971 17.6161 20.6774 17.0291 20.3073C16.5441 20.0014 16.3017 19.8485 16.0325 19.8397C15.7417 19.8301 15.4949 19.9768 14.9709 20.3073L13.06 21.5124C12.5445 21.8374 12.2868 22 12 22C11.7132 22 11.4555 21.8374 10.94 21.5124L9.02913 20.3073C8.54415 20.0014 8.30166 19.8485 8.03253 19.8397C7.74172 19.8301 7.49493 19.9768 6.97087 20.3073C6.38395 20.6774 5.21687 21.6971 4.46195 21.2108C4 20.9133 4 20.1575 4 18.6458Z" stroke="#1B1B1B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M11 11H8" stroke="#1B1B1B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M14 7H8" stroke="#1B1B1B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                          </svg>

                          <span class="text-base font-medium" :class="isOpen ? 'text-blue-600' : 'text-black'">Purchase History</span>
                      </a>
                  </li>
              </ul>
              @endrole
</div>


        <!-- Main Content -->
        <div class="bg-white p-5 rounded-lg shadow-md col-span-3">
            <div class="w-full">
                <h2 class="text-xl border-b border-gray-100 font-medium text-[#0053FF] pb-2">Change Password</h2>
                 <!-- Flash Message -->
                @if (session()->has('message'))
                    <div class="mb-3 text-blue-600 text-sm">{{ session('message') }}</div>
                @endif

                <!-- Change Password Form -->
                <form wire:submit.prevent="updatePassword" class="mt-6 space-y-6 max-w-[650px]">

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
                               wire:model="password"
                               placeholder="Enter new password"
                               class="w-full border rounded px-4 py-2 border-[#eaeaea] focus:ring-2 focus:ring-blue-500 pr-10">
                        <button type="button"
                                @click="show = !show"
                                class="absolute right-3 top-9 text-gray-500 hover:text-gray-700">
                            <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                        </button>
                        <x-input-error for="password" class="mt-2"/>
                    </div>

                    <!-- Confirm Password -->
                    <div x-data="{ show: false }" class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input :type="show ? 'text' : 'password'"
                               wire:model="password_confirmation"
                               placeholder="Confirm new password"
                               class="w-full border rounded px-4 py-2 border-[#eaeaea] focus:ring-2 focus:ring-blue-500 pr-10">
                        <button type="button"
                                @click="show = !show"
                                class="absolute right-3 top-9 text-gray-500 hover:text-gray-700">
                            <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                        </button>
                        <x-input-error for="password_confirmation" class="mt-2"/>
                    </div>

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
