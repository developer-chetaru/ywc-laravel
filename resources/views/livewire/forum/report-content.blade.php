<div>
    <button 
        wire:click="openReportModal"
        class="text-sm text-red-600 hover:text-red-700 font-medium transition-colors"
        title="Report this content">
        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        Report
    </button>

    @if ($showReportModal)
        <!-- Report Modal -->
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click="closeReportModal">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4" wire:click.stop>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Report Content</h3>
                        <button wire:click="closeReportModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="submitReport" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Reason for Reporting <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="reason" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select a reason...</option>
                                <option value="spam">Spam or Promotional Content</option>
                                <option value="harassment">Harassment or Bullying</option>
                                <option value="off_topic">Off-Topic or Wrong Category</option>
                                <option value="inappropriate">Inappropriate Content</option>
                                <option value="libel">Libel or False Information</option>
                                <option value="privacy">Privacy Violation</option>
                            </select>
                            @error('reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Explanation <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500">(Minimum 20 characters)</span>
                            </label>
                            <textarea 
                                wire:model="explanation" 
                                rows="4"
                                placeholder="Please provide details about why you're reporting this content..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            ></textarea>
                            @error('explanation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            <p class="text-xs text-gray-500 mt-1">{{ strlen($explanation) }}/1000 characters</p>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button 
                                type="button"
                                wire:click="closeReportModal"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <button 
                                type="submit"
                                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                Submit Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
