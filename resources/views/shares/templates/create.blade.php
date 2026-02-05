<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Share Template
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    There were {{ $errors->count() }} error(s) with your submission:
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <form action="{{ route('share-templates.store') }}" method="POST">
                        @csrf

                        <div class="space-y-6">
                            <!-- Basic Info -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Template Name *</label>
                                <input type="text" name="name" required value="{{ old('name') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                                       placeholder="e.g., Employer Share, Quick Share">
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea name="description" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                                          placeholder="Optional description...">{{ old('description') }}</textarea>
                                @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Permissions -->
                            <div class="border-t pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Default Permissions</h3>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="can_download" value="1" checked class="mr-2">
                                        <span class="text-sm">Allow Download</span>
                                    </label>

                                    <label class="flex items-center">
                                        <input type="checkbox" name="can_print" value="1" checked class="mr-2">
                                        <span class="text-sm">Allow Print</span>
                                    </label>

                                    <label class="flex items-center">
                                        <input type="checkbox" name="can_share" value="1" class="mr-2">
                                        <span class="text-sm">Allow Re-sharing</span>
                                    </label>

                                    <label class="flex items-center">
                                        <input type="checkbox" name="can_comment" value="1" class="mr-2">
                                        <span class="text-sm">Allow Comments</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Access Control -->
                            <div class="border-t pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Access Control</h3>
                                <div class="space-y-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_one_time" value="1" class="mr-2">
                                        <span class="text-sm">One-time access (expires after first view)</span>
                                    </label>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Views</label>
                                        <input type="number" name="max_views" min="1" value="{{ old('max_views') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md @error('max_views') border-red-500 @enderror"
                                               placeholder="Leave empty for unlimited">
                                        @error('max_views')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <label class="flex items-center">
                                        <input type="checkbox" name="require_password" value="1" class="mr-2">
                                        <span class="text-sm">Require password by default</span>
                                    </label>

                                    <label class="flex items-center">
                                        <input type="checkbox" name="require_watermark" value="1" class="mr-2">
                                        <span class="text-sm">Apply watermark to documents</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Time Settings -->
                            <div class="border-t pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Time Settings</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Default Duration (Days)</label>
                                        <input type="number" name="duration_days" min="1" max="365" value="{{ old('duration_days', 30) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md @error('duration_days') border-red-500 @enderror">
                                        @error('duration_days')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_access_window" value="1" class="mr-2">
                                        <span class="text-sm">Use access time window (start/end dates)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end gap-3 pt-6 border-t">
                                <a href="{{ route('share-templates.index') }}"
                                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                    Cancel
                                </a>
                                <button type="submit"
                                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    <i class="fas fa-save mr-2"></i>Create Template
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
