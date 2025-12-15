<div>
    @role('super_admin')
    <main class="flex-1 overflow-y-auto p-4">
        <div class="h-[calc(100vh-100px)] bg-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-[#0053FF] text-[30px] font-semibold">
                    {{ $providerId ? 'Edit Provider' : 'Add Provider' }}
                </h2>
                <a href="{{ route('training.admin.providers') }}" 
                   class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    ← Back to Providers
                </a>
            </div>

            @if (session()->has('success'))
                <div class="w-full bg-blue-500 text-white text-center py-2 rounded-md mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-6">
                <form wire:submit.prevent="save">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Name *</label>
                            <input type="text" wire:model="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Description</label>
                            <textarea wire:model="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-2">Email</label>
                                <input type="email" wire:model="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Phone</label>
                                <input type="text" wire:model="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Website</label>
                            <input type="url" wire:model="website" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            @error('website') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex gap-4">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="is_active" class="mr-2">
                                <span>Active</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="is_verified_partner" class="mr-2">
                                <span>Verified Partner</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-4 mt-6">
                        <button type="submit" class="px-6 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-blue-700">
                            {{ $providerId ? 'Update' : 'Create' }} Provider
                        </button>
                        <a href="{{ route('training.admin.providers') }}" 
                           class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-center">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
    @endrole
</div>
