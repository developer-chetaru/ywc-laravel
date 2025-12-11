<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('financial.calculators.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to Calculators</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🏘️ Buy vs Rent Calculator</h1>
            <p class="text-gray-600 mb-6">Compare the financial benefits of buying vs renting a home</p>

            <form wire:submit.prevent="calculate" class="space-y-6">
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Home Purchase</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Home Price (€) *</label>
                            <input type="number" wire:model="home_price" min="0" step="10000" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('home_price') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Down Payment (€) *</label>
                            <input type="number" wire:model="down_payment" min="0" step="5000" 
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
                    </div>
                </div>

                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Buying Costs</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Property Tax Rate (%) *</label>
                            <input type="number" wire:model="property_tax_rate" min="0" max="10" step="0.1" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Annual % of home value</p>
                            @error('property_tax_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
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
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maintenance Rate (%) *</label>
                            <input type="number" wire:model="maintenance_rate" min="0" max="10" step="0.1" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Annual % of home value (typically 1%)</p>
                            @error('maintenance_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Renting</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Rent (€) *</label>
                            <input type="number" wire:model="monthly_rent" min="0" step="100" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('monthly_rent') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Annual Rent Increase (%) *</label>
                            <input type="number" wire:model="rent_increase_rate" min="0" max="20" step="0.5" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Typical: 2-5%</p>
                            @error('rent_increase_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Assumptions</h3>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Home Appreciation (%) *</label>
                            <input type="number" wire:model="home_appreciation_rate" min="0" max="20" step="0.5" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Annual home value increase</p>
                            @error('home_appreciation_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Investment Return (%) *</label>
                            <input type="number" wire:model="investment_return" min="0" max="30" step="0.5" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">If renting, invest savings</p>
                            @error('investment_return') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Time Horizon (Years) *</label>
                            <input type="number" wire:model="time_horizon_years" min="1" max="100" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('time_horizon_years') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Compare Buy vs Rent
                </button>
            </form>

            @if($show_results)
                <div class="mt-8 space-y-6">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-200">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Buy vs Rent Comparison</h2>
                        
                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-white p-5 rounded-lg shadow-sm">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">🏠 Buying</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total Cost:</span>
                                        <span class="font-medium text-red-600">€{{ number_format($buying_total_cost, 0) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Net Worth:</span>
                                        <span class="font-bold text-green-600 text-lg">€{{ number_format($buying_net_worth, 0) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-5 rounded-lg shadow-sm">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">🏡 Renting</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total Cost:</span>
                                        <span class="font-medium text-red-600">€{{ number_format($renting_total_cost, 0) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Net Worth:</span>
                                        <span class="font-bold text-green-600 text-lg">€{{ number_format($renting_net_worth, 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-{{ $difference >= 0 ? 'green' : 'red' }}-50 p-4 rounded-lg border border-{{ $difference >= 0 ? 'green' : 'red' }}-200 mb-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-{{ $difference >= 0 ? 'green' : 'red' }}-800 font-medium">
                                        {{ $difference >= 0 ? '✅ Buying is Better' : '✅ Renting is Better' }}
                                    </div>
                                    <div class="text-2xl font-bold text-{{ $difference >= 0 ? 'green' : 'red' }}-600 mt-1">
                                        €{{ number_format(abs($difference), 0) }}
                                    </div>
                                    <div class="text-xs text-{{ $difference >= 0 ? 'green' : 'red' }}-700 mt-1">
                                        {{ $difference >= 0 ? 'Net advantage of buying' : 'Net advantage of renting' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($break_even_years > 0 && $break_even_years <= $time_horizon_years)
                            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200 mb-4">
                                <p class="text-sm text-yellow-800">
                                    💡 <strong>Break-even point:</strong> Buying becomes financially better after approximately 
                                    <strong>{{ $break_even_years }} years</strong>.
                                </p>
                            </div>
                        @endif

                        @if(!empty($yearly_breakdown))
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Year-by-Year Breakdown</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-white rounded-lg overflow-hidden">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Year</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Home Value</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Buying Cost</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Rent Cost</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Equity Built</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach(array_slice($yearly_breakdown, 0, 10) as $year)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $year['year'] }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-gray-900">€{{ number_format($year['home_value'], 0) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-red-600">€{{ number_format($year['buying_cost'], 0) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-blue-600">€{{ number_format($year['rent_cost'], 0) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-green-600">€{{ number_format($year['principal_paid'], 0) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if(count($yearly_breakdown) > 10)
                                        <p class="text-xs text-gray-500 mt-2">Showing first 10 years of {{ count($yearly_breakdown) }} years</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <h3 class="font-semibold text-blue-900 mb-2">💡 Buy vs Rent Considerations</h3>
                        <ul class="list-disc list-inside space-y-1 text-sm text-blue-800">
                            <li><strong>Buy if:</strong> You plan to stay 5+ years, want stability, building equity is important</li>
                            <li><strong>Rent if:</strong> You move frequently, prefer flexibility, want to invest savings elsewhere</li>
                            <li>Home ownership includes hidden costs: maintenance, repairs, property taxes, insurance</li>
                            <li>Consider transaction costs when buying/selling (5-10% of home value)</li>
                            <li>Renting allows investing down payment elsewhere for potentially higher returns</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
