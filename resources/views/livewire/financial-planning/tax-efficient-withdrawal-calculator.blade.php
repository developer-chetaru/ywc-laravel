<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('financial.calculators.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to Calculators</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">💵 Tax-Efficient Withdrawal Calculator</h1>
            <p class="text-gray-600 mb-6">Optimize your retirement withdrawal strategy to minimize taxes</p>

            <form wire:submit.prevent="calculate" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Annual Income Need (€) *</label>
                        <input type="number" wire:model="annual_need" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('annual_need') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Age *</label>
                        <input type="number" wire:model="current_age" min="59" max="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Must be 59.5+ for penalty-free withdrawals</p>
                        @error('current_age') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Taxable Account Balance (€) *</label>
                        <input type="number" wire:model="taxable_account_balance" min="0" step="10000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('taxable_account_balance') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tax-Deferred Balance (€) *</label>
                        <input type="number" wire:model="tax_deferred_balance" min="0" step="10000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">401k, Traditional IRA, Pension</p>
                        @error('tax_deferred_balance') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tax-Free Balance (€) *</label>
                        <input type="number" wire:model="tax_free_balance" min="0" step="10000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Roth IRA, Roth 401k</p>
                        @error('tax_free_balance') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Taxable Account Gains (%) *</label>
                        <input type="number" wire:model="taxable_gains_percent" min="0" max="100" step="5" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">% of taxable account that is capital gains</p>
                        @error('taxable_gains_percent') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Long-Term Capital Gains Rate (%) *</label>
                        <input type="number" wire:model="long_term_cap_gains_rate" min="0" max="50" step="0.5" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('long_term_cap_gains_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ordinary Income Tax Rate (%) *</label>
                        <input type="number" wire:model="ordinary_income_rate" min="0" max="50" step="0.5" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('ordinary_income_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Years to Plan *</label>
                        <input type="number" wire:model="years" min="1" max="50" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('years') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Calculate Optimal Strategy
                </button>
            </form>

            @if($show_results)
                <div class="mt-8 space-y-6">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-xl border border-green-200">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Optimal Withdrawal Strategy</h2>
                        
                        <div class="bg-green-100 p-4 rounded-lg mb-6">
                            <h3 class="font-bold text-green-900 mb-2">✅ Recommended: {{ $strategy_recommendation }}</h3>
                            <p class="text-sm text-green-800">This strategy minimizes your total tax liability</p>
                        </div>

                        <div class="grid md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">From Taxable</div>
                                <div class="text-xl font-bold text-blue-600">€{{ number_format($taxable_annual_withdrawal, 0) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">From Tax-Deferred</div>
                                <div class="text-xl font-bold text-purple-600">€{{ number_format($tax_deferred_annual_withdrawal, 0) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">From Tax-Free</div>
                                <div class="text-xl font-bold text-green-600">€{{ number_format($tax_free_annual_withdrawal, 0) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Annual Tax</div>
                                <div class="text-xl font-bold text-red-600">€{{ number_format($total_tax_paid, 0) }}</div>
                            </div>
                        </div>

                        @if(!empty($withdrawal_strategy))
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Strategy Comparison</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-white rounded-lg overflow-hidden">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Strategy</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Taxable</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tax-Deferred</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tax-Free</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Annual Tax</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($withdrawal_strategy as $name => $strategy)
                                                <tr class="hover:bg-gray-50 {{ $name === $strategy_recommendation ? 'bg-green-50' : '' }}">
                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $name }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-gray-600">€{{ number_format($strategy['taxable'], 0) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-gray-600">€{{ number_format($strategy['tax_deferred'], 0) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-gray-600">€{{ number_format($strategy['tax_free'], 0) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right font-medium {{ $name === $strategy_recommendation ? 'text-green-600' : 'text-red-600' }}">
                                                        €{{ number_format($strategy['total_tax'], 0) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <h3 class="font-semibold text-blue-900 mb-2">💡 Withdrawal Strategy Tips</h3>
                        <ul class="list-disc list-inside space-y-1 text-sm text-blue-800">
                            <li>Generally withdraw from taxable accounts first (lowest tax rate on long-term gains)</li>
                            <li>Delay tax-deferred withdrawals to minimize lifetime tax burden</li>
                            <li>Save tax-free accounts for last to maximize tax-free growth</li>
                            <li>Consider Required Minimum Distributions (RMDs) from tax-deferred accounts at age 73+</li>
                            <li>Balance withdrawals to stay in lower tax brackets</li>
                            <li>Consider Roth conversions during low-income years</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
