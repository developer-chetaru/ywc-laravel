<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🔔 Notifications</h1>
                    <p class="text-gray-600 mt-1">Stay updated on your financial goals and reminders</p>
                </div>
                <div class="flex gap-3">
                    @if($unreadCount > 0)
                    <button wire:click="markAllAsRead" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Mark All Read
                    </button>
                    @endif
                    <a href="{{ route('financial.dashboard') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            @if(session('message'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                    {{ session('message') }}
                </div>
            @endif

            @if($unreadCount > 0)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-blue-800">You have <strong>{{ $unreadCount }}</strong> unread notification(s).</p>
            </div>
            @endif

            @if($notifications->count() > 0)
            <div class="space-y-3">
                @foreach($notifications as $notification)
                <div class="border border-gray-200 rounded-lg p-4 {{ !$notification->is_read ? 'bg-blue-50 border-blue-300' : '' }}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            @if(!$notification->is_read)
                            <span class="inline-block w-2 h-2 bg-blue-600 rounded-full mr-2"></span>
                            @endif
                            <h3 class="font-semibold text-gray-900">{{ $notification->title }}</h3>
                            <p class="text-gray-600 mt-1">{{ $notification->message }}</p>
                            <p class="text-xs text-gray-500 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex gap-2 ml-4">
                            @if(!$notification->is_read)
                            <button wire:click="markAsRead({{ $notification->id }})" 
                                    class="text-blue-600 hover:text-blue-800 text-sm">
                                Mark Read
                            </button>
                            @endif
                            <button wire:click="delete({{ $notification->id }})" 
                                    wire:confirm="Delete this notification?"
                                    class="text-red-600 hover:text-red-800 text-sm">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications</h3>
                <p class="text-gray-500">You're all caught up!</p>
            </div>
            @endif
        </div>
    </div>
</div>

