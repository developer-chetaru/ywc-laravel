<div class="min-h-screen bg-gray-50 py-8">
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

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Mood Tracking</h1>
            <p class="mt-2 text-gray-600">Track your daily mood and wellness</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" wire:model.live="trackedDate" 
                               class="w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <!-- Mood Rating -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-4">
                            How are you feeling today? ({{ $moodRating }}/10)
                        </label>
                        <input type="range" wire:model="moodRating" min="1" max="10" 
                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>Very Low</span>
                            <span>Neutral</span>
                            <span>Very High</span>
                        </div>
                    </div>

                    <!-- Primary Mood -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Primary Mood</label>
                        <select wire:model="primaryMood" class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Select...</option>
                            <option value="happy">Happy</option>
                            <option value="sad">Sad</option>
                            <option value="anxious">Anxious</option>
                            <option value="calm">Calm</option>
                            <option value="angry">Angry</option>
                            <option value="excited">Excited</option>
                            <option value="tired">Tired</option>
                            <option value="content">Content</option>
                        </select>
                    </div>

                    <!-- Energy Level -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Energy Level ({{ $energyLevel }}/10)
                        </label>
                        <input type="range" wire:model="energyLevel" min="1" max="10" 
                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                    </div>

                    <!-- Sleep Quality -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Sleep Quality ({{ $sleepQuality }}/10)
                        </label>
                        <input type="range" wire:model="sleepQuality" min="1" max="10" 
                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                    </div>

                    <!-- Stress Level -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Stress Level ({{ $stressLevel }}/10)
                        </label>
                        <input type="range" wire:model="stressLevel" min="1" max="10" 
                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                    </div>

                    <!-- Physical Symptoms -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Physical Symptoms</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach(['headache', 'fatigue', 'muscle_tension', 'nausea', 'dizziness', 'chest_tightness'] as $symptom)
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="physicalSymptoms" value="{{ $symptom }}" class="mr-2">
                                    <span class="text-sm">{{ ucfirst(str_replace('_', ' ', $symptom)) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>
                        <textarea wire:model="triggerNotes" rows="3" 
                                  placeholder="What might have influenced your mood today?"
                                  class="w-full rounded-md border-gray-300 shadow-sm"></textarea>
                    </div>

                    <button wire:click="saveEntry" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg">
                        {{ $todayEntry ? 'Update Entry' : 'Save Entry' }}
                    </button>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Stats -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Stats</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600">7-Day Average Mood</p>
                            <p class="text-3xl font-bold text-blue-600">{{ $avgMood }}/10</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Entries This Month</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $recentEntries->where('tracked_date', '>=', now()->startOfMonth())->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Entries -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Entries</h3>
                    <div class="space-y-3">
                        @foreach($recentEntries->take(7) as $entry)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ Carbon\Carbon::parse($entry->tracked_date)->format('M d') }}
                                    </p>
                                    @if($entry->primary_mood)
                                        <p class="text-xs text-gray-500">{{ ucfirst($entry->primary_mood) }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-semibold 
                                        {{ $entry->mood_rating >= 7 ? 'text-green-600' : ($entry->mood_rating >= 4 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $entry->mood_rating }}/10
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
