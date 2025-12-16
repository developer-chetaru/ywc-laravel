<div>
    @if($job)
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('job-board.detail', $job->id) }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Job Details
                </a>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Apply for Temporary Work: {{ $job->position_title }}</h1>

            <!-- Job Summary -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Work Details</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Date:</span>
                        <span class="font-medium ml-2">{{ $job->work_start_date?->format('M d, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Location:</span>
                        <span class="font-medium ml-2">{{ $job->location }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Hours:</span>
                        <span class="font-medium ml-2">{{ $job->total_hours ?? 10 }} hours</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Day Rate:</span>
                        <span class="font-medium ml-2">€{{ number_format($job->day_rate_min ?? 150, 0) }}</span>
                    </div>
                </div>
                @if($job->about_position)
                <div class="mt-4">
                    <span class="text-gray-500 text-sm">Description:</span>
                    <p class="text-gray-700 mt-1">{{ $job->about_position }}</p>
                </div>
                @endif
            </div>

            <form wire:submit="submitApplication">
                <!-- Optional Message -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Message to Captain (Optional)</h2>
                    <textarea wire:model="message" rows="4" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none" 
                        placeholder="Add any notes or questions for the captain..."></textarea>
                </div>

                <!-- Application Summary -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <h3 class="font-semibold text-blue-900 mb-2">What Happens Next:</h3>
                    <ul class="space-y-1 text-sm text-blue-800">
                        <li>✓ Your application will be sent to the captain</li>
                        <li>✓ The captain will review and confirm the booking</li>
                        <li>✓ You'll be notified once the booking is confirmed</li>
                        <li>✓ You can view the status in "My Bookings"</li>
                    </ul>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('job-board.detail', $job->id) }}" class="px-6 py-2 border border-gray-300 rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 font-medium">
                        Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

