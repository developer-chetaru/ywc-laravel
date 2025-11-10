<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        Account Information
    </x-slot>

    <x-slot name="description">
        {{-- <hr> --}}
    </x-slot>

    <x-slot name="form">

        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
        <div class="flex justify-center mb-6 col-span-6 sm:col-span-6">
          <div x-data="{ photoPreview: null }" class="relative">

            {{-- Current Profile Photo / New Preview --}}
            <template x-if="photoPreview">
              <img :src="photoPreview" class="h-24 w-24 rounded-full object-cover" alt="New Profile Photo Preview">
            </template>
            <template x-if="!photoPreview">
              <img class="h-24 w-24 rounded-full object-cover"
           src="{{ $this->user->profile_photo_path 
                  ? asset('storage/' . $this->user->profile_photo_path) 
                  : 'https://ui-avatars.com/api/?name='.urlencode($this->user->name).'&color=7F9CF5&background=EBF4FF' }}"
           alt="{{ $this->user->name }}">
            </template>

            {{-- Upload Button --}}
            <label for="photo" class="absolute bottom-0 right-0 bg-blue-600 text-white p-1 rounded-full cursor-pointer hover:bg-blue-700 transition duration-200">
              ✎
            </label>

            {{-- File Input --}}
            <input id="photo" type="file" class="hidden" wire:model="photo"
                   x-on:change="
                                const file = $event.target.files[0];
                                if (file) {
                                const reader = new FileReader();
                                reader.onload = (e) => { photoPreview = e.target.result; };
                                reader.readAsDataURL(file);
                                }
                                ">

          </div>

          {{-- Error --}}
          @error('photo') <span class="text-blue-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>
        @endif
  		
      
        <!-- First Name -->
        <div class="col-span-6 sm:col-span-3">
            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
            <input type="text" id="first_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" wire:model.defer="state.first_name" autocomplete="name">
            <x-input-error for="first_name" class="mt-2" />
        </div>

        <!-- Last Name -->
        <div class="col-span-6 sm:col-span-3">
            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
            <input type="text" id="last_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" wire:model.defer="state.last_name" autocomplete="last_name">
            <x-input-error for="last_name" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-6">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm cursor-not-allowed" wire:model.defer="state.email" disabled />
            <x-input-error for="email" class="mt-2" />
        </div>
      
      
      <div class="col-span-6 sm:col-span-6">
          <x-action-message on="saved" class="text-blue-600">
              Profile updated successfully!
          </x-action-message>

          <x-action-message on="error" class="text-blue-600">
              Something went wrong.
          </x-action-message>
      </div>

    </x-slot>

   		
<x-slot name="actions">
    <div class="w-full flex justify-start -mt-6">
        <x-button class="bg-blue-600 hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 text-white">
            {{ __('Save') }}
        </x-button>
    </div>
</x-slot>
</x-form-section>
