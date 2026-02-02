<div>
    @if ($canMark)
        <div class="mt-2">
            @if ($isBestAnswer)
                <button 
                    wire:click="removeBestAnswer"
                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-100 text-green-800 border border-green-300 rounded-lg text-sm font-medium hover:bg-green-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Best Answer (Click to remove)
                </button>
            @else
                <button 
                    wire:click="markBestAnswer"
                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-700 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Mark as Best Answer (+10 reputation)
                </button>
            @endif
        </div>
    @elseif ($isBestAnswer)
        <div class="mt-2">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-100 text-green-800 border border-green-300 rounded-lg text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Best Answer
            </span>
        </div>
    @endif
</div>
