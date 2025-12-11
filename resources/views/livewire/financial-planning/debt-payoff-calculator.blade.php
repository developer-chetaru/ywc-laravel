<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('financial.calculators.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to Calculators</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">💳 Debt Payoff Calculator</h1>
            <p class="text-gray-600 mb-6">Calculate how long it will take to pay off your debt and total interest paid</p>

            <form wire:submit.prevent="calculate" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Debt Balance (€) *</label>
                        <input type="number" wire:model="debt_amount" min="1" step="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('debt_amount') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Annual Interest Rate (%) *</label>
                        <input type="number" wire:model="interest_rate" min="0" max="100" step="0.1" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('interest_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Payment (€) *</label>
                        <input type="number" wire:model="monthly_payment" min="0" step="50" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('monthly_payment') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        @if($recommended_payment > 0)
                            <p class="text-xs text-gray-500 mt-1">Recommended: €{{ number_format($recommended_payment, 2) }}/month</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payoff Strategy</label>
                        <select wire:model="strategy" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="minimum">Minimum Payment</option>
                            <option value="avalanche">Avalanche (Highest Interest First)</option>
                            <option value="snowball">Snowball (Smallest Balance First)</option>
                        </select>
                        @error('strategy') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Calculate Payoff Plan
                </button>
            </form>

            @if($show_results)
                <div class="mt-8 space-y-6">
                    <div class="bg-gradient-to-r from-red-50 to-orange-50 p-6 rounded-xl border border-red-200">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Debt Payoff Results</h2>
                        
                        <div class="grid md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Months to Payoff</div>
                                <div class="text-3xl font-bold text-blue-600">{{ $months_to_payoff }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ number_format($months_to_payoff / 12, 1) }} years</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Total Interest</div>
                                <div class="text-2xl font-bold text-red-600">€{{ number_format($total_interest, 2) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Total Amount Paid</div>
                                <div class="text-xl font-bold text-gray-900">€{{ number_format($total_paid, 2) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Principal</div>
                                <div class="text-xl font-bold text-green-600">€{{ number_format($debt_amount, 2) }}</div>
                            </div>
                        </div>

                        @if(!empty($payment_schedule))
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Schedule</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-white rounded-lg overflow-hidden">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Remaining Balance</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Interest Paid</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Principal Paid</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($payment_schedule as $payment)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $payment['month'] }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-gray-900">€{{ number_format($payment['balance'], 2) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-red-600">€{{ number_format($payment['total_interest'], 2) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-green-600">€{{ number_format($payment['principal_paid'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <h3 class="font-semibold text-blue-900 mb-2">💡 Debt Payoff Tips</h3>
                        <ul class="list-disc list-inside space-y-1 text-sm text-blue-800">
                            <li>Pay more than minimum to reduce total interest and payoff time</li>
                            <li><strong>Avalanche method:</strong> Pay off highest interest debt first (saves most money)</li>
                            <li><strong>Snowball method:</strong> Pay off smallest balance first (psychological boost)</li>
                            <li>Consider balance transfer to lower interest rate if available</li>
                            <li>Avoid taking on new debt while paying off existing debt</li>
                            <li>Every extra €10-50/month can save months or years of payments</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
