<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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

            // For subscription mode, webhook will handle the actual subscription creation
            // But if webhook hasn't processed yet, create subscription directly
            if ($checkoutSession->mode === 'subscription' && $checkoutSession->payment_status === 'paid') {
                $user = Auth::user();
                
                // Check if subscription already exists (created by webhook)
                $subscription = Subscription::where('stripe_session_id', $sessionId)
                    ->orWhere('stripe_subscription_id', $checkoutSession->subscription)
                    ->first();

                if ($subscription) {
                    // Ensure status is active and refresh subscription data from Stripe
                    if ($subscription->stripe_subscription_id) {
                        try {
                            $stripeSubscription = \Stripe\Subscription::retrieve($subscription->stripe_subscription_id);
                            $subscription->update([
                                'status' => $stripeSubscription->status === 'active' ? 'active' : $subscription->status,
                                'current_period_end' => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
                                'end_date' => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
                            ]);
                        } catch (\Exception $e) {
                            Log::warning('Failed to refresh subscription from Stripe', ['error' => $e->getMessage()]);
                            if ($subscription->status !== 'active') {
                                $subscription->update(['status' => 'active']);
                            }
                        }
                    } else {
                        if ($subscription->status !== 'active') {
                            $subscription->update(['status' => 'active']);
                        }
                    }
                    return redirect()->route('subscription.page')
                        ->with('success', 'Subscription activated! Your subscription is now active.');
                }

                // If webhook hasn't processed yet, create subscription directly
                if ($checkoutSession->subscription) {
                    try {
                        Stripe::setApiKey(config('services.stripe.secret'));
                        
                        // Expand subscription to get all data
                        $stripeSubscription = \Stripe\Subscription::retrieve([
                            'id' => $checkoutSession->subscription,
                            'expand' => ['items.data.price.product']
                        ]);
                        
                        // Determine plan type from metadata or interval
                        $planType = $checkoutSession->metadata->plan_type ?? 
                                   ($stripeSubscription->items->data[0]->price->recurring->interval === 'year' ? 'annual' : 'monthly');
                        
                        // Get period dates - if not available, calculate from now
                        $currentPeriodStart = isset($stripeSubscription->current_period_start) 
                            ? Carbon::createFromTimestamp($stripeSubscription->current_period_start)
                            : now();
                            
                        $currentPeriodEnd = isset($stripeSubscription->current_period_end) 
                            ? Carbon::createFromTimestamp($stripeSubscription->current_period_end)
                            : ($planType === 'annual' ? now()->addYear() : now()->addMonth());
                        
                        // Get amount from price or use default
                        $amount = $stripeSubscription->items->data[0]->price->unit_amount ?? 
                                 ($planType === 'annual' ? 7188 : 1000);
                        
                        // Get interval
                        $interval = $stripeSubscription->items->data[0]->price->recurring->interval ?? 
                                   ($planType === 'annual' ? 'year' : 'month');
                        
                        // Check if subscription already exists to avoid duplicates
                        $existingSubscription = Subscription::where('stripe_subscription_id', $checkoutSession->subscription)
                            ->orWhere('stripe_session_id', $sessionId)
                            ->first();

                        if ($existingSubscription) {
                            // Update existing subscription with all data
                            $existingSubscription->update([
                                'user_id' => $user->id,
                                'stripe_customer_id' => $checkoutSession->customer,
                                'stripe_session_id' => $sessionId,
                                'stripe_subscription_id' => $checkoutSession->subscription,
                                'plan_type' => $planType,
                                'amount' => $amount,
                                'status' => $stripeSubscription->status === 'active' ? 'active' : 'pending',
                                'interval' => $interval,
                                'interval_count' => $stripeSubscription->items->data[0]->price->recurring->interval_count ?? 1,
                                'start_date' => $currentPeriodStart,
                                'end_date' => $currentPeriodEnd,
                                'current_period_end' => $currentPeriodEnd,
                                'cancel_at_period_end' => $stripeSubscription->cancel_at_period_end ?? false,
                            ]);
                            $subscription = $existingSubscription;
                            
                            Log::info('Subscription updated in success handler', [
                                'subscription_id' => $existingSubscription->id,
                                'stripe_subscription_id' => $checkoutSession->subscription,
                            ]);
                        } else {
                            // Create new subscription
                            $subscription = Subscription::create([
                                'user_id' => $user->id,
                                'stripe_customer_id' => $checkoutSession->customer,
                                'stripe_session_id' => $sessionId,
                                'stripe_subscription_id' => $checkoutSession->subscription,
                                'plan_type' => $planType,
                                'amount' => $amount,
                                'status' => $stripeSubscription->status === 'active' ? 'active' : 'pending',
                                'interval' => $interval,
                                'interval_count' => $stripeSubscription->items->data[0]->price->recurring->interval_count ?? 1,
                                'start_date' => $currentPeriodStart,
                                'end_date' => $currentPeriodEnd,
                                'current_period_end' => $currentPeriodEnd,
                                'cancel_at_period_end' => $stripeSubscription->cancel_at_period_end ?? false,
                            ]);
                            
                            Log::info('Subscription created in success handler', [
                                'subscription_id' => $subscription->id,
                                'stripe_subscription_id' => $checkoutSession->subscription,
                                'plan_type' => $planType,
                            ]);
                        }

                        return redirect()->route('subscription.page')
                            ->with('success', 'Subscription activated! Your subscription is now active.');
                    } catch (\Exception $e) {
                        Log::error('Subscription creation error in success handler', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                            'session_id' => $sessionId,
                            'subscription_id' => $checkoutSession->subscription ?? null,
                        ]);
                        return redirect()->route('subscription.page')
                            ->with('success', 'Payment successful! Your subscription is being activated. This may take a few moments.');
                    }
                }

                // If webhook hasn't processed yet, show pending message
                return redirect()->route('subscription.page')
                    ->with('success', 'Payment successful! Your subscription is being activated. This may take a few moments.');
            }

            return redirect()->route('subscription.page')
                ->with('error', 'Payment not completed.');
        } catch (\Exception $e) {
            Log::error('Subscription success handler error', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId,
            ]);
            return redirect()->route('subscription.page')
                ->with('error', 'Invalid payment session.');
        }
    }
}