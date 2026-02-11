@php
    use Spatie\Permission\Models\Role;
    // Get all roles except super_admin
    $nonAdminRoles = Role::where('name', '!=', 'super_admin')->pluck('name')->toArray();
@endphp

@extends('layouts.app-laravel')

@section('content')
@role($nonAdminRoles)
<div class="flex-1 flex flex-col overflow-hidden">
    <div class="flex min-h-screen bg-gray-100">
        <div class="flex-1 transition-all duration-300">
            <main class="p-3 sm:p-6 flex-1">
                <div class="w-full min-h-full">
                    <div class="bg-white p-4 sm:p-5 rounded-lg shadow-md">
                        <h2 class="text-xl border-b border-gray-100 font-medium text-[#0053FF] pb-2" role="heading" aria-level="2">Documents</h2>

                        <!-- Success Popup -->
                        <div id="successPopup" class="fixed inset-0 flex items-center justify-center z-50 hidden bg-black bg-opacity-20">
                            <div id="successContent" class="bg-white rounded-2xl shadow-lg p-6 max-w-sm w-full text-center scale-90 opacity-0 transition-all duration-300">
                                <img src="{{ asset('images/success.png') }}" alt="Success" class="w-24 h-24 mx-auto mb-4">
                                <h2 class="text-2xl font-bold text-green-600">Document Saved!</h2>
                                <p class="text-gray-600 mt-2">Your document has been added successfully.</p>
                                <div class="mt-5">
                                    <button id="continueBtn" class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                                        Continue
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Success Popup (unique ID for share) -->
                        <div id="shareSuccessPopup" class="fixed inset-0 flex items-center justify-center z-50 hidden bg-black bg-opacity-20">
                            <div id="shareSuccessContent" class="bg-white rounded-2xl shadow-lg p-6 max-w-sm w-full text-center scale-90 opacity-0 transition-all duration-300">
                                <img src="{{ asset('images/success.png') }}" alt="Success" class="w-24 h-24 mx-auto mb-4">
                                <h2 class="text-2xl font-bold text-green-600">Success!</h2>
                                <p class="text-gray-600 mt-2" id="shareSuccessMessage">Documents shared successfully.</p>
                                <div class="mt-5">
                                    <button id="shareContinueBtn" class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                                        Continue
                                    </button>
                                </div>
                            </div>
                        </div>
                      
                        <!-- Success / Error Modal -->
                        <div id="emailResultPopup" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                            <div id="emailResultContent" class="bg-white rounded-lg shadow-lg w-[90%] max-w-md p-6 text-center transform scale-90 opacity-0 transition-all duration-300">
                                <h2 id="emailResultTitle" class="text-xl font-semibold mb-4"></h2>
                                <p id="emailResultMessage" class="mb-6"></p>
                                <button id="emailResultCloseBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Continue</button>
                            </div>
                        </div>

                        <!-- Error placeholder under input -->
                        <p id="emailError" class="text-red-600 text-sm mt-1 hidden"></p>
                      
                      	<!-- Delete Confirmation Modal -->
                        <div id="deleteConfirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                            <div class="bg-white rounded-xl shadow-xl w-[90%] sm:w-96 p-4 sm:p-6 text-center">
                                <h3 class="text-lg font-semibold text-gray-700 mb-4">Confirm Delete</h3>
                                <p class="text-gray-500 mb-6">Are you sure you want to delete this document?</p>
                                <div class="flex justify-center gap-4">
                                    <button id="cancelDeleteBtn" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 py-2 rounded-lg">No</button>
                                    <button id="confirmDeleteBtn" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg">Yes, Delete</button>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Success Modal -->
                        <div id="deleteSuccessModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                            <div class="bg-white rounded-xl shadow-xl w-80 p-6 text-center">
                                <h3 class="text-lg font-semibold text-green-600 mb-4">Deleted Successfully!</h3>
                                <p class="text-gray-500 mb-6">The document has been deleted.</p>
                                <button id="closeSuccessBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg">OK</button>
                            </div>
                        </div>

                        <div class="bg-[#F5F6FA] p-4 sm:p-5 rounded-lg mt-6">
                            <!-- Crewdentials Import Card (CASE 1) -->
                            <div id="crewdentialsImportCard" class="hidden mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-5">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <i class="fas fa-cloud-download-alt text-2xl text-blue-600"></i>
                                            <h3 class="text-lg font-semibold text-gray-900">Import Documents from Crewdentials</h3>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-4">
                                            We found existing documents on Crewdentials for your email address. Would you like to import them?
                                        </p>
                                        <button onclick="showConsentModal('import')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                            <i class="fas fa-download mr-2"></i>Import Documents
                                        </button>
                                    </div>
                                    <button onclick="$('#crewdentialsImportCard').addClass('hidden')" class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-times text-xl"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Top cards -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <!-- Add Document Card -->
                                <div id="addDocumentCard" data-popup-target="#addDocumentModal" class="bg-white rounded-xl p-3 py-8 flex justify-center items-center flex-col cursor-pointer">
                                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16.6601 3.36803C12.496 3.19173 9.27886 3.67143 9.27886 3.67143C7.24744 3.81668 3.35443 4.95555 3.35446 11.6067C3.35449 18.2013 3.31139 26.3313 3.35446 29.5723C3.35446 31.5525 4.58049 36.1713 8.82409 36.4188C13.9822 36.7198 23.2732 36.7838 27.5361 36.4188C28.6772 36.3545 32.4764 35.4586 32.9572 31.3251C33.4554 27.043 33.3562 24.067 33.3562 23.3586" stroke="#616161" stroke-width="2.15385" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M29.9998 3.07715V16.9233" stroke="#0053FE" stroke-width="2.15385" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M23.0769 10L36.9231 10" stroke="#0053FE" stroke-width="2.15385" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M11.6346 21.6943H18.3013" stroke="#616161" stroke-width="2.15385" stroke-linecap="round"/>
                                        <path d="M11.6346 28.3652H24.9679" stroke="#616161" stroke-width="2.15385" stroke-linecap="round"/>
                                    </svg>
                                    <h4 class="mt-2">Add Document</h4>
                                </div>

                                <!-- Share Document Card (optional) -->
                                <div id="shareDocumentCard" data-popup-target="#shareDocumentModal" class="bg-white rounded-xl p-3 py-8 flex justify-center items-center flex-col cursor-pointer">
                                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16.6601 3.36803C12.496 3.19173 9.27886 3.67143 9.27886 3.67143C7.24744 3.81668 3.35443 4.95555 3.35446 11.6067C3.35449 18.2013 3.31139 26.3313 3.35446 29.5723C3.35446 31.5525 4.58049 36.1713 8.82409 36.4188C13.9822 36.7198 23.2732 36.7838 27.5361 36.4188C28.6772 36.3545 32.4764 35.4586 32.9572 31.3251C33.4554 27.043 33.3562 24.067 33.3562 23.3586" stroke="#616161" stroke-width="2.15385" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M36.1356 3.56964C34.1957 1.48046 19.6033 6.59822 19.6154 8.46671C19.629 10.5856 25.3141 11.2374 26.8898 11.6795C27.8374 11.9453 28.0912 12.2179 28.3096 13.2115C29.2992 17.7116 29.796 19.9499 30.9284 19.9999C32.7333 20.0797 38.0289 5.60849 36.1356 3.56964Z" stroke="#0053FE" stroke-width="2.15385" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M27.6322 11.9758L30.7496 8.8584" stroke="#0053FE" stroke-width="2.15385" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M11.6346 21.6943H18.3013" stroke="#616161" stroke-width="2.15385" stroke-linecap="round"/>
                                        <path d="M11.6346 28.3652H24.9679" stroke="#616161" stroke-width="2.15385" stroke-linecap="round"/>
                                    </svg>
                                    <h4 class="mt-2">Share Document</h4>
                                </div>
                                <div id="shareProfileCard" data-popup-target="#shareProfileModal" class="bg-white rounded-xl p-3 py-8 flex justify-center items-center flex-col cursor-pointer">
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

                            <div class="flex w-full flex-wrap mt-8">
                                

                                @php
                                    $expiredDocs = $documents->filter(fn($doc) => isset($doc->is_expired) && $doc->is_expired)->values();
                                    $priorityDocs = $documents->filter(fn($doc) => $doc->is_expiring_soon && (!isset($doc->is_expired) || !$doc->is_expired))->values();
                                    $normalDocs = $documents->filter(fn($doc) => !$doc->is_expiring_soon && (!isset($doc->is_expired) || !$doc->is_expired))->values();

                                    // Initialize categories with all possible document type categories
                                    $categories = [
                                        'STCW Certificates' => [],
                                        'Medical Certificates' => [],
                                        'Identity & Travel Documents' => [],
                                        'Professional Qualifications' => [],
                                        'Employment Records' => [],
                                        'Insurance & Financial' => [],
                                        'Additional Documents' => [],
                                        'Passport' => [],
                                        'Ids and Visa' => [],
                                        'Certificate' => [],
                                        'Other' => []
                                    ];

                                    foreach ($normalDocs as $doc) {
                                        $categoryKey = 'Other'; // Default
                                        
                                        // Check if document has a document type (new system)
                                        if ($doc->documentType && $doc->documentType->category) {
                                            $categoryKey = $doc->documentType->category;
                                        } else {
                                            // Fallback to legacy type system
                                            $type = strtolower($doc->type);
                                            $categoryKey = match($type) {
                                                'passport' => 'Passport',
                                                'idvisa' => 'Ids and Visa',
                                                'ids_and_visa' => 'Ids and Visa',
                                                'certificate' => 'Certificate',
                                                default => 'Other'
                                            };
                                        }
                                        
                                        // Ensure category exists in array
                                        if (!isset($categories[$categoryKey])) {
                                            $categories[$categoryKey] = [];
                                        }
                                        
                                        $categories[$categoryKey][] = $doc;
                                    }
                                    
                                    // Remove empty categories
                                    $categories = array_filter($categories, fn($docs) => count($docs) > 0);
                                @endphp

                                {{-- Tabs --}}
                                <div class="mb-6 border-b border-gray-200">
                                    <nav class="-mb-px flex space-x-8">
                                        <button onclick="showTab('all')" id="tab-all" 
                                            class="tab-button border-[#0053FF] text-[#0053FF] whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                            All Documents
                                        </button>
                                        <button onclick="showTab('expired')" id="tab-expired" 
                                            class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                            Expired Documents
                                            @if($expiredDocs->count() > 0)
                                            <span class="ml-2 bg-red-100 text-red-800 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $expiredDocs->count() }}</span>
                                            @endif
                                        </button>
                                        <button onclick="showTab('shares')" id="tab-shares" 
                                            class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                            <i class="fas fa-link mr-1"></i>Active Share Links
                                        </button>
                                    </nav>
                                </div>

                                {{-- All Documents Tab Content --}}
                                <div id="tab-content-all" class="tab-content">
                                
                                {{-- Info Banner for Bulk Selection --}}
                                <div id="bulkSelectionInfo" class="mb-4 p-4 bg-gradient-to-r from-blue-50 via-indigo-50 to-blue-50 border-2 border-blue-300 rounded-lg shadow-sm">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 mt-0.5">
                                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                                <i class="fas fa-check-square text-white text-lg"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-base font-bold text-gray-900 mb-2 flex items-center gap-2">
                                                <span>📋 Check the checkbox on documents to select them</span>
                                            </h4>
                                            <p class="text-sm text-gray-700 mb-2">
                                                <strong>How to use:</strong> Click the checkbox (☑️) in the top-left corner of each document card to select it for bulk verification.
                                            </p>
                                            <div class="bg-white rounded-md p-3 border border-blue-200">
                                                <p class="text-xs text-gray-600 mb-1">
                                                    <i class="fas fa-lightbulb text-yellow-500 mr-1"></i>
                                                    <strong>Tip:</strong> You can select multiple documents at once. After selecting, a button will appear at the top to request verification with Crewdentials for all selected documents.
                                                </p>
                                            </div>
                                        </div>
                                        <button onclick="$('#bulkSelectionInfo').slideUp(300); localStorage.setItem('hasClosedBulkSelectionInfo', 'true');" class="text-gray-400 hover:text-gray-600 transition-colors flex-shrink-0" title="Don't show this again">
                                            <i class="fas fa-times text-lg"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Bulk Actions for Crewdentials Verification --}}
                                <div id="bulkActionsBar" class="hidden mb-4 p-4 bg-blue-50 border-2 border-blue-300 rounded-lg shadow-sm">
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-blue-200">
                                                <i class="fas fa-check-circle text-blue-600"></i>
                                                <span class="text-sm font-semibold text-gray-800">
                                                    <span id="selectedCount" class="text-blue-600">0</span> document(s) selected
                                                </span>
                                            </div>
                                            <button onclick="clearDocumentSelection()" class="text-sm text-blue-600 hover:text-blue-800 underline font-medium flex items-center gap-1">
                                                <i class="fas fa-times-circle"></i>
                                                Clear Selection
                                            </button>
                                        </div>
                                        <button onclick="requestCrewdentialsVerificationForSelected()" 
                                                class="bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 transition-colors text-sm font-semibold flex items-center gap-2 shadow-md hover:shadow-lg">
                                            <i class="fas fa-shield-check"></i>
                                            Request Verification with Crewdentials
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-3 flex items-center gap-1">
                                        <i class="fas fa-lightbulb text-yellow-500"></i>
                                        Selected documents will be sent to Crewdentials for professional verification and certification.
                                    </p>
                                </div>

                                {{-- Expired / Expiring Soon --}}
                                @if($priorityDocs->count() > 0)
                              		<hr class="border-t border-gray-400 my-5 w-full">
                                	<!-- Heading -->
                                	<h2 class="text-lg font-bold border-gray-300 mb-3">Expiring Within 6 Months</h2>
                              	
                                    <div class="w-full mt-5">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                            @foreach($priorityDocs as $doc)
                                                @php
                                                    $isRejected = strtolower(trim($doc->status ?? '')) === 'rejected';
                                                @endphp
                                                <div class="bg-white rounded-xl p-3 sm:p-4 flex flex-col relative border border-gray-200 gap-3 document-item" data-document-id="{{ $doc->id }}">

                                                    <!-- Bulk Selection Checkbox (Top Left) -->
                                                    <label class="absolute top-2 left-2 z-10 cursor-pointer group" 
                                                           title="Click here to select this document for bulk verification">
                                                        <input type="checkbox" 
                                                               class="document-checkbox w-5 h-5 text-blue-600 border-2 border-blue-400 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer transition-all hover:scale-110 hover:border-blue-600 shadow-sm" 
                                                               value="{{ $doc->id }}"
                                                               onchange="updateBulkActionsBar()">
                                                        <div class="absolute -top-10 left-0 bg-gray-900 text-white text-xs px-3 py-2 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition-opacity z-20 font-medium">
                                                            <i class="fas fa-check-square mr-1"></i>
                                                            Click to select for bulk verification
                                                            <div class="absolute top-full left-3 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                                        </div>
                                                    </label>

                                                    <!-- History Button (Top Right) -->
                                                    <button onclick="openVersionHistoryModal({{ $doc->id }}); event.stopPropagation();" 
                                                            class="w-[30px] h-[30px] absolute top-2 right-2 z-10 p-0 bg-purple-600 text-white rounded-full hover:bg-purple-700 transition-colors shadow-md"
                                                            title="View Version History">
                                                        <i class="fas fa-history text-xs"></i>
                                                    </button>

                                                    <!-- Document Image -->
                                                    <div class="flex flex-wrap justify-center w-full sm:w-[80px] h-[90px] sm:h-[90px] items-center p-2 bg-[#E3F2FF] rounded-md cursor-pointer view-document-card hover:bg-[#D0E7FF] transition-colors group relative" data-doc='@json($doc)'>

                                                        @if($doc->file_path)
                                                            @php
                                                                $filePath = asset('storage/' . $doc->file_path);
                                                                $extension = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                                                            @endphp

                                                            @if(in_array($extension, ['jpg','jpeg','png','gif','bmp','webp','svg']))
                                                                <img src="{{ $filePath }}" alt="{{ $doc->name }}" class="max-w-full max-h-full object-contain" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                                <div style="display:none; flex-direction:column; align-items:center; color:#999;" class="fallback-icon">
                                                                    <i class="fas fa-file-image text-4xl mb-1"></i>
                                                                    <span class="text-xs">Image</span>
                                                                </div>
                                                            @elseif($extension === 'pdf')
                                                                <!-- PDF Icon -->
                                                                <a class="flex flex-col items-center text-red-600">
                                                                    <img src="{{ asset('images/pdf.png') }}" alt="PDF" class="h-10 w-10 object-contain" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-file-pdf text-red-600 text-4xl\'></i>';">
                                                                </a>
                                                            @else
                                                                <span class="text-gray-400 text-sm">Unsupported</span>
                                                            @endif
                                                        @else
                                                            <span class="text-gray-400 text-sm">No File</span>
                                                        @endif

                                                    </div>

                                                    <!-- Document Details -->
                                                    <div class="w-full flex flex-col sm:flex-row sm:justify-between items-start sm:pl-3 mb-1 max-[1200px]:!flex-col max-[1200px]:!pl-0 max-[1200px]:gap-5">
                                                        <div class="flex-1">
                                                            <h3 class="text-md font-semibold mb-1 text-left">{{ $doc->name }}</h3>
                                                            <p class="text-sm font-semibold text-gray-800 mb-1">
                                                                @php
                                                                    // Priority: document_type->name > document_name > otherDocument->doc_name > type
                                                                    if ($doc->documentType) {
                                                                        // New document type system
                                                                        $displayName = $doc->documentType->name;
                                                                    } elseif ($doc->document_name) {
                                                                        // Has document_name field set
                                                                        $displayName = $doc->document_name;
                                                                    } elseif ($doc->type === 'certificate') {
                                                                        $displayName = $doc->certificates->first()?->type->name ?? 'Certificate';
                                                                    } elseif ($doc->type === 'passport') {
                                                                        $displayName = 'Passport';
                                                                    } elseif ($doc->type === 'idvisa') {
                                                                        $displayName = 'ID / Visa';
                                                                    } elseif ($doc->type === 'resume') {
                                                                        $displayName = $doc->otherDocument?->doc_name ?? 'Resume';
                                                                    } elseif ($doc->type === 'other') {
                                                                        $displayName = $doc->otherDocument?->doc_name ?? 'Other Document';
                                                                    } else {
                                                                        $displayName = ucfirst($doc->type ?? 'Document');
                                                                    }
                                                                @endphp
                                                                {{ $displayName }}
                                                            </p>

                                                            <!-- Eye icon + Featured -->
                                                            <div class="flex flex-col space-y-1 text-gray-600 text-[12px]">
                                                                <div class="flex items-center space-x-2">
                                                                    <span class="toggle-share cursor-pointer" data-id="{{ $doc->id }}" onclick="toggleShare(this)" 
                                                                          title="{{ $doc->is_active ? 'Deactivate' : 'Activate' }}">
                                                                      <img src="{{ $doc->is_active ? asset('images/view-icon.png') : asset('images/view-off-slash.png') }}" class="w-4 h-4">
                                                                    </span>
                                                                    <span class="share-text">
                                                                      {{ $doc->is_active ? 'Featured on your Profile Preview' : 'Not featured on your Profile Preview' }}
                                                                    </span>
                                                                </div>

                                                                <!-- Status Display -->
                                                                @php
                                                                    $statusColor = 'text-yellow-600'; // default pending
                                                                    if($doc->status === 'approved') $statusColor = 'text-green-600';
                                                                    elseif($doc->status === 'rejected') $statusColor = 'text-red-600';
                                                                @endphp
                                                                <div class="text-[14px]">
                                                                    Status: <span class="{{ $statusColor }} font-semibold">{{ ucfirst($doc->status ?? 'pending') }}</span>
                                                                </div>

                                                                <!-- OCR Status Display -->
                                                                @if($doc->ocr_status)
                                                                <div class="text-[12px] mt-1">
                                                                    <span class="px-2 py-0.5 text-xs font-medium rounded
                                                                        @if($doc->ocr_status === 'completed') bg-blue-100 text-blue-800
                                                                        @elseif($doc->ocr_status === 'processing') bg-yellow-100 text-yellow-800
                                                                        @elseif($doc->ocr_status === 'failed') bg-red-100 text-red-800
                                                                        @else bg-gray-100 text-gray-800
                                                                        @endif"
                                                                        title="@if($doc->ocr_status === 'completed' && $doc->ocr_confidence)OCR Confidence: {{ number_format($doc->ocr_confidence, 1) }}%@elseif($doc->ocr_status === 'failed'){{ $doc->ocr_error ?? 'OCR processing failed' }}@else OCR {{ ucfirst($doc->ocr_status) }}@endif">
                                                                        <i class="fas fa-eye mr-1"></i>
                                                                        @if($doc->ocr_status === 'completed' && $doc->ocr_confidence)
                                                                            OCR {{ number_format($doc->ocr_confidence, 0) }}%
                                                                        @else
                                                                            OCR {{ ucfirst($doc->ocr_status) }}
                                                                        @endif
                                                                    </span>
                                                                </div>
                                                                @endif

                                                                <!-- Verification Level Badge -->
                                                                @if($doc->verificationLevel)
                                                                <div class="text-[12px] mt-1">
                                                                    <span class="px-2 py-0.5 text-xs font-medium rounded
                                                                        @if($doc->verificationLevel->badge_color === 'gold') bg-yellow-100 text-yellow-800
                                                                        @elseif($doc->verificationLevel->badge_color === 'purple') bg-purple-100 text-purple-800
                                                                        @elseif($doc->verificationLevel->badge_color === 'green') bg-green-100 text-green-800
                                                                        @elseif($doc->verificationLevel->badge_color === 'blue') bg-blue-100 text-blue-800
                                                                        @else bg-gray-100 text-gray-800
                                                                        @endif"
                                                                        title="{{ $doc->verificationLevel->description }}">
                                                                        <i class="{{ $doc->verificationLevel->badge_icon }} mr-1"></i>
                                                                        {{ $doc->verificationLevel->name }} (Level {{ $doc->verificationLevel->level }})
                                                                    </span>
                                                                </div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <!-- Remaining Duration Badge -->
                                                        <div class="flex flex-col items-center space-y-2">
                                                            @php
                                                                $badgeClasses = 'bg-[#E3F2FF] text-[#0053FF]';
                                                                $isCross = false;

                                                                if(strtoupper($doc->remaining_type) === 'EXPIRED') {
                                                                    $badgeClasses = 'bg-[#FFE3E3] text-[#C02020]';
                                                                    $isCross = true;
                                                                } elseif($doc->remaining_type === 'N/A') {
                                                                    $badgeClasses = 'bg-[#E3F2FF] text-[#0053FF]';
                                                                } elseif($doc->is_expiring_soon) {
                                                                    $badgeClasses = 'bg-yellow-200 text-yellow-800';
                                                                }
                                                            @endphp

                                                            @if($isCross)
                                                                <div class="flex items-center justify-center w-[60px] h-[60px] rounded-md {{ $badgeClasses }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="#C02020">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                    </svg>
                                                                </div>
                                                            @else
                                                                <div class="flex items-center p-2 font-medium w-[60px] justify-center rounded-md text-center {{ $badgeClasses }}">
                                                                    @if($doc->remaining_number !== null)
                                                                        <div class="flex flex-col items-center">
                                                                            <span class="text-xl font-bold">{{ $doc->remaining_number }}</span>
                                                                            <span class="text-xs">{{ $doc->remaining_type }}</span>
                                                                        </div>
                                                                    @else
                                                                        <div class="flex flex-col items-center">
                                                                            <span class="text-xs">{{ $doc->remaining_type }}</span>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- Action Buttons for Rejected Documents --}}
                                                    @if($isRejected)
                                                    <div class="mt-auto pt-3 border-t border-gray-200 w-full">
                                                        <button type="button" 
                                                                onclick="editDocument({{ $doc->id }})"
                                                                class="w-full px-3 py-2 bg-orange-600 text-white text-xs rounded-md hover:bg-orange-700 transition-colors font-medium shadow-sm">
                                                            <i class="fas fa-redo mr-1"></i>Re-Submit
                                                        </button>
                                                    </div>
                                                    @endif

                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @php
    // Check if there are any normal documents
    $hasNormalDocs = false;
    foreach ($categories as $docs) {
        if (count($docs) > 0) {
            $hasNormalDocs = true;
            break;
        }
    }
