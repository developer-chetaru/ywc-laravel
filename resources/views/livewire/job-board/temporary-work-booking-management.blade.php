<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
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
            
            <h1 class="text-3xl font-bold text-gray-900 mb-6">
                @if($viewMode === 'admin')
                    Admin Panel
                @elseif($viewMode === 'captain')
                    Temporary Work Bookings
                @else
                    My Temporary Work
                @endif
            </h1>
            
            @if($viewMode === 'admin')
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-blue-900">Admin Access Required</h3>
                        <p class="text-sm text-blue-700 mt-1">Use the admin panel to manage all bookings and verifications.</p>
                    </div>
                    <a href="{{ route('job-board.admin') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 font-medium">
                        Go to Admin Panel
                    </a>
                </div>
            </div>
            @elseif($viewMode === 'crew')
            <p class="text-gray-600 mb-6">View and manage your temporary work bookings. Pending bookings are awaiting captain confirmation.</p>
            @else
            <p class="text-gray-600 mb-6">Review and manage temporary work bookings for your posted jobs.</p>
            @endif

            <!-- Filters -->
            <div class="bg-white shadow rounded-lg p-4 mb-6">
                <div class="flex gap-2 flex-wrap">
                    <button wire:click="$set('filter', 'all')" class="px-4 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $filter === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        All
                    </button>
                    <button wire:click="$set('filter', 'upcoming')" class="px-4 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $filter === 'upcoming' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Upcoming
                    </button>
                    <button wire:click="$set('filter', 'completed')" class="px-4 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $filter === 'completed' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Completed
                    </button>
                    @if($viewMode === 'captain')
                    <button wire:click="$set('filter', 'pending')" class="px-4 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $filter === 'pending' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Pending Review
                    </button>
                    @endif
                    @if($viewMode === 'crew')
                    <button wire:click="$set('filter', 'pending_payment')" class="px-4 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $filter === 'pending_payment' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Pending Payment
                    </button>
                    @endif
                </div>
            </div>

            <!-- Bookings List -->
            <div class="space-y-4">
                @forelse($bookings as $booking)
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                @if($booking->status === 'pending')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-sm font-medium">
                                    ⏳ Pending Confirmation
                                </span>
                                @elseif($booking->status === 'confirmed')
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm font-medium">
                                    ✓ Confirmed
                                </span>
                                @elseif($booking->status === 'completed')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm font-medium">
                                    ✓ Completed
                                </span>
                                @elseif($booking->status === 'cancelled')
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm font-medium">
                                    ✗ Cancelled
                                </span>
                                @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-sm font-medium">
                                    {{ ucfirst($booking->status) }}
                                </span>
                                @endif
                                
                                @if($booking->status === 'completed' && !$booking->payment_received && $viewMode === 'crew')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-sm font-medium">
                                    ⏳ Payment Pending
                                </span>
                                @elseif($booking->payment_received)
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm font-medium">
                                    ✅ Paid
                                </span>
                                @endif
                            </div>
                            <h3 class="text-xl font-semibold mb-1">
                                @if($viewMode === 'captain')
                                    {{ $booking->user->name }}
                                @else
                                    {{ $booking->jobPost->position_title }}
                                @endif
                            </h3>
                            <p class="text-gray-600 mb-2">
                                {{ $booking->work_description }}
                            </p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600">
                                <div>
                                    <span class="font-medium">Date:</span> {{ $booking->work_date->format('M d, Y') }}
                                </div>
                                <div>
                                    <span class="font-medium">Hours:</span> {{ $booking->total_hours }}h
                                </div>
                                <div>
                                    <span class="font-medium">Rate:</span> €{{ number_format($booking->day_rate, 0) }}/day
                                </div>
                                <div>
                                    <span class="font-medium">Total:</span> €{{ number_format($booking->total_payment, 0) }}
                                </div>
                            </div>
                            @if($booking->location)
                            <p class="text-sm text-gray-500 mt-2">📍 {{ $booking->location }}</p>
                            @endif
                        </div>
                        <div class="flex flex-col gap-2">
                            @if($viewMode === 'captain')
                                @if($booking->status === 'pending')
                                <button wire:click="confirmBooking({{ $booking->id }})" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 font-medium">
                                    Confirm Booking
                                </button>
                                @elseif($booking->status === 'completed' && !$booking->payment_received)
                                <button wire:click="markAsPaid({{ $booking->id }})" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 font-medium">
                                    Mark as Paid
                                </button>
                                @elseif($booking->status === 'confirmed')
                                <button wire:click="markAsCompleted({{ $booking->id }})" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 font-medium">
                                    Mark Completed
                                </button>
                                @endif
                            @else
                                @if($booking->status === 'completed' && !$booking->payment_received)
                                <button wire:click="confirmPayment({{ $booking->id }})" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Confirm Payment Received
                                </button>
                                @endif
                            @endif
                            <a href="{{ route('job-board.detail', $booking->job_post_id) }}" 
                                class="px-4 py-2 border border-gray-300 rounded-md text-center shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white shadow rounded-lg p-12 text-center">
                    <p class="text-gray-500 text-lg mb-4">No bookings found.</p>
                    @if($viewMode === 'crew')
                    <p class="text-gray-400 text-sm mb-4">You don't have any temporary work bookings yet.</p>
                    <a href="{{ route('job-board.temporary-work') }}" class="inline-block px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Browse Temporary Work
                    </a>
                    @else
                    <p class="text-gray-400 text-sm mb-4">You haven't booked any crew members yet.</p>
                    <a href="{{ route('job-board.available-crew') }}" class="inline-block px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Find Available Crew
                    </a>
                    @endif
                </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</div>

