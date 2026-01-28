<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionAdmin extends Component
{
    public $filterStatus = 'all'; // all, active, cancelled, past_due, suspended
    public $filterPlanType = 'all'; // all, monthly, annual
    public $searchTerm = '';
    public $selectedSubscription = null;
    public $showDetailsModal = false;

    public function mount()
    {
        // Check if user is super admin
        if (!Auth::user()->hasRole('super_admin')) {
            abort(403, 'Unauthorized access');
        }
    }

    public function getSubscriptionsProperty()
    {
        $query = Subscription::with('user')
            ->when($this->filterStatus !== 'all', function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->filterPlanType !== 'all', function ($q) {
                $q->where('plan_type', $this->filterPlanType);
            })
            ->when($this->searchTerm, function ($q) {
                $q->whereHas('user', function ($userQuery) {
                    $userQuery->where('email', 'like', '%' . $this->searchTerm . '%')
                        ->orWhere('first_name', 'like', '%' . $this->searchTerm . '%')
                        ->orWhere('last_name', 'like', '%' . $this->searchTerm . '%');
                })->orWhere('stripe_customer_id', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('stripe_subscription_id', 'like', '%' . $this->searchTerm . '%');
            })
            ->orderBy('created_at', 'desc');

        return $query->paginate(20);
    }

    public function getStatsProperty()
    {
        $totalSubscriptions = Subscription::count();
        $activeSubscriptions = Subscription::where('status', 'active')
            ->where(function ($q) {
                $q->where('current_period_end', '>=', now())
                  ->orWhere(function ($q2) {
                      $q2->whereNull('current_period_end')
                         ->where('end_date', '>=', now());
                  });
            })
            ->count();
        
        $cancelledSubscriptions = Subscription::where('cancel_at_period_end', true)
            ->orWhere('status', 'cancelled')
            ->count();
        
        $pastDueSubscriptions = Subscription::where('status', 'past_due')->count();
        $suspendedSubscriptions = Subscription::where('status', 'suspended')->count();

        // Revenue calculations
        $monthlyRevenue = Subscription::where('status', 'active')
            ->where('plan_type', 'monthly')
            ->where(function ($q) {
                $q->where('current_period_end', '>=', now())
                  ->orWhere(function ($q2) {
                      $q2->whereNull('current_period_end')
                         ->where('end_date', '>=', now());
                  });
            })
            ->sum('amount');

        $annualRevenue = Subscription::where('status', 'active')
            ->where('plan_type', 'annual')
            ->where(function ($q) {
                $q->where('current_period_end', '>=', now())
                  ->orWhere(function ($q2) {
                      $q2->whereNull('current_period_end')
                         ->where('end_date', '>=', now());
                  });
            })
            ->sum('amount');

        $totalMonthlyRecurring = $monthlyRevenue / 100; // Convert from pennies
        $totalAnnualRecurring = $annualRevenue / 100;
        $totalAnnualizedRevenue = $totalMonthlyRecurring * 12 + $totalAnnualRecurring;

        // Failed payments
        $failedPayments = Subscription::where('status', 'past_due')
            ->orWhere('status', 'suspended')
            ->where('payment_retry_count', '>', 0)
            ->count();

        return [
            'total_subscriptions' => $totalSubscriptions,
            'active_subscriptions' => $activeSubscriptions,
            'cancelled_subscriptions' => $cancelledSubscriptions,
            'past_due_subscriptions' => $pastDueSubscriptions,
            'suspended_subscriptions' => $suspendedSubscriptions,
            'monthly_revenue' => $totalMonthlyRecurring,
            'annual_revenue' => $totalAnnualRecurring,
            'total_annualized_revenue' => $totalAnnualizedRevenue,
            'failed_payments' => $failedPayments,
        ];
    }

    public function getFailedPaymentsProperty()
    {
        return Subscription::with('user')
            ->where(function ($q) {
                $q->where('status', 'past_due')
                  ->orWhere('status', 'suspended');
            })
            ->where('payment_retry_count', '>', 0)
            ->orderBy('payment_retry_count', 'desc')
            ->orderBy('grace_period_end', 'asc')
            ->get();
    }

    public function viewDetails($subscriptionId)
    {
        $this->selectedSubscription = Subscription::with('user')->find($subscriptionId);
        $this->showDetailsModal = true;
    }

    public function closeModal()
    {
        $this->showDetailsModal = false;
        $this->selectedSubscription = null;
    }

    public function manualOverride($subscriptionId, $action)
    {
        $subscription = Subscription::find($subscriptionId);
        
        if (!$subscription) {
            session()->flash('error', 'Subscription not found.');
            return;
        }

        switch ($action) {
            case 'activate':
                $subscription->update([
                    'status' => 'active',
                    'payment_retry_count' => 0,
                    'grace_period_end' => null,
                ]);
                session()->flash('success', 'Subscription activated manually.');
                break;

            case 'suspend':
                $subscription->update([
                    'status' => 'suspended',
                ]);
                session()->flash('success', 'Subscription suspended manually.');
                break;

            case 'cancel':
                $subscription->update([
                    'cancel_at_period_end' => true,
                ]);
                session()->flash('success', 'Subscription will be cancelled at period end.');
                break;

            case 'extend':
                if ($subscription->current_period_end) {
                    $subscription->update([
                        'current_period_end' => $subscription->current_period_end->addMonth(),
                        'end_date' => $subscription->current_period_end,
                    ]);
                } elseif ($subscription->end_date) {
                    $subscription->update([
                        'end_date' => $subscription->end_date->addMonth(),
                        'current_period_end' => $subscription->end_date,
                    ]);
                }
                session()->flash('success', 'Subscription extended by 1 month.');
                break;
        }

        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.admin.subscription-admin', [
            'subscriptions' => $this->subscriptions,
            'stats' => $this->stats,
            'failedPayments' => $this->failedPayments,
        ])->layout('layouts.app');
    }
}