@endphp

@if($hasNormalDocs)
<h2 class="text-lg font-bold border-gray-300 mb-3">All Documents</h2>

    {{-- Normal Documents by Type --}}
    @foreach($categories as $categoryName => $docs)
        @if(count($docs) > 0)
            <div class="w-full mt-3 mb-3">
                <h4 class="text-md font-semibold mb-3">{{ $categoryName }}</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($docs as $doc)
                        @php
                            $isRejected = strtolower(trim($doc->status ?? '')) === 'rejected';
                        @endphp
                        <div class="bg-white rounded-xl p-3 sm:p-4 flex flex-col relative border border-gray-200 gap-3 document-item" data-document-id="{{ $doc->id }}">

                            <!-- Bulk Selection Checkbox (Top Left) -->
                            <label class="absolute top-2 left-2 z-10 cursor-pointer group" 
                                   title="Click here to select this document for bulk verification">
                                <input type="checkbox" 
                                       class="document-checkbox w-5 h-5 text-blue-600 border-2 border-blue-400 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer transition-all hover:scale-110 hover:border-blue-600 shadow-sm" 
                                       value="{{ $doc->id }}"
                                       onchange="updateBulkActionsBar()">
                                <div class="absolute -top-10 left-0 bg-gray-900 text-white text-xs px-3 py-2 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition-opacity z-20 font-medium">
                                    <i class="fas fa-check-square mr-1"></i>
                                    Click to select for bulk verification
                                    <div class="absolute top-full left-3 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                </div>
                            </label>

                            <!-- History Button (Top Right) -->
                            <button onclick="openVersionHistoryModal({{ $doc->id }}); event.stopPropagation();" 
                                    class="w-[30px] h-[30px] absolute top-2 right-2 z-10 p-0 bg-purple-600 text-white rounded-full hover:bg-purple-700 transition-colors shadow-md"
                                    title="View Version History">
                                <i class="fas fa-history text-xs"></i>
                            </button>

                            <!-- Document Image/Thumbnail -->
                            <div class="flex flex-wrap justify-center w-full sm:w-[80px] h-[90px] sm:h-[90px] items-center p-2 bg-[#E3F2FF] rounded-md cursor-pointer view-document-card hover:bg-[#D0E7FF] transition-colors group relative" data-doc='@json($doc)'>
                                @if($doc->file_path)
                                    @php
                                        // Use thumbnail if available, otherwise use original file
                                        $thumbnailPath = $doc->thumbnail_path ? asset('storage/' . $doc->thumbnail_path) : null;
                                        $filePath = asset('storage/' . $doc->file_path);
                                        $extension = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                                    @endphp

                                    @if(in_array($extension, ['jpg','jpeg','png','gif','bmp','webp','svg']))
                                        @if($thumbnailPath)
                                            <img src="{{ $thumbnailPath }}" alt="{{ $doc->name ?? 'Document' }}" class="max-w-full max-h-full object-contain rounded shadow-sm group-hover:shadow-md transition-shadow" onerror="this.onerror=null; this.src='{{ $filePath }}'; this.onerror=function(){this.style.display='none'; this.nextElementSibling.style.display='flex';};">
                                        @else
                                            <img src="{{ $filePath }}" alt="{{ $doc->name ?? 'Document' }}" class="max-w-full max-h-full object-contain rounded shadow-sm group-hover:shadow-md transition-shadow" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        @endif
                                        <div style="display:none; flex-direction:column; align-items:center; color:#999;" class="fallback-icon">
                                            <i class="fas fa-file-image text-4xl mb-1"></i>
                                            <span class="text-xs">Image</span>
                                        </div>
                                        <!-- Hover overlay for images -->
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 rounded-md transition-all flex items-center justify-center">
                                            <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition-opacity text-lg"></i>
                                        </div>
                                    @elseif($extension === 'pdf')
                                        <div class="flex flex-col items-center text-red-600 group-hover:text-red-700 transition-colors">
                                            <img src="{{ asset('images/pdf.png') }}" alt="PDF" class="h-10 w-10 object-contain group-hover:scale-110 transition-transform" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-file-pdf text-red-600 text-4xl\'></i>';">
                                            <span class="text-xs mt-1 font-medium">PDF</span>
                                        </div>
                                    @else
                                        <div class="flex flex-col items-center text-gray-500">
                                            <i class="fas fa-file text-2xl mb-1"></i>
                                            <span class="text-gray-400 text-xs">File</span>
                                        </div>
                                    @endif
                                @else
                                    <div class="flex flex-col items-center text-gray-400">
                                        <i class="fas fa-file-alt text-2xl mb-1"></i>
                                        <span class="text-xs">No File</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Document Details -->
                            <div class="w-full flex flex-col sm:flex-row sm:justify-between items-start gap-3">
                                <div class="flex-1">
                                    <h3 class="text-md font-semibold mb-1 text-left">{{ $doc->name }}</h3>
                                    <p class="text-sm font-semibold text-gray-800 mb-1">
                                        @php
                                            // Priority: document_type->name > document_name > otherDocument->doc_name > type
                                            if ($doc->documentType) {
                                                // New document type system
                                                $displayName = $doc->documentType->name;
                                            } elseif ($doc->document_name) {
                                                // Has document_name field set
                                                $displayName = $doc->document_name;
                                            } elseif ($doc->type === 'certificate') {
                                                $displayName = $doc->certificates->first()?->type->name ?? 'Certificate';
                                            } elseif ($doc->type === 'passport') {
                                                $displayName = 'Passport';
                                            } elseif ($doc->type === 'idvisa') {
                                                $displayName = 'ID / Visa';
                                            } elseif ($doc->type === 'resume') {
                                                $displayName = $doc->otherDocument?->doc_name ?? 'Resume';
                                            } elseif ($doc->type === 'other') {
                                                $displayName = $doc->otherDocument?->doc_name ?? 'Other Document';
                                            } else {
                                                $displayName = ucfirst($doc->type ?? 'Document');
                                            }
                                        @endphp
                                        {{ $displayName }}
                                    </p>

                                    <!-- Eye icon + Featured -->
                                    <div class="flex flex-col space-y-1 text-gray-600 text-[12px]">
                                        <div class="flex items-center space-x-2">
                                            <span class="toggle-share cursor-pointer" data-id="{{ $doc->id }}" onclick="toggleShare(this)" 
                                                  title="{{ $doc->is_active ? 'Deactivate' : 'Activate' }}">
                                              <img src="{{ $doc->is_active ? asset('images/view-icon.png') : asset('images/view-off-slash.png') }}" class="w-4 h-4">
                                            </span>
                                            <span class="share-text">
                                              {{ $doc->is_active ? 'Featured on your Profile Preview' : 'Not featured on your Profile Preview' }}
                                            </span>
                                        </div>

                                        <!-- Status Display -->
                                        @php
                                            $statusColor = 'text-yellow-600';
                                            if($doc->status === 'approved') $statusColor = 'text-green-600';
                                            elseif($doc->status === 'rejected') $statusColor = 'text-red-600';
                                        @endphp
                                        <div class="text-[14px]">
                                            Status: <span class="{{ $statusColor }} font-semibold">{{ ucfirst($doc->status ?? 'pending') }}</span>
                                        </div>

                                        <!-- OCR Status Display -->
                                        @if($doc->ocr_status)
                                        <div class="text-[12px] mt-1">
                                            <span class="px-2 py-0.5 text-xs font-medium rounded
                                                @if($doc->ocr_status === 'completed') bg-blue-100 text-blue-800
                                                @elseif($doc->ocr_status === 'processing') bg-yellow-100 text-yellow-800
                                                @elseif($doc->ocr_status === 'failed') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif"
                                                title="@if($doc->ocr_status === 'completed' && $doc->ocr_confidence)OCR Confidence: {{ number_format($doc->ocr_confidence, 1) }}%@elseif($doc->ocr_status === 'failed'){{ $doc->ocr_error ?? 'OCR processing failed' }}@else OCR {{ ucfirst($doc->ocr_status) }}@endif">
                                                <i class="fas fa-eye mr-1"></i>
                                                @if($doc->ocr_status === 'completed' && $doc->ocr_confidence)
                                                    OCR {{ number_format($doc->ocr_confidence, 0) }}%
                                                @else
                                                    OCR {{ ucfirst($doc->ocr_status) }}
                                                @endif
                                            </span>
                                        </div>
                                        @endif

                                        <!-- Verification Level Badge -->
                                        @if($doc->verificationLevel)
                                        <div class="text-[12px] mt-1">
                                            <span class="px-2 py-0.5 text-xs font-medium rounded
                                                @if($doc->verificationLevel->badge_color === 'gold') bg-yellow-100 text-yellow-800
                                                @elseif($doc->verificationLevel->badge_color === 'purple') bg-purple-100 text-purple-800
                                                @elseif($doc->verificationLevel->badge_color === 'green') bg-green-100 text-green-800
                                                @elseif($doc->verificationLevel->badge_color === 'blue') bg-blue-100 text-blue-800
                                                @else bg-gray-100 text-gray-800
                                                @endif"
                                                title="{{ $doc->verificationLevel->description }}">
                                                <i class="{{ $doc->verificationLevel->badge_icon }} mr-1"></i>
                                                {{ $doc->verificationLevel->name }} (Level {{ $doc->verificationLevel->level }})
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Remaining Duration Badge -->
                                <div class="flex flex-col items-center space-y-2">
                                    @php
                                        $badgeClasses = 'bg-[#E3F2FF] text-[#0053FF]';
                                        $isCross = false;

                                        if(strtoupper($doc->remaining_type) === 'EXPIRED') {
                                            $badgeClasses = 'bg-[#FFE3E3] text-[#C02020]';
                                            $isCross = true;
                                        } elseif($doc->remaining_type === 'N/A') {
                                            $badgeClasses = 'bg-[#E3F2FF] text-[#0053FF]';
                                        } elseif($doc->is_expiring_soon) {
                                            $badgeClasses = 'bg-yellow-200 text-yellow-800';
                                        }
                                    @endphp

                                    @if($isCross)
                                        <div class="flex items-center justify-center w-[60px] h-[60px] rounded-md {{ $badgeClasses }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="#C02020">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="flex items-center p-2 font-medium w-[60px] justify-center rounded-md text-center {{ $badgeClasses }}">
                                            @if($doc->remaining_number !== null)
                                                <div class="flex flex-col items-center">
                                                    <span class="text-xl font-bold">{{ $doc->remaining_number }}</span>
                                                    <span class="text-xs">{{ $doc->remaining_type }}</span>
                                                </div>
                                            @else
                                                <div class="flex flex-col items-center">
                                                    <span class="text-xs">{{ $doc->remaining_type }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Action Buttons for Rejected Documents and OCR Retry --}}
                            @if($isRejected || $doc->ocr_status === 'failed')
                            <div class="mt-auto pt-3 border-t border-gray-200 w-full space-y-2">
                                @if($isRejected)
                                <button type="button" 
                                        onclick="editDocument({{ $doc->id }})"
                                        class="w-full px-3 py-2 bg-orange-600 text-white text-xs rounded-md hover:bg-orange-700 transition-colors font-medium shadow-sm">
                                    <i class="fas fa-redo mr-1"></i>Re-Submit
                                </button>
                                @endif
                                @if($doc->ocr_status === 'failed')
                                <button type="button" 
                                        onclick="retryOcr({{ $doc->id }})"
                                        class="w-full px-3 py-2 bg-purple-600 text-white text-xs rounded-md hover:bg-purple-700 transition-colors font-medium shadow-sm">
                                    <i class="fas fa-sync-alt mr-1"></i>Retry OCR
                                </button>
                                @endif
                            </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
@endif

                            </div>

                        </div>
                    </div>
                </div>

                                {{-- Close All Documents Tab --}}
                                </div>

                                {{-- Active Share Links Tab Content --}}
                                <div id="tab-content-shares" class="tab-content hidden">
                                    <div class="bg-white rounded-xl p-4 sm:p-5 border border-gray-200">
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                <i class="fas fa-link mr-2 text-[#0053FF]"></i>Active Share Links
                                            </h3>
                                            <button onclick="loadActiveShares(1)" class="text-sm text-[#0053FF] hover:text-[#0044DD] flex items-center gap-1">
                                                <i class="fas fa-sync-alt"></i> Refresh
                                            </button>
                                        </div>
                                        
                                        <div id="activeSharesList" class="space-y-3">
                                            <div class="text-center py-8 text-gray-500">
                                                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                                <p>Loading active shares...</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Pagination -->
                                        <div id="activeSharesPagination" class="mt-6 flex items-center justify-between border-t border-gray-200 pt-4">
                                        </div>
                                    </div>
                                </div>

                                {{-- Expired Documents Tab Content --}}
                                <div id="tab-content-expired" class="tab-content hidden">
                                    @if($expiredDocs->count() > 0)
                                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                                        <div class="overflow-x-auto">
                                            <table class="w-full">
                                                <thead class="bg-gray-50 border-b border-gray-200">
                                                    <tr>
                                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Document</th>
                                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Type</th>
                                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Expired Date</th>
                                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @foreach($expiredDocs as $doc)
                                                    @php
                                                        $docName = $doc->type === 'passport' ? 'Passport' : ($doc->type === 'idvisa' ? 'ID / Visa' : ($doc->type === 'certificate' ? ($doc->certificates->first()->type->name ?? 'Certificate') : ($doc->type === 'resume' ? 'Resume' : ($doc->otherDocument->doc_name ?? 'Other Document'))));
                                                        $daysAgo = $doc->expiry_date ? abs(\Carbon\Carbon::parse($doc->expiry_date)->diffInDays(\Carbon\Carbon::now())) : 0;
                                                    @endphp
                                                    <tr class="hover:bg-red-50 transition-colors">
                                                        <td class="px-4 py-3 whitespace-nowrap">
                                                            <div class="flex items-center gap-3">
                                                                <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-md flex items-center justify-center cursor-pointer view-document-card" data-doc='@json($doc)'>
                                                                    @if($doc->file_path)
                                                                        @php
                                                                            $filePath = asset('storage/' . $doc->file_path);
                                                                            $extension = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                                                                        @endphp
                                                                        @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp']))
                                                                            <img src="{{ $filePath }}" alt="Document" class="w-full h-full object-cover rounded">
                                                                        @elseif($extension === 'pdf')
                                                                            <i class="fas fa-file-pdf text-red-600 text-sm"></i>
                                                                        @else
                                                                            <i class="fas fa-file text-red-600 text-sm"></i>
                                                                        @endif
                                                                    @else
                                                                        <i class="fas fa-file text-red-600 text-sm"></i>
                                                                    @endif
                                                                </div>
                                                                <div>
                                                                    <div class="text-sm font-medium text-gray-900">{{ $docName }}</div>
                                                                    @if($doc->document_number)
                                                                    <div class="text-xs text-gray-500">#{{ $doc->document_number }}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap">
                                                            <span class="text-sm text-gray-900">{{ ucfirst($doc->type) }}</span>
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap">
                                                            @if($doc->expiry_date)
                                                            <div class="text-sm text-red-600 font-medium">
                                                                {{ \Carbon\Carbon::parse($doc->expiry_date)->format('M d, Y') }}
                                                            </div>
                                                            <div class="text-xs text-gray-500">{{ $daysAgo }} days ago</div>
                                                            @else
                                                            <span class="text-sm text-gray-400">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap">
                                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                                EXPIRED
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                                            <div class="flex items-center justify-center gap-2">
                                                                <button onclick="viewDocument(@json($doc))" 
                                                                        class="p-2 text-[#0053FF] hover:bg-blue-50 rounded-md transition-colors" 
                                                                        title="View Details">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                <button onclick="editDocument({{ $doc->id }})" 
                                                                        class="p-2 text-orange-600 hover:bg-orange-50 rounded-md transition-colors" 
                                                                        title="Re-Upload">
                                                                    <i class="fas fa-redo"></i>
                                                                </button>
                                                                <button onclick="openVersionHistoryModal({{ $doc->id }});" 
                                                                        class="p-2 text-purple-600 hover:bg-purple-50 rounded-md transition-colors" 
                                                                        title="History">
                                                                    <i class="fas fa-history"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @else
                                    <div class="text-center py-8 bg-gray-50 rounded-lg border border-gray-200">
                                        <i class="fas fa-check-circle text-3xl text-green-500 mb-3"></i>
                                        <p class="text-gray-700 font-medium">No expired documents</p>
                                        <p class="text-sm text-gray-500 mt-1">All your documents are up to date!</p>
                                    </div>
                                    @endif
                                </div>
            </main>
        </div>
    </div>
    <!-- Action delete Buttons -->
   
</div>

@elserole('super_admin')

<div class="w-full">
    <div id="searchResults">
        @include('career-history.super-admin-career-history-dashboard')
    </div>
</div>
@endrole

