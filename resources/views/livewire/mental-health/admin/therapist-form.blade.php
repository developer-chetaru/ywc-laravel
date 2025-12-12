<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('mental-health.admin.therapists') }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-800">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Therapist Management
            </a>
        </div>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                {{ $isEditing ? 'Edit Therapist' : 'Create New Therapist' }}
            </h1>
            <p class="mt-2 text-gray-600">
                {{ $isEditing ? 'Update therapist information' : 'Add a new therapist to the platform' }}
            </p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form wire:submit.prevent="save" class="space-y-6">
                <!-- User Selection/Creation -->
                <div class="border-b pb-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">User Information</h2>
                    
                    @if(!$isEditing)
                        <label class="flex items-center mb-4">
                            <input type="checkbox" wire:model.live="createNewUser" class="mr-2">
                            <span class="text-sm font-medium text-gray-700">Create new user account</span>
                        </label>
                    @endif

                    @if($createNewUser || $isEditing)
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                                <input type="text" wire:model="first_name" 
                                       class="w-full rounded-md border-gray-300 shadow-sm">
                                @error('first_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                                <input type="text" wire:model="last_name" 
                                       class="w-full rounded-md border-gray-300 shadow-sm">
                                @error('last_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                <input type="email" wire:model="email" 
                                       class="w-full rounded-md border-gray-300 shadow-sm">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Password {{ $isEditing ? '(leave blank to keep current)' : '*' }}
                                </label>
                                <input type="password" wire:model="password" 
                                       class="w-full rounded-md border-gray-300 shadow-sm">
                                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" wire:model="phone" 
                                       class="w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>
                    @else
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Select User *</label>
                            <select wire:model="user_id" class="w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Choose a user...</option>
                                @foreach($availableUsers as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>

                <!-- Therapist Information -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Therapist Information</h2>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Biography</label>
                        <textarea wire:model="biography" rows="4" 
                                  class="w-full rounded-md border-gray-300 shadow-sm"></textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Years Experience</label>
                            <input type="number" wire:model="years_experience" min="0" 
                                   class="w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Base Hourly Rate (£)</label>
                            <input type="number" wire:model="base_hourly_rate" step="0.01" min="0" 
                                   class="w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                            <input type="text" wire:model="timezone" 
                                   placeholder="e.g., Europe/London"
                                   class="w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>

                    <!-- Specializations -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Specializations</label>
                        <div class="flex gap-2 mb-2">
                            <input type="text" wire:model="specializationInput" 
                                   placeholder="e.g., anxiety, depression, trauma"
                                   class="flex-1 rounded-md border-gray-300 shadow-sm">
                            <button type="button" wire:click="addSpecialization" 
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md">
                                Add
                            </button>
                        </div>
                        @if(count($specializations) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($specializations as $spec)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm flex items-center">
                                        {{ $spec }}
                                        <button type="button" wire:click="removeSpecialization('{{ $spec }}')" 
                                                class="ml-2 text-blue-600 hover:text-blue-800">×</button>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Languages -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Languages Spoken</label>
                        <div class="flex gap-2 mb-2">
                            <input type="text" wire:model="languageInput" 
                                   placeholder="e.g., English, Spanish, French"
                                   class="flex-1 rounded-md border-gray-300 shadow-sm">
                            <button type="button" wire:click="addLanguage" 
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md">
                                Add
                            </button>
                        </div>
                        @if(count($languages_spoken) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($languages_spoken as $lang)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm flex items-center">
                                        {{ $lang }}
                                        <button type="button" wire:click="removeLanguage('{{ $lang }}')" 
                                                class="ml-2 text-green-600 hover:text-green-800">×</button>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Therapeutic Approaches -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Therapeutic Approaches</label>
                        <div class="flex gap-2 mb-2">
                            <input type="text" wire:model="approachInput" 
                                   placeholder="e.g., CBT, DBT, Psychodynamic"
                                   class="flex-1 rounded-md border-gray-300 shadow-sm">
                            <button type="button" wire:click="addApproach" 
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md">
                                Add
                            </button>
                        </div>
                        @if(count($therapeutic_approaches) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($therapeutic_approaches as $approach)
                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-sm flex items-center">
                                        {{ $approach }}
                                        <button type="button" wire:click="removeApproach('{{ $approach }}')" 
                                                class="ml-2 text-purple-600 hover:text-purple-800">×</button>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Status -->
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Application Status</label>
                            <select wire:model="application_status" class="w-full rounded-md border-gray-300 shadow-sm">
                                <option value="pending">Pending</option>
                                <option value="under_review">Under Review</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="flex items-center pt-6">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="is_active" class="mr-2">
                                <span class="text-sm text-gray-700">Active</span>
                            </label>
                        </div>
                        <div class="flex items-center pt-6">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="is_featured" class="mr-2">
                                <span class="text-sm text-gray-700">Featured</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-2 pt-4 border-t">
                    <a href="{{ route('mental-health.admin.therapists') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                        {{ $isEditing ? 'Update Therapist' : 'Create Therapist' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

