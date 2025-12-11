<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">💼 Advisory Services</h1>
                    <p class="text-gray-600 mt-1">Book consultations with certified financial advisors</p>
                </div>
                <a href="{{ route('financial.dashboard') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Back to Dashboard
                </a>
            </div>

            @if(session('message'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-lg mb-6 shadow-md">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="font-semibold text-lg">{{ session('message') }}</p>
                            <p class="text-sm mt-1">Your consultation has been added to your list below.</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- My Consultations --}}
            <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg p-6 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                        <span class="mr-2">📅</span>
                        My Consultations
                    </h2>
                    @if($myConsultations->count() > 0)
                    <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                        {{ $myConsultations->count() }} {{ $myConsultations->count() === 1 ? 'Consultation' : 'Consultations' }}
                    </span>
                    @endif
                </div>
                @if($myConsultations->count() > 0)
                <div class="space-y-3">
                    @foreach($myConsultations as $consultation)
                    <div class="bg-white p-5 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <div class="font-bold text-lg text-gray-900">{{ $consultation->advisor->name }}</div>
                                    <span class="ml-3 px-2 py-1 text-xs rounded-full 
                                        {{ $consultation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($consultation->status === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                           ($consultation->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst($consultation->status) }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600 mb-1">
                                    <span class="font-semibold">📅 Date:</span> {{ $consultation->scheduled_at->format('l, F d, Y') }}
                                </div>
                                <div class="text-sm text-gray-600 mb-1">
                                    <span class="font-semibold">⏰ Time:</span> {{ $consultation->scheduled_at->format('h:i A') }}
                                </div>
                                <div class="text-sm text-gray-600 mb-2">
                                    <span class="font-semibold">💬 Type:</span> {{ ucfirst($consultation->type) }}
                                    @if($consultation->amount)
                                    <span class="ml-3 font-semibold">💰 Amount:</span> €{{ number_format($consultation->amount, 2) }}
                                    @endif
                                </div>
                                @if($consultation->pre_consultation_notes)
                                <div class="text-xs text-gray-500 italic mt-2 p-2 bg-gray-50 rounded">
                                    <span class="font-semibold">Notes:</span> {{ $consultation->pre_consultation_notes }}
                                </div>
                                @endif
                            </div>
                            <div class="ml-4 flex flex-col gap-2">
                                @if($consultation->meeting_link)
                                <a href="{{ $consultation->meeting_link }}" target="_blank" 
                                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-semibold whitespace-nowrap">
                                    🔗 Join Meeting
                                </a>
                                @endif
                                @if($consultation->status === 'pending')
                                <span class="text-xs text-yellow-700 bg-yellow-50 px-3 py-1 rounded border border-yellow-200 text-center">
                                    Awaiting Confirmation
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 bg-white rounded-lg border border-gray-200">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-gray-500 font-medium">No consultations booked yet.</p>
                    <p class="text-sm text-gray-400 mt-1">Book a consultation with an advisor below to get started!</p>
                </div>
                @endif
            </div>

            {{-- Advisors List --}}
            <h2 class="text-xl font-bold text-gray-900 mb-4">Available Advisors</h2>
            @if($advisors->count() > 0)
            <div class="grid md:grid-cols-3 gap-6">
                @foreach($advisors as $advisor)
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center text-2xl font-bold text-blue-600">
                            {{ substr($advisor->name, 0, 1) }}
                        </div>
                        <div class="ml-4">
                            <h3 class="font-bold text-gray-900">{{ $advisor->name }}</h3>
                            <div class="flex items-center text-sm text-gray-600">
                                <span>⭐ {{ number_format($advisor->rating, 1) }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ $advisor->total_consultations }} consultations</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">{{ Str::limit($advisor->bio, 100) }}</p>
                    @if($advisor->specializations)
                    <div class="mb-4">
                        @foreach(array_slice($advisor->specializations, 0, 3) as $spec)
                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1">
                            {{ $spec }}
                        </span>
                        @endforeach
                    </div>
                    @endif
                    <button wire:click="openBooking({{ $advisor->id }})" 
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Book Consultation
                    </button>
                </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $advisors->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <p class="text-gray-500">No advisors available at this time.</p>
            </div>
            @endif

            {{-- Booking Form Inline --}}
            @if($showBookingForm && $selectedAdvisor)
            <div class="mt-8 bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Book Consultation with {{ $selectedAdvisor->name }}</h2>
                    <button wire:click="closeBooking" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form wire:submit.prevent="bookConsultation" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Consultation Type</label>
                        <select wire:model="consultationType" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="30min">30 Minutes</option>
                            <option value="60min">60 Minutes</option>
                            <option value="90min">90 Minutes</option>
                            <option value="specialty">Specialty Service</option>
                        </select>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                            <input type="date" wire:model="scheduledDate" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Time</label>
                            <input type="time" wire:model="scheduledTime" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pre-Consultation Notes (Optional)</label>
                        <textarea wire:model="preConsultationNotes" rows="4" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" wire:click="closeBooking" 
                                class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Book Consultation
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

