<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('financial.calculators.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to Calculators</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">⚠️ Portfolio Risk Calculator</h1>
            <p class="text-gray-600 mb-6">Assess your portfolio risk level and get recommendations</p>

            <form wire:submit.prevent="calculate" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Portfolio Value (€) *</label>
                        <input type="number" wire:model="portfolio_value" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('portfolio_value') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Age *</label>
                        <input type="number" wire:model="age" min="18" max="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('age') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Investment Time Horizon (Years) *</label>
                        <input type="number" wire:model="time_horizon" min="1" max="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('time_horizon') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Portfolio Allocation (must total 100%)</h3>
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
                    Assess Risk
                </button>
            </form>

            @if($show_results)
                <div class="mt-8 space-y-6">
                    <div class="bg-gradient-to-r from-orange-50 to-red-50 p-6 rounded-xl border border-orange-200">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Risk Assessment Results</h2>
                        
                        <div class="grid md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Risk Score</div>
                                <div class="text-3xl font-bold text-orange-600">{{ round($risk_score) }}/100</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Risk Level</div>
                                <div class="text-xl font-bold 
                                    @if($risk_level == 'low') text-green-600
                                    @elseif($risk_level == 'moderate') text-yellow-600
                                    @elseif($risk_level == 'high') text-orange-600
                                    @else text-red-600
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $risk_level)) }}
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Expected Return</div>
                                <div class="text-xl font-bold text-blue-600">{{ number_format($expected_return, 1) }}%</div>
                                <div class="text-xs text-gray-500 mt-1">annual</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Max Loss Potential</div>
                                <div class="text-xl font-bold text-red-600">€{{ number_format($max_loss_potential, 0) }}</div>
                                <div class="text-xs text-gray-500 mt-1">worst case</div>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-lg mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Risk Level Indicator</span>
                                <span class="text-sm font-bold text-gray-900">{{ round($risk_score) }}/100</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-6 relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full h-2 bg-gradient-to-r from-green-500 via-yellow-500 via-orange-500 to-red-500 rounded-full"></div>
                                </div>
                                <div class="h-6 rounded-full border-2 border-gray-900" 
                                     style="width: {{ min(100, $risk_score) }}%">
                                </div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>Low</span>
                                <span>Moderate</span>
                                <span>High</span>
                                <span>Very High</span>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-3">Portfolio Metrics</h3>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <div class="text-sm text-gray-600">Estimated Volatility</div>
                                    <div class="text-lg font-bold text-gray-900">{{ number_format($volatility_estimate, 1) }}%</div>
                                    <div class="text-xs text-gray-500 mt-1">Annual standard deviation</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-600">Portfolio Value</div>
                                    <div class="text-lg font-bold text-blue-600">€{{ number_format($portfolio_value, 0) }}</div>
                                </div>
                            </div>
                        </div>

                        @if(!empty($recommendations))
                            <div class="mt-4 bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <h3 class="font-semibold text-blue-900 mb-2">💡 Recommendations</h3>
                                <ul class="list-disc list-inside space-y-1 text-sm text-blue-800">
                                    @foreach($recommendations as $rec)
                                        <li>{{ $rec }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="font-semibold text-gray-900 mb-2">📖 Understanding Risk Levels</h3>
                        <div class="space-y-2 text-sm text-gray-700">
                            <p><strong>Low Risk (0-30):</strong> Conservative portfolio, minimal volatility, lower returns</p>
                            <p><strong>Moderate Risk (30-50):</strong> Balanced portfolio, moderate volatility, moderate returns</p>
                            <p><strong>High Risk (50-70):</strong> Growth-oriented, higher volatility, higher potential returns</p>
                            <p><strong>Very High Risk (70-100):</strong> Aggressive portfolio, high volatility, highest potential returns</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
