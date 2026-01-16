@extends('layouts.app-laravel')

@section('content')
<main class="flex-1 flex flex-col bg-gray-100 p-4 sm:p-6">
    <div class="flex-1 overflow-hidden">
        <div class="bg-white rounded-xl shadow-md mx-auto w-full p-6" 
             style="height: calc(100vh - 117px); overflow-y: auto;">
            <h2 class="text-2xl font-semibold text-[#0053FF] mb-6">
                {{ $user->first_name }} {{ $user->last_name }} - Documents
            </h2>

            @foreach(['approved', 'rejected', 'pending'] as $status)
    @php
        $docs = $documentsByStatus[$status] ?? collect();
        $statusLabel = ucfirst($status);
    @endphp

    @if($docs->isNotEmpty())
        @if(!$loop->first)
            <hr class="border-t border-gray-400 my-5 w-full">
        @endif

        <h3 class="text-lg font-semibold text-gray-700 my-4">{{ $statusLabel }} Documents</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
            @foreach($docs as $doc)
                @php
                    if($doc->type === 'passport') {
                        $cardName = 'Passport';
                    } elseif($doc->type === 'idvisa') {
                        $cardName = 'ID / Visa';
                    } elseif($doc->type === 'certificate') {
                        $cardName = optional($doc->certificates->first())->type->name ?? 'Certificate';
                    } elseif($doc->type === 'other') {
                        $cardName = optional($doc->otherDocument)->doc_name ?? 'Other Document';
                    } else {
                        $cardName = ucfirst($doc->type ?? 'Document');
                    }

                    $filePath = $doc->file_path
                        ? asset('storage/'.$doc->file_path)
                        : (optional($doc->otherDocument)->file_path
                            ? asset('storage/'.optional($doc->otherDocument)->file_path)
                            : null);

                    $extension = $filePath ? strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) : null;

                    $statusColor = match($doc->status) {
                        'approved' => 'text-green-500',
                        'rejected' => 'text-red-500',
                        default => 'text-yellow-500',
                    };

                    $displayText = $doc->remaining_number !== null 
                        ? $doc->remaining_number . '<br>' . $doc->remaining_type 
                        : ($doc->remaining_type ?? 'N/A');

                    $docNumber = null;
                    if ($doc->type === 'passport' && !empty($doc->passportDetail->passport_number ?? null)) {
                        $docNumber = $doc->passportDetail->passport_number;
                    } elseif ($doc->type === 'idvisa' && !empty($doc->idvisaDetail->document_number ?? null)) {
                        $docNumber = $doc->idvisaDetail->document_number;
                    } elseif ($doc->type === 'certificate' && !empty($doc->certificates[0]->certificate_number ?? null)) {
                        $docNumber = $doc->certificates[0]->certificate_number;
                    } elseif ($doc->type === 'other' && !empty($doc->otherDocument->doc_number ?? null)) {
                        $docNumber = $doc->otherDocument->doc_number;
                    }
                @endphp

                <div class="bg-white rounded-xl p-4 flex items-center border border-gray-200 hover:shadow-md transition">
                    <!-- Document Image -->
                    <div class="flex justify-center w-[80px] h-[90px] items-center p-2 bg-[#E3F2FF] rounded-md cursor-pointer view-doc"
                        data-doc='@json($doc)'>

                        @if($filePath)
                            @if(in_array($extension, ['jpg','jpeg','png','gif','bmp','webp','svg']))
                                <img src="{{ $filePath }}" alt="{{ $cardName }}" class="max-h-full max-w-full object-contain">
                            @elseif($extension === 'pdf')
                                <!-- PDF Icon -->
                                <a class="flex flex-col items-center text-red-600">
                                    <img src="{{ asset('images/pdf.png') }}" alt="PDF" class="h-10 w-10 object-contain">
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">Unsupported</span>
                            @endif
                        @else
                            <span class="text-gray-400 text-sm">N/A</span>
                        @endif
                    </div>

                    <!-- Document Info -->
                    <div class="flex-1 flex justify-between items-center text-left pl-4">
                        <div>
                            <h3 class="text-md font-semibold mb-1">{{ $cardName }}</h3>
                            <div class="flex flex-col text-[12px] text-gray-600">
                                <span>Featured on your Profile Preview</span>
                                <span>Status: <span class="{{ $statusColor }} font-semibold">{{ ucfirst($doc->status ?? 'pending') }}</span></span>
                            </div>
                        </div>

                        <!-- Remaining Info + Verify Button -->
                        <div class="flex flex-col items-center space-y-2">
                            <div class="flex items-center p-2 bg-[#E3F2FF] text-[#0053FF] font-medium w-[60px] justify-center rounded-md text-center text-sm">
                                {!! $displayText !!}
                            </div>

                            @if($doc->status === 'pending')
                                {{-- Only show Verify button for Certificate type documents --}}
                                @if($doc->type === 'certificate' && $doc->dob && $docNumber)
                                    <button class="px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition verify-btn"
                                            data-id="{{ $doc->id }}"
                                            data-dob="{{ $doc->dob ? \Carbon\Carbon::parse($doc->dob)->format('Y-m-d') : '' }}"
                                            data-docno="{{ $docNumber ?? '' }}"
                                            title="Verify UK Certificate of Competency (CoC)">
                                        Verify
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endforeach



        </div>
    </div>
