<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('financial.calculators.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to Calculators</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">⚖️ Asset Allocation Analyzer</h1>
            <p class="text-gray-600 mb-6">Get personalized asset allocation recommendations based on your age, risk tolerance, and time horizon</p>

            <form wire:submit.prevent="calculate" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Age *</label>
                        <input type="number" wire:model="age" min="18" max="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('age') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Risk Tolerance *</label>
                        <select wire:model="risk_tolerance" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="conservative">Conservative</option>
                            <option value="moderate">Moderate</option>
                            <option value="aggressive">Aggressive</option>
                        </select>
                        @error('risk_tolerance') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Investment Time Horizon (Years) *</label>
                        <input type="number" wire:model="time_horizon" min="1" max="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('time_horizon') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Investment Amount (€)</label>
                        <input type="number" wire:model="investment_amount" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('investment_amount') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Asset Allocation</h3>
                    <p class="text-sm text-gray-600 mb-4">Enter your current allocation percentages (must total 100%)</p>
                    
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stocks (%)</label>
                            <input type="number" wire:model="stocks_percent" min="0" max="100" step="1" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bonds (%)</label>
                            <input type="number" wire:model="bonds_percent" min="0" max="100" step="1" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cash (%)</label>
                            <input type="number" wire:model="cash_percent" min="0" max="100" step="1" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Real Estate (%)</label>
                            <input type="number" wire:model="real_estate_percent" min="0" max="100" step="1" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Other (%)</label>
                            <input type="number" wire:model="other_percent" min="0" max="100" step="1" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    @error('allocation') 
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                    
                    <p class="text-sm text-gray-500 mt-2">
                        Total: {{ $stocks_percent + $bonds_percent + $cash_percent + $real_estate_percent + $other_percent }}%
                    </p>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Analyze Allocation
                </button>
            </form>

            @if($show_results)
                <div class="mt-8 space-y-6">
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 p-6 rounded-xl border border-purple-200">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Analysis Results</h2>
                        
                        <div class="grid md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Risk Score</div>
                                <div class="text-3xl font-bold text-orange-600">{{ round($risk_score) }}</div>
                                <div class="text-xs text-gray-500 mt-1">out of 100</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Expected Return</div>
                                <div class="text-2xl font-bold text-green-600">{{ number_format($expected_return * 100, 1) }}%</div>
                                <div class="text-xs text-gray-500 mt-1">annual</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Investment Amount</div>
                                <div class="text-xl font-bold text-blue-600">€{{ number_format($investment_amount, 0) }}</div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Your Current Allocation</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-700">Stocks</span>
                                        <span class="font-medium">{{ $current_allocation['stocks'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $current_allocation['stocks'] }}%"></div>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-700">Bonds</span>
                                        <span class="font-medium">{{ $current_allocation['bonds'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $current_allocation['bonds'] }}%"></div>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-700">Cash</span>
                                        <span class="font-medium">{{ $current_allocation['cash'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ $current_allocation['cash'] }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Recommended Allocation</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-700">Stocks</span>
                                        <span class="font-medium">{{ $recommended_allocation['stocks'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $recommended_allocation['stocks'] }}%"></div>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-700">Bonds</span>
                                        <span class="font-medium">{{ $recommended_allocation['bonds'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $recommended_allocation['bonds'] }}%"></div>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-700">Cash</span>
                                        <span class="font-medium">{{ $recommended_allocation['cash'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ $recommended_allocation['cash'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(!empty($recommendations))
                            <div class="mt-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <h3 class="font-semibold text-blue-900 mb-2">💡 Recommendations</h3>
                                <ul class="list-disc list-inside space-y-1 text-sm text-blue-800">
                                    @foreach($recommendations as $rec)
                                        <li>{{ $rec }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
