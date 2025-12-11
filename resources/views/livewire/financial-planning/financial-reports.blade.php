<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📊 Financial Reports</h1>
                    <p class="text-gray-600 mt-1">Generate comprehensive financial reports and analytics</p>
                </div>
                <a href="{{ route('financial.dashboard') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Back to Dashboard
                </a>
            </div>

            {{-- Report Controls --}}
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <div class="grid md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                        <select wire:model="reportType" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="monthly">Monthly</option>
                            <option value="annual">Annual</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" wire:model="startDate" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" wire:model="endDate" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-end gap-2">
                        <button wire:click="exportPDF" 
                                class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            📄 PDF
                        </button>
                        <button wire:click="exportCSV" 
                                class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            📊 CSV
                        </button>
                    </div>
                </div>
            </div>

            @if(!empty($reportData))
            {{-- Summary Cards --}}
            <div class="grid md:grid-cols-4 gap-6 mb-6">
                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-xl shadow-lg">
                    <div class="text-sm opacity-90 mb-1">Total Income</div>
                    <div class="text-3xl font-bold">€{{ number_format($reportData['summary']['total_income'], 0) }}</div>
                </div>
                <div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-6 rounded-xl shadow-lg">
                    <div class="text-sm opacity-90 mb-1">Total Expenses</div>
                    <div class="text-3xl font-bold">€{{ number_format($reportData['summary']['total_expenses'], 0) }}</div>
                </div>
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-lg">
                    <div class="text-sm opacity-90 mb-1">Net Cash Flow</div>
                    <div class="text-3xl font-bold {{ $reportData['summary']['net_cash_flow'] >= 0 ? '' : 'text-red-200' }}">
                        €{{ number_format($reportData['summary']['net_cash_flow'], 0) }}
                    </div>
                </div>
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-xl shadow-lg">
                    <div class="text-sm opacity-90 mb-1">Savings Rate</div>
                    <div class="text-3xl font-bold">{{ number_format($reportData['summary']['savings_rate'], 1) }}%</div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                {{-- Income by Category --}}
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">💵 Income by Category</h2>
                    <div class="space-y-3">
                        @forelse($reportData['income_by_category'] as $item)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">{{ ucfirst(str_replace('_', ' ', $item->category)) }}</span>
                            <div class="text-right">
                                <div class="font-bold text-green-600">€{{ number_format($item->total, 2) }}</div>
                                <div class="text-xs text-gray-500">{{ $item->count }} transactions</div>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">No income transactions in this period</p>
                        @endforelse
                    </div>
                </div>

                {{-- Expenses by Category --}}
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">💸 Expenses by Category</h2>
                    <div class="space-y-3">
                        @forelse($reportData['expenses_by_category'] as $item)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">{{ ucfirst(str_replace('_', ' ', $item->category)) }}</span>
                            <div class="text-right">
                                <div class="font-bold text-red-600">€{{ number_format($item->total, 2) }}</div>
                                <div class="text-xs text-gray-500">{{ $item->count }} transactions</div>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">No expense transactions in this period</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Net Worth Summary --}}
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">💰 Net Worth Summary</h2>
                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <div class="text-sm text-gray-600 mb-1">Total Assets</div>
                        <div class="text-2xl font-bold text-green-600">€{{ number_format($reportData['summary']['total_assets'], 2) }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600 mb-1">Total Debts</div>
                        <div class="text-2xl font-bold text-red-600">€{{ number_format($reportData['summary']['total_debts'], 2) }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600 mb-1">Net Worth</div>
                        <div class="text-2xl font-bold text-blue-600">€{{ number_format($reportData['summary']['net_worth'], 2) }}</div>
                    </div>
                </div>
            </div>

            {{-- Top Transactions --}}
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">📈 Top Income Transactions</h2>
                    <div class="space-y-3">
                        @forelse($reportData['top_income'] as $transaction)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <div>
                                <div class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $transaction->category)) }}</div>
                                <div class="text-sm text-gray-500">{{ $transaction->transaction_date->format('M d, Y') }}</div>
                            </div>
                            <div class="font-bold text-green-600">€{{ number_format($transaction->amount, 2) }}</div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">No income transactions</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">📉 Top Expense Transactions</h2>
                    <div class="space-y-3">
                        @forelse($reportData['top_expenses'] as $transaction)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <div>
                                <div class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $transaction->category)) }}</div>
                                <div class="text-sm text-gray-500">{{ $transaction->transaction_date->format('M d, Y') }}</div>
                            </div>
                            <div class="font-bold text-red-600">€{{ number_format($transaction->amount, 2) }}</div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">No expense transactions</p>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

