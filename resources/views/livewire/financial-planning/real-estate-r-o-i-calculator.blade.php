<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('financial.calculators.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to Calculators</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🏡 Real Estate ROI Calculator</h1>
            <p class="text-gray-600 mb-6">Calculate total return on investment for real estate property</p>

            <form wire:submit.prevent="calculate" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Price (€) *</label>
                        <input type="number" wire:model="purchase_price" min="0" step="10000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('purchase_price') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Down Payment (€) *</label>
                        <input type="number" wire:model="down_payment" min="0" step="5000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('down_payment') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Closing Costs (€) *</label>
                        <input type="number" wire:model="closing_costs" min="0" step="500" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('closing_costs') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Renovation Costs (€) *</label>
                        <input type="number" wire:model="renovation_costs" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('renovation_costs') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Annual Rental Income (€) *</label>
                        <input type="number" wire:model="annual_rental_income" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('annual_rental_income') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vacancy Rate (%) *</label>
                        <input type="number" wire:model="vacancy_rate" min="0" max="100" step="0.5" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('vacancy_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Annual Expenses (€) *</label>
                        <input type="number" wire:model="annual_expenses" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('annual_expenses') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Years Held *</label>
                        <input type="number" wire:model="years_held" min="1" max="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('years_held') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Annual Appreciation (%) *</label>
                        <input type="number" wire:model="annual_appreciation_rate" min="0" max="20" step="0.5" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('annual_appreciation_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Selling Price (€) *</label>
                        <input type="number" wire:model="selling_price" min="0" step="10000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Leave 0 to calculate from appreciation</p>
                        @error('selling_price') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Calculate ROI
                </button>
            </form>

            @if($show_results)
                <div class="mt-8 space-y-6">
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-6 rounded-xl border border-purple-200">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 ROI Analysis</h2>
                        
                        <div class="grid md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Total Investment</div>
                                <div class="text-xl font-bold text-gray-900">€{{ number_format($total_investment, 0) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Total Return</div>
                                <div class="text-2xl font-bold text-green-600">€{{ number_format($total_return, 0) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Total ROI</div>
                                <div class="text-2xl font-bold text-blue-600">{{ number_format($roi_percentage, 1) }}%</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Annualized ROI</div>
                                <div class="text-xl font-bold text-purple-600">{{ number_format($annualized_roi, 1) }}%</div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-white p-4 rounded-lg">
                                <h3 class="font-semibold text-gray-900 mb-3">Returns Breakdown</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">Total Cash Flow:</span>
                                        <span class="font-medium text-green-600">€{{ number_format($total_cash_flow, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">Appreciation Gain:</span>
                                        <span class="font-medium text-blue-600">€{{ number_format($appreciation_gain, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t font-semibold">
                                        <span class="text-gray-900">Total Return:</span>
                                        <span class="text-purple-600">€{{ number_format($total_return, 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-4 rounded-lg">
                                <h3 class="font-semibold text-gray-900 mb-3">Investment Summary</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">Initial Investment:</span>
                                        <span class="font-medium">€{{ number_format($total_investment, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">Annual Cash Flow:</span>
                                        <span class="font-medium text-green-600">€{{ number_format($annual_cash_flow, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t font-semibold">
                                        <span class="text-gray-900">Net Profit:</span>
                                        <span class="text-green-600">€{{ number_format($total_return, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
