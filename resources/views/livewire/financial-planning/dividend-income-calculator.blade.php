<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('financial.calculators.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to Calculators</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">💰 Dividend Income Calculator</h1>
            <p class="text-gray-600 mb-6">Project your dividend income and portfolio growth over time</p>

            <form wire:submit.prevent="calculate" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Portfolio Value (€) *</label>
                        <input type="number" wire:model="portfolio_value" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('portfolio_value') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dividend Yield (%) *</label>
                        <input type="number" wire:model="dividend_yield" min="0" max="20" step="0.1" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('dividend_yield') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Annual dividend yield (typical: 2-5%)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dividend Growth Rate (%) *</label>
                        <input type="number" wire:model="dividend_growth_rate" min="0" max="20" step="0.5" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('dividend_growth_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Annual dividend increase rate</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Years to Project *</label>
                        <input type="number" wire:model="years" min="1" max="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('years') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Addition (€) *</label>
                        <input type="number" wire:model="monthly_addition" min="0" step="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('monthly_addition') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Optional: additional monthly investment</p>
                    </div>

                    <div>
                        <label class="flex items-center space-x-2 mt-8">
                            <input type="checkbox" wire:model="reinvest_dividends" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">Reinvest dividends</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Calculate Dividend Income
                </button>
            </form>

            @if($show_results)
                <div class="mt-8 space-y-6">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-xl border border-green-200">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Dividend Income Projection</h2>
                        
                        <div class="grid md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Current Annual Income</div>
                                <div class="text-2xl font-bold text-green-600">€{{ number_format($annual_dividend_income, 2) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Monthly Income</div>
                                <div class="text-xl font-bold text-blue-600">€{{ number_format($monthly_dividend_income, 2) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Final Portfolio Value</div>
                                <div class="text-xl font-bold text-purple-600">€{{ number_format($final_portfolio_value, 0) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Total Dividends</div>
                                <div class="text-xl font-bold text-orange-600">€{{ number_format($total_dividends_received, 0) }}</div>
                            </div>
                        </div>

                        @if(!empty($yearly_breakdown))
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Year-by-Year Projection</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-white rounded-lg overflow-hidden">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Year</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Portfolio Value</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Annual Dividend</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Yield</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cumulative Dividends</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach(array_slice($yearly_breakdown, 0, 15) as $year)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $year['year'] }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-gray-900">€{{ number_format($year['portfolio_value'], 2) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-green-600">€{{ number_format($year['annual_dividend'], 2) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-blue-600">{{ number_format($year['yield'], 2) }}%</td>
                                                    <td class="px-4 py-3 text-sm text-right text-purple-600">€{{ number_format($year['cumulative_dividends'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if(count($yearly_breakdown) > 15)
                                        <p class="text-xs text-gray-500 mt-2">Showing first 15 years of {{ count($yearly_breakdown) }} years</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <h3 class="font-semibold text-blue-900 mb-2">💡 Dividend Investing Tips</h3>
                        <ul class="list-disc list-inside space-y-1 text-sm text-blue-800">
                            <li>Look for companies with consistent dividend growth history</li>
                            <li>Reinvesting dividends accelerates portfolio growth through compounding</li>
                            <li>Dividend aristocrats are companies that have increased dividends for 25+ years</li>
                            <li>Higher yield doesn't always mean better - consider sustainability</li>
                            <li>Diversify across sectors and regions to reduce risk</li>
                            <li>Dividend income can provide passive income stream in retirement</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
    </div>
</div>
