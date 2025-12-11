<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('financial.calculators.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to Calculators</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">📈 Investment Return Projector</h1>
            <p class="text-gray-600 mb-6">Project your investment returns over time with regular contributions</p>

            <form wire:submit.prevent="calculate" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Initial Investment (€) *</label>
                        <input type="number" wire:model="initial_investment" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('initial_investment') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Contribution (€) *</label>
                        <input type="number" wire:model="monthly_contribution" min="0" step="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('monthly_contribution') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Investment Period (Years) *</label>
                        <input type="number" wire:model="years" min="1" max="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('years') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expected Annual Return (%) *</label>
                        <input type="number" wire:model="expected_return" min="0" max="20" step="0.1" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('expected_return') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Typical: 7-10% for diversified portfolio</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Annual Contribution Increase (%)</label>
                        <input type="number" wire:model="contribution_increase" min="0" max="50" step="0.5" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('contribution_increase') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Account for salary increases or inflation adjustments</p>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Project Returns
                </button>
            </form>

            @if($show_results)
                <div class="mt-8 space-y-6">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-200">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Projection Results</h2>
                        
                        <div class="grid md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Future Value</div>
                                <div class="text-2xl font-bold text-blue-600">€{{ number_format($future_value, 2) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Total Contributions</div>
                                <div class="text-2xl font-bold text-gray-700">€{{ number_format($total_contributions, 2) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Interest Earned</div>
                                <div class="text-2xl font-bold text-green-600">€{{ number_format($interest_earned, 2) }}</div>
                            </div>
                        </div>

                        @if(!empty($yearly_breakdown))
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Year-by-Year Breakdown</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-white rounded-lg overflow-hidden">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Year</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Start Balance</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Contributions</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Growth</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">End Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($yearly_breakdown as $year)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $year['year'] }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-gray-600">€{{ number_format($year['start_balance'], 2) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-blue-600">€{{ number_format($year['contributions'], 2) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-green-600">€{{ number_format($year['growth'], 2) }}</td>
                                                    <td class="px-4 py-3 text-sm font-medium text-right text-gray-900">€{{ number_format($year['end_balance'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
