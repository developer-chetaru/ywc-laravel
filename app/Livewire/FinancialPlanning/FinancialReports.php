<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialAccount;
use App\Models\FinancialGoal;
use App\Models\FinancialTransaction;
use App\Models\FinancialBudget;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

#[Layout('layouts.app')]
class FinancialReports extends Component
{
    public $reportType = 'monthly';
    public $startDate = '';
    public $endDate = '';
    public $reportData = [];

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        $this->generateReport();
    }

    public function updatedReportType()
    {
        if ($this->reportType === 'monthly') {
            $this->startDate = now()->startOfMonth()->format('Y-m-d');
            $this->endDate = now()->endOfMonth()->format('Y-m-d');
        } elseif ($this->reportType === 'annual') {
            $this->startDate = now()->startOfYear()->format('Y-m-d');
            $this->endDate = now()->endOfYear()->format('Y-m-d');
        }
        $this->generateReport();
    }

    public function updatedStartDate()
    {
        $this->generateReport();
    }

    public function updatedEndDate()
    {
        $this->generateReport();
    }

    public function generateReport()
    {
        $user = Auth::user();
        
        // Income
        $totalIncome = FinancialTransaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$this->startDate, $this->endDate])
            ->sum('amount');

        // Expenses
        $totalExpenses = FinancialTransaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$this->startDate, $this->endDate])
            ->sum('amount');

        // Income by category
        $incomeByCategory = FinancialTransaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$this->startDate, $this->endDate])
            ->selectRaw('category, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        // Expenses by category
        $expensesByCategory = FinancialTransaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$this->startDate, $this->endDate])
            ->selectRaw('category, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        // Accounts summary
        $accounts = FinancialAccount::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        $totalAssets = $accounts->whereIn('type', ['savings', 'checking', 'investment', 'pension', 'property', 'other'])
            ->sum('current_balance');
        $totalDebts = abs($accounts->where('type', 'debt')->sum('current_balance'));
        $netWorth = $totalAssets - $totalDebts;

        // Goals progress
        $goals = FinancialGoal::where('user_id', $user->id)->get();

        // Top transactions
        $topIncome = FinancialTransaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$this->startDate, $this->endDate])
            ->orderBy('amount', 'desc')
            ->limit(5)
            ->get();

        $topExpenses = FinancialTransaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$this->startDate, $this->endDate])
            ->orderBy('amount', 'desc')
            ->limit(5)
            ->get();

        $this->reportData = [
            'period' => [
                'start' => $this->startDate,
                'end' => $this->endDate,
                'type' => $this->reportType,
            ],
            'summary' => [
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'net_cash_flow' => $totalIncome - $totalExpenses,
                'savings_rate' => $totalIncome > 0 ? (($totalIncome - $totalExpenses) / $totalIncome) * 100 : 0,
                'net_worth' => $netWorth,
                'total_assets' => $totalAssets,
                'total_debts' => $totalDebts,
            ],
            'income_by_category' => $incomeByCategory,
            'expenses_by_category' => $expensesByCategory,
            'accounts' => $accounts,
            'goals' => $goals,
            'top_income' => $topIncome,
            'top_expenses' => $topExpenses,
        ];
    }

    public function exportPDF()
    {
        $user = Auth::user();
        try {
            $pdf = PDF::loadView('livewire.financial-planning.report-pdf', [
                'user' => $user,
                'data' => $this->reportData,
            ]);
            
            $filename = 'financial-report-' . $this->reportType . '-' . now()->format('Y-m-d') . '.pdf';
            return $pdf->download($filename);
        } catch (\Exception $e) {
            session()->flash('error', 'Error generating PDF: ' . $e->getMessage());
            return null;
        }
    }

    public function exportCSV()
    {
        $user = Auth::user();
        $transactions = FinancialTransaction::where('user_id', $user->id)
            ->whereBetween('transaction_date', [$this->startDate, $this->endDate])
            ->orderBy('transaction_date', 'desc')
            ->get();

        $filename = 'financial-transactions-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Type', 'Category', 'Amount', 'Description', 'Account']);
            
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->transaction_date->format('Y-m-d'),
                    ucfirst($transaction->type),
                    ucfirst(str_replace('_', ' ', $transaction->category)),
                    $transaction->amount,
                    $transaction->description ?? '',
                    $transaction->account->name ?? '',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        return view('livewire.financial-planning.financial-reports');
    }
}

