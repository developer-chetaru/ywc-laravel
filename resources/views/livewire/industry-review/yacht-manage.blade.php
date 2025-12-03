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
                @can('create', \App\Models\Yacht::class)
                    <a href="{{ route('industryreview.yachts.create') }}" class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add New Yacht
                    </a>
                @endcan
            </div>

            @if (session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 text-green-700 px-4 py-3 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if($isCaptain && !auth()->user()->current_yacht)
                <div class="mb-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 px-4 py-3 rounded-md">
                    <p class="font-medium">No Current Yacht Set</p>
                    <p class="text-sm">Please set your current yacht in your profile to view and manage it here.</p>
                </div>
            @endif

            @if(auth()->user() && auth()->user()->hasRole('Captain'))
                <div class="mb-4 bg-blue-50 border-l-4 border-blue-400 text-blue-700 px-4 py-3 rounded-md">
                    <p class="font-medium">Captain Access</p>
                    <p class="text-sm">As a Captain, you can add and manage yachts that match your current yacht. The yacht name must match your profile's current yacht setting.</p>
                </div>
            @endif

            @if(auth()->user() && (auth()->user()->hasRole('Crew Member') || auth()->user()->hasRole('crew_member')))
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 text-green-700 px-4 py-3 rounded-md">
                    <p class="font-medium">Crew Member Access</p>
                    <p class="text-sm">As a Crew Member, you can add yachts you've worked on to the system. You can edit yachts you've added, but cannot delete them.</p>
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
                            @foreach($yachtTypes as $yachtType)
                                <option value="{{ $yachtType->code }}">{{ $yachtType->name }}</option>
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
                                Type: {{ $yachtTypes->where('code', $filterType)->first()->name ?? $filterType }}
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
                        <col style="width: 120px;">
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
                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Added By</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Members</th>
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
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    @if($yacht->createdBy)
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ $yacht->createdBy->first_name }} {{ $yacht->createdBy->last_name }}</span>
                                            <span class="text-xs text-gray-500">
                                                @if($yacht->added_by_role === 'super_admin')
                                                    Super Admin
                                                @elseif($yacht->added_by_role === 'captain')
                                                    Captain
                                                @elseif($yacht->added_by_role === 'crew_member')
                                                    Crew Member
                                                @else
                                                    {{ ucfirst($yacht->added_by_role ?? 'Unknown') }}
                                                @endif
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($yacht->member_count > 0)
                                        <a href="{{ route('industryreview.yachts.members', $yacht->id) }}" 
                                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors cursor-pointer">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            {{ $yacht->member_count }}
                                        </a>
                                    @else
                                        <span class="text-sm text-gray-400">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @can('update', $yacht)
                                            <a href="{{ route('industryreview.yachts.edit', $yacht->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                        @endcan
                                        @can('delete', $yacht)
                                            <button wire:click="deleteYacht({{ $yacht->id }})" onclick="return confirm('Are you sure you want to delete this yacht?')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center text-gray-500">
                                    @if($isCaptain)
                                        No yacht found. Please set your current yacht in your profile.
                                    @else
                                        No yachts found. Click "Add New Yacht" to get started.
                                    @endif
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

</div>