<!-- Add Document Modal -->
<div id="addDocumentModal" class="popup hidden fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-[95%] sm:w-[90%] h-[95%] sm:h-[90%] max-w-6xl flex flex-col relative" style="isolation: isolate;">

        <!-- Top header: Left text + Right close -->
        <div class="flex justify-between items-center px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-300">
            <h2 class="text-xl sm:text-2xl font-bold">Add Document</h2>
            <button class="closePopup text-gray-600 hover:text-gray-800 text-2xl sm:text-3xl font-bold">&times;</button>
        </div>

        <!-- Main content: Left + Right -->
        <div class="flex flex-col lg:flex-row flex-1 overflow-hidden">

            <!-- LEFT SIDE: Upload + Preview -->
            <div class="w-full lg:w-1/2 border-b lg:border-b-0 lg:border-r p-4 sm:p-6 flex flex-col items-center relative">
                <!-- Hidden file input -->
                <input type="file" id="docFile" class="hidden" accept="image/*,application/pdf">

                <div id="previewBox" class="w-full h-full rounded-xl flex flex-col items-center justify-center 
                            text-gray-500 cursor-pointer transition-all duration-200 relative overflow-hidden px-4 text-center
                            bg-sky-50">

                    <!-- Placeholder -->
                    <span id="placeholderText" class="flex flex-col items-center justify-center w-full h-full text-center">
                        <svg width="30" height="25" viewBox="0 0 30 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M25.6809 20.5809C26.922 19.9043 27.9025 18.8336 28.4675 17.5378C29.0326 16.2421 29.15 14.7951 28.8014 13.4252C28.4527 12.0552 27.6577 10.8404 26.542 9.97251C25.4262 9.10457 24.0532 8.63291 22.6396 8.63198H21.0362C20.651 7.14217 19.9331 5.75906 18.9365 4.58663C17.9398 3.41421 16.6904 2.48298 15.282 1.86296C13.8737 1.24293 12.3431 0.950246 10.8053 1.0069C9.26756 1.06356 7.76266 1.46809 6.40375 2.19007C5.04485 2.91205 3.86729 3.9327 2.95962 5.17529C2.05194 6.41787 1.43777 7.85006 1.16328 9.36418C0.888783 10.8783 0.961107 12.4349 1.37481 13.9171C1.78852 15.3992 2.53284 16.7683 3.55182 17.9214" 
                                stroke="#0053FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9.91459 18.8123L15.0047 23.9023L20.0947 18.8123" stroke="#0053FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M15.0039 23.9023L15.0039 12.4497" stroke="#0053FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <p class="text-lg font-medium mt-4 text-blue-600">Drag and Drop or browse your file</p>
                        <p class="text-sm text-gray-400">Support JPG, PNG, PDF | Max Size 5 MB</p>
                    </span>

                    <!-- Transparent scan overlay inside preview -->
                    <div id="scanOverlay" class="hidden absolute inset-0 bg-white bg-opacity-40 flex items-center justify-center z-20">
                        <!-- Moving scan line -->
                        <div id="scanLine" class="absolute top-0 left-0 w-full h-1 bg-blue-600 animate-scan"></div>
                    </div>
                </div>

                <p id="scanError" class="text-red-500 text-sm mt-2 hidden"></p>
              
              	<!-- Replace button (hidden initially) -->
                <button type="button" id="replaceBtn" class="hidden mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Replace Document
                </button>

            </div>

            <!-- RIGHT SIDE: Dynamic Form -->
            <div class="w-full lg:w-1/2 p-4 sm:p-6 overflow-y-auto">

                <form id="documentForm" class="flex flex-col h-full mb-0">
                    @csrf

                    <!-- Document Type -->
                    <div class="mb-4 relative">
                        <label class="block mb-1">Document Type</label>
                        <select id="docType" name="type" class="w-full border p-2 rounded-md">
                            <option value="">Select document type</option>
                            @foreach($documentTypes as $category => $types)
                                <optgroup label="{{ $category }}">
                                    @foreach($types as $type)
                                        <option value="{{ $type->slug }}">{{ $type->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>

                        <!-- Notification for unmatched document -->
                        <div id="typeNotification" class="absolute left-0 top-full mt-2 w-full bg-blue-50 border border-blue-200 text-blue-700 rounded p-3 flex items-start justify-between space-x-2 text-sm hidden shadow-sm">
                            <div class="flex items-start space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-1 flex-shrink-0 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zm-8 3a1 1 0 100 2 1 1 0 000-2zm0-7a1 1 0 00-.993.883L9 7v4a1 1 0 001.993.117L11 11V7a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <div>Document not matched. Please fill in manually.</div>
                                    <div class="mt-1 text-blue-600 hover:underline cursor-pointer">Find out more</div>
                                </div>
                            </div>
                            <button id="closeTypeNotification" class="text-blue-500 hover:text-blue-700 text-xl font-bold leading-none">&times;</button>
                        </div>
                    </div>

                    <!-- Dynamic fields container -->
                    <div id="dynamicFields" class="space-y-4"></div>

                     <!-- Fixed Buttons at Bottom Right -->
                    <div class="sticky mt-auto left-0 flex space-x-3 justify-start bg-white p-3 pl-0 bottom-[-25px]">
                        <button type="button" class="closePopup px-4 py-[10px] border rounded-md bg-white hover:bg-gray-100 min-w-[130px]">Cancel</button>
                        <button type="submit" form="documentForm" class="px-4 py-[10px] bg-blue-600 text-white rounded-md hover:bg-blue-700 min-w-[130px]">Save</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- View Document Modal -->
<div id="viewDocumentModal" class="popup hidden fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-[95%] sm:w-[90%] h-[95%] sm:h-[90%] max-w-6xl flex flex-col relative overflow-hidden transform scale-95 transition-transform duration-300">

        <!-- Header -->
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-300 bg-gray-50">
            <h2 class="text-2xl font-bold text-blue-600">View Document</h2>
                            <button class="closePopup text-gray-600 hover:text-gray-800 text-3xl font-bold" aria-label="Close document view">&times;</button>
        </div>

        <!-- Content -->
        <div class="flex flex-1 overflow-hidden">

            <!-- LEFT: Preview -->
            <div class="w-full lg:w-1/2 border-b lg:border-b-0 lg:border-r p-4 sm:p-6 flex flex-col items-center justify-center relative bg-gray-50">
                <!-- Zoom Controls for Images -->
                <div id="zoomControls" class="absolute top-6 right-6 z-10 flex gap-2 hidden">
                    <button id="zoomInBtn" class="bg-white hover:bg-gray-100 text-gray-700 px-3 py-2 rounded-md shadow-md border border-gray-300 transition-colors" title="Zoom In" aria-label="Zoom in on document">
                        <i class="fas fa-search-plus" aria-hidden="true"></i>
                    </button>
                    <button id="zoomOutBtn" class="bg-white hover:bg-gray-100 text-gray-700 px-3 py-2 rounded-md shadow-md border border-gray-300 transition-colors" title="Zoom Out" aria-label="Zoom out on document">
                        <i class="fas fa-search-minus" aria-hidden="true"></i>
                    </button>
                    <button id="resetZoomBtn" class="bg-white hover:bg-gray-100 text-gray-700 px-3 py-2 rounded-md shadow-md border border-gray-300 transition-colors" title="Reset Zoom" aria-label="Reset zoom to 100%">
                        <i class="fas fa-expand-arrows-alt" aria-hidden="true"></i>
                    </button>
                </div>
                
                <div id="viewPreviewBox" class="w-full h-[450px] border-2 border-dashed border-gray-300 rounded-md flex items-center justify-center text-gray-500 overflow-auto px-4 text-center bg-white shadow-sm relative">
                    <img id="viewDocImage" src="" alt="Document Preview" class="max-w-full max-h-full hidden rounded shadow-md cursor-zoom-in transition-transform duration-200" style="transform-origin: center;" role="img" aria-label="Document preview image">
                    <embed id="viewDocPDF" src="" type="application/pdf" class="w-full h-full hidden">
                    <span id="viewNoFileText" class="text-gray-400 font-medium">No File</span>
                </div>
                
                <!-- Zoom Level Indicator -->
                <div id="zoomLevel" class="mt-2 text-sm text-gray-500 hidden">100%</div>
            </div>

            <!-- RIGHT: Details -->
            <div class="w-1/2 p-6 overflow-y-auto space-y-4">

                <!-- Document Name -->
                <div>
                    <label class="block font-semibold text-gray-600">Document Name</label>
                    <p id="viewDocName" class="text-gray-800 text-lg font-medium">-</p>
                </div>

                <!-- Status -->
                <div>
                    <label class="block font-semibold text-gray-600">Status</label>
                    <p id="viewDocStatus" class="text-gray-800 text-lg font-medium">-</p>
                </div>

                <!-- DOB -->
                <div id="dobDiv" class="hidden">
                    <label class="block font-semibold text-gray-600">Date of Birth</label>
                    <p id="viewDOB" class="text-gray-800 text-lg font-medium">-</p>
                </div>

                <!-- Issue Date -->
                <div id="issueDateDiv" class="hidden">
                    <label class="block font-semibold text-gray-600">Issue Date</label>
                    <p id="viewIssueDate" class="text-gray-800 text-lg font-medium">-</p>
                </div>

                <!-- Expiry Date -->
                <div id="expiryDateDiv" class="hidden">
                    <label class="block font-semibold text-gray-600">Expiry Date</label>
                    <p id="viewExpiryDate" class="text-gray-800 text-lg font-medium">-</p>
                </div>

                <!-- Nationality -->
                <div id="nationalityDiv" class="hidden">
                    <label class="block font-semibold text-gray-600">Nationality</label>
                    <p id="viewNationality" class="text-gray-800 text-lg font-medium">-</p>
                </div>

                <!-- Country Code -->
                <div id="countryCodeDiv" class="hidden">
                    <label class="block font-semibold text-gray-600">Country Code</label>
                    <p id="viewCountryCode" class="text-gray-800 text-lg font-medium">-</p>
                </div>

                <!-- Remaining Duration -->
                <div id="remainingDurationDiv" class="hidden">
                    <label class="block font-semibold text-gray-600">Remaining Duration</label>
                    <p id="viewDocRemaining" class="text-gray-800 text-lg font-medium">-</p>
                </div>

                <!-- Document Number -->
                <div id="documentNumberDiv" class="hidden">
                    <label class="block font-semibold text-gray-600">Document Number</label>
                    <p id="viewDocumentNumber" class="text-gray-800 text-lg font-medium">-</p>
                </div>

                <!-- Certificate Details -->
                <div id="certificateDiv" class="hidden space-y-2">
                    <label class="block font-semibold text-gray-600">Qualifications</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-500 text-sm">Certificate Type</label>
                            <p id="viewCertificateType" class="text-gray-800 font-medium">-</p>
                        </div>
                        <div>
                            <label class="block text-gray-500 text-sm">Issue Date</label>
                            <p id="viewCertificateIssue" class="text-gray-800 font-medium">-</p>
                        </div>
                        <div>
                            <label class="block text-gray-500 text-sm">Expiry Date</label>
                            <p id="viewCertificateExpiry" class="text-gray-800 font-medium">-</p>
                        </div>
                        <div>
                            <label class="block text-gray-500 text-sm">Certificate Number</label>
                            <p id="viewCertificateNumber" class="text-gray-800 font-medium">-</p>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-gray-500 text-sm">Certificate Issuer</label>
                            <p id="viewCertificateIssuer" class="text-gray-800 font-medium">-</p>
                        </div>
                    </div>
                </div>

                <!-- Verification Notes (if rejected) -->
                <div id="verificationNotesDiv" class="hidden">
                    <label class="block font-semibold text-gray-600">Verification Notes</label>
                    <p id="viewVerificationNotes" class="text-gray-800 text-sm bg-yellow-50 p-3 rounded border border-yellow-200">-</p>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 border-t border-gray-200 space-y-2">
                    <div id="reSubmitButtonDiv" class="hidden">
                        <button type="button" 
                                id="reSubmitButton"
                                onclick="editDocument(0)"
                                class="w-full px-4 py-2 bg-orange-600 text-white text-sm rounded-md hover:bg-orange-700 transition-colors font-medium shadow-sm">
                            <i class="fas fa-redo mr-1"></i>Re-Submit Document
                        </button>
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        <button type="button" 
                                id="historyDocumentButton"
                                onclick="openVersionHistoryModal(window.currentViewDocId || 0);"
                                class="flex-1 px-4 py-2 bg-purple-600 text-white text-sm rounded-md hover:bg-purple-700 transition-colors font-medium min-w-[120px]"
                                aria-label="View version history">
                            <i class="fas fa-history mr-1" aria-hidden="true"></i>History
                        </button>
                        <button type="button" 
                                id="editDocumentButton"
                                onclick="editDocument(0)"
                                class="flex-1 px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition-colors font-medium min-w-[120px]"
                                aria-label="Edit document">
                            <i class="fas fa-edit mr-1" aria-hidden="true"></i>Edit
                        </button>
                        <button type="button" 
                                id="downloadDocumentButton"
                                class="flex-1 px-4 py-2 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700 transition-colors font-medium min-w-[120px]"
                                aria-label="Download document">
                            <i class="fas fa-download mr-1" aria-hidden="true"></i>Download
                        </button>
                        <button type="button" 
                                id="printDocumentButton"
                                class="flex-1 px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition-colors font-medium min-w-[120px]"
                                aria-label="Print document">
                            <i class="fas fa-print mr-1" aria-hidden="true"></i>Print
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Share Document Modal -->
<div id="shareDocumentModal" class="popup hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl w-[1000px] max-h-[90vh] overflow-y-auto p-6 relative">
        <!-- Close Button -->
        <button class="closePopup absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-2xl">&times;</button>

        <!-- Title -->
        <h3 class="text-xl font-semibold mb-4">Share Documents</h3>


        <!-- Share Form -->
        <div id="shareDocumentForm">
            <div>
                {{-- Template Selection -- Hidden --}}
                <div class="mb-4 hidden">
                    <label class="block mb-2 text-sm font-medium">Use Template (Optional)</label>
                    <select id="shareTemplateSelect" class="w-full border p-2 rounded outline-none">
                        <option value="">-- Select a template --</option>
                        @php
                            $templates = \App\Models\ShareTemplate::forUser(auth()->id())
                                ->orderBy('is_default', 'desc')
                                ->orderBy('name', 'asc')
                                ->get();
                        @endphp
                        @foreach($templates as $template)
                        <option value="{{ $template->id }}" 
                                data-expiry="{{ $template->expiry_duration_days }}"
                                data-message="{{ $template->default_message ?? '' }}">
                            {{ $template->name }}
                            @if($template->is_default) (Default) @endif
                        </option>
                        @endforeach
                    </select>
                    {{-- Template Preview --}}
                    <div id="templatePreview" class="hidden mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="text-sm font-semibold text-blue-900">Template Preview</h4>
                            <button type="button" onclick="clearTemplate()" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="text-xs text-blue-700 space-y-1">
                            <p><strong>Expiry:</strong> <span id="templateExpiry">-</span> days</p>
                            <p><strong>Default Message:</strong> <span id="templateMessage">-</span></p>
                        </div>
                    </div>
                </div>

                <!-- Validation Error Summary -->
                <div id="shareValidationErrors" class="hidden mb-4 p-4 bg-red-50 border-l-4 border-red-400 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul id="shareErrorList" class="list-disc list-inside space-y-1"></ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recipient Email (Optional) - Multiple Emails Support -->
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium">
                        <i class="fas fa-envelope mr-1 text-blue-600"></i>Recipient Email(s) <span class="text-gray-400 font-normal">(Optional)</span>
                    </label>
                    <div class="border border-gray-300 rounded-lg p-2 min-h-[50px] bg-white">
                        <div id="recipientEmailTags" class="flex flex-wrap gap-2 mb-2"></div>
                        <input type="text" id="recipientEmail" placeholder="Enter email addresses (comma or space separated)" class="w-full border-0 p-1 outline-none focus:ring-0" />
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i><strong>Purpose:</strong> Send email notifications to recipients when share is created. You can add multiple emails (max 10). Leave empty to create share link without sending emails.
                    </p>
                    <p id="recipientEmailError" class="text-red-600 text-xs mt-1 hidden"></p>
                </div>

                <!-- Message -->
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium">Message</label>
                    <textarea id="messageInput" class="w-full border p-2 rounded outline-none" rows="3" placeholder="Enter message (optional)"></textarea>
                </div>

                <!-- Expiry Options -->
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium">Link Expiry</label>
                    <select id="expiryOption" class="w-full border p-2 rounded outline-none">
                        <option value="7">7 Days</option>
                        <option value="30" selected>30 Days</option>
                        <option value="permanent">Permanent (No Expiry)</option>
                    </select>
                </div>

                <!-- Security Options -->
                <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <h4 class="text-sm font-semibold mb-3">Security Options</h4>
                    <p class="text-xs text-gray-600 mb-3 bg-blue-50 p-2 rounded border border-blue-200">
                        <i class="fas fa-shield-alt mr-1 text-blue-600"></i><strong>Note:</strong> You can revoke access to any share link at any time from the success screen or your shares management page.
                    </p>
                    
                    <!-- Password Protection -->
                    <div class="mb-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="enablePassword" class="mr-2" />
                            <span class="text-sm font-medium">Password Protection</span>
                        </label>
                        <input type="password" id="sharePassword" placeholder="Enter password (min 6 characters)" class="w-full border p-2 rounded mt-2 outline-none hidden" minlength="6" />
                    </div>

                    <!-- Email Restriction -->
                    <div class="mb-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="enableEmailRestriction" class="mr-2" />
                            <span class="text-sm font-medium">Restrict to Specific Email</span>
                        </label>
                        <input type="email" id="restrictToEmail" placeholder="Only this email can access" class="w-full border p-2 rounded mt-2 outline-none hidden" />
                        <p class="text-xs text-orange-600 bg-orange-50 p-2 rounded mt-2 hidden border border-orange-200" id="emailRestrictionHint">
                            <i class="fas fa-shield-alt mr-1"></i><strong>Security Feature:</strong> Only the specified email address will be able to access this share link. This is different from "Recipient Email" - this restricts who can VIEW the share, while "Recipient Email" only sends notifications.
                        </p>
                    </div>

                    <!-- Generate QR Code -->
                    <div>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="generateQrCode" class="mr-2" />
                            <span class="text-sm font-medium">Generate QR Code</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">Create a QR code for easy sharing</p>
                    </div>
                </div>

                <!-- Document Selection -->
                <div class="mb-4">
                    <label class="block text-lg font-semibold mb-3">Select Documents <span class="text-red-500">*</span></label>
                    <p id="documentsError" class="text-red-600 text-sm mt-1 mb-2 hidden"></p>
                    <div id="docList" class="grid grid-cols-3 gap-4">
                        @foreach($share_documents as $doc)
                        <div class="docCard border rounded-lg p-3 cursor-pointer hover:shadow-lg transition-shadow duration-200 ease-in-out bg-white flex flex-row items-center" data-id="{{ $doc->id }}">
                            <div class="w-16 h-16 bg-gray-100 flex items-center justify-center rounded-md overflow-hidden mr-3">
                                @if(strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION)) === 'pdf')
                                	<img src="{{ asset('images/pdf.png') }}" alt="PDF" class="h-10 w-10 object-contain" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-file-pdf text-red-500 text-3xl\'></i>';">
                                @else
                                	<img src="{{ asset('storage/' . $doc->file_path) }}" alt="{{ $doc->type }}" class="object-contain h-full w-full" onerror="this.onerror=null; this.style.display='none'; this.parentElement.innerHTML='<i class=\'fas fa-file-image text-gray-400 text-3xl\'></i>';">
                                @endif
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold truncate" title="{{ $doc->type }}">{{ $doc->type }}</h4>
                                <span class="text-xs text-gray-500">Featured on profile</span>
                            </div>
                            <input type="checkbox" name="documents[]" value="{{ $doc->id }}" class="hidden docCheckbox">
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Share Button -->
                <button type="button" id="saveShareBtn" class="bg-blue-600 text-white px-4 py-3 rounded disabled:opacity-50 disabled:cursor-not-allowed flex flex-col items-center justify-center w-full hover:bg-blue-700 transition-colors">
                    <span id="shareBtnText">Create Share Link</span>
                    <svg id="shareBtnSpinner" class="animate-spin h-6 w-6 text-white mt-2 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Share Success Modal -->
<div id="shareSuccessModal" class="popup hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl w-[600px] max-h-[90vh] overflow-y-auto p-6 relative">
        <!-- Close Button -->
        <button class="closePopup absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-2xl">&times;</button>

        <!-- Title -->
        <h3 class="text-xl font-semibold mb-4 text-green-600">
            <i class="fas fa-check-circle mr-2"></i>Share Created Successfully!
        </h3>

        <!-- Success Content -->
        <div class="space-y-4">
            <!-- Share Link -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Share Link:</label>
                <div class="flex items-center gap-2">
                    <input type="text" id="shareUrlDisplay" readonly class="flex-1 px-3 py-2 border border-gray-300 rounded text-sm bg-gray-50" />
                    <button onclick="copyShareUrl()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-copy mr-1"></i> Copy
                    </button>
                </div>
            </div>

            <!-- QR Code -->
            <div id="qrCodeDisplay" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">QR Code:</label>
                <div class="flex flex-col items-center gap-4 p-4 bg-gray-50 rounded-lg">
                    <img id="qrCodeImage" src="" alt="QR Code" class="w-48 h-48 border-2 border-gray-300 rounded-lg shadow-md" />
                    <button onclick="downloadQRCode()" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm font-medium">
                        <i class="fas fa-download mr-1"></i> Download QR Code
                    </button>
                </div>
            </div>

            <!-- Revoke Access Info -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-3 rounded">
                <p class="text-sm text-blue-700">
                    <i class="fas fa-info-circle mr-1"></i><strong>Note:</strong> You can revoke access to this share link at any time. Once revoked, the link will no longer be accessible.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4">
                <button onclick="closeShareSuccessModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors font-medium">
                    Close
                </button>
                <button id="revokeShareBtn" onclick="revokeShareAccess()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors font-medium">
                    <i class="fas fa-ban mr-1"></i> Revoke Access
                </button>
                <a id="visitShareLink" href="#" target="_blank" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors text-center font-medium">
                    <i class="fas fa-external-link-alt mr-1"></i> Visit Link
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Category Assignment Modal for Imported Documents -->
<div id="categoryAssignmentModal" class="popup hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl w-[700px] max-w-[90vw] max-h-[90vh] overflow-y-auto p-6 relative">
        <button class="closePopup absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-2xl">&times;</button>
        
        <h3 class="text-xl font-semibold mb-4 text-gray-900">Help Us Organize Your Imported Documents</h3>
        
        <p class="text-sm text-gray-600 mb-6">
            Some imported documents couldn't be automatically categorized. Please assign categories (STCW, Medical, Flag State, etc.) for better organization.
        </p>
        
        <div id="categoryAssignmentList" class="space-y-4">
            <!-- Documents will be loaded here -->
        </div>
        
        <div class="flex gap-3 pt-4 border-t mt-6">
            <button onclick="closeCategoryAssignmentModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors font-medium">
                Skip for Now
            </button>
            <button onclick="saveCategoryAssignments()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors font-medium">
                Save Categories
            </button>
        </div>
    </div>
</div>

<!-- Crewdentials Consent Modal -->
<div id="crewdentialsConsentModal" class="popup hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl w-[600px] max-w-[90vw] max-h-[90vh] overflow-y-auto p-6 relative">
        <button class="closePopup absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-2xl">&times;</button>
        
        <h3 class="text-xl font-semibold mb-4 text-gray-900">Data Sharing Consent</h3>
        
        <div class="space-y-4">
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                <p class="text-sm text-gray-700">
                    <strong>Crewdentials</strong> is a third-party verification service that helps verify your documents. 
                    To use this service, we need your consent to share your document data with Crewdentials.
                </p>
            </div>

            <div class="space-y-3">
                <h4 class="font-semibold text-gray-900">What will be shared:</h4>
                <ul class="list-disc list-inside space-y-2 text-sm text-gray-600 ml-4">
                    <li>Document files and metadata (name, type, issue date, expiry date)</li>
                    <li>Your email address for account linking</li>
                    <li>Verification status updates</li>
                </ul>
            </div>

            <div class="space-y-3">
                <h4 class="font-semibold text-gray-900">How it's used:</h4>
                <ul class="list-disc list-inside space-y-2 text-sm text-gray-600 ml-4">
                    <li>Crewdentials verifies your documents against official databases</li>
                    <li>Verification results are sent back to YWC</li>
                    <li>You can revoke consent at any time from settings</li>
                </ul>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <a href="{{ config('services.crewdentials.policy_url', '#') }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                    <i class="fas fa-external-link-alt mr-1"></i>View Data Sharing Policy
                </a>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="flex items-start cursor-pointer">
                    <input type="checkbox" id="consentCheckbox" class="mt-1 mr-3 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500" />
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-900 block">
                            I consent to sharing my document data with Crewdentials for verification purposes
                        </span>
                        <span class="text-xs text-gray-500 mt-1 block">
                            You can withdraw this consent at any time from your account settings
                        </span>
                    </div>
                </label>
            </div>

            <div class="flex gap-3 pt-4">
                <button onclick="closeConsentModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors font-medium">
                    Cancel
                </button>
                <button id="consentSubmitBtn" onclick="submitConsent()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="consentBtnText">Give Consent & Continue</span>
                    <svg id="consentBtnSpinner" class="animate-spin h-5 w-5 text-white hidden inline-block ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Share Profile Modal -->
