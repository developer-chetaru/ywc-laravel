<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        {{-- Back Button --}}
        <a href="{{ route('financial.calculators.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to Calculators</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🔥 FIRE Calculator</h1>
            <p class="text-gray-600 mb-6">Calculate when you can achieve Financial Independence and Retire Early</p>

            <form wire:submit.prevent="calculate" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    {{-- Current Age --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Age *</label>
                        <input type="number" wire:model="current_age" min="18" max="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('current_age') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Current Savings --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Savings (€) *</label>
                        <input type="number" wire:model="current_savings" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('current_savings') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Annual Income --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Annual Income (€) *</label>
                        <input type="number" wire:model="annual_income" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('annual_income') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Annual Expenses --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Annual Expenses (€) *</label>
                        <input type="number" wire:model="annual_expenses" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('annual_expenses') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Based on 4% rule, you'll need 25x this amount</p>
                    </div>

                    {{-- Expected Return --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expected Annual Return (%) *</label>
                        <input type="number" wire:model="expected_return" min="0" max="20" step="0.1" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('expected_return') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Typical: 7% for diversified portfolio</p>
                    </div>

                    {{-- Savings Rate Display --}}
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Savings Rate</label>
                        <div class="text-2xl font-bold text-blue-600">
                            @if($savings_rate > 0)
                                {{ number_format($savings_rate, 1) }}%
                            @else
                                -
                            @endif
                        </div>
                        <p class="text-xs text-gray-600 mt-1">
                            @if($monthly_savings > 0)
                                Saving €{{ number_format($monthly_savings, 2) }}/month
                            @endif
                        </p>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Calculate FIRE Date
                </button>
            </form>

            @if($show_results)
                <div class="mt-8 space-y-6">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-xl border border-green-200">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">🎯 Your FIRE Results</h2>
                        
                        <div class="grid md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Years to FIRE</div>
                                <div class="text-3xl font-bold text-blue-600">{{ $years_to_fire }}</div>
                                <div class="text-xs text-gray-500 mt-1">years</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">FIRE Age</div>
                                <div class="text-3xl font-bold text-green-600">{{ $fire_age }}</div>
                                <div class="text-xs text-gray-500 mt-1">years old</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Target Amount</div>
                                <div class="text-2xl font-bold text-purple-600">€{{ number_format($total_needed, 0) }}</div>
                                <div class="text-xs text-gray-500 mt-1">25x annual expenses</div>
                            </div>
                        </div>

                        @if(!empty($projected_net_worth))
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Projected Net Worth Growth</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-white rounded-lg overflow-hidden">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Year</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Age</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Net Worth</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Saved</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Investment Growth</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach(array_slice($projected_net_worth, 0, 10) as $projection)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $projection['year'] }}</td>
                                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $projection['age'] }}</td>
                                                    <td class="px-4 py-3 text-sm font-medium text-right text-gray-900">€{{ number_format($projection['net_worth'], 2) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-blue-600">€{{ number_format($projection['savings'], 2) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-green-600">€{{ number_format($projection['growth'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if(count($projected_net_worth) > 10)
                                    <p class="text-xs text-gray-500 mt-2">Showing first 10 years. FIRE achieved in year {{ $years_to_fire }}.</p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <h3 class="font-semibold text-blue-900 mb-2">💡 Tips to Reach FIRE Faster</h3>
                        <ul class="list-disc list-inside space-y-1 text-sm text-blue-800">
                            <li>Increase your savings rate - every 5% increase can shave off years</li>
                            <li>Reduce expenses - small cuts compound over time</li>
                            <li>Increase income through side hustles or career advancement</li>
                            <li>Optimize investment returns - diversify and minimize fees</li>
                            <li>Consider geographic arbitrage - move to lower cost of living areas</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
