<div class="bg-white border border-gray-200 rounded-lg p-6 space-y-5 shadow-sm">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Crew & Collaboration</h2>
            <p class="text-sm text-gray-500">Invite crew members, assign roles, and control who can edit or view this itinerary.</p>
        </div>
        @can('manageCrew', $route)
            <form wire:submit.prevent="addMember" 
                  x-on:submit="
                    // Ensure select value is captured before submission
                    const selectEl = document.getElementById('userSelect');
                    if (selectEl && selectEl.value) {
                        const wireId = selectEl.closest('[wire\\:id]')?.getAttribute('wire:id');
                        if (wireId && typeof Livewire !== 'undefined') {
                            const component = Livewire.find(wireId);
                            if (component) {
                                component.set('form.user_id', selectEl.value);
                            }
                        }
                    }
                  "
                  class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div class="flex-1">
                    <label class="block text-xs uppercase tracking-wide text-gray-500 mb-1">Email</label>
                    <select id="userSelect" 
                           wire:model="form.user_id"
                           class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 searchable-user-select"
                           style="min-height: 38px;">
                        <option value="">Select a user...</option>
                        @forelse($users as $user)
                            <option value="{{ $user->id }}" 
                                    data-email="{{ $user->email }}"
                                    data-name="{{ $user->first_name }} {{ $user->last_name }}">
                                {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                            </option>
                        @empty
                            <option value="" disabled>No users available</option>
                        @endforelse
                    </select>
                    @error('form.user_id') 
                        <div class="mt-1">
                            <p class="text-xs text-red-600 font-medium">{{ $message }}</p>
                        </div>
                    @enderror
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
                        wire:loading.attr="disabled"
                        wire:target="addMember"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md shadow hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="addMember">Invite</span>
                    <span wire:loading wire:target="addMember">Adding...</span>
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

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<style>
    /* Ensure Choices.js dropdown is visible and clickable */
    .choices {
        position: relative;
        margin-bottom: 0;
    }
    .choices__inner {
        min-height: 38px;
        padding: 4px 8px 4px 4px;
        cursor: pointer;
    }
    .choices__list--dropdown {
        z-index: 9999 !important;
    }
    .choices__list--dropdown .choices__item--selectable {
        padding-right: 10px;
    }
    .choices.is-open .choices__list--dropdown {
        border-color: #6366f1;
    }
    .choices.is-focused .choices__inner {
        border-color: #6366f1;
        box-shadow: 0 0 0 1px #6366f1;
    }
</style>
<script>
    (function() {
        let userChoices = null;

        function initializeUserSelect() {
            const userSelectEl = document.getElementById('userSelect');
            if (!userSelectEl) {
                return;
            }

            // Destroy existing instance if any
            if (userChoices) {
                try {
                    userChoices.destroy();
                } catch (e) {
                    // Ignore errors if already destroyed
                }
                userChoices = null;
            }

            // Remove initialization class if it exists
            userSelectEl.classList.remove('choices-initialized');

                    // Check if there are options (excluding the placeholder)
                    const options = userSelectEl.querySelectorAll('option:not([value=""])');
                    if (options.length === 0) {
                        setTimeout(() => {
                            const retryOptions = userSelectEl.querySelectorAll('option:not([value=""])');
                            if (retryOptions.length > 0) {
                                initializeUserSelect();
                            }
                        }, 100);
                        return;
                    }

            try {
                // Ensure the select is visible
                userSelectEl.style.display = '';
                
                // Get current value before initializing Choices.js
                const currentValue = userSelectEl.value || '';
                
                userChoices = new Choices(userSelectEl, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: 'Select a user...',
                    searchPlaceholderValue: 'Search by name or email...',
                    shouldSort: true,
                    searchChoices: true,
                    removeItemButton: false,
                    allowHTML: false,
                    classNames: {
                        containerOuter: 'choices',
                        containerInner: 'choices__inner',
                        input: 'choices__input',
                        inputCloned: 'choices__input--cloned',
                        list: 'choices__list',
                        listItems: 'choices__list--multiple',
                        listSingle: 'choices__list--single',
                        listDropdown: 'choices__list--dropdown',
                        item: 'choices__item',
                        itemSelectable: 'choices__item--selectable',
                        itemDisabled: 'choices__item--disabled',
                        itemChoice: 'choices__item--choice',
                        placeholder: 'choices__placeholder',
                        group: 'choices__group',
                        groupHeading: 'choices__heading',
                        button: 'choices__button',
                        activeState: 'is-active',
                        focusState: 'is-focused',
                        openState: 'is-open',
                        disabledState: 'is-disabled',
                        highlightedState: 'is-highlighted',
                        selectedState: 'is-selected',
                        flippedState: 'is-flipped',
                        loadingState: 'is-loading',
                        noResults: 'has-no-results',
                        noChoices: 'has-no-choices'
                    },
                    fuseOptions: {
                        threshold: 0.4,
                        minMatchCharLength: 1,
                    },
                });

                userSelectEl.classList.add('choices-initialized');

                // Restore previous value if it exists
                if (currentValue) {
                    userChoices.setChoiceByValue(currentValue);
                }

                // Sync with Livewire when choice is made
                const originalSelect = userChoices.passedElement.element;
                
                // Function to update Livewire
                function updateLivewire(value) {
                    if (!value || value === '') return;
                    
                    // Find the Livewire component
                    let component = null;
                    const wireId = originalSelect.closest('[wire\\:id]')?.getAttribute('wire:id');
                    
                    if (wireId && typeof Livewire !== 'undefined') {
                        component = Livewire.find(wireId);
                    }
                    
                    if (component) {
                        component.set('form.user_id', value);
                        originalSelect.value = value;
                    }
                }
                
                // Listen to Choices.js addItem event (fires when user selects)
                userChoices.passedElement.element.addEventListener('addItem', function(event) {
                    const selectedValue = event.detail.value || '';
                    if (selectedValue) {
                        originalSelect.value = selectedValue;
                        updateLivewire(selectedValue);
                        // Ensure the choice is displayed - immediate update
                        userChoices.setChoiceByValue(selectedValue);
                    }
                });
                
                // Also listen to direct change events
                originalSelect.addEventListener('change', function(e) {
                    const selectedValue = e.target.value || '';
                    if (selectedValue) {
                        updateLivewire(selectedValue);
                        // Immediate update
                        userChoices.setChoiceByValue(selectedValue);
                    } else {
                        // Reset to placeholder if empty
                        userChoices.setChoiceByValue('');
                    }
                });
                
                // Sync displayed value when Livewire updates the form
                const syncDisplayValue = () => {
                    if (userChoices && originalSelect) {
                        const currentValue = originalSelect.value || '';
                        if (currentValue) {
                            userChoices.setChoiceByValue(currentValue);
                        } else {
                            userChoices.setChoiceByValue('');
                        }
                    }
                };
                
                        // Sync on initialization - reduced delay
                        setTimeout(syncDisplayValue, 50);

                        // Ensure dropdown container is clickable and opens - reduced delay
                        setTimeout(() => {
                            const choicesContainer = userSelectEl.closest('.choices');
                            if (choicesContainer) {
                                const choicesInner = choicesContainer.querySelector('.choices__inner');
                                if (choicesInner) {
                                    choicesInner.style.cursor = 'pointer';
                                    // Ensure click opens dropdown
                                    choicesInner.addEventListener('click', function() {
                                        if (!choicesContainer.classList.contains('is-open')) {
                                            userChoices.showDropdown();
                                        }
                                    });
                                }
                            }
                        }, 50);
            } catch (error) {
                console.error('Error initializing Choices.js:', error);
            }
        }

        // Initialize on page load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeUserSelect);
        } else {
            initializeUserSelect();
        }

        // Reinitialize after Livewire updates
        document.addEventListener('livewire:init', () => {
            // Reinitialize after component updates - reduced delay
            Livewire.hook('morph.updated', ({ el }) => {
                const userSelectEl = document.getElementById('userSelect');
                if (userSelectEl && el.contains(userSelectEl)) {
                    // Destroy existing instance first
                    if (userChoices) {
                        try {
                            userChoices.destroy();
                        } catch (e) {
                            // Ignore errors
                        }
                        userChoices = null;
                    }
                    // Reinitialize immediately
                    initializeUserSelect();
                }
            });
            
            // Listen for crew-updated event to reinitialize dropdown - reduced delay
            window.addEventListener('crew-updated', () => {
                const userSelectEl = document.getElementById('userSelect');
                if (userSelectEl) {
                    // Destroy existing instance
                    if (userChoices) {
                        try {
                            userChoices.destroy();
                        } catch (e) {
                            // Ignore errors
                        }
                        userChoices = null;
                    }
                    // Reinitialize immediately
                    initializeUserSelect();
                }
            });
        });
    })();
</script>
@endpush

