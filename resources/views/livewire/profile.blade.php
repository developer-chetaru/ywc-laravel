<main class="p-2 md:p-4 flex-1 overflow-y-auto bg-gray-50">
    <div class="w-full gap-5 grid grid-cols-1 md:grid-cols-4">
        <!-- Sidebar -->
        @php
            $currentRoute = Route::currentRouteName();
        @endphp

        <div class="flex bg-white p-4 md:p-5 rounded-xl shadow-lg border border-gray-100 main-nav-left flex-wrap flex-col mb-4 md:mb-0">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Personal account</h4>
            <ul class="mb-4">
                <li class="{{ $currentRoute == 'profile' ? 'active' : '' }}">
                    <a href="{{ route('profile') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200
                        {{ $currentRoute == 'profile' ? 'bg-gradient-to-r from-blue-50 to-blue-100 text-blue-600 shadow-sm' : 'text-gray-700 hover:bg-gray-50' }}">
                        
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
                    <a href="{{ route('profile.password') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200
                        {{ $currentRoute == 'profile.password' ? 'bg-gradient-to-r from-blue-50 to-blue-100 text-blue-600 shadow-sm' : 'text-gray-700 hover:bg-gray-50' }}">
                        
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

            @unlessrole('super_admin')
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 mt-6">Payment and plans</h4>
            <ul>
                <li class="{{ $currentRoute == 'subscription.page' ? 'active' : '' }}">
                    <a href="{{ route('subscription.page') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200
                        {{ $currentRoute == 'subscription.page' ? 'bg-gradient-to-r from-blue-50 to-blue-100 text-blue-600 shadow-sm' : 'text-gray-700 hover:bg-gray-50' }}">
                        
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
                    <a href="{{ route('purchase.history') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200
                                   text-gray-700 hover:bg-gray-50">
                                   
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 18.6458V8.05426C4 5.20025 4 3.77325 4.87868 2.88663C5.75736 2 7.17157 2 10 2H14C16.8284 2 18.2426 2 19.1213 2.88663C20 3.77325 20 5.20025 20 8.05426V18.6458C20 20.1575 20 20.9133 19.538 21.2108C18.7831 21.6971 17.6161 20.6774 17.0291 20.3073C16.5441 20.0014 16.3017 19.8485 16.0325 19.8397C15.7417 19.8301 15.4949 19.9768 14.9709 20.3073L13.06 21.5124C12.5445 21.8374 12.2868 22 12 22C11.7132 22 11.4555 21.8374 10.94 21.5124L9.02913 20.3073C8.54415 20.0014 8.30166 19.8485 8.03253 19.8397C7.74172 19.8301 7.49493 19.9768 6.97087 20.3073C6.38395 20.6774 5.21687 21.6971 4.46195 21.2108C4 20.9133 4 20.1575 4 18.6458Z" stroke="#1B1B1B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11 11H8" stroke="#1B1B1B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14 7H8" stroke="#1B1B1B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>

                        <span class="text-base font-medium" :class="isOpen ? 'text-blue-600' : 'text-black'">Purchase History</span>
                    </a>
                </li>
            </ul>
            @endunlessrole
        </div>


        <div class="bg-white p-4 md:p-6 rounded-xl shadow-lg border border-gray-100 md:col-span-3">
            <div class="w-full">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                    <h2 class="text-xl md:text-2xl font-bold text-[#0053FF]">Your profile</h2>
                    <div class="hidden md:flex items-center space-x-2 px-3 py-1 bg-blue-50 rounded-full">
                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs font-medium text-blue-600">Profile Settings</span>
                    </div>
                </div>

                <div class="rounded-xl bg-gradient-to-br from-gray-50 to-blue-50 p-6 md:p-8 mt-6 border border-gray-100">
                    <div class="flex items-center space-x-2 mb-6">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h4 class="text-base font-semibold text-gray-800">Profile Photo</h4>
                    </div>

                    <!-- Flash Message -->
                        @if (session()->has('message'))
                            <div 
                                x-data="{ show: true }" 
                                x-init="setTimeout(() => show = false, 3000)" 
                                x-show="show"
                                x-transition
                                class="mb-3 text-blue-600 text-md font-semibold"
                            >
                                {{ session('message') }}
                            </div>
                        @endif

                    <form wire:submit.prevent="updateProfile">
                        <div x-data="{ photoPreview: null }" class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">

                            <!-- Profile Image Preview -->
                            <div class="overflow-hidden rounded-full w-24 h-24 md:w-32 md:h-32 border-4 border-white shadow-lg flex-shrink-0 ring-2 ring-blue-100">

                                {{-- New Preview from Alpine (file selected) --}}
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" class="object-cover w-full h-full rounded-full" alt="New Profile Photo Preview">
                                </template>

                                {{-- Livewire Temporary Upload --}}
                                <template x-if="!photoPreview && @js($photo)">
                                    <img src="{{ $photo?->temporaryUrl() }}" class="object-cover w-full h-full rounded-full" alt="Preview">
                                </template>

                                {{-- Saved Profile Photo --}}
                                <template x-if="!photoPreview && !@js($photo) && @js($profile_photo_path)">
                                    <img src="{{ $profile_photo_path ? asset('storage/' . $profile_photo_path) : '' }}" class="object-cover w-full h-full rounded-full" alt="Profile">
                                </template>

                                {{-- Default Avatar --}}
                                <template x-if="!photoPreview && !@js($photo) && !@js($profile_photo_path)">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=7F9CF5&background=EBF4FF"
                                        class="object-cover w-full h-full rounded-full" alt="Default">
                                </template>

                            </div>

                            <!-- Buttons -->
                            <div class="profile-btns flex flex-wrap items-center gap-2 w-full sm:w-auto">

                                @if ($profile_photo_path)
                                    <!-- Remove Button -->
                                    <button type="button"
                                            wire:click="removeProfilePhoto"
                                            class="px-4 md:px-5 py-2.5 text-sm md:text-base text-red-600 border-2 border-red-200 rounded-lg hover:bg-red-50 hover:border-red-300 transition-all duration-200 font-medium flex-1 sm:flex-initial shadow-sm">
                                        <i class="fa-solid fa-trash mr-2"></i>Remove
                                    </button>
                                @endif

                                <!-- Upload / Change Button -->
                                <label class="px-4 md:px-5 py-2.5 flex justify-center items-center text-sm md:text-base text-white bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg cursor-pointer hover:from-blue-600 hover:to-blue-700 transition-all duration-200 font-medium flex-1 sm:flex-initial shadow-md hover:shadow-lg">
                                    <i class="fa-solid fa-upload mr-2"></i>
                                    {{ $profile_photo_path ? 'Change Photo' : 'Add Photo' }}
                                    <input type="file" class="hidden" wire:model="photo" accept="image/*"
                                        x-on:change="
                                            const file = $event.target.files[0];
                                            if (file) {
                                                const reader = new FileReader();
                                                reader.onload = (e) => { photoPreview = e.target.result };
                                                reader.readAsDataURL(file);
                                            }
                                        ">
                                </label>

                                <!-- Save Button (only visible if new photo selected) -->
                                @if ($photo)
                                    <button type="button"
                                            wire:click="updateProfilePhoto"
                                            class="px-4 md:px-5 py-2.5 text-sm md:text-base bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all duration-200 font-medium flex-1 sm:flex-initial shadow-md hover:shadow-lg">
                                        <i class="fa-solid fa-check mr-2"></i>Save
                                    </button>
                                @endif

                            </div>
                        </div>
                    </form>

                    <!-- Form -->
                    <form wire:submit.prevent="updateProfile" class="grid mt-8 relative z-10">
                        <div x-data="{ editing: false }" class="relative max-w-[650px] space-y-6">

                                <!-- Flash Message -->
                                @if (session()->has('profile-message'))
                                    <div 
                                        x-data="{ show: true }" 
                                        x-init="setTimeout(() => show = false, 3000)" 
                                        x-show="show"
                                        x-transition
                                        class="mb-3 text-blue-600 text-md font-semibold"
                                    >
                                        {{ session('profile-message') }}
                                    </div>
                                @endif

                                <!-- First & Last Name -->
                                <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl p-5 md:p-6 border border-gray-200 shadow-sm">
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                                        <div class="flex flex-col sm:flex-row gap-4 sm:space-x-4 flex-1">
                                            <div class="flex-1">
                                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                                    <i class="fa-solid fa-user mr-2 text-blue-500"></i>First Name
                                                </label>
                                                <input type="text" wire:model.defer="first_name"
                                                    x-ref="first_name"
                                                    :readonly="!editing"
                                                    class="w-full border-2 rounded-lg px-4 py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                                    :class="editing ? 'bg-white border-gray-300 shadow-sm' : 'bg-gray-50 border-gray-200 cursor-not-allowed'">
                                                @error('first_name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                            </div>

                                            <div class="flex-1">
                                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                                    <i class="fa-solid fa-user mr-2 text-blue-500"></i>Last Name
                                                </label>
                                                <input type="text" wire:model.defer="last_name"
                                                    :readonly="!editing"
                                                    class="w-full border-2 rounded-lg px-4 py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                                    :class="editing ? 'bg-white border-gray-300 shadow-sm' : 'bg-gray-50 border-gray-200 cursor-not-allowed'">
                                                @error('last_name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                        </div>

                                        <!-- Button -->
                                        <div class="flex-shrink-0">
                                            <button type="button"  x-show="!editing" @click="editing = true; $nextTick(() => $refs.first_name.focus())" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 text-sm md:text-base shadow-md hover:shadow-lg">
                                                <i class="fa-solid fa-pen mr-2"></i>Edit
                                            </button>

                                            <button type="submit" x-show="editing" @click="editing = false" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 text-sm md:text-base shadow-md hover:shadow-lg">
                                                <i class="fa-solid fa-check mr-2"></i>Update
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl p-5 md:p-6 border border-gray-200 shadow-sm">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fa-solid fa-envelope mr-2 text-blue-500"></i>Email
                                    </label>
                                    <input type="email" readonly wire:model="email"
                                        class="w-full bg-gray-50 border-2 border-gray-200 rounded-lg px-4 py-3 text-sm md:text-base cursor-not-allowed">
                                </div>

                                <!-- Role -->
                                <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl p-5 md:p-6 border border-gray-200 shadow-sm">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fa-solid fa-user-tag mr-2 text-blue-500"></i>Role
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <span class="px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg font-semibold capitalize text-sm md:text-base shadow-md">
                                            <i class="fa-solid fa-shield-halved mr-2"></i>{{ Auth::user()->roles->pluck('name')->join(', ') ?: 'No role assigned' }}
                                        </span>
                                    </div>
                                </div>
                            
                        </div>
                    </form>

                    <!-- Crew Profile Section -->
                    <div class="mt-8 md:mt-10 border-t-2 border-gray-200 pt-8 md:pt-10">
                        <div class="flex items-center space-x-3 mb-6 md:mb-8">
                            <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                                <i class="fa-solid fa-ship text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl md:text-2xl font-bold text-gray-900">Crew Profile Information</h3>
                                <p class="text-sm text-gray-500 mt-1">Complete your professional profile</p>
                            </div>
                        </div>
                        
                        <form wire:submit.prevent="updateCrewProfile" class="space-y-6">
                            <!-- Years of Experience & Current Yacht -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                                <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <div class="p-2 bg-yellow-100 rounded-lg mr-2">
                                            <i class="fa-solid fa-star text-yellow-600"></i>
                                        </div>
                                        Years of Experience
                                    </label>
                                    <input type="number" wire:model.defer="years_experience" min="0" max="100"
                                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm">
                                    @error('years_experience') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <div class="p-2 bg-blue-100 rounded-lg mr-2">
                                            <i class="fa-solid fa-ship text-blue-600"></i>
                                        </div>
                                        Current Yacht
                                    </label>
                                    <select wire:model.defer="current_yacht"
                                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm mb-3">
                                        <option value="">Select yacht...</option>
                                        @foreach($yachts as $yacht)
                                            <option value="{{ $yacht->name }}">{{ $yacht->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="date" wire:model.defer="current_yacht_start_date"
                                        placeholder="Start Date"
                                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm">
                                    @error('current_yacht') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                    @error('current_yacht_start_date') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Sea Service Time -->
                            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                                <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                    <div class="p-2 bg-purple-100 rounded-lg mr-2">
                                        <i class="fa-solid fa-clock text-purple-600"></i>
                                    </div>
                                    Sea Service Time (Months)
                                </label>
                                <input type="number" wire:model.defer="sea_service_time_months" min="0"
                                    class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm">
                                @error('sea_service_time_months') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Availability Status -->
                            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                                <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                    <div class="p-2 bg-green-100 rounded-lg mr-2">
                                        <i class="fa-solid fa-circle-check text-green-600"></i>
                                    </div>
                                    Availability Status
                                </label>
                                <select wire:model.defer="availability_status"
                                    class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm">
                                    <option value="">Select status...</option>
                                    <option value="available">Available</option>
                                    <option value="busy">Busy</option>
                                    <option value="looking_for_work">Looking for Work</option>
                                    <option value="on_leave">On Leave</option>
                                </select>
                                @error('availability_status') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Availability Message -->
                            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                                <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                    <div class="p-2 bg-indigo-100 rounded-lg mr-2">
                                        <i class="fa-solid fa-message text-indigo-600"></i>
                                    </div>
                                    Availability Message
                                </label>
                                <textarea wire:model.defer="availability_message" rows="3" maxlength="500"
                                    placeholder="Tell others about your availability..."
                                    class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm resize-none"></textarea>
                                @error('availability_message') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Looking For -->
                            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                                <label class="block text-sm font-semibold text-gray-700 mb-4 flex items-center">
                                    <div class="p-2 bg-pink-100 rounded-lg mr-2">
                                        <i class="fa-solid fa-search text-pink-600"></i>
                                    </div>
                                    Looking For
                                </label>
                                <div class="space-y-3">
                                    <label class="flex items-center p-3 rounded-lg border-2 border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-all cursor-pointer">
                                        <input type="checkbox" wire:model.defer="looking_to_meet" class="mr-3 w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                        <span class="text-gray-700 font-medium">Looking to meet other crew members</span>
                                    </label>
                                    <label class="flex items-center p-3 rounded-lg border-2 border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-all cursor-pointer">
                                        <input type="checkbox" wire:model.defer="looking_for_work" class="mr-3 w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                        <span class="text-gray-700 font-medium">Looking for work opportunities</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Languages -->
                            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                                <label class="block text-sm font-semibold text-gray-700 mb-4 flex items-center">
                                    <div class="p-2 bg-blue-100 rounded-lg mr-2">
                                        <i class="fa-solid fa-language text-blue-600"></i>
                                    </div>
                                    Languages
                                </label>
                                <div class="flex gap-2 mb-4">
                                    <input type="text" wire:model="newLanguage" 
                                        placeholder="Add language (e.g., English)"
                                        class="flex-1 border-2 border-gray-300 rounded-lg px-4 py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm"
                                        wire:keydown.enter.prevent="addLanguage">
                                    <button type="button" wire:click="addLanguage"
                                        class="px-5 py-3 text-sm md:text-base bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 whitespace-nowrap transition-all duration-200 shadow-md hover:shadow-lg font-medium">
                                        <i class="fa-solid fa-plus mr-1"></i> <span class="hidden sm:inline">Add</span>
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($languages as $index => $language)
                                        <span class="px-4 py-2 bg-gradient-to-r from-blue-50 to-blue-100 text-blue-700 rounded-full text-sm font-medium flex items-center gap-2 shadow-sm border border-blue-200">
                                            <i class="fa-solid fa-language text-blue-600"></i>
                                            {{ $language }}
                                            <button type="button" wire:click="removeLanguage({{ $index }})" class="text-blue-700 hover:text-red-600 transition-colors ml-1">
                                                <i class="fa-solid fa-times text-xs"></i>
                                            </button>
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Certifications -->
                            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                                <label class="block text-sm font-semibold text-gray-700 mb-4 flex items-center">
                                    <div class="p-2 bg-green-100 rounded-lg mr-2">
                                        <i class="fa-solid fa-certificate text-green-600"></i>
                                    </div>
                                    Certifications
                                </label>
                                <div class="flex gap-2 mb-4">
                                    <input type="text" wire:model="newCertification" 
                                        placeholder="Add certification (e.g., STCW)"
                                        class="flex-1 border-2 border-gray-300 rounded-lg px-4 py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all shadow-sm"
                                        wire:keydown.enter.prevent="addCertification">
                                    <button type="button" wire:click="addCertification"
                                        class="px-5 py-3 text-sm md:text-base bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 whitespace-nowrap transition-all duration-200 shadow-md hover:shadow-lg font-medium">
                                        <i class="fa-solid fa-plus mr-1"></i> <span class="hidden sm:inline">Add</span>
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($certifications as $index => $cert)
                                        <span class="px-4 py-2 bg-gradient-to-r from-green-50 to-green-100 text-green-700 rounded-full text-sm font-medium flex items-center gap-2 shadow-sm border border-green-200">
                                            <i class="fa-solid fa-certificate text-green-600"></i>
                                            {{ $cert }}
                                            <button type="button" wire:click="removeCertification({{ $index }})" class="text-green-700 hover:text-red-600 transition-colors ml-1">
                                                <i class="fa-solid fa-times text-xs"></i>
                                            </button>
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Specializations -->
                            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                                <label class="block text-sm font-semibold text-gray-700 mb-4 flex items-center">
                                    <div class="p-2 bg-purple-100 rounded-lg mr-2">
                                        <i class="fa-solid fa-tools text-purple-600"></i>
                                    </div>
                                    Specializations
                                </label>
                                <div class="flex gap-2 mb-4">
                                    <input type="text" wire:model="newSpecialization" 
                                        placeholder="Add specialization"
                                        class="flex-1 border-2 border-gray-300 rounded-lg px-4 py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all shadow-sm"
                                        wire:keydown.enter.prevent="addSpecialization">
                                    <button type="button" wire:click="addSpecialization"
                                        class="px-5 py-3 text-sm md:text-base bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 whitespace-nowrap transition-all duration-200 shadow-md hover:shadow-lg font-medium">
                                        <i class="fa-solid fa-plus mr-1"></i> <span class="hidden sm:inline">Add</span>
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($specializations as $index => $spec)
                                        <span class="px-4 py-2 bg-gradient-to-r from-purple-50 to-purple-100 text-purple-700 rounded-full text-sm font-medium flex items-center gap-2 shadow-sm border border-purple-200">
                                            <i class="fa-solid fa-tools text-purple-600"></i>
                                            {{ $spec }}
                                            <button type="button" wire:click="removeSpecialization({{ $index }})" class="text-purple-700 hover:text-red-600 transition-colors ml-1">
                                                <i class="fa-solid fa-times text-xs"></i>
                                            </button>
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Interests -->
                            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                                <label class="block text-sm font-semibold text-gray-700 mb-4 flex items-center">
                                    <div class="p-2 bg-pink-100 rounded-lg mr-2">
                                        <i class="fa-solid fa-heart text-pink-600"></i>
                                    </div>
                                    Interests
                                </label>
                                <div class="flex gap-2 mb-4">
                                    <input type="text" wire:model="newInterest" 
                                        placeholder="Add interest"
                                        class="flex-1 border-2 border-gray-300 rounded-lg px-4 py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all shadow-sm"
                                        wire:keydown.enter.prevent="addInterest">
                                    <button type="button" wire:click="addInterest"
                                        class="px-5 py-3 text-sm md:text-base bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-lg hover:from-pink-600 hover:to-pink-700 whitespace-nowrap transition-all duration-200 shadow-md hover:shadow-lg font-medium">
                                        <i class="fa-solid fa-plus mr-1"></i> <span class="hidden sm:inline">Add</span>
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($interests as $index => $interest)
                                        <span class="px-4 py-2 bg-gradient-to-r from-pink-50 to-pink-100 text-pink-700 rounded-full text-sm font-medium flex items-center gap-2 shadow-sm border border-pink-200">
                                            <i class="fa-solid fa-heart text-pink-600"></i>
                                            {{ $interest }}
                                            <button type="button" wire:click="removeInterest({{ $index }})" class="text-pink-700 hover:text-red-600 transition-colors ml-1">
                                                <i class="fa-solid fa-times text-xs"></i>
                                            </button>
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Previous Yachts -->
                            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                                <label class="block text-sm font-semibold text-gray-700 mb-4 flex items-center">
                                    <div class="p-2 bg-indigo-100 rounded-lg mr-2">
                                        <i class="fa-solid fa-ship text-indigo-600"></i>
                                    </div>
                                    Previous Yachts
                                </label>
                                <div class="space-y-2 mb-4">
                                    @if(session()->has('yacht-error'))
                                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-2 rounded text-sm">
                                            {{ session('yacht-error') }}
                                        </div>
                                    @endif
                                    
                                    <select wire:model.live="newPreviousYachtId" 
                                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm">
                                        <option value="">Select yacht...</option>
                                        @foreach($yachts as $yacht)
                                            <option value="{{ $yacht->id }}">{{ $yacht->name }}</option>
                                        @endforeach
                                        <option value="other">Other (Manual Entry)</option>
                                    </select>
                                    
                                    @if($showOtherInput)
                                        <div class="mt-2">
                                            <label class="block text-xs text-gray-600 mb-1 font-medium">Enter Yacht Name</label>
                                            <input type="text" wire:model="newPreviousYachtName" 
                                                placeholder="e.g., M/Y Ocean Dream, S/Y Wind Dancer"
                                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm">
                                        </div>
                                    @endif
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <div>
                                            <input type="date" wire:model="newPreviousYachtStartDate" 
                                                placeholder="Start Date"
                                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm"
                                                x-on:change="$wire.set('newPreviousYachtEndDate', '')">
                                            <label class="text-xs font-medium text-gray-600 mt-2 block">Start Date</label>
                                        </div>
                                        <div>
                                            <input type="date" wire:model="newPreviousYachtEndDate" 
                                                placeholder="End Date"
                                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm"
                                                @if($newPreviousYachtStartDate) min="{{ $newPreviousYachtStartDate }}" @endif>
                                            <label class="text-xs font-medium text-gray-600 mt-2 block">End Date</label>
                                        </div>
                                    </div>
                                    
                                    <button type="button" wire:click="addPreviousYacht"
                                        class="w-full px-5 py-3 text-sm md:text-base bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-lg hover:from-indigo-600 hover:to-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg font-medium">
                                        <i class="fa-solid fa-plus mr-2"></i> Add Yacht
                                    </button>
                                </div>
                                
                                <div class="space-y-2">
                                    @foreach($previous_yachts as $index => $yacht)
                                        @php
                                            $yachtName = is_array($yacht) ? ($yacht['name'] ?? '') : $yacht;
                                            $startDate = is_array($yacht) && !empty($yacht['start_date']) ? \Carbon\Carbon::parse($yacht['start_date']) : null;
                                            $endDate = is_array($yacht) && !empty($yacht['end_date']) ? \Carbon\Carbon::parse($yacht['end_date']) : null;
                                            $isInvalid = $startDate && $endDate && $endDate->lt($startDate);
                                        @endphp
                                        <div class="bg-gradient-to-r from-gray-50 to-indigo-50 border-2 {{ $isInvalid ? 'border-red-300 bg-red-50' : 'border-indigo-200' }} rounded-xl p-4 flex items-center justify-between shadow-sm hover:shadow-md transition-all">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2 mb-2">
                                                    <i class="fa-solid fa-ship text-indigo-600"></i>
                                                    <div class="font-semibold text-gray-900">{{ $yachtName }}</div>
                                                </div>
                                                @if($startDate || $endDate)
                                                    <div class="text-sm {{ $isInvalid ? 'text-red-600' : 'text-gray-600' }} flex items-center space-x-2">
                                                        @if($startDate)
                                                            <span class="px-2 py-1 bg-white rounded text-xs font-medium">Start: {{ $startDate->format('M Y') }}</span>
                                                        @endif
                                                        @if($startDate && $endDate)
                                                            <i class="fa-solid fa-arrow-right text-xs"></i>
                                                        @endif
                                                        @if($endDate)
                                                            <span class="px-2 py-1 bg-white rounded text-xs font-medium">End: {{ $endDate->format('M Y') }}</span>
                                                        @endif
                                                        @if($isInvalid)
                                                            <span class="ml-2 px-2 py-1 bg-red-100 text-red-600 rounded text-xs font-semibold">Invalid dates</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            <button type="button" wire:click="removePreviousYacht({{ $index }})" 
                                                class="ml-4 p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                                <i class="fa-solid fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-6 border-t-2 border-gray-200">
                                <button type="submit" 
                                    class="w-full sm:w-auto px-8 py-3.5 text-base md:text-lg bg-gradient-to-r from-[#0053FF] to-[#0046CC] text-white rounded-xl hover:from-[#0046CC] hover:to-[#003399] transition-all duration-200 font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <i class="fa-solid fa-save mr-2"></i>Save Crew Profile
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>


    </div>
</main>
       