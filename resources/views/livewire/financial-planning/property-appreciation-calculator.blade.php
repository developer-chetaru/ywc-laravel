<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('financial.calculators.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Back to Calculators</span>
        </a>

        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">📈 Property Appreciation Calculator</h1>
            <p class="text-gray-600 mb-6">Calculate future property value based on appreciation rates</p>

            <form wire:submit.prevent="calculate" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Price (€) *</label>
                        <input type="number" wire:model="purchase_price" min="0" step="10000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('purchase_price') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Value (€) *</label>
                        <input type="number" wire:model="current_value" min="0" step="10000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Leave same as purchase if new</p>
                        @error('current_value') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Appreciation Rate (%) *</label>
                        <input type="number" wire:model="appreciation_rate" min="0" max="20" step="0.5" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Annual % (typical: 2-5%)</p>
                        @error('appreciation_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Years to Project *</label>
                        <input type="number" wire:model="years" min="1" max="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('years') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Down Payment (€)</label>
                        <input type="number" wire:model="down_payment" min="0" step="5000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">For ROI calculation</p>
                        @error('down_payment') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Annual Rental Income (€)</label>
                        <input type="number" wire:model="annual_rental_income" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('annual_rental_income') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Annual Expenses (€)</label>
                        <input type="number" wire:model="annual_expenses" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Maintenance, taxes, insurance, etc.</p>
                        @error('annual_expenses') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Calculate Appreciation
                </button>
            </form>

            @if($show_results)
                <div class="mt-8 space-y-6">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-xl border border-green-200">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Appreciation Results</h2>
                        
                        <div class="grid md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Future Value</div>
                                <div class="text-2xl font-bold text-green-600">€{{ number_format($future_value, 0) }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Total Appreciation</div>
                                <div class="text-2xl font-bold text-blue-600">{{ number_format($total_appreciation, 1) }}%</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Appreciation Amount</div>
                                <div class="text-xl font-bold text-purple-600">€{{ number_format($appreciation_amount, 0) }}</div>
                            </div>
                            @if($down_payment > 0)
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">ROI on Equity</div>
                                <div class="text-xl font-bold text-orange-600">{{ number_format($roi_on_equity, 1) }}%</div>
                            </div>
                            @endif
                        </div>

                        @if(!empty($yearly_breakdown))
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Year-by-Year Projection</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-white rounded-lg overflow-hidden">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Year</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Property Value</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Appreciation</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Net Rental</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Return</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach(array_slice($yearly_breakdown, 0, 15) as $year)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $year['year'] }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-gray-900">€{{ number_format($year['property_value'], 0) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-green-600">€{{ number_format($year['appreciation'], 0) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-blue-600">€{{ number_format($year['net_rental'], 0) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-purple-600">€{{ number_format($year['total_return'], 0) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if(count($yearly_breakdown) > 15)
                                        <p class="text-xs text-gray-500 mt-2">Showing first 15 years</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <h3 class="font-semibold text-blue-900 mb-2">💡 Property Appreciation Tips</h3>
                        <ul class="list-disc list-inside space-y-1 text-sm text-blue-800">
                            <li>Historical average appreciation is 3-5% annually in most markets</li>
                            <li>Location, location, location - prime areas appreciate faster</li>
                            <li>Property improvements can increase appreciation rate</li>
                            <li>Market cycles affect appreciation - buy during downturns for better returns</li>
                            <li>Consider tax implications when selling appreciated property</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