<div id="shareProfileModal" class="popup hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl w-1/3 h-[600px] p-6 relative flex flex-col">
        <button class="closePopup absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-2xl">&times;</button>

        <h3 class="text-2xl font-semibold mb-6 text-[#0053FF] text-center">Share Profile</h3>

        <!-- Loader -->
        <div id="loader" class="flex justify-center items-center absolute inset-0 bg-white bg-opacity-75 z-10 hidden">
            <div class="loader ease-linear rounded-full border-4 border-t-4 border-blue-500 h-12 w-12"></div>
        </div>

        <!-- QR Code -->
        <div id="qrcode" class="flex justify-center mb-6 flex-1 items-center">
            <img id="qrcodeImage" src="" alt="QR Code" class="w-64 h-64 mx-auto opacity-0 transition-opacity duration-500"/>
        </div>

        <!-- Profile Link with Copy Icon -->
        <div class="flex justify-center items-center mb-6 px-4">
            <input id="profileLink" type="text" readonly value="" class="border px-4 py-2 rounded-l w-full text-sm"/>
            <button id="copyBtn" class="bg-gray-200 hover:bg-gray-300 px-3 py-2 rounded-r flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h6a2 2 0 012 2v2m0 0h4a2 2 0 012 2v8a2 2 0 01-2 2h-8a2 2 0 01-2-2v-4"/>
                </svg>
            </button>
        </div>

        <!-- Buttons -->
        <div class="flex justify-center space-x-4 px-4">
            <!-- Download QR Code Button -->
            <button id="downloadBtn" onclick="downloadQRCode()" class="bg-gray-300 hover:bg-gray-400 px-6 py-2 rounded w-1/2 flex justify-center items-center space-x-2 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                </svg>
                <span>Download QR</span>
            </button>

            <a id="visitProfileBtn" href="#" target="_blank" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 w-1/2 text-center">
                Visit Profile
            </a>
        </div>
    </div>
</div>


@endsection

<style>
/* Loader animation */
.loader {
    border-top-color: #3498db;
    animation: spin 1s linear infinite;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes scan-move {
  0% { top: 0; }
  50% { top: 100%; }
  100% { top: 0; }
}

.animate-scan {
  animation: scan-move 2s linear infinite;
}

/* Fix date picker positioning in modal */
#addDocumentModal {
    isolation: isolate;
}

#addDocumentModal input[type="date"] {
    position: relative;
    z-index: 10;
}

#addDocumentModal input[type="date"]::-webkit-calendar-picker-indicator {
    position: relative;
    z-index: 20;
    cursor: pointer;
}

#addDocumentModal .relative {
    isolation: isolate;
}
</style>

<!-- jQuery & JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script> -->
<script>
  
  // Retry OCR Function
  function retryOcr(docId) {
      if (!confirm('Retry OCR processing for this document?')) {
          return;
      }
      
      $.ajax({
          url: "/documents/" + docId + "/retry-ocr",
          method: "POST",
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response) {
              if (response.success) {
                  alert(response.message || 'OCR processing has been restarted. Please wait a few moments.');
                  // Reload page after 2 seconds to show updated status
                  setTimeout(function() {
                      window.location.reload();
                  }, 2000);
              }
          },
          error: function(xhr) {
              const message = xhr.responseJSON?.message || 'Failed to retry OCR. Please try again.';
              alert(message);
          }
      });
  }

  // Re-Submit Document Function - Opens edit modal (must be globally accessible)
  function editDocument(docId) {
      // Close view modal if open
      const viewModal = $("#viewDocumentModal");
      if (viewModal && !viewModal.hasClass("hidden")) {
          viewModal.removeClass("flex").addClass("hidden");
      }
      
      $.ajax({
          url: "/career-history/documents/" + docId + "/edit",
          method: "GET",
          headers: {
              "X-Requested-With": "XMLHttpRequest"
          },
          success: function(doc) {
              if (doc && (doc.id || doc.document_id)) {
                  // Open the modal
                  $("#addDocumentModal").removeClass("hidden").addClass("flex");
                  
                  // Change modal title
                  $("#addDocumentModal h2").text("Re-Submit Document");
                  
                  // Set form to edit mode
                  const form = $("#documentForm");
                  const editId = doc.id || doc.document_id;
                  form.attr("data-edit-id", editId);
                  form.attr("action", "/career-history/" + editId);
                  form.find('input[name="_method"]').remove();
                  form.append('<input type="hidden" name="_method" value="PUT">');
                  
                  // Populate form fields
                  $("#docType").val(doc.type || '').trigger('change');
                  $("#docName").val(doc.name || '');
                  
                  // Populate document-specific fields
                  setTimeout(function() {
                      // Support both snake_case and camelCase from API
                      const passportDetail = doc.passportDetail || doc.passport_detail;
                      const idvisaDetail = doc.idvisaDetail || doc.idvisa_detail;
                      
                      if (doc.type === 'passport' && passportDetail) {
                          // Use correct field IDs that match the dynamic form
                          const passportNumber = passportDetail.passport_number || '';
                          const nationality = passportDetail.nationality || '';
                          const countryCode = passportDetail.country_code || '';
                          const dob = passportDetail.dob || '';
                          const issueDate = passportDetail.issue_date || '';
                          const expiryDate = passportDetail.expiry_date || '';
                          
                          // Set passport-specific fields
                          $("input[name='passport_number']").val(passportNumber);
                          $("input[name='nationality']").val(nationality);
                          $("input[name='country_code']").val(countryCode);
                          
                          // Set date fields (common IDs)
                          $("input[name='dob']").val(dob);
                          $("input[name='issue_date']").val(issueDate);
                          $("input[name='expiry_date']").val(expiryDate);
                          
                          console.log("Populated passport fields:", {passportNumber, nationality, countryCode, dob, issueDate, expiryDate});
                      } else if (doc.type === 'idvisa' && idvisaDetail) {
                          const documentName = idvisaDetail.document_name || '';
                          const documentNumber = idvisaDetail.document_number || '';
                          const dob = idvisaDetail.dob || '';
                          const issueDate = idvisaDetail.issue_date || '';
                          const expiryDate = idvisaDetail.expiry_date || '';
                          const issueCountry = idvisaDetail.issue_country || '';
                          const countryCode = idvisaDetail.country_code || '';
                          
                          $("select[name='document_name']").val(documentName);
                          $("input[name='document_number']").val(documentNumber);
                          $("input[name='dob']").val(dob);
                          $("input[name='issue_date']").val(issueDate);
                          $("input[name='expiry_date']").val(expiryDate);
                          $("input[name='issue_country']").val(issueCountry);
                          $("input[name='country_code']").val(countryCode);
                          
                          console.log("Populated idvisa fields:", {documentName, documentNumber, dob, issueDate, expiryDate});
                      } else if (doc.type === 'certificate' && doc.certificates && doc.certificates.length > 0) {
                          const cert = doc.certificates[0];
                          $("#certificateType").val(cert.type_id || '');
                          $("#certificateNumber").val(cert.certificate_number || '');
                          $("#certificateIssueDate").val(cert.issue_date || '');
                          $("#certificateExpiryDate").val(cert.expiry_date || '');
                          $("#certificateIssuer").val(cert.issuer_id || '');
                      }
                  }, 500);
                  
                  // Show replace button
                  $("#replaceBtn").removeClass("hidden");
                  
                  // Show existing file preview if available
                  if (doc.file_path || doc.file_url) {
                      const filePath = doc.file_url || ("{{ asset('storage/') }}/" + doc.file_path);
                      const extension = filePath.split('.').pop().toLowerCase();
                      if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(extension)) {
                          $("#previewBox").html('<img src="' + filePath + '" class="max-w-full max-h-full object-contain">');
                      } else if (extension === 'pdf') {
                          $("#previewBox").html('<img src="{{ asset("images/pdf.png") }}" class="h-10 w-10 object-contain">');
                      }
                  }
              } else {
                  alert("Failed to load document details.");
              }
          },
          error: function(xhr) {
              console.error("Error loading document:", xhr);
              if (xhr.status === 404) {
                  alert("Document not found.");
              } else {
                  alert("Error loading document details.");
              }
          }
      });
  }

  $(document).ready(function () {
    console.log("jQuery ready");

   // Super admin Search data 
    // Old search input handler - kept for backward compatibility but new form uses standard form submission
    $(document).on("input", "#searchInput", function () {
        let timer;
        clearTimeout(timer);

        timer = setTimeout(function () {
            const query = $("#searchInput").val();
            
            $.ajax({
                url: "{{ route('documents') }}",
                method: "GET",
                data: { search: query },
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                success: function (html) {
                    $("#searchResults").html(html);
                },
                error: function (err) {
                    console.error("Search error:", err);
                }
            });
        }, 300); // debounce delay
    });

    function fetchData() {
        let search = $("#searchInput").val();
        let sort = $("#sortFilter").val();
        let status = $("#statusFilter").val();

        $.ajax({
            url: "{{ route('career-history') }}",
            type: "GET",
            data: {
                search: search,
                sort: sort,
                status: status
            },
            headers: { "X-Requested-With": "XMLHttpRequest" },
            success: function (data) {
                $("#searchResults").html(data);
            },
            error: function () {
                console.error("Error loading data");
            }
        });
    }

    // Live search
    $("#searchInput").on("input", function () {
        fetchData();
    });

    // Filter dropdowns
    $("#sortFilter, #statusFilter").on("change", function () {
        fetchData();
    });
});
  
$(function () {
    // =======================
    // OPEN POPUP
    // =======================
    $("[data-popup-target]").on("click", function() {
        const target = $(this).data("popup-target");
        
        // If opening share document modal, enable button and clear errors
        if (target === "#shareDocumentModal") {
            $("#saveShareBtn").prop("disabled", false);
            hideShareValidationErrors();
            
            // Ensure click handler is attached when modal opens
            setTimeout(function() {
                $("#saveShareBtn").off("click").on("click", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    
                    console.log("Share button clicked!");
                    
                    // Prevent any form submission - check all parent forms
                    let parent = this.parentElement;
                    while (parent && parent !== document.body) {
                        if (parent.tagName === "FORM") {
                            $(parent).off("submit").on("submit", function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                return false;
                            });
                        }
                        parent = parent.parentElement;
                    }
                    
                    // Execute share logic
                    handleShareButtonClick();
                    return false;
                });
            }, 100);
        }
        
        $(target).removeClass("hidden").addClass("flex");
    });

    // =======================
    // CLOSE POPUP AND RESET
    // =======================
    $(".closePopup").on("click", function() {
        const popup = $(this).closest(".popup");
        
        // Special handling for share success modal
        if (popup.attr('id') === 'shareSuccessModal') {
            closeShareSuccessModal();
            return;
        }

        // Hide the popup
        popup.addClass("hidden").removeClass("flex");

        // Reset the form and clear edit mode
        const formElement = popup.find("#documentForm");
        if (formElement.length) {
            const form = formElement[0];
            if (form) form.reset();
            
            // Clear edit mode attributes
            formElement.removeAttr("data-edit-id");
            formElement.find('input[name="_method"]').remove();
            formElement.attr("action", "{{ route('career-history.store') }}");
            
            // Reset modal title
            popup.find("h2").text("Add Document");
            
            // Hide replace button
            popup.find("#replaceBtn").addClass("hidden");
        }

        // Reset the file input separately
        const fileInput = popup.find("#docFile");
        fileInput.val('');

        // Restore the preview box
        const previewBox = popup.find("#previewBox");
        previewBox.html(`
            <span id="placeholderText" class="flex flex-col items-center justify-center w-full h-full text-center">
                <svg width="30" height="25" viewBox="0 0 30 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M25.6809 20.5809C26.922 19.9043 27.9025 18.8336 28.4675 17.5378C29.0326 16.2421 29.15 14.7951 28.8014 13.4252C28.4527 12.0552 27.6577 10.8404 26.542 9.97251C25.4262 9.10457 24.0532 8.63291 22.6396 8.63198H21.0362C20.651 7.14217 19.9331 5.75906 18.9365 4.58663C17.9398 3.41421 16.6904 2.48298 15.282 1.86296C13.8737 1.24293 12.3431 0.950246 10.8053 1.0069C9.26756 1.06356 7.76266 1.46809 6.40375 2.19007C5.04485 2.91205 3.86729 3.9327 2.95962 5.17529C2.05194 6.41787 1.43777 7.85006 1.16328 9.36418C0.888783 10.8783 0.961107 12.4349 1.37481 13.9171C1.78852 15.3992 2.53284 16.7683 3.55182 17.9214" stroke="#75A2FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9.91459 18.8123L15.0047 23.9023L20.0947 18.8123" stroke="#75A2FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M15.0039 23.9023L15.0039 12.4497" stroke="#75A2FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <p class="text-lg font-medium mt-4" style="color: #0053FF;">Drag and Drop or browse your file</p>
                <p class="text-sm text-gray-400">Support JPG, PNG, PDF | Max Size 5 MB</p>
            </span>
        `);

        // Clear any dynamically generated fields
        popup.find("#dynamicFields").empty();

        // Clear all validation errors and red borders
        popup.find(".error-text").remove();
        popup.find("input, select, textarea").removeClass("border-red-600");
      
      	 // Hide the Replace button
        popup.find("#replaceBtn").addClass("hidden");
    });

    const previewBox = $("#previewBox");
    const fileInput = $("#docFile");
    const scanOverlay = $("#scanOverlay");
    const placeholderText = $("#placeholderText");
    const docType = $("#docType");
    const typeNotification = $("#typeNotification");
    const replaceBtn = $("#replaceBtn");

    // Click on preview box opens file selector
    previewBox.on("click", function () {
        fileInput.click();
    });

    // Show Replace button after successful file preview
    function showReplaceButton() {
        replaceBtn.removeClass("hidden");
    }

    // Handle file input selection
    fileInput.on("change", function () {
        if (this.files.length > 0) {
            const file = this.files[0];
            handleFile(file);
            scanDocument(file);
        }
    });

    // Drag and drop handlers
    previewBox.on("dragover", function (e) {
        e.preventDefault();
        e.stopPropagation();
        previewBox.addClass("border-blue-500 bg-blue-50");
    });

    previewBox.on("dragleave", function (e) {
        e.preventDefault();
        e.stopPropagation();
        previewBox.removeClass("border-blue-500 bg-blue-50");
    });

    previewBox.on("drop", function (e) {
        e.preventDefault();
        e.stopPropagation();
        previewBox.removeClass("border-blue-500 bg-blue-50");

        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            handleFile(file);
            scanDocument(file);

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput[0].files = dataTransfer.files;
        }
    });
  
  	
  	// Replace button click
    replaceBtn.on("click", function() {
        // Reset input and preview for new selection
        fileInput.val("");
        previewBox.find("img, embed").remove();
        $("#placeholderText").show();

        // Hide Replace button until new file selected
        replaceBtn.addClass("hidden");

        // Open file picker
        fileInput.click();
    });
  	

    // Allowed file types
    const allowedTypes = ["image/jpeg", "image/png", "image/jpg", "application/pdf"];

    // Function to handle file preview
    function handleFile(file) {
        if (!file) return;

        // Check file type
        if (!allowedTypes.includes(file.type)) {
            previewBox.html('<p class="text-red-500">Invalid file type. Only JPG, JPEG, PNG, and PDF are allowed.</p>');
            fileInput.val(""); // Reset input
            return;
        }

        previewBox.find("img, embed").remove();

        $("#previewSVG").hide();
        $("#placeholderText").hide();

        // Show preview depending on file type
        if (file.type.includes("image")) {
            const img = document.createElement("img");
            img.src = URL.createObjectURL(file);
            img.className = "max-h-full max-w-full object-contain";
            previewBox.append(img);
        } else if (file.type.includes("pdf")) {
            const embed = document.createElement("embed");
            embed.src = URL.createObjectURL(file);
            embed.type = "application/pdf";
            embed.className = "w-full h-full";
            previewBox.append(embed);
        } else {
            previewBox.text("Unsupported file type").addClass("text-red-500");
        }
    }

    // Function to scan the document using AJAX
    function scanDocument(file) {
        if (!file) return;

        // Skip if invalid file type
        if (!allowedTypes.includes(file.type)) {
            return;
        }

        let formData = new FormData();
        formData.append('docFile', file);
        formData.append('_token', $('input[name="_token"]').val());

        $("#scanOverlay").removeClass("hidden");

        $.ajax({
            url: "{{ route('documents.scan') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response && response.text) {
                    let text = response.text;
                    let matchedType = matchDocumentType(text);

                    if (matchedType) {
                        renderDynamicFields(matchedType, function() {
                            fillDocumentFields(matchedType, text);
                        });
                    } else {
                        showTypeNotification();
                    }
                }
              	// Show Replace button after scan is complete
                $("#replaceBtn").removeClass("hidden");
            },
            error: function(xhr) {
                let msg = xhr.responseJSON?.message || "OCR failed. Please try again with a clearer document.";
                $("#scanError").removeClass("hidden").text(msg);
                setTimeout(() => {
                    $("#scanError").addClass("hidden").text("");
                }, 5000);
              	 // Even if scan fails, allow user to replace
            	$("#replaceBtn").removeClass("hidden");
            },
            complete: function() {
                $("#scanOverlay").addClass("hidden");
            }
        });
    }
  
    // Show unmatched type notification
    function showTypeNotification() {
        typeNotification.removeClass("hidden");
        setTimeout(function() {
            typeNotification.addClass("hidden");
        }, 3000);
    }

    $("#closeTypeNotification").on("click", function() {
        typeNotification.addClass("hidden");
    });

    // Document type matcher
    function matchDocumentType(text) {
        const lowerText = text.toLowerCase();
        const upperText = text.toUpperCase();

        // Passport detection - multiple patterns
        const passportPatterns = [
            /passport/i,
            /पासपोर्ट/i,  // Hindi: passport
            /republic of india/i,
            /भारत गणराज्य/i,  // Hindi: Republic of India
            /passport\s*no[.:\s]*[A-Z0-9]{6,12}/i,  // "Passport No: Z0000000"
            /पासपोर्ट\s*न[.:\s]*[A-Z0-9]{6,12}/i,  // Hindi: "पासपोर्ट न: Z0000000"
            /\b[A-Z]\d{7,9}\b/,  // Standard passport: A1234567
            /\b[A-Z]{1,2}\d{6,9}\b/,  // Indian passport: Z0000000, AB1234567
            /P<[A-Z]{3}/,  // MRZ pattern: P<UTOERIKSSON
            /machine readable zone/i,
            /mrz/i,
            /date of issue/i,
            /date of expiry/i,
            /place of issue/i,
            /place of birth/i,
            /nationality/i,
            /राष्ट्रीयता/i  // Hindi: nationality
        ];

        for (let pattern of passportPatterns) {
            if (pattern.test(text)) {
                return "passport";
            }
        }

        // Visa/ID detection
        if (lowerText.includes("visa") || 
            lowerText.includes("permit") || 
            lowerText.includes("id card") ||
            lowerText.includes("identity card") ||
            lowerText.includes("driving license") ||
            lowerText.includes("schengen")) {
            return "idvisa";
        }

        // Certificate detection
        if (lowerText.includes("certificate") || 
            lowerText.includes("degree") || 
            lowerText.includes("qualification") ||
            lowerText.includes("diploma") ||
            lowerText.includes("license") ||
            lowerText.includes("certification")) {
            return "certificate";
        }

        return null;
    }

// =======================
// EXTRACT PASSPORT FIELDS
// =======================
function extractPassportNumber(text) {
    // Indian passport format: "Passport No." or "पासपोर्ट न." followed by number
    let match = text.match(/(?:passport\s*no[.:\s]*|पासपोर्ट\s*न[.:\s]*)([A-Z0-9]{6,12})/i);
    if (match) {
        console.log("Passport number found (label):", match[1]);
        return match[1].trim();
    }
    
    // Generic passport number pattern
    match = text.match(/documentnummer.*?\n([A-Z0-9]{6,9})/i);
    if (match) {
        console.log("Passport number found (generic label):", match[1]);
        return match[1].trim();
    }
    
    // MRZ format
    match = text.match(/\n([A-Z0-9]{9})[A-Z0-9]{3}[A-Z0-9]{7}/);
    if (match) {
        console.log("Passport number found (MRZ):", match[1]);
        return match[1].trim();
    }
    
    // Pattern: Z followed by numbers (Indian passport format)
    match = text.match(/\b([A-Z]\d{7,9})\b/);
    if (match) {
        console.log("Passport number found (pattern):", match[1]);
        return match[1].trim();
    }
    
    console.log("Passport number not found");
    return "";
}

