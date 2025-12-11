<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🏖️ Retirement Planner</h1>
                    <p class="text-gray-600 mt-1">Plan your retirement with comprehensive projections</p>
                </div>
                <a href="{{ route('financial.dashboard') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Back to Dashboard
                </a>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                {{-- Input Form --}}
                <div class="space-y-6">
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Your Retirement Profile</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Age</label>
                                <input type="number" wire:model.live="current_age" min="18" max="100"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Target Retirement Age</label>
                                <input type="number" wire:model.live="target_retirement_age" min="50" max="100"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Life Expectancy</label>
                                <input type="number" wire:model.live="life_expectancy" min="65" max="100"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Desired Annual Income (€)</label>
                                <input type="number" wire:model.live="desired_annual_income" min="0" step="1000"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Savings (€)</label>
                                <input type="number" wire:model.live="current_savings" min="0" step="1000"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Contribution (€)</label>
                                <input type="number" wire:model.live="monthly_contribution" min="0" step="100"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Expected Annual Return (%)</label>
                                <input type="number" wire:model.live="expected_return_rate" min="0" max="20" step="0.1"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Inflation Rate (%)</label>
                                <input type="number" wire:model.live="inflation_rate" min="0" max="10" step="0.1"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Results --}}
                <div class="space-y-6">
                    {{-- Readiness Score --}}
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-lg">
                        <div class="text-sm opacity-90 mb-2">Retirement Readiness Score</div>
                        <div class="text-5xl font-bold mb-4">{{ number_format($readiness_score, 0) }}/100</div>
                        <div class="w-full bg-white/20 rounded-full h-3">
                            <div class="bg-white h-3 rounded-full transition-all" 
                                 style="width: {{ min(100, $readiness_score) }}%"></div>
                        </div>
                        <div class="mt-4 text-sm">
                            @if($readiness_score >= 80)
                                <span class="font-semibold">✅ Excellent! You're on track for retirement.</span>
                            @elseif($readiness_score >= 60)
                                <span class="font-semibold">⚠️ Good progress, but consider increasing contributions.</span>
                            @elseif($readiness_score >= 40)
                                <span class="font-semibold">⚠️ Behind schedule. Action needed.</span>
                            @else
                                <span class="font-semibold">❌ Significantly behind. Urgent action required.</span>
                            @endif
                        </div>
                    </div>

                    {{-- Key Metrics --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded-lg border border-gray-200">
                            <div class="text-sm text-gray-600 mb-1">Total Needed</div>
                            <div class="text-2xl font-bold text-gray-900">€{{ number_format($total_needed, 0) }}</div>
                        </div>
                        <div class="bg-white p-4 rounded-lg border border-gray-200">
                            <div class="text-sm text-gray-600 mb-1">Projected Amount</div>
                            <div class="text-2xl font-bold {{ $projected_amount >= $total_needed ? 'text-green-600' : 'text-orange-600' }}">
                                €{{ number_format($projected_amount, 0) }}
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-lg border border-gray-200">
                            <div class="text-sm text-gray-600 mb-1">Gap/Surplus</div>
                            <div class="text-2xl font-bold {{ $gap <= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $gap <= 0 ? '+' : '' }}€{{ number_format(abs($gap), 0) }}
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-lg border border-gray-200">
                            <div class="text-sm text-gray-600 mb-1">Required Monthly</div>
                            <div class="text-2xl font-bold text-blue-600">
                                €{{ number_format($required_monthly_contribution, 0) }}
                            </div>
                        </div>
                    </div>

                    @if($gap > 0)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h3 class="font-semibold text-yellow-900 mb-2">⚠️ Action Required</h3>
                        <p class="text-sm text-yellow-800">
                            To meet your retirement goal, you need to contribute 
                            <strong>€{{ number_format($required_monthly_contribution, 0) }}</strong> per month 
                            (currently contributing €{{ number_format($monthly_contribution, 0) }}).
                        </p>
                    </div>
                    @else
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h3 class="font-semibold text-green-900 mb-2">✅ On Track!</h3>
                        <p class="text-sm text-green-800">
                            Your current plan will exceed your retirement goal by 
                            <strong>€{{ number_format(abs($gap), 0) }}</strong>.
                        </p>
                    </div>
                    @endif

                    {{-- Year-by-Year Projection --}}
                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                        <h3 class="font-bold text-gray-900 mb-4">Year-by-Year Projection</h3>
                        <div class="max-h-64 overflow-y-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Age</th>
                                        <th class="px-3 py-2 text-right">Balance</th>
                                        <th class="px-3 py-2 text-right">Needed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(array_slice($year_by_year, -10) as $year)
                                    <tr class="border-b">
                                        <td class="px-3 py-2">{{ $year['age'] }}</td>
                                        <td class="px-3 py-2 text-right">€{{ number_format($year['balance'], 0) }}</td>
                                        <td class="px-3 py-2 text-right">
                                            @if($year['needed'] > 0)
                                                €{{ number_format($year['needed'], 0) }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

