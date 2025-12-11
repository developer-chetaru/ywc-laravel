<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">💰 Financial Dashboard</h1>
                <p class="text-gray-600 mt-1">Track your net worth, goals, and financial progress</p>
            </div>
                <div class="flex gap-3">
                <a href="{{ route('financial.calculators.index') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Use Calculators
                </a>
                <a href="{{ route('financial.reports.index') }}" 
                   class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    📊 Reports
                </a>
                <a href="{{ route('financial.retirement-planner') }}" 
                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    🏖️ Retirement
                </a>
                @role('super_admin')
                    <a href="{{ route('financial.admin.index') }}" 
                       class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        ⚙️ Admin
                    </a>
                    <a href="{{ route('main-dashboard') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        Main Dashboard
                    </a>
                @endrole
            </div>
        </div>

        @if(session('message'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                {{ session('message') }}
            </div>
        @endif

        {{-- Net Worth Summary --}}
        <div class="grid md:grid-cols-4 gap-6 mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-lg">
                <div class="text-sm opacity-90 mb-1">Net Worth</div>
                <div class="text-3xl font-bold">€{{ number_format($net_worth, 0) }}</div>
            </div>
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-xl shadow-lg">
                <div class="text-sm opacity-90 mb-1">Total Assets</div>
                <div class="text-3xl font-bold">€{{ number_format($total_assets, 0) }}</div>
            </div>
            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-6 rounded-xl shadow-lg">
                <div class="text-sm opacity-90 mb-1">Total Debts</div>
                <div class="text-3xl font-bold">€{{ number_format($total_debts, 0) }}</div>
            </div>
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-xl shadow-lg">
                <div class="text-sm opacity-90 mb-1">Monthly Cash Flow</div>
                <div class="text-3xl font-bold {{ $monthly_cash_flow >= 0 ? '' : 'text-red-200' }}">
                    €{{ number_format($monthly_cash_flow, 0) }}
                </div>
            </div>
        </div>

        {{-- Charts Section --}}
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            {{-- Net Worth Trend --}}
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-4">📈 Net Worth Trend</h2>
                <canvas id="netWorthChart" height="100"></canvas>
            </div>

            {{-- Income vs Expenses Trend --}}
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-4">💰 Income vs Expenses</h2>
                <canvas id="incomeExpenseChart" height="100"></canvas>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-6">
            {{-- Expense Breakdown --}}
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-4">💸 Expense Breakdown</h2>
                <canvas id="expenseChart" height="200"></canvas>
            </div>

            {{-- Income Breakdown --}}
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-4">💵 Income Breakdown</h2>
                <canvas id="incomeChart" height="200"></canvas>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-6">
            {{-- Monthly Income vs Expenses --}}
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-4">📊 Monthly Summary</h2>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-700">Income</span>
                            <span class="font-bold text-green-600">€{{ number_format($total_income, 2) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-500 h-3 rounded-full" 
                                 style="width: {{ $total_income > 0 ? min(100, ($total_income / max($total_income + $total_expenses, 1)) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-700">Expenses</span>
                            <span class="font-bold text-red-600">€{{ number_format($total_expenses, 2) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-red-500 h-3 rounded-full" 
                                 style="width: {{ $total_expenses > 0 ? min(100, ($total_expenses / max($total_income + $total_expenses, 1)) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="pt-4 border-t">
                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-900">Cash Flow</span>
                            <span class="font-bold text-lg {{ $monthly_cash_flow >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                €{{ number_format($monthly_cash_flow, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-4">⚡ Quick Actions</h2>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('financial.calculators.index') }}" 
                       class="bg-blue-50 hover:bg-blue-100 p-4 rounded-lg border border-blue-200 text-center transition-colors">
                        <div class="text-2xl mb-2">🧮</div>
                        <div class="font-medium text-blue-900">Calculators</div>
                    </a>
                    <a href="{{ route('financial.accounts.index') }}" 
                       class="bg-green-50 hover:bg-green-100 p-4 rounded-lg border border-green-200 text-center transition-colors">
                        <div class="text-2xl mb-2">💳</div>
                        <div class="font-medium text-green-900">Manage Accounts</div>
                    </a>
                    <a href="{{ route('financial.goals.index') }}" 
                       class="bg-purple-50 hover:bg-purple-100 p-4 rounded-lg border border-purple-200 text-center transition-colors">
                        <div class="text-2xl mb-2">🎯</div>
                        <div class="font-medium text-purple-900">Manage Goals</div>
                    </a>
                    <a href="{{ route('financial.transactions.index') }}" 
                       class="bg-orange-50 hover:bg-orange-100 p-4 rounded-lg border border-orange-200 text-center transition-colors">
                        <div class="text-2xl mb-2">📝</div>
                        <div class="font-medium text-orange-900">Add Transaction</div>
                    </a>
                    <a href="{{ route('financial.budget.index') }}" 
                       class="bg-pink-50 hover:bg-pink-100 p-4 rounded-lg border border-pink-200 text-center transition-colors">
                        <div class="text-2xl mb-2">📊</div>
                        <div class="font-medium text-pink-900">Budget</div>
                    </a>
                    <a href="{{ route('financial.reports.index') }}" 
                       class="bg-indigo-50 hover:bg-indigo-100 p-4 rounded-lg border border-indigo-200 text-center transition-colors">
                        <div class="text-2xl mb-2">📈</div>
                        <div class="font-medium text-indigo-900">Reports</div>
                    </a>
                    <a href="{{ route('financial.retirement-planner') }}" 
                       class="bg-teal-50 hover:bg-teal-100 p-4 rounded-lg border border-teal-200 text-center transition-colors">
                        <div class="text-2xl mb-2">🏖️</div>
                        <div class="font-medium text-teal-900">Retirement</div>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            {{-- Financial Goals --}}
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900">🎯 Financial Goals</h2>
                    <a href="{{ route('financial.goals.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
                </div>
                @if($goals->count() > 0)
                    <div class="space-y-4">
                        @foreach($goals as $goal)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $goal->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $goal->type)) }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-{{ $goal->priority === 'high' ? 'red' : ($goal->priority === 'medium' ? 'yellow' : 'green') }}-100 text-{{ $goal->priority === 'high' ? 'red' : ($goal->priority === 'medium' ? 'yellow' : 'green') }}-800">
                                        {{ ucfirst($goal->priority) }}
                                    </span>
                                </div>
                                <div class="mb-2">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Progress</span>
                                        <span class="font-medium">{{ number_format($goal_progress[$goal->id]['progress'] ?? 0, 1) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" 
                                             style="width: {{ min(100, $goal_progress[$goal->id]['progress'] ?? 0) }}%"></div>
                                    </div>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">
                                        €{{ number_format($goal->current_amount, 0) }} / €{{ number_format($goal->target_amount, 0) }}
                                    </span>
                                    <span class="text-gray-500">
                                        {{ $goal->target_date->format('M Y') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <p>No financial goals set yet.</p>
                        <a href="{{ route('financial.goals.index') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">Create your first goal</a>
                    </div>
                @endif
            </div>

            {{-- Recent Transactions --}}
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900">💸 Recent Transactions</h2>
                    <a href="{{ route('financial.goals.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
                </div>
                @if($recent_transactions->count() > 0)
                    <div class="space-y-3">
                        @foreach($recent_transactions as $transaction)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $transaction->category }}</div>
                                    <div class="text-sm text-gray-500">{{ $transaction->transaction_date->format('M d, Y') }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->type === 'income' ? '+' : '-' }}€{{ number_format($transaction->amount, 2) }}
                                    </div>
                                    @if($transaction->account)
                                        <div class="text-xs text-gray-500">{{ $transaction->account->name }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <p>No transactions yet.</p>
                        <a href="{{ route('financial.transactions.index') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">Add your first transaction</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Recent Calculations --}}
        @if($recent_calculations->count() > 0)
            <div class="mt-6 bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900">📊 Recent Saved Calculations</h2>
                    <a href="{{ route('financial.goals.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
                </div>
                <div class="grid md:grid-cols-5 gap-4">
                    @foreach($recent_calculations as $calculation)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="text-sm font-medium text-gray-900 mb-1">
                                {{ ucfirst(str_replace('_', ' ', $calculation->calculator_type)) }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $calculation->created_at->format('M d, Y') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Net Worth Chart
    const netWorthCtx = document.getElementById('netWorthChart');
    if (netWorthCtx) {
        new Chart(netWorthCtx, {
            type: 'line',
            data: {
                labels: @json($net_worth_history['labels'] ?? []),
                datasets: [{
                    label: 'Net Worth',
                    data: @json($net_worth_history['data'] ?? []),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) {
                                return '€' + (value / 1000).toFixed(0) + 'k';
                            }
                        }
                    }
                }
            }
        });
    }

    // Income vs Expenses Chart
    const incomeExpenseCtx = document.getElementById('incomeExpenseChart');
    if (incomeExpenseCtx) {
        new Chart(incomeExpenseCtx, {
            type: 'bar',
            data: {
                labels: @json($income_expense_data['labels'] ?? []),
                datasets: [
                    {
                        label: 'Income',
                        data: @json($income_expense_data['income'] ?? []),
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    },
                    {
                        label: 'Expenses',
                        data: @json($income_expense_data['expense'] ?? []),
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '€' + (value / 1000).toFixed(0) + 'k';
                            }
                        }
                    }
                }
            }
        });
    }

    // Expense Pie Chart
    const expenseCtx = document.getElementById('expenseChart');
    if (expenseCtx) {
        const expenseData = @json($expense_by_category);
        new Chart(expenseCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(expenseData).map(cat => cat.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())),
                datasets: [{
                    data: Object.values(expenseData),
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(234, 179, 8, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(147, 51, 234, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
            }
        });
    }

    // Income Pie Chart
    const incomeCtx = document.getElementById('incomeChart');
    if (incomeCtx) {
        const incomeData = @json($income_by_category);
        new Chart(incomeCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(incomeData).map(cat => cat.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())),
                datasets: [{
                    data: Object.values(incomeData),
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(147, 51, 234, 0.8)',
                        'rgba(234, 179, 8, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
            }
        });
    }
});
</script>

