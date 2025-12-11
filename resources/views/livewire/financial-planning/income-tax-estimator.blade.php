<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('financial.calculators.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to Calculators</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">💰 Income Tax Estimator</h1>
            <p class="text-gray-600 mb-6">Estimate your income tax liability and effective tax rate</p>

            <form wire:submit.prevent="calculate" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Annual Income (€) *</label>
                        <input type="number" wire:model="annual_income" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('annual_income') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tax Deductions (€) *</label>
                        <input type="number" wire:model="tax_deductions" min="0" step="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Standard or itemized deductions</p>
                        @error('tax_deductions') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tax Credits (€) *</label>
                        <input type="number" wire:model="tax_credits" min="0" step="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('tax_credits') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tax Withholdings (€) *</label>
                        <input type="number" wire:model="withholdings" min="0" step="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Already paid through withholdings</p>
                        @error('withholdings') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Calculate Tax
                </button>
            </form>

            @if($show_results)
                <div class="mt-8 space-y-6">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-200">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Tax Calculation Results</h2>
                        
                        <div class="grid md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Taxable Income</div>
                                <div class="text-xl font-bold text-gray-900">€{{ number_format($taxable_income, 2) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Tax Owed</div>
                                <div class="text-2xl font-bold text-red-600">€{{ number_format($tax_after_credits, 2) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Effective Tax Rate</div>
                                <div class="text-2xl font-bold text-blue-600">{{ number_format($effective_tax_rate, 2) }}%</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Marginal Tax Rate</div>
                                <div class="text-2xl font-bold text-purple-600">{{ number_format($marginal_tax_rate, 1) }}%</div>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-lg mb-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Tax Breakdown</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Annual Income:</span>
                                    <span class="font-medium">€{{ number_format($annual_income, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Deductions:</span>
                                    <span class="font-medium text-green-600">-€{{ number_format($tax_deductions, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Taxable Income:</span>
                                    <span class="font-medium">€{{ number_format($taxable_income, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Tax Before Credits:</span>
                                    <span class="font-medium">€{{ number_format($tax_before_credits, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Tax Credits:</span>
                                    <span class="font-medium text-green-600">-€{{ number_format($tax_credits, 2) }}</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t font-semibold">
                                    <span class="text-gray-900">Tax Owed:</span>
                                    <span class="text-red-600">€{{ number_format($tax_after_credits, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Withholdings:</span>
                                    <span class="font-medium">€{{ number_format($withholdings, 2) }}</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t-2 font-bold">
                                    <span class="text-gray-900">
                                        {{ $refund_or_owe >= 0 ? 'Refund:' : 'Amount Owed:' }}
                                    </span>
                                    <span class="{{ $refund_or_owe >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        €{{ number_format(abs($refund_or_owe), 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if(!empty($tax_brackets_used))
                            <div class="mt-4">
                                <h3 class="font-semibold text-gray-900 mb-3">Tax Brackets Used</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-white rounded-lg overflow-hidden">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bracket</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Taxable Amount</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Rate</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tax</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($tax_brackets_used as $bracket)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $bracket['bracket'] }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-gray-600">€{{ number_format($bracket['taxable'], 2) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-blue-600">{{ number_format($bracket['rate'], 1) }}%</td>
                                                    <td class="px-4 py-3 text-sm text-right text-red-600">€{{ number_format($bracket['tax'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <h3 class="font-semibold text-blue-900 mb-2">💡 Tax Planning Tips</h3>
                        <ul class="list-disc list-inside space-y-1 text-sm text-blue-800">
                            <li>Maximize deductions: retirement contributions, health savings accounts, charitable giving</li>
                            <li>Take advantage of tax credits which reduce tax dollar-for-dollar</li>
                            <li>Consider tax-advantaged accounts (401k, IRA, pension plans)</li>
                            <li>Plan timing of income and deductions for tax optimization</li>
                            <li>Consult with a tax professional for complex situations</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
