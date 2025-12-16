<div>
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Vessel/Captain Verification</h1>

            @if($verification && $verification->status === 'verified')
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                <p class="text-green-800 font-medium">✓ You are verified!</p>
                <p class="text-green-700 text-sm mt-2">Verified on {{ $verification->verified_at->format('M d, Y') }}</p>
            </div>
            @elseif($verification && $verification->status === 'pending')
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                <p class="text-yellow-800 font-medium">⏳ Verification pending review</p>
                <p class="text-yellow-700 text-sm mt-2">We'll review your submission within 24-48 hours.</p>
            </div>
            @endif

            <form wire:submit="submit" class="bg-white shadow rounded-lg p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Verification Method *</label>
                    <select wire:model="verificationMethod" class="w-full border-gray-300 rounded-md" required>
                        <option value="captain">Captain - Vessel Owner/Master</option>
                        <option value="management_company">Management Company Representative</option>
                        <option value="hod_authorized">HOD with Captain Authorization</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vessel Name *</label>
                    <input type="text" wire:model="vesselName" class="w-full border-gray-300 rounded-md" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Your Role on Vessel *</label>
                    <input type="text" wire:model="roleOnVessel" class="w-full border-gray-300 rounded-md" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Authority Description *</label>
                    <textarea wire:model="authorityDescription" rows="4" class="w-full border-gray-300 rounded-md" required></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Captain's License</label>
                    <input type="file" wire:model="captainLicense" class="w-full border-gray-300 rounded-md">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vessel Registration</label>
                    <input type="file" wire:model="vesselRegistration" class="w-full border-gray-300 rounded-md">
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-md hover:bg-indigo-700">
                    Submit Verification Request
                </button>
            </form>
        </div>
    </div>
</div>
