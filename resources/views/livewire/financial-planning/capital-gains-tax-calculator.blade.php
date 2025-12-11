<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('financial.calculators.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to Calculators</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">📈 Capital Gains Tax Calculator</h1>
            <p class="text-gray-600 mb-6">Calculate capital gains tax on investment sales</p>

            <form wire:submit.prevent="calculate" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Price (€) *</label>
                        <input type="number" wire:model="purchase_price" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('purchase_price') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sale Price (€) *</label>
                        <input type="number" wire:model="sale_price" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('sale_price') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Improvement Costs (€) *</label>
                        <input type="number" wire:model="improvement_costs" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Renovations, repairs that add value</p>
                        @error('improvement_costs') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Selling Expenses (€) *</label>
                        <input type="number" wire:model="selling_expenses" min="0" step="500" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Agent fees, closing costs, etc.</p>
                        @error('selling_expenses') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Holding Period (Months) *</label>
                        <input type="number" wire:model="holding_period_months" min="0" step="1" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">12+ months = long-term (lower tax rate)</p>
                        @error('holding_period_months') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filing Status *</label>
                        <select wire:model="filing_status" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="single">Single</option>
                            <option value="married_joint">Married Filing Jointly</option>
                            <option value="married_separate">Married Filing Separately</option>
                            <option value="head_of_household">Head of Household</option>
                        </select>
                        @error('filing_status') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Other Taxable Income (€) *</label>
                        <input type="number" wire:model="taxable_income" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Income from other sources (affects tax bracket)</p>
                        @error('taxable_income') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Calculate Capital Gains Tax
                </button>
            </form>

            @if($show_results)
                <div class="mt-8 space-y-6">
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-6 rounded-xl border border-purple-200">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Capital Gains Tax Results</h2>
                        
                        <div class="grid md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Capital Gain</div>
                                <div class="text-2xl font-bold text-green-600">€{{ number_format($capital_gain, 2) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Taxable Gain</div>
                                <div class="text-xl font-bold text-blue-600">€{{ number_format($taxable_gain, 2) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Capital Gains Tax</div>
                                <div class="text-2xl font-bold text-red-600">€{{ number_format($capital_gains_tax, 2) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Net Profit</div>
                                <div class="text-xl font-bold text-purple-600">€{{ number_format($net_profit, 2) }}</div>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-lg mb-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Tax Calculation Details</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Sale Price:</span>
                                    <span class="font-medium">€{{ number_format($sale_price, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Cost Basis:</span>
                                    <span class="font-medium text-red-600">-€{{ number_format($cost_basis, 2) }}</span>
                                    <span class="text-xs text-gray-500">(Purchase + Improvements + Expenses)</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t">
                                    <span class="font-medium text-gray-900">Capital Gain:</span>
                                    <span class="font-bold text-green-600">€{{ number_format($capital_gain, 2) }}</span>
                                </div>
                                <div class="flex justify-between pt-2">
                                    <span class="text-gray-700">Holding Period:</span>
                                    <span class="font-medium {{ $is_long_term ? 'text-green-600' : 'text-orange-600' }}">
                                        {{ $is_long_term ? 'Long-Term' : 'Short-Term' }} ({{ number_format($holding_period_months / 12, 1) }} years)
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Tax Rate Applied:</span>
                                    <span class="font-medium text-purple-600">{{ number_format($tax_rate_used, 2) }}%</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t font-semibold">
                                    <span class="text-gray-900">Capital Gains Tax:</span>
                                    <span class="text-red-600">€{{ number_format($capital_gains_tax, 2) }}</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t-2 font-bold">
                                    <span class="text-gray-900">Net Profit After Tax:</span>
                                    <span class="text-green-600 text-lg">€{{ number_format($net_profit, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        @if($is_long_term)
                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                <p class="text-sm text-green-800">
                                    ✅ <strong>Long-term capital gain</strong> - You qualify for lower long-term capital gains tax rates (0%, 15%, or 20% depending on income) instead of ordinary income tax rates.
                                </p>
                            </div>
                        @else
                            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                <p class="text-sm text-yellow-800">
                                    ⚠️ <strong>Short-term capital gain</strong> - This is taxed as ordinary income at your regular tax bracket. Consider holding for 12+ months to qualify for lower long-term rates.
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <h3 class="font-semibold text-blue-900 mb-2">💡 Capital Gains Tax Tips</h3>
                        <ul class="list-disc list-inside space-y-1 text-sm text-blue-800">
                            <li>Hold investments 12+ months to qualify for lower long-term capital gains rates</li>
                            <li>Offset gains with capital losses (tax-loss harvesting)</li>
                            <li>Time sales to stay in lower tax brackets (0% for low income)</li>
                            <li>Consider 1031 exchanges for real estate to defer taxes</li>
                            <li>Track all costs that increase your basis (improvements, expenses)</li>
                            <li>Consider gifting appreciated assets to reduce estate taxes</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
