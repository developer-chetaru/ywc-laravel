<div class="min-h-screen py-4 px-4 sm:px-6 lg:px-8" 
     x-data="{ currentStep: 1, totalSteps: 3 }"
     x-init="console.log('=== CreateForum Component Initialized ==='); console.log('Alpine.js loaded:', typeof Alpine !== 'undefined'); console.log('Livewire available:', typeof $wire !== 'undefined'); console.log('Current step:', currentStep);"
     @console-log.window="console.log($event.detail.message)">
    <div class="max-w-4xl mx-auto">
    <!-- 🔹 Back Button -->
    <div class="flex gap-4 mb-4 sm:mb-6">
        <a href="{{ Forum::route('category.index') }}" 
           class="backToChat cursor-pointer bg-white border border-gray-300 flex gap-2 justify-center items-center px-3 sm:px-4 py-2 rounded-md text-gray-600 hover:text-blue-600 hover:border-blue-600 transition text-sm">
            <img class="h-3" src="/images/left-arr.svg" alt="">
            <span class="hidden sm:inline">Back to Department Forums</span>
            <span class="sm:hidden">Back</span>
        </a>
    </div>

    {{-- ✅ Success message --}}
    @if (session()->has('success'))
        <div id="successMessage"
            class="mb-4 p-4 text-green-800 bg-green-50 border-l-4 border-green-500 rounded-lg flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle text-green-500 text-xl"></i>
            <div class="flex-1">
                <p class="font-semibold">{{ session('success') }}</p>
            </div>
            <button onclick="document.getElementById('successMessage').remove()" class="text-green-600 hover:text-green-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Error message --}}
    @if (session()->has('error'))
        <div class="mb-4 p-4 text-red-800 bg-red-50 border-l-4 border-red-500 rounded-lg flex items-center gap-3 shadow-sm">
            <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
            <div class="flex-1">
                <p class="font-semibold">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Validation Errors Summary --}}
    @error('title')
        <div class="mb-4 p-4 text-red-800 bg-red-50 border-l-4 border-red-500 rounded-lg shadow-sm">
            <p class="font-semibold">Validation Error: {{ $message }}</p>
        </div>
    @enderror

    <!-- Progress Steps -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0 mb-4">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Create New Forum</h2>
            <span class="text-xs sm:text-sm text-gray-500">Step <span x-text="currentStep"></span> of <span x-text="totalSteps"></span></span>
        </div>
        
        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                 :style="'width: ' + (currentStep / totalSteps * 100) + '%'"></div>
        </div>
        
        <!-- Step Indicators -->
        <div class="flex items-center justify-between gap-1 sm:gap-2">
            <div class="flex items-center gap-1 sm:gap-2 flex-1" :class="currentStep >= 1 ? 'text-blue-600' : 'text-gray-400'">
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center font-semibold text-xs sm:text-sm flex-shrink-0"
                     :class="currentStep >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500'">
                    <span x-show="currentStep > 1"><i class="fas fa-check text-[10px] sm:text-xs"></i></span>
                    <span x-show="currentStep <= 1">1</span>
                </div>
                <span class="text-xs sm:text-sm font-medium hidden md:inline">Basic Info</span>
            </div>
            
            <div class="flex-1 h-0.5 mx-1 sm:mx-2" :class="currentStep >= 2 ? 'bg-blue-600' : 'bg-gray-200'"></div>
            
            <div class="flex items-center gap-1 sm:gap-2 flex-1" :class="currentStep >= 2 ? 'text-blue-600' : 'text-gray-400'">
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center font-semibold text-xs sm:text-sm flex-shrink-0"
                     :class="currentStep >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500'">
                    <span x-show="currentStep > 2"><i class="fas fa-check text-[10px] sm:text-xs"></i></span>
                    <span x-show="currentStep <= 2">2</span>
                </div>
                <span class="text-xs sm:text-sm font-medium hidden md:inline">Thread Setup</span>
            </div>
            
            <div class="flex-1 h-0.5 mx-1 sm:mx-2" :class="currentStep >= 3 ? 'bg-blue-600' : 'bg-gray-200'"></div>
            
            <div class="flex items-center gap-1 sm:gap-2 flex-1" :class="currentStep >= 3 ? 'text-blue-600' : 'text-gray-400'">
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center font-semibold text-xs sm:text-sm flex-shrink-0"
                     :class="currentStep >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500'">
                    3
                </div>
                <span class="text-xs sm:text-sm font-medium hidden md:inline">Access Control</span>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="store" 
        onsubmit="console.log('=== FORM SUBMIT EVENT (onsubmit) ==='); console.log('Form submitting...'); return false;"
        x-on:submit.prevent="console.log('=== FORM SUBMIT EVENT (Alpine) ==='); console.log('Title:', $wire.title); console.log('Description:', $wire.description);"
        class="w-full bg-white rounded-lg border border-gray-200 shadow-lg overflow-hidden">

        <!-- Step 1: Basic Information -->
        <div x-show="currentStep === 1" class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
            <div class="mb-4 sm:mb-6">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-1 sm:mb-2">Basic Information</h3>
                <p class="text-xs sm:text-sm text-gray-600">Provide the essential details for your new forum.</p>
            </div>

            <div>
                <label class="block text-gray-900 text-sm font-semibold mb-2">
                    Forum Name <span class="text-red-500">*</span>
                    <span class="text-xs font-normal text-gray-500 ml-2" x-text="title.length + '/255'"></span>
                </label>
                <input type="text" 
                       wire:model.blur="title" 
                       placeholder="e.g. Onboard Safety Forum, Deck Department Discussions"
                       maxlength="255"
                       class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all
                       @error('title') border-red-500 @else border-gray-300 @enderror"
                       x-on:input="if ($el.value.trim()) { currentStep = 1; }">
                @error('title') 
                    <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>{{ $message }}
                    </p> 
                @enderror
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>Choose a clear, descriptive name that reflects the forum's purpose.
                </p>
            </div>

            <div>
                <label class="block text-gray-900 text-sm font-semibold mb-2">
                    Forum Description
                    <span class="text-xs font-normal text-gray-500 ml-2" x-text="(description?.length || 0) + '/500'"></span>
                </label>
                <textarea wire:model.blur="description" 
                          rows="4" 
                          placeholder="Briefly describe the purpose of this forum. What topics will be discussed? Who is it for?"
                          maxlength="500"
                          class="resize-none w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all
                          @error('description') border-red-500 @else border-gray-300 @enderror"></textarea>
                @error('description') 
                    <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>{{ $message }}
                    </p> 
                @enderror
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>Optional: Help users understand what this forum is about.
                </p>
            </div>

            <div>
                <label class="block text-gray-900 text-sm font-semibold mb-2">
                    Parent Category
                    <span class="text-xs font-normal text-gray-400 ml-2">(Optional)</span>
                </label>
                <select wire:model.blur="parent_category"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    <option value="">Top-Level Forum (No Parent)</option>
                    @foreach($availableCategories as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('parent_category') 
                    <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>{{ $message }}
                    </p> 
                @enderror
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>Organize this forum under an existing category, or leave as top-level.
                </p>
            </div>

            <div class="flex justify-end gap-2 sm:gap-3 pt-4 border-t">
                <button type="button" 
                        @click="currentStep = 2"
                        :disabled="!title.trim()"
                        class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium disabled:bg-gray-300 disabled:cursor-not-allowed flex items-center justify-center gap-2 text-sm sm:text-base">
                    <span class="hidden sm:inline">Next: Thread Setup</span>
                    <span class="sm:hidden">Next</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- Step 2: Thread Setup (Optional) -->
        <div x-show="currentStep === 2" class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
            <div class="mb-4 sm:mb-6">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-1 sm:mb-2">Initial Thread Setup</h3>
                <p class="text-xs sm:text-sm text-gray-600">Optionally create a starter thread to kick off discussions in your new forum.</p>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mb-6">
                <div class="flex items-start gap-3">
                    <i class="fas fa-lightbulb text-blue-500 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-medium text-blue-900">Optional Step</p>
                        <p class="text-xs text-blue-700 mt-1">You can create the first thread now, or do it later after the forum is created.</p>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-gray-900 text-sm font-semibold mb-2">
                    Thread Title
                    <span class="text-xs font-normal text-gray-500 ml-2" x-text="(threadTitle?.length || 0) + '/255'"></span>
                </label>
                <input type="text" 
                       wire:model.blur="threadTitle" 
                       placeholder="e.g. Welcome to our Safety Forum! How often should we conduct fire drills?"
                       maxlength="255"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white">
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>Create an engaging first thread to welcome members and start discussions.
                </p>
            </div>

            <div>
                <label class="block text-gray-900 text-sm font-semibold mb-2">
                    Thread Content / First Post
                    <span class="text-xs font-normal text-gray-500 ml-2" x-text="(threadDescription?.length || 0) + '/1000'"></span>
                </label>
                <textarea wire:model.blur="threadDescription" 
                          rows="5" 
                          placeholder="Enter the content for your first post. This will be the initial message in the thread..."
                          maxlength="1000"
                          class="resize-none w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white"></textarea>
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>If left empty, the thread title will be used as the post content.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row justify-between gap-2 sm:gap-3 pt-4 border-t">
                <button type="button" 
                        @click="currentStep = 1"
                        class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium flex items-center justify-center gap-2 text-sm sm:text-base">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </button>
                <button type="button" 
                        @click="currentStep = 3"
                        class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center justify-center gap-2 text-sm sm:text-base">
                    <span class="hidden sm:inline">Next: Access Control</span>
                    <span class="sm:hidden">Next</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- Step 3: Access Control -->
        <div x-show="currentStep === 3" class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
            <div class="mb-4 sm:mb-6">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-1 sm:mb-2">Access Control</h3>
                <p class="text-xs sm:text-sm text-gray-600">Control who can access this forum by assigning specific roles.</p>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg mb-6">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-yellow-500 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-medium text-yellow-900">Default: Public Access</p>
                        <p class="text-xs text-yellow-700 mt-1">If no roles are selected, the forum will be accessible to all users. Select specific roles to restrict access.</p>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-gray-900 text-sm font-semibold mb-3">
                    Select Roles for Forum Access
                    @if(count($selectedRoles) > 0)
                        <span class="text-xs font-normal text-blue-600 ml-2">
                            ({{ count($selectedRoles) }} {{ Str::plural('role', count($selectedRoles)) }} selected)
                        </span>
                    @endif
                </label>

                <!-- Search -->
                <div class="relative mb-4">
                    <input type="search" 
                           wire:model.live.debounce.300ms="searchRole" 
                           placeholder="Search roles by name..."
                           class="w-full py-3 px-4 pl-10 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm bg-white">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    @if(!empty($searchRole))
                        <button wire:click="$set('searchRole', '')" 
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>

                <!-- Role List -->
                <div class="max-h-64 overflow-y-auto rounded-lg p-4 bg-gray-50 border border-gray-200 custom-scrollbar">
                    @if($roles->count() > 0)
                        <ul class="space-y-2">
                            @foreach ($roles as $role)
                                <li class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-colors">
                                    <input type="checkbox" 
                                           wire:model="selectedRoles" 
                                           value="{{ $role->id }}"
                                           id="role-{{ $role->id }}"
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                                    <label for="role-{{ $role->id }}" class="flex-1 cursor-pointer text-sm text-gray-700">
                                        {{ $role->name }}
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-search text-3xl mb-2 text-gray-300"></i>
                            <p>No roles found matching "{{ $searchRole }}"</p>
                        </div>
                    @endif
                </div>

                <!-- Selected Roles Tags -->
                @if (count($selectedRoles) > 0)
                    <div class="mt-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Selected Roles:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($selectedRoles as $id)
                                @php $r = $roles->firstWhere('id', $id); @endphp
                                @if ($r)
                                    <span class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 px-3 py-1.5 text-sm font-medium rounded-lg border border-blue-200">
                                        <i class="fas fa-user-tag text-xs"></i>
                                        {{ $r->name }}
                                        <button wire:click="removeRole({{ $id }})" 
                                                type="button"
                                                class="text-blue-600 hover:text-red-600 transition-colors ml-1">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Preview Section -->
            <div class="bg-gray-50 rounded-lg p-4 sm:p-6 border border-gray-200">
                <h4 class="text-xs sm:text-sm font-semibold text-gray-900 mb-2 sm:mb-3 flex items-center gap-2">
                    <i class="fas fa-eye text-blue-500"></i>
                    Preview
                </h4>
                <div class="space-y-2 text-xs sm:text-sm">
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-gray-700">Forum Name:</span>
                        <span class="text-gray-900">{{ $title ?: 'Not set' }}</span>
                    </div>
                    @if($description)
                        <div class="flex items-start gap-2">
                            <span class="font-medium text-gray-700">Description:</span>
                            <span class="text-gray-600">{{ Str::limit($description, 100) }}</span>
                        </div>
                    @endif
                    @if($parent_category)
                        @php $parent = \TeamTeaTime\Forum\Models\Category::find($parent_category); @endphp
                        @if($parent)
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-700">Parent:</span>
                                <span class="text-gray-600">{{ $parent->title }}</span>
                            </div>
                        @endif
                    @endif
                    @if(count($selectedRoles) > 0)
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-gray-700">Access:</span>
                            <span class="text-gray-600">Restricted to {{ count($selectedRoles) }} {{ Str::plural('role', count($selectedRoles)) }}</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-gray-700">Access:</span>
                            <span class="text-green-600">Public (All users)</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-between gap-2 sm:gap-3 pt-4 border-t">
                <button type="button" 
                        @click="currentStep = 2"
                        class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium flex items-center justify-center gap-2 text-sm sm:text-base">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </button>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 w-full sm:w-auto">
                    <button type="button" 
                            wire:click="resetForm"
                            class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium text-sm sm:text-base">
                        Reset
                    </button>
                    <button type="button" 
                            wire:click="store"
                            wire:loading.attr="disabled"
                            onclick="console.log('=== CREATE BUTTON CLICKED (onclick) ==='); console.log('Button type:', this.type); console.log('Button disabled:', this.disabled);"
                            x-on:click="console.log('=== CREATE BUTTON CLICKED (Alpine) ==='); console.log('Title:', $wire.title); console.log('Description:', $wire.description); console.log('Parent Category:', $wire.parent_category); console.log('Selected Roles:', $wire.selectedRoles); console.log('Thread Title:', $wire.threadTitle); console.log('Thread Description:', $wire.threadDescription); console.log('Current Step:', currentStep); console.log('Livewire component:', typeof $wire !== 'undefined' ? 'Available' : 'Not Available');"
                            class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base">
                        <span wire:loading.remove wire:target="store">
                            <i class="fas fa-check mr-2"></i>Create Forum
                        </span>
                        <span wire:loading wire:target="store" class="flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i>Creating...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
    </div>
    
    <style>
        /* ✅ Custom Scrollbar Style (optional) */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #BDBDBD;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #F1F1F1;
        }
    </style>
</div>