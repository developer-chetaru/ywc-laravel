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

            @unlessrole('super_admin')
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
            @endunlessrole
        </div>


        <div class="bg-white p-5 rounded-lg shadow-md col-span-3">
            <div class="w-full">
                <h2 class="text-xl border-b border-gray-100 font-medium text-[#0053FF] pb-2">Your profile</h2>

                <div class="rounded-lg mt-6">
                    <h4 class="mb-4 text-[14px] text-[#616161]">Profile Photo</h4>

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
                        <div x-data="{ photoPreview: null }" class="flex justify-between items-center flex-wrap">

                            <!-- Profile Image Preview -->
                            <div class="overflow-hidden rounded-full w-[80px] h-[80px] border">

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
                            <div class="profile-btns flex items-center space-x-2 mt-2 sm:mt-0">

                                @if ($profile_photo_path)
                                    <!-- Remove Button -->
                                    <button type="button"
                                            wire:click="removeProfilePhoto"
                                            class="px-4 py-2 text-gray-700 border rounded hover:bg-gray-100 transition">
                                        Remove
                                    </button>
                                @endif

                                <!-- Upload / Change Button -->
                                <label class="px-4 py-2 flex justify-center text-gray-700 border rounded cursor-pointer hover:bg-gray-100 transition">
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
                                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                        Save
                                    </button>
                                @endif

                            </div>
                        </div>
                    </form>

                    <!-- Form -->
                    <form wire:submit.prevent="updateProfile" class="grid mt-8 relative z-10">
                        <div x-data="{ editing: false }" class="relative max-w-[650px]">

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
                                <div class="flex justify-between items-center border-t py-5 border-[#eaeaea]">
                                    <div class="flex space-x-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                            <input type="text" wire:model.defer="first_name"
                                                x-ref="first_name"
                                                :readonly="!editing"
                                                class="w-full border-[#eaeaea] border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                :class="editing ? 'bg-white' : 'bg-gray-100 cursor-not-allowed'">
                                            @error('first_name') <span class="text-[#0053FF] text-md">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                            <input type="text" wire:model.defer="last_name"
                                                :readonly="!editing"
                                                class="w-full border-[#eaeaea] border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                :class="editing ? 'bg-white' : 'bg-gray-100 cursor-not-allowed'">
                                            @error('last_name') <span class="text-[#0053FF] text-md">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Button -->
                                    <button type="button"  x-show="!editing" @click="editing = true; $nextTick(() => $refs.first_name.focus())" class="px-4 py-2 bg-blue-500 text-white font-medium rounded-sm hover:bg-blue-600 transition">
                                        Edit
                                    </button>

                                    <button type="submit" x-show="editing" @click="editing = false" class="px-4 py-2 bg-blue-500 text-white font-medium rounded-sm hover:bg-blue-600 transition">
                                        Update
                                    </button>
                                </div>

                                <!-- Email -->
                                <div class="md:col-span-2 border-t py-5 border-[#eaeaea] flex flex-col">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" readonly wire:model="email"
                                        class="w-full bg-gray-100 border border-[#eaeaea] rounded px-4 py-2 cursor-not-allowed">
                                </div>

                                <!-- Role -->
                                <div class="md:col-span-2 border-t py-5 border-[#eaeaea] flex flex-col">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                    <div class="flex items-center gap-2">
                                        <span class="px-4 py-2 bg-blue-100 text-blue-700 rounded font-medium capitalize">
                                            {{ Auth::user()->roles->pluck('name')->join(', ') ?: 'No role assigned' }}
                                        </span>
                                    </div>
                                </div>
                            
                        </div>
                    </form>

                    <!-- Crew Profile Section -->
                    <div class="mt-8 border-t border-gray-200 pt-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Crew Profile Information</h3>
                        
                        <form wire:submit.prevent="updateCrewProfile" class="space-y-6">
                            <!-- Years of Experience & Current Yacht -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fa-solid fa-star mr-1"></i>Years of Experience
                                    </label>
                                    <input type="number" wire:model.defer="years_experience" min="0" max="100"
                                        class="w-full border border-[#eaeaea] rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('years_experience') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fa-solid fa-ship mr-1"></i>Current Yacht
                                    </label>
                                    <select wire:model.defer="current_yacht"
                                        class="w-full border border-[#eaeaea] rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-2">
                                        <option value="">Select yacht...</option>
                                        @foreach($yachts as $yacht)
                                            <option value="{{ $yacht->name }}">{{ $yacht->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="date" wire:model.defer="current_yacht_start_date"
                                        placeholder="Start Date"
                                        class="w-full border border-[#eaeaea] rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('current_yacht') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    @error('current_yacht_start_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Sea Service Time -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fa-solid fa-clock mr-1"></i>Sea Service Time (Months)
                                </label>
                                <input type="number" wire:model.defer="sea_service_time_months" min="0"
                                    class="w-full border border-[#eaeaea] rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('sea_service_time_months') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Availability Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fa-solid fa-circle-check mr-1"></i>Availability Status
                                </label>
                                <select wire:model.defer="availability_status"
                                    class="w-full border border-[#eaeaea] rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select status...</option>
                                    <option value="available">Available</option>
                                    <option value="busy">Busy</option>
                                    <option value="looking_for_work">Looking for Work</option>
                                    <option value="on_leave">On Leave</option>
                                </select>
                                @error('availability_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Availability Message -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fa-solid fa-message mr-1"></i>Availability Message
                                </label>
                                <textarea wire:model.defer="availability_message" rows="3" maxlength="500"
                                    placeholder="Tell others about your availability..."
                                    class="w-full border border-[#eaeaea] rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                @error('availability_message') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Looking For -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fa-solid fa-search mr-1"></i>Looking For
                                </label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model.defer="looking_to_meet" class="mr-2">
                                        <span>Looking to meet other crew members</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model.defer="looking_for_work" class="mr-2">
                                        <span>Looking for work opportunities</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Languages -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fa-solid fa-language mr-1"></i>Languages
                                </label>
                                <div class="flex gap-2 mb-2">
                                    <input type="text" wire:model="newLanguage" 
                                        placeholder="Add language (e.g., English)"
                                        class="flex-1 border border-[#eaeaea] rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        wire:keydown.enter.prevent="addLanguage">
                                    <button type="button" wire:click="addLanguage"
                                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                        <i class="fa-solid fa-plus"></i> Add
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($languages as $index => $language)
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm flex items-center gap-2">
                                            {{ $language }}
                                            <button type="button" wire:click="removeLanguage({{ $index }})" class="text-blue-700 hover:text-blue-900">
                                                <i class="fa-solid fa-times text-xs"></i>
                                            </button>
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Certifications -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fa-solid fa-certificate mr-1"></i>Certifications
                                </label>
                                <div class="flex gap-2 mb-2">
                                    <input type="text" wire:model="newCertification" 
                                        placeholder="Add certification (e.g., STCW)"
                                        class="flex-1 border border-[#eaeaea] rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        wire:keydown.enter.prevent="addCertification">
                                    <button type="button" wire:click="addCertification"
                                        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                        <i class="fa-solid fa-plus"></i> Add
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($certifications as $index => $cert)
                                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm flex items-center gap-2">
                                            {{ $cert }}
                                            <button type="button" wire:click="removeCertification({{ $index }})" class="text-green-700 hover:text-green-900">
                                                <i class="fa-solid fa-times text-xs"></i>
                                            </button>
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Specializations -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fa-solid fa-tools mr-1"></i>Specializations
                                </label>
                                <div class="flex gap-2 mb-2">
                                    <input type="text" wire:model="newSpecialization" 
                                        placeholder="Add specialization"
                                        class="flex-1 border border-[#eaeaea] rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        wire:keydown.enter.prevent="addSpecialization">
                                    <button type="button" wire:click="addSpecialization"
                                        class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                                        <i class="fa-solid fa-plus"></i> Add
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($specializations as $index => $spec)
                                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm flex items-center gap-2">
                                            {{ $spec }}
                                            <button type="button" wire:click="removeSpecialization({{ $index }})" class="text-purple-700 hover:text-purple-900">
                                                <i class="fa-solid fa-times text-xs"></i>
                                            </button>
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Interests -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fa-solid fa-heart mr-1"></i>Interests
                                </label>
                                <div class="flex gap-2 mb-2">
                                    <input type="text" wire:model="newInterest" 
                                        placeholder="Add interest"
                                        class="flex-1 border border-[#eaeaea] rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        wire:keydown.enter.prevent="addInterest">
                                    <button type="button" wire:click="addInterest"
                                        class="px-4 py-2 bg-pink-500 text-white rounded hover:bg-pink-600">
                                        <i class="fa-solid fa-plus"></i> Add
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($interests as $index => $interest)
                                        <span class="px-3 py-1 bg-pink-100 text-pink-700 rounded-full text-sm flex items-center gap-2">
                                            {{ $interest }}
                                            <button type="button" wire:click="removeInterest({{ $index }})" class="text-pink-700 hover:text-pink-900">
                                                <i class="fa-solid fa-times text-xs"></i>
                                            </button>
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Previous Yachts -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fa-solid fa-ship mr-1"></i>Previous Yachts
                                </label>
                                <div class="space-y-2 mb-4">
                                    @if(session()->has('yacht-error'))
                                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-2 rounded text-sm">
                                            {{ session('yacht-error') }}
                                        </div>
                                    @endif
                                    
                                    <select wire:model.live="newPreviousYachtId" 
                                        class="w-full border border-[#eaeaea] rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                                                class="w-full border border-[#eaeaea] rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                    @endif
                                    
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <input type="date" wire:model="newPreviousYachtStartDate" 
                                                placeholder="Start Date"
                                                class="w-full border border-[#eaeaea] rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                x-on:change="$wire.set('newPreviousYachtEndDate', '')">
                                            <label class="text-xs text-gray-500 mt-1 block">Start Date</label>
                                        </div>
                                        <div>
                                            <input type="date" wire:model="newPreviousYachtEndDate" 
                                                placeholder="End Date"
                                                class="w-full border border-[#eaeaea] rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                @if($newPreviousYachtStartDate) min="{{ $newPreviousYachtStartDate }}" @endif>
                                            <label class="text-xs text-gray-500 mt-1 block">End Date</label>
                                        </div>
                                    </div>
                                    
                                    <button type="button" wire:click="addPreviousYacht"
                                        class="w-full px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                                        <i class="fa-solid fa-plus"></i> Add
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
                                        <div class="bg-gray-50 border {{ $isInvalid ? 'border-red-300' : 'border-gray-200' }} rounded-lg p-3 flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="font-medium text-gray-900">{{ $yachtName }}</div>
                                                @if($startDate || $endDate)
                                                    <div class="text-sm {{ $isInvalid ? 'text-red-600' : 'text-gray-600' }} mt-1">
                                                        @if($startDate)
                                                            <span>Start: {{ $startDate->format('M Y') }}</span>
                                                        @endif
                                                        @if($startDate && $endDate)
                                                            <span class="mx-2">-</span>
                                                        @endif
                                                        @if($endDate)
                                                            <span>End: {{ $endDate->format('M Y') }}</span>
                                                        @endif
                                                        @if($isInvalid)
                                                            <span class="ml-2 text-red-600 font-semibold">(Invalid dates)</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            <button type="button" wire:click="removePreviousYacht({{ $index }})" 
                                                class="ml-4 text-gray-500 hover:text-red-600 transition-colors">
                                                <i class="fa-solid fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4 border-t border-gray-200">
                                <button type="submit" 
                                    class="px-6 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-[#0046CC] transition font-medium">
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
       