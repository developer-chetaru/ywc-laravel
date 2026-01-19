<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <h3 class="font-semibold text-gray-900">{{ $document->document_name ?? ucfirst($document->type) }}</h3>
                <span class="px-2 py-1 text-xs rounded-full
                    {{ $document->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $document->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $document->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ ucfirst($document->status) }}
                </span>
            </div>

            @if($document->document_number)
            <p class="text-sm text-gray-600 mt-1">
                <i class="fas fa-hashtag mr-1"></i>{{ $document->document_number }}
            </p>
            @endif

            <div class="flex gap-4 mt-2 text-sm text-gray-500">
                @if($document->issue_date)
                <span>
                    <i class="fas fa-calendar-alt mr-1"></i>Issued: {{ \Carbon\Carbon::parse($document->issue_date)->format('d M Y') }}
                </span>
                @endif

                @if($document->expiry_date)
                <span class="{{ $document->expiry_date->isPast() ? 'text-red-600 font-medium' : ($document->expiry_date->lte(now()->addDays(30)) ? 'text-orange-600 font-medium' : '') }}">
                    <i class="fas fa-calendar-times mr-1"></i>
                    @if($document->expiry_date->isPast())
                        Expired {{ $document->expiry_date->diffForHumans() }}
                    @elseif($document->expiry_date->lte(now()->addDays(30)))
                        Expires in {{ $document->expiry_date->diffInDays() }} days
                    @else
                        Expires: {{ $document->expiry_date->format('d M Y') }}
                    @endif
                </span>
                @endif
            </div>

            {{-- Verification Status --}}
            @if($document->verifications->isNotEmpty())
                @php $latestVerification = $document->verifications->first(); @endphp
                <div class="mt-2 flex items-center gap-2">
                    <i class="fas fa-shield-alt text-blue-600"></i>
                    <span class="text-sm text-blue-600 font-medium">
                        Verified - Level {{ $latestVerification->verificationLevel->level ?? 'N/A' }}
                    </span>
                </div>
            @endif
        </div>

        <div class="flex gap-2 ml-4">
            @if($document->file_path)
            <a href="{{ route('documents.download', $document->id) }}" 
               class="px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
               title="Download">
                <i class="fas fa-download"></i>
            </a>
            @endif
        </div>
    </div>
</div>
