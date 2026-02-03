<div class="h-full w-full flex flex-col">
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" 
             x-init="setTimeout(() => show = false, 5000)" 
             x-show="show" 
             x-transition
             class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
            <span>{{ session('message') }}</span>
            <button @click="show = false" class="text-white hover:text-gray-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <div class="flex-1 min-h-0 w-full flex gap-x-[18px] px-2 pt-3" style="padding-bottom: 0px;">
        <!-- Sidebar -->
        <div class="w-72 max-[1750px]:w-64 bg-[#0066FF] rounded-xl flex flex-col shadow-lg border border-blue-400/30 overflow-hidden shrink-0 h-full">
            <!-- Settings Header -->
            <div class="px-6 py-4 flex items-center justify-between border-b border-blue-300/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Forum Settings</h3>
                </div>
            </div>

            <!-- Settings Navigation -->
            <div class="flex-1 overflow-y-auto p-5 max-[1750px]:p-4 [scrollbar-width:none]">
                <div class="space-y-2">
                    <a href="{{ route('forum.notifications.index') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('forum.notifications.index') ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="font-medium">Notifications</span>
                    </a>
                    <a href="{{ route('forum.notifications.preferences') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('forum.notifications.preferences') ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="font-medium">Preferences</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 min-h-0 bg-white rounded-xl shadow-sm p-6 overflow-y-auto">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Notification Preferences</h1>
                <p class="text-gray-600 mb-6">Manage how you receive notifications from the forum.</p>

                <div class="space-y-6">
                    @php
                        $notificationTypes = [
                            'new_reply' => ['label' => 'New Replies', 'description' => 'Get notified when someone replies to threads you\'re subscribed to'],
                            'new_thread' => ['label' => 'New Threads', 'description' => 'Get notified when new threads are created in categories you follow'],
                            'quote' => ['label' => 'Quotes', 'description' => 'Get notified when someone quotes your post'],
                            'reaction' => ['label' => 'Reactions', 'description' => 'Get notified when someone reacts to your posts'],
                            'best_answer' => ['label' => 'Best Answer', 'description' => 'Get notified when your post is marked as the best answer'],
                            'pm' => ['label' => 'Private Messages', 'description' => 'Get notified when you receive a private message'],
                            'mention' => ['label' => 'Mentions', 'description' => 'Get notified when someone mentions you using @username'],
                            'moderation' => ['label' => 'Moderation', 'description' => 'Get notified about moderation actions on your content'],
                        ];
                    @endphp

                    @foreach($notificationTypes as $type => $info)
                        <div class="border border-gray-200 rounded-lg p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $info['label'] }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $info['description'] }}</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <!-- On-Site Notifications -->
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">On-Site Notifications</label>
                                        <p class="text-xs text-gray-500 mt-1">Show notifications in the notification bell</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            wire:change="updatePreference('{{ $type }}', 'on_site_enabled', $event.target.checked)"
                                            @if($preferences[$type]['on_site_enabled'] ?? true) checked @endif
                                            class="sr-only peer"
                                        >
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>

                                <!-- Email Notifications -->
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Email Notifications</label>
                                        <p class="text-xs text-gray-500 mt-1">Receive email notifications</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            wire:change="updatePreference('{{ $type }}', 'email_enabled', $event.target.checked)"
                                            @if($preferences[$type]['email_enabled'] ?? true) checked @endif
                                            class="sr-only peer"
                                        >
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>

                                <!-- Digest Mode (only if email is enabled) -->
                                <div x-data="{ 
                                    emailEnabled: @js($preferences[$type]['email_enabled'] ?? true),
                                    init() {
                                        this.$watch('$wire.preferences.{{ $type }}.email_enabled', value => {
                                            this.emailEnabled = value;
                                        });
                                    }
                                }" 
                                     x-show="emailEnabled"
                                     class="flex items-center justify-between"
                                     wire:key="digest-{{ $type }}">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Email Digest</label>
                                        <p class="text-xs text-gray-500 mt-1">How often to receive email notifications</p>
                                    </div>
                                    <select 
                                        wire:change="updatePreference('{{ $type }}', 'digest_mode', $event.target.value)"
                                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                    >
                                        <option value="none" @if(($preferences[$type]['digest_mode'] ?? 'none') === 'none') selected @endif>Immediate</option>
                                        <option value="daily" @if(($preferences[$type]['digest_mode'] ?? 'none') === 'daily') selected @endif>Daily Digest</option>
                                        <option value="weekly" @if(($preferences[$type]['digest_mode'] ?? 'none') === 'weekly') selected @endif>Weekly Digest</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
