@php
    $isRejected = strtolower(trim($doc->status ?? '')) === 'rejected';
    
    // Get display name
    if ($doc->documentType) {
        $displayName = $doc->documentType->name;
    } elseif ($doc->document_name) {
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
    
    // Status badge config - EXACT match to HTML
    $statusConfig = [
        'approved' => ['bg' => 'bg-[#EAFDF3]', 'text' => 'text-[#2D9510]'],
        'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-600'],
        'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
        'expired' => ['bg' => 'bg-gray-200', 'text' => 'text-gray-600']
    ];
    $status = strtolower($doc->status ?? 'pending');
    $statusStyle = $statusConfig[$status] ?? $statusConfig['pending'];
    
    // Expiry badge - EXACT match to HTML
    $expiryBadge = '';
    $expiryText = '';
    if(strtoupper($doc->remaining_type ?? '') === 'EXPIRED') {
        $expiryBadge = 'EXP';
    } elseif($doc->remaining_number !== null && $doc->remaining_type) {
        // Format: "2 MTH" or "9 YR" (singular/plural handled)
        $type = strtoupper($doc->remaining_type);
        if($doc->remaining_number > 1 && $type === 'YR') {
            $type = 'YRS';
        } elseif($doc->remaining_number > 1 && $type === 'MTH') {
            $type = 'MTH';
        }
        $expiryBadge = $doc->remaining_number . ' ' . $type;
        $expiryText = 'Expires: ' . $doc->remaining_number . ' ' . $type;
    } elseif($doc->remaining_type && $doc->remaining_type !== 'N/A') {
        $expiryBadge = strtoupper($doc->remaining_type);
        $expiryText = 'Expires: ' . strtoupper($doc->remaining_type);
    }
@endphp

@php
    // Determine category for filtering
    $category = 'Other';
    if ($doc->documentType && $doc->documentType->category) {
        $category = $doc->documentType->category;
        // Map to simple categories
        if (!in_array($category, ['Passport', 'Ids and Visa', 'Certificate', 'Other'])) {
            $category = 'Other';
        }
    } else {
        $type = strtolower($doc->type ?? '');
        $category = match($type) {
            'passport' => 'Passport',
            'idvisa', 'ids_and_visa' => 'Ids and Visa',
            'certificate' => 'Certificate',
            default => 'Other'
        };
    }
@endphp

<div class="bg-white border border-gray-200 rounded-xl p-6 flex justify-between items-start gap-6 document-item {{ isset($isModal) && $isModal ? 'modal-document-item' : '' }}" 
     data-document-id="{{ $doc->id }}" 
     data-status="{{ $doc->status ?? 'pending' }}"
     data-category="{{ $category }}"
     data-document-name="{{ strtolower($doc->document_name ?? $doc->name ?? $displayName ?? '') }}">
    
    <!-- Left Content -->
    <div class="w-full relative">
        @if(isset($isModal) && $isModal)
        <!-- Modal Checkbox (Top Left) -->
        <label class="absolute top-0 left-0 z-10 cursor-pointer" title="Select for verification">
            <input type="checkbox" 
                   class="verification-doc-checkbox w-5 h-5 text-blue-600 border-2 border-blue-400 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer transition-all hover:scale-110 hover:border-blue-600" 
                   value="{{ $doc->id }}"
                   onchange="updateModalSelectedCount()">
        </label>
        @endif
        
        <h3 class="text-[#1B1B1B] font-medium text-lg leading-snug">
            {{ $doc->document_name ?? $doc->name ?? $displayName ?? 'Document' }}
        </h3>
        
        <!-- Status Row -->
        <div class="flex items-center gap-3 mt-2">
            <span class="{{ $statusStyle['bg'] }} {{ $statusStyle['text'] }} text-xs px-3 py-1 rounded-md">
                {{ ucfirst($status) }}
            </span>
            
            @if($expiryText)
            <span class="text-[#808080] text-sm">
                {{ $expiryText }}
            </span>
            @endif
        </div>
        
        <!-- Icons -->
        <div class="flex gap-2 mt-4">
            <!-- Reset/History - Only show if versions exist -->
            @if(($doc->version_count ?? 0) > 0)
            <button onclick="openVersionHistoryModal({{ $doc->id }}); event.stopPropagation();" 
                    class="w-[30px] h-[30px] flex items-center justify-center p-0 overflow-hidden" title="View Version History">
                <img class="w-[30px] h-[30px] border border-transparent rounded-[8px] hover:border-[#000000] object-contain" src="{{ asset('images/reset.svg') }}" alt="" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                <i class="fas fa-history text-gray-600" style="display:none;"></i>
            </button>
            @endif
            
            <!-- Eye/View -->
            <button onclick="toggleShare(this); event.stopPropagation();" 
                    class="toggle-share w-[30px] h-[30px] flex items-center justify-center p-0 overflow-hidden" 
                    data-id="{{ $doc->id }}"
                    title="{{ $doc->is_active ? 'Hide from profile' : 'Show on profile' }}">
                <img class="w-[30px] h-[30px] border border-transparent rounded-[8px] hover:border-[#000000] object-contain" src="{{ $doc->is_active ? asset('images/eye.svg') : asset('images/view-off-slash.png') }}" alt="" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                <i class="fas {{ $doc->is_active ? 'fa-eye' : 'fa-eye-slash' }} text-gray-600" style="display:none;"></i>
            </button>
            
            <!-- Download -->
            <button onclick="downloadDocument({{ $doc->id }}); event.stopPropagation();" 
                    class="w-[30px] h-[30px] flex items-center justify-center p-0 overflow-hidden" title="Download">
                <img class="w-[30px] h-[30px] border border-transparent rounded-[8px] hover:border-[#000000] object-contain" src="{{ asset('images/download.svg') }}" alt="" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                <i class="fas fa-download text-gray-600" style="display:none;"></i>
            </button>
            
            <!-- Edit -->
            <button onclick="editDocument({{ $doc->id }}); event.stopPropagation();" 
                    class="w-[30px] h-[30px] flex items-center justify-center p-0 overflow-hidden" title="Edit">
                <img class="w-[30px] h-[30px] border border-transparent rounded-[8px] hover:border-[#000000] object-contain" src="{{ asset('images/edit.svg') }}" alt="" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                <i class="fas fa-edit text-gray-600" style="display:none;"></i>
            </button>
        </div>
    </div>
    
    <!-- Right Badge - EXACT match to HTML -->
    @if($expiryBadge)
    <span class="bg-[#E3F2FF] text-blue-600 text-[12px] font-medium px-2.5 py-1 rounded-md whitespace-nowrap">
        {{ $expiryBadge }}
    </span>
    @endif
    
</div>
