
@role('user')
<div class="flex-1 flex flex-col overflow-hidden">
    <div class="flex min-h-screen bg-gray-100">
        <div class="flex-1 transition-all duration-300">
            <main class="p-6 flex-1 overflow-y-auto">
                <div class="w-full h-screen ">
                    <div class="bg-white p-5 rounded-lg shadow-md">
                        <h2 class="text-xl border-b border-gray-100 font-medium text-[#0053FF] pb-2">Career History</h2>
                        <div class="bg-[#F5F6FA] p-5 rounded-lg mt-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div x-data="{ showModal: @entangle('showModal'), docType: '', resetForm() { this.docType=''; } }">
                                    <!-- Add Document Card -->
                                    <div class="bg-white rounded-xl p-3 py-8 flex justify-center items-center flex-col cursor-pointer"
                                        @click="showModal = true">
                                        <!-- SVG Icon -->
                                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16.6601 3.36803C12.496 3.19173 9.27886 3.67143 9.27886 3.67143C7.24744 3.81668 3.35443 4.95555 3.35446 11.6067C3.35449 18.2013 3.31139 26.3313 3.35446 29.5723C3.35446 31.5525 4.58049 36.1713 8.82409 36.4188C13.9822 36.7198 23.2732 36.7838 27.5361 36.4188C28.6772 36.3545 32.4764 35.4586 32.9572 31.3251C33.4554 27.043 33.3562 24.067 33.3562 23.3586" stroke="#616161" stroke-width="2.15385" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M29.9998 3.07715V16.9233" stroke="#0053FE" stroke-width="2.15385" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M23.0769 10L36.9231 10" stroke="#0053FE" stroke-width="2.15385" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M11.6346 21.6943H18.3013" stroke="#616161" stroke-width="2.15385" stroke-linecap="round"/>
                                        <path d="M11.6346 28.3652H24.9679" stroke="#616161" stroke-width="2.15385" stroke-linecap="round"/>
                                        </svg>
                                        <h4 class="mt-2">Add Document</h4>
                                    </div>


                                    <div x-show="showModal" x-on:close-modal.window="showModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
                                     x-transition x-data="{
                                            docType: '',
                                            certificateRows: [{ type:'', issue:'', expiry:'' }],
                                            searchIssuer: '',
                                            fileName: '',
                                            previewUrl: '',
                                            isPdf: false,
                                            resetForm() {
                                                this.docType = '';
                                                this.certificateRows = [{ type:'', issue:'', expiry:'' }];
                                                this.searchIssuer = '';
                                                this.fileName = '';                                                
                                                this.previewUrl = '';
                                                this.isPdf = false;
                                            }
                                        }"
                                    >
                                        <div class="bg-white rounded-lg shadow-lg w-[95%] sm:w-[90%] h-[95%] sm:h-[90%] max-w-6xl p-4 sm:p-6 relative overflow-y-auto">

                                            <!-- Close Button -->
                                            <button @click="resetForm(); showModal = false" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">
                                                &times;
                                            </button>

                                            <h2 class="text-2xl font-bold text-[#0053FF] mb-6">Add Document</h2>

                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                                                <!-- Left side: Upload area -->
                                                <div class="border-dashed border-2 border-gray-300 rounded-lg p-6 flex items-center justify-center text-center cursor-pointer relative overflow-hidden"
                                                    x-data="{
                                                        isDragging: false, 
                                                        fileName: '', 
                                                        previewUrl: '', 
                                                        isPdf: false,
                                                        handleFiles(event) {
                                                            let files = event.target.files || event.dataTransfer.files;
                                                            if (files.length > 0) {
                                                                let file = files[0];
                                                                this.fileName = file.name;
                                                                this.isPdf = file.type === 'application/pdf';
                                                                this.previewUrl = this.isPdf ? '' : URL.createObjectURL(file);
                                                            }
                                                        }
                                                    }"
                                                    @click="$refs.fileInput.click()"
                                                    @dragover.prevent="isDragging = true"
                                                    @dragleave.prevent="isDragging = false"
                                                    @drop.prevent="handleFiles($event); isDragging = false"
                                                    :class="isDragging ? 'border-blue-500 bg-blue-50' : ''"
                                                    style="min-height: 300px; max-height: 400px;">

                                                    <!-- Show instructions when no file -->
                                                    <template x-if="!fileName">
                                                        <div>
                                                            <i class="fa-solid fa-upload text-gray-400 text-4xl mb-2"></i>
                                                            <p class="text-gray-500">Drag and drop or click to browse your files</p>
                                                            <p class="text-gray-400 text-sm">Support JPEG, PNG, PDF | Max: 5MB</p>
                                                        </div>
                                                    </template>

                                                    <!-- Image Preview (fixed size) -->
                                                    <template x-if="previewUrl && !isPdf">
                                                        <img :src="previewUrl" class="w-full h-full object-contain rounded-lg" />
                                                    </template>

                                                    <!-- PDF Preview -->
                                                    <template x-if="isPdf">
                                                        <div class="flex flex-col items-center justify-center text-center text-red-600 w-full h-full">
                                                            <i class="fa-solid fa-file-pdf text-5xl mb-2"></i>
                                                            <p class="text-sm font-medium">PDF Selected</p>
                                                            <p class="text-xs text-gray-600" x-text="fileName"></p>
                                                        </div>
                                                    </template>

                                                    <!-- Hidden file input -->
                                                    <input type="file" accept=".jpg,.jpeg,.png,.pdf" class="hidden" x-ref="fileInput" wire:model="file" @change="handleFiles($event)" 
                                                    />
                                                </div>

                                                <!-- Right side: Dynamic form -->
                                                <div class="space-y-4">
                                                    <!-- Document type selector -->
                                                    <div>
                                                        <label class="block mb-1">Document Type</label>
                                                        <select class="w-full border border-gray-300 rounded-md p-2" x-model="docType" wire:model="type">
                                                            <option value="">Select document type</option>
                                                            <option value="passport">Passport</option>
                                                            <option value="idvisa">IDs & Visas</option>
                                                            <option value="certificate">Certificate</option>
                                                            <option value="resume">Resume</option>
                                                            <option value="other">Other</option>
                                                        </select>
                                                    </div>

                                                    <!-- Passport -->
                                                    <template x-if="docType === 'passport'">
                                                        <div class="space-y-4">
                                                            <!-- Passport Number -->
                                                            <div>
                                                                <label class="block">Passport Number</label>
                                                                <input type="text" wire:model.defer="passport_number"
                                                                    class="w-full border p-2 rounded-md @error('passport_number') border-red-500 @enderror"
                                                                    placeholder="e.g. A1234567">
                                                                @error('passport_number') 
                                                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                                                                @enderror
                                                            </div>

                                                            <!-- Issue & Expiry Dates -->
                                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                                <div>
                                                                    <label class="block">Issue Date</label>
                                                                    <input type="date" wire:model.defer="issue_date"
                                                                        class="w-full border p-2 rounded-md @error('issue_date') border-red-500 @enderror">
                                                                    @error('issue_date') 
                                                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                                                                    @enderror
                                                                </div>
                                                                <div>
                                                                    <label class="block">Expiry Date</label>
                                                                    <input type="date" wire:model.defer="expiry_date"
                                                                        class="w-full border p-2 rounded-md @error('expiry_date') border-red-500 @enderror">
                                                                    @error('expiry_date') 
                                                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <!-- Nationality -->
                                                            <div>
                                                                <label class="block">Passport Nationality</label>
                                                                <input type="text" wire:model.defer="nationality"
                                                                    class="w-full border p-2 rounded-md @error('nationality') border-red-500 @enderror"
                                                                    placeholder="e.g. American">
                                                                @error('nationality') 
                                                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                                                                @enderror
                                                            </div>

                                                            <!-- Country Code -->
                                                            <div>
                                                                <label class="block">Country Code <span class="text-sm text-gray-500">(ISO Alpha-3, e.g. IND, USA, GBR)</span></label>
                                                                <input type="text" wire:model.defer="country_code"
                                                                    maxlength="3"
                                                                    class="w-full border p-2 rounded-md uppercase @error('country_code') border-red-500 @enderror"
                                                                    placeholder="USA">
                                                                @error('country_code') 
                                                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </template>


                                                    <!-- IDs & Visas -->
                                                    <template x-if="docType === 'idvisa'">
                                                        <div class="space-y-4">

                                                            <!-- Document Name -->
                                                            <div>
                                                                <label class="block font-medium">Document Name</label>
                                                                <select 
                                                                    wire:model="document_name" 
                                                                    class="w-full border p-2 rounded-md"
                                                                >
                                                                    <option value="">-- Select Document --</option>
                                                                    <option value="Schengen visa">Schengen visa</option>
                                                                    <option value="B1/B2 visa">B1/B2 visa</option>
                                                                    <option value="Frontier work permit">Frontier work permit</option>
                                                                    <option value="C1/D visa">C1/D visa</option>
                                                                    <option value="Driving license">Driving license</option>
                                                                    <option value="Identity card">Identity card</option>
                                                                </select>
                                                                @error('document_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                            </div>

                                                            <!-- Document Number -->
                                                            <div>
                                                                <label class="block font-medium">Document Number</label>
                                                                <input 
                                                                    type="text" 
                                                                    wire:model="document_number" 
                                                                    class="w-full border p-2 rounded-md"
                                                                    placeholder="Enter document number"
                                                                >
                                                                @error('document_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                            </div>

                                                            <!-- Issue / Expiry Dates -->
                                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                                <div>
                                                                    <label class="block font-medium">Issue Date</label>
                                                                    <input 
                                                                        type="date" 
                                                                        wire:model="issue_date" 
                                                                        class="w-full border p-2 rounded-md"
                                                                    >
                                                                    @error('issue_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                                </div>

                                                                <!-- Expiry Date (Required for Visa/Permit, Optional for License/ID) -->
                                                                <div x-show="['Schengen visa','B1/B2 visa','Frontier work permit','C1/D visa','Driving license','Identity card'].includes($wire.document_name)">
                                                                    <label class="block font-medium">Expiry Date</label>
                                                                    <input 
                                                                        type="date" 
                                                                        wire:model="expiry_date" 
                                                                        class="w-full border p-2 rounded-md"
                                                                    >
                                                                    @error('expiry_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                                </div>
                                                            </div>

                                                            <!-- Issue Country -->
                                                            <div>
                                                                <label class="block font-medium">Country</label>
                                                                <input 
                                                                    type="text" 
                                                                    wire:model="issue_country" 
                                                                    class="w-full border p-2 rounded-md"
                                                                    placeholder="e.g. United States"
                                                                >
                                                                @error('issue_country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                            </div>

                                                            <!-- Country Code (only for Visa/Permits) -->
                                                            <div x-show="['Schengen visa','B1/B2 visa','Frontier work permit','C1/D visa'].includes($wire.document_name)">
                                                                <label class="block font-medium">
                                                                    Country Code 
                                                                    <span class="text-xs text-gray-500">(ISO Alpha-3, e.g. USA, IND, GBR)</span>
                                                                </label>
                                                                <input 
                                                                    type="text" 
                                                                    wire:model="country_code" 
                                                                    class="w-full border p-2 rounded-md uppercase"
                                                                    maxlength="3"
                                                                    placeholder="USA"
                                                                >
                                                                @error('country_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                            </div>

                                                            <!-- Visa Type (only for Visa/Permits) -->
                                                            <div x-show="['Schengen visa','B1/B2 visa','Frontier work permit','C1/D visa'].includes($wire.document_name)">
                                                                <label class="block font-medium">Visa Type (optional)</label>
                                                                <input 
                                                                    type="text" 
                                                                    wire:model="visa_type" 
                                                                    class="w-full border p-2 rounded-md"
                                                                    placeholder="e.g. Tourist, Work, Student"
                                                                >
                                                                @error('visa_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                            </div>

                                                            <!-- Place of Issue -->
                                                            <div>
                                                                <label class="block font-medium">Place of Issue (optional)</label>
                                                                <input 
                                                                    type="text" 
                                                                    wire:model="place_of_issue" 
                                                                    class="w-full border p-2 rounded-md"
                                                                    placeholder="e.g. New Delhi Embassy"
                                                                >
                                                                @error('place_of_issue') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                            </div>
                                                        </div>
                                                    </template>

                                                    <!-- Resume -->
                                                    <template x-if="docType === 'resume'">
                                                        <div class="space-y-4">
                                                            <!-- Resume Name (Optional) -->
                                                            <div>
                                                                <label class="block font-medium">Resume Name (Optional)</label>
                                                                <input 
                                                                    type="text" 
                                                                    wire:model="doc_name" 
                                                                    class="w-full border p-2 rounded-md"
                                                                    placeholder="e.g. My CV 2025"
                                                                >
                                                                @error('doc_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                            </div>
                                                            <p class="text-sm text-gray-500">Upload your resume/CV. Supported formats: PDF, DOC, DOCX, JPG, PNG</p>
                                                        </div>
                                                    </template>

                                                    <!-- Certificate -->
                                                    <template x-if="docType === 'certificate'">
                                                        <div class="space-y-5"
                                                            x-data="{ 
                                                                certificateRows: [{ type_id: null, type_name: '', issue: '', expiry: '' }], 
                                                                certificateTypes: @js($certificateTypes),
                                                                issuers: @js($certificateIssuers),
                                                                issuer_id: null,
                                                                issuer_name: ''
                                                            }"
                                                        >
                                                            <div class="flex items-center justify-between mb-2">
                                                                <!-- Label -->
                                                                <label class="text-sm font-medium text-gray-700">Qualifications</label>

                                                                <!-- Add Certificate Button -->
                                                                <button type="button" 
                                                                    class="flex items-center space-x-1 text-[#0053FF] hover:text-blue-700 text-sm font-medium"
                                                                    @click="certificateRows.push({ type_id:null, type_name:'', issue:'', expiry:'' })">
                                                                    <i class="fa fa-plus-circle"></i>
                                                                    <span>Add Certificate</span>
                                                                </button>
                                                            </div>

                                                            <!-- Dynamic Certificate Rows -->
                                                            <div class="space-y-3">
                                                                <template x-for="(row, index) in certificateRows" :key="index">
                                                                    <div class="border rounded-md p-3 space-y-2">
                                                                        
                                                                        <!-- Searchable Dropdown (Certificate Type) -->
                                                                        <div x-data="{ open: false, search: '' }" class="relative">
                                                                            <label class="block text-sm">Certificate Type</label>
                                                                            
                                                                            <input type="text" x-model="row.type_name" @focus="open = true" @click.away="open = false"
                                                                                placeholder="Search certificate type..." class="w-full border p-2 rounded-md">

                                                                            <!-- Dropdown list -->
                                                                            <div x-show="open" class="absolute z-10 bg-white border w-full mt-1 max-h-40 overflow-y-auto rounded-md shadow-md">
                                                                                <template x-for="type in certificateTypes.filter(t => t.name.toLowerCase().includes(row.type_name.toLowerCase()))" 
                                                                                    :key="type.id">
                                                                                    <div @click="row.type_id = type.id; row.type_name = type.name; open = false" 
                                                                                        class="px-3 py-2 hover:bg-blue-100 cursor-pointer"
                                                                                        x-text="type.name"></div>
                                                                                </template>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Issue & Expiry Dates -->
                                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                                            <div>
                                                                                <label class="block text-sm">Issue Date</label>
                                                                                <input type="date" x-model="row.issue" class="w-full border p-2 rounded-md">
                                                                            </div>
                                                                            <div>
                                                                                <label class="block text-sm">Expiry Date</label>
                                                                                <input type="date" x-model="row.expiry" class="w-full border p-2 rounded-md">
                                                                            </div>
                                                                        </div>

                                                                        <!-- Remove button -->
                                                                        <div class="flex justify-end" x-show="index > 0">
                                                                            <button type="button" 
                                                                                class="text-[#0053FF] hover:text-red-600"
                                                                                @click="certificateRows.splice(index,1)">
                                                                                <i class="fa-regular fa-trash-can"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </div>

                                                            <!-- Common Fields -->
                                                            <div>
                                                                <label class="block">Certificate Number</label>
                                                                <input type="text" wire:model="certificate_number" class="w-full border p-2 rounded-md">
                                                                @error('certificate_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                            </div>

                                                            <!-- Certificate Issuer -->
                                                            <div x-data="{ open: false, search: '' }" class="relative">
                                                                <label class="block text-sm">Certificate Issuer</label>
                                                                
                                                                <input type="text" 
                                                                    x-model="issuer_name" 
                                                                    @focus="open = true" 
                                                                    @click.away="open = false"
                                                                    placeholder="Search certificate issuer..." 
                                                                    class="w-full border p-2 rounded-md">

                                                                <!-- Dropdown list -->
                                                                <div x-show="open" class="absolute z-10 bg-white border w-full mt-1 max-h-40 overflow-y-auto rounded-md shadow-md">
                                                                    <template x-for="issuer in issuers.filter(i => i.name.toLowerCase().includes(issuer_name.toLowerCase()))" 
                                                                        :key="issuer.id">
                                                                        <div 
                                                                            @click="issuer_id = issuer.id; issuer_name = issuer.name; open = false" 
                                                                            class="px-3 py-2 hover:bg-blue-100 cursor-pointer"
                                                                            x-text="issuer.name"></div>
                                                                    </template>
                                                                </div>
                                                            </div>

                                                            <!-- Hidden fields to send Alpine → Livewire -->
                                                            <textarea class="hidden" wire:model="certificate_data" x-text="JSON.stringify(certificateRows)"></textarea>
                                                            <input type="hidden" wire:model="certificate_issuer_id" :value="issuer_id">
                                                        </div>
                                                    </template>

                                                    <!-- Other Document -->
                                                    <template x-if="docType === 'other'">
                                                        <div class="space-y-5">

                                                            <!-- Document Name -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700">Document Name</label>
                                                                <input type="text" 
                                                                    wire:model="doc_name" 
                                                                    class="w-full border p-2 rounded-md" 
                                                                    placeholder="Enter document name">
                                                                @error('doc_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                            </div>

                                                            <!-- Document Number -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700">Document Number</label>
                                                                <input type="text" 
                                                                    wire:model="doc_number" 
                                                                    class="w-full border p-2 rounded-md" 
                                                                    placeholder="Enter document number (optional)">
                                                                @error('doc_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                            </div>

                                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                                <!-- Issue Date -->
                                                                <div>
                                                                    <label class="block text-sm font-medium text-gray-700">Issue Date</label>
                                                                    <input type="date" 
                                                                        wire:model="issue_date" 
                                                                        class="w-full border p-2 rounded-md">
                                                                    @error('issue_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                                </div>

                                                                <!-- Expiry Date -->
                                                                <div>
                                                                    <label class="block text-sm font-medium text-gray-700">Expiry Date</label>
                                                                    <input type="date" 
                                                                        wire:model="expiry_date" 
                                                                        class="w-full border p-2 rounded-md">
                                                                    @error('expiry_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                                </div>
                                                            </div>

                                                            <!-- File Upload -->
                                                            <!-- <div>
                                                                <label class="block text-sm font-medium text-gray-700">Upload File</label>
                                                                <input type="file" 
                                                                    wire:model="file" 
                                                                    class="w-full border p-2 rounded-md">
                                                                @error('file') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                                                                
                                                                <div wire:loading wire:target="file" class="text-sm text-gray-500 mt-1">Uploading...</div>
                                                                @if($file)
                                                                    <div class="mt-2 text-green-600 text-sm">File selected: {{ $file->getClientOriginalName() }}</div>
                                                                @endif
                                                            </div> -->

                                                        </div>
                                                    </template>

                                                </div>
                                            </div>

                                            <!-- Buttons -->
                                            <div class="flex justify-end space-x-3 mt-6">
                                                <button  @click="resetForm(); showModal = false" class="px-4 py-2 border rounded-md"> Cancel </button>
                                                <button class="px-4 py-2 bg-[#0053FF] text-white rounded-md" x-show="docType !== ''" wire:click="save">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>


                                </div>


                                <div class="bg-white rounded-xl p-3 py-8 flex justify-center items-center flex-col">
                                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.6601 3.36803C12.496 3.19173 9.27886 3.67143 9.27886 3.67143C7.24744 3.81668 3.35443 4.95555 3.35446 11.6067C3.35449 18.2013 3.31139 26.3313 3.35446 29.5723C3.35446 31.5525 4.58049 36.1713 8.82409 36.4188C13.9822 36.7198 23.2732 36.7838 27.5361 36.4188C28.6772 36.3545 32.4764 35.4586 32.9572 31.3251C33.4554 27.043 33.3562 24.067 33.3562 23.3586" stroke="#616161" stroke-width="2.15385" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M36.1356 3.56964C34.1957 1.48046 19.6033 6.59822 19.6154 8.46671C19.629 10.5856 25.3141 11.2374 26.8898 11.6795C27.8374 11.9453 28.0912 12.2179 28.3096 13.2115C29.2992 17.7116 29.796 19.9499 30.9284 19.9999C32.7333 20.0797 38.0289 5.60849 36.1356 3.56964Z" stroke="#0053FE" stroke-width="2.15385" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M27.6322 11.9758L30.7496 8.8584" stroke="#0053FE" stroke-width="2.15385" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M11.6346 21.6943H18.3013" stroke="#616161" stroke-width="2.15385" stroke-linecap="round"/>
                                    <path d="M11.6346 28.3652H24.9679" stroke="#616161" stroke-width="2.15385" stroke-linecap="round"/>
                                    </svg>

                                    <h4 class="mt-2"> Share Document </h4>
                                </div>

                                <div class="bg-white rounded-xl p-3 py-8 flex justify-center items-center flex-col">
                                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 10C5 7.64298 5 6.46447 5.73223 5.73223C6.46447 5 7.64298 5 10 5C12.357 5 13.5355 5 14.2678 5.73223C15 6.46447 15 7.64298 15 10C15 12.357 15 13.5355 14.2678 14.2678C13.5355 15 12.357 15 10 15C7.64298 15 6.46447 15 5.73223 14.2678C5 13.5355 5 12.357 5 10Z" stroke="#616161" stroke-width="2.15385" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M5 30C5 27.643 5 26.4645 5.73223 25.7322C6.46447 25 7.64298 25 10 25C12.357 25 13.5355 25 14.2678 25.7322C15 26.4645 15 27.643 15 30C15 32.357 15 33.5355 14.2678 34.2678C13.5355 35 12.357 35 10 35C7.64298 35 6.46447 35 5.73223 34.2678C5 33.5355 5 32.357 5 30Z" stroke="#616161" stroke-width="2.15385" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M5 20H15" stroke="#616161" stroke-width="2.15385" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M20 5V13.3333" stroke="#616161" stroke-width="2.15385" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M25 10C25 7.64298 25 6.46447 25.7322 5.73223C26.4645 5 27.643 5 30 5C32.357 5 33.5355 5 34.2678 5.73223C35 6.46447 35 7.64298 35 10C35 12.357 35 13.5355 34.2678 14.2678C33.5355 15 32.357 15 30 15C27.643 15 26.4645 15 25.7322 14.2678C25 13.5355 25 12.357 25 10Z" stroke="#616161" stroke-width="2.15385" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M35 20H25C22.643 20 21.4645 20 20.7322 20.7322C20 21.4645 20 22.643 20 25M20 29.6153V34.2308M25 25V27.5C25 29.9107 26.3062 30 28.3333 30C29.2538 30 30 30.7462 30 31.6667M26.6667 35H25M30 25C32.357 25 33.5355 25 34.2678 25.7333C35 26.4665 35 27.6468 35 30.0072C35 32.3677 35 33.5478 34.2678 34.2812C33.7333 34.8163 32.9612 34.961 31.6667 35" stroke="#0053FE" stroke-width="2.15385" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>

                                    <h4 class="mt-2"> Share Profile </h4>
                                </div>
                            </div>
                            
                            <div class=" flex w-full flex-wrap mt-10">
                                <h3 class="text-lg font-medium w-full text">1 Documents</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full mt-3">
                                    <div class="bg-white rounded-xl p-4 flex relative cursor-pointer border border-gray-200 flex items-center " title="Edit Organisation"> 
                                        <div class="flex flex-wrap justify-center w-[80px] h-[90px] items-center p-2 bg-[#E3F2FF] rounded-md ">
                                            <img src="images/passport-img.png" alt="passport" class="">
                                        </div>
                                        <div class="w-[100% - 100px] text-center mb-1 pl-3 flex justify-between items-center" style="width: calc(100% - 100px);">
                                            <div>
                                                <h3 class="text-md w-full font-semibold mb-1 text-left ">Passport</h3>
                                                <span class="text-[12px] text-gray-600 flex"> <img src="images/view-icon.png" > <span class="pl-1">Featured on your Profile Preview </span></span>
                                            </div>
                                            <div class="flex items-center p-2 bg-[#E3F2FF] text-[#0053FF] font-medium flex w-[60px] items-center justify-center rounded-md">
                                                4 <br>
                                                    Yrs
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@elserole('super_admin')
    <div class="flex-1 flex flex-col overflow-hidden">
        <div class="flex min-h-screen bg-gray-100">
            <div class="flex-1 transition-all duration-300">
                <main class="p-3 sm:p-6 flex-1 overflow-y-auto">
                    <div class="w-full min-h-screen">
                        <div class="bg-white p-4 sm:p-5 rounded-lg shadow-md">
                            <h2 class="text-xl border-b border-gray-100 font-medium text-[#0053FF] pb-2">Career History</h2>
                            <div class="flex items-center justify-center h-40">
                                <p class="text-gray-500 text-lg font-medium">Career History Coming Soon...</p>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
@endrole