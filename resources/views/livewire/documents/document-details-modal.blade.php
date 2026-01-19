<div x-data="{ showModal: @entangle('showModal').live }" 
     x-show="showModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="z-index: 50;" 
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
        {{-- Flash Messages --}}
        @if(session()->has('ocr_message'))
        <div class="px-6 py-3 bg-blue-50 border-b border-blue-200">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>{{ session('ocr_message') }}
            </p>
        </div>
        @endif
        
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

                {{-- OCR Status Section --}}
                @if($document->ocr_status)
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-lg font-semibold text-gray-900">OCR Status</h4>
                        @if($document->ocr_status === 'failed')
                        <button wire:click="retryOcr({{ $document->id }})" 
                                class="px-3 py-1 text-sm bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors">
                            <i class="fas fa-sync-alt mr-1"></i>Retry OCR
                        </button>
                        @endif
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 text-sm font-medium rounded-full
                                @if($document->ocr_status === 'completed') bg-blue-100 text-blue-800
                                @elseif($document->ocr_status === 'processing') bg-yellow-100 text-yellow-800
                                @elseif($document->ocr_status === 'failed') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                <i class="fas fa-eye mr-1"></i>{{ ucfirst($document->ocr_status) }}
                            </span>
                            @if($document->ocr_status === 'completed' && $document->ocr_confidence)
                            <span class="text-sm text-gray-600">
                                Confidence: <strong class="text-gray-900">{{ number_format($document->ocr_confidence, 1) }}%</strong>
                            </span>
                            @endif
                        </div>
                        @if($document->ocr_status === 'failed' && $document->ocr_error)
                        <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm font-semibold text-red-900 mb-1">Error:</p>
                            <p class="text-sm text-red-700">{{ $document->ocr_error }}</p>
                        </div>
                        @endif
                        @if($document->ocr_status === 'completed' && $document->ocr_data)
                            @if(isset($document->ocr_data['text']) && !empty($document->ocr_data['text']))
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Extracted Text</label>
                                <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg max-h-32 overflow-y-auto">
                                    <p class="text-xs text-gray-700 whitespace-pre-wrap">{{ \Illuminate\Support\Str::limit($document->ocr_data['text'], 500) }}</p>
                                </div>
                            </div>
                            @endif
                            @if(isset($document->ocr_data['fields']) && !empty($document->ocr_data['fields']))
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Extracted Fields</label>
                                <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                    <div class="space-y-1 text-xs">
                                        @foreach($document->ocr_data['fields'] as $key => $value)
                                            @if($value)
                                            <p><span class="font-semibold text-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span> <span class="text-gray-600">{{ $value }}</span></p>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endif
                    </div>
                </div>
                @endif

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

                {{-- Version History Section (Collapsed by default, expandable) --}}
        <div class="mb-6 pb-6 border-b border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-lg font-semibold text-gray-900">Version History</h4>
                <button 
                    onclick="window.openVersionHistoryModal({{ $document->id }})"
                    class="px-3 py-1 text-sm bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors">
                    <i class="fas fa-history mr-1"></i>View Full History
                </button>
            </div>
            <div class="space-y-3">
                <p class="text-sm text-gray-600 mb-3">
                    Current version: <strong>v{{ $document->version }}</strong>
                </p>
                @php
                    $versions = \App\Models\DocumentVersion::where('document_id', $document->id)
                        ->with('creator')
                        ->latest('version_number')
                        ->take(3)
                        ->get();
                @endphp
                @if($versions->count() > 0)
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($versions as $version)
                    <div class="border border-gray-200 rounded-lg p-3 text-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">
                                    v{{ $version->version_number }}
                                </span>
                                @if($version->version_number == $document->version)
                                <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Current</span>
                                @endif
                                <span class="ml-2 text-xs text-gray-500">
                                    {{ $version->created_at->format('M d, Y') }}
                                </span>
                            </div>
                            @if($version->change_notes)
                            <span class="text-xs text-gray-600" title="{{ $version->change_notes }}">
                                <i class="fas fa-sticky-note"></i>
                            </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @if(\App\Models\DocumentVersion::where('document_id', $document->id)->count() > 3)
                    <p class="text-xs text-gray-500 text-center mt-2">
                        <a href="#" onclick="window.openVersionHistoryModal({{ $document->id }}); return false;" class="text-purple-600 hover:text-purple-800">
                            View all {{ \App\Models\DocumentVersion::where('document_id', $document->id)->count() }} versions
                        </a>
                    </p>
                    @endif
                </div>
                @else
                <p class="text-sm text-gray-500">No version history yet. Versions are created automatically when you update the document.</p>
                @endif
            </div>
        </div>

        {{-- Verification Section --}}
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Verification</h4>
                    <div class="space-y-3">
                        @if($document->verificationLevel)
                        <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-3 py-1 text-sm font-medium rounded-full
                                    @if($document->verificationLevel->badge_color === 'gold') bg-yellow-100 text-yellow-800
                                    @elseif($document->verificationLevel->badge_color === 'purple') bg-purple-100 text-purple-800
                                    @elseif($document->verificationLevel->badge_color === 'green') bg-green-100 text-green-800
                                    @elseif($document->verificationLevel->badge_color === 'blue') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    <i class="{{ $document->verificationLevel->badge_icon }} mr-1"></i>
                                    {{ $document->verificationLevel->name }} (Level {{ $document->verificationLevel->level }})
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">{{ $document->verificationLevel->description }}</p>
                        </div>
                        @else
                        <p class="text-sm text-gray-600 mb-3">This document has not been verified yet.</p>
                        @endif

                        @if($document->user_id === auth()->id())
                        <button onclick="openVerificationModal({{ $document->id }})" 
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm font-medium">
                            <i class="fas fa-shield-alt mr-2"></i>Request Verification
                        </button>
                        @endif
                    </div>
                </div>

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
            <button onclick="openVersionHistoryModal({{ $document->id }}); return false;" 
                    class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors">
                <i class="fas fa-history mr-2"></i>History
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

