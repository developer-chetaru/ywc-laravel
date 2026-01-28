<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Invoice;
use Illuminate\Support\Facades\Log;

class PurchaseHistory extends Component
{
    public function getInvoiceUrl($subscriptionId)
    {
        try {
            $subscription = Subscription::find($subscriptionId);
            
            if (!$subscription || !$subscription->stripe_subscription_id) {
                return null;
            }

            Stripe::setApiKey(config('services.stripe.secret'));
            
            // Get the latest invoice for this subscription
            $invoices = Invoice::all([
                'subscription' => $subscription->stripe_subscription_id,
                'limit' => 1,
            ]);

            if ($invoices->data && count($invoices->data) > 0) {
                $invoice = $invoices->data[0];
                return $invoice->hosted_invoice_url ?? $invoice->invoice_pdf ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching invoice URL', [
                'error' => $e->getMessage(),
                'subscription_id' => $subscriptionId,
            ]);
            return null;
        }
    }

    public function render()
    {
        $user = Auth::user();
        
        // Get all subscriptions for the user
        $subscriptions = Subscription::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($subscription) {
                $invoiceUrl = null;
                
                // Get invoice URL if subscription has Stripe ID
                if ($subscription->stripe_subscription_id) {
                    try {
                        Stripe::setApiKey(config('services.stripe.secret'));
                        $invoices = Invoice::all([
                            'subscription' => $subscription->stripe_subscription_id,
                            'limit' => 1,
                        ]);

                        if ($invoices->data && count($invoices->data) > 0) {
                            $invoice = $invoices->data[0];
                            $invoiceUrl = $invoice->hosted_invoice_url ?? $invoice->invoice_pdf ?? null;
                        }
                    } catch (\Exception $e) {
                        Log::warning('Error fetching invoice URL', [
                            'error' => $e->getMessage(),
                            'subscription_id' => $subscription->id,
                        ]);
                    }
                }
                
                return [
                    'id' => $subscription->id,
                    'type' => 'Subscription',
                    'plan' => $subscription->plan_type === 'annual' ? 'Annual Plan' : 'Monthly Plan',
                    'amount' => $subscription->amount ? number_format($subscription->amount / 100, 2) : '0.00',
                    'status' => ucfirst($subscription->status),
                    'date' => $subscription->created_at ? Carbon::parse($subscription->created_at)->format('d M Y') : 'N/A',
                    'start_date' => $subscription->start_date ? Carbon::parse($subscription->start_date)->format('d M Y') : 'N/A',
                    'end_date' => $subscription->current_period_end ? Carbon::parse($subscription->current_period_end)->format('d M Y') : 'N/A',
                    'interval' => $subscription->interval ?? 'month',
                    'stripe_subscription_id' => $subscription->stripe_subscription_id,
                    'stripe_customer_id' => $subscription->stripe_customer_id,
                    'invoice_url' => $invoiceUrl,
                ];
            });

        return view('livewire.purchase-history', [
            'subscriptions' => $subscriptions
        ])->layout('layouts.app');
    }
}