// =======================
// RENDER DYNAMIC FIELDS WITH CALLBACK
// =======================
function renderDynamicFields(type, callback) {
    let fields = "";

    if (type === "passport") {
        fields = `
            <div>
                <label class="block">Passport Number</label>
                <input type="text" name="passport_number" class="w-full border p-2 rounded-md" placeholder="A1234567">
            </div>
            <div>
                <label>Date of Birth</label>
                <input type="date" name="dob" class="w-full border p-2 rounded-md">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>Issue Date</label>
                    <input type="date" name="issue_date" class="w-full border p-2 rounded-md">
                </div>
                <div>
                    <label>Expiry Date</label>
                    <input type="date" name="expiry_date" class="w-full border p-2 rounded-md">
                </div>
            </div>
            <div>
                <label>Nationality</label>
                <input type="text" name="nationality" class="w-full border p-2 rounded-md" placeholder="American">
            </div>
            <div>
                <label>Country Code</label>
                <input type="text" name="country_code" maxlength="3" class="w-full border p-2 rounded-md uppercase" placeholder="USA">
            </div>
        `;
    }

    if (type == "idvisa") {
        fields = `
            <div id="idvisaContainer" class="space-y-4">
                <div>
                    <label class="block font-medium">Document Name</label>
                    <select id="documentName" name="document_name" class="w-full border p-2 rounded-md">
                        <option value="">-- Select Document --</option>
                        <option value="Schengen visa">Schengen visa</option>
                        <option value="B1/B2 visa">B1/B2 visa</option>
                        <option value="Frontier work permit">Frontier work permit</option>
                        <option value="C1/D visa">C1/D visa</option>
                        <option value="Driving license">Driving license</option>
                        <option value="Identity card">Identity card</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium">Document Number</label>
                    <input type="text" id="documentNumber" name="document_number" class="w-full border p-2 rounded-md" placeholder="Enter document number">
                </div>
                <div>
                    <label class="block font-medium">Date of Birth</label>
                    <input type="date" id="dob" name="dob" class="w-full border p-2 rounded-md">
                </div>
                <div>
                    <label class="block font-medium">Issue Date</label>
                    <input type="date" id="issueDate" name="issue_date" class="w-full border p-2 rounded-md">
                </div>
                <div id="expiryDateWrapper" style="display:none;">
                    <label class="block font-medium">Expiry Date</label>
                    <input type="date" id="expiryDate" name="expiry_date" class="w-full border p-2 rounded-md">
                </div>
                <div>
                    <label class="block font-medium">Country</label>
                    <input type="text" id="issueCountry" name="issue_country" class="w-full border p-2 rounded-md" placeholder="e.g. United States">
                </div>
                <div id="countryCodeWrapper" style="display:none;">
                    <label class="block font-medium">Country Code <span class="text-xs text-gray-500">(ISO Alpha-3)</span></label>
                    <input type="text" id="countryCode" name="country_code" class="w-full border p-2 rounded-md uppercase" maxlength="3" placeholder="USA">
                </div>
                <div id="visaTypeWrapper" style="display:none;">
                    <label class="block font-medium">Visa Type (optional)</label>
                    <input type="text" id="visaType" name="visa_type" class="w-full border p-2 rounded-md" placeholder="e.g. Tourist, Work, Student">
                </div>
                <div>
                    <label class="block font-medium">Place of Issue (optional)</label>
                    <input type="text" id="placeOfIssue" name="place_of_issue" class="w-full border p-2 rounded-md" placeholder="e.g. New Delhi Embassy">
                </div>
            </div>
        `;
    }

    if (type === "certificate") {
        fields = `
            <div class="mb-4">
                <div class="flex justify-between items-center mb-3">
                    <label class="block font-medium mb-1">Qualifications</label>
                    <button type="button" id="addCertificateBtn" class="px-3 py-1 text-white rounded-md flex items-center gap-2 bg-[#0053FF] hover:bg-[#0041CC]">
                        <i class="fas fa-plus"></i> Add
                    </button>
                </div>
                <div id="certificateContainer" class="space-y-4">
                    <div class="certificateRow border rounded-md p-4 relative">
                        <div>
                            <label class="block font-medium mb-1">Certificate Type <span class="text-red-500">*</span></label>
                            <select name="certificateRows[0][type_id]" class="w-full border p-2 rounded-md certificate-type" required>
                                <option value="">Select Certificate Type</option>
                                @foreach($certificateTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-red-500 text-sm mt-1 error-text-certificate-type-0 hidden">The certificate type field is required.</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-2">
                            <div>
                                <label class="block font-medium mb-1">Issue Date</label>
                                <input type="date" name="certificateRows[0][issue]" class="w-full border p-2 rounded-md">
                            </div>
                            <div>
                                <label class="block font-medium mb-1">Expiry Date</label>
                                <input type="date" name="certificateRows[0][expiry]" class="w-full border p-2 rounded-md">
                            </div>
                        </div>
                        <button type="button" class="removeRow absolute top-2 right-3 px-2 py-1 text-red-600 hover:text-red-800 text-sm hidden">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block font-medium mb-1">Date of Birth</label>
                    <input type="date" name="dob" class="w-full border p-2 rounded-md">
                </div>
                <div class="mt-4">
                    <label class="block font-medium mb-1">Certificate Number</label>
                    <input type="text" name="certificate_number" class="w-full border p-2 rounded-md" placeholder="Enter Certificate Number">
                </div>
                <div class="mt-4">
                    <label class="block font-medium mb-1">Certificate Issuer <span class="text-red-500">*</span></label>
                    <select name="certificate_issuer_id" class="w-full border p-2 rounded-md certificate-issuer" required>
                        <option value="">Select Certificate Issuer</option>
                        @foreach($certificateIssuers as $issuer)
                            <option value="{{ $issuer->id }}">{{ $issuer->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-red-500 text-sm mt-1 error-text-certificate-issuer hidden">The certificate issuer field is required.</p>
                </div>
            </div>
        `;

        // After inserting fields into the DOM, add the dependent dropdown behavior
        setTimeout(function() {
            $(".certificate-type").on("change", function() {
                let typeId = $(this).val();
                let issuerSelect = $(".certificate-issuer");

                issuerSelect.html('<option value="">Loading...</option>');

                if (typeId) {
                    $.ajax({
                        url: '/certificate-type/' + typeId + '/issuers',
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            issuerSelect.empty().append('<option value="">Select Certificate Issuer</option>');
                            data.forEach(function(issuer) {
                                issuerSelect.append('<option value="' + issuer.id + '">' + issuer.name + '</option>');
                            });
                        },
                        error: function() {
                            issuerSelect.html('<option value="">Error loading issuers</option>');
                        }
                    });
                } else {
                    issuerSelect.html('<option value="">Select Certificate Issuer</option>');
                }
            });
        }, 100);
    }

    if (type === "resume") {
        fields = `
            <div>
                <label>Resume Name (Optional)</label>
                <input type="text" name="doc_name" class="w-full border p-2 rounded-md" placeholder="e.g. My CV 2025">
            </div>
            <p class="text-sm text-gray-500">Upload your resume/CV. Supported formats: PDF, DOC, DOCX, JPG, PNG</p>
        `;
    }

    if (type === "other") {
        fields = `
            <div>
                <label>Document Name</label>
                <input type="text" name="doc_name" class="w-full border p-2 rounded-md">
            </div>
            <div>
                <label>Document Number</label>
                <input type="text" name="doc_number" class="w-full border p-2 rounded-md">
            </div>
            <div>
                <label>Date of Birth</label>
                <input type="date" name="dob" class="w-full border p-2 rounded-md">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>Issue Date</label>
                    <input type="date" name="issue_date" class="w-full border p-2 rounded-md">
                </div>
                <div>
                    <label>Expiry Date</label>
                    <input type="date" name="expiry_date" class="w-full border p-2 rounded-md">
                </div>
            </div>
        `;
    }

    $("#dynamicFields").html(fields);

    // Callback after DOM insertion
    if (typeof callback === "function") callback();
}

// =======================
// FILL DOCUMENT FIELDS
// =======================
function fillDocumentFields(type, text) {

    $("#docType").val(type);

    $("input[name='dob']").val(extractDOB(text));
    if (type === "passport") {
        $("input[name='passport_number']").val(extractPassportNumber(text));
        
        $("input[name='issue_date']").val(extractIssueDate(text));
        $("input[name='expiry_date']").val(extractExpiryDate(text));
        $("input[name='nationality']").val(extractNationality(text));
        $("input[name='country_code']").val(extractCountryCode(text));
    }
    if (type === "idvisa") {
        $("select[name='document_name']").val(extractVisaName(text));
        $("input[name='document_number']").val(extractDocumentNumber(text));
         
        $("input[name='issue_date']").val(extractIssueDate(text));
        $("input[name='expiry_date']").val(extractExpiryDate(text));
        $("input[name='issue_country']").val(extractIssueCountry(text));
    }
    if (type === "certificate") {
        
        $("input[name='certificate_number']").val(extractCertificateNumber(text));
        $("select[name='certificate_issuer_id']").val(extractCertificateIssuer(text));
    }
}

// =======================
// FORMAT DATE STRING
// =======================
function formatDate(dateStr) {
    let parts = dateStr.split(/[\/\-]/);
    if (parts.length === 3) {
        return `${parts[2]}-${parts[1].padStart(2,'0')}-${parts[0].padStart(2,'0')}`;
    }
    return "";
}

function formatCustomDate(dateStr) {
    let match = dateStr.match(/(\d{2})\s?[A-Z]{3}\/?[A-Z]{3}\s?(\d{4})/i);
    if (match) {
        let day = match[1].padStart(2, '0');
        let year = match[2];
        
        let monthPart = dateStr.match(/[A-Z]{3}/i);
        let monthMap = {
            "JAN": "01", "FEB": "02", "MAR": "03",
            "APR": "04", "MAY": "05", "JUN": "06",
            "JUL": "07", "AUG": "08", "SEP": "09",
            "OCT": "10", "NOV": "11", "DEC": "12"
        };
        let month = monthPart ? monthMap[monthPart[0].toUpperCase()] || "01" : "01";
        
        return `${year}-${month}-${day}`;
    }
    return "";
}

// Helper function to format DD/MM/YYYY or DD-MM-YYYY to YYYY-MM-DD
function formatDateDDMMYYYY(dateStr) {
    if (!dateStr) return "";
    
    // Remove extra spaces and normalize separators
    dateStr = dateStr.trim().replace(/\s+/g, '');
    
    // Match DD/MM/YYYY or DD-MM-YYYY
    let match = dateStr.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/);
    if (match) {
        let day = match[1].padStart(2, '0');
        let month = match[2].padStart(2, '0');
        let year = match[3];
        
        // Validate date
        if (parseInt(day) > 0 && parseInt(day) <= 31 && 
            parseInt(month) > 0 && parseInt(month) <= 12 &&
            parseInt(year) >= 1900 && parseInt(year) <= 2100) {
            return `${year}-${month}-${day}`;
        }
    }
    return "";
}



// =======================
// EXTRACTION FUNCTIONS
// =======================

function normalizeOCRText(text) {
    return text.replace(/\r/g, "\n").replace(/\n{2,}/g, "\n").trim();
}

function tryParseDateString(dateStr) {
    const parsed = Date.parse(dateStr);
    if (!isNaN(parsed)) {
        let d = new Date(parsed);
        let mm = String(d.getMonth() + 1).padStart(2, "0");
        let dd = String(d.getDate()).padStart(2, "0");
        return `${d.getFullYear()}-${mm}-${dd}`;
    }
    return null;
}

function findDateAfterLabel(text, regex) {
    // First try DD/MM/YYYY format (Indian format)
    const ddmmMatch = text.match(new RegExp(regex.source + "[:\\s\\n]*([\\d]{1,2}[\\/\\-][\\d]{1,2}[\\/\\-][\\d]{4})", "i"));
    if (ddmmMatch) {
        let formatted = formatDateDDMMYYYY(ddmmMatch[1]);
        if (formatted) {
            console.log("Date found (DD/MM/YYYY):", formatted);
            return formatted;
        }
    }
    
    // Try other date formats
    const match = text.match(new RegExp(regex.source + "[:\\s\\n]*([\\dA-Za-z/\\-\\s]{6,20})", "i"));
    if (match) {
        // Try DD/MM/YYYY format first
        let formatted = formatDateDDMMYYYY(match[1]);
        if (formatted) return formatted;
        
        // Fallback to tryParseDateString
        return tryParseDateString(match[1]);
    }
    return null;
}

function parse6DigitMRZToISO(val) {
    if (!/^\d{6}$/.test(val)) return null;
    let year = parseInt(val.slice(0, 2), 10);
    let month = val.slice(2, 4);
    let day = val.slice(4, 6);
    year += year < 50 ? 2000 : 1900; // heuristic: passport years
    return `${year}-${month}-${day}`;
}

function extractDOB(text) {
    text = normalizeOCRText(text);

    // Indian passport format: "Date of Birth: 24/05/1985" or "जन्म तिथि: 24/05/1985"
    let match = text.match(/(?:date\s+of\s+birth|जन्म\s+तिथि|जन्मतिथि|dob|geburtsdatum|date\s+de\s+naissance|birth\s+date)[:\s]*(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4})/i);
    if (match) {
        console.log("DOB found (label):", match[1]);
        let formatted = formatDateDDMMYYYY(match[1]);
        if (formatted) return formatted;
    }

    const dobLabels = [
        /date of birth/i,
        /\bdob\b/i,
        /geburtsdatum/i,
        /date de naissance/i,
        /birth date/i,
        /जन्म\s+तिथि/i,
        /जन्मतिथि/i
    ];

    for (const lbl of dobLabels) {
        const val = findDateAfterLabel(text, lbl);
        if (val) {
            console.log("DOB found by label:", val);
            return val;
        }
    }

    // Try to find DD/MM/YYYY format dates (Indian format)
    const dateSearch = text.match(/(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4})/g);
    if (dateSearch && dateSearch.length) {
        for (const candidate of dateSearch) {
            // Check if it's a valid date in DD/MM/YYYY format
            let formatted = formatDateDDMMYYYY(candidate);
            if (formatted) {
                const y = Number(formatted.slice(0, 4));
                const curr = new Date().getFullYear();
                // DOB should be between 1900 and current year - 15
                if (y >= 1900 && y <= curr - 15) {
                    console.log("DOB found by DD/MM/YYYY format:", formatted);
                    return formatted;
                }
            }
        }
    }

    // Try other date formats
    const otherDateSearch = text.match(/(\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}|\d{1,2}\s+[A-Za-z]{3,9}\s*\d{4}|\d{6})/g);
    if (otherDateSearch && otherDateSearch.length) {
        for (const candidate of otherDateSearch) {
            const iso = tryParseDateString(candidate.replace(/\s+/g, " "));
            if (!iso) continue;
            const y = Number(iso.slice(0, 4));
            const curr = new Date().getFullYear();
            if (y >= 1900 && y <= curr - 15) {
                console.log("DOB found by candidate:", iso);
                return iso;
            }
        }
    }

    const mrzMatch = text.match(/([A-Z<]{30,})\n([A-Z0-9<]{30,})/m);
    if (mrzMatch) {
        const mrzLine2 = mrzMatch[2].replace(/\s+/g, "");
        if (mrzLine2.length >= 20) {
            const pos = mrzLine2.slice(13, 19);
            if (/^\d{6}$/.test(pos)) {
                const iso = parse6DigitMRZToISO(pos);
                if (iso) {
                    console.log("DOB found in MRZ:", iso);
                    return iso;
                }
            }
        }
    }

    console.log("DOB not found");
    return "";
}

function extractIssueDate(text) {
    // Indian passport format: "Date of Issue: 01/01/2013" or "जारी करने की तिथि: 01/01/2013"
    let match = text.match(/(?:date\s+of\s+issue|जारी\s+करने\s+की\s+तिथि|date\s+de\s+délivrance|datum\s+van\s+afgifte)[:\s]*(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4})/i);
    if (match) {
        console.log("Issue date found (label):", match[1]);
        let formatted = formatDateDDMMYYYY(match[1]);
        if (formatted) return formatted;
    }
    
    // Pattern: DD/MM/YYYY or DD-MM-YYYY after "issue" or "जारी"
    match = text.match(/(?:issue|जारी)[:\s]*(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4})/i);
    if (match) {
        console.log("Issue date found (pattern):", match[1]);
        let formatted = formatDateDDMMYYYY(match[1]);
        if (formatted) return formatted;
    }
    
    // Look for date pattern near "issue" keywords (more flexible)
    match = text.match(/(?:issue|जारी).{0,50}?(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4})/i);
    if (match) {
        console.log("Issue date found (flexible):", match[1]);
        let formatted = formatDateDDMMYYYY(match[1]);
        if (formatted) return formatted;
    }
    
    // Generic date format after "date of issue"
    match = text.match(/(?:datum\s+van\s+afgifte|date\s+of\s+issue|date\s+de\s+délivrance).*?\n\s*(\d{2}\s?[A-Z]{3}\/?[A-Z]{3}\s?\d{4})/i);
    if (match) {
        console.log("Issue date found (custom):", match[1]);
        return formatCustomDate(match[1]);
    }
    
    return "";
}


