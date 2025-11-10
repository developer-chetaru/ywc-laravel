<div>

    @role('super_admin')
    <div class="p-6 bg-white rounded-lg shadow-md">

        <!-- Title and Search -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-xl font-bold text-[#0053FF]">Certificate Issuer</h2>
                <input type="text" wire:model.debounce.300ms="search"
                       placeholder="Search certificate issuer..."
                       class="border p-2 rounded w-64">
            </div>

            <!-- Add/Edit Form -->
            <div class="flex items-center gap-2">
                <input type="text" wire:model.defer="name"
                       placeholder="Enter certificate issuer"
                       class="border p-2 rounded flex-1">

                @if ($editId)
                    <button wire:click="updateCertificate"
                            class="px-4 py-2 bg-[#0053FF] text-white rounded">
                        Update
                    </button>
                    <button wire:click="cancelEdit"
                            class="text-gray-500 hover:text-red-600" title="Cancel">
                        <i class="fas fa-times"></i>
                    </button>
                @else
                    <button wire:click="save"
                            class="px-4 py-2 bg-[#0053FF] text-white rounded" title="Add">
                        Add
                    </button>
                @endif
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

        <!-- Issuers Table -->
        <table class="w-full border-collapse border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2 text-left">#</th>
                    <th class="border p-2 text-left">Issuer Name</th>
                    <th class="border p-2 text-center">Status</th>
                    <th class="border p-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($issuers as $index => $issuer)
                    <tr wire:key="issuer-{{ $issuer->id }}">
                        <td class="border p-2">{{ $issuers->firstItem() + $index }}</td>
                        <td class="border p-2">{{ $issuer->name }}</td>
                        <td class="border p-2 text-center">
                            <input type="checkbox"
                                   wire:change="toggleActive({{ $issuer->id }})"
                                   @if($issuer->is_active) checked @endif
                                   class="h-4 w-4 text-[#0053FF] rounded cursor-pointer">
                        </td>
                        <td class="border p-2 text-center flex justify-center gap-3">
                            <button wire:click="edit({{ $issuer->id }})"
                                    class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button wire:click="confirmDelete({{ $issuer->id }})"
                                    class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
      
        <!-- Pagination -->
        <div class="mt-6">
            {{ $issuers->links('livewire.custom-pagination') }}
        </div>

        <!-- Confirm Delete Modal -->
        @if ($showConfirm)
            <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
                <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Are you sure?</h2>
                    <p class="text-gray-600 mb-6">Do you really want to delete this issuer?</p>
                    <div class="flex justify-end gap-3">
                        <button wire:click="$set('showConfirm', false)"
                                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                            Cancel
                        </button>
                        <button wire:click="delete"
                                class="px-4 py-2 bg-[#0053FF] text-white rounded hover:bg-[#0053FF]">
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
