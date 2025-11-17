<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">My Connections</h1>
            <p class="text-gray-600 mt-2">Manage your professional network</p>
        </div>

        @if($alert)
            <div x-data="{ show: true }" 
                x-init="setTimeout(() => show = false, 5000)" 
                x-show="show"
                x-transition
                class="mb-4 bg-green-50 border-l-4 border-green-400 text-green-700 px-4 py-3 rounded-md">
                <i class="fa-solid fa-check-circle mr-2"></i>{{ $alert }}
            </div>
        @endif

        @if($error)
            <div x-data="{ show: true }" 
                x-init="setTimeout(() => show = false, 5000)" 
                x-show="show"
                x-transition
                class="mb-4 bg-red-50 border-l-4 border-red-400 text-red-700 px-4 py-3 rounded-md">
                <i class="fa-solid fa-exclamation-circle mr-2"></i>{{ $error }}
            </div>
        @endif

        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button wire:click="$set('tab', 'connections')" 
                        class="px-6 py-4 font-medium text-sm transition {{ $tab === 'connections' ? 'border-b-2 border-[#0053FF] text-[#0053FF]' : 'text-gray-500 hover:text-gray-700' }}">
                        <i class="fa-solid fa-users mr-2"></i>Connections ({{ $connections->total() }})
                    </button>
                    <button wire:click="$set('tab', 'requests')" 
                        class="px-6 py-4 font-medium text-sm transition {{ $tab === 'requests' ? 'border-b-2 border-[#0053FF] text-[#0053FF]' : 'text-gray-500 hover:text-gray-700' }}">
                        <i class="fa-solid fa-bell mr-2"></i>Requests ({{ $requests->total() }})
                    </button>
                    <button wire:click="$set('tab', 'sent')" 
                        class="px-6 py-4 font-medium text-sm transition {{ $tab === 'sent' ? 'border-b-2 border-[#0053FF] text-[#0053FF]' : 'text-gray-500 hover:text-gray-700' }}">
                        <i class="fa-solid fa-paper-plane mr-2"></i>Sent Requests ({{ $sentRequests->total() }})
                    </button>
                </nav>
            </div>

            <div class="p-6">
                @if($tab === 'connections')
                    <!-- Create Group Button -->
                    <div class="mb-6 flex justify-end">
                        <button wire:click="openCreateGroupModal" 
                            class="px-4 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-[#0046CC] transition">
                            <i class="fa-solid fa-users-plus mr-2"></i>Create Group
                        </button>
                    </div>

                    <!-- Groups Section -->
                    @if(count($groups) > 0)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Groups</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                                @foreach($groups as $group)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer" 
                                        wire:click="openGroupModal({{ $group->id }})">
                                        <div class="flex items-center gap-3">
                                            @if($group->photo_path)
                                                <img src="{{ $group->photo_url }}" 
                                                    alt="{{ $group->name }}" 
                                                    class="w-12 h-12 rounded-full object-cover">
                                            @else
                                                <div class="w-12 h-12 rounded-full bg-[#0053FF] text-white flex items-center justify-center font-bold">
                                                    {{ strtoupper(substr($group->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900">{{ $group->name }}</h4>
                                                <p class="text-xs text-gray-500">{{ $group->members->count() }} members</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <hr class="my-6">
                        </div>
                    @endif

                    <!-- Connections Section -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Connections</h3>
                    </div>
                    @if($connections->count() > 0)
                        <div class="space-y-4">
                            @foreach($connections as $item)
                                @php
                                    $connection = $item['connection'];
                                    $connectedUser = $item['user'];
                                    $unreadCount = $item['unread_count'];
                                @endphp
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex items-start gap-4">
                                        <div class="relative">
                                            <img src="{{ $connectedUser->profile_photo_url ?? '/default-avatar.png' }}" 
                                                alt="{{ $connectedUser->name }}" 
                                                class="w-16 h-16 rounded-full border-2 {{ $unreadCount > 0 ? 'border-red-500' : 'border-[#0053FF]' }}">
                                            @if($unreadCount > 0)
                                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">
                                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <h3 class="font-semibold text-lg text-gray-900">
                                                        {{ $connectedUser->name }}
                                                        @if($unreadCount > 0)
                                                            <span class="text-red-500 text-sm font-normal">({{ $unreadCount }} unread)</span>
                                                        @endif
                                                    </h3>
                                                    <p class="text-gray-600">
                                                        <i class="fa-solid fa-star mr-1"></i>{{ $connectedUser->years_experience ?? 'N/A' }} years experience
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="mt-3 flex gap-2">
                                                <button wire:click="openMessageModal({{ $connectedUser->id }})" 
                                                    class="px-4 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-[#0046CC] transition relative">
                                                    <i class="fa-solid fa-message mr-1"></i>Message
                                                    @if($unreadCount > 0)
                                                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                                                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                                        </span>
                                                    @endif
                                                </button>
                                                <button wire:click="removeConnection({{ $connection->id }})" 
                                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                                    <i class="fa-solid fa-user-minus mr-1"></i>Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $connections->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fa-solid fa-users text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">No connections yet</p>
                        </div>
                    @endif

                @elseif($tab === 'requests')
                    @if($requests->count() > 0)
                        <div class="space-y-4">
                            @foreach($requests as $request)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex items-start gap-4">
                                        <img src="{{ $request->user->profile_photo_url ?? '/default-avatar.png' }}" 
                                            alt="{{ $request->user->name }}" 
                                            class="w-16 h-16 rounded-full border-2 border-[#0053FF]">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-lg text-gray-900">{{ $request->user->name }}</h3>
                                            <p class="text-gray-600">
                                                <i class="fa-solid fa-star mr-1"></i>{{ $request->user->years_experience }} years experience
                                            </p>
                                            @if($request->request_message)
                                                <div class="mt-2 p-3 bg-gray-50 rounded-lg">
                                                    <p class="text-gray-700 text-sm">{{ $request->request_message }}</p>
                                                </div>
                                            @endif
                                            <div class="mt-3 flex gap-2">
                                                <button wire:click="acceptRequest({{ $request->id }})" 
                                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                                    <i class="fa-solid fa-check mr-1"></i>Accept
                                                </button>
                                                <button wire:click="declineRequest({{ $request->id }})" 
                                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                                    <i class="fa-solid fa-times mr-1"></i>Decline
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $requests->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fa-solid fa-inbox text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">No pending connection requests</p>
                        </div>
                    @endif

                @elseif($tab === 'connections')
                    @if($connections->count() > 0)
                        <div class="space-y-4">
                            @foreach($connections as $item)
                                @php
                                    $connection = $item['connection'];
                                    $connectedUser = $item['user'];
                                    $unreadCount = $item['unread_count'];
                                @endphp
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            <div class="relative">
                                                <img src="{{ $connectedUser->profile_photo_url ?? '/default-avatar.png' }}" 
                                                    alt="{{ $connectedUser->name }}" 
                                                    class="w-16 h-16 rounded-full border-2 {{ $unreadCount > 0 ? 'border-red-500' : 'border-[#0053FF]' }}">
                                                @if($unreadCount > 0)
                                                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center">
                                                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <h3 class="font-semibold text-lg text-gray-900">{{ $connectedUser->name }}</h3>
                                                    @if($unreadCount > 0)
                                                        <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-medium rounded-full">
                                                            {{ $unreadCount }} unread
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-gray-600">{{ $connectedUser->email }}</p>
                                                <p class="text-sm text-gray-500">
                                                    <i class="fa-solid fa-clock mr-1"></i>Connected {{ $connection->connected_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button wire:click="openMessageModal({{ $connectedUser->id }})" 
                                                class="relative px-4 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-[#0046CC] transition">
                                                <i class="fa-solid fa-message mr-1"></i>Message
                                                @if($unreadCount > 0)
                                                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                                                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                                    </span>
                                                @endif
                                            </button>
                                            <button wire:click="removeConnection({{ $connection->id }})" 
                                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                                <i class="fa-solid fa-user-minus mr-1"></i>Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $connections->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fa-solid fa-users-slash text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">No connections yet</p>
                        </div>
                    @endif

                @elseif($tab === 'sent')
                    @if($sentRequests->count() > 0)
                        <div class="space-y-4">
                            @foreach($sentRequests as $request)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex items-center gap-4">
                                        <img src="{{ $request->connectedUser->profile_photo_url ?? '/default-avatar.png' }}" 
                                            alt="{{ $request->connectedUser->name }}" 
                                            class="w-16 h-16 rounded-full border-2 border-yellow-400">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-lg text-gray-900">{{ $request->connectedUser->name }}</h3>
                                            <p class="text-gray-600">{{ $request->connectedUser->email }}</p>
                                            <p class="text-sm text-gray-500">
                                                <i class="fa-solid fa-clock mr-1"></i>Request sent {{ $request->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">
                                            <i class="fa-solid fa-hourglass-half mr-1"></i>Pending
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $sentRequests->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fa-solid fa-paper-plane text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">No sent requests</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Messaging Modal -->
        @if($showMessageModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" 
                wire:click="closeMessageModal">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl h-[600px] flex flex-col mx-4" wire:click.stop>
                    <div class="flex justify-between items-center p-4 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <img src="{{ $messageUserId ? \App\Models\User::find($messageUserId)->profile_photo_url ?? '/default-avatar.png' : '/default-avatar.png' }}" 
                                alt="{{ $messageUserName }}" 
                                class="w-10 h-10 rounded-full">
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">{{ $messageUserName }}</h2>
                                <p class="text-xs text-gray-500">Connected</p>
                            </div>
                        </div>
                        <button wire:click="closeMessageModal" 
                            class="text-gray-500 hover:text-gray-700">
                            <i class="fa-solid fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <!-- Messages Area with Real-time Polling -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50" 
                        id="messages-container"
                        wire:poll.3s="refreshMessages"
                        x-data="{ 
                            scrollToBottom() {
                                this.$nextTick(() => {
                                    const container = this.$el;
                                    container.scrollTop = container.scrollHeight;
                                });
                            }
                        }"
                        x-init="
                            scrollToBottom();
                            $watch('$wire.messages', () => scrollToBottom());
                        ">
                        @if(count($messages) > 0)
                            @foreach($messages as $message)
                                <div class="flex {{ $message['is_sent'] ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-xs lg:max-w-md">
                                        @if(!$message['is_sent'])
                                            <div class="flex items-center gap-2 mb-1">
                                                <img src="{{ $message['sender_photo'] ?? '/default-avatar.png' }}" 
                                                    alt="{{ $message['sender_name'] }}" 
                                                    class="w-6 h-6 rounded-full">
                                                <span class="text-xs text-gray-600">{{ $message['sender_name'] }}</span>
                                            </div>
                                        @endif
                                        <div class="rounded-lg px-4 py-2 {{ $message['is_sent'] ? 'bg-[#0053FF] text-white' : 'bg-white text-gray-900 border border-gray-200' }}">
                                            <p class="text-sm">{{ $message['message'] }}</p>
                                            <div class="flex items-center justify-between mt-1">
                                                <p class="text-xs {{ $message['is_sent'] ? 'text-blue-100' : 'text-gray-500' }}">
                                                    {{ $message['time_display'] }}
                                                </p>
                                                @if($message['is_sent'])
                                                    @if($message['is_read'])
                                                        <i class="fa-solid fa-check-double text-blue-200 text-xs" title="Read"></i>
                                                    @else
                                                        <i class="fa-solid fa-check text-blue-200 text-xs" title="Sent"></i>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <i class="fa-solid fa-comments text-4xl text-gray-300 mb-2"></i>
                                <p class="text-gray-500">No messages yet. Start the conversation!</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Message Input -->
                    <div class="p-4 border-t border-gray-200 bg-white">
                        <form wire:submit.prevent="sendMessage" class="flex gap-2">
                            <input type="text" 
                                wire:model="messageText" 
                                placeholder="Type a message..." 
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0053FF] focus:border-transparent"
                                autofocus>
                            <button type="submit" 
                                class="px-6 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-[#0046CC] transition">
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <script>
                // Auto-scroll when new messages arrive
                document.addEventListener('livewire:init', () => {
                    Livewire.on('scroll-to-bottom', () => {
                        setTimeout(() => {
                            const container = document.getElementById('messages-container');
                            if (container) {
                                container.scrollTop = container.scrollHeight;
                            }
                        }, 100);
                    });
                });
            </script>
        @endif

        <!-- Create Group Modal -->
        @if($showCreateGroupModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" 
                wire:click="closeCreateGroupModal">
                <div class="bg-white rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto shadow-2xl" 
                    wire:click.stop>
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold text-gray-900">Create New Group</h2>
                        <button wire:click="closeCreateGroupModal" 
                            class="text-gray-500 hover:text-gray-700">
                            <i class="fa-solid fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <form wire:submit.prevent="createGroup" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fa-solid fa-users mr-1"></i>Group Name *
                            </label>
                            <input type="text" wire:model="groupName" 
                                placeholder="Enter group name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0053FF] focus:border-transparent">
                            @error('groupName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fa-solid fa-info-circle mr-1"></i>Description (Optional)
                            </label>
                            <textarea wire:model="groupDescription" rows="3"
                                placeholder="Enter group description"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0053FF] focus:border-transparent"></textarea>
                            @error('groupDescription') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fa-solid fa-user-plus mr-1"></i>Add Members * (Select from your connections)
                            </label>
                            <div class="border border-gray-300 rounded-lg p-4 max-h-60 overflow-y-auto">
                                @php
                                    $availableConnections = \App\Models\UserConnection::where(function ($q) {
                                        $q->where('user_id', auth()->id())
                                            ->orWhere('connected_user_id', auth()->id());
                                    })
                                    ->where('status', 'accepted')
                                    ->with(['user:id,first_name,last_name,email,profile_photo_path', 'connectedUser:id,first_name,last_name,email,profile_photo_path'])
                                    ->get()
                                    ->map(function ($conn) {
                                        return $conn->user_id === auth()->id() ? $conn->connectedUser : $conn->user;
                                    });
                                @endphp
                                
                                @if($availableConnections->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($availableConnections as $user)
                                            <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                                <input type="checkbox" 
                                                    wire:model="selectedGroupMembers" 
                                                    value="{{ $user->id }}"
                                                    class="rounded border-gray-300 text-[#0053FF] focus:ring-[#0053FF]">
                                                <img src="{{ $user->profile_photo_url ?? '/default-avatar.png' }}" 
                                                    alt="{{ $user->name }}" 
                                                    class="w-8 h-8 rounded-full">
                                                <span class="text-gray-900">{{ $user->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 text-sm text-center py-4">No connections available. Connect with users first.</p>
                                @endif
                            </div>
                            @error('selectedGroupMembers') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="submit" 
                                class="flex-1 px-6 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-[#0046CC] transition font-medium">
                                <i class="fa-solid fa-check mr-2"></i>Create Group
                            </button>
                            <button type="button" wire:click="closeCreateGroupModal" 
                                class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Group Messaging Modal -->
        @if($showGroupModal && $selectedGroupId)
            @php
                $selectedGroup = \App\Models\Group::with(['creator', 'members'])->find($selectedGroupId);
            @endphp
            @if($selectedGroup)
                <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" 
                    wire:click="closeGroupModal">
                    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl h-[700px] flex flex-col mx-4" wire:click.stop>
                        <div class="flex justify-between items-center p-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                @if($selectedGroup->photo_path)
                                    <img src="{{ $selectedGroup->photo_url }}" 
                                        alt="{{ $selectedGroup->name }}" 
                                        class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-[#0053FF] text-white flex items-center justify-center font-bold">
                                        {{ strtoupper(substr($selectedGroup->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900">{{ $selectedGroup->name }}</h2>
                                    <p class="text-xs text-gray-500">{{ $selectedGroup->members->count() }} members</p>
                                </div>
                            </div>
                            <button wire:click="closeGroupModal" 
                                class="text-gray-500 hover:text-gray-700">
                                <i class="fa-solid fa-times text-xl"></i>
                            </button>
                        </div>
                        
                        <!-- Group Messages Area -->
                        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50" 
                            id="group-messages-container"
                            wire:poll.3s="refreshMessages"
                            x-data="{ 
                                scrollToBottom() {
                                    this.$nextTick(() => {
                                        const container = this.$el;
                                        container.scrollTop = container.scrollHeight;
                                    });
                                }
                            }"
                            x-init="
                                scrollToBottom();
                                $watch('$wire.groupMessages', () => scrollToBottom());
                            ">
                            @if(count($groupMessages) > 0)
                                @foreach($groupMessages as $message)
                                    <div class="flex {{ $message['is_sent'] ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-xs lg:max-w-md">
                                            @if(!$message['is_sent'])
                                                <div class="flex items-center gap-2 mb-1">
                                                    <img src="{{ $message['sender_photo'] ?? '/default-avatar.png' }}" 
                                                        alt="{{ $message['sender_name'] }}" 
                                                        class="w-6 h-6 rounded-full">
                                                    <span class="text-xs text-gray-600 font-medium">{{ $message['sender_name'] }}</span>
                                                </div>
                                            @endif
                                            <div class="rounded-lg px-4 py-2 {{ $message['is_sent'] ? 'bg-[#0053FF] text-white' : 'bg-white text-gray-900 border border-gray-200' }}">
                                                <p class="text-sm">{{ $message['message'] }}</p>
                                                <p class="text-xs {{ $message['is_sent'] ? 'text-blue-100' : 'text-gray-500' }} mt-1">
                                                    {{ $message['time_display'] }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-8">
                                    <i class="fa-solid fa-comments text-4xl text-gray-300 mb-2"></i>
                                    <p class="text-gray-500">No messages yet. Start the conversation!</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Group Message Input -->
                        <div class="p-4 border-t border-gray-200 bg-white">
                            <form wire:submit.prevent="sendGroupMessage" class="flex gap-2">
                                <input type="text" 
                                    wire:model="messageText" 
                                    placeholder="Type a message..." 
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0053FF] focus:border-transparent"
                                    autofocus>
                                <button type="submit" 
                                    class="px-6 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-[#0046CC] transition">
                                    <i class="fa-solid fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- Connection Request Modal -->
        @if($showRequestModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" 
                wire:click="closeRequestModal">
                <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 shadow-2xl" wire:click.stop>
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Send Connection Request</h2>
                        <button wire:click="closeRequestModal" 
                            class="text-gray-500 hover:text-gray-700">
                            <i class="fa-solid fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Message (optional)</label>
                        <textarea wire:model="requestMessage" 
                            class="w-full px-4 py-2 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF]" 
                            rows="4" 
                            placeholder="Add a personal message..."></textarea>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="sendConnectionRequest" 
                            class="flex-1 px-4 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-[#0046CC] transition">
                            <i class="fa-solid fa-paper-plane mr-1"></i>Send Request
                        </button>
                        <button wire:click="closeRequestModal" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
