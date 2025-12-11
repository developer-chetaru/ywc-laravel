<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('financial.calculators.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to Calculators</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🏘️ Rental Property Analyzer</h1>
            <p class="text-gray-600 mb-6">Analyze rental property investment returns and cash flow</p>

            <form wire:submit.prevent="calculate" class="space-y-6">
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Property Details</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Property Price (€) *</label>
                            <input type="number" wire:model="property_price" min="0" step="10000" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('property_price') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Down Payment (€) *</label>
                            <input type="number" wire:model="down_payment" min="0" step="5000" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('down_payment') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Loan Term (Years) *</label>
                            <input type="number" wire:model="loan_term_years" min="1" max="50" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('loan_term_years') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Interest Rate (%) *</label>
                            <input type="number" wire:model="interest_rate" min="0" max="30" step="0.1" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('interest_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Rental Income</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Rent (€) *</label>
                            <input type="number" wire:model="monthly_rent" min="0" step="100" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('monthly_rent') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vacancy Rate (%) *</label>
                            <input type="number" wire:model="vacancy_rate" min="0" max="50" step="0.5" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Typical: 5-10%</p>
                            @error('vacancy_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Property Management (%) *</label>
                            <input type="number" wire:model="property_management_rate" min="0" max="50" step="0.5" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">% of rent (typical: 8-12%)</p>
                            @error('property_management_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Appreciation Rate (%) *</label>
                            <input type="number" wire:model="appreciation_rate" min="0" max="20" step="0.5" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Annual property value increase</p>
                            @error('appreciation_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Expenses</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maintenance Rate (%) *</label>
                            <input type="number" wire:model="maintenance_rate" min="0" max="10" step="0.1" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Annual % of property value (typical: 1%)</p>
                            @error('maintenance_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Property Tax (€/year) *</label>
                            <input type="number" wire:model="property_tax_annual" min="0" step="100" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('property_tax_annual') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Insurance (€/year) *</label>
                            <input type="number" wire:model="insurance_annual" min="0" step="100" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('insurance_annual') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
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
                    Analyze Property
                </button>
            </form>

            @if($show_results)
                <div class="mt-8 space-y-6">
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-6 rounded-xl border border-purple-200">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Investment Analysis</h2>
                        
                        <div class="grid md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Monthly Cash Flow</div>
                                <div class="text-2xl font-bold {{ $monthly_cash_flow >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    €{{ number_format($monthly_cash_flow, 2) }}
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Cash-on-Cash Return</div>
                                <div class="text-2xl font-bold text-blue-600">{{ number_format($cash_on_cash_return, 2) }}%</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Cap Rate</div>
                                <div class="text-2xl font-bold text-purple-600">{{ number_format($cap_rate, 2) }}%</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">ROI</div>
                                <div class="text-2xl font-bold text-green-600">{{ number_format($roi, 2) }}%</div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-white p-4 rounded-lg">
                                <h3 class="font-semibold text-gray-900 mb-3">Monthly Breakdown</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">Effective Rent:</span>
                                        <span class="font-medium text-green-600">
                                            €{{ number_format($monthly_rent * (1 - ($vacancy_rate / 100)), 2) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">Mortgage Payment:</span>
                                        <span class="font-medium text-red-600">€{{ number_format($monthly_mortgage, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">Other Expenses:</span>
                                        <span class="font-medium text-red-600">
                                            €{{ number_format($monthly_expenses - $monthly_mortgage, 2) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t font-semibold">
                                        <span class="text-gray-900">Cash Flow:</span>
                                        <span class="{{ $monthly_cash_flow >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            €{{ number_format($monthly_cash_flow, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-4 rounded-lg">
                                <h3 class="font-semibold text-gray-900 mb-3">Key Metrics</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">Gross Rent Multiplier:</span>
                                        <span class="font-medium">{{ number_format($gross_rent_multiplier, 2) }}x</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">DSCR:</span>
                                        <span class="font-medium {{ $debt_service_coverage >= 1.25 ? 'text-green-600' : 'text-orange-600' }}">
                                            {{ number_format($debt_service_coverage, 2) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">Annual Cash Flow:</span>
                                        <span class="font-medium {{ $annual_cash_flow >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            €{{ number_format($annual_cash_flow, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($monthly_cash_flow < 0)
                            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                <p class="text-sm text-red-800">
                                    ⚠️ <strong>Negative Cash Flow:</strong> This property has negative monthly cash flow of 
                                    €{{ number_format(abs($monthly_cash_flow), 2) }}. You'll need to cover this from other income.
                                </p>
                            </div>
                        @elseif($cash_on_cash_return < 8)
                            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                <p class="text-sm text-yellow-800">
                                    💡 Cash-on-cash return is below 8%. Consider if the appreciation and tax benefits justify the investment.
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <h3 class="font-semibold text-blue-900 mb-2">💡 Investment Tips</h3>
                        <ul class="list-disc list-inside space-y-1 text-sm text-blue-800">
                            <li>Aim for cash-on-cash return of 8-12% or higher for good investments</li>
                            <li>Cap rate of 6-10% is typically considered good in most markets</li>
                            <li>DSCR (Debt Service Coverage Ratio) above 1.25 is preferred by lenders</li>
                            <li>GRM below 15 is often considered a good investment</li>
                            <li>Factor in all expenses: maintenance, vacancy, property management, taxes</li>
                            <li>Consider property appreciation and tax benefits in your overall return</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
