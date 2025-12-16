<div>
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('job-board.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Job Board
                </a>
            </div>
            
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
                    <select wire:model="verificationMethod" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none bg-white" required>
                        <option value="captain">Captain - Vessel Owner/Master</option>
                        <option value="management_company">Management Company Representative</option>
                        <option value="hod_authorized">HOD with Captain Authorization</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vessel Name *</label>
                    <input type="text" wire:model="vesselName" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Your Role on Vessel *</label>
                    <input type="text" wire:model="roleOnVessel" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none" placeholder="e.g. Captain, Owner, Management Representative" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Authority Description *</label>
                    <textarea wire:model="authorityDescription" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none" placeholder="Describe your authority to hire crew for this vessel..." required></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Captain's License</label>
                    <input type="file" wire:model="captainLicense" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none bg-white">
                    @if($captainLicense)
                    <p class="text-sm text-gray-600 mt-1">Selected: {{ $captainLicense->getClientOriginalName() }}</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vessel Registration</label>
                    <input type="file" wire:model="vesselRegistration" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none bg-white">
                    @if($vesselRegistration)
                    <p class="text-sm text-gray-600 mt-1">Selected: {{ $vesselRegistration->getClientOriginalName() }}</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Authorization Letter (if applicable)</label>
                    <input type="file" wire:model="authorizationLetter" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none bg-white">
                    @if($authorizationLetter)
                    <p class="text-sm text-gray-600 mt-1">Selected: {{ $authorizationLetter->getClientOriginalName() }}</p>
                    @endif
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800">
                        <strong>Note:</strong> Verification typically takes 24-48 hours. You'll receive an email notification once your verification is reviewed.
                    </p>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 font-medium">
                    Submit Verification Request
                </button>
            </form>
        </div>
    </div>
</div>

