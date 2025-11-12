<div class="bg-white border border-gray-200 rounded-lg p-6 space-y-5 shadow-sm">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Crew & Collaboration</h2>
            <p class="text-sm text-gray-500">Invite crew members, assign roles, and control who can edit or view this itinerary.</p>
        </div>
        @can('manageCrew', $route)
            <form wire:submit.prevent="addMember" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div>
                    <label class="block text-xs uppercase tracking-wide text-gray-500">Email</label>
                    <input type="email" wire:model.defer="form.email"
                           class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="crew@example.com">
                    @error('form.email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wide text-gray-500">Role</label>
                    <select wire:model.defer="form.role"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="viewer">Viewer</option>
                        <option value="editor">Editor</option>
                        <option value="owner">Owner</option>
                    </select>
                </div>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md shadow hover:bg-indigo-700">
                    Invite
                </button>
            </form>
        @endcan
    </div>

    @if($alert)
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-md text-sm">
            {{ $alert }}
        </div>
    @endif

    <div class="space-y-4">
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Route Owner</h3>
            <div class="flex items-center justify-between text-sm text-gray-600">
                <div>
                    <p class="font-medium text-gray-800">{{ $owner->first_name }} {{ $owner->last_name }}</p>
                    <p>{{ $owner->email }}</p>
                </div>
                <span class="px-3 py-1 text-xs font-semibold uppercase tracking-wide bg-indigo-100 text-indigo-700 rounded-full">
                    Owner
                </span>
            </div>
        </div>

        <div class="space-y-3">
            <h3 class="text-sm font-semibold text-gray-700">Crew Members</h3>
            @forelse($crewCollection as $member)
                <div class="border border-gray-200 rounded-lg p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="text-sm text-gray-600 space-y-1">
                        <p class="font-medium text-gray-800">
                            {{ optional($member->user)->first_name }} {{ optional($member->user)->last_name }}
                            <span class="text-gray-500 text-xs">
                                ({{ $member->email }})
                            </span>
                        </p>
                        <p>Status:
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                @class([
                                    'bg-green-100 text-green-700' => $member->status === 'accepted',
                                    'bg-yellow-100 text-yellow-700' => $member->status === 'pending',
                                    'bg-red-100 text-red-700' => $member->status === 'declined',
                                ])">
                                {{ ucfirst($member->status) }}
                            </span>
                        </p>
                        <p>Notifications:
                            <span class="font-medium">{{ $member->notify_on_updates ? 'Enabled' : 'Muted' }}</span>
                        </p>
                    </div>
                    @can('manageCrew', $route)
                        <div class="flex items-center gap-2">
                            <select wire:change="updateRole({{ $member->id }}, $event.target.value)"
                                    class="rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="viewer" @selected($member->role === 'viewer')>Viewer</option>
                                <option value="editor" @selected($member->role === 'editor')>Editor</option>
                                <option value="owner" @selected($member->role === 'owner')>Owner</option>
                            </select>
                            <button wire:click="toggleNotifications({{ $member->id }})"
                                    class="inline-flex items-center px-3 py-1.5 border text-xs rounded-md text-gray-700 border-gray-300 hover:bg-gray-100">
                                {{ $member->notify_on_updates ? 'Mute' : 'Notify' }}
                            </button>
                            <button wire:click="removeMember({{ $member->id }})"
                                    class="inline-flex items-center px-3 py-1.5 bg-red-50 text-xs font-medium text-red-600 rounded-md hover:bg-red-100">
                                Remove
                            </button>
                        </div>
                    @endcan
                </div>
            @empty
                <p class="text-sm text-gray-500">No crew members yet. Invite teammates to collaborate on this route.</p>
            @endforelse
        </div>
    </div>
</div>

