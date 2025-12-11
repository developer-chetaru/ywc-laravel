<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🎯 Goal Management</h1>
                    <p class="text-gray-600 mt-1">Set and track your financial goals</p>
                </div>
                <div class="flex gap-3">
                    <button wire:click="openForm" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        + Set New Goal
                    </button>
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

            {{-- Form Inline --}}
            @if($showForm)
            <div class="mt-6 bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ $editingId ? 'Edit Goal' : 'Set New Goal' }}
                    </h2>
                    <button wire:click="closeForm" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form wire:submit.prevent="save" class="space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Goal Name *</label>
                                <input type="text" wire:model="name" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Goal Type *</label>
                                <select wire:model="type" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @foreach($goalTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Target Amount (€) *</label>
                                <input type="number" step="0.01" wire:model="target_amount" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('target_amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Amount (€) *</label>
                                <input type="number" step="0.01" wire:model="current_amount" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('current_amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Target Date *</label>
                                <input type="date" wire:model="target_date" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('target_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Contribution (€)</label>
                                <input type="number" step="0.01" wire:model="monthly_contribution" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                                <select wire:model="priority" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @foreach($priorities as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Link to Account (Optional)</label>
                                <select wire:model="linked_account_id" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">No account link</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }} (€{{ number_format($account->current_balance, 2) }})</option>
                                    @endforeach
                                </select>
                                @error('linked_account_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea wire:model="description" rows="3" 
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                            <button type="button" wire:click="closeForm" 
                                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                {{ $editingId ? 'Update Goal' : 'Create Goal' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            {{-- Goals List --}}
            @if($goals->count() > 0)
            <div class="grid md:grid-cols-2 gap-6">
                @foreach($goals as $goal)
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $goal->name }}</h3>
                            <p class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $goal->type)) }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-{{ $goal->priority === 'high' ? 'red' : ($goal->priority === 'medium' ? 'yellow' : 'green') }}-100 text-{{ $goal->priority === 'high' ? 'red' : ($goal->priority === 'medium' ? 'yellow' : 'green') }}-800">
                            {{ ucfirst($goal->priority) }}
                        </span>
                    </div>

                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Progress</span>
                            <span class="font-medium">{{ number_format($goal->progress_percentage, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-blue-600 h-3 rounded-full transition-all" 
                                 style="width: {{ min(100, $goal->progress_percentage) }}%"></div>
                        </div>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Current:</span>
                            <span class="font-medium">€{{ number_format($goal->current_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Target:</span>
                            <span class="font-medium">€{{ number_format($goal->target_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Remaining:</span>
                            <span class="font-medium text-blue-600">€{{ number_format($goal->target_amount - $goal->current_amount, 2) }}</span>
                        </div>
                        @if($goal->monthly_contribution)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Monthly:</span>
                            <span class="font-medium">€{{ number_format($goal->monthly_contribution, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Target Date:</span>
                            <span class="font-medium">{{ $goal->target_date->format('M Y') }}</span>
                        </div>
                    </div>

                    @if($goal->description)
                    <p class="text-sm text-gray-600 mb-4">{{ Str::limit($goal->description, 100) }}</p>
                    @endif

                    <div class="flex gap-2 pt-4 border-t border-gray-200">
                        <button wire:click="openForm({{ $goal->id }})" 
                                class="flex-1 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors text-sm font-medium">
                            Edit
                        </button>
                        <button wire:click="delete({{ $goal->id }})" 
                                wire:confirm="Are you sure you want to delete this goal?"
                                class="flex-1 px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors text-sm font-medium">
                            Delete
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $goals->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No goals set yet</h3>
                <p class="text-gray-500 mb-4">Start by setting your first financial goal.</p>
                <button wire:click="openForm" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    + Set Your First Goal
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

