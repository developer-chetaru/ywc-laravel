<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">💰 Pension & Investment Advice</h1>
                    <p class="text-gray-600 mt-1">Expert guidance for your pension and investment planning</p>
                </div>
                <a href="{{ route('financial.dashboard') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Back to Dashboard
                </a>
            </div>

            {{-- Tabs --}}
            <div class="border-b border-gray-200 mb-6">
                <nav class="flex space-x-8">
                    <button wire:click="$set('activeTab', 'overview')" 
                            class="px-4 py-2 border-b-2 {{ $activeTab === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}">
                        Overview
                    </button>
                    <button wire:click="$set('activeTab', 'pension')" 
                            class="px-4 py-2 border-b-2 {{ $activeTab === 'pension' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}">
                        Pension Analysis
                    </button>
                    <button wire:click="$set('activeTab', 'investment')" 
                            class="px-4 py-2 border-b-2 {{ $activeTab === 'investment' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}">
                        Investment Portfolio
                    </button>
                    <button wire:click="$set('activeTab', 'advisors')" 
                            class="px-4 py-2 border-b-2 {{ $activeTab === 'advisors' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}">
                        Find an Advisor
                    </button>
                </nav>
            </div>

            {{-- Overview Tab --}}
            @if($activeTab === 'overview')
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-lg">
                    <div class="text-sm opacity-90 mb-1">Total Pension Value</div>
                    <div class="text-3xl font-bold">€{{ number_format($totalPensionValue, 0) }}</div>
                    <div class="text-sm mt-2 opacity-90">{{ $pensionAccounts->count() }} pension account(s)</div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-xl shadow-lg">
                    <div class="text-sm opacity-90 mb-1">Total Investment Value</div>
                    <div class="text-3xl font-bold">€{{ number_format($totalInvestmentValue, 0) }}</div>
                    <div class="text-sm mt-2 opacity-90">{{ $investmentAccounts->count() }} investment account(s)</div>
                </div>
            </div>

            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">📊 Quick Insights</h2>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-700">Combined Value</span>
                            <span class="font-bold text-blue-600">€{{ number_format($totalPensionValue + $totalInvestmentValue, 0) }}</span>
                        </div>
                    </div>
                    @if(count($projections) > 0)
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-700">Projected at Retirement ({{ $projectedRetirementAge }} years)</span>
                            <span class="font-bold text-green-600">€{{ number_format($totalProjected, 0) }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($retirementGoals->count() > 0)
            <div class="mt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">🎯 Retirement Goals</h2>
                <div class="space-y-3">
                    @foreach($retirementGoals as $goal)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $goal->name }}</h3>
                                <p class="text-sm text-gray-600">Target: €{{ number_format($goal->target_amount, 0) }} by {{ $goal->target_date->format('M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-blue-600">{{ number_format($goal->progress_percentage, 1) }}%</div>
                                <div class="text-sm text-gray-500">Progress</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @endif

            {{-- Pension Analysis Tab --}}
            @if($activeTab === 'pension')
            <div class="space-y-6">
                @if($pensionAccounts->count() > 0)
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Pension Accounts</h2>
                    <div class="space-y-4">
                        @foreach($pensionAccounts as $account)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $account->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $account->subtype ?? 'Standard Pension' }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-xl font-bold text-blue-600">€{{ number_format($account->current_balance, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                @if(count($projections) > 0)
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Projected Growth</h2>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">Account</th>
                                    <th class="text-right py-2">Current Value</th>
                                    <th class="text-right py-2">Projected Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($projections as $projection)
                                <tr class="border-b">
                                    <td class="py-2">{{ $projection['account'] }}</td>
                                    <td class="text-right py-2">€{{ number_format($projection['current'], 2) }}</td>
                                    <td class="text-right py-2 font-semibold text-green-600">€{{ number_format($projection['projected'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-bold">
                                    <td class="py-2">Total</td>
                                    <td class="text-right py-2">€{{ number_format($totalPensionValue, 2) }}</td>
                                    <td class="text-right py-2 text-green-600">€{{ number_format($totalProjected, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @endif
                @else
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <p class="text-gray-500 mb-4">No pension accounts found.</p>
                    <a href="{{ route('financial.accounts.index') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Add Pension Account
                    </a>
                </div>
                @endif
            </div>
            @endif

            {{-- Investment Portfolio Tab --}}
            @if($activeTab === 'investment')
            <div class="space-y-6">
                @if($investmentAccounts->count() > 0)
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Investment Accounts</h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach($investmentAccounts as $account)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-2">{{ $account->name }}</h3>
                            <p class="text-sm text-gray-600 mb-2">{{ $account->subtype ?? 'Investment Account' }}</p>
                            <div class="text-2xl font-bold text-green-600">€{{ number_format($account->current_balance, 2) }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Portfolio Summary</h2>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <div class="text-sm text-gray-600">Total Value</div>
                            <div class="text-2xl font-bold text-blue-600">€{{ number_format($totalInvestmentValue, 2) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">Number of Accounts</div>
                            <div class="text-2xl font-bold text-gray-900">{{ $investmentAccounts->count() }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">Average per Account</div>
                            <div class="text-2xl font-bold text-gray-900">
                                €{{ number_format($investmentAccounts->count() > 0 ? $totalInvestmentValue / $investmentAccounts->count() : 0, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <p class="text-gray-500 mb-4">No investment accounts found.</p>
                    <a href="{{ route('financial.accounts.index') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Add Investment Account
                    </a>
                </div>
                @endif
            </div>
            @endif

            {{-- Find an Advisor Tab --}}
            @if($activeTab === 'advisors')
            <div class="space-y-6">
                @if($advisors->count() > 0)
                <div class="grid md:grid-cols-2 gap-6">
                    @foreach($advisors as $advisor)
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center text-2xl font-bold text-blue-600">
                                {{ substr($advisor->name, 0, 1) }}
                            </div>
                            <div class="ml-4">
                                <h3 class="font-bold text-gray-900">{{ $advisor->name }}</h3>
                                <div class="flex items-center text-sm text-gray-600">
                                    <span>⭐ {{ number_format($advisor->rating, 1) }}</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ $advisor->total_consultations }} consultations</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">{{ Str::limit($advisor->bio, 120) }}</p>
                        @if($advisor->specializations)
                        <div class="mb-4">
                            @foreach(array_slice($advisor->specializations, 0, 3) as $spec)
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1">
                                {{ $spec }}
                            </span>
                            @endforeach
                        </div>
                        @endif
                        <a href="{{ route('financial.advisory.index') }}" 
                           class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Book Consultation
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <p class="text-gray-500 mb-4">No pension/investment advisors available at this time.</p>
                    <a href="{{ route('financial.advisory.index') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        View All Advisors
                    </a>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

