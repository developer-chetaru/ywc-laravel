<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\Order as InternalOrder;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

class SubscriptionController extends Controller
{
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('subscription.page')
                ->with('error', 'Invalid session.');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // Retrieve the Stripe checkout session
            $checkoutSession = CheckoutSession::retrieve($sessionId);

            // Ensure payment is successful
            if ($checkoutSession->payment_status === 'paid') {
                // Find the internal order by session ID
                $order = InternalOrder::where('stripe_checkout_session_id', $sessionId)->first();

                if (!$order) {
                    return redirect()->route('subscription.page')
                        ->with('error', 'Order not found.');
                }

                // Avoid duplicate subscriptions
                if (!Subscription::where('stripe_session_id', $sessionId)->exists()) {
                    $start = Carbon::now();
                    $end   = Carbon::now()->addMonth();

                    // Create subscription
                    Subscription::create([
                        'user_id'                => $order->user_id,
                        'stripe_session_id'      => $sessionId,
                        'stripe_subscription_id' => $checkoutSession->subscription ?? null,
                        'amount'                 => $checkoutSession->amount_total ?? $order->amount,
                        'status'                 => 'active',
                        'interval'               => 'month',
                        'interval_count'         => 1,
                        'climate_order_id'       => $order->climate_order_id,
                        'start_date'             => $start,
                        'end_date'               => $end,
                    ]);

                    // Update order status
                    $order->update(['status' => 'completed']);
                }

                return redirect()->route('subscription.page')
                    ->with('success', 'Subscription activated!');
            }

            return redirect()->route('subscription.page')
                ->with('error', 'Payment not completed.');
        } catch (\Exception $e) {
            return redirect()->route('subscription.page')
                ->with('error', 'Invalid payment session.');
        }
    }
}