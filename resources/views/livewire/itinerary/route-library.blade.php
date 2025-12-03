@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="py-4 sm:py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">
        {{-- Header Section --}}
        <div class="bg-white shadow-lg rounded-xl p-4 sm:p-6 border border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4 sm:mb-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Itinerary Library</h1>
                    <p class="text-xs sm:text-sm text-gray-600">Browse curated voyages, public itineraries, and your private drafts</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <a href="{{ route('itinerary.routes.planner') }}"
                       class="inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white text-sm sm:text-base font-semibold rounded-lg shadow-md hover:from-indigo-700 hover:to-indigo-800 transition-all transform hover:scale-105">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span class="hidden sm:inline">Create Itinerary</span>
                        <span class="sm:hidden">Create</span>
                    </a>
                    <button wire:click="clearFilters"
                            class="inline-flex items-center justify-center px-3 sm:px-4 py-2.5 sm:py-3 border-2 border-gray-300 rounded-lg text-xs sm:text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all">
                        <svg class="w-4 h-4 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span class="hidden sm:inline">Reset Filters</span>
                        <span class="sm:hidden">Reset</span>
                    </button>
                </div>
            </div>

            @if (session('status'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 text-green-700 px-4 py-3 rounded-md shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('status') }}
                    </div>
                </div>
            @endif

            {{-- Filters Section - Horizontal Layout --}}
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-lg p-3 sm:p-4 border border-indigo-100">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-8 gap-3 sm:gap-4">
                    {{-- Search --}}
                    <div class="sm:col-span-2 lg:col-span-2 xl:col-span-2 2xl:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Search</label>
                        <div class="relative flex flex-col sm:flex-row gap-2">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 sm:h-5 sm:w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" 
                                       wire:model.live.debounce.500ms="search"
                                       wire:key="search-input"
                                       placeholder="Search..."
                                       class="block w-full pl-9 sm:pl-10 pr-9 sm:pr-10 py-2 sm:py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-sm">
                                @if(!empty($search))
                                    <button type="button" 
                                            wire:click="$set('search', '')"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                            title="Clear search">
                                        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            <button type="button"
                                    wire:click="applyFilters"
                                    class="px-3 sm:px-4 py-2 sm:py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors whitespace-nowrap flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <span class="ml-1.5 sm:hidden">Search</span>
                            </button>
                        </div>
                    </div>

                    {{-- Region --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Region</label>
                        <select wire:model.live="region"
                                class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-sm">
                            <option value="">All Regions</option>
                            @foreach($regions as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Difficulty --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Difficulty</label>
                        <select wire:model.live="difficulty"
                                class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-sm">
                            <option value="">Any</option>
                            @foreach($difficulties as $option)
                                <option value="{{ $option }}">{{ ucfirst($option) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Season --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Season</label>
                        <select wire:model.live="season"
                                class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-sm">
                            <option value="">Any</option>
                            @foreach($seasons as $option)
                                <option value="{{ $option }}">{{ ucfirst($option) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Days --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Days</label>
                        <select wire:model.live="days"
                                class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-sm">
                            <option value="">Any</option>
                            @if(count($availableDays) > 0)
                                @foreach($availableDays as $day)
                                    <option value="{{ $day }}">{{ $day }} {{ $day == 1 ? 'Day' : 'Days' }}</option>
                                @endforeach
                            @else
                                {{-- Fallback options if no routes exist yet --}}
                                <option value="1">1 Day</option>
                                <option value="2">2 Days</option>
                                <option value="3">3 Days</option>
                                <option value="4">4 Days</option>
                                <option value="5">5 Days</option>
                                <option value="7">7 Days</option>
                                <option value="10">10 Days</option>
                                <option value="14">14 Days</option>
                                <option value="21">21 Days</option>
                                <option value="30">30+ Days</option>
                            @endif
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Status</label>
                        <select wire:model.live="status"
                                class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-sm">
                            <option value="">All</option>
                            @foreach($routeStatus as $status)
                                <option value="{{ $status->code }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Visibility --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Visibility</label>
                        <select wire:model.live="visibility"
                                class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-sm">
                            <option value="">All</option>
                            @foreach($routeVisibility as $visibility)
                                <option value="{{ $visibility->code }}">{{ $visibility->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Templates Toggle --}}
                <div class="mt-4 flex items-center gap-2">
                    <input type="checkbox" wire:model.live="templates" id="templates-toggle"
                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                    <label for="templates-toggle" class="text-sm font-medium text-gray-700 cursor-pointer">
                        Show templates only
                    </label>
                </div>
            </div>
        </div>

        {{-- Routes Grid --}}
        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
            @if($routes->isEmpty())
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No routes found</h3>
                    <p class="text-gray-500 mb-4">Try adjusting your filters or create a new route to get started.</p>
                    <a href="{{ route('itinerary.routes.planner') }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        Create Your First Route
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    @foreach($routes as $route)
                        <article class="group border border-gray-200 rounded-xl shadow-md bg-white flex flex-col overflow-hidden hover:shadow-xl transition-all duration-300">
                            {{-- Cover Image --}}
                            <div class="relative h-40 sm:h-48 bg-gradient-to-br from-indigo-100 to-blue-100 overflow-hidden">
                                @if($route->cover_image)
                                    <img src="{{ Storage::url($route->cover_image) }}" 
                                         alt="{{ $route->title }}" 
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-20 h-20 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                @endif
                                {{-- Status Badges --}}
                                <div class="absolute top-2 right-2 sm:top-3 sm:right-3 flex flex-col gap-1.5 sm:gap-2">
                                    <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 text-[10px] sm:text-xs font-bold uppercase tracking-wide rounded-full shadow-md {{ $route->visibility === 'public' ? 'bg-green-500 text-white' : ($route->visibility === 'crew' ? 'bg-blue-500 text-white' : 'bg-gray-700 text-white') }}">
                                        {{ ucfirst($route->visibility) }}
                                    </span>
                                    <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 text-[10px] sm:text-xs font-bold uppercase tracking-wide rounded-full shadow-md {{ $route->status === 'active' ? 'bg-green-500 text-white' : ($route->status === 'completed' ? 'bg-indigo-500 text-white' : 'bg-yellow-500 text-white') }}">
                                        {{ ucfirst($route->status) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-4 sm:p-5 flex-1 flex flex-col space-y-2 sm:space-y-3">
                                <h2 class="text-lg sm:text-xl font-bold text-gray-900 line-clamp-2 group-hover:text-indigo-600 transition-colors leading-tight">
                                    <a href="{{ route('itinerary.routes.show', $route) }}">
                                        {{ $route->title }}
                                    </a>
                                </h2>

                                @if($route->description)
                                    <p class="text-xs sm:text-sm text-gray-600 line-clamp-2 flex-grow">{{ $route->description }}</p>
                                @else
                                    <p class="text-xs sm:text-sm text-gray-400 italic line-clamp-2">No description provided.</p>
                                @endif

                                {{-- Route Details Grid --}}
                                <dl class="grid grid-cols-2 gap-2 sm:gap-3 text-[10px] sm:text-xs">
                                    <div class="bg-gray-50 rounded-lg p-2">
                                        <dt class="font-semibold text-gray-500 uppercase tracking-wide mb-1">Region</dt>
                                        <dd class="text-gray-900 font-medium">{{ $route->region ?: '—' }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-2">
                                        <dt class="font-semibold text-gray-500 uppercase tracking-wide mb-1">Difficulty</dt>
                                        <dd class="text-gray-900 font-medium">{{ $route->difficulty ?: '—' }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-2">
                                        <dt class="font-semibold text-gray-500 uppercase tracking-wide mb-1">Distance</dt>
                                        <dd class="text-gray-900 font-medium">{{ number_format($route->distance_nm, 2) }} NM</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-2">
                                        <dt class="font-semibold text-gray-500 uppercase tracking-wide mb-1">Duration</dt>
                                        <dd class="text-gray-900 font-medium">{{ $route->duration_days }} days</dd>
                                    </div>
                                </dl>

                                {{-- Author & Rating --}}
                                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                    <div class="flex items-center gap-2 text-xs text-gray-600">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span class="font-medium">{{ optional($route->owner)->first_name }} {{ optional($route->owner)->last_name }}</span>
                                    </div>
                                    @if($route->statistics && $route->statistics->reviews_count > 0)
                                        <div class="flex items-center gap-1 text-xs font-semibold text-yellow-600">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"></path>
                                            </svg>
                                            <span>{{ number_format($route->statistics->rating_avg, 1) }}</span>
                                            <span class="text-gray-500">({{ $route->statistics->reviews_count }})</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-1 text-xs text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                            </svg>
                                            <span>No reviews</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="border-t border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 p-2.5 sm:p-3">
                                <div class="flex items-center justify-between gap-2">
                                    <a href="{{ route('itinerary.routes.show', $route) }}"
                                       class="flex-1 inline-flex items-center justify-center px-3 py-2.5 text-xs sm:text-sm font-medium text-indigo-700 bg-white border border-indigo-200 rounded-lg hover:bg-indigo-50 hover:border-indigo-300 transition-all min-w-0"
                                       title="View Route">
                                        <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <span class="truncate">View</span>
                                    </a>
                                    <a href="{{ route('itinerary.routes.planner') }}?template={{ $route->id }}"
                                       class="flex-1 inline-flex items-center justify-center px-3 py-2.5 text-xs sm:text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-lg hover:from-indigo-700 hover:to-indigo-800 transition-all shadow-sm min-w-0"
                                       title="Edit Route">
                                        <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span class="truncate">Edit</span>
                                    </a>
                                    @php
                                        $user = Auth::user();
                                        $canDelete = $user && ($user->hasRole('super_admin') || $route->user_id === $user->id);
                                    @endphp
                                    @if($canDelete)
                                        <button wire:click="deleteRoute({{ $route->id }})"
                                                wire:confirm="Are you sure you want to delete this route? This action cannot be undone."
                                                class="flex-1 inline-flex items-center justify-center px-3 py-2.5 text-xs sm:text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-all min-w-0"
                                                title="Delete Route">
                                            <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            <span class="truncate">Delete</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $routes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
