<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Subscription;
use App\Models\Order as InternalOrder;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Climate\Order as StripeClimateOrder;
use Carbon\Carbon;

class SubscriptionPage extends Component
{
    public $subscription;

    public function mount()
    {
        $this->subscription = Subscription::where('user_id', Auth::id())->latest()->first();
    }

    public function checkout()
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $amount = 5000; // £50 in pennies
        $userId = Auth::id();

        // Step 1: Create Stripe Climate Order
        $climateOrder = StripeClimateOrder::create([
            'metric_tons' => 0.01,
            'product' => 'climsku_frontier_offtake_portfolio_2027',
        ]);

        // Step 2: Save internal order in DB BEFORE Stripe Checkout
        $order = InternalOrder::create([
            'user_id' => $userId,
            'amount' => $amount,
            'climate_order_id' => $climateOrder->id,
            'status' => 'pending',
        ]);

        // Step 3: Create Stripe Checkout Session
        $checkoutSession = CheckoutSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'gbp',
                    'product_data' => ['name' => 'Full Access Membership'],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('subscription.cancel'),
            'metadata' => [
                'order_id' => $order->id,
                'climate_order_id' => $climateOrder->id,
            ],
        ]);

         

        // Step 4: Save session ID in order
        $order->stripe_checkout_session_id = $checkoutSession->id;
        $order->save();

        // Step 5: Redirect to Stripe Checkout
        return redirect()->away($checkoutSession->url);
    }

    public function render()
    {
        return view('livewire.subscription-page')->layout('layouts.app');
    }
}
