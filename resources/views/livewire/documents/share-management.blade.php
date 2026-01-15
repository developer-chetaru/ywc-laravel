<div>
    <main class="flex-1 flex flex-col bg-gray-100 p-4 sm:p-6">
        <div class="flex-1 overflow-hidden">
            <div class="p-4 bg-[#F5F6FA]">
                <div class="rounded-lg bg-white p-4 sm:p-6">
                    {{-- Header --}}
                    <div class="mb-6 flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Document Shares</h1>
                            <p class="text-sm text-gray-600 mt-1">Manage your shared documents</p>
                        </div>
                        <button wire:click="openCreateModal" 
                            class="bg-[#0053FF] text-white px-4 py-2 rounded-md hover:bg-[#0044DD] transition-colors text-sm font-medium">
                            <i class="fas fa-plus mr-2"></i>Create Share
                        </button>
                    </div>

                    {{-- Flash Message --}}
                    @if(session()->has('message'))
                    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4">
                        <p class="text-sm text-green-700">{{ session('message') }}</p>
                    </div>
                    @endif

                    @error('create') 
                    <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                        <p class="text-sm text-red-700">{{ $message }}</p>
                    </div>
                    @enderror

                    {{-- Shares List --}}
                    @if($shares->count() > 0)
                    <div class="space-y-4">
                        @foreach($shares as $share)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="font-semibold text-gray-900">Share #{{ $share->id }}</h3>
                                        @if($share->isExpired())
                                        <span class="px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-800">Expired</span>
                                        @elseif(!$share->is_active)
                                        <span class="px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800">Inactive</span>
                                        @else
                                        <span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">Active</span>
                                        @endif
                                    </div>
                                    
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <p><strong>Documents:</strong> {{ $share->documents()->count() }}</p>
                                        @if($share->recipient_email)
                                        <p><strong>Recipient:</strong> {{ $share->recipient_email }}</p>
                                        @endif
                                        @if($share->expires_at)
                                        <p><strong>Expires:</strong> {{ \Carbon\Carbon::parse($share->expires_at)->format('M d, Y') }}</p>
                                        @else
                                        <p><strong>Expires:</strong> Never</p>
                                        @endif
                                        <p><strong>Views:</strong> {{ $share->view_count }}</p>
                                        <p><strong>Created:</strong> {{ $share->created_at->format('M d, Y') }}</p>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <div class="flex items-center gap-2">
                                            <input type="text" 
                                                value="{{ $share->share_url }}" 
                                                readonly 
                                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-sm"
                                                id="share-url-{{ $share->id }}">
                                            <button onclick="copyToClipboard('share-url-{{ $share->id }}')" 
                                                class="bg-gray-100 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-200 transition-colors text-sm">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex flex-col gap-2">
                                    @if($share->recipient_email)
                                    <button wire:click="resendEmail({{ $share->id }})" 
                                        class="bg-blue-100 text-blue-700 px-4 py-2 rounded-md hover:bg-blue-200 transition-colors text-sm">
                                        <i class="fas fa-envelope mr-1"></i>Resend Email
                                    </button>
                                    @endif
                                    <button wire:click="revokeShare({{ $share->id }})" 
                                        onclick="return confirm('Are you sure you want to revoke this share?')"
                                        class="bg-red-100 text-red-700 px-4 py-2 rounded-md hover:bg-red-200 transition-colors text-sm">
                                        <i class="fas fa-ban mr-1"></i>Revoke
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $shares->links() }}
                    </div>
                    @else
                    <div class="text-center py-12">
                        <i class="fas fa-share-alt text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">No shares created yet</p>
                        <button wire:click="openCreateModal" 
                            class="mt-4 bg-[#0053FF] text-white px-6 py-2 rounded-md hover:bg-[#0044DD] transition-colors">
                            Create Your First Share
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    {{-- Create Share Modal --}}
    @if($showCreateModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
         wire:click="closeCreateModal">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white" 
             wire:click.stop>
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Create Document Share</h3>
                
                <form wire:submit.prevent="createShare">
                    {{-- Document Selection --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Documents <span class="text-red-500">*</span></label>
                        <div class="border border-gray-300 rounded-md p-3 max-h-60 overflow-y-auto">
                            @foreach($availableDocuments as $doc)
                            <label class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                <input type="checkbox" 
                                    wire:model="selectedDocuments" 
                                    value="{{ $doc->id }}"
                                    class="rounded border-gray-300 text-[#0053FF] focus:ring-[#0053FF]">
                                <span class="text-sm text-gray-700">
                                    {{ $doc->document_name ?? ($doc->documentType->name ?? 'Document #' . $doc->id) }}
                                    @if($doc->document_number) - #{{ $doc->document_number }} @endif
                                </span>
                            </label>
                            @endforeach
                        </div>
                        @error('selectedDocuments') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- Recipient Email --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recipient Email (Optional)</label>
                        <input type="email" 
                            wire:model="recipientEmail" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]"
                            placeholder="recipient@example.com">
                        @error('recipientEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- Recipient Name --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recipient Name (Optional)</label>
                        <input type="text" 
                            wire:model="recipientName" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]"
                            placeholder="John Doe">
                        @error('recipientName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- Personal Message --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Personal Message (Optional)</label>
                        <textarea wire:model="personalMessage" 
                            rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]"
                            placeholder="Add a personal message..."></textarea>
                        @error('personalMessage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- Expiry --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expires In (Days, Optional)</label>
                        <input type="number" 
                            wire:model="expiresInDays" 
                            min="1" 
                            max="365"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]"
                            placeholder="30">
                        @error('expiresInDays') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="flex gap-2">
                        <button type="submit" 
                            class="flex-1 bg-[#0053FF] text-white px-4 py-2 rounded-md hover:bg-[#0044DD] transition-colors">
                            Create Share
                        </button>
                        <button type="button" 
                            wire:click="closeCreateModal" 
                            class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <script>
        function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            element.select();
            document.execCommand('copy');
            alert('Share URL copied to clipboard!');
        }
    </script>
</div>
