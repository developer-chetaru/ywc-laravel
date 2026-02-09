<div x-data="{ showModal: @entangle('showModal').live }" 
     x-show="showModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true"
     style="display: none !important;"
     @keydown.escape.window="showModal = false">
    
    {{-- Backdrop --}}
    <div x-show="showModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" 
         aria-hidden="true"
         @click="showModal = false"></div>

    {{-- Modal Container --}}
    <div class="flex min-h-full items-center justify-center p-4">
        {{-- Modal Content --}}
        <div x-show="showModal" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="relative w-full max-w-5xl max-h-[90vh] flex flex-col bg-white rounded-xl shadow-2xl transform transition-all"
             @click.stop>
        
        @if($entry)
        {{-- Header --}}
        <div class="flex justify-between items-start px-6 py-4 border-b border-gray-200 bg-gray-50 flex-shrink-0">
            <div class="flex-1">
                <h3 class="text-2xl font-bold text-gray-900" id="modal-title">
                    {{ $entry->vessel_name }}
                </h3>
                <p class="text-lg text-gray-600 mt-1">{{ $entry->position_title }}</p>
                <div class="flex items-center gap-4 mt-2">
                    <span class="text-sm text-gray-600">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ $entry->start_date->format('M d, Y') }} 
                        @if($entry->end_date)
                            - {{ $entry->end_date->format('M d, Y') }}
                        @else
                            - Present
                        @endif
                    </span>
                    <span class="text-sm text-gray-600">
                        <i class="fas fa-clock mr-1"></i>{{ $entry->getFormattedDuration() }}
                    </span>
                    @if($entry->isCurrentPosition())
                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                        Current Position
                    </span>
                    @endif
                </div>
            </div>
            <button wire:click="closeModal" 
                    class="text-gray-400 hover:text-gray-600 text-3xl font-bold ml-4" 
                    aria-label="Close">
                &times;
            </button>
        </div>

        {{-- Navigation --}}
        @if($previousEntryId || $nextEntryId)
        <div class="px-6 py-2 bg-gray-100 border-b border-gray-200 flex justify-between items-center flex-shrink-0">
            <button wire:click="navigateToEntry({{ $previousEntryId }})" 
                    @if(!$previousEntryId) disabled @endif
                    class="px-3 py-1 text-sm text-gray-600 hover:text-gray-900 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-chevron-left mr-1"></i>Previous
            </button>
            <span class="text-xs text-gray-500">Navigate between entries</span>
            <button wire:click="navigateToEntry({{ $nextEntryId }})" 
                    @if(!$nextEntryId) disabled @endif
                    class="px-3 py-1 text-sm text-gray-600 hover:text-gray-900 disabled:opacity-50 disabled:cursor-not-allowed">
                Next<i class="fas fa-chevron-right ml-1"></i>
            </button>
        </div>
        @endif

        {{-- Content --}}
        <div class="p-6 overflow-y-auto flex-1" style="min-height: 0;">
            {{-- Vessel Information Card --}}
            <div class="mb-6 bg-gray-50 rounded-lg p-4">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-ship mr-2 text-[#0053FF]"></i>Vessel Information
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($entry->vessel_type)
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Vessel Type</label>
                        <p class="text-gray-900">{{ $entry->vessel_type }}</p>
                    </div>
                    @endif
                    @if($entry->vessel_flag)
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Flag</label>
                        <p class="text-gray-900">{{ $entry->vessel_flag }}</p>
                    </div>
                    @endif
                    @if($entry->vessel_length_meters)
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Length</label>
                        <p class="text-gray-900">
                            {{ number_format($entry->vessel_length_meters, 2) }} m 
                            ({{ number_format($entry->vessel_length_meters * 3.28084, 2) }} ft)
                        </p>
                    </div>
                    @endif
                    @if($entry->gross_tonnage)
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Gross Tonnage</label>
                        <p class="text-gray-900">{{ number_format($entry->gross_tonnage) }} GT</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Position Details Card --}}
            <div class="mb-6 bg-gray-50 rounded-lg p-4">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-briefcase mr-2 text-[#0053FF]"></i>Position Details
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($entry->position_rank)
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Rank</label>
                        <p class="text-gray-900">{{ $entry->position_rank }}</p>
                    </div>
                    @endif
                    @if($entry->department)
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Department</label>
                        <p class="text-gray-900">{{ $entry->department }}</p>
                    </div>
                    @endif
                    @if($entry->employment_type)
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Employment Type</label>
                        <p class="text-gray-900">{{ $entry->employment_type }}</p>
                    </div>
                    @endif
                    @if($entry->supervisor_name)
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Reporting To</label>
                        <p class="text-gray-900">{{ $entry->supervisor_name }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Employment Information --}}
            @if($entry->employer_company || $entry->supervisor_contact)
            <div class="mb-6 bg-gray-50 rounded-lg p-4">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-building mr-2 text-[#0053FF]"></i>Employment Information
                </h4>
                <div class="space-y-3">
                    @if($entry->employer_company)
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Employer/Management Company</label>
                        <p class="text-gray-900">{{ $entry->employer_company }}</p>
                    </div>
                    @endif
                    @if($entry->supervisor_contact)
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Supervisor Contact</label>
                        <p class="text-gray-900">{{ $entry->supervisor_contact }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Duties & Achievements --}}
            @if($entry->key_duties || $entry->notable_achievements)
            <div class="mb-6">
                @if($entry->key_duties)
                <div class="mb-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-tasks mr-2 text-[#0053FF]"></i>Key Duties & Responsibilities
                    </h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $entry->key_duties }}</p>
                    </div>
                </div>
                @endif

                @if($entry->notable_achievements)
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-trophy mr-2 text-[#0053FF]"></i>Notable Achievements
                    </h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $entry->notable_achievements }}</p>
                    </div>
                </div>
                @endif
            </div>
            @endif

            {{-- Departure Reason --}}
            @if($entry->departure_reason && !$entry->isCurrentPosition())
            <div class="mb-6 bg-gray-50 rounded-lg p-4">
                <h4 class="text-lg font-semibold text-gray-900 mb-2">Departure Reason</h4>
                <p class="text-gray-700">{{ $entry->departure_reason }}</p>
            </div>
            @endif

            {{-- Employment Documentation --}}
            @if($entry->referenceDocument || $entry->contractDocument || $entry->signoffDocument)
            <div class="mb-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-file-alt mr-2 text-[#0053FF]"></i>Employment Documentation
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @if($entry->referenceDocument)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <h5 class="font-medium text-gray-900">Reference Letter</h5>
                            @if($entry->referenceDocument->thumbnail_path)
                            <img src="{{ asset('storage/' . $entry->referenceDocument->thumbnail_path) }}" 
                                 alt="Reference" 
                                 class="w-12 h-12 object-cover rounded">
                            @else
                            <i class="fas fa-file text-gray-400 text-2xl"></i>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mb-2">
                            {{ $entry->referenceDocument->document_name ?? 'Reference Document' }}
                        </p>
                        <a href="{{ asset('storage/' . $entry->referenceDocument->file_path) }}" 
                           target="_blank" 
                           class="text-sm text-[#0053FF] hover:text-[#0044DD]">
                            <i class="fas fa-download mr-1"></i>Download
                        </a>
                    </div>
                    @endif

                    @if($entry->contractDocument)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <h5 class="font-medium text-gray-900">Contract</h5>
                            @if($entry->contractDocument->thumbnail_path)
                            <img src="{{ asset('storage/' . $entry->contractDocument->thumbnail_path) }}" 
                                 alt="Contract" 
                                 class="w-12 h-12 object-cover rounded">
                            @else
                            <i class="fas fa-file text-gray-400 text-2xl"></i>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mb-2">
                            {{ $entry->contractDocument->document_name ?? 'Contract Document' }}
                        </p>
                        <a href="{{ asset('storage/' . $entry->contractDocument->file_path) }}" 
                           target="_blank" 
                           class="text-sm text-[#0053FF] hover:text-[#0044DD]">
                            <i class="fas fa-download mr-1"></i>Download
                        </a>
                    </div>
                    @endif

                    @if($entry->signoffDocument)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <h5 class="font-medium text-gray-900">Sign-Off Document</h5>
                            @if($entry->signoffDocument->thumbnail_path)
                            <img src="{{ asset('storage/' . $entry->signoffDocument->thumbnail_path) }}" 
                                 alt="Sign-Off" 
                                 class="w-12 h-12 object-cover rounded">
                            @else
                            <i class="fas fa-file text-gray-400 text-2xl"></i>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mb-2">
                            {{ $entry->signoffDocument->document_name ?? 'Sign-Off Document' }}
                        </p>
                        <a href="{{ asset('storage/' . $entry->signoffDocument->file_path) }}" 
                           target="_blank" 
                           class="text-sm text-[#0053FF] hover:text-[#0044DD]">
                            <i class="fas fa-download mr-1"></i>Download
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Sea Service Information --}}
            <div class="mb-6 bg-blue-50 rounded-lg p-4 border border-blue-200">
                <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-anchor mr-2 text-[#0053FF]"></i>Sea Service
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Duration</label>
                        <p class="text-gray-900 font-semibold">{{ $entry->getFormattedDuration() }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Sea Service Days</label>
                        <p class="text-gray-900 font-semibold">{{ $entry->getSeaServiceDays() }} days</p>
                    </div>
                    @if($entry->qualifiesForSeaService())
                    <div class="md:col-span-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>Qualifies for Sea Service
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-wrap gap-2 justify-end flex-shrink-0">
            <button wire:click="closeModal" 
                    class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                <i class="fas fa-times mr-2"></i>Close
            </button>
            <button wire:click="duplicate" 
                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                <i class="fas fa-copy mr-2"></i>Duplicate
            </button>
            <button wire:click="edit" 
                    class="px-4 py-2 bg-[#0053FF] text-white rounded-md hover:bg-[#0044DD] transition-colors">
                <i class="fas fa-edit mr-2"></i>Edit
            </button>
            <button wire:click="delete" 
                    onclick="return confirm('Are you sure you want to delete this career entry? This action cannot be undone.')"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                <i class="fas fa-trash mr-2"></i>Delete
            </button>
        </div>
        @endif
        </div>
    </div>
</div>
