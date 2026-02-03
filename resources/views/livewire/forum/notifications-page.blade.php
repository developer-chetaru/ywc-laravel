<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
                
                <div class="flex items-center gap-3">
                    <a 
                        href="{{ route('forum.notifications.preferences') }}" 
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-medium flex items-center gap-2"
                        title="Notification Preferences"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Preferences
                    </a>
                    <select 
                        wire:model.live="filter"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="all">All</option>
                        <option value="unread">Unread</option>
                        <option value="read">Read</option>
                    </select>
                    
                    @if($filter === 'unread' || $filter === 'all')
                        <button 
                            onclick="Livewire.dispatch('markAllAsRead')"
                            class="px-4 py-2 text-sm text-blue-600 hover:text-blue-800 font-medium"
                        >
                            Mark All as Read
                        </button>
                    @endif
                </div>
            </div>

            <div wire:key="notification-list-{{ $filter }}">
                @livewire('forum.notification-list', ['filter' => $filter, 'limit' => null], key('notification-list-page'))
            </div>
        </div>
    </div>
</div>
