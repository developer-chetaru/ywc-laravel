<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('financial.calculators.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to Calculators</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">💾 Savings Rate Calculator</h1>
            <p class="text-gray-600 mb-6">Calculate and optimize your savings rate for financial independence</p>

            <form wire:submit.prevent="calculate" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Annual Income (€) *</label>
                        <input type="number" wire:model="annual_income" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('annual_income') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Annual Expenses (€) *</label>
                        <input type="number" wire:model="annual_expenses" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('annual_expenses') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Savings (€)</label>
                        <input type="number" wire:model="savings_amount" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Optional: for FIRE calculation</p>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Calculate Savings Rate
                </button>
            </form>

            @if($show_results)
                <div class="mt-8 space-y-6">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-xl border border-green-200">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Your Savings Analysis</h2>
                        
                        <div class="grid md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Savings Rate</div>
                                <div class="text-3xl font-bold text-green-600">{{ number_format($savings_rate, 1) }}%</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Annual Savings</div>
                                <div class="text-2xl font-bold text-blue-600">€{{ number_format($annual_savings, 0) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Monthly Savings</div>
                                <div class="text-xl font-bold text-purple-600">€{{ number_format($monthly_savings, 0) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Years to FIRE</div>
                                <div class="text-2xl font-bold text-orange-600">{{ $years_to_fire }}</div>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-lg mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Savings Rate Progress</span>
                                <span class="text-sm font-bold text-gray-900">{{ number_format($savings_rate, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-gradient-to-r from-green-500 to-emerald-600 h-4 rounded-full flex items-center justify-end pr-2" 
                                     style="width: {{ min(100, $savings_rate) }}%">
                                    @if($savings_rate >= 50)
                                        <span class="text-xs text-white font-bold">🔥 FIRE!</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>0%</span>
                                <span>20% (Good)</span>
                                <span>50% (Excellent)</span>
                                <span>100%</span>
                            </div>
                        </div>

                        @if(!empty($recommendations))
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <h3 class="font-semibold text-blue-900 mb-2">💡 Recommendations</h3>
                                <ul class="list-disc list-inside space-y-1 text-sm text-blue-800">
                                    @foreach($recommendations as $rec)
                                        <li>{{ $rec }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($years_to_fire > 0 && $years_to_fire < 100)
                            <div class="mt-4 bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                <h3 class="font-semibold text-yellow-900 mb-2">🎯 Financial Independence (FIRE)</h3>
                                <p class="text-sm text-yellow-800">
                                    Based on your savings rate, you could achieve financial independence in approximately 
                                    <strong>{{ $years_to_fire }} years</strong> with a target of €{{ number_format($fire_target, 0) }} 
                                    (25x annual expenses using the 4% rule).
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
