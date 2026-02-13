<div class="space-y-4">
    @if(session('version_message'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md">
        {{ session('version_message') }}
    </div>
    @endif

    @if(session('version_error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md">
        {{ session('version_error') }}
    </div>
    @endif

    @if($document)
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-end mb-4">
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-600">
                    Current Version: <strong>v{{ $document->version }}</strong>
                </span>
                @if($versions->count() > 1)
                <button 
                    wire:click="openComparison"
                    class="px-3 py-1 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors text-sm font-medium">
                    <i class="fas fa-code-branch mr-1"></i>Compare Versions
                </button>
                @endif
                @if(count($selectedVersionsForCleanup) > 0)
                <button 
                    wire:click="bulkCleanup"
                    onclick="return confirm('Are you sure you want to delete {{ count($selectedVersionsForCleanup) }} selected version(s)? This cannot be undone.')"
                    class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors text-sm font-medium">
                    <i class="fas fa-trash mr-1"></i>Delete Selected ({{ count($selectedVersionsForCleanup) }})
                </button>
                @endif
            </div>
        </div>

        @if($versions->count() > 0)
        <div class="space-y-3">
            @foreach($versions as $version)
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                Version {{ $version->version_number }}
                            </span>
                            @if($version->version_number == $document->version)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">
                                Current
                            </span>
                            @endif
                            <span class="text-sm text-gray-500">
                                {{ $version->created_at->format('M d, Y h:i A') }}
                            </span>
                        </div>

                        @if($version->creator)
                        <p class="text-sm text-gray-600 mb-2">
                            <i class="fas fa-user mr-1"></i>
                            Created by: {{ $version->creator->name ?? $version->creator->email }}
                        </p>
                        @endif

                        @if($version->change_notes)
                        <p class="text-sm text-gray-700 mb-2">
                            <i class="fas fa-sticky-note mr-1"></i>
                            {{ $version->change_notes }}
                        </p>
                        @endif

                        <div class="flex items-center gap-4 text-xs text-gray-500 mt-2">
                            @if($version->file_size)
                            <span>
                                <i class="fas fa-file mr-1"></i>
                                {{ $version->formatted_file_size }}
                            </span>
                            @endif
                            @if($version->file_type)
                            <span>
                                <i class="fas fa-file-alt mr-1"></i>
                                {{ strtoupper($version->file_type) }}
                            </span>
                            @endif
                            @if($version->ocr_status)
                            <span class="px-2 py-0.5 rounded
                                @if($version->ocr_status === 'completed') bg-blue-100 text-blue-800
                                @elseif($version->ocr_status === 'processing') bg-yellow-100 text-yellow-800
                                @elseif($version->ocr_status === 'failed') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                OCR: {{ ucfirst($version->ocr_status) }}
                            </span>
                            @endif
                        </div>

                        @if($version->metadata)
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <details class="text-sm">
                                <summary class="cursor-pointer text-gray-600 hover:text-gray-900">
                                    <i class="fas fa-info-circle mr-1"></i>View Metadata
                                </summary>
                                <div class="mt-2 pl-4 space-y-1 text-gray-600">
                                    @if($version->metadata['document_name'] ?? null)
                                    <p><strong>Name:</strong> {{ $version->metadata['document_name'] }}</p>
                                    @endif
                                    @if($version->metadata['document_number'] ?? null)
                                    <p><strong>Number:</strong> {{ $version->metadata['document_number'] }}</p>
                                    @endif
                                    @if($version->metadata['issue_date'] ?? null)
                                    <p><strong>Issue Date:</strong> {{ \Carbon\Carbon::parse($version->metadata['issue_date'])->format('M d, Y') }}</p>
                                    @endif
                                    @if($version->metadata['expiry_date'] ?? null)
                                    <p><strong>Expiry Date:</strong> {{ \Carbon\Carbon::parse($version->metadata['expiry_date'])->format('M d, Y') }}</p>
                                    @endif
                                </div>
                            </details>
                        </div>
                        @endif
                    </div>

                    <div class="ml-4 flex gap-2">
                        @if($version->file_path)
                        <button 
                            wire:click="previewVersion({{ $version->id }})"
                            class="px-3 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors text-sm font-medium"
                            title="Preview">
                            <i class="fas fa-eye"></i>
                        </button>
                        <a 
                            href="{{ route('documents.versions.download', $version->id) }}"
                            class="px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors text-sm font-medium"
                            title="Download">
                            <i class="fas fa-download"></i>
                        </a>
                        @endif
                        @if($version->version_number != $document->version)
                        <button 
                            wire:click="confirmRestore({{ $version->id }})"
                            class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm font-medium"
                            title="Restore">
                            <i class="fas fa-undo"></i>
                        </button>
                        @endif
                        @if($version->version_number != $document->version)
                        <input 
                            type="checkbox" 
                            wire:model="selectedVersionsForCleanup"
                            value="{{ $version->id }}"
                            class="mt-2"
                            title="Select for cleanup">
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-history text-4xl mb-3"></i>
            <p>No version history available yet.</p>
            <p class="text-sm mt-1">Versions will be created automatically when you update this document.</p>
        </div>
        @endif
    </div>
    @endif

    {{-- Version Preview Modal --}}
    @if($showPreview && $this->previewVersion)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click="closePreview">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden flex flex-col" wire:click.stop>
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">
                    Preview: Version {{ $this->previewVersion->version_number }}
                </h3>
                <button wire:click="closePreview" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="flex-1 overflow-auto p-4">
                @if($this->previewVersion->file_path && \Storage::disk('public')->exists($this->previewVersion->file_path))
                    @php
                        $extension = strtolower(pathinfo($this->previewVersion->file_path, PATHINFO_EXTENSION));
                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        $isPdf = $extension === 'pdf';
                    @endphp
                    @if($isImage)
                    <div class="text-center">
                        <img src="{{ asset('storage/' . $this->previewVersion->file_path) }}" 
                             alt="Version Preview" 
                             class="max-w-full max-h-[70vh] rounded-lg shadow-lg">
                    </div>
                    @elseif($isPdf)
                    <div class="w-full h-[70vh] border border-gray-300 rounded-lg bg-white">
                        <iframe src="{{ asset('storage/' . $this->previewVersion->file_path) }}#toolbar=1" 
                                class="w-full h-full rounded-lg"
                                frameborder="0"></iframe>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <i class="fas fa-file text-6xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 mb-4">File type: {{ strtoupper($extension) }}</p>
                        <a href="{{ route('documents.versions.download', $this->previewVersion->id) }}" 
                           download
                           class="inline-flex items-center px-4 py-2 bg-[#0053FF] text-white rounded-md hover:bg-[#0044DD]">
                            <i class="fas fa-download mr-2"></i>Download Version
                        </a>
                    </div>
                    @endif
                @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-file-slash text-6xl mb-4"></i>
                    <p>File not available for this version</p>
                </div>
                @endif
            </div>
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">Created:</span>
                        <span class="text-gray-600">{{ $this->previewVersion->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($this->previewVersion->creator)
                    <div>
                        <span class="font-medium text-gray-700">Created by:</span>
                        <span class="text-gray-600">{{ $this->previewVersion->creator->name ?? $this->previewVersion->creator->email }}</span>
                    </div>
                    @endif
                    @if($this->previewVersion->change_notes)
                    <div class="col-span-2">
                        <span class="font-medium text-gray-700">Change Notes:</span>
                        <span class="text-gray-600">{{ $this->previewVersion->change_notes }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Version Comparison Modal --}}
    @if($showComparison)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click="closeComparison">
        <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full mx-4 max-h-[90vh] overflow-hidden flex flex-col" wire:click.stop>
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Compare Versions</h3>
                <button wire:click="closeComparison" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-4 flex-1 overflow-auto">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Version 1</label>
                        <select wire:model="compareVersionId1" class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="">Select version...</option>
                            @foreach($versions as $v)
                            <option value="{{ $v->id }}">Version {{ $v->version_number }} ({{ $v->created_at->format('M d, Y') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Version 2</label>
                        <select wire:model="compareVersionId2" class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="">Select version...</option>
                            @foreach($versions as $v)
                            <option value="{{ $v->id }}">Version {{ $v->version_number }} ({{ $v->created_at->format('M d, Y') }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if($compareVersionId1 && $compareVersionId2)
                @php
                    $v1 = \App\Models\DocumentVersion::find($compareVersionId1);
                    $v2 = \App\Models\DocumentVersion::find($compareVersionId2);
                @endphp
                @if($v1 && $v2)
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-4">Metadata Comparison</h4>
                    <div class="space-y-3">
                        @php
                            $fields = [
                                'document_name' => 'Document Name',
                                'document_number' => 'Document Number',
                                'issuing_authority' => 'Issuing Authority',
                                'issuing_country' => 'Issuing Country',
                                'issue_date' => 'Issue Date',
                                'expiry_date' => 'Expiry Date',
                            ];
                        @endphp
                        @foreach($fields as $key => $label)
                        <div class="grid grid-cols-3 gap-4 py-2 border-b border-gray-100">
                            <div class="font-medium text-gray-700">{{ $label }}:</div>
                            <div class="text-sm {{ ($v1->metadata[$key] ?? null) !== ($v2->metadata[$key] ?? null) ? 'bg-yellow-50 text-yellow-800' : 'text-gray-600' }}">
                                {{ $v1->metadata[$key] ?? 'N/A' }}
                            </div>
                            <div class="text-sm {{ ($v1->metadata[$key] ?? null) !== ($v2->metadata[$key] ?? null) ? 'bg-yellow-50 text-yellow-800' : 'text-gray-600' }}">
                                {{ $v2->metadata[$key] ?? 'N/A' }}
                            </div>
                        </div>
                        @endforeach
                        <div class="grid grid-cols-3 gap-4 py-2">
                            <div class="font-medium text-gray-700">File Size:</div>
                            <div class="text-sm text-gray-600">{{ $v1->formatted_file_size }}</div>
                            <div class="text-sm text-gray-600">{{ $v2->formatted_file_size }}</div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 py-2">
                            <div class="font-medium text-gray-700">OCR Status:</div>
                            <div class="text-sm text-gray-600">{{ ucfirst($v1->ocr_status ?? 'N/A') }}</div>
                            <div class="text-sm text-gray-600">{{ ucfirst($v2->ocr_status ?? 'N/A') }}</div>
                        </div>
                    </div>
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Restore Confirmation Modal --}}
    @if($showRestoreConfirm)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click="cancelRestore">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4" wire:click.stop>
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Restore Version?</h3>
                <p class="text-gray-600 mb-6">
                    This will create a new version of the current document and restore the selected version. 
                    The current version will be saved before restoration.
                </p>
                <div class="flex gap-3 justify-end">
                    <button 
                        wire:click="cancelRestore"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button 
                        wire:click="restoreVersion"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-undo mr-1"></i>Restore
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
