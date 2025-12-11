<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('financial.calculators.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to Calculators</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🏠 Mortgage Affordability Calculator</h1>
            <p class="text-gray-600 mb-6">Calculate how much house you can afford based on your income and debt</p>

            <form wire:submit.prevent="calculate" class="space-y-6">
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Income & Debt</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Annual Income (€) *</label>
                            <input type="number" wire:model="annual_income" min="0" step="1000" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('annual_income') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Debt Payments (€) *</label>
                            <input type="number" wire:model="monthly_debt_payments" min="0" step="50" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Credit cards, car loans, etc.</p>
                            @error('monthly_debt_payments') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Mortgage Details</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Down Payment (€) *</label>
                            <input type="number" wire:model="down_payment" min="0" step="1000" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('down_payment') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Interest Rate (%) *</label>
                            <input type="number" wire:model="interest_rate" min="0" max="30" step="0.1" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('interest_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Loan Term (Years) *</label>
                            <input type="number" wire:model="loan_term_years" min="1" max="50" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('loan_term_years') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">DTI Ratio Limit (%) *</label>
                            <input type="number" wire:model="dti_ratio_limit" min="20" max="50" step="1" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Typical: 28-36% for housing, 43% total</p>
                            @error('dti_ratio_limit') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Costs</h3>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Property Tax (€/year) *</label>
                            <input type="number" wire:model="property_tax_annual" min="0" step="100" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('property_tax_annual') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Home Insurance (€/year) *</label>
                            <input type="number" wire:model="home_insurance_annual" min="0" step="100" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('home_insurance_annual') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">HOA Fees (€/month) *</label>
                            <input type="number" wire:model="hoa_fees_monthly" min="0" step="50" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('hoa_fees_monthly') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Calculate Affordability
                </button>
            </form>

            @if($show_results)
                <div class="mt-8 space-y-6">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-xl border border-green-200">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">💰 Affordability Results</h2>
                        
                        <div class="grid md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Max Home Price</div>
                                <div class="text-2xl font-bold text-green-600">€{{ number_format($max_home_price, 0) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Max Loan Amount</div>
                                <div class="text-xl font-bold text-blue-600">€{{ number_format($max_loan_amount, 0) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Total Monthly Payment</div>
                                <div class="text-xl font-bold text-purple-600">€{{ number_format($total_monthly_payment, 2) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">DTI Ratio</div>
                                <div class="text-2xl font-bold {{ $dti_ratio > $dti_ratio_limit ? 'text-red-600' : 'text-green-600' }}">
                                    {{ number_format($dti_ratio, 1) }}%
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-lg mb-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Monthly Payment Breakdown</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Principal & Interest:</span>
                                    <span class="font-medium">€{{ number_format($principal_interest, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Property Tax:</span>
                                    <span class="font-medium">€{{ number_format($property_tax_annual / 12, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Home Insurance:</span>
                                    <span class="font-medium">€{{ number_format($home_insurance_annual / 12, 2) }}</span>
                                </div>
                                @if($hoa_fees_monthly > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-700">HOA Fees:</span>
                                    <span class="font-medium">€{{ number_format($hoa_fees_monthly, 2) }}</span>
                                </div>
                                @endif
                                <div class="flex justify-between pt-2 border-t font-semibold">
                                    <span class="text-gray-900">Total Monthly Payment:</span>
                                    <span class="text-blue-600">€{{ number_format($total_monthly_payment, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        @if($dti_ratio > $dti_ratio_limit)
                            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                <p class="text-sm text-red-800">
                                    ⚠️ Your debt-to-income ratio ({{ number_format($dti_ratio, 1) }}%) exceeds the recommended limit 
                                    of {{ $dti_ratio_limit }}%. Consider reducing debt or increasing income.
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <h3 class="font-semibold text-blue-900 mb-2">💡 Home Buying Tips</h3>
                        <ul class="list-disc list-inside space-y-1 text-sm text-blue-800">
                            <li>Keep total housing costs below 28-30% of gross income</li>
                            <li>Aim for 20% down payment to avoid PMI (private mortgage insurance)</li>
                            <li>Factor in maintenance costs (1% of home value annually)</li>
                            <li>Consider closing costs (2-5% of home price)</li>
                            <li>Build emergency fund before buying (3-6 months expenses)</li>
                            <li>Get pre-approved before house hunting</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
