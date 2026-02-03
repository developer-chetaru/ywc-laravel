<div>
    @if($notifications->count() > 0)
        <div class="divide-y divide-gray-200">
            @foreach($notifications as $notification)
                <a 
                    href="{{ $notification->link ?? '#' }}"
                    wire:click="markAsRead({{ $notification->id }})"
                    class="block px-4 py-3 hover:bg-gray-50 transition-colors {{ !$notification->is_read ? 'bg-blue-50' : '' }}"
                >
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            @if($notification->is_read)
                                <div class="w-2 h-2 rounded-full bg-gray-300 mt-2"></div>
                            @else
                                <div class="w-2 h-2 rounded-full bg-blue-500 mt-2"></div>
                            @endif
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900 {{ !$notification->is_read ? 'font-semibold' : '' }}">
                                {{ $notification->title }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $notification->message }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="px-4 py-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="mt-2 text-sm text-gray-500">No notifications</p>
        </div>
    @endif
</div>
