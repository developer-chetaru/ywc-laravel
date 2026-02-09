<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Private Messages</h1>
        <p class="text-gray-600 mt-1">Manage your messages</p>
    </div>

    {{-- Folder Tabs --}}
    <div class="mb-4 border-b border-gray-200">
        <div class="flex gap-4">
            <button wire:click="switchFolder('inbox')" 
                class="px-4 py-2 border-b-2 {{ $folder === 'inbox' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-600' }} font-medium">
                Inbox @if($folder === 'inbox' && $unreadCount > 0) <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full ml-2">{{ $unreadCount }}</span> @endif
            </button>
            <button wire:click="switchFolder('sent')" 
                class="px-4 py-2 border-b-2 {{ $folder === 'sent' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-600' }} font-medium">
                Sent
            </button>
            <button wire:click="switchFolder('archived')" 
                class="px-4 py-2 border-b-2 {{ $folder === 'archived' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-600' }} font-medium">
                Archived
            </button>
            <div class="ml-auto">
                <a href="{{ route('forum.messages.send') }}" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    New Message
                </a>
            </div>
        </div>
    </div>

    {{-- Messages List --}}
    <div class="bg-white rounded-lg shadow border border-gray-200">
        @if ($messages->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach ($messages as $message)
                    <div class="p-4 hover:bg-gray-50 cursor-pointer {{ $selectedMessageId === $message->id ? 'bg-blue-50' : '' }}"
                         wire:click="selectMessage({{ $message->id }})">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="font-semibold text-gray-900">
                                        @if($folder === 'sent')
                                            To: {{ $message->recipient_first_name ?? 'Unknown' }} {{ $message->recipient_last_name ?? '' }}
                                        @else
                                            {{ $message->sender_first_name }} {{ $message->sender_last_name }}
                                        @endif
                                    </span>
                                    @if (!$message->is_read && $folder === 'inbox')
                                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                    @endif
                                </div>
                                <div class="text-sm font-medium text-gray-800 mb-1">
                                    {{ $message->subject ?: '(No Subject)' }}
                                </div>
                                <div class="text-sm text-gray-600 line-clamp-2">
                                    {{ Str::limit(strip_tags($message->content), 100) }}
                                </div>
                                <div class="text-xs text-gray-500 mt-2">
                                    {{ \Carbon\Carbon::parse($message->created_at)->format('M d, Y H:i') }}
                                </div>
                            </div>
                            <div class="flex gap-2 ml-4">
                                @if ($folder === 'inbox' && !$message->is_read)
                                    <button wire:click.stop="markAsRead({{ $message->id }})"
                                        class="text-xs text-blue-600 hover:text-blue-800">
                                        Mark Read
                                    </button>
                                @endif
                                @if ($folder === 'inbox' && !$message->is_archived)
                                    <button wire:click.stop="archiveMessage({{ $message->id }})"
                                        class="text-xs text-gray-600 hover:text-gray-800">
                                        Archive
                                    </button>
                                @elseif ($folder === 'archived')
                                    <button wire:click.stop="unarchiveMessage({{ $message->id }})"
                                        class="text-xs text-gray-600 hover:text-gray-800">
                                        Unarchive
                                    </button>
                                @endif
                                <button wire:click.stop="deleteMessage({{ $message->id }})"
                                    class="text-xs text-red-600 hover:text-red-800"
                                    onclick="return confirm('Are you sure you want to delete this message?')">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center text-gray-500">
                No messages in {{ $folder }}.
            </div>
        @endif
    </div>

    {{-- View Conversation Button --}}
    @if ($selectedMessageId)
        <div class="mt-4">
            <a href="{{ route('forum.messages.conversation', $selectedMessageId) }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                View Conversation
            </a>
        </div>
    @endif
</div>
