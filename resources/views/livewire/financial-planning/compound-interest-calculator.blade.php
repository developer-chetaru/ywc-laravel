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
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Compound Interest Calculator</h1>
            <p class="text-gray-600 mb-6">See how your money grows with the power of compound interest</p>

            <form wire:submit.prevent="calculate" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    {{-- Starting Amount --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Starting Amount (€) *</label>
                        <input type="number" wire:model="starting_amount" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('starting_amount') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Monthly Addition --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Addition (€) *</label>
                        <input type="number" wire:model="monthly_addition" min="0" step="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('monthly_addition') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Years --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Number of Years *</label>
                        <input type="number" wire:model="years" min="1" max="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('years') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Interest Rate --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Annual Interest Rate (%) *</label>
                        <input type="number" wire:model="interest_rate" min="0" max="30" step="0.1" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('interest_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Enter the expected annual return rate</p>
                    </div>
                </div>

                <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                    Calculate Compound Interest
                </button>
            </form>

            {{-- Results Section --}}
            @if($show_results)
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Your Results</h2>
                    
                    <div class="grid md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <p class="text-sm text-gray-600 mb-1">Future Value</p>
                            <p class="text-2xl font-bold text-blue-900">€{{ number_format($future_value, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500 mt-1">After {{ $years }} years</p>
                        </div>

                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <p class="text-sm text-gray-600 mb-1">Total Contributions</p>
                            <p class="text-2xl font-bold text-purple-900">€{{ number_format($total_contributions, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500 mt-1">What you invested</p>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <p class="text-sm text-gray-600 mb-1">Interest Earned</p>
                            <p class="text-2xl font-bold text-green-900">€{{ number_format($interest_earned, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500 mt-1">Money made from interest</p>
                        </div>
                    </div>

                    {{-- Year-by-Year Breakdown --}}
                    @if(count($yearly_breakdown) > 0)
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Year-by-Year Growth</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Year</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Value</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Contributions</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Interest Earned</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($yearly_breakdown as $yearData)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $yearData['year'] }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900">€{{ number_format($yearData['value'], 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600">€{{ number_format($yearData['contributions'], 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-green-600 font-medium">€{{ number_format($yearData['interest'], 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- Lead Capture (if not logged in) --}}
                    @guest
                        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-900 mb-3">
                                <strong>Save your calculations!</strong> Create a free account to save this calculation and track your investment growth over time.
                            </p>
                            <a href="{{ route('register') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                Create Free Account
                            </a>
                            <span class="ml-2 text-sm text-blue-700">or <a href="{{ route('login') }}" class="underline">login</a> if you already have an account</span>
                        </div>
                    @endguest
                </div>
            @endif
        </div>
    </div>
</div>
