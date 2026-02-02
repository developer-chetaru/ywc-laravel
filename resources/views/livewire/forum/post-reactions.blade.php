<div class="flex items-center gap-4 mt-3">
    <button 
        wire:click="toggleReaction('like')"
        class="flex items-center gap-2 px-3 py-1.5 rounded-lg transition-colors {{ $userReaction === 'like' ? 'bg-blue-100 text-blue-700 border border-blue-300' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-300' }}"
        title="Like this post">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
        </svg>
        <span class="text-sm font-medium">{{ $reactionCounts['like'] ?? 0 }}</span>
    </button>

    <button 
        wire:click="toggleReaction('helpful')"
        class="flex items-center gap-2 px-3 py-1.5 rounded-lg transition-colors {{ $userReaction === 'helpful' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-300' }}"
        title="Mark as helpful (+3 reputation for author)">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="text-sm font-medium">{{ $reactionCounts['helpful'] ?? 0 }}</span>
    </button>

    <button 
        wire:click="toggleReaction('insightful')"
        class="flex items-center gap-2 px-3 py-1.5 rounded-lg transition-colors {{ $userReaction === 'insightful' ? 'bg-purple-100 text-purple-700 border border-purple-300' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-300' }}"
        title="Mark as insightful">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
        </svg>
        <span class="text-sm font-medium">{{ $reactionCounts['insightful'] ?? 0 }}</span>
    </button>
</div>
