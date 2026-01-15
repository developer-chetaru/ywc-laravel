@extends('layouts.app-laravel')

@section('content')
<main class="flex-1 flex flex-col bg-gray-100 p-4 sm:p-6">
    <div class="flex-1 overflow-hidden">
        <div class="p-4 bg-[#F5F6FA]">
            {{-- Back Button --}}
            <div class="flex gap-4 mb-6">
                <a href="{{ route('documents') }}" class="cursor-pointer bg-white border border-[#808080] flex gap-2 justify-center items-center px-4 py-2 rounded-md text-[#808080] text-sm sm:text-base">
                    <img class="h-3" src="{{ asset('images/left-arr.svg') }}" alt="">
                    <span>Back to Documents</span>
                </a>
            </div>

            <div class="rounded-lg bg-white p-4 sm:p-6">
                {{-- Page Title --}}
                <div class="mb-4 sm:mb-6">
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">User Documents</h1>
                    <p class="text-sm text-gray-600 mt-1">View and manage documents for {{ $user->first_name }} {{ $user->last_name }}</p>
                </div>

                {{-- Header & Stats --}}
                <div class="flex flex-col lg:flex-row gap-4 sm:gap-[16px] mb-6 sm:mb-8">
                    <div class="flex flex-wrap justify-between items-center bg-[#F5F6FA] p-4 sm:p-5 py-4 sm:py-6 rounded-lg w-full lg:w-[35%]">
                        <div class="flex items-center gap-[16px]">
                            <img src="{{ $user->profile_photo_path ? asset('storage/'.$user->profile_photo_path) : 'https://ui-avatars.com/api/?name='.$user->first_name.'+'.$user->last_name }}" 
                             class="w-8 h-8 rounded-full object-cover" alt="">
                            <!-- <img src="{{ asset($user->profile_photo ?? 'images/default-avatar.png') }}" alt="Profile" class="rounded-full w-14 h-14"> -->
                            <div>
                                <h2 class="font-semibold text-lg text-[#0053FF]">{{ $user->first_name }} {{ $user->last_name }}</h2>
                                <p class="text-[#616161] text-md">{{ $user->email }}</p>
                            </div>
                        </div>
                        <button id="openProfileSidebar" class="bg-white border border-[#1B1B1B] hover:bg-gray-200 px-4 py-2 rounded-md text-[#1B1B1B] text-sm">
                            View Profile
                        </button>
                    </div>

                    {{-- Stats Cards --}}
                    <div class="flex flex-1 flex-wrap gap-3 sm:gap-[16px]">
                        @php
                            $totalDocs = $documentsByStatus['pending']->count() + $documentsByStatus['approved']->count() + $documentsByStatus['rejected']->count();
                        @endphp
                        <div class="bg-[#F5F6FA] flex-1 min-w-[120px] p-3 sm:p-4 py-3 sm:py-5 rounded-lg text-center">
                            <p class="text-[#020202] text-xs sm:text-sm mb-3 sm:mb-5 font-semibold">Total Documents</p>
                            <p class="font-semibold leading-[20px] text-[#FF7700] text-lg sm:text-xl">{{ $totalDocs }}</p>
                        </div>
                        <div class="bg-[#F5F6FA] flex-1 min-w-[120px] p-3 sm:p-4 py-3 sm:py-5 rounded-lg text-center">
                            <p class="text-[#020202] text-xs sm:text-sm mb-3 sm:mb-5 font-semibold">Pending</p>
                            <p class="font-semibold leading-[20px] text-[#E07911] text-lg sm:text-xl">{{ $documentsByStatus['pending']->count() }}</p>
                        </div>
                        <div class="bg-[#F5F6FA] flex-1 min-w-[120px] p-3 sm:p-4 py-3 sm:py-5 rounded-lg text-center">
                            <p class="text-[#020202] text-xs sm:text-sm mb-3 sm:mb-5 font-semibold">Approved</p>
                            <p class="font-semibold leading-[20px] text-[#0C7B24] text-lg sm:text-xl">{{ $documentsByStatus['approved']->count() }}</p>
                        </div>
                        <div class="bg-[#F5F6FA] flex-1 min-w-[120px] p-3 sm:p-4 py-3 sm:py-5 rounded-lg text-center">
                            <p class="text-[#020202] text-xs sm:text-sm mb-3 sm:mb-5 font-semibold">Rejected</p>
                            <p class="font-semibold leading-[20px] text-[#EB1C24] text-lg sm:text-xl">{{ $documentsByStatus['rejected']->count() }}</p>
                        </div>
                        <div class="bg-[#F5F6FA] flex-1 min-w-[120px] p-3 sm:p-4 py-3 sm:py-5 rounded-lg text-center">
                            <p class="text-[#020202] text-xs sm:text-sm mb-3 sm:mb-5 font-semibold">Expired</p>
                            <p class="font-semibold leading-[20px] text-[#616161] text-lg sm:text-xl">0</p>
                        </div>
                    </div>
                </div>

                {{-- Documents Sections --}}
                @foreach(['pending', 'approved', 'rejected'] as $status)
                    @php
                        $docs = $documentsByStatus[$status] ?? collect();
                        $statusLabel = ucfirst($status);
                        $statusColor = match($status) {
                            'approved' => 'text-[#0C7B24]',
                            'rejected' => 'text-[#EB1C24]',
                            default => 'text-[#E07911]',
                        };
                    @endphp

                    @if($docs->isNotEmpty())
                        <div class="mb-8 border-b border-b-[#BDBDBD] pb-9 {{ $status }}-documents">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">{{ $statusLabel }} Documents</h3>
                            <div class="grid grid-cols-2 gap-[24px]">
                                @foreach($docs as $doc)
                                    @php
                                        if($doc->type === 'passport') $cardName = 'Passport';
                                        elseif($doc->type === 'idvisa') $cardName = 'ID / Visa';
                                        elseif($doc->type === 'certificate') $cardName = optional($doc->certificates->first())->type->name ?? 'Certificate';
                                        elseif($doc->type === 'other') $cardName = optional($doc->otherDocument)->doc_name ?? 'Other Document';
                                        else $cardName = ucfirst($doc->type ?? 'Document');

                                        $filePath = $doc->file_path
                                            ? asset('storage/'.$doc->file_path)
                                            : (optional($doc->otherDocument)->file_path ? asset('storage/'.optional($doc->otherDocument)->file_path) : null);

                                        $extension = $filePath ? strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) : null;

                                        $docNumber = null;
                                        if ($doc->type === 'passport' && !empty($doc->passportDetail->passport_number ?? null)) $docNumber = $doc->passportDetail->passport_number;
                                        elseif ($doc->type === 'idvisa' && !empty($doc->idvisaDetail->document_number ?? null)) $docNumber = $doc->idvisaDetail->document_number;
                                        elseif ($doc->type === 'certificate' && !empty($doc->certificates[0]->certificate_number ?? null)) $docNumber = $doc->certificates[0]->certificate_number;
                                        elseif ($doc->type === 'other' && !empty($doc->otherDocument->doc_number ?? null)) $docNumber = $doc->otherDocument->doc_number;

                                        $uploadDate = $doc->created_at ? $doc->created_at->format('d-m-Y') : '-';
                                        $uploadTime = $doc->created_at ? $doc->created_at->format('H:i') : '-';
                                        $expiryDate = $doc->expiry_date_formatted ? $doc->expiry_date_formatted: '-';
                                    @endphp

                                    <div class="bg-white border border-[#BDBDBD80] flex p-4 py-6 rounded-lg gap-[16px]">
                                        <div class="bg-[#E3F2FF] flex items-center justify-center rounded-md w-[95px] h-[105px]">
                                            @if($filePath)
                                                @if(in_array($extension, ['jpg','jpeg','png','gif','bmp','webp','svg']))
                                                    <img src="{{ $filePath }}" alt="{{ $cardName }}" class="h-[60px] w-[80px]">
                                                @elseif($extension === 'pdf')
                                                    <img src="{{ asset('images/pdf.png') }}" alt="PDF" class="h-10 w-10 object-contain">
                                                @endif
                                            @else
                                                <span class="text-gray-400 text-sm">N/A</span>
                                            @endif
                                        </div>

                                        <div class="flex flex-1 flex-col pt-2">
                                            <div>
                                                <p class="font-semibold text-[#1B1B1B] text-lg mb-3">{{ $cardName }}</p>
                                                <div class="flex flex-col gap-2 mb-5">
                                                    <p class="text-sm text-[#616161] flex gap-[58px]"><span class="min-w-[75px]">Upload At:</span> <span class="text-[#1B1B1B] min-w-[125px]"><span class="upload-date">{{ $uploadDate }},</span> <span class="upload-time">{{ $uploadTime }}</span></span></p>
                                                    <p class="text-sm text-[#616161] flex gap-[58px]"><span class="min-w-[75px]">Expiry Date:</span> <span class="text-[#1B1B1B] min-w-[125px]">{{ $expiryDate }}</span></p>
                                                    <p class="text-sm text-[#616161] flex gap-[58px]"><span class="min-w-[75px]">Status:</span> <span class="min-w-[125px] {{ $statusColor }}">{{ ucfirst($status) }}</span></p>
                                                </div>
                                            </div>

                                            <div class="flex gap-[12px]">
                                                <button class="px-4 py-2 rounded-md text-sm border border-[#1B1B1B] hover:bg-gray-200 min-w-[120px] view-doc-btn" data-doc='@json($doc)'>View Details</button>

                                                @if($status === 'pending')
                                                    <button class="px-4 py-2 rounded-md text-sm border border-[#1B1B1B] hover:bg-gray-200 min-w-[120px] verify-btn" data-id="{{ $doc->id }}" data-dob="{{ $doc->passportDetail->dob ?? $doc->idvisaDetail->dob ?? null }}" data-docno="{{ $docNumber }}">Verify</button>
                                                @else
                                                    <button class="px-4 py-2 rounded-md text-sm border border-[#1B1B1B] hover:bg-gray-200 min-w-[120px] change-status-btn" data-id="{{ $doc->id }}">Change Status</button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</main>

