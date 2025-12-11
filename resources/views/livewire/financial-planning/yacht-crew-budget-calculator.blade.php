<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('financial.calculators.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to Calculators</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">⚓ Yacht Crew Budget Calculator</h1>
            <p class="text-gray-600 mb-6">Plan your budget for working periods and time off</p>

            <form wire:submit.prevent="calculate" class="space-y-6">
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">💰 Working Period Income</h3>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Salary (€) *</label>
                            <input type="number" wire:model="monthly_salary" min="0" step="100" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('monthly_salary') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tips per Month (€) *</label>
                            <input type="number" wire:model="tips_per_month" min="0" step="50" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('tips_per_month') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Working Months/Year *</label>
                            <input type="number" wire:model="working_months_per_year" min="1" max="12" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('working_months_per_year') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🏢 Working Period Expenses</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Expenses (€) *</label>
                            <input type="number" wire:model="working_expenses_monthly" min="0" step="10" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Phone, personal items, etc.</p>
                            @error('working_expenses_monthly') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Travel to Yacht (€) *</label>
                            <input type="number" wire:model="travel_to_yacht" min="0" step="50" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">One-time per contract</p>
                            @error('travel_to_yacht') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🏝️ Time Off Expenses</h3>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Time Off Months/Year *</label>
                        <input type="number" wire:model="time_off_months_per_year" min="0" max="12" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('time_off_months_per_year') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rent/Month (€) *</label>
                            <input type="number" wire:model="rent_monthly" min="0" step="50" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('rent_monthly') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Utilities (€) *</label>
                            <input type="number" wire:model="utilities_monthly" min="0" step="10" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('utilities_monthly') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Food (€) *</label>
                            <input type="number" wire:model="food_time_off_monthly" min="0" step="50" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('food_time_off_monthly') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Transportation (€) *</label>
                            <input type="number" wire:model="transportation_monthly" min="0" step="50" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('transportation_monthly') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Insurance (€) *</label>
                            <input type="number" wire:model="insurance_monthly" min="0" step="10" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('insurance_monthly') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Other Expenses (€) *</label>
                            <input type="number" wire:model="other_expenses_monthly" min="0" step="50" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('other_expenses_monthly') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Calculate Budget
                </button>
            </form>

            @if($show_results)
                <div class="mt-8 space-y-6">
                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 p-6 rounded-xl border border-blue-200">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Annual Budget Summary</h2>
                        
                        <div class="grid md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Annual Income</div>
                                <div class="text-2xl font-bold text-green-600">€{{ number_format($annual_income, 2) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Annual Expenses</div>
                                <div class="text-2xl font-bold text-red-600">€{{ number_format($annual_expenses, 2) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Annual Savings</div>
                                <div class="text-2xl font-bold text-blue-600">€{{ number_format($annual_savings, 2) }}</div>
                                <div class="text-xs text-gray-500 mt-1">Savings Rate: {{ number_format($actual_savings_rate, 1) }}%</div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Working Period ({{ $working_months_per_year }} months)</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">Monthly Income:</span>
                                        <span class="font-medium text-green-600">€{{ number_format($monthly_budget_summary['working']['income'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">Monthly Expenses:</span>
                                        <span class="font-medium text-red-600">€{{ number_format($monthly_budget_summary['working']['expenses'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t">
                                        <span class="font-semibold text-gray-900">Monthly Net:</span>
                                        <span class="font-bold text-blue-600">€{{ number_format($monthly_budget_summary['working']['net'], 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Time Off ({{ $time_off_months_per_year }} months)</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">Monthly Expenses:</span>
                                        <span class="font-medium text-red-600">€{{ number_format($time_off_monthly_expenses, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">Total Time Off Cost:</span>
                                        <span class="font-medium text-red-600">€{{ number_format($time_off_monthly_expenses * $time_off_months_per_year, 2) }}</span>
                                    </div>
                                    <div class="pt-2 border-t">
                                        <p class="text-sm text-gray-600">Required savings per working month:</p>
                                        <p class="text-lg font-bold text-orange-600">
                                            €{{ number_format($time_off_months_per_year > 0 ? ($time_off_monthly_expenses * $time_off_months_per_year) / max(1, $working_months_per_year) : 0, 2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <h3 class="font-semibold text-yellow-900 mb-2">💡 Budget Tips</h3>
                        <ul class="list-disc list-inside space-y-1 text-sm text-yellow-800">
                            <li>Save during working months to cover time off expenses</li>
                            <li>Consider temporary housing or house-sitting during time off to reduce rent</li>
                            <li>Plan major purchases during working periods when expenses are lower</li>
                            <li>Aim to save at least 30-40% of income for long-term financial security</li>
                            <li>Keep an emergency fund of 3-6 months of time-off expenses</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
