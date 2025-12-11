<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\FinancialAccount;
use App\Models\FinancialGoal;
use App\Models\FinancialTransaction;
use App\Models\FinancialBudget;
use App\Models\FinancialEducationalContent;
use App\Models\FinancialAdvisor;
use App\Models\FinancialSuccessStory;
use App\Models\FinancialNotification;
use Illuminate\Support\Facades\DB;

class FinancialPlanningSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create a test user
        $user = User::where('email', 'superadmin@mailinator.com')->first();
        
        if (!$user) {
            $user = User::factory()->create([
                'email' => 'superadmin@mailinator.com',
                'first_name' => 'Super',
                'last_name' => 'Admin',
            ]);
        }

        // Clear existing data for this user (optional)
        FinancialAccount::where('user_id', $user->id)->delete();
        FinancialGoal::where('user_id', $user->id)->delete();
        FinancialTransaction::where('user_id', $user->id)->delete();
        FinancialBudget::where('user_id', $user->id)->delete();

        // Create Financial Accounts
        $accounts = [
            [
                'user_id' => $user->id,
                'name' => 'Primary Savings',
                'type' => 'savings',
                'current_balance' => 25000.00,
                'institution' => 'Chase Bank',
                'account_number' => '****1234',
                'is_active' => true,
            ],
            [
                'user_id' => $user->id,
                'name' => 'Investment Portfolio',
                'type' => 'investment',
                'current_balance' => 75000.00,
                'institution' => 'Fidelity',
                'account_number' => '****5678',
                'is_active' => true,
            ],
            [
                'user_id' => $user->id,
                'name' => 'Retirement 401(k)',
                'type' => 'pension',
                'current_balance' => 125000.00,
                'institution' => 'Vanguard',
                'account_number' => '****9012',
                'is_active' => true,
            ],
            [
                'user_id' => $user->id,
                'name' => 'Emergency Fund',
                'type' => 'savings',
                'current_balance' => 15000.00,
                'institution' => 'Ally Bank',
                'account_number' => '****3456',
                'is_active' => true,
            ],
        ];

        foreach ($accounts as $account) {
            FinancialAccount::create($account);
        }

        $savingsAccount = FinancialAccount::where('user_id', $user->id)->where('type', 'savings')->first();
        $investmentAccount = FinancialAccount::where('user_id', $user->id)->where('type', 'investment')->first();
        $pensionAccount = FinancialAccount::where('user_id', $user->id)->where('type', 'pension')->first();

        // Create Financial Goals
        $goals = [
            [
                'user_id' => $user->id,
                'name' => 'Buy a House',
                'type' => 'property_deposit',
                'target_amount' => 500000.00,
                'current_amount' => 125000.00,
                'target_date' => now()->addYears(5),
                'priority' => 'high',
            ],
            [
                'user_id' => $user->id,
                'name' => 'Retirement at 60',
                'type' => 'retirement',
                'target_amount' => 2000000.00,
                'current_amount' => 200000.00,
                'target_date' => now()->addYears(25),
                'priority' => 'high',
            ],
            [
                'user_id' => $user->id,
                'name' => 'Children Education Fund',
                'type' => 'education',
                'target_amount' => 100000.00,
                'current_amount' => 25000.00,
                'target_date' => now()->addYears(15),
                'priority' => 'medium',
            ],
            [
                'user_id' => $user->id,
                'name' => 'Dream Yacht Trip',
                'type' => 'travel',
                'target_amount' => 750000.00,
                'current_amount' => 50000.00,
                'target_date' => now()->addYears(10),
                'priority' => 'low',
            ],
        ];

        foreach ($goals as $goal) {
            FinancialGoal::create($goal);
        }

        // Create Financial Transactions
        $transactions = [];
        for ($i = 0; $i < 30; $i++) {
            $isIncome = rand(0, 1) === 1;
            $accountId = $isIncome ? $savingsAccount->id : ($i % 2 === 0 ? $savingsAccount->id : $investmentAccount->id);
            $transactions[] = [
                'user_id' => $user->id,
                'account_id' => $accountId,
                'type' => $isIncome ? 'income' : 'expense',
                'category' => $isIncome ? 'salary' : ['groceries', 'utilities', 'entertainment', 'transportation', 'dining'][array_rand(['groceries', 'utilities', 'entertainment', 'transportation', 'dining'])],
                'amount' => $isIncome ? rand(3000, 8000) : rand(50, 500),
                'description' => $isIncome ? 'Monthly Salary' : ['Grocery Shopping', 'Electric Bill', 'Movie Tickets', 'Gas', 'Restaurant'][array_rand(['Grocery Shopping', 'Electric Bill', 'Movie Tickets', 'Gas', 'Restaurant'])],
                'transaction_date' => now()->subDays($i),
                'period_type' => 'both',
                'created_at' => now()->subDays($i),
                'updated_at' => now()->subDays($i),
            ];
        }

        foreach ($transactions as $transaction) {
            FinancialTransaction::create($transaction);
        }

        // Create Financial Budgets
        $budgets = [
            [
                'user_id' => $user->id,
                'name' => 'Monthly Budget - ' . now()->format('F Y'),
                'period' => 'monthly',
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth(),
                'total_income' => 7500.00,
                'total_expenses' => 6500.00,
                'savings_target' => 1500.00,
                'category_budgets' => [
                    'groceries' => 800.00,
                    'utilities' => 300.00,
                    'entertainment' => 500.00,
                    'dining' => 600.00,
                    'transportation' => 400.00,
                ],
                'is_active' => true,
            ],
        ];

        foreach ($budgets as $budget) {
            FinancialBudget::create($budget);
        }

        // Create Educational Content
        if (FinancialEducationalContent::count() === 0) {
            $content = [
                [
                    'title' => 'Understanding Compound Interest',
                    'type' => 'guide',
                    'description' => 'Learn how your money can grow exponentially over time.',
                    'content' => 'Compound interest is the eighth wonder of the world. This guide explains how compound interest works and how to leverage it for long-term wealth building.',
                    'difficulty' => 'beginner',
                    'duration_minutes' => 15,
                    'is_published' => true,
                    'order' => 1,
                ],
                [
                    'title' => 'Retirement Planning Basics',
                    'type' => 'guide',
                    'description' => 'A comprehensive guide to planning for retirement.',
                    'content' => 'Learn about 401(k) contributions, IRA options, and investment strategies for retirement planning.',
                    'difficulty' => 'intermediate',
                    'duration_minutes' => 30,
                    'is_published' => true,
                    'order' => 2,
                ],
                [
                    'title' => 'Emergency Fund: Your Financial Safety Net',
                    'type' => 'guide',
                    'description' => 'Build your financial safety net.',
                    'content' => 'Learn why an emergency fund is crucial and how to build one that covers 3-6 months of expenses.',
                    'difficulty' => 'beginner',
                    'duration_minutes' => 10,
                    'is_published' => true,
                    'order' => 3,
                ],
            ];

            foreach ($content as $item) {
                FinancialEducationalContent::create($item);
            }
        }

        // Create Financial Advisors
        if (FinancialAdvisor::count() === 0) {
            $advisors = [
                [
                    'name' => 'John Smith',
                    'email' => 'john.smith@financialadvisors.com',
                    'phone' => '+1-555-0101',
                    'bio' => 'Experienced financial advisor specializing in retirement planning and pension management.',
                    'specializations' => ['retirement planning', 'pension management'],
                    'qualifications' => ['CFP', 'CFA'],
                    'hourly_rate' => 200.00,
                    'rating' => 4.8,
                    'total_consultations' => 150,
                    'is_active' => true,
                ],
                [
                    'name' => 'Sarah Johnson',
                    'email' => 'sarah.johnson@financialadvisors.com',
                    'phone' => '+1-555-0102',
                    'bio' => 'Expert in investment portfolio management and tax planning strategies.',
                    'specializations' => ['investment portfolio management', 'tax planning'],
                    'qualifications' => ['CFA', 'CPA'],
                    'hourly_rate' => 250.00,
                    'rating' => 4.9,
                    'total_consultations' => 200,
                    'is_active' => true,
                ],
                [
                    'name' => 'Michael Chen',
                    'email' => 'michael.chen@financialadvisors.com',
                    'phone' => '+1-555-0103',
                    'bio' => 'Senior advisor specializing in estate planning and comprehensive wealth management.',
                    'specializations' => ['estate planning', 'wealth management'],
                    'qualifications' => ['CFP'],
                    'hourly_rate' => 300.00,
                    'rating' => 5.0,
                    'total_consultations' => 300,
                    'is_active' => true,
                ],
            ];

            foreach ($advisors as $advisor) {
                FinancialAdvisor::create($advisor);
            }
        }

        // Create Success Stories
        if (FinancialSuccessStory::count() === 0) {
            $stories = [
                [
                    'name' => 'Anonymous',
                    'position' => 'Yacht Crew Member',
                    'age' => 35,
                    'strategy_type' => 'aggressive_saver',
                    'story' => 'How one yacht crew member paid off $50,000 in debt and achieved financial independence in 5 years through disciplined saving and strategic investing.',
                    'starting_point' => -50000.00,
                    'current_status' => 200000.00,
                    'advice' => 'Focus on eliminating high-interest debt first, then build an emergency fund before investing.',
                    'is_featured' => true,
                    'is_published' => true,
                ],
                [
                    'name' => 'Captain James',
                    'position' => 'Yacht Captain',
                    'age' => 45,
                    'strategy_type' => 'early_starter',
                    'story' => 'A yacht captain shares how strategic investing and financial planning allowed for early retirement at age 45.',
                    'starting_point' => 0.00,
                    'current_status' => 2500000.00,
                    'advice' => 'Start investing early, maximize employer contributions, and live below your means.',
                    'is_featured' => true,
                    'is_published' => true,
                ],
            ];

            foreach ($stories as $story) {
                FinancialSuccessStory::create($story);
            }
        }

        // Create Notifications
        $notifications = [
            [
                'user_id' => $user->id,
                'type' => 'goal_milestone',
                'title' => 'Goal Progress Update',
                'message' => 'Your "Buy a House" goal is 25% complete. Keep up the great work!',
                'is_read' => false,
            ],
            [
                'user_id' => $user->id,
                'type' => 'budget_alert',
                'title' => 'Budget Warning',
                'message' => 'You\'ve spent 95% of your dining budget for this month.',
                'is_read' => false,
            ],
            [
                'user_id' => $user->id,
                'type' => 'bill_reminder',
                'title' => 'Payment Due Soon',
                'message' => 'Reminder: Credit card payment due in 3 days.',
                'is_read' => true,
            ],
        ];

        foreach ($notifications as $notification) {
            FinancialNotification::create($notification);
        }

        $this->command->info('✅ Financial Planning dummy data created successfully!');
        $this->command->info('📊 Created:');
        $this->command->info('   - ' . FinancialAccount::count() . ' accounts');
        $this->command->info('   - ' . FinancialGoal::count() . ' goals');
        $this->command->info('   - ' . FinancialTransaction::count() . ' transactions');
        $this->command->info('   - ' . FinancialBudget::count() . ' budgets');
        $this->command->info('   - ' . FinancialEducationalContent::count() . ' educational content items');
        $this->command->info('   - ' . FinancialAdvisor::count() . ' advisors');
        $this->command->info('   - ' . FinancialSuccessStory::count() . ' success stories');
        $this->command->info('   - ' . FinancialNotification::where('user_id', $user->id)->count() . ' notifications');
    }
}