function extractExpiryDate(text) {
    // Indian passport format: "Date of Expiry: 01/01/2023" or "समाप्ति की तिथि: 01/01/2023"
    let match = text.match(/(?:date\s+of\s+expiry|समाप्ति\s+की\s+तिथि|date\s+d'expiration|geldig\s+tot)[:\s]*(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4})/i);
    if (match) {
        console.log("Expiry date found (label):", match[1]);
        let formatted = formatDateDDMMYYYY(match[1]);
        if (formatted) return formatted;
    }
    
    // Pattern: DD/MM/YYYY or DD-MM-YYYY after "expiry" or "समाप्ति"
    match = text.match(/(?:expiry|समाप्ति)[:\s]*(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4})/i);
    if (match) {
        console.log("Expiry date found (pattern):", match[1]);
        let formatted = formatDateDDMMYYYY(match[1]);
        if (formatted) return formatted;
    }
    
    // Look for date pattern near "expiry" keywords (more flexible)
    match = text.match(/(?:expiry|समाप्ति).{0,50}?(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4})/i);
    if (match) {
        console.log("Expiry date found (flexible):", match[1]);
        let formatted = formatDateDDMMYYYY(match[1]);
        if (formatted) return formatted;
    }
    
    // Generic date format after "date of expiry"
    match = text.match(/(?:geldig\s+tot|date\s+of\s+expiry|date\s+d'expiration).*?\n\s*(\d{2}\s?[A-Z]{3}\/?[A-Z]{3}\s?\d{4})/i);
    if (match) {
        console.log("Expiry date found (custom):", match[1]);
        return formatCustomDate(match[1]);
    }
    
    return "";
}

function extractNationality(text) {
    const lowerText = text.toLowerCase();
    
    // Check for Indian nationality (Hindi + English)
    if (lowerText.includes("indian") || lowerText.includes("भारतीय") || lowerText.includes("india")) {
        return "Indian";
    }
    if (lowerText.includes("american") || lowerText.includes("united states")) {
        return "American";
    }
    if (lowerText.includes("netherlands") || lowerText.includes("dutch")) {
        return "Netherlands";
    }
    if (lowerText.includes("british") || lowerText.includes("uk") || lowerText.includes("united kingdom")) {
        return "British";
    }
    if (lowerText.includes("canadian") || lowerText.includes("canada")) {
        return "Canadian";
    }
    if (lowerText.includes("australian") || lowerText.includes("australia")) {
        return "Australian";
    }
    
    return "";
}

function extractCountryCode(text) {
    const lowerText = text.toLowerCase();
    
    // Indian passport - check for IND code or India
    if (lowerText.includes("ind") || lowerText.includes("india") || lowerText.includes("भारत")) {
        return "IND";
    }
    if (lowerText.includes("usa") || lowerText.includes("united states")) {
        return "USA";
    }
    if (lowerText.includes("netherlands") || lowerText.includes("nld") || lowerText.includes("dutch")) {
        return "NLD";
    }
    if (lowerText.includes("uk") || lowerText.includes("united kingdom") || lowerText.includes("gbr")) {
        return "GBR";
    }
    if (lowerText.includes("canada") || lowerText.includes("can")) {
        return "CAN";
    }
    if (lowerText.includes("australia") || lowerText.includes("aus")) {
        return "AUS";
    }
    
    return "";
}

// =======================
// EXTRACT ID/VISA FIELDS
// =======================
function extractVisaName(text) {
    text = text.toLowerCase();
    if (text.includes("schengen")) return "Schengen visa";
    if (text.includes("b1")) return "B1/B2 visa";
    return "Other visa";
}

function extractDocumentNumber(text) {
    let match = text.match(/\b\d{6,9}\b/);
    return match ? match[0] : "";
}

function extractIssueCountry(text) {
    text = text.toLowerCase();
    if (text.includes("usa")) return "United States";
    if (text.includes("india")) return "India";
    if (text.includes("netherlands") || text.includes("nld")) return "Netherlands";
    return "";
}

// =======================
// EXTRACT CERTIFICATE FIELDS
// =======================
function extractCertificateNumber(text) {
    let match = text.match(/\b\d{5,10}\b/);
    return match ? match[0] : "";
}

function extractCertificateIssuer(text) {
    text = text.toLowerCase();
    if (text.includes("university")) return "1"; // Example issuer ID
    if (text.includes("board")) return "2";
    return "3"; // Default
}

// =======================
// MANUAL DATA INPUT HANDLING
// =======================
$("#docType").on("change", function () {
    let type = $(this).val();
    renderDynamicFields(type); // Manual data logic intact
});


document.querySelectorAll('.toggle-share').forEach(span => {
    span.addEventListener('click', function() {
        let docId = this.dataset.id;
        let img = this.querySelector('img');
        let shareText = this.nextElementSibling; // Get the sibling span for text
        let currentSrc = img.src.split('/').pop();
        let isActive = (currentSrc === 'view-icon.png') ? 0 : 1;

        $.ajax({
            url: "{{ route('documents.toggleShare') }}",
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: docId,
                is_active: isActive
            },
            success: function(response) {
                if(response.success) {
                    // Update icon and text dynamically without page reload
                    img.src = isActive
                        ? "{{ asset('images/view-icon.png') }}"
                        : "{{ asset('images/view-off-slash.png') }}";

                    shareText.textContent = isActive
                        ? "Featured on your Profile Preview"
                        : "Not featured on your Profile Preview";
                } else {
                    alert("Failed to update document!");
                }
            },
            error: function(xhr) {
                alert("Error: " + xhr.responseText);
            }
        });
    });
});



    function capitalizeFirstLetter(string) {
        if (!string) return '';
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    $(".view-document-card").on("click", function() {
        const doc = $(this).data("doc");

        if (doc.file_path) {
            const ext = doc.file_path.split('.').pop().toLowerCase();
            const imageExtensions = ['jpg','jpeg','png','gif','bmp','svg','webp','tiff','jfif','ico','heic'];

            if (imageExtensions.includes(ext)) {
                const img = $("#viewDocImage");
                img.attr("src", "/storage/" + doc.file_path).removeClass("hidden");
                img.off("error").on("error", function() {
                    $(this).addClass("hidden");
                    $("#viewNoFileText").removeClass("hidden").text("Image not available");
                });
                $("#viewDocPDF").addClass("hidden");
                $("#viewNoFileText").addClass("hidden");
                // Show zoom controls for images
                $("#zoomControls").removeClass("hidden");
                $("#zoomLevel").removeClass("hidden");
                // Reset zoom when new image loads
                resetImageZoom();
            } else if (ext === 'pdf') {
                $("#viewDocPDF").attr("src", "/storage/" + doc.file_path).removeClass("hidden");
                $("#viewDocImage").addClass("hidden");
                $("#viewNoFileText").addClass("hidden");
                // Hide zoom controls for PDFs
                $("#zoomControls").addClass("hidden");
                $("#zoomLevel").addClass("hidden");
            } else {
                $("#viewDocImage, #viewDocPDF").addClass("hidden");
                $("#viewNoFileText").removeClass("hidden").text("Unsupported file type");
                $("#zoomControls").addClass("hidden");
                $("#zoomLevel").addClass("hidden");
            }
        } else {
            $("#viewDocImage, #viewDocPDF").addClass("hidden");
            $("#viewNoFileText").removeClass("hidden").text("No File");
        }

        let docName = '-';

        if (doc.type === 'passport' || doc.type === 'idvisa') {
            docName = capitalizeFirstLetter(doc.type);
        } else if (doc.type === 'certificate') {
            docName = doc.certificates?.[0]?.type?.name || '-';
        } else if (doc.type === 'resume') {
            docName = doc.other_document?.doc_name || 'Resume';
        } else if (doc.type === 'other') {
            docName = doc.other_document?.doc_name || '-';
        }

        // --- DOB ---
        if (doc.dob) {
            $("#dobDiv").removeClass("hidden").find("#viewDOB").text(doc.dob);
        } else {
            $("#dobDiv").addClass("hidden");
        }

        $("#viewDocName").text(docName);

        // --- Status Display (Verification Status) ---
        const status = doc.status || 'pending';
        const statusColors = {
            'approved': 'text-green-600',
            'rejected': 'text-red-600',
            'pending': 'text-yellow-600'
        };
        const statusText = capitalizeFirstLetter(status);
        $("#viewDocStatus").text(statusText).removeClass('text-green-600 text-red-600 text-yellow-600').addClass(statusColors[status] || 'text-yellow-600');
        
        // Show Re-Submit button if rejected
        if (status === 'rejected') {
            $("#reSubmitButtonDiv").removeClass("hidden");
            $("#reSubmitButton").attr("onclick", "editDocument(" + doc.id + ")");
        } else {
            $("#reSubmitButtonDiv").addClass("hidden");
        }
        
        // Set edit, download, and print button actions
        $("#editDocumentButton").attr("onclick", "editDocument(" + doc.id + ")");
        if (doc.file_path) {
            const fileUrl = "/storage/" + doc.file_path;
            $("#downloadDocumentButton").attr("onclick", "window.open('" + fileUrl + "', '_blank')").removeClass("hidden");
            $("#printDocumentButton").off("click").on("click", function() { printDocument(fileUrl); }).removeClass("hidden");
        } else {
            $("#downloadDocumentButton").addClass("hidden");
            $("#printDocumentButton").addClass("hidden");
        }
        
        // Show verification notes if rejected - get from latest status change
        if (status === 'rejected') {
            // Try to get notes from status change history (if available in doc object)
            // Check both camelCase and snake_case
            const statusChanges = doc.status_changes || doc.statusChanges || [];
            const latestRejection = statusChanges.length > 0 
                ? statusChanges.find(sc => (sc.new_status === 'rejected' || sc.newStatus === 'rejected'))
                : null;
            const rejectionNotes = latestRejection ? (latestRejection.notes || latestRejection.note) : null;
            
            if (rejectionNotes) {
                $("#verificationNotesDiv").removeClass("hidden");
                $("#viewVerificationNotes").text(rejectionNotes);
            } else {
                $("#verificationNotesDiv").addClass("hidden");
            }
        } else {
            $("#verificationNotesDiv").addClass("hidden");
        }

        // --- Standard Fields ---
        doc.issue_date ? $("#issueDateDiv").removeClass("hidden").find("#viewIssueDate").text(doc.issue_date) : $("#issueDateDiv").addClass("hidden");
        doc.expiry_date ? $("#expiryDateDiv").removeClass("hidden").find("#viewExpiryDate").text(doc.expiry_date) : $("#expiryDateDiv").addClass("hidden");
        doc.nationality ? $("#nationalityDiv").removeClass("hidden").find("#viewNationality").text(doc.nationality) : $("#nationalityDiv").addClass("hidden");
        doc.country_code ? $("#countryCodeDiv").removeClass("hidden").find("#viewCountryCode").text(doc.country_code) : $("#countryCodeDiv").addClass("hidden");
        // --- DOB ---
        doc.dob ? $("#dobDiv").removeClass("hidden").find("#viewDOB").text(doc.dob) : $("#dobDiv").addClass("hidden");
        (doc.remaining_number || doc.remaining_type)
            ? $("#remainingDurationDiv").removeClass("hidden").find("#viewDocRemaining").text(doc.remaining_number ? doc.remaining_number + " " + doc.remaining_type : doc.remaining_type)
            : $("#remainingDurationDiv").addClass("hidden");

        if (doc.type === 'other' && doc.document_number) {
            $("#documentNumberDiv").removeClass("hidden").find("#viewDocumentNumber").text(doc.document_number);
        } else {
            $("#documentNumberDiv").addClass("hidden");
        }

        if (doc.type === 'certificate') {
            $("#certificateDiv").removeClass("hidden");

            $("#viewCertificateType").text(doc.certificates?.[0]?.type?.name || '-');
            $("#viewCertificateIssue").text(doc.certificates?.[0]?.issue_date || '-');
            $("#viewCertificateExpiry").text(doc.certificates?.[0]?.expiry_date || '-');
            $("#viewCertificateNumber").text(doc.certificates?.[0]?.certificate_number || '-');
            $("#viewCertificateIssuer").text(doc.certificates?.[0]?.issuer?.name || '-');
        } else {
            $("#certificateDiv").addClass("hidden");
        }


        // Show popup
        // Store document ID for History button
        window.currentViewDocId = doc.id;
        
        $("#viewDocumentModal").removeClass("hidden").addClass("flex");
    });

    // Zoom functionality for images
    let currentZoom = 1;
    const minZoom = 0.5;
    const maxZoom = 5;
    const zoomStep = 0.25;

    function resetImageZoom() {
        currentZoom = 1;
        const img = $("#viewDocImage");
        if (img.length) {
            img.css('transform', 'scale(1)');
            updateZoomLevel();
        }
    }

    function updateZoomLevel() {
        $("#zoomLevel").text(Math.round(currentZoom * 100) + '%');
    }

    function applyZoom() {
        const img = $("#viewDocImage");
        if (img.length) {
            img.css('transform', 'scale(' + currentZoom + ')');
            updateZoomLevel();
        }
    }

    // Zoom In
    $("#zoomInBtn").on("click", function(e) {
        e.stopPropagation();
        if (currentZoom < maxZoom) {
            currentZoom = Math.min(currentZoom + zoomStep, maxZoom);
            applyZoom();
        }
    });

    // Zoom Out
    $("#zoomOutBtn").on("click", function(e) {
        e.stopPropagation();
        if (currentZoom > minZoom) {
            currentZoom = Math.max(currentZoom - zoomStep, minZoom);
            applyZoom();
        }
    });

    // Reset Zoom
    $("#resetZoomBtn").on("click", function(e) {
        e.stopPropagation();
        resetImageZoom();
    });

    // Mouse wheel zoom
    $("#viewPreviewBox").on("wheel", function(e) {
        if ($("#viewDocImage").is(":visible")) {
            e.preventDefault();
            const delta = e.originalEvent.deltaY > 0 ? -zoomStep : zoomStep;
            currentZoom = Math.max(minZoom, Math.min(maxZoom, currentZoom + delta));
            applyZoom();
        }
    });

    // Double click to zoom in/out
    $("#viewDocImage").on("dblclick", function(e) {
        e.preventDefault();
        if (currentZoom === 1) {
            currentZoom = 2;
        } else {
            currentZoom = 1;
        }
        applyZoom();
    });

    // Print document function
    function printDocument(fileUrl) {
        const printWindow = window.open(fileUrl, '_blank');
        if (printWindow) {
            printWindow.onload = function() {
                printWindow.print();
            };
        } else {
            alert('Please allow popups to print documents');
        }
    }


    $(".closePopup").on("click", function() {
        const popup = $(this).closest(".popup");
        popup.addClass("hidden").removeClass("flex");

        $("#viewDocImage, #viewDocPDF").addClass("hidden").attr("src", "");
        $("#viewNoFileText").removeClass("hidden").text("No File");

        $("#viewDocName, #viewDocStatus, #viewIssueDate, #viewExpiryDate, #viewNationality, #viewCountryCode, #viewDocRemaining, #viewDocumentNumber, #viewCertificateType, #viewCertificateIssue, #viewCertificateExpiry, #viewCertificateNumber, #viewCertificateIssuer, #viewVerificationNotes").text('');
        $("#issueDateDiv, #expiryDateDiv, #nationalityDiv, #countryCodeDiv, #remainingDurationDiv, #documentNumberDiv, #certificateDiv, #verificationNotesDiv, #reSubmitButtonDiv").addClass("hidden");
        $("#editDocumentButton").attr("onclick", "editDocument(0)");
        $("#reSubmitButton").attr("onclick", "editDocument(0)");
        $("#downloadDocumentButton").attr("onclick", "").removeClass("hidden");
        $("#printDocumentButton").attr("onclick", "").removeClass("hidden");
        $("#zoomControls").addClass("hidden");
        $("#zoomLevel").addClass("hidden");
        resetImageZoom();
    });


    let docIdToDelete = null;

    // Open Delete Confirmation
    $(".delete-document-btn").on("click", function() {
        docIdToDelete = $(this).data("id");
        if (!docIdToDelete) return alert("Document ID is missing.");
        $("#deleteConfirmModal").removeClass("hidden");
    });

    // Cancel Delete
    $("#cancelDeleteBtn").on("click", function() {
        docIdToDelete = null;
        $("#deleteConfirmModal").addClass("hidden");
    });

    // Confirm Delete
    $("#confirmDeleteBtn").on("click", function() {
        if(!docIdToDelete) return;

        $.ajax({
            url: '/documents/' + docIdToDelete, // Laravel DELETE route
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $("#deleteConfirmModal").addClass("hidden");

                if(response.success){
                    $("#deleteSuccessModal").removeClass("hidden");
                    // Optionally remove the document row from UI
                    // $('#document-row-' + docIdToDelete).remove();
                } else {
                    alert('Failed to delete document: ' + response.message);
                }
            },
            error: function(xhr) {
                $("#deleteConfirmModal").addClass("hidden");
                alert('Error deleting document.');
            }
        });
    });

    // Close Success Modal
    $("#closeSuccessBtn").on("click", function() {
        $("#deleteSuccessModal").addClass("hidden");
        location.reload(); // reload page or update UI dynamically
    });





    let rowIndex = 0; // initialize to 0, first row

    $(document).on("click", "#addCertificateBtn", function () {
        rowIndex++;

        let newRow = $(".certificateRow:first").clone();

        newRow.find("select, input").each(function () {
            let name = $(this).attr("name");
            if (name) {
                // Replace the index inside brackets with new rowIndex
                let newName = name.replace(/\[\d+\]/, `[${rowIndex}]`);
                $(this).attr("name", newName);
            }
            $(this).val(""); // clear values
        });

        newRow.find(".removeRow").removeClass("hidden");
        
        // Update error text class name for new row
        newRow.find('.error-text-certificate-type-0').removeClass('error-text-certificate-type-0').addClass(`error-text-certificate-type-${rowIndex}`);
        
        $("#certificateContainer").append(newRow);
    });

    // Remove row
    $(document).on("click", ".removeRow", function () {
        $(this).closest(".certificateRow").remove();
    });

    // Ajax submit
        $("#documentForm").on("submit", function (e) {
        e.preventDefault();

        // Client-side validation for certificate type
        let docType = $("#docType").val();
        if (docType === "certificate") {
            let hasValidType = false;
            $('select[name^="certificateRows["][name$="[type_id]"]').each(function() {
                if ($(this).val()) {
                    hasValidType = true;
                    return false; // break loop
                }
            });
            if (!hasValidType) {
                alert("Please select at least one Certificate Type.");
                $('select[name^="certificateRows["][name$="[type_id]"]').first().focus().addClass("border-red-600");
                return false;
            }
        }

        let formData = new FormData(this);
        
        // Only append file if a file is actually selected
        const fileInput = $("#docFile")[0];
        if (fileInput && fileInput.files && fileInput.files[0]) {
            formData.append("file", fileInput.files[0]);
        }

        // Clear ALL previous error messages and borders before validating again
        $(".error-text").remove();
        $("input, select, textarea").removeClass("border-red-600");
        $(".error-text-certificate-type-0").addClass("hidden");

        // Check if form is in edit mode
        const form = $(this);
        const editId = form.attr("data-edit-id");
        let url, method;
        
        if (editId) {
            // Update existing document - use POST with _method=PUT for Laravel method spoofing
            url = "/career-history/" + editId;
            method = "POST";
            // Ensure _method is set for Laravel method spoofing
            // The form should already have it, but we'll ensure it's in FormData
            formData.set("_method", "PUT");
        } else {
            // Create new document
            url = "{{ route('career-history.store') }}";
            method = "POST";
            // Remove _method if it exists (shouldn't be there for new documents)
            formData.delete("_method");
        }

        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                // Reset form
                const form = $("#documentForm");
                form[0].reset();
                form.removeAttr("data-edit-id");
                form.find('input[name="_method"]').remove();
                form.attr("action", "{{ route('career-history.store') }}");
                
                // Reset modal title
                $("#addDocumentModal h2").text("Add Document");
                
                // Hide replace button
                $("#replaceBtn").addClass("hidden");
                
                $("#previewBox").html("No file selected");
                $("#dynamicFields").empty();

                // Hide Add Document Modal
                $("#addDocumentModal").addClass("hidden").removeClass("flex");

                // Show Success Popup
                let popup = $("#successPopup");
                let content = $("#successContent");
                popup.removeClass("hidden");

                setTimeout(() => {
                    content.removeClass("scale-90 opacity-0").addClass("scale-100 opacity-100");
                }, 10);

                // Auto-hide after 4 seconds
                setTimeout(() => {
                    content.removeClass("scale-100 opacity-100").addClass("scale-90 opacity-0");
                    setTimeout(() => popup.addClass("hidden"), 300);
                    location.reload();
                }, 3000);

                // Continue button
                $("#continueBtn").off("click").on("click", function() {
                    content.removeClass("scale-100 opacity-100").addClass("scale-90 opacity-0");
                    setTimeout(() => popup.addClass("hidden"), 300);
                    location.reload();
                });
            },
            error: function (xhr) {
                let errors = xhr.responseJSON?.errors;
                if (errors) {
                    // Loop through validation errors
                    Object.keys(errors).forEach(function (field) {
                        let messages = errors[field];

                        // Convert dot notation for dynamic fields
                        let inputName = field.replace(/\.(\d+)\./g, '[$1][')
                                            .replace(/\.(\d+)$/, '[$1]')
                                            .replace(/\./g, '][');
                        inputName = inputName.startsWith('[') ? 'certificateRows' + inputName : inputName;

                        let inputField = $(`[name="${inputName}"]`);
                        if (inputField.length) {
                            // Add only once
                            if (!inputField.next(".error-text").length) {
                                inputField.after(`<p class="error-text text-red-600 text-sm mt-1">${messages[0]}</p>`);
                            }
                            inputField.addClass("border-red-600");
                        } else if (field.startsWith('certificateRows.') && field.includes('.type_id')) {
                            // Handle certificateRows type_id errors
                            let match = field.match(/certificateRows\.(\d+)\.type_id/);
                            if (match) {
                                let index = match[1];
                                let typeField = $(`select[name="certificateRows[${index}][type_id]"]`);
                                if (typeField.length) {
                                    typeField.addClass("border-red-600");
                                    let errorText = typeField.siblings('.error-text-certificate-type-' + index);
                                    if (errorText.length) {
                                        errorText.removeClass('hidden').text(messages[0]);
                                    } else {
                                        typeField.after(`<p class="error-text text-red-600 text-sm mt-1 error-text-certificate-type-${index}">${messages[0]}</p>`);
                                    }
                                }
                            }
                        } else if (field === 'certificate_issuer_id') {
                            // Handle certificate_issuer_id error specifically
                            let issuerField = $('select[name="certificate_issuer_id"]');
                            if (issuerField.length) {
                                issuerField.addClass("border-red-600");
                                $('.error-text-certificate-issuer').removeClass('hidden').text(messages[0]);
                            }
                        } else if (field === 'file') {
                            let fileField = $("#docFile");
                            if (!fileField.next(".error-text").length) {
                                fileField.after(`<p class="error-text text-red-600 text-sm mt-1">${messages[0]}</p>`);
                            }
                            fileField.addClass("border-red-600");
                        }
                    });
                } else {
                    alert("Something went wrong.");
                }
            }
        });
    });

    // Show/hide Expiry, Country Code, Visa Type based on Document Name
    $(document).on("change", "#documentName", function() {
        let docName = $(this).val();
        let visaDocs = ["Schengen visa","B1/B2 visa","Frontier work permit","C1/D visa"];
        let allDocs = ["Schengen visa","B1/B2 visa","Frontier work permit","C1/D visa","Driving license","Identity card"];

        // Expiry Date: show for all documents
        if (allDocs.includes(docName)) {
            $("#expiryDateWrapper").show();
        } else {
            $("#expiryDateWrapper").hide();
        }

        // Country Code & Visa Type: only for visa/permit
        if (visaDocs.includes(docName)) {
            $("#countryCodeWrapper").show();
            $("#visaTypeWrapper").show();
        } else {
            $("#countryCodeWrapper").hide();
            $("#visaTypeWrapper").hide();
        }
    });

    // share document start

    // Multiple Recipient Emails Handler for Share Documents
    const recipientEmailInput = $("#recipientEmail");
    const recipientEmailTags = $("#recipientEmailTags");
    const recipientEmailError = $("#recipientEmailError");
    
    let recipientEmails = [];
    const maxRecipientEmails = 10;
    
    // Function to validate email
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Render recipient email tags
    function renderRecipientEmailTags() {
        recipientEmailTags.empty();
        recipientEmails.forEach((email, index) => {
            const tag = $(`
                <span class="bg-blue-500 text-white px-2 py-1 rounded text-sm flex items-center gap-1">
                    ${email} <span class="cursor-pointer hover:text-red-200 remove-recipient-email" data-index="${index}">&times;</span>
                </span>
            `);
            recipientEmailTags.append(tag);
        });
        recipientEmailError.addClass("hidden");
    }
    
    // Remove recipient email
    $(document).on("click", ".remove-recipient-email", function() {
        const index = $(this).data("index");
        recipientEmails.splice(index, 1);
        renderRecipientEmailTags();
    });
    
    // Process recipient email input
    function processRecipientEmailInput() {
        let value = recipientEmailInput.val().trim();
        if (!value) return;
        
        const splitEmails = value.split(/[\s,]+/);
        let errorMessage = "";
        
        splitEmails.forEach(e => {
            const email = e.trim();
            if (email && isValidEmail(email)) {
                if (recipientEmails.length < maxRecipientEmails) {
                    if (!recipientEmails.includes(email.toLowerCase())) {
                        recipientEmails.push(email.toLowerCase());
                    } else {
                        errorMessage = `Email already added: ${email}`;
                    }
                } else {
                    errorMessage = `Maximum ${maxRecipientEmails} emails allowed.`;
                }
            } else if (email) {
                errorMessage = `Invalid email format: ${email}`;
            }
        });
        
        if (errorMessage) {
            recipientEmailError.text(errorMessage).removeClass("hidden");
        } else {
            recipientEmailError.addClass("hidden");
        }
        
        recipientEmailInput.val('');
        renderRecipientEmailTags();
    }
    
    // Add email on Enter, comma, or space
    recipientEmailInput.on("keydown", function(e) {
        if (["Enter", ",", " "].includes(e.key)) {
            e.preventDefault();
            processRecipientEmailInput();
        }
    });
    
    // Process on blur
    recipientEmailInput.on("blur", function() {
        processRecipientEmailInput();
    });

    const emailInput = $("#emailInput");
const emailTags = $("#emailTags");
const hiddenEmails = $("#hiddenEmails");
const emailError = $("#emailError");

let emails = [];
const minEmails = 1;
const maxEmails = 15;

// Enable share button by default
$("#saveShareBtn").prop("disabled", false);

// Render email tags
function renderEmails() {
    emailTags.empty();
    emails.forEach((email, index) => {
        const span = $(`
            <span class="bg-blue-500 text-white px-2 py-1 rounded flex items-center gap-1">
                ${email} <span class="cursor-pointer remove-email">&times;</span>
            </span>
        `);
        span.find(".remove-email").click(() => {
            emails.splice(index, 1);
            renderEmails();
            checkShareBtn();
        });
        emailTags.append(span);
    });
    hiddenEmails.val(emails.join(","));
    hideError();
    checkShareBtn();
}

// Validate single email
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Show error
function showError(message) {
    emailError.text(message).removeClass("hidden");
}

// Hide error
function hideError() {
    emailError.text("").addClass("hidden");
}

