<div>
    <main class="flex-1 flex flex-col bg-gray-100 p-4 sm:p-6">
        <div class="flex-1 overflow-hidden">
            <div class="p-4 bg-[#F5F6FA]">
                <div class="rounded-lg bg-white p-4 sm:p-6">
                    {{-- Header --}}
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4  max-[1200px]:flex-col max-[1200px]:items-start max-[1200px]:gap-4">
                            <div class="flex-1">
                                <h1 class="text-2xl font-bold text-gray-900">Career History</h1>
                                @if($isSuperAdmin)
                                <div class="mt-2 flex items-center gap-3">
                                    @if($viewingUser && $viewingUser->id !== auth()->id())
                                    <p class="text-sm text-gray-600">
                                        Viewing: <span class="font-semibold">{{ $viewingUser->first_name }} {{ $viewingUser->last_name }}</span> ({{ $viewingUser->email }})
                                    </p>
                                    @else
                                    <p class="text-sm text-gray-600">Your career history</p>
                                    @endif
                                    <button wire:click="$set('showUserSelector', true)" 
                                        class="text-sm text-[#0053FF] hover:text-[#0044DD] underline">
                                        <i class="fas fa-search mr-1"></i>View Another User
                                    </button>
                                    @if($viewingUser && $viewingUser->id !== auth()->id())
                                    <button wire:click="viewMyCareer" 
                                        class="text-sm text-gray-600 hover:text-gray-900 underline">
                                        <i class="fas fa-user mr-1"></i>View My Career
                                    </button>
                                    @endif
                                </div>
                                @else
                                <p class="text-sm text-gray-600 mt-1">Manage your career history and sea service records</p>
                                @endif
                            </div>
                            <div class="flex gap-2 max-[992px]:flex-col max-[992px]:w-full">
                                @if($canEdit)
                                <button wire:click="openModal" 
                                    class="bg-[#0053FF] text-white px-4 py-2 rounded-md hover:bg-[#0044DD] transition-colors font-medium text-center">
                                    <i class="fas fa-plus mr-2"></i>Add Career Entry
                                </button>
                                @endif
                                @if($isSuperAdmin && $viewingUser && $viewingUser->id !== Auth::id())
                                <a href="{{ route('career-history.sea-service-report.user', ['userId' => $viewingUser->id]) }}" 
                                    target="_blank"
                                    class="text-center bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors font-medium">
                                    <i class="fas fa-file-pdf mr-2"></i>View Report
                                </a>
                                <a href="{{ route('career-history.sea-service-report.user.download', ['userId' => $viewingUser->id]) }}" 
                                    class="text-center bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors font-medium">
                                    <i class="fas fa-download mr-2"></i>Download PDF
                                </a>
                                @else
                                <a href="{{ route('career-history.sea-service-report') }}" 
                                    target="_blank"
                                    class="text-center bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors font-medium">
                                    <i class="fas fa-file-pdf mr-2"></i>View Report
                                </a>
                                <a href="{{ route('career-history.sea-service-report.download') }}" 
                                    class="text-center bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors font-medium">
                                    <i class="fas fa-download mr-2"></i>Download PDF
                                </a>
                                @endif
                            </div>
                        </div>

                        {{-- Super Admin User Selector --}}
                        @if($isSuperAdmin && $showUserSelector)
                        <div class="mb-4 bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-semibold text-gray-900">Search Users</h3>
                                <button wire:click="$set('showUserSelector', false)" 
                                    class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="search" 
                                placeholder="Search by name or email..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0053FF] focus:border-transparent mb-3"
                            >
                            @if($users->count() > 0)
                            <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg bg-white">
                                <div class="divide-y divide-gray-200">
                                    @foreach($users as $userItem)
                                    <div class="p-3 hover:bg-gray-50 cursor-pointer {{ $viewingUserId == $userItem->id ? 'bg-blue-50' : '' }}"
                                        wire:click="selectUser({{ $userItem->id }})">
                                        <div class="flex items-center gap-3">
                                            @if($userItem->profile_photo_path)
                                            <img src="{{ asset('storage/'.$userItem->profile_photo_path) }}" 
                                                class="w-10 h-10 rounded-full object-cover" alt="">
                                            @else
                                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-medium text-sm">
                                                {{ strtoupper(substr($userItem->first_name, 0, 1) . substr($userItem->last_name, 0, 1)) }}
                                            </div>
                                            @endif
                                            <div class="flex-1">
                                                <div class="font-medium text-gray-900">{{ $userItem->first_name }} {{ $userItem->last_name }}</div>
                                                <div class="text-xs text-gray-600 truncate">{{ $userItem->email }}</div>
                                            </div>
                                            @if($viewingUserId == $userItem->id)
                                            <i class="fas fa-check text-[#0053FF]"></i>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @elseif($search)
                            <p class="text-sm text-gray-500 text-center py-4">No users found</p>
                            @else
                            <p class="text-sm text-gray-500 text-center py-4">Start typing to search users...</p>
                            @endif
                        </div>
                        @endif

                        {{-- Summary Cards --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 max-[992px]:!grid-cols-1">
                            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600">Total Sea Service</p>
                                        <p class="text-2xl font-bold text-gray-900 max-[767px]:text-lg">{{ $totalSeaService }}</p>
                                    </div>
                                    <i class="fas fa-clock text-3xl text-blue-500"></i>
                                </div>
                            </div>

                            <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600">Total Entries</p>
                                        <p class="text-2xl font-bold text-gray-900 max-[767px]:text-lg">{{ $summary['total_entries'] }}</p>
                                    </div>
                                    <i class="fas fa-briefcase text-3xl text-green-500"></i>
                                </div>
                            </div>

                            <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600">Current Positions</p>
                                        <p class="text-2xl font-bold text-gray-900 max-[767px]:text-lg">{{ $summary['current_positions'] }}</p>
                                    </div>
                                    <i class="fas fa-ship text-3xl text-purple-500"></i>
                                </div>
                            </div>
                        </div>

                        @if($summary['current_yacht'])
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3 flex-1">
                                    <i class="fas fa-info-circle text-yellow-600 mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900">Current Yacht (Legacy Data)</p>
                                        <p class="text-sm text-gray-600">
                                            <strong>{{ $summary['current_yacht'] }}</strong>
                                            @if($summary['current_yacht_start_date'])
                                            <span class="text-gray-500">- Started: {{ \Carbon\Carbon::parse($summary['current_yacht_start_date'])->format('M Y') }}</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            This is from your profile. Add it as a career entry to track it properly.
                                        </p>
                                    </div>
                                </div>
                                <button wire:click="addCurrentYachtAsEntry" 
                                        class="px-4 py-2 bg-[#0053FF] text-white text-sm font-medium rounded-md hover:bg-[#0044DD] transition-colors whitespace-nowrap flex items-center gap-2">
                                    <i class="fas fa-plus-circle"></i>
                                    Add as Career Entry
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if (session()->has('message'))
                    <div x-data="{ show: true }" 
                         x-show="show"
                         x-init="setTimeout(() => show = false, 5000)"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between animate-slide-down">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>{{ session('message') }}</span>
                        </div>
                        <button @click="show = false" class="text-green-600 hover:text-green-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @endif

                    @if (session()->has('error'))
                    <div x-data="{ show: true }" 
                         x-show="show"
                         x-init="setTimeout(() => show = false, 5000)"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @endif
                    
                    @if (session()->has('info'))
                    <div x-data="{ show: true }" 
                         x-show="show"
                         x-init="setTimeout(() => show = false, 5000)"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="mb-4 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>{{ session('info') }}</span>
                        </div>
                        <button @click="show = false" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @endif

                    {{-- Timeline View --}}
                    @if($entries->count() > 0)
                    <div class="space-y-6">
                        @foreach($entries as $entry)
                        <div class="border-l-4 border-[#0053FF] pl-6 pb-6 relative">
                            {{-- Timeline dot --}}
                            <div class="absolute -left-2 top-0 w-4 h-4 bg-[#0053FF] rounded-full border-2 border-white"></div>
                            
                            <div class="bg-gray-50 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="text-xl font-bold text-gray-900">{{ $entry->vessel_name }}</h3>
                                            @if($entry->isCurrentPosition())
                                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded">Current</span>
                                            @endif
                                        </div>
                                        
                                        <p class="text-lg text-gray-700 mb-2">{{ $entry->position_title }}</p>
                                        
                                        <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-3">
                                            <span><i class="fas fa-calendar mr-1"></i>
                                                {{ $entry->start_date->format('M Y') }} - 
                                                {{ $entry->end_date ? $entry->end_date->format('M Y') : 'Present' }}
                                            </span>
                                            <span><i class="fas fa-clock mr-1"></i>{{ $entry->getFormattedDuration() }}</span>
                                            @if($entry->vessel_type)
                                            <span><i class="fas fa-ship mr-1"></i>{{ ucfirst(str_replace('_', ' ', $entry->vessel_type)) }}</span>
                                            @endif
                                            @if($entry->department)
                                            <span><i class="fas fa-briefcase mr-1"></i>{{ ucfirst($entry->department) }}</span>
                                            @endif
                                        </div>

                                        @if($entry->key_duties || $entry->notable_achievements)
                                        <div class="mt-3 space-y-2">
                                            @if($entry->key_duties)
                                            <div>
                                                <p class="text-sm font-semibold text-gray-700">Key Duties:</p>
                                                <p class="text-sm text-gray-600">{{ $entry->key_duties }}</p>
                                            </div>
                                            @endif
                                            @if($entry->notable_achievements)
                                            <div>
                                                <p class="text-sm font-semibold text-gray-700">Achievements:</p>
                                                <p class="text-sm text-gray-600">{{ $entry->notable_achievements }}</p>
                                            </div>
                                            @endif
                                        </div>
                                        @endif
                                    </div>

                                    <div class="flex gap-2">
                                        <button wire:click="$dispatch('openCareerEntryDetails', { entryId: {{ $entry->id }}, viewingUserId: {{ $viewingUserId ?? 'null' }} })" 
                                            class="text-[#0053FF] hover:text-[#0044DD] p-2" 
                                            title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($canEdit)
                                        <button wire:click="openModal({{ $entry->id }})" 
                                            class="text-blue-600 hover:text-blue-800 p-2"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click="delete({{ $entry->id }})" 
                                            wire:confirm="Are you sure you want to delete this career entry?"
                                            class="text-red-600 hover:text-red-800 p-2"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-12 bg-gray-50 rounded-lg">
                        <i class="fas fa-briefcase text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 mb-4">
                            @if($isSuperAdmin && $viewingUser && $viewingUser->id !== auth()->id())
                            This user has no career history entries yet.
                            @else
                            No career history entries yet.
                            @endif
                        </p>
                        @if($canEdit)
                        <button wire:click="openModal" 
                            class="bg-[#0053FF] text-white px-6 py-2 rounded-md hover:bg-[#0044DD] transition-colors">
                            Add Your First Career Entry
                        </button>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <div>
        {{-- Add/Edit Modal --}}
        @if($showModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" 
         wire:click.self="closeModal"
         x-data="careerEntryForm()"
         x-init="init()"
         @keydown.escape.window="closeModal()">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto" 
             @click.stop>
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between z-10">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ $editingId ? 'Edit Career Entry' : 'Add Career Entry' }}
                    </h2>
                    <span x-show="hasUnsavedChanges" class="text-xs text-orange-600 bg-orange-50 px-2 py-1 rounded">
                        <i class="fas fa-circle text-xs"></i> Unsaved changes
                    </span>
                </div>
                <button wire:click="closeModal" 
                        @click="closeModal()"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            {{-- Loading Overlay - Only show on form submit --}}
            <div wire:loading.delay wire:target="save" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-20 rounded-lg">
                <div class="text-center">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#0053FF]"></div>
                    <p class="mt-2 text-sm text-gray-600">Saving...</p>
                </div>
            </div>

            <form wire:submit.prevent="save" 
                  class="p-6 space-y-6"
                  @input="markAsChanged()"
                  @change="markAsChanged()">
                {{-- Vessel Information --}}
                <div class="border-b pb-4" x-data="{ isOpen: true }">
                    <button type="button" @click="isOpen = !isOpen" class="w-full flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-ship mr-2 text-[#0053FF]"></i>Vessel Information
                        </h3>
                        <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': isOpen }"></i>
                    </button>
                    <div x-show="isOpen" x-collapse class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Vessel Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   wire:model.defer="vessel_name" 
                                   wire:keydown.debounce.500ms="markAsChanged"
                                   class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all
                                   @error('vessel_name') border-red-500 @enderror">
                            @error('vessel_name') 
                                <span class="text-red-500 text-sm mt-1 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </span> 
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Position Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   wire:model.defer="position_title" 
                                   wire:keydown.debounce.500ms="markAsChanged"
                                   class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all
                                   @error('position_title') border-red-500 @enderror">
                            @error('position_title') 
                                <span class="text-red-500 text-sm mt-1 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </span> 
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vessel Type</label>
                            <select wire:model.defer="vessel_type" 
                                    @change="markAsChanged()"
                                    class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all">
                                <option value="">Select Type</option>
                                @foreach($vesselTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vessel Flag (Country Code)</label>
                            <input type="text" 
                                   wire:model.defer="vessel_flag" 
                                   maxlength="3" 
                                   placeholder="e.g., USA, GBR"
                                   class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all uppercase"
                                   style="text-transform: uppercase;">
                            <p class="text-xs text-gray-500 mt-1">3-letter country code</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Length (meters)</label>
                            <div class="relative">
                                <input type="number" 
                                       wire:model.defer="vessel_length_meters" 
                                       step="0.01" 
                                       min="0"
                                       placeholder="0.00"
                                       class="w-full border rounded-md px-3 py-2 pr-8 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all">
                                <span class="absolute right-3 top-2.5 text-gray-500 text-sm">m</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gross Tonnage</label>
                            <input type="number" 
                                   wire:model.defer="gross_tonnage" 
                                   min="0"
                                   placeholder="0"
                                   class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all">
                        </div>
                    </div>
                </div>

                {{-- Position Details --}}
                <div class="border-b pb-4" x-data="{ isOpen: true }">
                    <button type="button" @click="isOpen = !isOpen" class="w-full flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-briefcase mr-2 text-[#0053FF]"></i>Position Details
                        </h3>
                        <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': isOpen }"></i>
                    </button>
                    <div x-show="isOpen" x-collapse class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Start Date <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="date" 
                                       wire:model.defer="start_date" 
                                       max="{{ date('Y-m-d') }}"
                                       class="w-full border rounded-md px-3 py-2 pr-10 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all
                                       @error('start_date') border-red-500 @enderror">
                                <i class="fas fa-calendar-alt absolute right-3 top-2.5 text-gray-400 pointer-events-none"></i>
                            </div>
                            @error('start_date') 
                                <span class="text-red-500 text-sm mt-1 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </span> 
                            @enderror
                            @if($start_date)
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Selected: {{ \Carbon\Carbon::parse($start_date)->format('M d, Y') }}
                                </p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                End Date
                                @if($is_current_position)
                                    <span class="text-xs text-gray-500 font-normal">(Disabled for current position)</span>
                                @endif
                            </label>
                            <div class="space-y-2">
                                <div class="relative">
                                    <input type="date" 
                                           wire:model.defer="end_date" 
                                           :min="$wire.start_date || ''"
                                           :max="date('Y-m-d')"
                                           :disabled="$wire.is_current_position"
                                           class="w-full border rounded-md px-3 py-2 pr-10 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all
                                           @if($is_current_position) bg-gray-100 cursor-not-allowed @endif
                                           @error('end_date') border-red-500 @enderror">
                                    <i class="fas fa-calendar-alt absolute right-3 top-2.5 text-gray-400 pointer-events-none"></i>
                                </div>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="checkbox" 
                                           wire:model.defer="is_current_position" 
                                           wire:change="markAsChanged"
                                           class="mr-2 w-4 h-4 text-[#0053FF] border-gray-300 rounded focus:ring-[#0053FF] cursor-pointer">
                                    <span class="text-sm text-gray-700 group-hover:text-gray-900">
                                        <i class="fas fa-check-circle mr-1"></i>Current Position
                                    </span>
                                </label>
                            </div>
                            @error('end_date') 
                                <span class="text-red-500 text-sm mt-1 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </span> 
                            @enderror
                            @if($start_date && $end_date && !$is_current_position)
                                <p class="text-xs text-blue-600 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Duration: {{ \Carbon\Carbon::parse($start_date)->diffForHumans(\Carbon\Carbon::parse($end_date), true) }}
                                </p>
                            @elseif($start_date && $is_current_position)
                                <p class="text-xs text-green-600 mt-1">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Ongoing since {{ \Carbon\Carbon::parse($start_date)->diffForHumans() }}
                                </p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employment Type</label>
                            <select wire:model.defer="employment_type" 
                                    wire:change="markAsChanged"
                                    class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all">
                                <option value="">Select Type</option>
                                @foreach($employmentTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-xs text-gray-500 font-normal">(Position/Role)</span></label>
                            <select wire:model.defer="role" 
                                    wire:change="markAsChanged"
                                    class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all">
                                <option value="">Select Role</option>
                                @foreach($availableRoles as $roleName => $roleLabel)
                                <option value="{{ $roleName }}">{{ $roleLabel }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>Select your specific role/position
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Position Rank</label>
                            <select wire:model.defer="position_rank" 
                                    wire:change="markAsChanged"
                                    class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all">
                                <option value="">Select Rank</option>
                                @foreach($positionRanks as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                            <select wire:model.defer="department" 
                                    wire:change="markAsChanged"
                                    class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all">
                                <option value="">Select Department</option>
                                @foreach($departments as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Employment Information --}}
                <div class="border-b pb-4" x-data="{ isOpen: true }">
                    <button type="button" @click="isOpen = !isOpen" class="w-full flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-building mr-2 text-[#0053FF]"></i>Employment Information
                        </h3>
                        <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': isOpen }"></i>
                    </button>
                    <div x-show="isOpen" x-collapse class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employer/Management Company</label>
                            <input type="text" 
                                   wire:model.defer="employer_company" 
                                   placeholder="Company name"
                                   class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Supervisor/Captain Name</label>
                            <input type="text" 
                                   wire:model.defer="supervisor_name" 
                                   placeholder="Full name"
                                   class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Supervisor Contact</label>
                            <input type="text" 
                                   wire:model.defer="supervisor_contact" 
                                   placeholder="Email or phone number"
                                   class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Key Duties & Responsibilities
                            </label>
                            <textarea wire:model.defer="key_duties" 
                                      rows="4" 
                                      maxlength="500"
                                      placeholder="Describe your key duties and responsibilities..."
                                      x-on:input="updateCharCount('key_duties', $event.target.value, 500)"
                                      class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all resize-y"></textarea>
                            <div class="flex items-center justify-between mt-1">
                                <p class="text-xs text-gray-500">
                                    <span x-text="getCharCount('key_duties')">0</span> / 500 characters
                                </p>
                                <span x-show="getCharCount('key_duties') > 450" class="text-xs text-orange-600">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>Approaching limit
                                </span>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notable Achievements</label>
                            <textarea wire:model.defer="notable_achievements" 
                                      rows="4" 
                                      maxlength="500"
                                      placeholder="Describe your notable achievements..."
                                      x-on:input="updateCharCount('notable_achievements', $event.target.value, 500)"
                                      class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all resize-y"></textarea>
                            <div class="flex items-center justify-between mt-1">
                                <p class="text-xs text-gray-500">
                                    <span x-text="getCharCount('notable_achievements')">0</span> / 500 characters
                                </p>
                                <span x-show="getCharCount('notable_achievements') > 450" class="text-xs text-orange-600">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>Approaching limit
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Departure Reason</label>
                            <select wire:model.defer="departure_reason" 
                                    wire:change="markAsChanged"
                                    class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all">
                                <option value="">Select Reason</option>
                                @foreach($departureReasons as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Documentation Links --}}
                <div class="border-b pb-4" x-data="{ isOpen: true }">
                    <button type="button" @click="isOpen = !isOpen" class="w-full flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-file-alt mr-2 text-[#0053FF]"></i>Documentation Links
                        </h3>
                        <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': isOpen }"></i>
                    </button>
                    <div x-show="isOpen" x-collapse class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div x-data="{ search: '', isOpen: false, selectedValue: '{{ $reference_document_id ?? '' }}' }" class="relative" wire:ignore.self>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Reference Letter
                                <span class="text-xs text-gray-500 font-normal">(from Documents)</span>
                            </label>
                            <div class="relative">
                                <select x-model="selectedValue" 
                                        @change="selectedValue = $event.target.value; $wire.set('reference_document_id', $event.target.value, false); markAsChanged()"
                                        class="w-full border rounded-md px-3 py-2 pr-8 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all appearance-none
                                        @if(count($documents) === 0) bg-gray-100 cursor-not-allowed @endif">
                                    <option value="">None</option>
                                    @foreach($documents as $doc)
                                    <option value="{{ $doc->id }}" {{ ($reference_document_id ?? '') == $doc->id ? 'selected' : '' }}>
                                        {{ $doc->name ?? ($doc->document_name ?? ($doc->type ?? 'Document #' . $doc->id)) }}
                                        @if($doc->status === 'approved')
                                            <span class="text-green-600">✓</span>
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down absolute right-3 top-3 text-gray-400 pointer-events-none"></i>
                            </div>
                            <div x-show="selectedValue" 
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 style="display: none !important;">
                                <p class="text-xs text-green-600 mt-1">
                                    <i class="fas fa-check-circle mr-1"></i>Document linked
                                </p>
                            </div>
                        </div>

                        <div x-data="{ search: '', isOpen: false, selectedValue: '{{ $contract_document_id ?? '' }}' }" class="relative" wire:ignore.self>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Employment Contract
                                <span class="text-xs text-gray-500 font-normal">(from Documents)</span>
                            </label>
                            <div class="relative">
                                <select x-model="selectedValue" 
                                        @change="selectedValue = $event.target.value; $wire.set('contract_document_id', $event.target.value, false); markAsChanged()"
                                        class="w-full border rounded-md px-3 py-2 pr-8 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all appearance-none
                                        @if(count($documents) === 0) bg-gray-100 cursor-not-allowed @endif">
                                    <option value="">None</option>
                                    @foreach($documents as $doc)
                                    <option value="{{ $doc->id }}" {{ ($contract_document_id ?? '') == $doc->id ? 'selected' : '' }}>
                                        {{ $doc->name ?? ($doc->document_name ?? ($doc->type ?? 'Document #' . $doc->id)) }}
                                        @if($doc->status === 'approved')
                                            <span class="text-green-600">✓</span>
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down absolute right-3 top-3 text-gray-400 pointer-events-none"></i>
                            </div>
                            <div x-show="selectedValue" 
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 style="display: none !important;">
                                <p class="text-xs text-green-600 mt-1">
                                    <i class="fas fa-check-circle mr-1"></i>Document linked
                                </p>
                            </div>
                        </div>

                        <div x-data="{ search: '', isOpen: false, selectedValue: '{{ $signoff_document_id ?? '' }}' }" class="relative" wire:ignore.self>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Sign-Off Document
                                <span class="text-xs text-gray-500 font-normal">(from Documents)</span>
                            </label>
                            <div class="relative">
                                <select x-model="selectedValue" 
                                        @change="selectedValue = $event.target.value; $wire.set('signoff_document_id', $event.target.value, false); markAsChanged()"
                                        class="w-full border rounded-md px-3 py-2 pr-8 focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition-all appearance-none
                                        @if(count($documents) === 0) bg-gray-100 cursor-not-allowed @endif">
                                    <option value="">None</option>
                                    @foreach($documents as $doc)
                                    <option value="{{ $doc->id }}" {{ ($signoff_document_id ?? '') == $doc->id ? 'selected' : '' }}>
                                        {{ $doc->name ?? ($doc->document_name ?? ($doc->type ?? 'Document #' . $doc->id)) }}
                                        @if($doc->status === 'approved')
                                            <span class="text-green-600">✓</span>
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down absolute right-3 top-3 text-gray-400 pointer-events-none"></i>
                            </div>
                            <div x-show="selectedValue" 
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 style="display: none !important;">
                                <p class="text-xs text-green-600 mt-1">
                                    <i class="fas fa-check-circle mr-1"></i>Document linked
                                </p>
                            </div>
                        </div>
                    </div>
                    @if(count($documents) === 0)
                        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-yellow-600 mr-2 mt-0.5"></i>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-yellow-800 mb-1">No approved documents available</p>
                                    <p class="text-xs text-yellow-700 mb-3">Upload documents in the Documents section first, then link them here.</p>
                                    <a href="{{ route('documents') }}" 
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-1.5 bg-yellow-600 text-white text-xs font-medium rounded-md hover:bg-yellow-700 transition-colors">
                                        <i class="fas fa-upload mr-1.5"></i>Go to Documents
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-xs text-blue-700">
                                <i class="fas fa-lightbulb mr-1"></i>
                                <strong>Tip:</strong> These documents are from your <a href="{{ route('documents') }}" target="_blank" class="underline font-medium">Documents section</a>. Only approved documents appear here.
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Visibility --}}
                <div class="pb-4">
                    <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Profile Visibility</label>
                            <p class="text-xs text-gray-500">Show this entry on your public profile</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   wire:model.defer="visible_on_profile" 
                                   wire:change="markAsChanged"
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0053FF]"></div>
                        </label>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-between items-center pt-4 border-t bg-gray-50 -mx-6 -mb-6 px-6 py-4 rounded-b-lg">
                    <button type="button" 
                            wire:click="closeModal" 
                            @click="closeModal()"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <div class="flex gap-3">
                        <button type="button" 
                                wire:click="save" 
                                wire:loading.attr="disabled"
                                wire:target="save"
                                class="px-6 py-2 bg-[#0053FF] text-white rounded-md hover:bg-[#0044DD] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                            <span wire:loading.remove wire:target="save">
                                <i class="fas fa-save mr-2"></i>{{ $editingId ? 'Update Entry' : 'Save Entry' }}
                            </span>
                            <span wire:loading wire:target="save" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

        {{-- Career Entry Details Modal --}}
        @livewire('career-history.career-history-entry-details-modal')

        {{-- Alpine.js Form Helper --}}
        <script>
        function careerEntryForm() {
            return {
                hasUnsavedChanges: false,
                charCounts: {
                    key_duties: 0,
                    notable_achievements: 0
                },
                
                init() {
                    // Initialize character counts from Livewire
                    const keyDuties = @this.get('key_duties') || '';
                    const notableAchievements = @this.get('notable_achievements') || '';
                    this.charCounts.key_duties = keyDuties.length;
                    this.charCounts.notable_achievements = notableAchievements.length;
                    
                    // Watch for Livewire updates
                    Livewire.on('livewire:update', () => {
                        const keyDuties = @this.get('key_duties') || '';
                        const notableAchievements = @this.get('notable_achievements') || '';
                        this.charCounts.key_duties = keyDuties.length;
                        this.charCounts.notable_achievements = notableAchievements.length;
                    });
                    
                    // Auto-save disabled - only save on form submit
                    // Removed auto-save interval to prevent flashing
                    
                    // Warn before leaving with unsaved changes
                    window.addEventListener('beforeunload', (e) => {
                        if (this.hasUnsavedChanges) {
                            e.preventDefault();
                            e.returnValue = '';
                        }
                    });
                },
                
                markAsChanged() {
                    this.hasUnsavedChanges = true;
                },
                
                closeModal() {
                    if (this.hasUnsavedChanges) {
                        if (confirm('You have unsaved changes. Are you sure you want to close?')) {
                            this.hasUnsavedChanges = false;
                            @this.call('closeModal');
                        }
                    } else {
                        @this.call('closeModal');
                    }
                },
                
                updateCharCount(field, value, max) {
                    const count = value.length;
                    this.charCounts[field] = count;
                    this.markAsChanged();
                },
                
                getCharCount(field) {
                    return this.charCounts[field] || 0;
                },
                
                saveDraft() {
                    // Auto-save to localStorage as backup
                    const formData = {
                        vessel_name: @this.vessel_name,
                        position_title: @this.position_title,
                        key_duties: @this.key_duties,
                        notable_achievements: @this.notable_achievements,
                        timestamp: new Date().toISOString()
                    };
                    localStorage.setItem('career_entry_draft', JSON.stringify(formData));
                }
            }
        }
    </script>

    {{-- Success Animation Script --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('career-entry-saved', () => {
                // Trigger success animation
                const flash = document.createElement('div');
                flash.className = 'fixed inset-0 bg-green-500 bg-opacity-20 z-[60] flex items-center justify-center pointer-events-none';
                flash.innerHTML = '<div class="bg-white rounded-lg p-6 shadow-xl animate-bounce"><i class="fas fa-check-circle text-green-500 text-4xl"></i></div>';
                document.body.appendChild(flash);
                setTimeout(() => {
                    flash.style.opacity = '0';
                    flash.style.transition = 'opacity 0.3s';
                    setTimeout(() => flash.remove(), 300);
                }, 1000);
            });
        });
    </script>

    {{-- Custom Styles for Animations --}}
    <style>
        @keyframes slide-down {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-slide-down {
            animation: slide-down 0.3s ease-out;
        }
        
        /* Smooth transitions for collapsible sections */
        [x-collapse] {
            transition: all 0.3s ease-out;
        }
        </style>
    </div>
</div>
