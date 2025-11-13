@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Manage Yachts</h1>
                    <p class="text-sm text-gray-600">Add, edit, and manage yacht information</p>
                </div>
                <button wire:click="openAddModal" class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Yacht
                </button>
            </div>

            @if (session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 text-green-700 px-4 py-3 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filters and Search --}}
            <div class="mb-6 space-y-4">
                {{-- Search Bar --}}
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search"
                           placeholder="Search by name, home port, or builder..."
                           class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-sm">
                </div>

                {{-- Filter Row --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Type Filter --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                        <select wire:model.live="filterType" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="">All Types</option>
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model.live="filterStatus" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sort By --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Sort By</label>
                        <select wire:model.live="sortBy" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="rating">Highest Rated</option>
                            <option value="reviews_desc">Most Reviews</option>
                            <option value="name_asc">Name (A-Z)</option>
                            <option value="name_desc">Name (Z-A)</option>
                            <option value="length_desc">Largest First</option>
                            <option value="length_asc">Smallest First</option>
                        </select>
                    </div>

                    {{-- Per Page --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Per Page</label>
                        <select wire:model.live="perPage" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>

                {{-- Active Filters & Clear --}}
                @if($search || $filterType || $filterStatus)
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs text-gray-600">Active filters:</span>
                        @if($search)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Search: "{{ $search }}"
                                <button wire:click="$set('search', '')" class="ml-1.5 inline-flex items-center justify-center w-4 h-4 rounded-full hover:bg-blue-200">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </span>
                        @endif
                        @if($filterType)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Type: {{ $types[$filterType] ?? $filterType }}
                                <button wire:click="$set('filterType', '')" class="ml-1.5 inline-flex items-center justify-center w-4 h-4 rounded-full hover:bg-blue-200">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </span>
                        @endif
                        @if($filterStatus)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Status: {{ ucfirst($filterStatus) }}
                                <button wire:click="$set('filterStatus', '')" class="ml-1.5 inline-flex items-center justify-center w-4 h-4 rounded-full hover:bg-blue-200">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </span>
                        @endif
                        <button wire:click="clearFilters" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                            Clear All
                        </button>
                    </div>
                @endif
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-lg shadow-sm overflow-hidden overflow-x-auto">
                <table class="w-full text-left" style="table-layout: auto; min-width: 1000px;">
                    <colgroup>
                        <col style="width: 50px;">
                        <col style="width: 300px;">
                        <col style="width: 150px;">
                        <col style="width: 100px;">
                        <col style="width: 120px;">
                        <col style="width: 100px;">
                        <col style="width: 100px;">
                    </colgroup>
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Length</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Reviews</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($yachts as $yacht)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $yachts->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        @if(!empty($yacht->cover_image_url))
                                            <img src="{{ $yacht->cover_image_url }}" alt="{{ $yacht->name }}" class="w-10 h-10 rounded object-cover flex-shrink-0" onerror="this.style.display='none';">
                                        @endif
                                        <span class="text-sm font-medium text-gray-900 truncate" style="max-width: 250px;" title="{{ $yacht->name }}">{{ $yacht->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ ucfirst(str_replace('_', ' ', $yacht->type)) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                                    @if($yacht->length_meters)
                                        {{ number_format($yacht->length_meters, 1) }}m
                                    @elseif($yacht->length_feet)
                                        {{ number_format($yacht->length_feet, 0) }}ft
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $yacht->status === 'charter' ? 'bg-green-100 text-green-800' : ($yacht->status === 'private' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                        {{ ucfirst($yacht->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $yacht->reviews_count }} reviews</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button wire:click="openEditModal({{ $yacht->id }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button wire:click="deleteYacht({{ $yacht->id }})" onclick="return confirm('Are you sure you want to delete this yacht?')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                    No yachts found. Click "Add New Yacht" to get started.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($message)
                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-800">{{ $message }}</p>
                </div>
            @endif

            @if($error)
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-800">{{ $error }}</p>
                </div>
            @endif

            {{-- Pagination --}}
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-700">
                    Showing 
                    <span class="font-medium">{{ $yachts->firstItem() }}</span>
                    to 
                    <span class="font-medium">{{ $yachts->lastItem() }}</span>
                    of 
                    <span class="font-medium">{{ $yachts->total() }}</span>
                    results
                </div>
                @if($yachts->hasPages())
                    <div>
                        {{ $yachts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click="closeModal">
            <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $isEditMode ? 'Edit Yacht' : 'Add New Yacht' }}</h2>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                @if($error)
                    <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-800">{{ $error }}</p>
                    </div>
                @endif
                @if($message)
                    <div class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-green-800">{{ $message }}</p>
                    </div>
                @endif
                <form wire:submit.prevent="save" class="p-6 space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Yacht Name *</label>
                            <input type="text" wire:model="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                            <select wire:model="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Type</option>
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Length (meters)</label>
                            <input type="number" step="0.1" wire:model="length_meters" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Length (feet)</label>
                            <input type="number" step="0.1" wire:model="length_feet" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Year Built</label>
                            <input type="number" wire:model="year_built" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select wire:model="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Flag/Registry</label>
                            <input type="text" wire:model="flag_registry" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Home Port</label>
                            <input type="text" wire:model="home_port" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Crew Capacity</label>
                            <input type="number" wire:model="crew_capacity" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Guest Capacity</label>
                            <input type="number" wire:model="guest_capacity" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>
                        @if($cover_image_preview)
                            <img src="{{ $cover_image_preview }}" alt="Preview" class="w-32 h-32 object-cover rounded-lg mb-2">
                        @elseif($existing_cover_image)
                            <img src="{{ $existing_cover_image }}" alt="Current" class="w-32 h-32 object-cover rounded-lg mb-2">
                        @endif
                        <input type="file" wire:model="cover_image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        @error('cover_image') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex gap-4 pt-4 border-t border-gray-200">
                        <button type="submit" wire:loading.attr="disabled" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold disabled:opacity-50">
                            <span wire:loading.remove wire:target="save">{{ $isEditMode ? 'Update' : 'Create' }} Yacht</span>
                            <span wire:loading wire:target="save" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ $isEditMode ? 'Updating...' : 'Creating...' }}
                            </span>
                        </button>
                        <button type="button" wire:click="closeModal" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

