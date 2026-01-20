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
                    <form action="{{ route('share-templates-new.store') }}" method="POST">
                        @csrf

                        <div class="space-y-6">
                            <!-- Basic Info -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Template Name *</label>
                                <input type="text" name="name" required value="{{ old('name') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                                       placeholder="e.g., Employer Share, Quick Share">
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea name="description" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                                          placeholder="Optional description...">{{ old('description') }}</textarea>
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
                                        <input type="number" name="max_views" min="1"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                               placeholder="Leave empty for unlimited">
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
                                        <input type="number" name="duration_days" min="1" max="365" value="30"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                    </div>

                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_access_window" value="1" class="mr-2">
                                        <span class="text-sm">Use access time window (start/end dates)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end gap-3 pt-6 border-t">
                                <a href="{{ route('share-templates-new.index') }}"
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
