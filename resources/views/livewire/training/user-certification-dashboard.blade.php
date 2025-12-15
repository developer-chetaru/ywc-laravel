<div>
    <main class="flex-1">
        <div class="w-full bg-white p-5 rounded-md pb-10">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-[#0053FF] text-[30px] font-semibold">My Certifications</h2>
                <div class="flex gap-2">
                    <a href="{{ route('training.schedule.calendar') }}" 
                       class="px-6 py-2 border border-[#0053FF] text-[#0053FF] rounded-lg hover:bg-blue-50 font-semibold">
                        📅 View Calendar
                    </a>
                    <button wire:click="openAddModal" 
                            class="px-6 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-blue-700 font-semibold">
                        + Add Certification
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="text-sm text-green-600 font-semibold">Valid</div>
                    <div class="text-2xl font-bold text-green-700">{{ $this->getValidCount() }}</div>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="text-sm text-yellow-600 font-semibold">Expiring Soon</div>
                    <div class="text-2xl font-bold text-yellow-700">{{ $this->getExpiringSoonCount() }}</div>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="text-sm text-red-600 font-semibold">Expired</div>
                    <div class="text-2xl font-bold text-red-700">{{ $this->getExpiredCount() }}</div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="text-sm text-blue-600 font-semibold">Total</div>
                    <div class="text-2xl font-bold text-blue-700">{{ $certifications->count() }}</div>
                </div>
            </div>

            <!-- Certifications List -->
            <div class="space-y-4">
                @forelse($certifications as $cert)
                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-xl font-semibold text-[#0053FF]">
                                        {{ $cert->certification->name }}
                                    </h3>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if($cert->status === 'valid') bg-green-100 text-green-800
                                        @elseif($cert->status === 'expiring_soon') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $cert->status)) }}
                                    </span>
                                </div>
                                
                                <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-600 mb-2">
                                    <div>
                                        <span class="font-semibold">Category:</span> 
                                        {{ $cert->certification->category->name }}
                                    </div>
                                    <div>
                                        <span class="font-semibold">Issue Date:</span> 
                                        {{ $cert->issue_date->format('M d, Y') }}
                                    </div>
                                    @if($cert->expiry_date)
                                        <div>
                                            <span class="font-semibold">Expiry Date:</span> 
                                            <span class="@if($cert->isExpired()) text-red-600 font-bold @elseif($cert->isExpiringSoon()) text-yellow-600 font-bold @endif">
                                                {{ $cert->expiry_date->format('M d, Y') }}
                                            </span>
                                            @if(!$cert->isExpired())
                                                <span class="text-gray-500">
                                                    ({{ $cert->expiry_date->diffForHumans() }})
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                    @if($cert->certificate_number)
                                        <div>
                                            <span class="font-semibold">Certificate #:</span> 
                                            {{ $cert->certificate_number }}
                                        </div>
                                    @endif
                                    @if($cert->issuing_authority)
                                        <div>
                                            <span class="font-semibold">Issued By:</span> 
                                            {{ $cert->issuing_authority }}
                                        </div>
                                    @endif
                                    @if($cert->providerCourse)
                                        <div>
                                            <span class="font-semibold">Provider:</span> 
                                            {{ $cert->providerCourse->provider->name }}
                                        </div>
                                    @endif
                                </div>

                                @if($cert->notes)
                                    <div class="text-sm text-gray-600 mb-2">
                                        <span class="font-semibold">Notes:</span> {{ $cert->notes }}
                                    </div>
                                @endif

                                @if($cert->certificate_document_path)
                                    <div class="mb-2">
                                        <a href="{{ asset('storage/' . $cert->certificate_document_path) }}" 
                                           target="_blank"
                                           class="text-[#0053FF] hover:underline text-sm">
                                            📄 View Certificate Document
                                        </a>
                                    </div>
                                @endif

                                @if($cert->isExpiringSoon() && !$cert->isExpired())
                                    <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-yellow-800 font-semibold">Renewal Required Soon</span>
                                        </div>
                                        <a href="{{ route('training.certification.detail', $cert->certification->slug) }}" 
                                           class="text-sm text-[#0053FF] hover:underline mt-1 block">
                                            Find renewal courses →
                                        </a>
                                    </div>
                                @endif

                                @if($cert->isExpired())
                                    <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-red-800 font-semibold">This certification has expired</span>
                                        </div>
                                        <a href="{{ route('training.certification.detail', $cert->certification->slug) }}" 
                                           class="text-sm text-[#0053FF] hover:underline mt-1 block">
                                            Find renewal courses →
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div class="flex gap-2">
                                <button wire:click="openEditModal({{ $cert->id }})" 
                                        class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $cert->id }})" 
                                        wire:confirm="Are you sure you want to remove this certification?"
                                        class="px-4 py-2 text-sm bg-red-100 text-red-700 rounded-lg hover:bg-red-200">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 border rounded-lg">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-500 text-lg mb-4">No certifications added yet.</p>
                        <button wire:click="openAddModal" 
                                class="px-6 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-blue-700">
                            Add Your First Certification
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Add/Edit Modal -->
        @if($showAddModal || $showEditModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" 
                 wire:click="closeModals">
                <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto" 
                     wire:click.stop>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-2xl font-bold">
                                {{ $showEditModal ? 'Edit Certification' : 'Add Certification' }}
                            </h3>
                            <button wire:click="closeModals" class="text-gray-500 hover:text-gray-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form wire:submit.prevent="save">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-2">Certification *</label>
                                    <select wire:model="certification_id" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]">
                                        <option value="">Select Certification</option>
                                        @foreach($availableCertifications as $cert)
                                            <option value="{{ $cert->id }}">{{ $cert->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('certification_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Issue Date *</label>
                                        <input type="date" wire:model="issue_date" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]">
                                        @error('issue_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Expiry Date</label>
                                        <input type="date" wire:model="expiry_date" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]">
                                        @error('expiry_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Certificate Number</label>
                                        <input type="text" wire:model="certificate_number" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Issuing Authority</label>
                                        <input type="text" wire:model="issuing_authority" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-2">Certificate Document</label>
                                    <input type="file" wire:model="certificate_document" 
                                           accept=".pdf,.jpg,.jpeg,.png"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]">
                                    @if($selectedCertification && $selectedCertification->certificate_document_path)
                                        <p class="text-sm text-gray-600 mt-1">
                                            Current: <a href="{{ asset('storage/' . $selectedCertification->certificate_document_path) }}" 
                                                       target="_blank" class="text-[#0053FF] hover:underline">View</a>
                                        </p>
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-2">Notes</label>
                                    <textarea wire:model="notes" rows="3" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]"></textarea>
                                </div>
                            </div>

                            <div class="flex gap-4 mt-6">
                                <button type="submit" 
                                        class="flex-1 px-6 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-blue-700 font-semibold">
                                    {{ $showEditModal ? 'Update' : 'Add' }} Certification
                                </button>
                                <button type="button" wire:click="closeModals" 
                                        class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </main>
</div>