{{-- Verify Confirmation Popup --}}
<div id="verifyPopup" class="fixed inset-0 flex items-center justify-center z-50 hidden bg-black bg-opacity-30 backdrop-blur-lg">
    <div class="bg-white rounded-2xl shadow-lg p-6 max-w-sm w-full text-center scale-90 opacity-0 transition-all duration-300" id="verifyContent">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Confirm Verification</h2>
        <p class="text-gray-600 mb-6">Are you sure you want to verify this document?</p>
        <div class="flex justify-center space-x-4">
            <button id="cancelVerify" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">Cancel</button>
            <button id="confirmVerify" class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">Verify</button>
        </div>
    </div>
</div>

{{-- View Document Modal --}}
<div id="viewDocumentModal" class="popup hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity duration-300 overflow-auto">
    <div id="viewDocumentModalInner" class="bg-white rounded-2xl shadow-2xl w-[95%] max-w-6xl flex flex-col relative overflow-hidden transform scale-95 transition-transform duration-300 max-h-[90vh]">
        {{-- Header --}}
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-300 bg-gray-50">
            <h2 id="viewModalTitle" class="text-2xl font-bold text-blue-600">View Document</h2>
            <button class="closePopup text-gray-600 hover:text-gray-800 text-3xl font-bold" aria-label="Close">&times;</button>
        </div>

        {{-- Content --}}
        <div class="flex flex-1 overflow-hidden">
            <div class="w-1/2 border-r p-6 flex flex-col items-center justify-center bg-gray-50">
                <div id="viewPreviewBox" class="w-full h-[70vh] min-h-[300px] border-2 border-dashed border-gray-300 rounded-md flex items-center justify-center text-gray-500 overflow-hidden px-4 text-center bg-white shadow-sm">
                    <img id="viewDocImage" src="" alt="Document Preview" class="max-w-full max-h-full hidden rounded shadow-md">
                    <embed id="viewDocPDF" src="" type="application/pdf" class="w-full h-full hidden">
                    <span id="viewNoFileText" class="text-gray-400 font-medium">No File</span>
                </div>
            </div>
            <div class="w-1/2 p-6 overflow-y-auto space-y-4">
                {{-- Details Sections --}}
                <div>
                    <label class="block font-semibold text-gray-600">Document Name</label>
                    <p id="viewDocName" class="text-gray-800 text-lg font-medium">-</p>
                </div>
                <div>
                    <label class="block font-semibold text-gray-600">Status</label>
                    <p id="viewDocStatus" class="text-gray-800 text-lg font-medium">-</p>
                </div>
                <div id="dobDiv" class="hidden">
                    <label class="block font-semibold text-gray-600">Date of Birth</label>
                    <p id="viewDOB" class="text-gray-800 text-lg font-medium">-</p>
                </div>
                <div id="issueDateDiv" class="hidden">
                    <label class="block font-semibold text-gray-600">Issue Date</label>
                    <p id="viewIssueDate" class="text-gray-800 text-lg font-medium">-</p>
                </div>
                <div id="expiryDateDiv" class="hidden">
                    <label class="block font-semibold text-gray-600">Expiry Date</label>
                    <p id="viewExpiryDate" class="text-gray-800 text-lg font-medium">-</p>
                </div>
                <div id="nationalityDiv" class="hidden">
                    <label class="block font-semibold text-gray-600">Nationality</label>
                    <p id="viewNationality" class="text-gray-800 text-lg font-medium">-</p>
                </div>
                <div id="countryCodeDiv" class="hidden">
                    <label class="block font-semibold text-gray-600">Country Code</label>
                    <p id="viewCountryCode" class="text-gray-800 text-lg font-medium">-</p>
                </div>
                <div id="remainingDurationDiv" class="hidden">
                    <label class="block font-semibold text-gray-600">Remaining Duration</label>
                    <p id="viewDocRemaining" class="text-gray-800 text-lg font-medium">-</p>
                </div>
                <div id="documentNumberDiv" class="hidden">
                    <label class="block font-semibold text-gray-600">Document Number</label>
                    <p id="viewDocumentNumber" class="text-gray-800 text-lg font-medium">-</p>
                </div>
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


