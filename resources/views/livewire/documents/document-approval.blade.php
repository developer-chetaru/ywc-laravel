<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Login Form (if not authenticated) --}}
        @if($showLogin)
            <div class="max-w-md mx-auto mt-20">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Login to Approve Documents</h2>
                    
                    @if (session()->has('error'))
                        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="login">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input 
                                type="email" 
                                wire:model="email"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter your email"
                                required
                            >
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input 
                                type="password" 
                                wire:model="password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter your password"
                                required
                            >
                        </div>

                        <button 
                            type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors"
                        >
                            Login
                        </button>
                    </form>

                    <p class="text-sm text-gray-600 mt-4 text-center">
                        Or access via token: <code class="bg-gray-100 px-2 py-1 rounded">{{ url('/documents/approval?token=YOUR_TOKEN') }}</code>
                    </p>
                </div>
            </div>
        @else
        {{-- Header --}}
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Document Approval</h1>
                    <p class="text-gray-600 mt-1">Review and approve or reject pending documents</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-blue-600">{{ $documents->total() }}</div>
                    <div class="text-sm text-gray-500">Pending Documents</div>
                </div>
            </div>

            {{-- Search and Filter --}}
            <div class="flex flex-col sm:flex-row gap-4 mt-4">
                <div class="flex-1">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by document name, number, or owner name..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>
                <div class="sm:w-48">
                    <select 
                        wire:model.live="filterType"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="all">All Types</option>
                        <option value="passport">Passport</option>
                        <option value="idvisa">ID/Visa</option>
                        <option value="certificate">Certificate</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Success Message --}}
        @if (session()->has('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button wire:click="$set('flash', null)" class="text-green-600 hover:text-green-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        {{-- Documents Grid --}}
        @if($documents->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($documents as $document)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                        {{-- Document Preview --}}
                        <div class="bg-gray-100 h-48 flex items-center justify-center relative">
                            @php
                                $fileUrl = $this->getDocumentUrl($document);
                                $extension = strtolower(pathinfo($document->file_path ?? '', PATHINFO_EXTENSION));
                            @endphp
                            
                            @if($fileUrl && in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <img src="{{ $fileUrl }}" alt="{{ $document->document_name }}" 
                                     class="max-h-full max-w-full object-contain cursor-pointer"
                                     onclick="window.open('{{ $fileUrl }}', '_blank')">
                            @elseif($fileUrl && $extension === 'pdf')
                                <div class="text-center">
                                    <svg class="w-16 h-16 mx-auto text-red-500 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 18h12V6h-4V2H4v16zm-2 1V0h12l4 4v16H2v-1z"/>
                                    </svg>
                                    <a href="{{ $fileUrl }}" target="_blank" 
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View PDF
                                    </a>
                                </div>
                            @else
                                <div class="text-center text-gray-400">
                                    <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-sm">No Preview</p>
                                </div>
                            @endif
                        </div>

                        {{-- Document Info --}}
                        <div class="p-4">
                            <h3 class="font-semibold text-lg text-gray-900 mb-2 truncate">
                                {{ $document->document_name ?? ($document->documentType->name ?? 'Unnamed Document') }}
                            </h3>
                            
                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="font-medium">Owner:</span>
                                    <span class="ml-1">{{ $document->user->first_name }} {{ $document->user->last_name }}</span>
                                </div>
                                
                                @if($document->document_number)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    <span class="font-medium">Number:</span>
                                    <span class="ml-1">{{ $document->document_number }}</span>
                                </div>
                                @endif

                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    <span class="font-medium">Type:</span>
                                    <span class="ml-1 capitalize">{{ $document->type ?? 'N/A' }}</span>
                                </div>

                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="font-medium">Uploaded:</span>
                                    <span class="ml-1">{{ $document->created_at->format('M d, Y') }}</span>
                                </div>

                                @if($document->expiry_date)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="font-medium">Expires:</span>
                                    <span class="ml-1 {{ $document->expiry_date->isPast() ? 'text-red-600 font-semibold' : '' }}">
                                        {{ $document->expiry_date->format('M d, Y') }}
                                    </span>
                                </div>
                                @endif
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex gap-2 mt-4">
                                <button 
                                    wire:click="openModal({{ $document->id }}, 'approve')"
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Approve
                                </button>
                                <button 
                                    wire:click="openModal({{ $document->id }}, 'reject')"
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Reject
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $documents->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Pending Documents</h3>
                <p class="text-gray-600">All documents have been reviewed. Great job! 🎉</p>
            </div>
        @endif
    </div>

    {{-- Approval/Rejection Modal --}}
    @if($showModal && $selectedDocument)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showModal') }" x-show="show" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4">
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                     wire:click="closeModal"></div>

                {{-- Modal --}}
                <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6 z-10"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ ucfirst($action) }} Document
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="mb-4">
                        <p class="text-gray-700 mb-2">
                            <span class="font-semibold">Document:</span> 
                            {{ $selectedDocument->document_name ?? ($selectedDocument->documentType->name ?? 'Unnamed Document') }}
                        </p>
                        <p class="text-gray-700 mb-2">
                            <span class="font-semibold">Owner:</span> 
                            {{ $selectedDocument->user->first_name }} {{ $selectedDocument->user->last_name }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notes (Optional)
                        </label>
                        <textarea 
                            wire:model="notes"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Add any notes about your decision..."
                        ></textarea>
                    </div>

                    <div class="flex gap-3 justify-end">
                        <button 
                            wire:click="closeModal"
                            class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium transition-colors"
                        >
                            Cancel
                        </button>
                        @if($action === 'approve')
                            <button 
                                wire:click="approveDocument"
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition-colors flex items-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Confirm Approval
                            </button>
                        @else
                            <button 
                                wire:click="rejectDocument"
                                class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors flex items-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Confirm Rejection
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
    @endif
</div>
