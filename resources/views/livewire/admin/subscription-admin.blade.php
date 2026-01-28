<div>
    <main class="flex-1 p-5">
        <div class="w-full bg-white p-5 rounded-md pb-10">
            <h2 class="text-[#0053FF] text-[30px] font-semibold mb-6">Subscription & Billing - Admin Dashboard</h2>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="text-sm text-blue-600 font-semibold">Total Subscriptions</div>
                    <div class="text-2xl font-bold text-blue-700">{{ $stats['total_subscriptions'] }}</div>
                    <div class="text-xs text-blue-600 mt-1">{{ $stats['active_subscriptions'] }} active</div>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="text-sm text-green-600 font-semibold">Active Subscriptions</div>
                    <div class="text-2xl font-bold text-green-700">{{ $stats['active_subscriptions'] }}</div>
                    <div class="text-xs text-green-600 mt-1">Currently active</div>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="text-sm text-yellow-600 font-semibold">Past Due</div>
                    <div class="text-2xl font-bold text-yellow-700">{{ $stats['past_due_subscriptions'] }}</div>
                    <div class="text-xs text-yellow-600 mt-1">Payment issues</div>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="text-sm text-red-600 font-semibold">Suspended</div>
                    <div class="text-2xl font-bold text-red-700">{{ $stats['suspended_subscriptions'] }}</div>
                    <div class="text-xs text-red-600 mt-1">Access restricted</div>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="text-sm text-purple-600 font-semibold">Monthly Revenue</div>
                    <div class="text-2xl font-bold text-purple-700">£{{ number_format($stats['monthly_revenue'], 2) }}</div>
                    <div class="text-xs text-purple-600 mt-1">Recurring monthly</div>
                </div>
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                    <div class="text-sm text-indigo-600 font-semibold">Annual Revenue</div>
                    <div class="text-2xl font-bold text-indigo-700">£{{ number_format($stats['annual_revenue'], 2) }}</div>
                    <div class="text-xs text-indigo-600 mt-1">Recurring annual</div>
                </div>
                <div class="bg-teal-50 border border-teal-200 rounded-lg p-4">
                    <div class="text-sm text-teal-600 font-semibold">Annualized Revenue</div>
                    <div class="text-2xl font-bold text-teal-700">£{{ number_format($stats['total_annualized_revenue'], 2) }}</div>
                    <div class="text-xs text-teal-600 mt-1">Total projected</div>
                </div>
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                    <div class="text-sm text-orange-600 font-semibold">Failed Payments</div>
                    <div class="text-2xl font-bold text-orange-700">{{ $stats['failed_payments'] }}</div>
                    <div class="text-xs text-orange-600 mt-1">Requires attention</div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
                        <select wire:model.live="filterStatus" class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="all">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="past_due">Past Due</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Plan Type</label>
                        <select wire:model.live="filterPlanType" class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="all">All Plans</option>
                            <option value="monthly">Monthly</option>
                            <option value="annual">Annual</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                               placeholder="Search by email, name, or Stripe ID..."
                               class="w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                </div>
            </div>

            <!-- Failed Payments Report -->
            @if($failedPayments->count() > 0)
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-red-700 mb-3">⚠️ Failed Payment Alerts</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-red-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 uppercase">User</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 uppercase">Email</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 uppercase">Retry Count</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 uppercase">Grace Period End</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($failedPayments as $sub)
                            <tr>
                                <td class="px-4 py-2 text-sm">{{ $sub->user->first_name ?? '' }} {{ $sub->user->last_name ?? '' }}</td>
                                <td class="px-4 py-2 text-sm">{{ $sub->user->email ?? 'N/A' }}</td>
                                <td class="px-4 py-2 text-sm">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $sub->status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($sub->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-sm">{{ $sub->payment_retry_count }}/3</td>
                                <td class="px-4 py-2 text-sm">
                                    {{ $sub->grace_period_end ? $sub->grace_period_end->format('d M Y') : 'N/A' }}
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    <button wire:click="viewDetails({{ $sub->id }})" 
                                            class="text-blue-600 hover:text-blue-800 text-xs">View Details</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Subscriptions Table -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700">All Subscriptions</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Billing</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($subscriptions as $subscription)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $subscription->user->first_name ?? '' }} {{ $subscription->user->last_name ?? '' }}
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $subscription->user->email ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($subscription->plan_type ?? 'monthly') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $subscription->status === 'past_due' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $subscription->status === 'suspended' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $subscription->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ ucfirst($subscription->status) }}
                                        @if($subscription->cancel_at_period_end)
                                            <span class="text-gray-500">(Cancelling)</span>
                                        @endif
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    £{{ number_format($subscription->amount / 100, 2) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    @if($subscription->current_period_end)
                                        {{ $subscription->current_period_end->format('d M Y') }}
                                    @elseif($subscription->end_date)
                                        {{ $subscription->end_date->format('d M Y') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $subscription->created_at->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <button wire:click="viewDetails({{ $subscription->id }})" 
                                            class="text-blue-600 hover:text-blue-800 mr-3">View</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    No subscriptions found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                    {{ $subscriptions->links() }}
                </div>
            </div>
        </div>

        <!-- Subscription Details Modal -->
        @if($showDetailsModal && $selectedSubscription)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click="closeModal">
            <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Subscription Details</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">User</label>
                            <p class="text-sm text-gray-900">{{ $selectedSubscription->user->first_name ?? '' }} {{ $selectedSubscription->user->last_name ?? '' }}</p>
                            <p class="text-sm text-gray-600">{{ $selectedSubscription->user->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <p class="text-sm text-gray-900">{{ ucfirst($selectedSubscription->status) }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Plan Type</label>
                            <p class="text-sm text-gray-900">{{ ucfirst($selectedSubscription->plan_type ?? 'monthly') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Amount</label>
                            <p class="text-sm text-gray-900">£{{ number_format($selectedSubscription->amount / 100, 2) }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Stripe Customer ID</label>
                            <p class="text-sm text-gray-900 font-mono text-xs">{{ $selectedSubscription->stripe_customer_id ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Stripe Subscription ID</label>
                            <p class="text-sm text-gray-900 font-mono text-xs">{{ $selectedSubscription->stripe_subscription_id ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Start Date</label>
                            <p class="text-sm text-gray-900">{{ $selectedSubscription->start_date ? $selectedSubscription->start_date->format('d M Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Next Billing Date</label>
                            <p class="text-sm text-gray-900">
                                @if($selectedSubscription->current_period_end)
                                    {{ $selectedSubscription->current_period_end->format('d M Y') }}
                                @elseif($selectedSubscription->end_date)
                                    {{ $selectedSubscription->end_date->format('d M Y') }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Cancel at Period End</label>
                            <p class="text-sm text-gray-900">{{ $selectedSubscription->cancel_at_period_end ? 'Yes' : 'No' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Payment Retry Count</label>
                            <p class="text-sm text-gray-900">{{ $selectedSubscription->payment_retry_count ?? 0 }}/3</p>
                        </div>
                        @if($selectedSubscription->grace_period_end)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Grace Period End</label>
                            <p class="text-sm text-gray-900">{{ $selectedSubscription->grace_period_end->format('d M Y') }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Manual Override Actions -->
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Manual Override Actions</h4>
                        <div class="flex flex-wrap gap-2">
                            @if($selectedSubscription->status !== 'active')
                            <button wire:click="manualOverride({{ $selectedSubscription->id }}, 'activate')" 
                                    wire:confirm="Are you sure you want to activate this subscription?"
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                                Activate
                            </button>
                            @endif
                            @if($selectedSubscription->status !== 'suspended')
                            <button wire:click="manualOverride({{ $selectedSubscription->id }}, 'suspend')" 
                                    wire:confirm="Are you sure you want to suspend this subscription?"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                                Suspend
                            </button>
                            @endif
                            @if(!$selectedSubscription->cancel_at_period_end)
                            <button wire:click="manualOverride({{ $selectedSubscription->id }}, 'cancel')" 
                                    wire:confirm="Are you sure you want to cancel this subscription at period end?"
                                    class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 text-sm">
                                Cancel at Period End
                            </button>
                            @endif
                            <button wire:click="manualOverride({{ $selectedSubscription->id }}, 'extend')" 
                                    wire:confirm="Are you sure you want to extend this subscription by 1 month?"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                Extend by 1 Month
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Flash Messages -->
        @if(session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
             class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('success') }}
        </div>
        @endif

        @if(session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
             class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('error') }}
        </div>
        @endif
    </main>
</div>