<!-- Overlay -->
<div id="profileOverlay" class="fixed inset-0 bg-black bg-opacity-60 hidden z-40 transition-opacity duration-300"></div>

<div id="profileSidebar" class="fixed right-0 top-0 h-screen w-[400px] bg-[#F5F6FA] shadow-xl p-7 py-10 transform translate-x-full transition-transform duration-500 z-50 hidden">
    <button id="closeProfileSidebar" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-50">X</button>

    <!-- Your existing profile content -->
    <div>
        <!-- BASIC INFO SECTION -->
        <div class="pb-4">
            <div class="flex gap-[13px] items-center mb-6">
                <h3 class="text-sm text-[#0053FF] font-normal">Basic Info</h3>
                <div class="bg-[#808080] flex-1 h-[1px] relative top-[2px]"></div>
            </div>

            <!-- Avatar + Name + Email -->
            <div class="flex items-center gap-4 mb-4">
                @if($user->profile_photo_path)
                    <img src="{{ asset('storage/'.$user->profile_photo_path) }}"
                        alt="User Avatar"
                        class="w-14 h-14 rounded-full object-cover" />
                @else
                    <div class="w-14 h-14 flex items-center justify-center bg-blue-100 text-blue-600 font-bold text-lg rounded-full">
                        {{ strtoupper(substr($user->first_name,0,1).substr($user->last_name,0,1)) }}
                    </div>
                @endif

                <div class="flex flex-col">
                    <h4 class="text-[#1B1B1B] font-semibold text-lg">
                        {{ $user->first_name }} {{ $user->last_name }}
                    </h4>
                    <p class="text-sm text-[#616161]">{{ $user->email }}</p>
                </div>
            </div>

            @php
                $membership = $user->latestSubscription ?? null;
                $statusText = $user->email_verified_at ? 'Verified' : 'Unverified';
                $membershipPlan = $membership ? ($membership->plan_type == 'yearly' ? '1-Year Plan' : 'Monthly') : 'None';
                $lastLogin = $user->last_login ? \Carbon\Carbon::parse($user->last_login)->format('d-m-Y') : '–';
                $firstLogin = $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('d-m-Y') : '–';
            @endphp

            <div class="space-y-4 text-sm max-w-[70%]">
                <div class="flex justify-between"><span class="text-[#616161]">Status</span><span class="text-[#1B1B1B] font-medium">{{ $statusText }}</span></div>
                <div class="flex justify-between"><span class="text-[#616161]">Membership</span><span class="text-[#1B1B1B]">{{ $membershipPlan }}</span></div>
                <div class="flex justify-between"><span class="text-[#616161]">Last Login</span><span class="text-[#1B1B1B]">{{ $lastLogin }}</span></div>
                <div class="flex justify-between"><span class="text-[#616161]">First Login</span><span class="text-[#1B1B1B]">{{ $firstLogin }}</span></div>
                <div class="flex justify-between"><span class="text-[#616161]">Phone Number</span><span class="text-[#1B1B1B]">{{ $user->phone_number ?? '–' }}</span></div>
                <div class="flex justify-between"><span class="text-[#616161]">Nationality</span><span class="text-[#1B1B1B]">{{ $user->nationality ?? '–' }}</span></div>
                <div class="flex justify-between"><span class="text-[#616161]">Marital Status</span><span class="text-[#1B1B1B]">{{ $user->marital_status ?? '–' }}</span></div>
                <div class="flex justify-between"><span class="text-[#616161]">Birth Country / Province</span><span class="text-[#1B1B1B]">{{ $user->birth_country ?? '–' }}</span></div>
            </div>

            @php
                $subStatus = $membership ? ucfirst($membership->status) : 'Inactive';
                $startDate = $membership && $membership->start_date ? \Carbon\Carbon::parse($membership->start_date)->format('d-m-Y') : '–';
                $expiryDate = $membership && $membership->end_date ? \Carbon\Carbon::parse($membership->end_date)->format('d-m-Y') : '–';
                $paymentMethod = $membership ? ucfirst($membership->payment_method ?? 'Stripe') : '–';
            @endphp

        </div>

        <!-- MEMBERSHIP STATUS -->
        @php
            $subStatus = $membership ? ucfirst($membership->status) : 'Inactive';
            $startDate = $membership && $membership->start_date ? \Carbon\Carbon::parse($membership->start_date)->format('d-m-Y') : '–';
            $expiryDate = $membership && $membership->end_date ? \Carbon\Carbon::parse($membership->end_date)->format('d-m-Y') : '–';
            $paymentMethod = $membership ? ucfirst($membership->payment_method ?? 'Stripe') : '–';
        @endphp

        <div class="py-4">
            <div class="flex gap-[13px] items-center mb-6">
                <h3 class="text-sm text-[#0053FF] font-normal">Membership Status</h3>
                <div class="bg-[#808080] flex-1 h-[1px] relative top-[2px]"></div>
            </div>

            <div class="space-y-4 text-sm max-w-[70%]">
                <div class="flex justify-between"><span class="text-gray-500">Membership Plan</span><span class="text-[#1B1B1B] font-medium">{{ $membershipPlan }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="text-[#1B1B1B]">{{ $subStatus }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Start Date</span><span class="text-[#1B1B1B]">{{ $startDate }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Expiry Date</span><span class="text-[#1B1B1B]">{{ $expiryDate }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Payment Method</span><span class="text-[#1B1B1B]">{{ $paymentMethod }}</span></div>
            </div>

            <div class="flex mt-7 gap-[16px]">
                <button class="bg-white border border-[#0053FF] text-[#0053FF] text-sm px-8 py-2 rounded-lg hover:bg-gray-100 leading-[15px]">Invoice</button>
                <button class="bg-white border border-[#0053FF] text-[#0053FF] text-sm px-8 py-2 rounded-lg hover:bg-gray-100 leading-[15px]">Renewal Now</button>
                <button class="bg-white border border-[#0053FF] text-[#0053FF] text-sm px-8 py-2 rounded-lg hover:bg-gray-100 leading-[15px]">More Info</button>
            </div>
        </div>

        <!-- LEGAL SUPPORT -->
        <div class="pt-4 flex gap-[13px] items-center">
            <h3 class="text-sm text-blue-600 font-medium">Legal Support</h3>
            <div class="bg-[#808080] flex-1 h-[1px] relative top-[2px]"></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const byId = id => document.getElementById(id);
    const hide = el => el.classList.add('hidden');
    const show = el => el.classList.remove('hidden');

    function formatDateSmart(raw) {
        if (!raw) return '-';
        try { const d = new Date(raw); if (isNaN(d)) return raw; return d.toLocaleDateString('en-GB',{ day:'2-digit',month:'short',year:'numeric' }); } catch(e){return raw;}
    }

    function resetModal() {
        ["issueDateDiv","expiryDateDiv","nationalityDiv","countryCodeDiv","remainingDurationDiv","documentNumberDiv","certificateDiv"].forEach(id=>hide(byId(id)));
        ["viewDocName","viewDocStatus","viewIssueDate","viewExpiryDate","viewNationality","viewCountryCode","viewDocRemaining","viewDocumentNumber","viewCertificateType","viewCertificateIssue","viewCertificateExpiry","viewCertificateNumber","viewCertificateIssuer"].forEach(id=>byId(id).textContent='-');
        hide(byId('viewDocImage')); hide(byId('viewDocPDF')); show(byId('viewNoFileText')); byId('viewNoFileText').textContent='No File';
    }

    function getRelation(obj,...keys){for(const k of keys){if(obj&&Object.prototype.hasOwnProperty.call(obj,k)&&obj[k]!=null)return obj[k];}return null;}
    function resolveFilePath(doc){const passport=getRelation(doc,'passport_detail','passportDetail','passportdetail','passport');const idvisa=getRelation(doc,'idvisa_detail','idvisaDetail','idvisadetail','idvisa');const other=getRelation(doc,'other_document','otherDocument','otherdocument','other'); const certs=doc.certificates||[]; if(doc.file_path)return doc.file_path; if(passport?.file_path)return passport.file_path; if(idvisa?.file_path)return idvisa.file_path; if(other?.file_path)return other.file_path; if(certs.length&&certs[0].file_path)return certs[0].file_path;return null;}

    // VIEW DOCUMENT
    document.querySelectorAll('.view-doc-btn').forEach(el=>{
        el.addEventListener('click',function(e){
            e.stopPropagation(); resetModal();
            let doc; try{doc=JSON.parse(this.dataset.doc);}catch(err){console.error(err);return;}
            const filePath=resolveFilePath(doc); const imgEl=byId('viewDocImage'); const pdfEl=byId('viewDocPDF'); const noFileEl=byId('viewNoFileText');
            if(filePath){const ext=filePath.split('.').pop().toLowerCase(); const imgExt=['jpg','jpeg','png','gif','bmp','svg','webp','tiff','jfif','ico','heic']; if(imgExt.includes(ext)){imgEl.src='/storage/'+filePath; show(imgEl); hide(pdfEl); hide(noFileEl);} else if(ext==='pdf'){pdfEl.src='/storage/'+filePath; show(pdfEl); hide(imgEl); hide(noFileEl);} else {hide(imgEl); hide(pdfEl); noFileEl.textContent='Unsupported file type'; show(noFileEl);}}else{hide(imgEl); hide(pdfEl); noFileEl.textContent='No File'; show(noFileEl);}
            byId('viewDocName').textContent=doc.type==='passport'?'Passport':doc.type==='idvisa'?'ID / Visa':doc.type==='certificate'?doc.certificates?.[0]?.type?.name||'Certificate':doc.type==='resume'?getRelation(doc,'other_document','otherDocument','otherdocument')?.doc_name||'Resume':doc.type==='other'?getRelation(doc,'other_document','otherDocument','otherdocument')?.doc_name||'Other Document':doc.name||'-';
            byId('viewDocStatus').textContent=doc.is_active?'Active':'Inactive';
            // DOB
            const dob=getRelation(doc,'passport_detail','passportDetail','passportdetail')?.dob||getRelation(doc,'idvisa_detail','idvisaDetail','idvisadetail')?.dob||getRelation(doc,'other_document','otherDocument','otherdocument')?.dob||doc.dob; if(dob){byId('viewDOB').textContent=formatDateSmart(dob); show(byId('dobDiv'));} else hide(byId('dobDiv'));
            // Show modal
            const modal=byId('viewDocumentModal'); modal.classList.remove('hidden'); setTimeout(()=>byId('viewDocumentModalInner').classList.remove('scale-95'),20);
        });
    });

    // Close modal
    document.addEventListener('click',function(e){
        if(e.target.closest('.closePopup')||e.target===byId('viewDocumentModal')){byId('viewDocumentModalInner').classList.add('scale-95'); setTimeout(()=>{byId('viewDocumentModal').classList.add('hidden'); resetModal();},150);}
    });
    document.addEventListener('keydown',e=>{if(e.key==='Escape'&&!byId('viewDocumentModal').classList.contains('hidden')){byId('viewDocumentModalInner').classList.add('scale-95'); setTimeout(()=>{byId('viewDocumentModal').classList.add('hidden'); resetModal();},150);}});

    resetModal();

    // VERIFY DOCUMENT
    document.querySelectorAll('.verify-btn').forEach(btn=>{
        btn.addEventListener('click',function(){
            const docId=this.dataset.id, dob=this.dataset.dob, docNo=this.dataset.docno;
            if(!docId || !dob || !docNo){Swal.fire({icon:'error',title:'Missing Document Info'}); return;}
            const csrfToken='{{ csrf_token() }}';
            fetch(`/admin/documents/${docId}/verify`,{method:'POST',headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},body:JSON.stringify({dob,doc_number:docNo})})
            .then(res=>res.ok?res.json():res.json().then(err=>Promise.reject(err)))
            .then(data=>{
                if(data.status==='found'){Swal.fire({icon:'success',title:'Document Verified!',text:'✅ Document verified successfully.',timer:2000,showConfirmButton:false}).then(()=>window.location.reload());}
                else if(data.status==='not_found'){Swal.fire({icon:'error',title:'Document Not Found',text:'❌ Document not found. Rejecting...',timer:2000,showConfirmButton:false}).then(()=>rejectDocument(docId));}
                else Swal.fire({icon:'warning',title:'Unexpected result'});
            }).catch(err=>{console.error(err); Swal.fire({icon:'error',title:err.message||'Request failed!'})});
        });
    });

    function rejectDocument(docId){
        const csrfToken='{{ csrf_token() }}';
        fetch(`/admin/documents/${docId}/status`,{method:'PATCH',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},body:JSON.stringify({status:'rejected'})})
        .then(res=>res.json()).then(data=>{
            if(data.success){Swal.fire({icon:'error',title:'Document Rejected',text:'❌ Document rejected.',timer:2000,showConfirmButton:false}).then(()=>window.location.reload());}
            else Swal.fire({icon:'warning',title:'Error rejecting document'});
        }).catch(err=>{console.error(err); Swal.fire({icon:'error',title:'Reject request failed'});});
    }

    const openBtn = document.getElementById('openProfileSidebar');
    const closeBtn = document.getElementById('closeProfileSidebar');
    const overlay = document.getElementById('profileOverlay');
    const sidebar = document.getElementById('profileSidebar');

    openBtn.addEventListener('click', () => {
        sidebar.classList.remove('hidden');
        setTimeout(() => sidebar.classList.remove('translate-x-full'), 10);

        overlay.classList.remove('hidden');
        setTimeout(() => overlay.classList.add('opacity-100'), 10);
    });

    function closeSidebar() {
        sidebar.classList.add('translate-x-full'); // slide out
        overlay.classList.remove('opacity-100');

        setTimeout(() => {
            sidebar.classList.add('hidden'); // hide after transition
            overlay.classList.add('hidden');
        }, 500); // match duration-500
    }

    closeBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);

    // CHANGE STATUS
    document.querySelectorAll('.change-status-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const docId = this.dataset.id;
            if (!docId) {
                Swal.fire({icon: 'error', title: 'Document ID missing'});
                return;
            }

            // Show status selection dialog
            Swal.fire({
                title: 'Change Document Status',
                text: 'Select the new status for this document',
                icon: 'question',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'Approve',
                denyButtonText: 'Reject',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#0C7B24',
                denyButtonColor: '#EB1C24',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Approve
                    updateDocumentStatus(docId, 'approved');
                } else if (result.isDenied) {
                    // Reject
                    updateDocumentStatus(docId, 'rejected');
                }
            });
        });
    });

    function updateDocumentStatus(docId, status) {
        const csrfToken = '{{ csrf_token() }}';
        
        fetch(`/admin/documents/${docId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: status })
        })
        .then(res => {
            if (!res.ok) {
                return res.json().then(err => Promise.reject(err));
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Status Updated!',
                    text: `Document status changed to ${status}.`,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: data.message || 'Failed to update document status'
                });
            }
        })
        .catch(err => {
            console.error('Error updating status:', err);
            Swal.fire({
                icon: 'error',
                title: 'Request Failed',
                text: err.message || 'Failed to update document status. Please try again.'
            });
        });
    }

});


</script>

@endsection
