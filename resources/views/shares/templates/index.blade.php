<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Share Templates
            </h2>
            <a href="{{ route('share-templates-new.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Create Template
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-md">
                <p class="text-green-800"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</p>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($templates as $template)
                        <div class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $template->name }}
                                    @if($template->is_default)
                                    <span class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Default</span>
                                    @endif
                                </h3>
                            </div>

                            @if($template->description)
                            <p class="text-sm text-gray-600 mb-4">{{ Str::limit($template->description, 100) }}</p>
                            @endif

                            <div class="space-y-2 mb-4">
                                <div class="text-xs text-gray-700">
                                    <strong>Permissions:</strong>
                                    <span class="text-gray-600">{{ $template->permissions_summary }}</span>
                                </div>
                                <div class="text-xs text-gray-700">
                                    <strong>Restrictions:</strong>
                                    <span class="text-gray-600">{{ $template->restrictions_summary }}</span>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-chart-line mr-1"></i>Used {{ $template->usage_count }} times
                                </div>
                            </div>

                            <div class="flex gap-2">
                                @if(!$template->is_default || $template->user_id === Auth::id())
                                <a href="{{ route('share-templates-new.edit', $template->id) }}"
                                   class="flex-1 text-center px-3 py-2 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 text-sm">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                @endif

                                @if($template->user_id === Auth::id())
                                <form action="{{ route('share-templates-new.destroy', $template->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this template?')"
                                            class="w-full px-3 py-2 bg-red-50 text-red-700 rounded-md hover:bg-red-100 text-sm">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full text-center py-12">
                            <i class="fas fa-folder-open text-gray-300 text-6xl mb-4"></i>
                            <p class="text-gray-500 mb-4">No templates yet</p>
                            <a href="{{ route('share-templates-new.create') }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                <i class="fas fa-plus mr-2"></i>Create Your First Template
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
