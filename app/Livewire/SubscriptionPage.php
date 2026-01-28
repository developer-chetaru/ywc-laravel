<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Customer;
use Stripe\Subscription as StripeSubscription;
use Carbon\Carbon;

class SubscriptionPage extends Component
{
    public $subscription;
    public $selectedPlan = 'monthly'; // 'monthly' or 'annual'

    // Plan pricing (in pennies)
    const MONTHLY_PRICE = 1000; // £10.00
    const ANNUAL_PRICE = 7188; // £71.88

    public function mount()
    {
        $this->loadSubscription();
    }

    public function loadSubscription()
    {
        // Priority order: suspended > active > past_due > others
        // First check for suspended subscription (highest priority)
        $suspendedSubscription = Subscription::where('user_id', Auth::id())
            ->where('status', 'suspended')
            ->latest()
            ->first();
        
        if ($suspendedSubscription) {
            $this->subscription = $suspendedSubscription;
            return;
        }
        
        // Then check for active subscription
        $activeSubscription = Subscription::where('user_id', Auth::id())
            ->where('status', 'active')
            ->latest()
            ->first();
        
        if ($activeSubscription) {
            $this->subscription = $activeSubscription;
            
            // If subscription exists but current_period_end is missing, set it
            if (!$this->subscription->current_period_end) {
                // Set default period end (1 month from now for monthly, 1 year for annual)
                $periodEnd = $this->subscription->plan_type === 'annual' 
                    ? now()->addYear() 
                    : now()->addMonth();
                
                $this->subscription->update([
                    'current_period_end' => $periodEnd,
                    'end_date' => $periodEnd,
                ]);
                $this->subscription->refresh();
            }
            return;
        }
        
        // Then check for past_due
        $pastDueSubscription = Subscription::where('user_id', Auth::id())
            ->where('status', 'past_due')
            ->latest()
            ->first();
        
        if ($pastDueSubscription) {
            $this->subscription = $pastDueSubscription;
            return;
        }
        
        // Finally, get any other subscription (cancelled, pending, etc.)
        $this->subscription = Subscription::where('user_id', Auth::id())
            ->whereIn('status', ['cancelled', 'pending'])
            ->latest()
            ->first();
    }

    public function updated()
    {
        $this->loadSubscription();
    }
    
    public function refreshSubscription()
    {
        $this->loadSubscription();
    }

    public function checkout($planType = 'monthly')
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $userId = Auth::id();
            $user = Auth::user();

            // Determine price and interval
            if ($planType === 'annual') {
                $amount = self::ANNUAL_PRICE;
                $interval = 'year';
                $intervalCount = 1;
                $planName = 'Annual Plan';
            } else {
                $amount = self::MONTHLY_PRICE;
                $interval = 'month';
                $intervalCount = 1;
                $planName = 'Monthly Plan';
            }

            // Get or create Stripe customer
            $customer = $this->getOrCreateStripeCustomer($user);

            // Create Stripe Checkout Session for subscription
            $checkoutSession = CheckoutSession::create([
                'customer' => $customer->id,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'gbp',
                        'product_data' => [
                            'name' => 'YWC Full Access Membership - ' . $planName,
                        ],
                        'unit_amount' => $amount,
                        'recurring' => [
                            'interval' => $interval,
                            'interval_count' => $intervalCount,
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscription.cancel'),
                'metadata' => [
                    'user_id' => $userId,
                    'plan_type' => $planType,
                ],
                'subscription_data' => [
                    'metadata' => [
                        'user_id' => $userId,
                        'plan_type' => $planType,
                    ],
                ],
                'customer_update' => [
                    'address' => 'auto',
                ],
            ]);

            // Redirect to Stripe Checkout
            return redirect()->away($checkoutSession->url);
        } catch (\Exception $e) {
            Log::error('Subscription checkout error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            session()->flash('error', 'An error occurred. Please try again.');
            return;
        }
    }

    protected function getOrCreateStripeCustomer($user)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        // Check if user already has a Stripe customer ID
        $existingSubscription = Subscription::where('user_id', $user->id)
            ->whereNotNull('stripe_customer_id')
            ->first();

        if ($existingSubscription && $existingSubscription->stripe_customer_id) {
            try {
                return Customer::retrieve($existingSubscription->stripe_customer_id);
            } catch (\Exception $e) {
                // Customer might not exist, create new one
            }
        }

        // Create new Stripe customer
        $customer = Customer::create([
            'email' => $user->email,
            'name' => $user->name ?? ($user->first_name . ' ' . $user->last_name),
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        return $customer;
    }

    public function cancel()
    {
        try {
            if (!$this->subscription || !$this->subscription->stripe_subscription_id) {
                session()->flash('error', 'No active subscription found.');
                return;
            }

            Stripe::setApiKey(config('services.stripe.secret'));
            $stripeSubscription = StripeSubscription::retrieve($this->subscription->stripe_subscription_id);
            
            // Cancel at period end
            $stripeSubscription->cancel_at_period_end = true;
            $stripeSubscription->save();

            // Update local subscription
            $this->subscription->update([
                'cancel_at_period_end' => true,
            ]);

            session()->flash('success', 'Your subscription will be cancelled at the end of your billing period (' . 
                $this->subscription->current_period_end->format('d M Y') . ').');
            
            $this->subscription->refresh();
        } catch (\Exception $e) {
            Log::error('Subscription cancellation error', [
                'error' => $e->getMessage(),
                'subscription_id' => $this->subscription->id ?? null,
            ]);
            session()->flash('error', 'An error occurred while cancelling your subscription.');
        }
    }

    public function reactivate()
    {
        try {
            if (!$this->subscription || !$this->subscription->stripe_subscription_id) {
                session()->flash('error', 'No subscription found.');
                return;
            }

            if (!$this->subscription->canReactivate()) {
                session()->flash('error', 'This subscription cannot be reactivated.');
                return;
            }

            Stripe::setApiKey(config('services.stripe.secret'));
            $stripeSubscription = StripeSubscription::retrieve($this->subscription->stripe_subscription_id);
            
            // Remove cancellation
            $stripeSubscription->cancel_at_period_end = false;
            $stripeSubscription->save();

            // Update local subscription
            $this->subscription->update([
                'cancel_at_period_end' => false,
            ]);

            session()->flash('success', 'Your subscription has been reactivated!');
            
            $this->subscription->refresh();
        } catch (\Exception $e) {
            Log::error('Subscription reactivation error', [
                'error' => $e->getMessage(),
                'subscription_id' => $this->subscription->id ?? null,
            ]);
            session()->flash('error', 'An error occurred while reactivating your subscription.');
        }
    }

    public function getCustomerPortalUrl()
    {
        try {
            if (!$this->subscription || !$this->subscription->stripe_customer_id) {
                return null;
            }

            Stripe::setApiKey(config('services.stripe.secret'));
            
            $session = \Stripe\BillingPortal\Session::create([
                'customer' => $this->subscription->stripe_customer_id,
                'return_url' => route('subscription.page'),
            ]);

            return $session->url;
        } catch (\Exception $e) {
            Log::error('Customer portal URL error', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function render()
    {
        return view('livewire.subscription-page')->layout('layouts.app');
    }
}
