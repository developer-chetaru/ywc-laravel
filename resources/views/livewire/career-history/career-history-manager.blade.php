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
                            <div class="flex items-center gap-3">
                                <i class="fas fa-info-circle text-yellow-600"></i>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Current Yacht (Legacy Data)</p>
                                    <p class="text-sm text-gray-600">
                                        {{ $summary['current_yacht'] }}
                                        @if($summary['current_yacht_start_date'])
                                        - Started: {{ \Carbon\Carbon::parse($summary['current_yacht_start_date'])->format('M Y') }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        This is from your profile. Add it as a career entry to track it properly.
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if (session()->has('message'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
                        {{ session('message') }}
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

    {{-- Add/Edit Modal --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" wire:click.self="closeModal">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">
                    {{ $editingId ? 'Edit Career Entry' : 'Add Career Entry' }}
                </h2>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form wire:submit.prevent="save" class="p-6 space-y-6">
                {{-- Vessel Information --}}
                <div class="border-b pb-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Vessel Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vessel Name *</label>
                            <input type="text" wire:model="vessel_name" 
                                class="w-full border rounded-md px-3 py-2 focus:ring-[#0053FF] focus:border-[#0053FF]">
                            @error('vessel_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Position Title *</label>
                            <input type="text" wire:model="position_title" 
                                class="w-full border rounded-md px-3 py-2 focus:ring-[#0053FF] focus:border-[#0053FF]">
                            @error('position_title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vessel Type</label>
                            <select wire:model="vessel_type" 
                                class="w-full border rounded-md px-3 py-2 focus:ring-[#0053FF] focus:border-[#0053FF]">
                                <option value="">Select Type</option>
                                @foreach($vesselTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vessel Flag (Country Code)</label>
                            <input type="text" wire:model="vessel_flag" maxlength="3" 
                                class="w-full border rounded-md px-3 py-2 focus:ring-[#0053FF] focus:border-[#0053FF]"
                                placeholder="e.g., USA, GBR">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Length (meters)</label>
                            <input type="number" wire:model="vessel_length_meters" step="0.01" 
                                class="w-full border rounded-md px-3 py-2 focus:ring-[#0053FF] focus:border-[#0053FF]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gross Tonnage</label>
                            <input type="number" wire:model="gross_tonnage" 
                                class="w-full border rounded-md px-3 py-2 focus:ring-[#0053FF] focus:border-[#0053FF]">
                        </div>
                    </div>
                </div>

                {{-- Position Details --}}
                <div class="border-b pb-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Position Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                            <input type="date" wire:model="start_date" 
                                class="w-full border rounded-md px-3 py-2 focus:ring-[#0053FF] focus:border-[#0053FF]">
                            @error('start_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <div class="space-y-2">
                                <input type="date" wire:model="end_date" 
                                    @if($is_current_position) disabled @endif
                                    class="w-full border rounded-md px-3 py-2 focus:ring-[#0053FF] focus:border-[#0053FF] 
                                    @if($is_current_position) bg-gray-100 @endif">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="is_current_position" 
                                        class="mr-2">
                                    <span class="text-sm text-gray-700">Current Position</span>
                                </label>
                            </div>
                            @error('end_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employment Type</label>
                            <select wire:model="employment_type" 
                                class="w-full border rounded-md px-3 py-2 focus:ring-[#0053FF] focus:border-[#0053FF]">
                                <option value="">Select Type</option>
                                @foreach($employmentTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Position Rank</label>
                            <select wire:model="position_rank" 
                                class="w-full border rounded-md px-3 py-2 focus:ring-[#0053FF] focus:border-[#0053FF]">
                                <option value="">Select Rank</option>
                                @foreach($positionRanks as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                            <select wire:model="department" 
                                class="w-full border rounded-md px-3 py-2 focus:ring-[#0053FF] focus:border-[#0053FF]">
                                <option value="">Select Department</option>
                                @foreach($departments as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Employment Information --}}
                <div class="border-b pb-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Employment Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employer/Management Company</label>
                            <input type="text" wire:model="employer_company" 
                                class="w-full border rounded-md px-3 py-2 focus:ring-[#0053FF] focus:border-[#0053FF]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Supervisor/Captain Name</label>
                            <input type="text" wire:model="supervisor_name" 
                                class="w-full border rounded-md px-3 py-2 focus:ring-[#0053FF] focus:border-[#0053FF]">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Supervisor Contact</label>
                            <input type="text" wire:model="supervisor_contact" 
                                class="w-full border rounded-md px-3 py-2 focus:ring-[#0053FF] focus:border-[#0053FF]">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Key Duties & Responsibilities</label>
                            <textarea wire:model="key_duties" rows="3" maxlength="500"
                                class="w-full border rounded-md px-3 py-2 focus:ring-[#0053FF] focus:border-[#0053FF]"></textarea>
                            <p class="text-xs text-gray-500 mt-1">Max 500 characters</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notable Achievements</label>
                            <textarea wire:model="notable_achievements" rows="3" maxlength="500"
                                class="w-full border rounded-md px-3 py-2 focus:ring-[#0053FF] focus:border-[#0053FF]"></textarea>
                            <p class="text-xs text-gray-500 mt-1">Max 500 characters</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Departure Reason</label>
                            <select wire:model="departure_reason" 
                                class="w-full border rounded-md px-3 py-2 focus:ring-[#0053FF] focus:border-[#0053FF]">
                                <option value="">Select Reason</option>
                                @foreach($departureReasons as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Documentation Links --}}
                <div class="border-b pb-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Documentation Links</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reference Letter</label>
                            <select wire:model="reference_document_id" 
                                class="w-full border rounded-md px-3 py-2 focus:ring-[#0053FF] focus:border-[#0053FF]">
                                <option value="">None</option>
                                @foreach($documents as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->document_name ?? 'Document #' . $doc->id }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employment Contract</label>
                            <select wire:model="contract_document_id" 
                                class="w-full border rounded-md px-3 py-2 focus:ring-[#0053FF] focus:border-[#0053FF]">
                                <option value="">None</option>
                                @foreach($documents as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->document_name ?? 'Document #' . $doc->id }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sign-Off Document</label>
                            <select wire:model="signoff_document_id" 
                                class="w-full border rounded-md px-3 py-2 focus:ring-[#0053FF] focus:border-[#0053FF]">
                                <option value="">None</option>
                                @foreach($documents as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->document_name ?? 'Document #' . $doc->id }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Visibility --}}
                <div class="pb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Visibility</label>
                            <p class="text-xs text-gray-500">Show this entry on your public profile</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="visible_on_profile" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0053FF]"></div>
                        </label>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" wire:click="closeModal" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-[#0053FF] text-white rounded-md hover:bg-[#0044DD]">
                        {{ $editingId ? 'Update Entry' : 'Save Entry' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Career Entry Details Modal --}}
    @livewire('career-history.career-history-entry-details-modal')
</div>
