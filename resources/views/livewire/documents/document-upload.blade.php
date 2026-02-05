<div>
    <style>
        /* Force hide save button when no file for new documents */
        .save-btn-toggle[data-hide="true"] {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }
    </style>
    {{-- Upload Modal --}}
    @if($showModal)
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 p-4" 
         wire:click.self="closeModal">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
            <form wire:submit.prevent="save" onsubmit="return validateFormBeforeSubmit(event)">
                {{-- Header --}}
                <div class="p-6 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white z-10">
                    <h3 class="text-2xl font-bold text-gray-800">
                        @if($rejectionNotes)
                            Re-Submit Document
                        @else
                            {{ $editingDocumentId ? 'Edit Document' : 'Upload Document' }}
                        @endif
                    </h3>
                    <button type="button" wire:click="closeModal" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                {{-- Flash Message --}}
                @if (session()->has('message'))
                <div class="mx-6 mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
                @endif

                {{-- Error Message --}}
                @if (session()->has('error'))
                <div class="mx-6 mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
                @endif

                {{-- Rejection Notes Alert (for re-submission) --}}
                @if($rejectionNotes)
                <div class="mx-6 mt-4 bg-red-50 border-l-4 border-red-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Rejection Reason</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>{{ $rejectionNotes }}</p>
                            </div>
                            <p class="mt-2 text-xs text-red-600">
                                <i class="fas fa-info-circle mr-1"></i>Please address the issues above and re-submit your document.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="p-6 space-y-6">
                    {{-- File Upload Section --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Document File 
                            @if(!$editingDocumentId || !$existingFile)
                                <span class="text-red-500">*</span>
                            @else
                                <span class="text-gray-500 text-xs">(Optional - leave empty to keep current file)</span>
                            @endif
                        </label>
                        
                        {{-- File Error Display - Prominent Alert Box --}}
                        @error('file')
                        <div class="mb-3 p-4 bg-red-50 border-l-4 border-red-500 rounded-md shadow-sm animate-pulse">
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-red-800 font-bold text-base mb-1">⚠️ File Upload Required</p>
                                    <p class="text-red-700 text-sm">{{ $message }}</p>
                                </div>
                            </div>
                        </div>
                        @enderror
                        
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-[#0053FF] transition-colors @error('file') border-red-500 bg-red-50 @enderror">
                            <div class="space-y-1 text-center">
                                @if($filePreview)
                                <img src="{{ $filePreview }}" alt="Preview" class="mx-auto h-32 object-contain mb-2">
                                @else
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                @endif
                                <div class="flex text-sm text-gray-600">
                                    <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-[#0053FF] hover:text-[#0044DD] focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-[#0053FF]">
                                        <span>Upload a file</span>
                                        <input id="file-upload" name="file-upload" type="file" class="sr-only" 
                                            wire:model="file" 
                                            accept=".pdf,.jpg,.jpeg,.png,.heic,application/pdf,image/jpeg,image/png,image/heic"
                                            onchange="validateFileInput(this); toggleSaveButton();"
                                            @if(!$editingDocumentId) required @endif>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, JPG, PNG, HEIC up to 5MB</p>
                                @if($file)
                                <div class="mt-2 space-y-1">
                                    <p class="text-sm text-gray-700 font-medium">{{ $file->getClientOriginalName() }}</p>
                                    <p class="text-xs text-gray-500">
                                        Size: {{ number_format($file->getSize() / 1024, 2) }} KB
                                        @if(($file->getSize() / 1024) > 5120)
                                            <span class="text-red-500 font-semibold">(Exceeds 5MB limit!)</span>
                                        @endif
                                    </p>
                                </div>
                                @endif
                                
                                {{-- Upload Progress Bar --}}
                                <div x-data="{ progress: 0 }" 
                                     x-on:livewire-upload-progress.window="progress = $event.detail.progress"
                                     x-on:livewire-upload-start.window="$el.classList.remove('hidden')"
                                     x-on:livewire-upload-finish.window="$el.classList.add('hidden'); progress = 0"
                                     x-on:livewire-upload-error.window="$el.classList.add('hidden'); progress = 0"
                                     class="hidden mt-3 w-full">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-medium text-gray-700">Uploading...</span>
                                        <span class="text-xs font-medium text-gray-700" x-text="progress + '%'"></span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-[#0053FF] h-2 rounded-full transition-all duration-300" 
                                             :style="'width: ' + progress + '%'"></div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        
                        {{-- Additional File Error Display Below Upload Area --}}
                        @error('file')
                        <div class="mt-3 p-4 bg-red-100 border-2 border-red-400 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-red-800 text-sm font-semibold">{{ $message }}</p>
                            </div>
                        </div>
                        @enderror
                        
                        @if($file)
                        <div class="mt-2 flex gap-2">
                            <button type="button" wire:click="scan" 
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors text-sm font-medium">
                                <i class="fas fa-search mr-2"></i>
                                <span wire:loading.remove wire:target="scan">Scan Document</span>
                                <span wire:loading wire:target="scan">Scanning...</span>
                            </button>
                        </div>
                        @endif
                    </div>

                    {{-- Scan Results --}}
                    @if($scanResult)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-900 mb-2">Scan Results:</h4>
                        <pre class="text-sm text-blue-800 whitespace-pre-wrap max-h-40 overflow-y-auto">{{ $scanResult }}</pre>
                    </div>
                    @endif

                    @if($scanError)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-sm text-red-800">{{ $scanError }}</p>
                    </div>
                    @endif

                    {{-- Document Type --}}
                    <div>
                        <label for="document_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Document Type <span class="text-red-500">*</span>
                        </label>
                        <select id="document_type_id" wire:model.live="document_type_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF] @error('document_type_id') border-red-500 @enderror"
                            required>
                            <option value="">Select Document Type</option>
                            @foreach($documentTypes as $type)
                            <option value="{{ $type->id }}">
                                @if($type->icon)
                                <i class="{{ $type->icon }}"></i>
                                @endif
                                {{ $type->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('document_type_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Document Name --}}
                    <div>
                        <label for="document_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Document Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="document_name" wire:model="document_name" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]"
                            placeholder="Enter document name">
                        @error('document_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Conditional Fields Based on Document Type --}}
                    @if($selectedDocumentType)
                    <div class="bg-gray-50 rounded-lg p-4 space-y-4">
                        {{-- Document Number (if required) --}}
                        @if($selectedDocumentType->requires_document_number)
                        <div>
                            <label for="document_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Document Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="document_number" wire:model="document_number" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]"
                                placeholder="Enter document number">
                            @error('document_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        @else
                        <div>
                            <label for="document_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Document Number (Optional)
                            </label>
                            <input type="text" id="document_number" wire:model="document_number" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]"
                                placeholder="Enter document number">
                        </div>
                        @endif

                        {{-- Issuing Authority (if required) --}}
                        @if($selectedDocumentType->requires_issuing_authority)
                        <div>
                            <label for="issuing_authority" class="block text-sm font-medium text-gray-700 mb-2">
                                Issuing Authority <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="issuing_authority" wire:model="issuing_authority" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]"
                                placeholder="e.g., Maritime and Coastguard Agency">
                            @error('issuing_authority') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        @else
                        <div>
                            <label for="issuing_authority" class="block text-sm font-medium text-gray-700 mb-2">
                                Issuing Authority (Optional)
                            </label>
                            <input type="text" id="issuing_authority" wire:model="issuing_authority" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]"
                                placeholder="e.g., Maritime and Coastguard Agency">
                        </div>
                        @endif

                        {{-- Issuing Country --}}
                        <div>
                            <label for="issuing_country" class="block text-sm font-medium text-gray-700 mb-2">
                                Issuing Country (Optional)
                            </label>
                            <input type="text" id="issuing_country" wire:model="issuing_country" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]"
                                placeholder="e.g., USA, GBR">
                        </div>
                    </div>
                    @endif

                    {{-- Date Fields --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="issue_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Issue Date
                            </label>
                            <input type="date" id="issue_date" wire:model="issue_date" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]">
                            @error('issue_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Expiry Date
                                @if($selectedDocumentType && $selectedDocumentType->requires_expiry_date)
                                <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <input type="date" id="expiry_date" wire:model="expiry_date" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]">
                            @error('expiry_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes (Optional)
                        </label>
                        <textarea id="notes" wire:model="notes" rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]"
                            placeholder="Add any additional notes about this document"></textarea>
                    </div>

                    {{-- Tags --}}
                    <div>
                        <label for="tagInput" class="block text-sm font-medium text-gray-700 mb-2">
                            Tags (Optional)
                        </label>
                        <div class="flex gap-2">
                            <input type="text" id="tagInput" wire:model="tagInput" 
                                wire:keydown.enter.prevent="addTag"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]"
                                placeholder="Type and press Enter to add tag">
                            <button type="button" wire:click="addTag" 
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors">
                                Add
                            </button>
                        </div>
                        @if(count($tags) > 0)
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($tags as $index => $tag)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                                {{ $tag }}
                                <button type="button" wire:click="removeTag({{ $index }})" class="ml-2 text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </span>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    {{-- Featured on Profile --}}
                    <div class="flex items-center">
                        <input type="checkbox" id="featured_on_profile" wire:model="featured_on_profile" 
                            class="h-4 w-4 text-[#0053FF] focus:ring-[#0053FF] border-gray-300 rounded">
                        <label for="featured_on_profile" class="ml-2 block text-sm text-gray-900">
                            Featured on Profile
                        </label>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="p-6 bg-gray-50 border-t border-gray-200 flex justify-between items-center sticky bottom-0">
                    <div class="flex-1">
                        @if(!$editingDocumentId && !$file)
                        <div class="flex items-center space-x-2 text-amber-600 bg-amber-50 px-4 py-2 rounded-md border border-amber-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span class="text-sm font-medium">Please upload a document file to continue</span>
                        </div>
                        @elseif($file)
                        <div class="flex items-center space-x-2 text-green-600 bg-green-50 px-4 py-2 rounded-md border border-green-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium">File ready: {{ $file->getClientOriginalName() }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="flex gap-3">
                        <button type="button" wire:click="closeModal" 
                            class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        {{-- Save button - hidden by default for new documents, shown via JavaScript when file is uploaded --}}
                        <button type="submit" 
                            id="save-document-btn"
                            wire:key="save-btn-{{ $editingDocumentId ?? 'new' }}-{{ $file ? 'has-file' : 'no-file' }}"
                            wire:loading.attr="disabled"
                            onclick="return validateFormBeforeSubmit(event)"
                            class="px-4 py-2 bg-[#0053FF] text-white rounded-md hover:bg-[#0044DD] transition-colors disabled:opacity-50 save-btn-toggle"
                            data-hide="{{ (!$editingDocumentId && !$file) ? 'true' : 'false' }}">
                            <span wire:loading.remove wire:target="save">
                                {{ $editingDocumentId ? 'Update Document' : 'Upload Document' }}
                            </span>
                            <span wire:loading wire:target="save">Uploading...</span>
                        </button>
                        {{-- No button shown when no file for new documents - message already displayed above --}}
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

<script>
    // Client-side file validation
    function validateFileInput(input) {
        const file = input.files[0];
        if (!file) return;

        const maxSize = 5 * 1024 * 1024; // 5MB in bytes
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'image/heic'];
        const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'heic'];
        
        const fileExtension = file.name.split('.').pop().toLowerCase();
        
        // Validate file size
        if (file.size > maxSize) {
            alert('File size exceeds 5MB limit. Please choose a smaller file.');
            input.value = '';
            return false;
        }
        
        // Validate file type
        if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
            alert('Invalid file type. Only PDF, JPG, PNG, and HEIC files are allowed.');
            input.value = '';
            return false;
        }
        
        return true;
    }

    // STRICT Frontend Validation - Prevents form submission without file
    function validateFormBeforeSubmit(event) {
        const fileInput = document.getElementById('file-upload');
        const saveBtn = document.getElementById('save-document-btn');
        const isEditMode = @js($editingDocumentId ?? null);
        
        // For new documents, file is MANDATORY
        if (!isEditMode) {
            // Check if file exists
            if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                
                // Force hide button
                if (saveBtn) {
                    saveBtn.style.display = 'none';
                    saveBtn.style.visibility = 'hidden';
                    saveBtn.disabled = true;
                }
                
                // Show error message
                showFileError('⚠️ Error: Please upload a document file before saving. File is required.');
                
                // Highlight the file input area
                const uploadArea = fileInput?.closest('.border-dashed');
                if (uploadArea) {
                    uploadArea.classList.add('border-red-500', 'bg-red-50', 'animate-pulse');
                    setTimeout(() => {
                        uploadArea.classList.remove('border-red-500', 'bg-red-50', 'animate-pulse');
                    }, 3000);
                }
                
                fileInput?.focus();
                fileInput?.click();
                return false;
            }
            
            // Additional check: file size and type
            const file = fileInput.files[0];
            if (file) {
                const maxSize = 5 * 1024 * 1024; // 5MB
                if (file.size > maxSize) {
                    event.preventDefault();
                    event.stopPropagation();
                    event.stopImmediatePropagation();
                    showFileError('⚠️ Error: File size exceeds 5MB limit. Please choose a smaller file.');
                    return false;
                }
            } else {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                showFileError('⚠️ Error: Invalid file. Please select a valid file.');
                return false;
            }
        }
        
        return true;
    }

    // Toggle save button visibility based on file input - STRICT CONTROL
    function toggleSaveButton() {
        const fileInput = document.getElementById('file-upload');
        const saveBtn = document.getElementById('save-document-btn');
        const isEditMode = @js($editingDocumentId ?? null);
        
        if (!saveBtn) return;
        
        // Always show for edit mode
        if (isEditMode) {
            saveBtn.style.display = 'inline-flex';
            saveBtn.style.visibility = 'visible';
            saveBtn.style.opacity = '1';
            saveBtn.style.pointerEvents = 'auto';
            saveBtn.setAttribute('data-hide', 'false');
            saveBtn.disabled = false;
            return;
        }
        
        // For new documents, show only if file is selected
        if (fileInput && fileInput.files && fileInput.files.length > 0) {
            saveBtn.style.display = 'inline-flex';
            saveBtn.style.visibility = 'visible';
            saveBtn.style.opacity = '1';
            saveBtn.style.pointerEvents = 'auto';
            saveBtn.setAttribute('data-hide', 'false');
            saveBtn.disabled = false;
        } else {
            // COMPLETE HIDE - multiple methods
            saveBtn.style.display = 'none';
            saveBtn.style.visibility = 'hidden';
            saveBtn.style.opacity = '0';
            saveBtn.style.pointerEvents = 'none';
            saveBtn.setAttribute('data-hide', 'true');
            saveBtn.disabled = true;
        }
    }

    // Show file error message
    function showFileError(message) {
        // Remove existing error if any
        const existingError = document.getElementById('file-error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Create error message element
        const errorDiv = document.createElement('div');
        errorDiv.id = 'file-error-message';
        errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 animate-bounce';
        errorDiv.innerHTML = `
            <div class="flex items-center space-x-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-semibold">${message}</span>
            </div>
        `;
        
        document.body.appendChild(errorDiv);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.remove();
            }
        }, 5000);
    }

    // Initialize button state on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleSaveButton();
    });

    document.addEventListener('livewire:init', () => {
        // Listen for file state changes
        Livewire.on('file-ready', () => {
            console.log('File is ready for upload');
            setTimeout(toggleSaveButton, 100);
        });
        
        Livewire.on('file-removed', () => {
            console.log('File was removed');
            setTimeout(toggleSaveButton, 100);
        });
        
        // Watch for Livewire updates
        Livewire.hook('morph.updated', () => {
            setTimeout(toggleSaveButton, 100);
        });
        
        Livewire.on('openUploadModal', (event) => {
            const documentId = event?.documentId || null;
            const mode = event?.mode || 'add';
            @this.openModal(documentId, mode);
            
            // Re-check file after modal opens and hide/show button
            setTimeout(() => {
                toggleSaveButton();
            }, 200);
        });
        
        Livewire.on('documentUploaded', () => {
            @this.closeModal();
            // Refresh the document list in parent component
            window.location.reload();
        });
        
        // Intercept Livewire form submission
        Livewire.hook('morph.updated', ({ el, component }) => {
            const fileInput = document.getElementById('file-upload');
            const saveBtn = document.getElementById('save-document-btn');
            const isEditMode = @js($editingDocumentId ?? null);
            
            if (fileInput && saveBtn && !isEditMode) {
                if (!fileInput.files || fileInput.files.length === 0) {
                    saveBtn.disabled = true;
                    saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }
        });
        
        // CRITICAL: Intercept Livewire method calls to prevent save without file
        Livewire.hook('morph.updating', ({ el, component, updateQueue }) => {
            // Check if this is a save operation
            const fileInput = document.getElementById('file-upload');
            const isEditMode = @js($editingDocumentId ?? null);
            
            if (!isEditMode && fileInput) {
                // Check all updates in the queue
                updateQueue.forEach(update => {
                    if (update.method === 'save' || update.path === 'save') {
                        if (!fileInput.files || fileInput.files.length === 0) {
                            console.error('BLOCKED: Attempt to save without file');
                            showFileError('⚠️ Error: Please upload a document file before saving.');
                            updateQueue.splice(updateQueue.indexOf(update), 1); // Remove from queue
                            return false;
                        }
                    }
                });
            }
        });
        
        // Intercept wire:submit events
        document.addEventListener('livewire:submit', function(event) {
            const fileInput = document.getElementById('file-upload');
            const isEditMode = @js($editingDocumentId ?? null);
            
            if (!isEditMode && fileInput && (!fileInput.files || fileInput.files.length === 0)) {
                event.preventDefault();
                event.stopPropagation();
                showFileError('⚠️ Error: Please upload a document file before saving.');
                return false;
            }
        }, true);
    });
    
    // Additional protection: Intercept form submit at document level
    document.addEventListener('submit', function(event) {
        const form = event.target;
        if (form && form.closest('.bg-white.rounded-lg.shadow-xl')) {
            const fileInput = document.getElementById('file-upload');
            const isEditMode = @js($editingDocumentId ?? null);
            
            if (!isEditMode && fileInput && (!fileInput.files || fileInput.files.length === 0)) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                showFileError('⚠️ Error: Please upload a document file before saving. File is required.');
                return false;
            }
        }
    }, true); // Use capture phase to catch early
</script>