// Process input and add emails
function processEmailInput() {
    let value = emailInput.val().trim();
    if (!value) return;
    const splitEmails = value.split(/[\s,]+/);
    let errorMessage = "";

    splitEmails.forEach(e => {
        const email = e.trim();
        if (email && isValidEmail(email)) {
            if (emails.length < maxEmails) {
                if (!emails.includes(email)) {
                    emails.push(email);
                } else {
                    errorMessage = `Email already added: ${email}`;
                }
            } else {
                errorMessage = `You can add maximum ${maxEmails} emails.`;
            }
        } else if (email) {
            errorMessage = `Invalid email: ${email}`;
        }
    });

    if (errorMessage) showError(errorMessage);
    else hideError();

    emailInput.val('');
    renderEmails();
}

// Add email on Enter, comma, or space
emailInput.on("keydown", function(e) {
    if (["Enter", ",", " "].includes(e.key)) {
        e.preventDefault();
        processEmailInput();
    }
});

// Process emails on blur
emailInput.on("blur", processEmailInput);

// Handle paste
emailInput.on("paste", function(e) {
    e.preventDefault();
    const paste = (e.originalEvent || e).clipboardData.getData('text');
    emailInput.val(paste);
    processEmailInput();
});

// Check Share button - Always enabled (validation happens on submit)
function checkShareBtn() {
    // Button is always enabled to allow validation on click
    // Validation will be shown when user clicks the button
    $("#saveShareBtn").prop("disabled", false);
}

// Show validation errors
function showShareValidationErrors(errors) {
    const errorContainer = $("#shareValidationErrors");
    const errorList = $("#shareErrorList");
    
    if (errors.length > 0) {
        errorList.empty();
        errors.forEach(error => {
            errorList.append(`<li>${error}</li>`);
        });
        errorContainer.removeClass("hidden");
        
        // Scroll to top of modal to show errors
        errorContainer[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
        errorContainer.addClass("hidden");
    }
}

// Hide all validation errors
function hideShareValidationErrors() {
    $("#shareValidationErrors").addClass("hidden");
    $("#emailError").addClass("hidden");
    $("#messageError").addClass("hidden");
    $("#documentsError").addClass("hidden");
    
    // Remove error borders
    $("#emailInput").removeClass("border-red-500");
    $("#messageInput").removeClass("border-red-500");
}

// Update check on doc/message with error clearing
$(".docCard").on("click", function() {
    $(this).toggleClass("selected bg-blue-100 border-blue-500");
    checkShareBtn();
    // Clear documents error when at least one is selected
    if ($(".docCard.selected").length > 0) {
        $("#documentsError").addClass("hidden");
    }
});

$("#messageInput").on("input", function() {
    checkShareBtn();
    // Clear message error when user starts typing
    if ($(this).val().trim()) {
        $("#messageError").addClass("hidden");
        $(this).removeClass("border-red-500");
    }
});

// Clear email error when user starts typing
$("#emailInput").on("input", function() {
    checkShareBtn();
    if ($(this).val().trim()) {
        $("#emailError").addClass("hidden");
        $(this).removeClass("border-red-500");
    }
});

// Toggle password input visibility
$("#enablePassword").on("change", function() {
    if ($(this).is(":checked")) {
        $("#sharePassword").removeClass("hidden").prop("required", true);
    } else {
        $("#sharePassword").addClass("hidden").prop("required", false).val("");
    }
});

    // Toggle email restriction input visibility
    $("#enableEmailRestriction").on("change", function() {
        if ($(this).is(":checked")) {
            $("#restrictToEmail").removeClass("hidden").prop("required", true);
            $("#emailRestrictionHint").removeClass("hidden");
        } else {
            $("#restrictToEmail").addClass("hidden").prop("required", false).val("");
            $("#emailRestrictionHint").addClass("hidden");
        }
    });
    
    // Reset recipient emails when modal closes
    $(document).on("click", ".closePopup", function() {
        if ($("#shareDocumentModal").hasClass("hidden")) {
            recipientEmails = [];
            renderRecipientEmailTags();
            recipientEmailInput.val('');
        }
    });

// Store share data globally
let currentShareData = null;
let currentShareId = null; // Store share ID for revoke functionality

// Copy share URL function - Make sure it's in global scope
window.copyShareUrl = function(event) {
    event = event || window.event;
    const urlInput = document.getElementById("shareUrlDisplay");
    if (!urlInput) {
        console.error('Share URL input not found');
        return;
    }
    urlInput.select();
    urlInput.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand("copy");
        
        // Show feedback
        const copyBtn = event ? event.target.closest('button') : document.querySelector('button[onclick*="copyShareUrl"]');
        if (copyBtn) {
            const originalText = copyBtn.innerHTML;
            copyBtn.innerHTML = '<i class="fas fa-check mr-1"></i> Copied!';
            copyBtn.classList.add('bg-green-600', 'hover:bg-green-700');
            copyBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            
            setTimeout(function() {
                copyBtn.innerHTML = originalText;
                copyBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                copyBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
            }, 2000);
        }
    } catch (err) {
        console.error('Failed to copy:', err);
        alert('Failed to copy. Please select and copy manually.');
    }
}

// Download QR code function - Make sure it's in global scope
window.downloadQRCode = function() {
    const qrImage = document.getElementById("qrCodeImage");
    if (!qrImage || !qrImage.src) {
        alert('QR code not available');
        return;
    }
    const link = document.createElement("a");
    link.href = qrImage.src;
    link.download = "share-qr-code.png";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Close share success modal function - Make sure it's in global scope
window.closeShareSuccessModal = function() {
    $("#shareSuccessModal").addClass("hidden").removeClass("flex");
    // Reset form
    if (typeof resetShareForm === 'function') {
        resetShareForm();
    }
    // Reset share data
    currentShareData = null;
    currentShareId = null;
}

// Revoke share access function - Make sure it's in global scope
window.revokeShareAccess = function() {
    if (!currentShareId) {
        alert('Share ID not found. Cannot revoke access.');
        return;
    }
    
    if (!confirm('Are you sure you want to revoke access to this share link? Once revoked, the link will no longer be accessible.')) {
        return;
    }
    
    const revokeBtn = $("#revokeShareBtn");
    const originalText = revokeBtn.html();
    revokeBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Revoking...');
    
    $.ajax({
        url: `/shares/documents/${currentShareId}`,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        success: function(response) {
            if (response && response.success) {
                // Show success message
                alert('Share access revoked successfully. The link is no longer accessible.');
                
                // Update UI - disable visit link button
                $("#visitShareLink").addClass('opacity-50 cursor-not-allowed').prop('onclick', 'return false;');
                revokeBtn.html('<i class="fas fa-check mr-1"></i> Revoked').addClass('bg-gray-500 hover:bg-gray-600').removeClass('bg-red-600 hover:bg-red-700');
                
                // Update info message
                $(".bg-blue-50").html('<p class="text-sm text-red-700"><i class="fas fa-ban mr-1"></i><strong>Access Revoked:</strong> This share link has been revoked and is no longer accessible.</p>').removeClass('bg-blue-50 border-blue-400').addClass('bg-red-50 border-red-400');
            } else {
                alert('Failed to revoke access: ' + (response?.message || 'Unknown error'));
                revokeBtn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr) {
            const errorMsg = xhr.responseJSON?.message || xhr.responseText || 'Failed to revoke access. Please try again.';
            alert('Error: ' + errorMsg);
            revokeBtn.prop('disabled', false).html(originalText);
        }
    });
}

// Handle share button click - COMPLETELY prevent any form submission
// Remove ALL existing handlers first
$("#saveShareBtn").off("click submit");
$(document).off("click submit", "#saveShareBtn");

// Also prevent any form submission on the div container
$("#shareDocumentForm").off("submit");

// Attach handler using jQuery with proper event delegation
// Ensure this runs after DOM is ready
$(document).ready(function() {
    // Remove any existing handlers
    $("#saveShareBtn").off("click");
    $(document).off("click", "#saveShareBtn");
    
    // Attach new handler
    $(document).on("click", "#saveShareBtn", function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        console.log("Share button clicked!");
        
        // Prevent any form submission - check all parent forms
        let parent = this.parentElement;
        while (parent && parent !== document.body) {
            if (parent.tagName === "FORM") {
                $(parent).off("submit").on("submit", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                });
            }
            parent = parent.parentElement;
        }
        
        // Execute share logic
        handleShareButtonClick();
        return false;
    });
});

// Separate function for share logic
function handleShareButtonClick() {
    // Hide previous errors and success message
    hideShareValidationErrors();
    $("#shareSuccessMessage").addClass("hidden");

    const validationErrors = [];

    // Validate documents
    const selectedDocs = $(".docCard.selected");
    if (selectedDocs.length === 0) {
        validationErrors.push("Please select at least one document.");
        $("#documentsError").text("Please select at least one document.").removeClass("hidden");
    }

    // Validate password if enabled
    if ($("#enablePassword").is(":checked")) {
        const password = $("#sharePassword").val();
        if (!password || password.length < 6) {
            validationErrors.push("Password must be at least 6 characters.");
        }
    }

    // Validate email restriction if enabled
    if ($("#enableEmailRestriction").is(":checked")) {
        const restrictEmail = $("#restrictToEmail").val();
        if (!restrictEmail || !isValidEmail(restrictEmail)) {
            validationErrors.push("Please enter a valid email address for restriction.");
        }
    }

    if (validationErrors.length > 0) {
        showShareValidationErrors(validationErrors);
        return;
    }

    // Prepare form data for new API
    const documentIds = [];
    selectedDocs.each(function() {
        documentIds.push($(this).find(".docCheckbox").val());
    });

    // Get form values
    // Process any remaining email input
    processRecipientEmailInput();
    const recipientEmailsArray = recipientEmails.length > 0 ? recipientEmails : null;
    const personalMessage = $("#messageInput").val()?.trim() || null;
    
    console.log('Form values:', {
        recipientEmails: recipientEmailsArray,
        personalMessage: personalMessage,
        expiryOption: $("#expiryOption").val(),
        generateQrCode: $("#generateQrCode").is(":checked")
    });
    
    const requestData = {
        document_ids: documentIds,
        recipient_emails: recipientEmailsArray, // Changed to support multiple emails
        personal_message: personalMessage,
        expiry_option: $("#expiryOption").val(),
        generate_qr_code: $("#generateQrCode").is(":checked"),
    };

    // Add password if enabled
    if ($("#enablePassword").is(":checked")) {
        requestData.password = $("#sharePassword").val();
    }

    // Add email restriction if enabled
    if ($("#enableEmailRestriction").is(":checked")) {
        requestData.restrict_to_email = $("#restrictToEmail").val();
    }

    // Show processing
    $("#shareBtnText").text("Creating Share Link...");
    $("#shareBtnSpinner").removeClass("hidden");
    $("#saveShareBtn").prop("disabled", true);

    // Ensure we're using the correct route - use absolute URL
    const shareUrl = "{{ route('shares.documents.store') }}";
    console.log('Sharing documents to:', shareUrl);
    console.log('Request data:', requestData);
    
    // CRITICAL: Verify route is correct before making request
    if (!shareUrl || (shareUrl.includes('/documents') && !shareUrl.includes('/shares/documents'))) {
        console.error('ERROR: Wrong route! Expected /shares/documents but got:', shareUrl);
        alert('Configuration error: Invalid route. Please contact administrator.');
        $("#shareBtnText").text("Create Share Link");
        $("#shareBtnSpinner").addClass("hidden");
        $("#saveShareBtn").prop("disabled", false);
        return false;
    }
    
    $.ajax({
        url: shareUrl,
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        data: JSON.stringify(requestData),
        dataType: 'json',
        success: function(response) {
            console.log('AJAX Success Response:', response);
            console.log('QR Code Generated:', response.qr_code_generated);
            console.log('QR Code URL:', response.qr_code_url);
            
            if (response && response.success) {
                // Store share data and ID for revoke functionality
                currentShareData = response.share;
                currentShareId = response.share?.id || null;
                
                // Close the share document modal
                $("#shareDocumentModal").addClass("hidden").removeClass("flex");
                
                // Populate success modal
                if (response.share_url) {
                    $("#shareUrlDisplay").val(response.share_url);
                    $("#visitShareLink").attr("href", response.share_url);
                }
                
                // Show QR code if generated
                if (response.qr_code_url) {
                    console.log('Displaying QR code:', response.qr_code_url);
                    $("#qrCodeImage").attr("src", response.qr_code_url);
                    $("#qrCodeDisplay").removeClass("hidden");
                } else {
                    console.log('No QR code URL in response');
                    $("#qrCodeDisplay").addClass("hidden");
                    
                    // Check if QR code was requested but not generated
                    const wasRequested = $("#generateQrCode").is(":checked");
                    if (wasRequested && !response.qr_code_generated) {
                        console.warn('QR code was requested but not generated');
                    }
                }
                
                // Reset the form
                resetShareForm();
                
                // Show success modal
                $("#shareSuccessModal").removeClass("hidden").addClass("flex");
                
                // Reload active shares list after successful share creation
                setTimeout(function() {
                    if (typeof loadActiveShares === 'function') {
                        loadActiveShares();
                    }
                }, 500);
            } else {
                const errorMsg = response?.message || "Failed to create share. Unknown error.";
                console.error('Share creation failed:', errorMsg);
                alert("Failed to create share: " + errorMsg);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', {
                status: status,
                error: error,
                responseText: xhr.responseText,
                responseJSON: xhr.responseJSON,
                statusCode: xhr.status
            });
            
            const errors = xhr.responseJSON?.errors || {};
            const serverErrors = [];

            if (errors.document_ids) {
                $("#documentsError").text(Array.isArray(errors.document_ids) ? errors.document_ids[0] : errors.document_ids).removeClass("hidden");
                serverErrors.push(Array.isArray(errors.document_ids) ? errors.document_ids[0] : errors.document_ids);
            }

            if (errors.password) {
                serverErrors.push(Array.isArray(errors.password) ? errors.password[0] : errors.password);
            }

            if (errors.restrict_to_email) {
                serverErrors.push(Array.isArray(errors.restrict_to_email) ? errors.restrict_to_email[0] : errors.restrict_to_email);
            }

            if (serverErrors.length > 0) {
                showShareValidationErrors(serverErrors);
            } else {
                const errorMsg = xhr.responseJSON?.message || xhr.responseText || "Failed to create share link. Please try again.";
                console.error('Final error message:', errorMsg);
                alert(errorMsg);
            }
        },
        complete: function() {
            console.log('AJAX request completed');
            $("#shareBtnText").text("Create Share Link");
            $("#shareBtnSpinner").addClass("hidden");
            $("#saveShareBtn").prop("disabled", false);
        }
    });
}

// Reset form
function resetForm() {
    emails = [];
    renderEmails();
    $("#emailInput").val('');
    $("#messageInput").val('');
    $("#recipientEmail").val('');
    $("#sharePassword").val('').addClass("hidden").prop("required", false);
    $("#restrictToEmail").val('').addClass("hidden").prop("required", false);
    $("#enablePassword").prop("checked", false);
    $("#enableEmailRestriction").prop("checked", false);
    $("#generateQrCode").prop("checked", false);
    $("#expiryOption").val("30");
    $("#emailRestrictionHint").addClass("hidden");
    $("#docList .docCard").removeClass("selected bg-blue-100 border-blue-500");
    hideShareValidationErrors();
    currentShareData = null;
}

// Reset share form (alias for consistency)
function resetShareForm() {
    resetForm();
}

// ✅ Show modal popup (close current + show result)
function showResultPopup(title, message, type) {
    // Close the share modal first
    $("#shareDocumentModal").addClass("hidden");

    // Reset form UI (just to be safe)
    resetForm();

    // Update popup text
    $("#emailResultTitle")
        .text(title)
        .removeClass("text-green-600 text-red-600")
        .addClass(type === "success" ? "text-green-600" : "text-red-600");
    $("#emailResultMessage").text(message);

    const popup = $("#emailResultPopup");
    const content = $("#emailResultContent");

    // Show popup with animation
    popup.removeClass("hidden");
    setTimeout(() => content.removeClass("scale-90 opacity-0").addClass("scale-100 opacity-100"), 10);

    // Close on button click
    $("#emailResultCloseBtn").off("click").on("click", function() {
        content.removeClass("scale-100 opacity-100").addClass("scale-90 opacity-0");
        setTimeout(() => popup.addClass("hidden"), 300);
    });

    // Auto close after 3s
    setTimeout(() => {
        content.removeClass("scale-100 opacity-100").addClass("scale-90 opacity-0");
        setTimeout(() => popup.addClass("hidden"), 300);
    }, 3000);
}

// Template Selection Handler
$(document).ready(function() {
    $('#shareTemplateSelect').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const templateId = $(this).val();
        
        if (templateId) {
            const expiry = selectedOption.data('expiry');
            const message = selectedOption.data('message');
            
            // Show preview
            $('#templatePreview').removeClass('hidden');
            $('#templateExpiry').text(expiry);
            $('#templateMessage').text(message || 'No default message');
            
            // Auto-fill message if empty
            if (message && !$('#messageInput').val().trim()) {
                $('#messageInput').val(message);
            }
        } else {
            $('#templatePreview').addClass('hidden');
        }
    });
});

