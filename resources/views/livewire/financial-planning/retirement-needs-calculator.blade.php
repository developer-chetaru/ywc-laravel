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
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Retirement Needs Calculator</h1>
            <p class="text-gray-600 mb-6">Calculate how much you'll need for retirement and if you're on track</p>

            <form wire:submit.prevent="calculate" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    {{-- Current Age --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Age *</label>
                        <input type="number" wire:model="current_age" min="18" max="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('current_age') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Retirement Age --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Target Retirement Age *</label>
                        <input type="number" wire:model="retirement_age" min="{{ $current_age + 1 }}" max="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('retirement_age') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Life Expectancy --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Life Expectancy *</label>
                        <input type="number" wire:model="life_expectancy" min="{{ $retirement_age + 1 }}" max="120" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('life_expectancy') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Desired Annual Income --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Desired Annual Income (€) *</label>
                        <input type="number" wire:model="desired_annual_income" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('desired_annual_income') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Current Savings --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Retirement Savings (€) *</label>
                        <input type="number" wire:model="current_savings" min="0" step="1000" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('current_savings') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Monthly Contribution --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Contribution (€) *</label>
                        <input type="number" wire:model="monthly_contribution" min="0" step="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('monthly_contribution') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Expected Return --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expected Annual Return (%) *</label>
                        <input type="number" wire:model="expected_return" min="0" max="20" step="0.1" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('expected_return') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Typical range: 5-8% for balanced portfolio</p>
                    </div>

                    {{-- Inflation Rate --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expected Inflation Rate (%) *</label>
                        <input type="number" wire:model="inflation_rate" min="0" max="10" step="0.1" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('inflation_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Typical range: 2-3%</p>
                    </div>
                </div>

                <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                    Calculate Retirement Needs
                </button>
            </form>

            {{-- Results Section --}}
            @if($show_results)
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Your Retirement Analysis</h2>
                    
                    <div class="space-y-4">
                        {{-- Status Indicator --}}
                        <div class="p-4 rounded-lg {{ $is_on_track ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' }}">
                            <div class="flex items-center gap-3">
                                @if($is_on_track)
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h3 class="font-semibold text-green-800">You're on track!</h3>
                                        <p class="text-sm text-green-700">Your current plan should meet your retirement needs.</p>
                                    </div>
                                @else
                                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <div>
                                        <h3 class="font-semibold text-yellow-800">Adjustment needed</h3>
                                        <p class="text-sm text-yellow-700">You need to increase your savings to meet your retirement goals.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Key Metrics --}}
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <p class="text-sm text-gray-600 mb-1">Total Amount Needed at Retirement</p>
                                <p class="text-2xl font-bold text-blue-900">€{{ number_format($total_needed, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500 mt-1">Based on {{ $life_expectancy - $retirement_age }} years in retirement</p>
                            </div>

                            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                                <p class="text-sm text-gray-600 mb-1">Projected Amount at Retirement</p>
                                <p class="text-2xl font-bold text-purple-900">€{{ number_format($projected_amount, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500 mt-1">Based on current savings and contributions</p>
                            </div>

                            @if(!$is_on_track)
                                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                    <p class="text-sm text-gray-600 mb-1">Retirement Gap</p>
                                    <p class="text-2xl font-bold text-red-900">€{{ number_format(abs($gap), 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Amount short of your goal</p>
                                </div>

                                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                    <p class="text-sm text-gray-600 mb-1">Adjusted Monthly Contribution Needed</p>
                                    <p class="text-2xl font-bold text-green-900">€{{ number_format($adjusted_monthly_contribution, 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-500 mt-1">To close the gap and meet your goal</p>
                                </div>
                            @else
                                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                    <p class="text-sm text-gray-600 mb-1">Surplus Amount</p>
                                    <p class="text-2xl font-bold text-green-900">€{{ number_format(abs($gap), 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-500 mt-1">You'll have more than needed</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Lead Capture (if not logged in) --}}
                    @guest
                        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-900 mb-3">
                                <strong>Save your results!</strong> Create a free account to save this calculation and track your retirement progress over time.
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