function openVerificationModal(documentId) {
    // Create modal if it doesn't exist
    let modal = document.getElementById('verificationModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'verificationModal';
        modal.className = 'hidden fixed inset-0 z-50 overflow-y-auto';
        modal.innerHTML = `
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeVerificationModal()"></div>
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Verification</h3>
                    <form id="verificationForm">
                        <input type="hidden" id="verificationDocumentId" name="document_id">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Verification Level</label>
                            <select id="verificationLevel" name="verification_level_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Select level...</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                            <textarea id="verificationNotes" name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <div class="flex gap-2 justify-end">
                            <button type="button" onclick="closeVerificationModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Load verification levels
        fetch('/api/verification-levels')
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('verificationLevel');
                data.forEach(level => {
                    const option = document.createElement('option');
                    option.value = level.id;
                    option.textContent = `${level.name} (Level ${level.level})`;
                    select.appendChild(option);
                });
            })
            .catch(err => console.error('Failed to load verification levels:', err));
        
        // Handle form submission
        document.getElementById('verificationForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const docId = document.getElementById('verificationDocumentId').value;
            const formData = new FormData(this);
            
            try {
                const response = await fetch(`/documents/${docId}/request-verification`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Verification request submitted successfully!');
                    closeVerificationModal();
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to submit verification request');
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
                console.error(error);
            }
        });
    }
    
    document.getElementById('verificationDocumentId').value = documentId;
    modal.classList.remove('hidden');
}

function closeVerificationModal() {
    const modal = document.getElementById('verificationModal');
    if (modal) {
        modal.classList.add('hidden');
        const form = document.getElementById('verificationForm');
        if (form) form.reset();
    }
}

function closeImageModal() {
    document.getElementById('imageZoomModal').classList.add('hidden');
    document.getElementById('imageZoomModal').classList.remove('flex');
}

// Open Version History Modal
window.openVersionHistoryModal = function(documentId) {
    // Close current document modal first
    if (window.Livewire) {
        Livewire.dispatch('closeDocumentDetails');
    }
    
    // Open version history modal with higher z-index
    setTimeout(() => {
        let historyModal = document.getElementById('versionHistoryModal');
        if (!historyModal) {
            historyModal = document.createElement('div');
            historyModal.id = 'versionHistoryModal';
            historyModal.className = 'fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50';
            historyModal.style.zIndex = '60';
            document.body.appendChild(historyModal);
            historyModal.addEventListener('click', function(e) {
                if (e.target === historyModal) {
                    closeVersionHistoryModal();
                }
            });
        }
        
        // Show loading state
        historyModal.innerHTML = `
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden flex flex-col" onclick="event.stopPropagation()">
                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Version History</h3>
                    <button onclick="closeVersionHistoryModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="flex-1 overflow-auto p-4">
                    <div id="versionHistoryContent">
                        <div class="text-center py-8">
                            <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                            <p class="text-gray-600">Loading version history...</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        historyModal.style.display = 'flex';
        
        // Load version history via AJAX
        fetch(`/documents/${documentId}/versions`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
            .then(res => {
                if (!res.ok) throw new Error('Failed to load');
                return res.text();
            })
            .then(html => {
                const contentDiv = document.getElementById('versionHistoryContent');
                if (contentDiv) {
                    contentDiv.innerHTML = html;
                }
            })
            .catch(err => {
                console.error('Failed to load version history:', err);
                const contentDiv = document.getElementById('versionHistoryContent');
                if (contentDiv) {
                    contentDiv.innerHTML = '<div class="text-center py-8 text-red-600"><i class="fas fa-exclamation-circle text-2xl mb-2"></i><p>Failed to load version history. Please try again.</p></div>';
                }
            });
    }, 200);
};

function closeVersionHistoryModal() {
    const modal = document.getElementById('versionHistoryModal');
    if (modal) {
        modal.style.display = 'none';
        modal.innerHTML = '';
    }
}
</script>
