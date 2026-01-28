<main class="flex-1 min-h-0 w-full flex gap-x-[18px] px-2 pt-3" style="padding-bottom: 0px;">
        <!-- Sidebar -->
        @php
            $currentRoute = Route::currentRouteName();
        @endphp

        <div class="w-72 max-[1750px]:w-64 bg-[#0066FF] rounded-xl flex flex-col shadow-lg border border-blue-400/30 overflow-hidden shrink-0 h-full">
            <!-- Settings Header -->
            <div class="px-6 py-4 flex items-center justify-between border-b border-blue-300/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Settings</h3>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="flex-1 overflow-y-auto p-5 max-[1750px]:p-4 [scrollbar-width:none]">
                <!-- Personal Account Section -->
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-1 h-5 bg-white/30 rounded-full"></div>
                        <h4 class="text-sm font-semibold text-white/80 uppercase tracking-wide">Personal account</h4>
                    </div>
                    <div class="space-y-1">
                        <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 group {{ $currentRoute == 'profile' ? 'bg-white text-black shadow-sm' : 'text-white hover:bg-white/10' }}">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $currentRoute == 'profile' ? 'bg-white/20' : 'bg-white/10 group-hover:bg-white/15' }} transition-colors">
                                <svg class="w-5 h-5 {{ $currentRoute == 'profile' ? 'text-black' : 'text-white' }}" fill="{{ $currentRoute == 'profile' ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <span class="font-medium flex-1">Your profile</span>
                            @if($currentRoute == 'profile')
                                <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            @endif
                        </a>
                        <a href="{{ route('profile.password') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 group {{ $currentRoute == 'profile.password' ? 'bg-white text-black shadow-sm' : 'text-white hover:bg-white/10' }}">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $currentRoute == 'profile.password' ? 'bg-white/20' : 'bg-white/10 group-hover:bg-white/15' }} transition-colors">
                                <svg class="w-5 h-5 {{ $currentRoute == 'profile.password' ? 'text-black' : 'text-white' }}" fill="{{ $currentRoute == 'profile.password' ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <span class="font-medium flex-1">Change Password</span>
                            @if($currentRoute == 'profile.password')
                                <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            @endif
                        </a>
                    </div>
                </div>

                @unlessrole('super_admin')
                <!-- Payment and Plans Section -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-1 h-5 bg-white/30 rounded-full"></div>
                        <h4 class="text-sm font-semibold text-white/80 uppercase tracking-wide">Payment and plans</h4>
                    </div>
                    <div class="space-y-1">
                        <a href="{{ route('subscription.page') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 group {{ $currentRoute == 'subscription.page' ? 'bg-white text-black shadow-sm' : 'text-white hover:bg-white/10' }}">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $currentRoute == 'subscription.page' ? 'bg-white/20' : 'bg-white/10 group-hover:bg-white/15' }} transition-colors">
                                <svg class="w-5 h-5 {{ $currentRoute == 'subscription.page' ? 'text-black' : 'text-white' }}" fill="{{ $currentRoute == 'subscription.page' ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <span class="font-medium flex-1">Subscription</span>
                            @if($currentRoute == 'subscription.page')
                                <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            @endif
                        </a>
                        <a href="{{ route('purchase.history') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 group {{ $currentRoute == 'purchase.history' ? 'bg-white text-black shadow-sm' : 'text-white hover:bg-white/10' }}">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $currentRoute == 'purchase.history' ? 'bg-white/20' : 'bg-white/10 group-hover:bg-white/15' }} transition-colors">
                                <svg class="w-5 h-5 {{ $currentRoute == 'purchase.history' ? 'text-black' : 'text-white' }}" fill="{{ $currentRoute == 'purchase.history' ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <span class="font-medium flex-1">Purchase History</span>
                            @if($currentRoute == 'purchase.history')
                                <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            @endif
                        </a>
                    </div>
                </div>
                @endunlessrole
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 min-w-0 overflow-y-auto flex flex-col gap-[11px] [scrollbar-width:none] h-full">
            <div class="bg-white p-5 rounded-lg shadow-md">
                <h2 class="text-xl border-b border-gray-100 font-medium text-[#0053FF] pb-2 mb-4">Purchase History</h2>

                @if($subscriptions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="px-4 py-3 text-sm font-medium text-gray-700">Date</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-700">Type</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-700">Plan</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-700">Amount</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-700">Status</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-700">Period</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($subscriptions as $subscription)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $subscription['date'] }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $subscription['type'] }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $subscription['plan'] }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">£{{ $subscription['amount'] }}</td>
                                        <td class="px-4 py-3">
                                            @if($subscription['status'] === 'Active')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ $subscription['status'] }}
                                                </span>
                                            @elseif($subscription['status'] === 'Cancelled')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $subscription['status'] }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    {{ $subscription['status'] }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            @if($subscription['start_date'] !== 'N/A' && $subscription['end_date'] !== 'N/A')
                                                {{ $subscription['start_date'] }} - {{ $subscription['end_date'] }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if(!empty($subscription['invoice_url']))
                                                <a 
                                                    href="{{ $subscription['invoice_url'] }}"
                                                    target="_blank"
                                                    class="inline-block px-3 py-1.5 text-xs font-medium bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
                                                    title="View Invoice">
                                                    View Invoice
                                                </a>
                                            @elseif(!empty($subscription['stripe_subscription_id']))
                                                <span class="text-xs text-gray-400">Invoice not available</span>
                                            @else
                                                <span class="text-xs text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex items-center justify-center h-40">
                        <div class="text-center">
                            <p class="text-gray-500 text-lg font-medium mb-2">No purchase history found</p>
                            <p class="text-gray-400 text-sm">Your subscription and payment history will appear here</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
</main>

