<div>
    @role('super_admin')
    <main class="flex-1 overflow-y-auto p-4">
        <div class="h-[calc(100vh-100px)] bg-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-[#0053FF] text-[30px] font-semibold">
                    {{ $certificationId ? 'Edit Certification' : 'Add Certification' }}
                </h2>
                <a href="{{ route('training.admin.certifications') }}" 
                   class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    ← Back to Certifications
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
                            <label class="block text-sm font-semibold mb-2">Category *</label>
                            <select wire:model="category_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Name *</label>
                            <input type="text" wire:model="name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Official Designation</label>
                            <input type="text" wire:model="official_designation" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Description *</label>
                            <textarea wire:model="description" rows="4" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-2">Validity Period (Months)</label>
                                <input type="number" wire:model="validity_period_months" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Prerequisites</label>
                            <textarea wire:model="prerequisites" rows="2" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Renewal Requirements</label>
                            <textarea wire:model="renewal_requirements" rows="2" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">International Recognition</label>
                            <input type="text" wire:model="international_recognition" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                   placeholder="Comma-separated values">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Career Benefits</label>
                            <input type="text" wire:model="career_benefits" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                   placeholder="Comma-separated values">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Positions Requiring</label>
                            <input type="text" wire:model="positions_requiring" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                   placeholder="Comma-separated values">
                        </div>

                        <div class="flex gap-4">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="is_active" class="mr-2">
                                <span>Active</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="requires_admin_approval" class="mr-2">
                                <span>Requires Admin Approval</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-4 mt-6">
                        <button type="submit" 
                                class="px-6 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-blue-700">
                            {{ $certificationId ? 'Update' : 'Create' }} Certification
                        </button>
                        <a href="{{ route('training.admin.certifications') }}" 
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
