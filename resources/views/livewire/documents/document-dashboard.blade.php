<div>
    <main class="flex-1 flex flex-col bg-gray-100 p-4 sm:p-6">
        <div class="flex-1 overflow-hidden">
            <div class="p-4 bg-[#F5F6FA]">
                <div class="rounded-lg bg-white p-4 sm:p-6">
                    {{-- Header --}}
                    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">My Documents</h1>
                            <p class="text-sm text-gray-600 mt-1">Manage and track all your documents</p>
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="$dispatch('openShareDocumentsModal')" 
                                class="bg-[#0053FF] text-white px-4 py-2 rounded-md hover:bg-[#0044DD] transition-colors text-sm font-medium">
                                <i class="fas fa-share-alt mr-2"></i>Share Documents
                            </button>
                            <button wire:click="$dispatch('openShareProfileModal')" 
                                class="bg-white border border-[#0053FF] text-[#0053FF] px-4 py-2 rounded-md hover:bg-[#F5F6FA] transition-colors text-sm font-medium">
                                <i class="fas fa-user-shield mr-2"></i>Share Profile
                            </button>
                            <button wire:click="$dispatch('openUploadModal')" 
                                class="bg-[#0C7B24] text-white px-4 py-2 rounded-md hover:bg-[#0A6B1F] transition-colors text-sm font-medium">
                                <i class="fas fa-plus mr-2"></i>Add Document
                            </button>
                        </div>
                    </div>

                    {{-- Stats Cards --}}
                    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
                        <div class="bg-[#F5F6FA] p-4 rounded-lg text-center">
                            <p class="text-xs text-gray-600 mb-2">Total</p>
                            <p class="text-xl font-bold text-[#FF7700]">{{ $stats['total'] }}</p>
                        </div>
                        <div class="bg-[#F5F6FA] p-4 rounded-lg text-center">
                            <p class="text-xs text-gray-600 mb-2">Pending</p>
                            <p class="text-xl font-bold text-[#E07911]">{{ $stats['pending'] }}</p>
                        </div>
                        <div class="bg-[#F5F6FA] p-4 rounded-lg text-center">
                            <p class="text-xs text-gray-600 mb-2">Approved</p>
                            <p class="text-xl font-bold text-[#0C7B24]">{{ $stats['approved'] }}</p>
                        </div>
                        <div class="bg-[#F5F6FA] p-4 rounded-lg text-center">
                            <p class="text-xs text-gray-600 mb-2">Rejected</p>
                            <p class="text-xl font-bold text-[#EB1C24]">{{ $stats['rejected'] }}</p>
                        </div>
                        <div class="bg-[#F5F6FA] p-4 rounded-lg text-center">
                            <p class="text-xs text-gray-600 mb-2">Expiring Soon</p>
                            <p class="text-xl font-bold text-[#E07911]">{{ $stats['expiring_soon'] }}</p>
                        </div>
                        <div class="bg-[#F5F6FA] p-4 rounded-lg text-center">
                            <p class="text-xs text-gray-600 mb-2">Expired</p>
                            <p class="text-xl font-bold text-[#616161]">{{ $stats['expired'] }}</p>
                        </div>
                    </div>

                    {{-- Expiring Documents Alert --}}
                    @if($expiringDocuments->count() > 0)
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Documents Expiring Soon</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <ul class="list-disc list-inside">
                                        @foreach($expiringDocuments->take(5) as $doc)
                                        <li>
                                            {{ $doc->document_name ?? 'Document #' . $doc->id }} 
                                            - Expires {{ $doc->expiry_date ? \Carbon\Carbon::parse($doc->expiry_date)->format('M d, Y') : 'N/A' }}
                                            ({{ $doc->days_until_expiry }} days)
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Filters --}}
                    <div class="bg-[#F5F6FA] p-4 rounded-lg mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            {{-- Search --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input type="text" wire:model.live.debounce.300ms="search" 
                                    placeholder="Search documents..." 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]">
                            </div>
                            
                            {{-- Type Filter --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                <select wire:model.live="selectedType" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]">
                                    <option value="all">All Types</option>
                                    @foreach($documentTypes as $type)
                                    <option value="{{ $type->slug }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            {{-- Status Filter --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select wire:model.live="selectedStatus" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]">
                                    <option value="all">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            
                            {{-- Expiry Filter --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry</label>
                                <select wire:model.live="expiryFilter" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]">
                                    <option value="all">All</option>
                                    <option value="expiring">Expiring Soon</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                        </div>
                        
                        {{-- Sort --}}
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                            <select wire:model.live="sortBy" 
                                class="w-full md:w-auto px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="expiry_date">Expiry Date</option>
                            </select>
                        </div>
                    </div>

                    {{-- Documents Grid --}}
                    @if($documents->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($documents as $document)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 mb-1">
                                        {{ $document->document_name ?? ($document->documentType->name ?? 'Document') }}
                                    </h3>
                                    @if($document->document_number)
                                    <p class="text-sm text-gray-600">#{{ $document->document_number }}</p>
                                    @endif
                                </div>
                                <span class="px-2 py-1 text-xs font-medium rounded
                                    @if($document->status === 'approved') bg-green-100 text-green-800
                                    @elseif($document->status === 'rejected') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ ucfirst($document->status) }}
                                </span>
                            </div>
                            
                            @if($document->thumbnail_path)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $document->thumbnail_path) }}" 
                                    alt="Thumbnail" 
                                    class="w-full h-32 object-cover rounded">
                            </div>
                            @endif
                            
                            <div class="space-y-1 text-sm text-gray-600 mb-3">
                                @if($document->documentType)
                                <p><i class="fas fa-tag mr-2"></i>{{ $document->documentType->name }}</p>
                                @endif
                                @if($document->issuing_authority)
                                <p><i class="fas fa-building mr-2"></i>{{ $document->issuing_authority }}</p>
                                @endif
                                @if($document->expiry_date)
                                <p><i class="fas fa-calendar-alt mr-2"></i>
                                    Expires: {{ \Carbon\Carbon::parse($document->expiry_date)->format('M d, Y') }}
                                    @if($document->days_until_expiry < 0)
                                        <span class="text-red-600 font-semibold">(Expired)</span>
                                    @elseif($document->days_until_expiry <= 30)
                                        <span class="text-yellow-600 font-semibold">({{ $document->days_until_expiry }} days)</span>
                                    @endif
                                </p>
                                @endif
                            </div>
                            
                            <div class="flex gap-2">
                                @if($document->file_path)
                                <a href="{{ asset('storage/' . $document->file_path) }}" 
                                    target="_blank" 
                                    class="flex-1 text-center bg-[#0053FF] text-white px-3 py-2 rounded-md hover:bg-[#0044DD] transition-colors text-sm">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                                @endif
                                <a href="{{ route('documents.show', $document->id) }}" 
                                    class="flex-1 text-center bg-gray-100 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-200 transition-colors text-sm">
                                    <i class="fas fa-edit mr-1"></i>Details
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $documents->links() }}
                    </div>
                    @else
                    <div class="text-center py-12">
                        <i class="fas fa-folder-open text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">No documents found</p>
                        <button wire:click="$dispatch('openUploadModal')" 
                            class="mt-4 bg-[#0053FF] text-white px-6 py-2 rounded-md hover:bg-[#0044DD] transition-colors">
                            Add Your First Document
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>
