@role('super_admin')
<div x-data="{ showConfirm: false, deleteId: null }" class="p-3 sm:p-6 bg-white rounded-lg shadow-md">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 mb-4 sm:mb-6">
        <h2 class="text-lg sm:text-xl font-bold text-[#0053FF]">Certificate Types</h2>
        <a href="{{ route('certificate-type.create') }}" class="px-3 sm:px-4 py-2 bg-[#0053FF] text-white rounded hover:bg-blue-700 text-sm sm:text-base whitespace-nowrap text-center">
            Add
        </a>
    </div>

    <!-- Search -->
    <div class="mb-4">
        <input type="text" wire:model.live="search" placeholder="Search certificate type..." class="border p-2 rounded w-full text-sm"/>
    </div>

    <!-- Table - Desktop View -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full border-collapse border border-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2 text-left text-sm">#</th>
                    <th class="border p-2 text-left text-sm">Name</th>
                    <th class="border p-2 text-center text-sm">Active</th>
                    <th class="border p-2 text-center text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($types as $index => $certificate)
                    <tr>
                        <td class="border p-2 text-sm">{{ $types->firstItem() + $index }}</td>
                        <td class="border p-2 text-sm">{{ $certificate->name }}</td>

                        <!-- Active -->
                        <td class="border p-2 text-center">
                            <input type="checkbox" wire:click="toggleActive({{ $certificate->id }})" @checked($certificate->is_active) class="h-4 w-4 text-[#0053FF] rounded cursor-pointer">
                        </td>

                        <!-- Actions -->
                        <td class="border p-2 text-center">
                            <div class="flex justify-center gap-2 items-center">
                                <!-- Edit -->
                                <a href="{{ route('certificate-type.edit', $certificate->id) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Delete -->
                                <button @click="showConfirm = true; deleteId = {{ $certificate->id }}" class="text-red-600 hover:text-red-800" title="Delete">
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
        @foreach ($types as $index => $certificate)
            <div class="border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs text-gray-500 font-medium">#{{ $types->firstItem() + $index }}</span>
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $certificate->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $certificate->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 break-words">{{ $certificate->name }}</h3>
                    </div>
                </div>
                
                <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" 
                               wire:click="toggleActive({{ $certificate->id }})" 
                               @checked($certificate->is_active) 
                               class="h-4 w-4 text-[#0053FF] rounded cursor-pointer">
                        <span>Toggle Status</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('certificate-type.edit', $certificate->id) }}" 
                           class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded"
                           title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button @click="showConfirm = true; deleteId = {{ $certificate->id }}" 
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
        {{ $types->links('livewire.custom-pagination') }}
    </div>

    <!-- Confirm Delete Modal -->
    <div x-show="showConfirm" x-transition x-cloak class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 p-4">
        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-lg max-w-sm w-full">
            <h2 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Are you sure?</h2>
            <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">Do you really want to delete this record?</p>
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3">
                <button @click="showConfirm = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm sm:text-base">Cancel</button>

                <form :action="`/certificate-type/${deleteId}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-[#0053FF] text-white rounded hover:bg-[#0053FF] text-sm sm:text-base w-full sm:w-auto">Delete</button>
                </form>
            </div>
        </div>
    </div>

</div>
@elserole('user') 
<div class="flex-1 flex flex-col overflow-hidden">
    <div class="flex min-h-screen bg-gray-100">
        <div class="flex-1 transition-all duration-300">
            <main class="p-6 flex-1 overflow-y-auto">
                <div class="w-full h-screen">
                    <div class="bg-white p-5 rounded-lg shadow-md">
                        <h2 class="text-xl border-b border-gray-100 font-medium text-[#0053FF] pb-2">Certificate Types</h2>
                        <div class="flex items-center justify-center h-40">
                            <p class="text-gray-500 text-lg font-medium">Certificate Types Coming Soon...</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endrole
