<div>
    @role('super_admin')
    <main class="flex-1 overflow-y-auto p-4">
        <div class="h-[calc(100vh-100px)] bg-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-[#0053FF] text-[30px] font-semibold">
                    {{ $courseId ? 'Edit Course' : 'Add Course' }}
                </h2>
                <a href="{{ route('training.admin.courses') }}" 
                   class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    ← Back to Courses
                </a>
            </div>

            @if (session()->has('success'))
                <div class="w-full bg-blue-500 text-white text-center py-2 rounded-md mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-6">
                <form wire:submit.prevent="save">
                    <div class="space-y-6">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-2">Certification *</label>
                                <select wire:model="certification_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="">Select certification</option>
                                    @foreach($certifications as $cert)
                                        <option value="{{ $cert->id }}">{{ $cert->name }}</option>
                                    @endforeach
                                </select>
                                @error('certification_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Provider *</label>
                                <select wire:model="provider_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="">Select provider</option>
                                    @foreach($providers as $provider)
                                        <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                    @endforeach
                                </select>
                                @error('provider_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-2">Price (£) *</label>
                                <input type="number" step="0.01" wire:model="price" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">YWC Discount (%) *</label>
                                <input type="number" step="0.1" wire:model="ywc_discount_percentage" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                @error('ywc_discount_percentage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Format *</label>
                                <select wire:model="format" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="in-person">In person</option>
                                    <option value="online">Online</option>
                                    <option value="blended">Blended</option>
                                </select>
                                @error('format') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-2">Duration (days) *</label>
                                <input type="number" min="1" wire:model="duration_days" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                @error('duration_days') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Duration (hours)</label>
                                <input type="number" min="0" wire:model="duration_hours" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                @error('duration_hours') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Language *</label>
                                <input type="text" wire:model="language_of_instruction" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                @error('language_of_instruction') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Booking URL</label>
                            <input type="url" wire:model="booking_url" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            @error('booking_url') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">What's Included (comma separated)</label>
                            <input type="text" wire:model="materials_included_text" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Course manual, Certificate, Lunch, Refreshments">
                            @error('materials_included_text') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold">Accommodation</label>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="accommodation_included" class="mr-2">
                                    <span>Accommodation included</span>
                                </label>
                                <textarea wire:model="accommodation_details" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Optional details..."></textarea>
                                @error('accommodation_details') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold">Meals</label>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="meals_included" class="mr-2">
                                    <span>Meals included</span>
                                </label>
                                <textarea wire:model="meals_details" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="e.g. Lunch daily, refreshments included"></textarea>
                                @error('meals_details') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-4">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="parking_included" class="mr-2">
                                <span>Parking included</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="transport_included" class="mr-2">
                                <span>Transport included</span>
                            </label>
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
                        <button type="submit" class="px-6 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-blue-700">
                            {{ $courseId ? 'Update' : 'Create' }} Course
                        </button>
                        <a href="{{ route('training.admin.courses') }}" 
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


