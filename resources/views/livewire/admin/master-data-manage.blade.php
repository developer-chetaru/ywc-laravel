<div>
    @role('super_admin')
    <div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
                {{-- Header --}}
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Master Data Management</h1>
                        <p class="text-sm text-gray-600">Manage route visibility, status, marina types, yacht types, and countries</p>
                    </div>
                    <button wire:click="openAddForm" 
                            class="inline-flex items-center justify-center px-6 py-3 bg-[#0053FF] text-white font-semibold rounded-lg shadow-md hover:bg-[#0040CC] transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add New Master Data
                    </button>
                </div>

                {{-- Success Message --}}
                @if (session()->has('message'))
                    <div x-data="{ show: true }"
                         x-init="setTimeout(() => show = false, 3000)"
                         x-show="show"
                         x-transition
                         class="mb-4 bg-green-50 border-l-4 border-green-400 text-green-700 px-4 py-3 rounded-md">
                        {{ session('message') }}
                    </div>
                @endif

                {{-- Back Button (when type is selected) --}}
                @if($selectedType)
                <div class="mb-4">
                    <button wire:click="backToTypes" 
                            class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Types
                    </button>
                </div>
                @endif

                {{-- Filters and Search (only show when type is selected) --}}
                @if($selectedType)
                <div class="mb-6 flex flex-col md:flex-row gap-4">
                    <div class="relative flex-1">
                        <input type="text" wire:model.debounce.300ms="search"
                               placeholder="Search by name, code, or description..."
                               class="text-gray-700 placeholder-gray-500 w-full py-3 px-4 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0053FF] focus:border-transparent text-sm pl-10 bg-white">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                @endif

                {{-- Add/Edit Form (Hidden by default) --}}
                @if($showForm)
                <div class="mb-6 bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-lg border border-blue-200 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $editId ? 'Edit' : 'Add New' }} Master Data</h3>
                        <button wire:click="cancelEdit" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                            <select wire:model.defer="type" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#0053FF] focus:border-transparent">
                                <option value="">Select Type</option>
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Code</label>
                            <input type="text" wire:model.defer="code"
                                   placeholder="e.g., private, US, motor_yacht"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#0053FF] focus:border-transparent">
                            @error('code') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                            <input type="text" wire:model.defer="name"
                                   placeholder="Display name"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#0053FF] focus:border-transparent">
                            @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea wire:model.defer="description" rows="2"
                                      placeholder="Optional description"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#0053FF] focus:border-transparent"></textarea>
                            @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>


                        <div class="md:col-span-2 flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.defer="is_active" class="h-4 w-4 text-[#0053FF] rounded focus:ring-[#0053FF]">
                                <span class="text-sm font-medium text-gray-700">Active</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mt-6">
                        @if ($editId)
                            <button wire:click="update"
                                    class="px-6 py-2.5 bg-[#0053FF] text-white font-medium rounded-lg hover:bg-[#0040CC] transition-colors">
                                Update Master Data
                            </button>
                            <button wire:click="cancelEdit"
                                    class="px-6 py-2.5 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors">
                                Cancel
                            </button>
                        @else
                            <button wire:click="save"
                                    class="px-6 py-2.5 bg-[#0053FF] text-white font-medium rounded-lg hover:bg-[#0040CC] transition-colors">
                                Add Master Data
                            </button>
                            <button wire:click="cancelEdit"
                                    class="px-6 py-2.5 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors">
                                Cancel
                            </button>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Type List View (default) --}}
                @if($selectedType === null)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($types as $key => $label)
                        @php
                            $count = $typeCounts[$key] ?? 0;
                            $bgColor = match($key) {
                                'route_visibility' => 'bg-gradient-to-br from-purple-50 to-purple-100 border-purple-200 hover:from-purple-100 hover:to-purple-200',
                                'route_status' => 'bg-gradient-to-br from-blue-50 to-blue-100 border-blue-200 hover:from-blue-100 hover:to-blue-200',
                                'marina_type' => 'bg-gradient-to-br from-green-50 to-green-100 border-green-200 hover:from-green-100 hover:to-green-200',
                                'yacht_type' => 'bg-gradient-to-br from-yellow-50 to-yellow-100 border-yellow-200 hover:from-yellow-100 hover:to-yellow-200',
                                default => 'bg-gradient-to-br from-gray-50 to-gray-100 border-gray-200 hover:from-gray-100 hover:to-gray-200',
                            };
                            $textColor = match($key) {
                                'route_visibility' => 'text-purple-800',
                                'route_status' => 'text-blue-800',
                                'marina_type' => 'text-green-800',
                                'yacht_type' => 'text-yellow-800',
                                default => 'text-gray-800',
                            };
                        @endphp
                        <button wire:click="selectType('{{ $key }}')" 
                                class="p-6 rounded-xl border-2 {{ $bgColor }} transition-all transform hover:scale-105 hover:shadow-lg text-left">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-bold {{ $textColor }}">{{ $label }}</h3>
                                <svg class="w-6 h-6 {{ $textColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-3xl font-bold {{ $textColor }}">{{ $count }}</span>
                                <span class="text-sm {{ $textColor }} opacity-75">items</span>
                            </div>
                        </button>
                    @endforeach
                </div>
                @else
                {{-- Items Table View (when type is selected) --}}
                <div class="mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">
                        {{ $types[$selectedType] ?? $selectedType }}
                        <span class="text-sm font-normal text-gray-500">({{ $items->total() }} items)</span>
                    </h2>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($items as $item)
                                <tr wire:key="item-{{ $item->id }}" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($item->code)
                                            <code class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded font-mono">{{ $item->code }}</code>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600 max-w-xs truncate">{{ $item->description ?: '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox"
                                                   wire:change="toggleActive({{ $item->id }})"
                                                   @if($item->is_active) checked @endif
                                                   class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0053FF]"></div>
                                        </label>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex justify-center gap-2">
                                            <button wire:click="edit({{ $item->id }})"
                                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" 
                                                    title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button wire:click="confirmDelete({{ $item->id }})"
                                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" 
                                                    title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                            <p class="text-gray-500 text-lg font-medium">No items found</p>
                                            <p class="text-gray-400 text-sm mt-1">Try adjusting your search</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif
              
                {{-- Pagination (only show when type is selected) --}}
                @if($selectedType && $items->hasPages())
                <div class="mt-6">
                    {{ $items->links('livewire.custom-pagination') }}
                </div>
                @endif

                {{-- Confirm Delete Modal --}}
                @if ($showConfirm)
                    <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50" wire:click="$set('showConfirm', false)">
                        <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full mx-4" wire:click.stop>
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h2 class="text-lg font-semibold text-gray-900">Delete Master Data?</h2>
                                </div>
                            </div>
                            <p class="text-gray-600 mb-6">Are you sure you want to delete this master data item? This action cannot be undone.</p>
                            <div class="flex justify-end gap-3">
                                <button wire:click="$set('showConfirm', false)"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors">
                                    Cancel
                                </button>
                                <button wire:click="delete"
                                        class="px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="flex-1 flex flex-col overflow-hidden">
        <div class="flex min-h-screen bg-gray-100">
            <div class="flex-1 transition-all duration-300">
                <div class="p-6">
                    <p class="text-gray-500 text-lg font-medium">Access Denied. Super Admin only.</p>
                </div>
            </div>
        </div>
    </div>
    @endrole
</div>
