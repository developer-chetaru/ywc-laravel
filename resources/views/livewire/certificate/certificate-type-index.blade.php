@role('super_admin')
<div x-data="{ showConfirm: false, deleteId: null }" class="p-6 bg-white rounded-lg shadow-md">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-[#0053FF]">Certificate Types</h2>
        <a href="{{ route('certificate-type.create') }}" class="px-4 py-2 bg-[#0053FF] text-white rounded hover:bg-blue-700">Add</a>
    </div>

    <!-- Search -->
    <div class="mb-4">
        <input type="text" wire:model.live="search" placeholder="Search certificate type..." class="border p-2 rounded w-full"/>
    </div>

    <!-- Table -->
    <table class="w-full border-collapse border border-gray-200">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">#</th>
                <th class="border p-2 text-left">Name</th>
                <th class="border p-2 text-center">Active</th>
                <th class="border p-2 text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($types as $index => $certificate)
                <tr>
                    <td class="border p-2">{{ $types->firstItem() + $index }}</td>
                    <td class="border p-2">{{ $certificate->name }}</td>

                    <!-- Active -->
                    <td class="border p-2 text-center">
                        <input type="checkbox" wire:click="toggleActive({{ $certificate->id }})" @checked($certificate->is_active) class="h-4 w-4 text-[#0053FF] rounded">
                    </td>

                    <!-- Actions -->
                    <td class="border p-2 flex justify-center gap-2 items-center">
                        <!-- Edit -->
                        <a href="{{ route('certificate-type.edit', $certificate->id) }}" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-edit"></i>
                        </a>

                        <!-- Delete -->
                        <button @click="showConfirm = true; deleteId = {{ $certificate->id }}" class="text-red-600 hover:text-red-800" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $types->links('livewire.custom-pagination') }}
    </div>

    <!-- Confirm Delete Modal -->
    <div x-show="showConfirm" x-transition x-cloak class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Are you sure?</h2>
            <p class="text-gray-600 mb-6">Do you really want to delete this record?</p>
            <div class="flex justify-end gap-3">
                <button @click="showConfirm = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>

                <form :action="`/certificate-type/${deleteId}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-[#0053FF] text-white rounded hover:bg-[#0053FF]">Delete</button>
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
