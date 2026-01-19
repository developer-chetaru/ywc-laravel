@extends('layouts.app-laravel')

@section('content')
<main class="flex-1 flex flex-col bg-gray-100 p-4 sm:p-6">
    <div class="max-w-7xl mx-auto w-full">
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Verification Queue</h1>
                    <p class="text-sm text-gray-600 mt-1">Review and verify pending document verification requests</p>
                </div>
            </div>

            @if($verifications->count() > 0)
            <div class="space-y-4">
                @foreach($verifications as $verification)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="font-semibold text-gray-900">
                                    {{ $verification->document->document_name ?? ($verification->document->documentType->name ?? 'Document') }}
                                </h3>
                                <span class="px-2 py-1 text-xs font-medium rounded
                                    @if($verification->verificationLevel->badge_color === 'gold') bg-yellow-100 text-yellow-800
                                    @elseif($verification->verificationLevel->badge_color === 'purple') bg-purple-100 text-purple-800
                                    @elseif($verification->verificationLevel->badge_color === 'green') bg-green-100 text-green-800
                                    @elseif($verification->verificationLevel->badge_color === 'blue') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    <i class="{{ $verification->verificationLevel->badge_icon }} mr-1"></i>
                                    {{ $verification->verificationLevel->name }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-1">
                                <strong>Owner:</strong> {{ $verification->document->user->first_name }} {{ $verification->document->user->last_name }}
                            </p>
                            <p class="text-sm text-gray-600 mb-1">
                                <strong>Requested:</strong> {{ $verification->created_at->format('M d, Y h:i A') }}
                            </p>
                            @if($verification->notes)
                            <p class="text-sm text-gray-600 mt-2">
                                <strong>Notes:</strong> {{ $verification->notes }}
                            </p>
                            @endif
                        </div>
                        <div class="flex gap-2 ml-4">
                            <button onclick="verifyDocument({{ $verification->document->id }}, {{ $verification->id }}, 'approved')" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors text-sm font-medium">
                                <i class="fas fa-check mr-1"></i>Approve
                            </button>
                            <button onclick="verifyDocument({{ $verification->document->id }}, {{ $verification->id }}, 'rejected')" 
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors text-sm font-medium">
                                <i class="fas fa-times mr-1"></i>Reject
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $verifications->links() }}
                </div>
            </div>
            @else
            <div class="text-center py-12">
                <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">No pending verification requests</p>
            </div>
            @endif
        </div>
    </div>
</main>

{{-- Verification Modal --}}
<div id="verificationModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
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
                        @foreach(\App\Models\VerificationLevel::active()->ordered()->get() as $level)
                        <option value="{{ $level->id }}">{{ $level->name }} (Level {{ $level->level }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                    <textarea id="verificationNotes" name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="closeVerificationModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 rounded-md">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openVerificationModal(documentId) {
    document.getElementById('verificationDocumentId').value = documentId;
    document.getElementById('verificationModal').classList.remove('hidden');
}

function closeVerificationModal() {
    document.getElementById('verificationModal').classList.add('hidden');
    document.getElementById('verificationForm').reset();
}

document.getElementById('verificationForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const documentId = document.getElementById('verificationDocumentId').value;
    const formData = new FormData(this);
    
    try {
        const response = await fetch(`/documents/${documentId}/request-verification`, {
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

async function verifyDocument(documentId, verificationId, status) {
    if (!confirm(`Are you sure you want to ${status} this verification?`)) {
        return;
    }
    
    const notes = prompt('Add notes (optional):') || '';
    
    try {
        const response = await fetch(`/documents/${documentId}/verify`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                verification_id: verificationId,
                status: status,
                notes: notes
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(`Verification ${status} successfully!`);
            window.location.reload();
        } else {
            alert(data.message || 'Failed to verify document');
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
        console.error(error);
    }
}
</script>
@endsection
