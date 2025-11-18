<div class="bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">All Users Documents</h2>
        <p class="text-sm text-gray-600">Manage and review documents for all users</p>
    </div>

    {{-- Search and Filters --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form id="userSearchForm" method="GET" action="{{ route('documents') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[250px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Users</label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Search by name or email..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                <select name="sort" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Search
                </button>
                @if(request('search') || request('sort'))
                    <a href="{{ route('documents') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $users->total() }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Pending</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $users->sum('pending_count') }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Approved</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $users->sum('approved_count') }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Rejected</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $users->sum('rejected_count') }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Total</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Pending</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Approved</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Rejected</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Action</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                @if($user->profile_photo_path)
                                    <img src="{{ asset('storage/'.$user->profile_photo_path) }}" 
                                         class="w-10 h-10 rounded-full object-cover border-2 border-gray-200" alt="">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center border-2 border-gray-200">
                                        <span class="text-blue-600 font-semibold text-sm">
                                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </p>
                                    <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                {{ $user->documents_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if(($user->pending_count ?? 0) > 0)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    {{ $user->pending_count }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400">0</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if(($user->approved_count ?? 0) > 0)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    {{ $user->approved_count }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400">0</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if(($user->rejected_count ?? 0) > 0)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    {{ $user->rejected_count }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400">0</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('career-history.show', $user->id) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View Documents
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <p class="text-gray-500 text-lg font-medium">No users found</p>
                            @if(request('search'))
                                <p class="text-gray-400 text-sm mt-1">Try adjusting your search criteria</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($users->hasPages())
    <div class="mt-6 bg-white rounded-lg shadow-sm p-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-700">
                <p class="font-medium">
                    Showing 
                    <span class="font-bold text-gray-900">{{ $users->firstItem() ?? 0 }}</span>
                    to 
                    <span class="font-bold text-gray-900">{{ $users->lastItem() ?? 0 }}</span>
                    of 
                    <span class="font-bold text-gray-900">{{ $users->total() }}</span>
                    results
                </p>
            </div>

            <div class="flex items-center gap-2">
                {{-- Previous Page --}}
                @if ($users->onFirstPage())
                    <button disabled class="px-4 py-2 rounded-lg border bg-gray-100 text-gray-400 cursor-not-allowed flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Prev
                    </button>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="px-4 py-2 rounded-lg border bg-white text-gray-700 hover:bg-blue-600 hover:text-white transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Prev
                    </a>
                @endif

                {{-- Page Numbers --}}
                @php
                    $currentPage = $users->currentPage();
                    $lastPage = $users->lastPage();
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($lastPage, $currentPage + 2);
                @endphp

                @if($startPage > 1)
                    <a href="{{ $users->url(1) }}" class="px-3 py-2 rounded-lg border bg-white text-gray-700 hover:bg-blue-600 hover:text-white transition-colors">1</a>
                    @if($startPage > 2)
                        <span class="px-2 text-gray-400">...</span>
                    @endif
                @endif

                @for ($page = $startPage; $page <= $endPage; $page++)
                    @if ($page == $currentPage)
                        <button class="px-3 py-2 rounded-lg border bg-blue-600 text-white font-semibold">{{ $page }}</button>
                    @else
                        <a href="{{ $users->url($page) }}" class="px-3 py-2 rounded-lg border bg-white text-gray-700 hover:bg-blue-600 hover:text-white transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endfor

                @if($endPage < $lastPage)
                    @if($endPage < $lastPage - 1)
                        <span class="px-2 text-gray-400">...</span>
                    @endif
                    <a href="{{ $users->url($lastPage) }}" class="px-3 py-2 rounded-lg border bg-white text-gray-700 hover:bg-blue-600 hover:text-white transition-colors">{{ $lastPage }}</a>
                @endif

                {{-- Next Page --}}
                @if ($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="px-4 py-2 rounded-lg border bg-white text-gray-700 hover:bg-blue-600 hover:text-white transition-colors flex items-center gap-1">
                        Next
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                @else
                    <button disabled class="px-4 py-2 rounded-lg border bg-gray-100 text-gray-400 cursor-not-allowed flex items-center gap-1">
                        Next
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    </div>
    @endif

</div>

