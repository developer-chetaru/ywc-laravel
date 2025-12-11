<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('financial.calculators.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to Calculators</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">💵 Dollar-Cost Averaging Simulator</h1>
            <p class="text-gray-600 mb-6">Compare lump sum investing vs dollar-cost averaging (DCA) strategy</p>

            <form wire:submit.prevent="calculate" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lump Sum Amount (€) *</label>
                        <input type="number" wire:model="lump_sum_amount" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('lump_sum_amount') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">One-time investment</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">DCA Initial Amount (€) *</label>
                        <input type="number" wire:model="dca_amount" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('dca_amount') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Initial investment for DCA</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Investment (€) *</label>
                        <input type="number" wire:model="monthly_investment" min="0" step="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('monthly_investment') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">For DCA strategy</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Investment Period (Months) *</label>
                        <input type="number" wire:model="investment_period_months" min="1" max="600" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('investment_period_months') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expected Annual Return (%) *</label>
                        <input type="number" wire:model="annual_return" min="0" max="30" step="0.1" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('annual_return') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Compare Strategy</label>
                        <select wire:model="strategy" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="both">Both (Lump Sum vs DCA)</option>
                            <option value="lump_sum">Lump Sum Only</option>
                            <option value="dca">DCA Only</option>
                        </select>
                        @error('strategy') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Simulate Strategies
                </button>
            </form>

            @if($show_results)
                <div class="mt-8 space-y-6">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-200">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Strategy Comparison</h2>
                        
                        @if($strategy === 'both')
                            <div class="grid md:grid-cols-2 gap-6 mb-6">
                                <div class="bg-white p-5 rounded-lg shadow-sm">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">💎 Lump Sum Strategy</h3>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Initial Investment:</span>
                                            <span class="font-medium">€{{ number_format($lump_sum_amount, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Final Value:</span>
                                            <span class="font-bold text-green-600 text-lg">€{{ number_format($lump_sum_final_value, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Total Gain:</span>
                                            <span class="font-bold text-blue-600">€{{ number_format($lump_sum_gain, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white p-5 rounded-lg shadow-sm">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">📈 DCA Strategy</h3>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Total Invested:</span>
                                            <span class="font-medium">€{{ number_format($dca_amount + ($monthly_investment * $investment_period_months), 2) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Final Value:</span>
                                            <span class="font-bold text-green-600 text-lg">€{{ number_format($dca_final_value, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Total Gain:</span>
                                            <span class="font-bold text-blue-600">€{{ number_format($dca_gain, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-{{ $difference >= 0 ? 'green' : 'blue' }}-50 p-4 rounded-lg border border-{{ $difference >= 0 ? 'green' : 'blue' }}-200">
                                <div class="text-sm text-{{ $difference >= 0 ? 'green' : 'blue' }}-800 font-medium mb-1">
                                    {{ $difference >= 0 ? '✅ Lump Sum is Better' : '✅ DCA is Better' }}
                                </div>
                                <div class="text-2xl font-bold text-{{ $difference >= 0 ? 'green' : 'blue' }}-600">
                                    €{{ number_format(abs($difference), 2) }}
                                </div>
                                <div class="text-xs text-{{ $difference >= 0 ? 'green' : 'blue' }}-700 mt-1">
                                    {{ $difference >= 0 ? 'Advantage of lump sum' : 'Advantage of DCA' }}
                                </div>
                            </div>
                        @else
                            <div class="bg-white p-5 rounded-lg shadow-sm">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">
                                    {{ $strategy === 'lump_sum' ? '💎 Lump Sum Strategy' : '📈 DCA Strategy' }}
                                </h3>
                                <div class="space-y-2">
                                    @if($strategy === 'lump_sum')
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Initial Investment:</span>
                                            <span class="font-medium">€{{ number_format($lump_sum_amount, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Final Value:</span>
                                            <span class="font-bold text-green-600 text-lg">€{{ number_format($lump_sum_final_value, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Total Gain:</span>
                                            <span class="font-bold text-blue-600">€{{ number_format($lump_sum_gain, 2) }}</span>
                                        </div>
                                    @else
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Total Invested:</span>
                                            <span class="font-medium">€{{ number_format($dca_amount + ($monthly_investment * $investment_period_months), 2) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Final Value:</span>
                                            <span class="font-bold text-green-600 text-lg">€{{ number_format($dca_final_value, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Total Gain:</span>
                                            <span class="font-bold text-blue-600">€{{ number_format($dca_gain, 2) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if(!empty($monthly_breakdown) && $strategy !== 'lump_sum')
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">DCA Progress Over Time</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-white rounded-lg overflow-hidden">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Portfolio Value</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Invested</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Gain</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($monthly_breakdown as $period)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $period['month'] }}</td>
                                                    <td class="px-4 py-3 text-sm text-right font-medium text-green-600">€{{ number_format($period['balance'], 2) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-gray-600">€{{ number_format($period['total_invested'], 2) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-blue-600">€{{ number_format($period['gain'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <h3 class="font-semibold text-blue-900 mb-2">💡 DCA vs Lump Sum Strategy</h3>
                        <ul class="list-disc list-inside space-y-1 text-sm text-blue-800">
                            <li><strong>Lump Sum:</strong> Better when markets consistently rise (time in market beats timing)</li>
                            <li><strong>DCA:</strong> Reduces risk of investing all at once at market peak, smoother entry</li>
                            <li>DCA helps reduce emotional investing and market timing mistakes</li>
                            <li>Lump sum typically wins in upward-trending markets (70% of the time historically)</li>
                            <li>DCA is better if you don't have lump sum available upfront</li>
                            <li>Consider your risk tolerance and market conditions when choosing</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
