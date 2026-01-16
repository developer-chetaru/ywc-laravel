<div x-data="{ showModal: @entangle('showModal').live }" 
     x-show="showModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true"
     style="display: none;">
    
    {{-- Backdrop --}}
    <div x-show="showModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" 
         aria-hidden="true"
         @click="showModal = false"></div>

    <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

    <div x-show="showModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
         class="inline-block w-full max-w-6xl my-8 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:align-middle">
        
        @if($document)
        {{-- Header --}}
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div>
                <h3 class="text-2xl font-bold text-gray-900" id="modal-title">
                    {{ $document->document_name ?? ($document->documentType->name ?? 'Document') }}
                </h3>
                @if($document->documentType)
                <p class="text-sm text-gray-600 mt-1">
                    <i class="fas fa-tag mr-1"></i>{{ $document->documentType->name }}
                </p>
                @endif
            </div>
            <button wire:click="closeModal" 
                    class="text-gray-400 hover:text-gray-600 text-3xl font-bold" 
                    aria-label="Close">
                &times;
            </button>
        </div>

        {{-- Content --}}
        <div class="flex flex-col lg:flex-row max-h-[80vh] overflow-hidden">
            {{-- Left Panel: Document Preview --}}
            <div class="w-full lg:w-1/2 border-r border-gray-200 p-6 bg-gray-50 flex flex-col items-center justify-center min-h-[400px] max-h-[80vh] overflow-auto">
                @if($document->file_path)
                    @if($isImage())
                        <div class="relative w-full">
                            <img id="documentPreview" 
                                 src="{{ asset('storage/' . $document->file_path) }}" 
                                 alt="Document Preview" 
                                 class="max-w-full max-h-[70vh] rounded-lg shadow-lg cursor-zoom-in"
                                 onclick="openImageModal(this.src)">
                            <div class="mt-4 text-center">
                                <button onclick="openImageModal('{{ asset('storage/' . $document->file_path) }}')" 
                                        class="text-sm text-[#0053FF] hover:text-[#0044DD]">
                                    <i class="fas fa-expand mr-1"></i>Click to zoom
                                </button>
                            </div>
                        </div>
                    @elseif($isPdf())
                        <div class="w-full h-[70vh] border border-gray-300 rounded-lg bg-white shadow-lg">
                            <iframe src="{{ asset('storage/' . $document->file_path) }}#toolbar=1" 
                                    class="w-full h-full rounded-lg"
                                    frameborder="0"></iframe>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ asset('storage/' . $document->file_path) }}" 
                               target="_blank" 
                               class="text-sm text-[#0053FF] hover:text-[#0044DD]">
                                <i class="fas fa-external-link-alt mr-1"></i>Open in new tab
                            </a>
                        </div>
                    @else
                        <div class="text-center">
                            <i class="fas fa-file text-6xl text-gray-400 mb-4"></i>
                            <p class="text-gray-600 mb-4">File type: {{ strtoupper($getFileExtension()) }}</p>
                            <a href="{{ asset('storage/' . $document->file_path) }}" 
                               target="_blank" 
                               download
                               class="inline-flex items-center px-4 py-2 bg-[#0053FF] text-white rounded-md hover:bg-[#0044DD]">
                                <i class="fas fa-download mr-2"></i>Download File
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center">
                        <i class="fas fa-file-slash text-6xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">No file available</p>
                    </div>
                @endif
            </div>

            {{-- Right Panel: Document Details --}}
            <div class="w-full lg:w-1/2 p-6 overflow-y-auto max-h-[80vh]">
                {{-- Status Section --}}
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Status</h4>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 text-sm font-medium rounded-full
                            @if($document->status === 'approved') bg-green-100 text-green-800
                            @elseif($document->status === 'rejected') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($document->status) }}
                        </span>
                        @if($document->status === 'rejected' && $document->notes)
                        <span class="text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>Review notes available
                        </span>
                        @endif
                    </div>
                    @if($document->status === 'rejected')
                        @php
                            $latestRejection = $document->statusChanges()
                                ->where('new_status', 'rejected')
                                ->latest()
                                ->first();
                        @endphp
                        @if($latestRejection && $latestRejection->notes)
                        <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm font-semibold text-red-900 mb-1">Rejection Reason:</p>
                            <p class="text-sm text-red-700 whitespace-pre-wrap">{{ $latestRejection->notes }}</p>
                        </div>
                        @endif
                    @endif
                    @if($document->updated_at)
                    <p class="text-sm text-gray-600 mt-2">
                        Last updated: {{ $document->updated_at->format('M d, Y') }}
                    </p>
                    @endif
                </div>

                {{-- Document Information --}}
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Document Information</h4>
                    <div class="space-y-3">
                        @if($document->document_number)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Document Number</label>
                            <p class="text-gray-900">{{ $document->document_number }}</p>
                        </div>
                        @endif

                        @if($document->issuing_authority)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Issuing Authority</label>
                            <p class="text-gray-900">{{ $document->issuing_authority }}</p>
                        </div>
                        @endif

                        @if($document->issuing_country)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Issuing Country</label>
                            <p class="text-gray-900">{{ $document->issuing_country }}</p>
                        </div>
                        @endif

                        @if($document->issue_date)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Issue Date</label>
                            <p class="text-gray-900">{{ $document->issue_date->format('M d, Y') }}</p>
                        </div>
                        @endif

                        @if($document->expiry_date)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Expiry Date</label>
                            <p class="text-gray-900">
                                {{ $document->expiry_date->format('M d, Y') }}
                                @if($document->days_until_expiry < 0)
                                    <span class="ml-2 text-red-600 font-semibold">(Expired)</span>
                                @elseif($document->days_until_expiry <= 30)
                                    <span class="ml-2 text-yellow-600 font-semibold">({{ $document->days_until_expiry }} days remaining)</span>
                                @else
                                    <span class="ml-2 text-green-600 font-semibold">(Valid)</span>
                                @endif
                            </p>
                        </div>
                        @endif

                        @if($document->dob)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Date of Birth</label>
                            <p class="text-gray-900">{{ $document->dob->format('M d, Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Certificate Details (if certificate type) --}}
                @if($document->documentType && $document->documentType->slug === 'certificate' && $document->certificates->count() > 0)
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Certificate Details</h4>
                    <div class="space-y-4">
                        @foreach($document->certificates as $certificate)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="space-y-2">
                                @if($certificate->certificateType)
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Certificate Type</label>
                                    <p class="text-gray-900">{{ $certificate->certificateType->name }}</p>
                                </div>
                                @endif

                                @if($certificate->certificateIssuer)
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Issuer</label>
                                    <p class="text-gray-900">{{ $certificate->certificateIssuer->name }}</p>
                                </div>
                                @endif

                                @if($certificate->certificate_number)
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Certificate Number</label>
                                    <p class="text-gray-900">{{ $certificate->certificate_number }}</p>
                                </div>
                                @endif

                                @if($certificate->issue_date)
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Issue Date</label>
                                    <p class="text-gray-900">{{ $certificate->issue_date->format('M d, Y') }}</p>
                                </div>
                                @endif

                                @if($certificate->expiry_date)
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Expiry Date</label>
                                    <p class="text-gray-900">{{ $certificate->expiry_date->format('M d, Y') }}</p>
                                </div>
                                @else
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Expiry Date</label>
                                    <p class="text-gray-900">—</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Passport Details (if passport type) --}}
                @if($document->documentType && $document->documentType->slug === 'passport' && $document->passportDetail)
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Passport Details</h4>
                    <div class="space-y-3">
                        @if($document->passportDetail->passport_number)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Passport Number</label>
                            <p class="text-gray-900">{{ $document->passportDetail->passport_number }}</p>
                        </div>
                        @endif
                        @if($document->passportDetail->nationality)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Nationality</label>
                            <p class="text-gray-900">{{ $document->passportDetail->nationality }}</p>
                        </div>
                        @endif
                        @if($document->passportDetail->country_code)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Country Code</label>
                            <p class="text-gray-900">{{ $document->passportDetail->country_code }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Notes --}}
                @if($document->notes)
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Notes</h4>
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $document->notes }}</p>
                </div>
                @endif

                {{-- File Information --}}
                @if($document->file_path)
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-3">File Information</h4>
                    <div class="space-y-2 text-sm">
                        @if($document->file_type)
                        <p class="text-gray-600">
                            <span class="font-medium">Type:</span> {{ strtoupper($document->file_type) }}
                        </p>
                        @endif
                        @if($document->file_size)
                        <p class="text-gray-600">
                            <span class="font-medium">Size:</span> {{ number_format($document->file_size / 1024, 2) }} KB
                        </p>
                        @endif
                        @if($document->uploaded_by && $document->uploader)
                        <p class="text-gray-600">
                            <span class="font-medium">Uploaded by:</span> {{ $document->uploader->first_name }} {{ $document->uploader->last_name }}
                        </p>
                        @endif
                        @if($document->created_at)
                        <p class="text-gray-600">
                            <span class="font-medium">Uploaded:</span> {{ $document->created_at->format('M d, Y') }}
                        </p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-wrap gap-2 justify-end">
            <button wire:click="closeModal" 
                    class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                <i class="fas fa-times mr-2"></i>Close
            </button>
            @if($document->file_path)
            <a href="{{ asset('storage/' . $document->file_path) }}" 
               download
               class="px-4 py-2 bg-[#0053FF] text-white rounded-md hover:bg-[#0044DD] transition-colors">
                <i class="fas fa-download mr-2"></i>Download
            </a>
            @endif
            @if($document->status === 'rejected')
            <button wire:click="resubmit" 
                    class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors">
                <i class="fas fa-redo mr-2"></i>Re-Submit
            </button>
            @endif
            <button wire:click="share" 
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                <i class="fas fa-share-alt mr-2"></i>Share
            </button>
            @if($document->status !== 'rejected')
            <button wire:click="edit" 
                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                <i class="fas fa-edit mr-2"></i>Edit
            </button>
            @endif
            <button wire:click="delete" 
                    onclick="return confirm('Are you sure you want to delete this document? This action cannot be undone.')"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                <i class="fas fa-trash mr-2"></i>Delete
            </button>
        </div>
        @endif
    </div>
</div>

{{-- Image Zoom Modal --}}
<div id="imageZoomModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black bg-opacity-90" onclick="closeImageModal()">
    <div class="max-w-7xl max-h-[90vh] p-4">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white text-3xl font-bold hover:text-gray-300">&times;</button>
        <img id="zoomedImage" src="" alt="Zoomed Document" class="max-w-full max-h-[90vh] rounded-lg">
    </div>
</div>

<script>
function openImageModal(src) {
    document.getElementById('zoomedImage').src = src;
    document.getElementById('imageZoomModal').classList.remove('hidden');
    document.getElementById('imageZoomModal').classList.add('flex');
}

function closeImageModal() {
    document.getElementById('imageZoomModal').classList.add('hidden');
    document.getElementById('imageZoomModal').classList.remove('flex');
}
</script>