</main>

<!-- Verify Confirmation Popup -->
<div id="verifyPopup" class="fixed inset-0 flex items-center justify-center z-50 hidden bg-black bg-opacity-20">
    <div class="bg-white rounded-2xl shadow-lg p-6 max-w-sm w-full text-center scale-90 opacity-0 transition-all duration-300" id="verifyContent">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Confirm Verification</h2>
        <p class="text-gray-600 mb-6">Are you sure you want to verify this document?</p>
        <div class="flex justify-center space-x-4">
            <button id="cancelVerify" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">Cancel</button>
            <button id="confirmVerify" class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">Verify</button>
        </div>
    </div>
</div>

<!-- View Document Modal -->
<div id="viewDocumentModal" class="popup hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity duration-300 overflow-auto">
    <div id="viewDocumentModalInner" class="bg-white rounded-2xl shadow-2xl w-[95%] max-w-6xl flex flex-col relative overflow-hidden transform scale-95 transition-transform duration-300 max-h-[90vh]">

        <!-- Header -->
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-300 bg-gray-50">
            <h2 id="viewModalTitle" class="text-2xl font-bold text-blue-600">View Document</h2>
            <button class="closePopup text-gray-600 hover:text-gray-800 text-3xl font-bold" aria-label="Close">&times;</button>
        </div>

        <!-- Content -->
        <div class="flex flex-1 overflow-hidden">
            <!-- LEFT: Preview -->
            <div class="w-1/2 border-r p-6 flex flex-col items-center justify-center bg-gray-50">
                <div id="viewPreviewBox" class="w-full h-[70vh] min-h-[300px] border-2 border-dashed border-gray-300 rounded-md flex items-center justify-center text-gray-500 overflow-hidden px-4 text-center bg-white shadow-sm">
                    <img id="viewDocImage" src="" alt="Document Preview" class="max-w-full max-h-full hidden rounded shadow-md">
                    <embed id="viewDocPDF" src="" type="application/pdf" class="w-full h-full hidden">
                    <span id="viewNoFileText" class="text-gray-400 font-medium">No File</span>
                </div>
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

                <!-- Nationality (Passport only) -->
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

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Helpers
    const byId = id => document.getElementById(id);
    const hide = el => el.classList.add('hidden');
    const show = el => el.classList.remove('hidden');

    function formatDateSmart(raw) {
        if (!raw) return '-';
        // try parse and format; if fails return original
        try {
            const d = new Date(raw);
            if (isNaN(d)) return raw;
            return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        } catch (e) {
            return raw;
        }
    }

    function resetModal() {
        // hide all conditional sections
        [
            "issueDateDiv", "expiryDateDiv", "nationalityDiv", "countryCodeDiv",
            "remainingDurationDiv", "documentNumberDiv", "certificateDiv"
        ].forEach(id => byId(id).classList.add('hidden'));

        // reset text nodes
        [
            "viewDocName","viewDocStatus","viewIssueDate","viewExpiryDate","viewNationality",
            "viewCountryCode","viewDocRemaining","viewDocumentNumber","viewCertificateType",
            "viewCertificateIssue","viewCertificateExpiry","viewCertificateNumber","viewCertificateIssuer"
        ].forEach(id => {
            const el = byId(id);
            if (el) el.textContent = '-';
        });

        // reset preview
        const img = byId('viewDocImage');
        const pdf = byId('viewDocPDF');
        img.src = ''; pdf.src = '';
        hide(img); hide(pdf);
        byId('viewNoFileText').textContent = 'No File';
        show(byId('viewNoFileText'));
    }

    // Resolve relation keys safely (handles passport_detail or passportDetail)
    function getRelation(obj, ...keys) {
        for (const k of keys) {
            if (obj && Object.prototype.hasOwnProperty.call(obj, k) && obj[k] != null) return obj[k];
        }
        return null;
    }

    // Determine file path from various places
    function resolveFilePath(doc) {
        // doc.file_path (document-level)
        if (doc.file_path) return doc.file_path;

        // relation-level file paths (passport, idvisa, otherDocument)
        const passport = getRelation(doc, 'passport_detail', 'passportDetail', 'passportdetail', 'passport');
        if (passport && passport.file_path) return passport.file_path;

        const idvisa = getRelation(doc, 'idvisa_detail', 'idvisaDetail', 'idvisadetail', 'idvisa');
        if (idvisa && idvisa.file_path) return idvisa.file_path;

        const other = getRelation(doc, 'other_document', 'otherDocument', 'otherdocument', 'other');
        if (other && other.file_path) return other.file_path;

        // certificate-level file (some setups may store on document only)
        const certs = doc.certificates || [];
        if (certs.length && certs[0].file_path) return certs[0].file_path;

        return null;
    }

    // Open modal when clicking the small preview box
    document.querySelectorAll('.view-doc').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.stopPropagation();
            resetModal();

            let doc;
            try {
                doc = JSON.parse(this.dataset.doc);
            } catch (err) {
                console.error('Invalid doc JSON', err);
                return;
            }

            // PREVIEW: choose file path from doc or nested relations
            const filePath = resolveFilePath(doc);
            const imgEl = byId('viewDocImage');
            const pdfEl = byId('viewDocPDF');
            const noFileEl = byId('viewNoFileText');

            if (filePath) {
                const ext = filePath.split('.').pop().toLowerCase();
                const imgExt = ['jpg','jpeg','png','gif','bmp','svg','webp','tiff','jfif','ico','heic'];
                if (imgExt.includes(ext)) {
                    imgEl.src = `/storage/${filePath}`;
                    show(imgEl);
                    hide(pdfEl);
                    hide(noFileEl);
                } else if (ext === 'pdf') {
                    pdfEl.src = `/storage/${filePath}`;
                    show(pdfEl);
                    hide(imgEl);
                    hide(noFileEl);
                } else {
                    // unsupported type: show a simple message
                    hide(imgEl); hide(pdfEl);
                    noFileEl.textContent = 'Unsupported file type';
                    show(noFileEl);
                }
            } else {
                hide(imgEl); hide(pdfEl);
                noFileEl.textContent = 'No File';
                show(noFileEl);
            }

            // Determine docName depending on type & relations (handles snake_case and camelCase)
            let docName = '-';
            const passport = getRelation(doc, 'passport_detail','passportDetail','passportdetail');
            const idvisa = getRelation(doc, 'idvisa_detail','idvisaDetail','idvisadetail');
            const other = getRelation(doc, 'other_document','otherDocument','otherdocument');

            if (doc.type === 'passport') {
                docName = 'Passport';
            } else if (doc.type === 'idvisa') {
                docName = 'ID / Visa';
            } else if (doc.type === 'certificate') {
                docName = (doc.certificates && doc.certificates[0] && doc.certificates[0].type && doc.certificates[0].type.name) ? doc.certificates[0].type.name : 'Certificate';
            } else if (doc.type === 'other') {
                docName = other?.doc_name || 'Other Document';
            } else {
                docName = doc.name || (doc.type ? capitalizeFirstLetter(doc.type) : '-');
            }

            byId('viewDocName').textContent = docName;
            byId('viewDocStatus').textContent = doc.is_active ? 'Active' : 'Inactive';

            // Remaining duration (fields prepared by controller or present on object)
            if ((doc.remaining_number !== undefined && doc.remaining_number !== null) || doc.remaining_type) {
                const rem = (doc.remaining_number !== null && doc.remaining_number !== undefined)
                    ? `${doc.remaining_number} ${doc.remaining_type || ''}`.trim()
                    : (doc.remaining_type || '-');
                byId('viewDocRemaining').textContent = rem;
                show(byId('remainingDurationDiv'));
            }

            // DOB
            let dobValue = null;

            if (doc.type === 'passport' && passport) {
                dobValue = passport.dob || doc.dob || null;
            } else if (doc.type === 'idvisa' && idvisa) {
                dobValue = idvisa.dob || doc.dob || null;
            } else if (doc.type === 'other' && other) {
                dobValue = other.dob || doc.dob || null;
            } else {
                dobValue = doc.dob || null;
            }

            if (dobValue) {
                byId('viewDOB').textContent = formatDateSmart(dobValue);
                show(byId('dobDiv'));
            } else {
                hide(byId('dobDiv'));
            }

            // Show fields depending on type and available nested data
            // Passport
            if (doc.type === 'passport' && passport) {
                byId('viewIssueDate').textContent = formatDateSmart(passport.issue_date || doc.issue_date);
                byId('viewExpiryDate').textContent = formatDateSmart(passport.expiry_date || doc.expiry_date);
                byId('viewNationality').textContent = passport.nationality || '-';
                byId('viewCountryCode').textContent = passport.country_code || '-';
                ['issueDateDiv','expiryDateDiv','nationalityDiv','countryCodeDiv'].forEach(id => show(byId(id)));
            }
            // ID/VISA
            else if (doc.type === 'idvisa' && idvisa) {
                byId('viewIssueDate').textContent = formatDateSmart(idvisa.issue_date || doc.issue_date);
                byId('viewExpiryDate').textContent = formatDateSmart(idvisa.expiry_date || doc.expiry_date);
                byId('viewDocumentNumber').textContent = idvisa.document_number || '-';
                byId('viewCountryCode').textContent = idvisa.country_code || '-';
                ['issueDateDiv','expiryDateDiv','documentNumberDiv','countryCodeDiv'].forEach(id => show(byId(id)));
            }
            // Certificate
            else if (doc.type === 'certificate' && doc.certificates && doc.certificates.length) {
                const cert = doc.certificates[0];
                byId('viewCertificateType').textContent = cert.type?.name || '-';
                byId('viewCertificateIssue').textContent = formatDateSmart(cert.issue_date || doc.issue_date);
                byId('viewCertificateExpiry').textContent = formatDateSmart(cert.expiry_date || doc.expiry_date);
                byId('viewCertificateNumber').textContent = cert.certificate_number || '-';
                byId('viewCertificateIssuer').textContent = cert.issuer?.name || '-';
                show(byId('certificateDiv'));
            }
            // Other Document
            else if (doc.type === 'other' && other) {
                byId('viewIssueDate').textContent = formatDateSmart(other.issue_date || doc.issue_date);
                byId('viewExpiryDate').textContent = formatDateSmart(other.expiry_date || doc.expiry_date);
                byId('viewDocumentNumber').textContent = other.doc_number || other.doc_number || '-';
                ['issueDateDiv','expiryDateDiv','documentNumberDiv'].forEach(id => show(byId(id)));
            } else {
                // Generic fallback: show doc-level issue/expiry if present
                if (doc.issue_date) {
                    byId('viewIssueDate').textContent = formatDateSmart(doc.issue_date);
                    show(byId('issueDateDiv'));
                }
                if (doc.expiry_date) {
                    byId('viewExpiryDate').textContent = formatDateSmart(doc.expiry_date);
                    show(byId('expiryDateDiv'));
                }
            }

            // show modal (animate)
            const modal = byId('viewDocumentModal');
            modal.classList.remove('hidden');
            setTimeout(() => byId('viewDocumentModalInner').classList.remove('scale-95'), 20);
        });
    });

    // Close actions (button, overlay click, Escape)
    document.addEventListener('click', function (e) {
        // close button
        if (e.target.closest('.closePopup')) {
            byId('viewDocumentModalInner').classList.add('scale-95');
            setTimeout(() => {
                byId('viewDocumentModal').classList.add('hidden');
                resetModal();
            }, 150);
            return;
        }

        // overlay click - only when clicking on the backdrop
        const modal = byId('viewDocumentModal');
        if (modal && e.target === modal) {
            byId('viewDocumentModalInner').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                resetModal();
            }, 150);
            return;
        }
    });

    // ESC to close
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !byId('viewDocumentModal').classList.contains('hidden')) {
            byId('viewDocumentModalInner').classList.add('scale-95');
            setTimeout(() => {
                byId('viewDocumentModal').classList.add('hidden');
                resetModal();
            }, 150);
        }
    });

    // initialise (ensure modal reset)
    resetModal();



    let selectedDocId = null;
    let selectedDob = null;
    let selectedDocNo = null;

    document.querySelectorAll('.verify-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            selectedDocId = this.dataset.id;
            selectedDob = this.dataset.dob || null;
            selectedDocNo = this.dataset.docno || null;

            if (!selectedDocId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Document ID'
                });
                return;
            }

            if (!selectedDob) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing DOB'
                });
                return;
            }

            if (!selectedDocNo) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Document Number'
                });
                return;
            }

            const csrfToken = '{{ csrf_token() }}';

            fetch(`/admin/documents/${selectedDocId}/verify`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    dob: selectedDob,
                    doc_number: selectedDocNo
                })
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => Promise.reject(err));
                }
                return res.json();
            })
            .then(data => {
                if (data.status === 'found') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Document Verified!',
                        text: '✅ Document verified successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else if (data.status === 'not_found') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Certificate Not Found',
                        text: '❌ This certificate was not found in the UK Certificate of Competency (CoC) database. This could mean:\n\n• The certificate number or DOB is incorrect\n• The certificate is not registered in the UK system\n• The certificate is from a different issuing authority\n\nWould you like to reject this document?',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Reject',
                        cancelButtonText: 'No, Keep Pending',
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        width: 600
                    }).then((result) => {
                        if (result.isConfirmed) {
                            rejectDocument(selectedDocId);
                        }
                    });
                } else if (data.status === 'error') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Verification Error',
                        text: data.message || 'Unable to verify document. Please try again later or verify manually.',
                        confirmButtonText: 'OK',
                        width: 500
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Unexpected result',
                        text: 'Please try again or verify manually.'
                    });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: err.message || 'Request failed!'
                });
            });
        });
    });

    function rejectDocument(docId) {
        const csrfToken = '{{ csrf_token() }}';
        fetch(`/admin/documents/${docId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ status: 'rejected' })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'error',
                    title: 'Document Rejected',
                    text: '❌ Document rejected.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Error rejecting document'
                });
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Reject request failed'
            });
        });
    }
});
</script>
@endsection
