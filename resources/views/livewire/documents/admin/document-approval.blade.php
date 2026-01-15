<div>
    <main class="flex-1 flex flex-col bg-gray-100 p-4 sm:p-6">
        <div class="flex-1 overflow-hidden">
            <div class="p-4 bg-[#F5F6FA]">
                <div class="rounded-lg bg-white p-4 sm:p-6">
                    {{-- Header --}}
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Document Approval</h1>
                        <p class="text-sm text-gray-600 mt-1">Review and approve pending documents</p>
                    </div>

                    {{-- Filters --}}
                    <div class="bg-[#F5F6FA] p-4 rounded-lg mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                            
                            {{-- User Filter --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                                <select wire:model.live="selectedUserId" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            {{-- Search --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input type="text" wire:model.live.debounce.300ms="search" 
                                    placeholder="Search documents..." 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]">
                            </div>
                        </div>
                    </div>

                    {{-- Flash Message --}}
                    @if(session()->has('message'))
                    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4">
                        <p class="text-sm text-green-700">{{ session('message') }}</p>
                    </div>
                    @endif

                    {{-- Documents Table --}}
                    @if($documents->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($documents as $document)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($document->thumbnail_path)
                                            <img src="{{ asset('storage/' . $document->thumbnail_path) }}" 
                                                alt="Thumbnail" 
                                                class="w-10 h-10 object-cover rounded mr-3">
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $document->document_name ?? 'Document #' . $document->id }}
                                                </div>
                                                @if($document->document_number)
                                                <div class="text-sm text-gray-500">#{{ $document->document_number }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $document->user->first_name }} {{ $document->user->last_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $document->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ $document->documentType->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded
                                            @if($document->status === 'approved') bg-green-100 text-green-800
                                            @elseif($document->status === 'rejected') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800
                                            @endif">
                                            {{ ucfirst($document->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $document->expiry_date ? \Carbon\Carbon::parse($document->expiry_date)->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex gap-2">
                                            @if($document->file_path)
                                            <a href="{{ asset('storage/' . $document->file_path) }}" 
                                                target="_blank" 
                                                class="text-[#0053FF] hover:text-[#0044DD]">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endif
                                            @if($document->status === 'pending')
                                            <button wire:click="openModal({{ $document->id }}, 'approve')" 
                                                class="text-green-600 hover:text-green-800">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button wire:click="openModal({{ $document->id }}, 'reject')" 
                                                class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $documents->links() }}
                    </div>
                    @else
                    <div class="text-center py-12">
                        <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">No documents found</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    {{-- Approval/Rejection Modal --}}
    @if($showModal && $selectedDocument)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
         wire:click="closeModal">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" 
             wire:click.stop>
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $action === 'approve' ? 'Approve Document' : 'Reject Document' }}
                </h3>
                
                <div class="mb-4">
                    <p class="text-sm text-gray-600">
                        <strong>Document:</strong> {{ $selectedDocument->document_name ?? 'Document #' . $selectedDocument->id }}
                    </p>
                    <p class="text-sm text-gray-600">
                        <strong>User:</strong> {{ $selectedDocument->user->first_name }} {{ $selectedDocument->user->last_name }}
                    </p>
                </div>

                @if($action === 'approve')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Approval Notes (Optional)</label>
                    <textarea wire:model="approvalNotes" 
                        rows="3" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]"
                        placeholder="Add any notes about this approval..."></textarea>
                </div>
                <div class="flex gap-2">
                    <button wire:click="approve" 
                        class="flex-1 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                        Approve
                    </button>
                    <button wire:click="closeModal" 
                        class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                </div>
                @else
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason <span class="text-red-500">*</span></label>
                    <textarea wire:model="rejectionNotes" 
                        rows="3" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]"
                        placeholder="Please provide a reason for rejection..."></textarea>
                    @error('rejectionNotes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="flex gap-2">
                    <button wire:click="reject" 
                        class="flex-1 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                        Reject
                    </button>
                    <button wire:click="closeModal" 
                        class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
