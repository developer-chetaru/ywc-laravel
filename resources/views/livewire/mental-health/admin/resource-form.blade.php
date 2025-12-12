<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('mental-health.admin.resources') }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-800">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Resource Management
            </a>
        </div>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                {{ $isEditing ? 'Edit Resource' : 'Create New Resource' }}
            </h1>
            <p class="mt-2 text-gray-600">
                {{ $isEditing ? 'Update resource information' : 'Add a new mental health resource' }}
            </p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form wire:submit.prevent="save" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" wire:model="title" 
                           class="w-full rounded-md border-gray-300 shadow-sm">
                    @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea wire:model="description" rows="3" 
                              class="w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                        <input type="text" wire:model="category" 
                               placeholder="e.g., anxiety, depression, stress"
                               class="w-full rounded-md border-gray-300 shadow-sm">
                        @error('category') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Resource Type *</label>
                        <select wire:model="resource_type" class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="article">Article</option>
                            <option value="video">Video</option>
                            <option value="audio">Audio</option>
                            <option value="worksheet">Worksheet</option>
                            <option value="pdf">PDF</option>
                        </select>
                        @error('resource_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                @if($resource_type === 'article')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                        <textarea wire:model="content" rows="10" 
                                  class="w-full rounded-md border-gray-300 shadow-sm"></textarea>
                    </div>
                @else
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">File Path</label>
                        <input type="text" wire:model="file_path" 
                               placeholder="/path/to/file"
                               class="w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                    <div class="flex gap-2 mb-2">
                        <input type="text" wire:model="tagInput" 
                               placeholder="Enter tags separated by commas"
                               class="flex-1 rounded-md border-gray-300 shadow-sm">
                        <button type="button" wire:click="addTag" 
                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md">
                            Add
                        </button>
                    </div>
                    @if(count($tags) > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($tags as $tag)
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm flex items-center">
                                    {{ $tag }}
                                    <button type="button" wire:click="removeTag('{{ $tag }}')" 
                                            class="ml-2 text-blue-600 hover:text-blue-800">×</button>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reading Time (minutes)</label>
                        <input type="number" wire:model="reading_time_minutes" min="0" 
                               class="w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Difficulty Level</label>
                        <select wire:model="difficulty_level" class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                        <select wire:model="status" class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                        @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                        <input type="text" wire:model="author" 
                               class="w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Publication Date</label>
                        <input type="date" wire:model="publication_date" 
                               class="w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-2 pt-4 border-t">
                    <a href="{{ route('mental-health.admin.resources') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                        {{ $isEditing ? 'Update Resource' : 'Create Resource' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

