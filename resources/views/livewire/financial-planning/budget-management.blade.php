<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📊 Budget Management</h1>
                    <p class="text-gray-600 mt-1">Track your spending against your budget</p>
                </div>
                <a href="{{ route('financial.dashboard') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Back to Dashboard
                </a>
            </div>

            @if($currentBudget)
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-xl shadow-lg">
                    <div class="text-sm opacity-90 mb-1">Budgeted Income</div>
                    <div class="text-3xl font-bold">€{{ number_format($currentBudget->total_income, 0) }}</div>
                    <div class="text-sm mt-2 opacity-90">Actual: €{{ number_format($actualIncome, 0) }}</div>
                </div>
                <div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-6 rounded-xl shadow-lg">
                    <div class="text-sm opacity-90 mb-1">Budgeted Expenses</div>
                    <div class="text-3xl font-bold">€{{ number_format($currentBudget->total_expenses, 0) }}</div>
                    <div class="text-sm mt-2 opacity-90">Actual: €{{ number_format($actualExpenses, 0) }}</div>
                </div>
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-lg">
                    <div class="text-sm opacity-90 mb-1">Savings Target</div>
                    <div class="text-3xl font-bold">€{{ number_format($currentBudget->savings_target, 0) }}</div>
                    <div class="text-sm mt-2 opacity-90">Actual: €{{ number_format($actualIncome - $actualExpenses, 0) }}</div>
                </div>
            </div>

            @if($currentBudget->category_budgets)
            <div class="space-y-4">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Category Budgets</h2>
                @foreach($currentBudget->category_budgets as $category => $budgeted)
                    @php
                        $actual = $categoryActuals[$category] ?? 0;
                        $percentage = $budgeted > 0 ? min(100, ($actual / $budgeted) * 100) : 0;
                        $isOver = $actual > $budgeted;
                    @endphp
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $category)) }}</span>
                            <span class="text-sm {{ $isOver ? 'text-red-600' : 'text-gray-600' }}">
                                €{{ number_format($actual, 2) }} / €{{ number_format($budgeted, 2) }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full transition-all {{ $isOver ? 'bg-red-500' : 'bg-blue-500' }}" 
                                 style="width: {{ min(100, $percentage) }}%"></div>
                        </div>
                        @if($isOver)
                        <div class="text-xs text-red-600 mt-1">Over budget by €{{ number_format($actual - $budgeted, 2) }}</div>
                        @else
                        <div class="text-xs text-gray-500 mt-1">{{ number_format(100 - $percentage, 1) }}% remaining</div>
                        @endif
                    </div>
                @endforeach
            </div>
            @endif
            @else
            <div class="text-center py-12">
                <p class="text-gray-500">No budget available. Start tracking transactions to generate a budget automatically.</p>
            </div>
            @endif
        </div>
    </div>
</div>

