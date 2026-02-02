<div>
    @if(!isset($category) || is_null($category))
        <div class="p-4 bg-red-100 border border-red-300 rounded-lg">
            <p class="text-red-800">Error: Category not found. Please go back and select a valid category.</p>
            <a href="{{ route('forum.category.index') }}" class="text-blue-600 hover:underline">Return to Forums</a>
        </div>
    @else
    <div class="w-full max-w-4xl mx-auto px-4 py-6">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 mb-2">{{ trans('forum::threads.new_thread') }}</h1>
            <p class="text-sm text-gray-600">Category: <span class="font-medium text-gray-800">{{ $category->title ?? 'Unknown Category' }}</span></p>
            
            {{-- Source Module Info --}}
            @if ($source_module && $source_item_title)
                <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center gap-2 text-sm text-blue-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium">Discussing:</span>
                        <span>{{ $source_item_title }}</span>
                        @if ($source_item_url)
                            <a href="{{ $source_item_url }}" target="_blank" class="text-blue-600 hover:underline ml-2">
                                (View Original)
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 md:p-8">
            <form wire:submit="create">
                {{-- Hidden fields for source module --}}
                @if ($source_module)
                    <input type="hidden" wire:model="source_module">
                    <input type="hidden" wire:model="source_item_id">
                    <input type="hidden" wire:model="source_item_type">
                @endif
                
                <div class="mb-6">
                    <x-forum::form.input-text
                        id="title"
                        value=""
                        :label="trans('forum::general.title')"
                        wire:model="title" />
                </div>

                <div class="mb-6">
                    <x-forum::form.input-textarea
                        id="content"
                        wire:model="content" />
                </div>

                {{-- Mark as Question --}}
                <div class="mb-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="is_question" 
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-700">
                            Mark as Question (allows marking best answer)
                        </span>
                    </label>
                </div>

                {{-- Role Restriction Selector --}}
                <div class="mb-6">
                    <button type="button" wire:click="toggleRoleSelector"
                        class="flex items-center gap-2 text-gray-700 font-medium text-sm mb-3 hover:text-blue-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Restrict Access to Specific Roles
                    </button>

                    @if ($showRoleSelector)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Select Roles (Leave empty for public access)
                            </label>

                            <!-- Search -->
                            <div class="relative mb-4">
                                <input type="search" wire:model.live="searchRole" placeholder="Search roles by name"
                                    class="text-gray-700 placeholder-gray-400 w-full py-2 px-4 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 text-sm pl-10">
                                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>

                            <!-- Role List -->
                            <div class="max-h-60 overflow-y-auto rounded-lg p-3 bg-white border border-gray-300">
                                <ul class="space-y-2 text-sm text-gray-600">
                                    @forelse ($roles as $role)
                                        <li class="flex items-center gap-2">
                                            <input type="checkbox" wire:model="selectedRoles" value="{{ $role->id }}"
                                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <span>{{ $role->name }}</span>
                                        </li>
                                    @empty
                                        <li class="text-gray-500 italic">No roles found.</li>
                                    @endforelse
                                </ul>
                            </div>

                            <!-- Selected Roles -->
                            @if (!empty($selectedRoles))
                                <div class="flex flex-wrap gap-2 mt-4">
                                    @foreach ($selectedRoles as $id)
                                        @php $r = $roles->firstWhere('id', $id); @endphp
                                        @if ($r)
                                            <span class="flex items-center bg-blue-100 text-blue-800 px-3 py-1 text-sm border border-blue-300 rounded">
                                                {{ $r->name }}
                                                <button wire:click="removeRole({{ $id }})" type="button"
                                                    class="ml-2 text-blue-600 hover:text-red-600">✕</button>
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <a href="{{ URL::previous() }}" 
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        {{ trans('forum::general.cancel') }}
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        {{ trans('forum::general.create') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
