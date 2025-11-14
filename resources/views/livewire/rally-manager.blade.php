<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Rallies & Events</h1>
                <p class="text-gray-600 mt-2">Discover and organize crew meetups</p>
            </div>
            @if($view !== 'create')
                <button wire:click="$set('view', 'create')" 
                    class="px-6 py-3 bg-[#0053FF] text-white rounded-lg hover:bg-[#0046CC] transition shadow-md">
                    <i class="fa-solid fa-plus mr-2"></i> Create Rally
                </button>
            @endif
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
        @if($view !== 'create')
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button wire:click="$set('view', 'discover')" 
                            class="px-6 py-4 font-medium text-sm {{ $view === 'discover' ? 'border-b-2 border-[#0053FF] text-[#0053FF]' : 'text-gray-500 hover:text-gray-700' }}">
                            <i class="fa-solid fa-search mr-2"></i>Discover Rallies
                        </button>
                        <button wire:click="$set('view', 'my-rallies')" 
                            class="px-6 py-4 font-medium text-sm {{ $view === 'my-rallies' ? 'border-b-2 border-[#0053FF] text-[#0053FF]' : 'text-gray-500 hover:text-gray-700' }}">
                            <i class="fa-solid fa-calendar-check mr-2"></i>My Rallies
                        </button>
                    </nav>
                </div>
            </div>
        @endif

        @if($view === 'create')
            <!-- Create Rally Form -->
            <div class="bg-white shadow-lg rounded-xl p-8 border border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Create New Rally</h2>
                    <button wire:click="$set('view', 'discover')" 
                        class="text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-times text-xl"></i>
                    </button>
                </div>
                
                <form wire:submit="createRally" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="form.title" 
                                placeholder="e.g., Crew Beach Day & BBQ"
                                class="w-full px-4 py-2.5 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition"
                                required>
                            @error('form.title') 
                                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea wire:model="form.description" 
                                placeholder="Describe your rally event..."
                                class="w-full px-4 py-2.5 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition" 
                                rows="4" required></textarea>
                            @error('form.description') 
                                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Type <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="form.type" 
                                class="w-full px-4 py-2.5 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition" 
                                required>
                                <option value="social">🍹 Social</option>
                                <option value="active">🏃 Active/Sports</option>
                                <option value="cultural">🎨 Cultural</option>
                                <option value="professional">💼 Professional</option>
                                <option value="learning">🎓 Learning</option>
                                <option value="celebration">🎉 Celebration</option>
                            </select>
                        </div>

                        <!-- Privacy -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Privacy <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="form.privacy" 
                                class="w-full px-4 py-2.5 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition" 
                                required>
                                <option value="public">🌐 Public - Anyone can see and join</option>
                                <option value="private">🔒 Private - Invite only</option>
                                <option value="invite_only">✉️ Invite Only - Requires approval</option>
                            </select>
                        </div>

                        <!-- Start Date & Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Start Date & Time <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" wire:model="form.start_date" 
                                class="w-full px-4 py-2.5 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition" 
                                required>
                            @error('form.start_date') 
                                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- End Date & Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                End Date & Time
                            </label>
                            <input type="datetime-local" wire:model="form.end_date" 
                                class="w-full px-4 py-2.5 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition">
                        </div>

                        <!-- Location -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Location <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="form.location_name" 
                                placeholder="e.g., Plage de la Garoupe, Antibes"
                                class="w-full px-4 py-2.5 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition" 
                                required>
                            @error('form.location_name') 
                                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- Max Participants -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Max Participants
                            </label>
                            <input type="number" wire:model="form.max_participants" 
                                placeholder="Leave empty for unlimited"
                                class="w-full px-4 py-2.5 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition" 
                                min="1">
                        </div>

                        <!-- Cost -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Cost ($)
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-2.5 text-gray-500">$</span>
                                <input type="number" wire:model="form.cost" 
                                    placeholder="0.00"
                                    class="w-full pl-8 pr-4 py-2.5 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition" 
                                    min="0" step="0.01">
                            </div>
                        </div>

                        <!-- What to Bring -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                What to Bring
                            </label>
                            <textarea wire:model="form.what_to_bring" 
                                placeholder="e.g., Towel, sunscreen, drinks..."
                                class="w-full px-4 py-2.5 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition" 
                                rows="2"></textarea>
                        </div>

                        <!-- Requirements -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Requirements
                            </label>
                            <textarea wire:model="form.requirements" 
                                placeholder="e.g., Age 18+, Bring ID..."
                                class="w-full px-4 py-2.5 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF] focus:border-[#0053FF] transition" 
                                rows="2"></textarea>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex gap-3 pt-4 border-t border-gray-200">
                        <button type="submit" 
                            wire:loading.attr="disabled"
                            class="px-6 py-2.5 bg-[#0053FF] text-white rounded-lg hover:bg-[#0046CC] transition shadow-md disabled:opacity-50">
                            <i class="fa-solid fa-check mr-2"></i>
                            <span wire:loading.remove>Create Rally</span>
                            <span wire:loading>Saving...</span>
                        </button>
                        <button type="button" wire:click="$set('view', 'discover')" 
                            class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

        @elseif($view === 'discover')
            <!-- Discover Rallies -->
            <div class="mb-6 bg-white rounded-lg shadow p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fa-solid fa-filter mr-1"></i>Type
                        </label>
                        <select wire:model.live="type" 
                            class="w-full px-4 py-2 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF]">
                            <option value="">All Types</option>
                            <option value="social">🍹 Social</option>
                            <option value="active">🏃 Active/Sports</option>
                            <option value="cultural">🎨 Cultural</option>
                            <option value="professional">💼 Professional</option>
                            <option value="learning">🎓 Learning</option>
                            <option value="celebration">🎉 Celebration</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fa-solid fa-search mr-1"></i>Search
                        </label>
                        <input type="text" wire:model.live.debounce.300ms="search" 
                            placeholder="Search rallies by title, description, or location..." 
                            class="w-full px-4 py-2 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF]">
                    </div>
                </div>
            </div>

            @if($rallies->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($rallies as $rally)
                        <div class="bg-white rounded-lg shadow hover:shadow-xl transition-all cursor-pointer transform hover:-translate-y-1" 
                            wire:click="showRally({{ $rally->id }})">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-3">
                                    <span class="px-3 py-1 bg-[#0053FF]/10 text-[#0053FF] rounded-full text-xs font-medium">
                                        {{ ucfirst($rally->type) }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        <i class="fa-solid fa-calendar mr-1"></i>{{ $rally->start_date->format('M d, Y') }}
                                    </span>
                                </div>
                                <h3 class="font-bold text-lg mb-2 text-gray-900">{{ $rally->title }}</h3>
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $rally->description }}</p>
                                <div class="flex items-center justify-between text-sm pt-3 border-t border-gray-100">
                                    <span class="text-gray-500">
                                        <i class="fa-solid fa-location-dot mr-1"></i>{{ $rally->location_name }}
                                    </span>
                                    <span class="font-medium text-[#0053FF]">
                                        <i class="fa-solid fa-users mr-1"></i>{{ $rally->goingAttendees->count() }} going
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $rallies->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <i class="fa-solid fa-calendar-xmark text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">No rallies found. Try adjusting your filters.</p>
                </div>
            @endif

        @elseif($view === 'my-rallies')
            <!-- My Rallies -->
            @if($myRallies->count() > 0)
                <div class="space-y-4">
                    @foreach($myRallies as $rally)
                        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="font-bold text-xl text-gray-900">{{ $rally->title }}</h3>
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            {{ $rally->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ ucfirst($rally->status) }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 mb-3">{{ $rally->description }}</p>
                                    <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                                        <span><i class="fa-solid fa-calendar mr-1"></i>{{ $rally->start_date->format('M d, Y H:i') }}</span>
                                        <span><i class="fa-solid fa-location-dot mr-1"></i>{{ $rally->location_name }}</span>
                                        <span><i class="fa-solid fa-users mr-1"></i>{{ $rally->goingAttendees->count() }} going</span>
                                        <span><i class="fa-solid fa-question mr-1"></i>{{ $rally->maybeAttendees->count() }} maybe</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $myRallies->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <i class="fa-solid fa-calendar-plus text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">You haven't created any rallies yet.</p>
                    <button wire:click="$set('view', 'create')" 
                        class="mt-4 px-6 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-[#0046CC] transition">
                        Create Your First Rally
                    </button>
                </div>
            @endif
        @endif

        <!-- Rally Details Modal -->
        @if($showRallyModal && $selectedRally)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" 
                wire:click="closeRallyModal">
                <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl" wire:click.stop>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4 pb-4 border-b">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">{{ $selectedRally->title }}</h2>
                                <p class="text-gray-600 mt-1">
                                    <span class="px-2 py-1 bg-[#0053FF]/10 text-[#0053FF] rounded text-xs">{{ ucfirst($selectedRally->type) }}</span>
                                    <span class="mx-2">•</span>
                                    <span class="text-sm">{{ ucfirst($selectedRally->privacy) }}</span>
                                </p>
                            </div>
                            <button wire:click="closeRallyModal" 
                                class="text-gray-500 hover:text-gray-700 text-xl">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>

                        <div class="mb-6">
                            <p class="text-gray-700 leading-relaxed">{{ $selectedRally->description }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
                            <div>
                                <span class="text-sm font-semibold text-gray-600">Date:</span>
                                <p class="text-gray-900">{{ $selectedRally->start_date->format('M d, Y H:i') }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600">Location:</span>
                                <p class="text-gray-900">{{ $selectedRally->location_name }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600">Going:</span>
                                <p class="text-gray-900">{{ $selectedRally->goingAttendees->count() }} people</p>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600">Cost:</span>
                                <p class="text-gray-900">${{ number_format($selectedRally->cost, 2) }}</p>
                            </div>
                        </div>

                        <!-- RSVP Section -->
                        <div class="border-t pt-4 mb-6">
                            <h3 class="font-semibold text-lg mb-3">RSVP</h3>
                            <div class="flex gap-2 mb-3">
                                <button wire:click="$set('rsvpStatus', 'going')" 
                                    class="px-4 py-2 rounded-lg transition {{ $rsvpStatus === 'going' ? 'bg-green-600 text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                    <i class="fa-solid fa-check mr-1"></i>Going
                                </button>
                                <button wire:click="$set('rsvpStatus', 'maybe')" 
                                    class="px-4 py-2 rounded-lg transition {{ $rsvpStatus === 'maybe' ? 'bg-yellow-500 text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                    <i class="fa-solid fa-question mr-1"></i>Maybe
                                </button>
                                <button wire:click="$set('rsvpStatus', 'cant_go')" 
                                    class="px-4 py-2 rounded-lg transition {{ $rsvpStatus === 'cant_go' ? 'bg-red-600 text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                    <i class="fa-solid fa-times mr-1"></i>Can't Go
                                </button>
                            </div>
                            <textarea wire:model="rsvpComment" 
                                placeholder="Add a comment (optional)" 
                                class="w-full px-4 py-2 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF] mb-3" 
                                rows="2"></textarea>
                            <button wire:click="rsvpToRally" 
                                class="px-4 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-[#0046CC] transition">
                                <i class="fa-solid fa-check mr-1"></i>Update RSVP
                            </button>
                        </div>

                        <!-- Comments Section -->
                        <div class="border-t pt-4">
                            <h3 class="font-semibold text-lg mb-3">Comments</h3>
                            <div class="mb-3">
                                <textarea wire:model="rallyComment" 
                                    placeholder="Add a comment..." 
                                    class="w-full px-4 py-2 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#0053FF] mb-2" 
                                    rows="3"></textarea>
                                <button wire:click="addComment" 
                                    class="px-4 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-[#0046CC] transition">
                                    <i class="fa-solid fa-paper-plane mr-1"></i>Post Comment
                                </button>
                            </div>
                            <div class="space-y-3">
                                @foreach($selectedRally->comments as $comment)
                                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="font-semibold text-gray-900">{{ $comment->user->name }}</span>
                                            <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-gray-700">{{ $comment->comment }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
