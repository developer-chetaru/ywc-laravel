<div>
    <main class="flex-1 flex flex-col bg-gray-100 p-4 sm:p-6">
        <div class="max-w-7xl mx-auto w-full">
            <div class="bg-white rounded-xl shadow-md p-6">
                {{-- Header --}}
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Share Templates</h1>
                        <p class="text-sm text-gray-600 mt-1">Create reusable templates for sharing documents</p>
                    </div>
                    <button wire:click="openCreateModal" 
                        class="bg-[#0053FF] text-white px-4 py-2 rounded-md hover:bg-[#0044DD] transition-colors text-sm font-medium">
                        <i class="fas fa-plus mr-2"></i>Create Template
                    </button>
                </div>

                {{-- Flash Message --}}
                @if(session()->has('message'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4">
                    <p class="text-sm text-green-700">{{ session('message') }}</p>
                </div>
                @endif

                {{-- Templates List --}}
                @if($templates->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($templates as $template)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-1">
                                    {{ $template->name }}
                                    @if($template->is_default)
                                    <span class="ml-2 px-2 py-0.5 text-xs font-medium rounded bg-blue-100 text-blue-800">Default</span>
                                    @endif
                                </h3>
                                @if($template->description)
                                <p class="text-sm text-gray-600 mb-2">{{ $template->description }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="space-y-2 text-sm text-gray-600 mb-4">
                            <p><strong>Expiry:</strong> {{ $template->expiry_duration_days }} days</p>
                            @if($template->default_message)
                            <p><strong>Message:</strong> {{ Str::limit($template->default_message, 50) }}</p>
                            @endif
                        </div>
                        
                        <div class="flex gap-2">
                            <button wire:click="openEditModal({{ $template->id }})" 
                                class="flex-1 bg-blue-100 text-blue-700 px-3 py-2 rounded-md hover:bg-blue-200 transition-colors text-sm">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            <button wire:click="deleteTemplate({{ $template->id }})" 
                                onclick="return confirm('Are you sure you want to delete this template?')"
                                class="flex-1 bg-red-100 text-red-700 px-3 py-2 rounded-md hover:bg-red-200 transition-colors text-sm">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12">
                    <i class="fas fa-file-alt text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">No templates created yet</p>
                    <button wire:click="openCreateModal" 
                        class="mt-4 bg-[#0053FF] text-white px-6 py-2 rounded-md hover:bg-[#0044DD] transition-colors">
                        Create Your First Template
                    </button>
                </div>
                @endif
            </div>
        </div>
    </main>

    {{-- Create Template Modal --}}
    @if($showCreateModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
         wire:click="closeCreateModal">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white" 
             wire:click.stop>
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Create Share Template</h3>
                
                <form wire:submit.prevent="createTemplate">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Template Name <span class="text-red-500">*</span></label>
                        <input type="text" 
                            wire:model="name" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]"
                            placeholder="e.g., Job Application">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                        <textarea wire:model="description" 
                            rows="2" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]"
                            placeholder="Describe when to use this template..."></textarea>
                        @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Duration (Days) <span class="text-red-500">*</span></label>
                        <input type="number" 
                            wire:model="expiryDurationDays" 
                            min="1" 
                            max="3650"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]">
                        @error('expiryDurationDays') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Default Message (Optional)</label>
                        <textarea wire:model="defaultMessage" 
                            rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]"
                            placeholder="Default message to include when sharing..."></textarea>
                        @error('defaultMessage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" 
                                wire:model="isDefault" 
                                class="rounded border-gray-300 text-[#0053FF] focus:ring-[#0053FF]">
                            <span class="text-sm text-gray-700">Set as default template</span>
                        </label>
                    </div>
                    
                    <div class="flex gap-2">
                        <button type="submit" 
                            class="flex-1 bg-[#0053FF] text-white px-4 py-2 rounded-md hover:bg-[#0044DD] transition-colors">
                            Create Template
                        </button>
                        <button type="button" 
                            wire:click="closeCreateModal" 
                            class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Edit Template Modal --}}
    @if($showEditModal && $editingTemplate)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
         wire:click="closeEditModal">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white" 
             wire:click.stop>
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Share Template</h3>
                
                <form wire:submit.prevent="updateTemplate">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Template Name <span class="text-red-500">*</span></label>
                        <input type="text" 
                            wire:model="name" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                        <textarea wire:model="description" 
                            rows="2" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]"></textarea>
                        @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Duration (Days) <span class="text-red-500">*</span></label>
                        <input type="number" 
                            wire:model="expiryDurationDays" 
                            min="1" 
                            max="3650"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]">
                        @error('expiryDurationDays') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Default Message (Optional)</label>
                        <textarea wire:model="defaultMessage" 
                            rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#0053FF] focus:border-[#0053FF]"></textarea>
                        @error('defaultMessage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" 
                                wire:model="isDefault" 
                                class="rounded border-gray-300 text-[#0053FF] focus:ring-[#0053FF]">
                            <span class="text-sm text-gray-700">Set as default template</span>
                        </label>
                    </div>
                    
                    <div class="flex gap-2">
                        <button type="submit" 
                            class="flex-1 bg-[#0053FF] text-white px-4 py-2 rounded-md hover:bg-[#0044DD] transition-colors">
                            Update Template
                        </button>
                        <button type="button" 
                            wire:click="closeEditModal" 
                            class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
