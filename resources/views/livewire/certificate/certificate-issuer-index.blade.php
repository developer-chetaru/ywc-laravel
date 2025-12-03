<div>

    @role('super_admin')
    <div class="p-3 sm:p-6 bg-white rounded-lg shadow-md">

        <!-- Title and Search -->
        <div class="mb-4 sm:mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 mb-3 sm:mb-2">
                <h2 class="text-lg sm:text-xl font-bold text-[#0053FF]">Certificate Issuer</h2>
                <input type="text" wire:model.debounce.300ms="search"
                       placeholder="Search certificate issuer..."
                       class="border p-2 rounded w-full sm:w-64 text-sm">
            </div>

            <!-- Add/Edit Form -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <input type="text" wire:model.defer="name"
                       placeholder="Enter certificate issuer"
                       class="border p-2 rounded flex-1 text-sm">

                <div class="flex items-center gap-2">
                    @if ($editId)
                        <button wire:click="updateCertificate"
                                class="px-3 sm:px-4 py-2 bg-[#0053FF] text-white rounded text-sm whitespace-nowrap">
                            Update
                        </button>
                        <button wire:click="cancelEdit"
                                class="px-3 py-2 text-gray-500 hover:text-red-600" title="Cancel">
                            <i class="fas fa-times"></i>
                        </button>
                    @else
                        <button wire:click="save"
                                class="px-3 sm:px-4 py-2 bg-[#0053FF] text-white rounded text-sm whitespace-nowrap" title="Add">
                            Add
                        </button>
                    @endif
                </div>
            </div>

            @error('name')
            <p class="text-[#0053FF] text-sm mt-1">{{ $message }}</p>
            @enderror

            @if (session()->has('message'))
                <div x-data="{ show: true }"
                     x-init="setTimeout(() => show = false, 3000)"
                     x-show="show"
                     x-transition
                     class="mt-3 text-[#0053FF] text-sm">
                    {{ session('message') }}
                </div>
            @endif
        </div>

        <!-- Issuers Table - Desktop View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full border-collapse border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border p-2 text-left text-sm">#</th>
                        <th class="border p-2 text-left text-sm">Issuer Name</th>
                        <th class="border p-2 text-center text-sm">Status</th>
                        <th class="border p-2 text-center text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($issuers as $index => $issuer)
                        <tr wire:key="issuer-{{ $issuer->id }}">
                            <td class="border p-2 text-sm">{{ $issuers->firstItem() + $index }}</td>
                            <td class="border p-2 text-sm">{{ $issuer->name }}</td>
                            <td class="border p-2 text-center">
                                <input type="checkbox"
                                       wire:change="toggleActive({{ $issuer->id }})"
                                       @if($issuer->is_active) checked @endif
                                       class="h-4 w-4 text-[#0053FF] rounded cursor-pointer">
                            </td>
                            <td class="border p-2 text-center">
                                <div class="flex justify-center gap-3">
                                    <button wire:click="edit({{ $issuer->id }})"
                                            class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="confirmDelete({{ $issuer->id }})"
                                            class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-3">
            @foreach ($issuers as $index => $issuer)
                <div wire:key="issuer-mobile-{{ $issuer->id }}" class="border rounded-lg p-4 bg-white shadow-sm">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs text-gray-500 font-medium">#{{ $issuers->firstItem() + $index }}</span>
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $issuer->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $issuer->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900">{{ $issuer->name }}</h3>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox"
                                   wire:change="toggleActive({{ $issuer->id }})"
                                   @if($issuer->is_active) checked @endif
                                   class="h-4 w-4 text-[#0053FF] rounded cursor-pointer">
                            <span>Toggle Status</span>
                        </label>
                        <div class="flex items-center gap-3">
                            <button wire:click="edit({{ $issuer->id }})"
                                    class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded"
                                    title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button wire:click="confirmDelete({{ $issuer->id }})"
                                    class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded"
                                    title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
      
        <!-- Pagination -->
        <div class="mt-4 sm:mt-6">
            {{ $issuers->links('livewire.custom-pagination') }}
        </div>

        <!-- Confirm Delete Modal -->
        @if ($showConfirm)
            <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 p-4">
                <div class="bg-white p-4 sm:p-6 rounded-lg shadow-lg max-w-sm w-full">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Are you sure?</h2>
                    <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">Do you really want to delete this issuer?</p>
                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3">
                        <button wire:click="$set('showConfirm', false)"
                                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm sm:text-base">
                            Cancel
                        </button>
                        <button wire:click="delete"
                                class="px-4 py-2 bg-[#0053FF] text-white rounded hover:bg-[#0053FF] text-sm sm:text-base">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @elserole('user')
    <div class="flex-1 flex flex-col overflow-hidden">
        <div class="flex min-h-screen bg-gray-100">
            <div class="flex-1 transition-all duration-300">
                <div class="p-6">
                    <p class="text-gray-500 text-lg font-medium">Certificate Issuer Management Coming Soon...</p>
                </div>
            </div>
        </div>
    </div>
    @endrole

</div>
