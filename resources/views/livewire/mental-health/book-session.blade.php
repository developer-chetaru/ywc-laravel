<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
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

        <h1 class="text-3xl font-bold text-gray-900 mb-8">Book a Session</h1>

        @if($step == 1)
            <!-- Step 1: Select Therapist -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Choose a Therapist</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($therapists as $therapist)
                        <div class="border-2 rounded-lg p-4 hover:border-blue-500 cursor-pointer transition
                            {{ $therapistId == $therapist->id ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}"
                            wire:click="selectTherapist({{ $therapist->id }})">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                    <span>{{ substr($therapist->user->first_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold">{{ $therapist->user->first_name }} {{ $therapist->user->last_name }}</h3>
                                    <p class="text-sm text-gray-600">£{{ number_format($therapist->base_hourly_rate ?? 0, 2) }}/hr</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        @elseif($step == 2 && $selectedTherapist)
            <!-- Step 2: Select Session Details -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-semibold">Session Details</h2>
                        <p class="text-gray-600">with {{ $selectedTherapist->user->first_name }} {{ $selectedTherapist->user->last_name }}</p>
                    </div>
                    <button wire:click="$set('step', 1)" class="text-blue-600 hover:text-blue-800">Change Therapist</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Session Type</label>
                        <select wire:model.live="sessionType" class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="video">Video Call</option>
                            <option value="voice">Voice Call</option>
                            <option value="chat">Chat</option>
                            <option value="email">Email Consultation</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duration</label>
                        <select wire:model.live="duration" class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="30">30 minutes</option>
                            <option value="60">60 minutes</option>
                            <option value="90">90 minutes</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Total Cost:</span>
                        <span class="text-2xl font-bold text-gray-900">£{{ number_format($totalCost, 2) }}</span>
                    </div>
                    @if($availableCredits > 0)
                        <div class="mt-2">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model.live="creditsToUse" 
                                       value="{{ min($availableCredits, $totalCost) }}" 
                                       class="mr-2">
                                <span class="text-sm text-gray-700">
                                    Use credits (Available: £{{ number_format($availableCredits, 2) }})
                                </span>
                            </label>
                            @if($creditsToUse)
                                <p class="text-sm text-gray-600 mt-1">
                                    After credits: £{{ number_format(max(0, $totalCost - $creditsToUse), 2) }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                    <input type="date" wire:model.live="selectedDate" 
                           min="{{ now()->toDateString() }}"
                           class="w-full rounded-md border-gray-300 shadow-sm">
                </div>

                @if($selectedDate)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Available Times</label>
                        @if(count($availableSlots) > 0)
                            <div class="grid grid-cols-4 gap-2">
                                @foreach($availableSlots as $slot)
                                    <button wire:click="selectTime('{{ $slot }}')" 
                                            class="py-2 px-4 border-2 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition
                                            {{ $selectedTime == $slot ? 'border-blue-500 bg-blue-100' : 'border-gray-200' }}">
                                        {{ \Carbon\Carbon::parse($slot)->format('g:i A') }}
                                    </button>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p class="text-yellow-800 text-sm">No available slots for this date. Please select another date.</p>
                            </div>
                        @endif
                    </div>
                @endif

                @if($selectedTime)
                    <div class="mt-6">
                        <button wire:click="confirmBooking" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg">
                            Confirm Booking
                        </button>
                    </div>
                @endif
            </div>

        @elseif($step == 3)
            <!-- Step 3: Confirmation -->
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="mb-6">
                    <div class="mx-auto w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Booking Confirmed!</h2>
                    <p class="text-gray-600 mt-2">Your session has been scheduled</p>
                </div>
            </div>
        @endif
    </div>
</div>