function clearTemplate() {
    $('#shareTemplateSelect').val('');
    $('#templatePreview').addClass('hidden');
}


    // Continue button closes popup
    $("#continueBtn").on("click", function() {
        $("#successPopup").addClass("hidden");
        $("#successContent").removeClass("scale-100 opacity-100");
    });

    // Initial check
    checkShareBtn();

    // $("#saveShareBtn").on("click", function(){
    //     alert("Documents shared successfully!");
    //     $(this).closest(".popup").addClass("hidden").removeClass("flex");
    // });

    // Share profile - Only initialize if elements exist
    const card = document.getElementById('shareProfileCard');
    const modal = document.getElementById('shareProfileModal');
    
    if (card && modal) {
        const img = document.getElementById('qrcodeImage');
        const profileLink = document.getElementById('profileLink');
        const visitProfileBtn = document.getElementById('visitProfileBtn');
        const loader = document.getElementById('loader');
        const closeButtons = modal.querySelectorAll('.closePopup');

        // Values from database
        const qrCodePath = "{{ asset(auth()->user()->qrcode ?? '') }}";
        const profileUrlDB = "{{ auth()->user()->profile_url ?? '' }}";

        // Open Modal - Create new profile share
        card.addEventListener('click', async () => {
            if (modal) modal.classList.remove('hidden');
            if (loader) loader.classList.remove('hidden');

            if (img) img.style.opacity = '0';

            // Safety timeout to hide loader after 10 seconds max
            const loaderTimeout = setTimeout(() => {
                if (loader) loader.classList.add('hidden');
            }, 10000);

            try {
                // Create new profile share
                const response = await fetch('{{ route("shares.profile.store") }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        shared_sections: ['personal_info', 'documents', 'career_history'],
                        generate_qr_code: true,
                        expires_at: null // No expiry by default
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.share_url) {
                        // Set Profile URL
                        if (profileLink) profileLink.value = data.share_url;
                        if (visitProfileBtn) visitProfileBtn.href = data.share_url;

                        // Load QR Code
                        if (data.qr_code_url && img) {
                            img.src = data.qr_code_url;
                            img.onload = () => {
                                clearTimeout(loaderTimeout);
                                if (loader) loader.classList.add('hidden');
                                if (img) img.style.opacity = '1';
                            };
                            img.onerror = () => {
                                clearTimeout(loaderTimeout);
                                if (loader) loader.classList.add('hidden');
                                console.error('Failed to load QR code image');
                                if (img) {
                                    img.alt = 'QR Code not available';
                                }
                            };
                        } else {
                            // Generate QR code if not provided
                            if (data.share && data.share.id) {
                                try {
                                    const qrResponse = await fetch(`/shares/profile/${data.share.id}/qr-code`, {
                                        method: 'POST',
                                        headers: {
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                                        },
                                        credentials: 'same-origin'
                                    });
                                    
                                    if (qrResponse.ok) {
                                        const qrData = await qrResponse.json();
                                        if (qrData.success && qrData.qr_code_url && img) {
                                            img.src = qrData.qr_code_url;
                                            img.onload = () => {
                                                clearTimeout(loaderTimeout);
                                                if (loader) loader.classList.add('hidden');
                                                if (img) img.style.opacity = '1';
                                            };
                                        }
                                    }
                                } catch (qrError) {
                                    console.error('Error generating QR code:', qrError);
                                }
                            }
                            clearTimeout(loaderTimeout);
                            if (loader) loader.classList.add('hidden');
                        }
                    } else {
                        throw new Error(data.message || 'Failed to create profile share');
                    }
                } else {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to create profile share');
                }
            } catch (error) {
                console.error('Error creating profile share:', error);
                clearTimeout(loaderTimeout);
                if (loader) loader.classList.add('hidden');
                alert('Failed to create profile share: ' + error.message);
                // Fallback to old method
                if (profileLink && profileUrlDB) {
                    profileLink.value = profileUrlDB;
                    if (visitProfileBtn) visitProfileBtn.href = profileUrlDB;
                }
            }
        });

        // Close Modal
        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                if (modal) modal.classList.add('hidden');
            });
        });

        // Close by clicking background
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.add('hidden');
        });

        // Copy Link (updated handler)
        const copyBtn = document.getElementById('copyBtn');
        if (copyBtn) {
            copyBtn.addEventListener('click', function() {
                const profileLink = document.getElementById('profileLink');
                if (profileLink && profileLink.value) {
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(profileLink.value).then(() => {
                            // Visual feedback
                            const originalHTML = this.innerHTML;
                            this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
                            setTimeout(() => {
                                this.innerHTML = originalHTML;
                            }, 2000);
                        });
                    } else {
                        // Fallback
                        profileLink.select();
                        document.execCommand('copy');
                        const originalHTML = this.innerHTML;
                        this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
                        setTimeout(() => {
                            this.innerHTML = originalHTML;
                        }, 2000);
                    }
                }
            });
        }
    }
    
    // Download QR Code Function (global)
    window.downloadQRCode = function() {
        const qrImage = document.getElementById('qrcodeImage');
        if (qrImage && qrImage.src && qrImage.style.opacity !== '0') {
            const link = document.createElement('a');
            link.href = qrImage.src;
            link.download = 'profile-share-qr-code-' + Date.now() + '.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } else {
            alert('QR code not available yet. Please wait for it to load.');
        }
    };

  });

  // Listen for Livewire events to open edit modal
  document.addEventListener('livewire:init', () => {
    Livewire.on('openUploadModal', (event) => {
      const data = Array.isArray(event) ? event[0] : event;
      const documentId = data?.documentId || data?.document_id;
      const mode = data?.mode || 'add';
      
      // Close view modal if open (Livewire modal)
      if (window.Livewire) {
        Livewire.dispatch('closeDocumentDetails');
      }
      
      // Small delay to ensure modal closes, then open edit modal with higher z-index
      setTimeout(() => {
        const editModal = document.getElementById('addDocumentModal');
        if (editModal) {
          editModal.classList.remove('hidden');
          editModal.style.zIndex = '60'; // Higher than view modal (z-50)
          editModal.style.display = 'flex';
          
          // If edit mode, load document data
          if (mode === 'edit' && documentId) {
            window.editDocumentId = documentId;
            // Load document for editing - you may need to implement this
            // For now, the modal should handle edit mode
          }
        }
      }, 150);
    });
  });

  // Make openVersionHistoryModal available globally if not already defined
  if (typeof window.openVersionHistoryModal === 'undefined') {
    window.openVersionHistoryModal = function(documentId) {
      if (!documentId || documentId === 0) {
        alert('Document ID not available. Please try again.');
        return;
      }
      
      // Close current modal if open
      $("#viewDocumentModal").addClass("hidden").removeClass("flex");
      
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
  }

  // Close version history modal function
  function closeVersionHistoryModal() {
    const modal = document.getElementById('versionHistoryModal');
    if (modal) {
      modal.style.display = 'none';
      modal.innerHTML = '';
    }
  }

  // Pagination state
  let currentSharesPage = 1;
  const sharesPerPage = 10;
  
  // Load and display active shares
  function loadActiveShares(page = 1) {
    currentSharesPage = page;
    const sharesList = $("#activeSharesList");
    const paginationContainer = $("#activeSharesPagination");
    sharesList.html('<div class="text-center py-4 text-gray-500"><i class="fas fa-spinner fa-spin text-xl mb-2"></i><p>Loading...</p></div>');
    paginationContainer.html('');
    
    $.ajax({
      url: "{{ route('shares.documents.index') }}",
      method: 'GET',
      data: {
        page: page,
        per_page: sharesPerPage
      },
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        if (response && response.success && response.shares) {
          const activeShares = response.shares;
          const pagination = response.pagination;
          
          if (activeShares.length === 0 && page === 1) {
            sharesList.html(`
              <div class="text-center py-8 text-gray-500">
                <i class="fas fa-link text-3xl mb-3 text-gray-400"></i>
                <p class="text-sm">No active share links found.</p>
                <p class="text-xs text-gray-400 mt-1">Create a share link to get started.</p>
              </div>
            `);
            paginationContainer.html('');
            return;
          }
          
          let html = '';
          activeShares.forEach(share => {
            const shareUrl = share.share_url || `{{ url('/documents/share') }}/${share.share_token}`;
            const createdAt = new Date(share.created_at).toLocaleDateString();
            const expiresAt = share.expires_at ? new Date(share.expires_at).toLocaleDateString() : 'Never';
            const documentCount = share.documents ? share.documents.length : 0;
            const accessCount = share.access_count || 0;
            
            html += `
              <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow bg-white" data-share-id="${share.id}">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                      <span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i>Active
                      </span>
                      ${share.expires_at ? `<span class="px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800">Expires: ${expiresAt}</span>` : ''}
                    </div>
                    <div class="space-y-1 text-sm text-gray-600">
                      <div class="flex items-center gap-2">
                        <i class="fas fa-link text-[#0053FF]"></i>
                        <span class="font-mono text-xs bg-gray-50 px-2 py-1 rounded flex-1 truncate">${shareUrl}</span>
                        <button onclick="copyShareUrlFromList('${shareUrl}')" class="text-[#0053FF] hover:text-[#0044DD] px-2 py-1 text-xs" title="Copy link">
                          <i class="fas fa-copy"></i>
                        </button>
                      </div>
                      <div class="flex items-center gap-4 text-xs text-gray-500 mt-2">
                        <span><i class="fas fa-file-alt mr-1"></i>${documentCount} document${documentCount !== 1 ? 's' : ''}</span>
                        <span><i class="fas fa-eye mr-1"></i>${accessCount} view${accessCount !== 1 ? 's' : ''}</span>
                        <span><i class="fas fa-calendar mr-1"></i>Created: ${createdAt}</span>
                      </div>
                    </div>
                  </div>
                  <div class="flex gap-2">
                    <a href="${shareUrl}" target="_blank" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
                      <i class="fas fa-external-link-alt mr-1"></i>View
                    </a>
                    <button onclick="revokeShareFromList(${share.id})" class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                      <i class="fas fa-ban mr-1"></i>Revoke
                    </button>
                  </div>
                </div>
              </div>
            `;
          });
          
          sharesList.html(html);
          
          // Render pagination
          if (pagination && pagination.last_page > 1) {
            renderSharesPagination(pagination);
          } else {
            paginationContainer.html('');
          }
        } else {
          sharesList.html(`
            <div class="text-center py-8 text-red-500">
              <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
              <p class="text-sm">Failed to load shares.</p>
            </div>
          `);
          paginationContainer.html('');
        }
      },
      error: function(xhr) {
        console.error('Error loading shares:', xhr);
        sharesList.html(`
          <div class="text-center py-8 text-red-500">
            <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
            <p class="text-sm">Failed to load shares. Please try again.</p>
          </div>
        `);
        paginationContainer.html('');
      }
    });
  }
  
  // Render pagination controls
  function renderSharesPagination(pagination) {
    const paginationContainer = $("#activeSharesPagination");
    let html = '';
    
    // Show total count
    html += `<div class="text-sm text-gray-700">
      Showing <span class="font-medium">${pagination.from}</span> to <span class="font-medium">${pagination.to}</span> of <span class="font-medium">${pagination.total}</span> results
    </div>`;
    
    // Pagination buttons
    html += '<div class="flex items-center gap-2">';
    
    // Previous button
    if (pagination.current_page > 1) {
      html += `<button onclick="loadActiveShares(${pagination.current_page - 1})" class="px-3 py-2 text-sm font-medium text-[#0053FF] bg-white border border-[#0053FF] rounded-md hover:bg-[#0053FF] hover:text-white transition-colors">
        <i class="fas fa-chevron-left mr-1"></i>Previous
      </button>`;
    } else {
      html += `<span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">
        <i class="fas fa-chevron-left mr-1"></i>Previous
      </span>`;
    }
    
    // Page numbers
    html += '<div class="flex items-center gap-1">';
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.last_page, pagination.current_page + 2);
    
    if (startPage > 1) {
      html += `<button onclick="loadActiveShares(1)" class="px-3 py-2 text-sm font-medium text-[#0053FF] bg-white border border-[#0053FF] rounded-md hover:bg-[#0053FF] hover:text-white transition-colors">1</button>`;
      if (startPage > 2) {
        html += `<span class="px-2 text-gray-500">...</span>`;
      }
    }
    
    for (let i = startPage; i <= endPage; i++) {
      if (i === pagination.current_page) {
        html += `<span class="px-3 py-2 text-sm font-medium text-white bg-[#0053FF] rounded-md">${i}</span>`;
      } else {
        html += `<button onclick="loadActiveShares(${i})" class="px-3 py-2 text-sm font-medium text-[#0053FF] bg-white border border-[#0053FF] rounded-md hover:bg-[#0053FF] hover:text-white transition-colors">${i}</button>`;
      }
    }
    
    if (endPage < pagination.last_page) {
      if (endPage < pagination.last_page - 1) {
        html += `<span class="px-2 text-gray-500">...</span>`;
      }
      html += `<button onclick="loadActiveShares(${pagination.last_page})" class="px-3 py-2 text-sm font-medium text-[#0053FF] bg-white border border-[#0053FF] rounded-md hover:bg-[#0053FF] hover:text-white transition-colors">${pagination.last_page}</button>`;
    }
    
    html += '</div>';
    
    // Next button
    if (pagination.current_page < pagination.last_page) {
      html += `<button onclick="loadActiveShares(${pagination.current_page + 1})" class="px-3 py-2 text-sm font-medium text-[#0053FF] bg-white border border-[#0053FF] rounded-md hover:bg-[#0053FF] hover:text-white transition-colors">
        Next<i class="fas fa-chevron-right ml-1"></i>
      </button>`;
    } else {
      html += `<span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">
        Next<i class="fas fa-chevron-right ml-1"></i>
      </span>`;
    }
    
    html += '</div>';
    paginationContainer.html(html);
  }
  
  // Revoke share from list
  function revokeShareFromList(shareId) {
    if (!confirm('Are you sure you want to revoke access to this share link? Once revoked, the link will no longer be accessible.')) {
      return;
    }
    
    const shareCard = $(`[data-share-id="${shareId}"]`);
    const revokeBtn = shareCard.find('button[onclick*="revokeShareFromList"]');
    const originalText = revokeBtn.html();
    revokeBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Revoking...');
    
    $.ajax({
      url: `/shares/documents/${shareId}`,
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      success: function(response) {
        if (response && response.success) {
          // Remove the share card with animation
          shareCard.fadeOut(300, function() {
            $(this).remove();
            // Reload the current page to check if we need to go to previous page
            const currentPage = currentSharesPage;
            loadActiveShares(currentPage);
          });
        } else {
          alert('Failed to revoke access: ' + (response?.message || 'Unknown error'));
          revokeBtn.prop('disabled', false).html(originalText);
        }
      },
      error: function(xhr) {
        const errorMsg = xhr.responseJSON?.message || xhr.responseText || 'Failed to revoke access. Please try again.';
        alert('Error: ' + errorMsg);
        revokeBtn.prop('disabled', false).html(originalText);
      }
    });
  }
  
  // Copy share URL from list
  function copyShareUrlFromList(url) {
    const tempInput = document.createElement('input');
    tempInput.value = url;
    document.body.appendChild(tempInput);
    tempInput.select();
    tempInput.setSelectionRange(0, 99999);
    
    try {
      document.execCommand('copy');
      // Show feedback
      const btn = event.target.closest('button');
      if (btn) {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check text-green-600"></i>';
        setTimeout(() => {
          btn.innerHTML = originalHtml;
        }, 2000);
      }
    } catch (err) {
      alert('Failed to copy. Please copy manually.');
    }
    
    document.body.removeChild(tempInput);
  }
  
  // Load active shares when page loads
  $(document).ready(function() {
    loadActiveShares();
    
    // Reload shares after successful share creation
    $(document).on('shareCreated', function() {
      setTimeout(() => {
        loadActiveShares();
      }, 1000);
    });
  });

  // Tab switching function
  function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
      content.classList.add('hidden');
    });
    
    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
      button.classList.remove('border-[#0053FF]', 'text-[#0053FF]');
      button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    const content = document.getElementById('tab-content-' + tabName);
    if (content) {
      content.classList.remove('hidden');
    }
    
    // Add active state to selected tab
    const button = document.getElementById('tab-' + tabName);
    if (button) {
      button.classList.remove('border-transparent', 'text-gray-500');
      button.classList.add('border-[#0053FF]', 'text-[#0053FF]');
    }
    
    // Load active shares when shares tab is selected
    if (tabName === 'shares' && typeof loadActiveShares === 'function') {
      loadActiveShares();
    }
  }

  // Initialize: Show 'all' tab by default
  document.addEventListener('DOMContentLoaded', function() {
    showTab('all');
    
    // Show bulk selection info banner by default (user can close it)
    // Hide only if user explicitly closed it before
    const hasClosedBulkInfo = localStorage.getItem('hasClosedBulkSelectionInfo');
    if (hasClosedBulkInfo === 'true') {
      $('#bulkSelectionInfo').hide();
    } else {
      // Show the banner by default
      $('#bulkSelectionInfo').show();
    }
    
    // Check for Crewdentials account on page load (CASE 1)
    checkCrewdentialsAccount();
  });
  
  // Crewdentials Integration Functions
  let consentAction = null; // 'import' or 'verification'
  
  // Check if user has Crewdentials account
  function checkCrewdentialsAccount() {
    $.ajax({
      url: "{{ route('crewdentials.check-account') }}",
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        if (response && response.success && response.has_account) {
          // Check if already imported
          // For now, show the import card
          $('#crewdentialsImportCard').removeClass('hidden');
        }
      },
      error: function(xhr) {
        console.error('Failed to check Crewdentials account:', xhr);
      }
    });
  }
  
  // Show consent modal
  function showConsentModal(action) {
    consentAction = action;
    $('#consentCheckbox').prop('checked', false); // Never pre-tick
    $('#crewdentialsConsentModal').removeClass('hidden').addClass('flex');
  }
  
  // Close consent modal
  function closeConsentModal() {
    $('#crewdentialsConsentModal').addClass('hidden').removeClass('flex');
    consentAction = null;
  }
  
  // Submit consent
  function submitConsent() {
    if (!$('#consentCheckbox').is(':checked')) {
      alert('Please check the consent checkbox to continue');
      return;
    }
    
    const btn = $('#consentSubmitBtn');
    const btnText = $('#consentBtnText');
    const spinner = $('#consentBtnSpinner');
    
    btn.prop('disabled', true);
    btnText.text('Processing...');
    spinner.removeClass('hidden');
    
    $.ajax({
      url: "{{ route('crewdentials.consent.store') }}",
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Content-Type': 'application/json'
      },
      data: JSON.stringify({
        has_consented: true
      }),
      success: function(response) {
        if (response && response.success) {
          closeConsentModal();
          
          // Proceed with the action
          if (consentAction === 'import') {
            importFromCrewdentials();
          } else if (consentAction === 'verification' && window.pendingVerificationDocIds) {
            submitVerificationRequest(window.pendingVerificationDocIds);
            window.pendingVerificationDocIds = null;
          }
        } else {
          alert('Failed to store consent: ' + (response?.message || 'Unknown error'));
          btn.prop('disabled', false);
          btnText.text('Give Consent & Continue');
          spinner.addClass('hidden');
        }
      },
      error: function(xhr) {
        const errorMsg = xhr.responseJSON?.message || 'Failed to store consent';
        alert('Error: ' + errorMsg);
        btn.prop('disabled', false);
        btnText.text('Give Consent & Continue');
        spinner.addClass('hidden');
      }
    });
  }
  
  // Import documents from Crewdentials
  function importFromCrewdentials() {
    $.ajax({
      url: "{{ route('crewdentials.import') }}",
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Content-Type': 'application/json'
      },
      success: function(response) {
        if (response && response.success) {
          alert('Document import started! You will be notified when the import is complete.');
          $('#crewdentialsImportCard').addClass('hidden');
          // Reload page after a delay to show imported documents
          setTimeout(() => {
            location.reload();
          }, 2000);
        } else if (response && response.requires_consent) {
          showConsentModal('import');
        } else {
          alert('Failed to import: ' + (response?.message || 'Unknown error'));
        }
      },
      error: function(xhr) {
        if (xhr.status === 403 && xhr.responseJSON?.requires_consent) {
          showConsentModal('import');
        } else {
          const errorMsg = xhr.responseJSON?.message || 'Failed to import documents';
          alert('Error: ' + errorMsg);
        }
      }
    });
  }
  
  // Bulk Actions Functions
  function updateBulkActionsBar() {
    const checkedBoxes = $('.document-checkbox:checked');
    const count = checkedBoxes.length;
    const bulkBar = $('#bulkActionsBar');
    const infoBanner = $('#bulkSelectionInfo');
    
    if (count > 0) {
      $('#selectedCount').text(count);
      bulkBar.removeClass('hidden').addClass('flex');
      // Hide info banner when documents are selected
      if (infoBanner.is(':visible')) {
        infoBanner.slideUp(300);
      }
    } else {
      bulkBar.addClass('hidden').removeClass('flex');
    }
  }
  
  function clearDocumentSelection() {
    $('.document-checkbox').prop('checked', false);
    updateBulkActionsBar();
    // Show info banner again when selection is cleared
    $('#bulkSelectionInfo').slideDown(300);
  }
  
  function requestCrewdentialsVerificationForSelected() {
    const selectedIds = $('.document-checkbox:checked').map(function() {
      return parseInt($(this).val());
    }).get();
    
    if (selectedIds.length === 0) {
      alert('Please select at least one document to verify.');
      return;
    }
    
    requestCrewdentialsVerification(selectedIds);
  }
  
  // Request verification (CASE 2)
  function requestCrewdentialsVerification(documentIds) {
    window.pendingVerificationDocIds = documentIds;
    
    // Check consent first
    $.ajax({
      url: "{{ route('crewdentials.consent.get') }}",
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        if (response && response.has_consent) {
          // Consent exists, proceed with verification
          submitVerificationRequest(documentIds);
        } else {
          // Show consent modal
          consentAction = 'verification';
          showConsentModal('verification');
        }
      },
      error: function() {
        // Assume no consent, show modal
        consentAction = 'verification';
        showConsentModal('verification');
      }
    });
  }
  
  // Submit verification request
  function submitVerificationRequest(documentIds) {
    $.ajax({
      url: "{{ route('crewdentials.request-verification') }}",
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Content-Type': 'application/json'
      },
      data: JSON.stringify({
        document_ids: documentIds
      }),
      success: function(response) {
        if (response && response.success) {
          alert('Verification request submitted! Documents will be processed in the background. You will be notified when verification is complete.');
          // Clear selection after successful submission
          clearDocumentSelection();
          // Optionally reload page to show updated status
          setTimeout(() => {
            location.reload();
          }, 2000);
        } else {
          alert('Failed to submit verification: ' + (response?.message || 'Unknown error'));
        }
      },
      error: function(xhr) {
        if (xhr.status === 403 && xhr.responseJSON?.requires_consent) {
          showConsentModal('verification');
        } else {
          const errorMsg = xhr.responseJSON?.message || 'Failed to submit verification';
          alert('Error: ' + errorMsg);
        }
      }
    });
  }
  
  // Update consent submit to handle verification action
  $(document).on('change', '#consentCheckbox', function() {
    $('#consentSubmitBtn').prop('disabled', !$(this).is(':checked'));
  });
  
  // Handle consent modal close
  $(document).on('click', '#crewdentialsConsentModal .closePopup', function() {
    closeConsentModal();
  });
  
  // Category Assignment Functions
  function checkDocumentsNeedingCategory() {
    $.ajax({
      url: "{{ route('crewdentials.documents.needing-category') }}",
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        if (response && response.success && response.documents && response.documents.length > 0) {
          showCategoryAssignmentModal(response.documents, response.document_types);
        }
      },
      error: function(xhr) {
        console.error('Failed to check documents needing category:', xhr);
      }
    });
  }
  
  function showCategoryAssignmentModal(documents, documentTypes) {
    const modal = $('#categoryAssignmentModal');
    const list = $('#categoryAssignmentList');
    list.html('');
    
    documents.forEach(function(doc) {
      const docHtml = `
        <div class="border border-gray-200 rounded-lg p-4" data-document-id="${doc.id}">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
              <h4 class="font-semibold text-gray-900">${doc.document_name || 'Imported Document'}</h4>
              ${doc.document_number ? `<p class="text-sm text-gray-600">Number: ${doc.document_number}</p>` : ''}
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Select Category:</label>
            <select class="document-category-select w-full border border-gray-300 rounded-lg p-2 text-sm" data-document-id="${doc.id}">
              <option value="">-- Select Category --</option>
              ${Object.keys(documentTypes).map(category => `
                <optgroup label="${category}">
                  ${documentTypes[category].map(type => `
                    <option value="${type.id}" ${doc.document_type_id == type.id ? 'selected' : ''}>${type.name}</option>
                  `).join('')}
                </optgroup>
              `).join('')}
            </select>
          </div>
        </div>
      `;
      list.append(docHtml);
    });
    
    modal.removeClass('hidden').addClass('flex');
  }
  
  function closeCategoryAssignmentModal() {
    $('#categoryAssignmentModal').addClass('hidden').removeClass('flex');
  }
  
  function saveCategoryAssignments() {
    const assignments = [];
    $('.document-category-select').each(function() {
      const documentId = $(this).data('document-id');
      const documentTypeId = $(this).val();
      if (documentTypeId) {
        assignments.push({ document_id: documentId, document_type_id: documentTypeId });
      }
    });
    
    if (assignments.length === 0) {
      alert('Please select at least one category');
      return;
    }
    
    let saved = 0;
    assignments.forEach(function(assignment) {
      $.ajax({
        url: `/crewdentials/documents/${assignment.document_id}/assign-category`,
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
          'Content-Type': 'application/json'
        },
        data: JSON.stringify({
          document_type_id: assignment.document_type_id
        }),
        success: function(response) {
          saved++;
          if (saved === assignments.length) {
            alert('Categories assigned successfully!');
            closeCategoryAssignmentModal();
            location.reload();
          }
        },
        error: function(xhr) {
          alert('Failed to assign category: ' + (xhr.responseJSON?.message || 'Unknown error'));
        }
      });
    });
  }
  
  // Check for documents needing category assignment on page load
  $(document).ready(function() {
    setTimeout(function() {
      checkDocumentsNeedingCategory();
    }, 2000);
  });
  
  // Handle category assignment modal close
  $(document).on('click', '#categoryAssignmentModal .closePopup', function() {
    closeCategoryAssignmentModal();
  });
  
  // Retry Failed Sync Function
  function retryFailedSync(syncId) {
    if (!confirm('Retry this sync operation?')) {
      return;
    }
    
    $.ajax({
      url: `/crewdentials/sync/${syncId}/retry`,
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        if (response && response.success) {
          alert('Sync retry initiated. You will be notified when complete.');
          location.reload();
        } else {
          alert('Failed to retry sync: ' + (response?.message || 'Unknown error'));
        }
      },
      error: function(xhr) {
        alert('Error: ' + (xhr.responseJSON?.message || 'Failed to retry sync'));
      }
    });
  }
</script>

