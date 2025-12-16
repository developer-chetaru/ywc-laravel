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
            
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Apply for: {{ $job->position_title }}</h1>

            <form wire:submit="submitApplication">
                <!-- Screening Questions -->
                @if($job->screeningQuestions->count() > 0)
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Screening Questions</h2>
                    @foreach($job->screeningQuestions as $question)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $question->question_text }}
                            @if($question->is_required)
                            <span class="text-red-500">*</span>
                            @endif
                        </label>
                        <textarea wire:model="screeningResponses.{{ $question->id }}" rows="3" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none @if($question->is_required) required @endif"
                            @if($question->is_required) required @endif></textarea>
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- Cover Message -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Cover Message (Optional)</h2>
                    <textarea wire:model="coverMessage" rows="6" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:outline-none" 
                        placeholder="Tell the captain why you're interested in this position..."></textarea>
                </div>

                <!-- Application Summary -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <h3 class="font-semibold text-blue-900 mb-2">Your Profile Will Be Sent:</h3>
                    <ul class="space-y-1 text-sm text-blue-800">
                        <li>✓ Basic Information (name, contact, photo)</li>
                        <li>✓ Certifications</li>
                        <li>✓ Experience History</li>
                        <li>✓ References</li>
                        <li>✓ Special Skills</li>
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
