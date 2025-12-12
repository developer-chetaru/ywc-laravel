<div class="min-h-screen bg-red-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('mental-health.dashboard') }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-800">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>

        @if($step == 1)
            <!-- Initial Screen -->
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="mb-6">
                    <div class="mx-auto w-24 h-24 bg-red-600 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Crisis Support</h1>
                    <p class="text-lg text-gray-600">We're here to help you right now</p>
                </div>

                <div class="bg-red-100 border-l-4 border-red-600 p-4 mb-6 text-left">
                    <p class="text-red-800 font-semibold mb-2">If this is a life-threatening emergency, please call your local emergency services immediately.</p>
                    <p class="text-red-700 text-sm">In the UK: 999 | In the US: 911 | International emergency numbers available</p>
                </div>

                <div class="space-y-4 mb-8">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-900 mb-2">What to expect:</h3>
                        <ul class="text-sm text-gray-700 space-y-1 text-left">
                            <li>✓ Immediate connection to a trained crisis counselor</li>
                            <li>✓ Confidential and secure support</li>
                            <li>✓ Available 24/7</li>
                            <li>✓ No payment required</li>
                        </ul>
                    </div>
                </div>

                <button wire:click="startAssessment" 
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-4 px-6 rounded-lg text-lg transition">
                    Get Help Now
                </button>

                <p class="mt-4 text-sm text-gray-500">
                    By continuing, you agree to connect with a crisis counselor
                </p>
            </div>

        @elseif($step == 2)
            <!-- Assessment -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Quick Assessment</h2>
                
                <form wire:submit.prevent="submitAssessment">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                How would you describe your current situation?
                            </label>
                            <select wire:model="severity" class="w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Select...</option>
                                <option value="low">I need someone to talk to</option>
                                <option value="medium">I'm feeling overwhelmed</option>
                                <option value="high">I'm in significant distress</option>
                                <option value="critical">I'm in immediate danger</option>
                            </select>
                            @error('severity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Your location (optional but helpful)
                            </label>
                            <input type="text" wire:model="location" 
                                   placeholder="City, Country" 
                                   class="w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Preferred contact method
                            </label>
                            <div class="grid grid-cols-3 gap-4">
                                <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 {{ $sessionType == 'chat' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                    <input type="radio" wire:model="sessionType" value="chat" class="mr-2">
                                    <span>Chat</span>
                                </label>
                                <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 {{ $sessionType == 'voice' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                    <input type="radio" wire:model="sessionType" value="voice" class="mr-2">
                                    <span>Voice</span>
                                </label>
                                <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 {{ $sessionType == 'video' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                    <input type="radio" wire:model="sessionType" value="video" class="mr-2">
                                    <span>Video</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex gap-4">
                        <button type="button" wire:click="$set('step', 1)" 
                                class="flex-1 border border-gray-300 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-50">
                            Back
                        </button>
                        <button type="submit" 
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-lg">
                            Connect Now
                        </button>
                    </div>
                </form>
            </div>

        @elseif($step == 3)
            <!-- Connecting/Connected -->
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                @if($connecting && !$crisisSession->counselor_id)
                    <div class="mb-6">
                        <div class="inline-block animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-red-600"></div>
                        <h2 class="text-2xl font-bold text-gray-900 mt-4">Connecting you to a counselor...</h2>
                        <p class="text-gray-600 mt-2">Please wait, this may take a moment</p>
                    </div>
                @else
                    <div class="mb-6">
                        <div class="mx-auto w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">Connected</h2>
                        <p class="text-gray-600 mt-2">You're now connected with a crisis counselor</p>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg mb-6">
                        <p class="text-sm text-gray-700">
                            Your session is secure and confidential. The counselor is here to support you.
                        </p>
                    </div>

                    <!-- Session interface would go here -->
                    <div class="bg-blue-50 p-8 rounded-lg">
                        <p class="text-gray-700">
                            Session interface will be displayed here based on session type (chat/video/voice)
                        </p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
