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

<div class="bg-white border border-gray-200 rounded-xl p-6 flex justify-between items-start gap-6 h-full">
    
    <div class="w-full">
        <!-- Custom Checkbox - Only show for pending documents -->
        @if($status === 'pending')
        <label class="inline-flex items-center cursor-pointer mb-2">
            <input type="checkbox" 
                   class="verification-doc-checkbox peer hidden" 
                   value="{{ $doc->id }}"
                   data-doc-name="{{ $doc->document_name ?? $doc->name ?? $displayName }}"
                   data-status="{{ $status }}"
                   onchange="updateModalSelectedCount()">
            
            <div class="group w-[19px] h-[19px] rounded border border-gray-300 flex items-center justify-center transition-all duration-200 peer-checked:bg-blue-600 peer-checked:border-blue-600">
                <img src="{{ asset('images/tick-mark-w.svg') }}" alt="tick" class="w-[10px] h-[10px] opacity-0 peer-checked:opacity-100 transition-opacity duration-200 select-none" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                <i class="fas fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity duration-200" style="display:none;"></i>
            </div>
        </label>
        @elseif($status === 'rejected')
        <!-- Appeal/Delete buttons for rejected documents -->
        <div class="flex gap-2 mb-2">
            <button onclick="appealDocument({{ $doc->id }}); event.stopPropagation();" 
                    class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700 transition" 
                    title="Appeal this rejection">
                <i class="fas fa-gavel mr-1"></i> Appeal
            </button>
            <button onclick="deleteDocument({{ $doc->id }}); event.stopPropagation();" 
                    class="px-3 py-1.5 bg-red-600 text-white text-xs rounded-md hover:bg-red-700 transition" 
                    title="Delete this document">
                <i class="fas fa-trash mr-1"></i> Delete
            </button>
        </div>
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
    <span class="bg-[#E3F2FF] text-blue-600 text-[12px] font-medium px-2.5 py-1 rounded-md whitespace-nowrap mt-[26px]">
        {{ $expiryBadge }}
    </span>
    @endif
    
</div>

<style>
    /* Custom checkbox styling - show tick when checked */
    input[type="checkbox"].verification-doc-checkbox:checked + div img,
    input[type="checkbox"].verification-doc-checkbox:checked + div i {
        opacity: 1 !important;
    }
</style>
