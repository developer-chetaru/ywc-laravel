<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('financial.calculators.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to Calculators</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🏝️ Time-Off Expense Planner</h1>
            <p class="text-gray-600 mb-6">Plan and budget for your time off between yacht contracts</p>

            <form wire:submit.prevent="calculate" class="space-y-6">
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Time Off Period</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Months of Time Off *</label>
                        <input type="number" wire:model="time_off_months" min="1" max="12" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('time_off_months') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Expenses</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rent (€) *</label>
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
                            <input type="number" wire:model="food_monthly" min="0" step="50" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('food_monthly') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Entertainment (€) *</label>
                            <input type="number" wire:model="entertainment_monthly" min="0" step="50" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('entertainment_monthly') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Healthcare (€) *</label>
                            <input type="number" wire:model="healthcare_monthly" min="0" step="10" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('healthcare_monthly') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Other Expenses (€) *</label>
                            <input type="number" wire:model="other_expenses_monthly" min="0" step="50" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('other_expenses_monthly') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Travel & Savings</h3>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Travel Budget (€) *</label>
                            <input type="number" wire:model="travel_budget" min="0" step="100" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Total for time off period</p>
                            @error('travel_budget') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Savings (€) *</label>
                            <input type="number" wire:model="current_savings" min="0" step="100" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('current_savings') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Savings per Month (€) *</label>
                            <input type="number" wire:model="savings_per_month" min="0" step="100" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('savings_per_month') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Plan My Time Off
                </button>
            </form>

            @if($show_results)
                <div class="mt-8 space-y-6">
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-6 rounded-xl border border-purple-200">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Time-Off Budget Plan</h2>
                        
                        <div class="grid md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Monthly Total</div>
                                <div class="text-2xl font-bold text-purple-600">€{{ number_format($monthly_total, 0) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Total Needed</div>
                                <div class="text-xl font-bold text-red-600">€{{ number_format($total_needed, 0) }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $time_off_months }} months</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Savings Required</div>
                                <div class="text-xl font-bold text-orange-600">€{{ number_format($savings_required, 0) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Months to Save</div>
                                <div class="text-2xl font-bold text-blue-600">{{ $months_to_save }}</div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Monthly Expense Breakdown</h3>
                                <div class="space-y-2">
                                    @foreach($monthly_breakdown as $item)
                                        <div class="flex justify-between items-center py-2 border-b">
                                            <span class="text-gray-700">{{ $item['category'] }}</span>
                                            <span class="font-medium text-gray-900">€{{ number_format($item['amount'], 2) }}</span>
                                        </div>
                                    @endforeach
                                    <div class="flex justify-between items-center py-2 border-t-2 border-gray-300 font-semibold">
                                        <span class="text-gray-900">Monthly Total</span>
                                        <span class="text-purple-600">€{{ number_format($monthly_total, 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Budget Summary</h3>
                                <div class="space-y-3">
                                    <div class="bg-blue-50 p-3 rounded-lg">
                                        <div class="text-sm text-gray-600">Current Savings</div>
                                        <div class="text-xl font-bold text-blue-600">€{{ number_format($current_savings, 2) }}</div>
                                    </div>
                                    <div class="bg-orange-50 p-3 rounded-lg">
                                        <div class="text-sm text-gray-600">Additional Savings Needed</div>
                                        <div class="text-xl font-bold text-orange-600">€{{ number_format($savings_required, 2) }}</div>
                                    </div>
                                    @if($months_to_save > 0)
                                        <div class="bg-green-50 p-3 rounded-lg">
                                            <div class="text-sm text-gray-600">Time to Save</div>
                                            <div class="text-xl font-bold text-green-600">{{ $months_to_save }} months</div>
                                            <div class="text-xs text-gray-500 mt-1">At €{{ number_format($savings_per_month, 0) }}/month</div>
                                        </div>
                                    @else
                                        <div class="bg-green-50 p-3 rounded-lg">
                                            <div class="text-sm text-green-800">✅ You have enough savings!</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <h3 class="font-semibold text-yellow-900 mb-2">💡 Time-Off Planning Tips</h3>
                        <ul class="list-disc list-inside space-y-1 text-sm text-yellow-800">
                            <li>Start saving early - divide total needed by months until time off</li>
                            <li>Consider house-sitting or short-term rentals to reduce accommodation costs</li>
                            <li>Plan travel during shoulder seasons for better deals</li>
                            <li>Keep an emergency buffer of 20% extra for unexpected expenses</li>
                            <li>Take advantage of free activities and local experiences</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
