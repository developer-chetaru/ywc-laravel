<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('forum.messages.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
            ← Back to Messages
        </a>
        <h1 class="text-2xl font-bold text-gray-900">
            {{ $originalMessage->subject ?? 'Conversation' }}
        </h1>
    </div>

    {{-- Conversation Thread --}}
    <div class="bg-white rounded-lg shadow border border-gray-200 mb-6">
        <div class="divide-y divide-gray-200">
            @foreach ($conversation as $message)
                <div class="p-6 {{ $message->sender_id === Auth::id() ? 'bg-blue-50' : '' }}">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="font-semibold text-gray-900">
                                {{ $message->sender_first_name }} {{ $message->sender_last_name }}
                                @if ($message->sender_id === Auth::id())
                                    <span class="text-sm text-gray-500">(You)</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($message->created_at)->format('M d, Y H:i') }}
                            </div>
                        </div>
                    </div>
                    <div class="text-gray-800 whitespace-pre-wrap">
                        {!! nl2br(e($message->content)) !!}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Reply Form --}}
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <h3 class="text-lg font-semibold mb-4">Reply</h3>
        <form wire:submit.prevent="reply">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Subject (Optional)</label>
                <input type="text" wire:model="replySubject"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2"
                    placeholder="Re: {{ $originalMessage->subject ?? 'No Subject' }}">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Message <span class="text-red-500">*</span></label>
                <textarea wire:model="replyContent" rows="6"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2"
                    placeholder="Type your reply here..."></textarea>
                @error('replyContent') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-3">
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Send Reply
                </button>
                <a href="{{ route('forum.messages.index') }}"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
