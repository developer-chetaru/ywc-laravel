<div>
	@role('super_admin')
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold">User Roles</h2>

            {{-- 🔍 Live Search --}}
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search roles..." 
                class="border border-gray-300 rounded-md px-4 py-2 w-64 focus:ring focus:ring-indigo-200"
            />
        </div>

        {{-- Success message (create/update/delete) --}}
        @if (session()->has('message'))
            <div 
                x-data="{ show: true }" 
                x-show="show"
                x-transition.duration.500ms
                x-init="setTimeout(() => show = false, 2000)"
                class="mb-4 p-3 bg-green-100 text-green-700 rounded-md"
            >
                {{ session('message') }}
            </div>
        @endif

        {{-- Role Form --}}
        <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'save' }}" class="space-y-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Role Name</label>
                <input 
                    type="text" 
                    wire:model="name"
                    class="w-full border border-gray-300 rounded-md p-2 focus:ring focus:ring-indigo-200" 
                />
                @error('name') 
                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                @enderror
            </div>

            <div class="flex items-center space-x-3">
                <button 
                    type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition"
                >
                    {{ $isEditMode ? 'Update Role' : 'Create Role' }}
                </button>
                @if ($isEditMode)
                    <button 
                        type="button" 
                        wire:click="resetForm"
                        class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition"
                    >
                        Cancel
                    </button>
                @endif
            </div>
        </form>

        {{-- Roles Table --}}
        <table class="min-w-full border-collapse border border-gray-300 rounded-md">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">ID</th>
                    <th class="border px-4 py-2 text-left">Name</th>
                    <th class="border px-4 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td class="border px-4 py-2">{{ $loop->iteration + ($roles->currentPage() - 1) * $roles->perPage() }}</td>
                        <td class="border px-4 py-2">{{ $role->name }}</td>
                        <td class="border px-4 py-2 text-center space-x-2">
                            <button 
                                wire:click="edit({{ $role->id }})" 
                                class="text-blue-600 hover:underline"
                            >
                                Edit
                            </button>
                            <button 
                                wire:click="delete({{ $role->id }})"
                                class="text-red-600 hover:underline"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="border px-4 py-3 text-center text-gray-500">
                            No roles found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $roles->links() }}
        </div>
    </div>

    {{-- SweetAlert2 setup (listens to Livewire v3 events) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ensure Livewire is initialized
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('confirm-delete', (data) => {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action will permanently delete the role.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // dispatch back to Livewire to call confirmDelete
                        Livewire.dispatch('confirmDelete', { id: data.id });
                    }
                });
            });
        });
    </script>
	@else
        <div class="p-10 text-center">
            <h2 class="text-2xl font-semibold text-red-600 mb-2">Access Denied</h2>
            <p class="text-gray-600">You are not authorized to view this page.</p>
        </div>
    @endrole
</div>
