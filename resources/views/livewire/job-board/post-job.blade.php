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
            
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Post a Job</h1>

            <form wire:submit="save">
                <!-- Job Type Toggle -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Job Type</label>
                    <div class="flex gap-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" wire:model.live="jobType" value="permanent" class="mr-2 text-indigo-600 focus:ring-indigo-500">
                            <span>Permanent Position</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" wire:model.live="jobType" value="temporary" class="mr-2 text-indigo-600 focus:ring-indigo-500">
                            <span>Temporary Work</span>
                        </label>
                    </div>
                </div>

                <!-- Basic Details -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Basic Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Position Title *</label>
                            <input type="text" wire:model="positionTitle" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
                            <select wire:model="department" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none bg-white" required>
                                <option value="deck">Deck</option>
                                <option value="interior">Interior</option>
                                <option value="engine">Engine</option>
                                <option value="galley">Galley</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Compensation -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Compensation</h2>
                    @if($jobType === 'permanent')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Min Salary (€/month) *</label>
                            <input type="number" wire:model="salaryMin" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Salary (€/month) *</label>
                            <input type="number" wire:model="salaryMax" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none" required>
                        </div>
                    </div>
                    @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Day Rate (€) *</label>
                            <input type="number" wire:model="dayRateMin" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Work Dates *</label>
                            <input type="date" wire:model="workStartDate" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none mb-2" required>
                            <input type="date" wire:model="workEndDate" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none" required>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Description -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Job Description</h2>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">About the Position</label>
                        <textarea wire:model="aboutPosition" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
                        <textarea wire:model="otherRequirements" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none"></textarea>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4">
                    <button type="button" wire:click="save(false)" class="px-6 py-2 border border-gray-300 rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Save Draft
                    </button>
                    <button type="button" wire:click="publish" class="px-6 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Publish Job
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
