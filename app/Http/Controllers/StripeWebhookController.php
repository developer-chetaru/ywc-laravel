<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Subscription;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Carbon\Carbon;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook: Invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook: Invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'invoice.payment_succeeded':
                $this->handleInvoicePaymentSucceeded($event->data->object);
                break;

            case 'invoice.payment_failed':
                $this->handleInvoicePaymentFailed($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;

            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event->data->object);
                break;

            default:
                Log::info('Stripe webhook: Unhandled event type', ['type' => $event->type]);
        }

        return response()->json(['received' => true]);
    }

    protected function handleCheckoutSessionCompleted($session)
    {
        try {
            $subscriptionId = $session->subscription;
            $customerId = $session->customer;
            $userId = $session->metadata->user_id ?? null;

            if (!$userId) {
                Log::error('Stripe webhook: No user_id in checkout session metadata', ['session_id' => $session->id]);
                return;
            }

            $user = User::find($userId);
            if (!$user) {
                Log::error('Stripe webhook: User not found', ['user_id' => $userId]);
                return;
            }

            // Get subscription details from Stripe
            if ($subscriptionId) {
                Stripe::setApiKey(config('services.stripe.secret'));
                $stripeSubscription = \Stripe\Subscription::retrieve($subscriptionId);
                
                $planType = $this->determinePlanType($stripeSubscription);
                $currentPeriodEnd = Carbon::createFromTimestamp($stripeSubscription->current_period_end);

                // Create or update subscription
                Subscription::updateOrCreate(
                    ['stripe_subscription_id' => $subscriptionId],
                    [
                        'user_id' => $userId,
                        'stripe_customer_id' => $customerId,
                        'stripe_session_id' => $session->id,
                        'plan_type' => $planType,
                        'amount' => $stripeSubscription->items->data[0]->price->unit_amount,
                        'status' => $stripeSubscription->status === 'active' ? 'active' : 'pending',
                        'interval' => $stripeSubscription->items->data[0]->price->recurring->interval,
                        'interval_count' => $stripeSubscription->items->data[0]->price->recurring->interval_count ?? 1,
                        'start_date' => Carbon::createFromTimestamp($stripeSubscription->current_period_start),
                        'end_date' => $currentPeriodEnd,
                        'current_period_end' => $currentPeriodEnd,
                        'cancel_at_period_end' => $stripeSubscription->cancel_at_period_end ?? false,
                    ]
                );

                // Send activation email
                $this->sendSubscriptionActivatedEmail($user);
            }
        } catch (\Exception $e) {
            Log::error('Stripe webhook: Error handling checkout.session.completed', [
                'error' => $e->getMessage(),
                'session_id' => $session->id ?? null,
            ]);
        }
    }

    protected function handleInvoicePaymentSucceeded($invoice)
    {
        try {
            $subscriptionId = $invoice->subscription;
            if (!$subscriptionId) {
                return;
            }

            $subscription = Subscription::where('stripe_subscription_id', $subscriptionId)->first();
            if (!$subscription) {
                Log::warning('Stripe webhook: Subscription not found for invoice', ['subscription_id' => $subscriptionId]);
                return;
            }

            // Update subscription status and period
            Stripe::setApiKey(config('services.stripe.secret'));
            $stripeSubscription = \Stripe\Subscription::retrieve($subscriptionId);

            $subscription->update([
                'status' => 'active',
                'current_period_end' => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
                'end_date' => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
                'payment_retry_count' => 0, // Reset retry count on successful payment
                'grace_period_end' => null,
            ]);

            // Send payment confirmation email
            $this->sendPaymentSucceededEmail($subscription->user, $invoice);
        } catch (\Exception $e) {
            Log::error('Stripe webhook: Error handling invoice.payment_succeeded', [
                'error' => $e->getMessage(),
                'invoice_id' => $invoice->id ?? null,
            ]);
        }
    }

    protected function handleInvoicePaymentFailed($invoice)
    {
        try {
            $subscriptionId = $invoice->subscription;
            if (!$subscriptionId) {
                return;
            }

            $subscription = Subscription::where('stripe_subscription_id', $subscriptionId)->first();
            if (!$subscription) {
                return;
            }

            // Increment retry count
            $retryCount = $subscription->payment_retry_count + 1;
            $gracePeriodEnd = Carbon::now()->addDays(7);

            $subscription->update([
                'status' => 'past_due',
                'payment_retry_count' => $retryCount,
                'grace_period_end' => $gracePeriodEnd,
            ]);

            // Send payment failed email
            $this->sendPaymentFailedEmail($subscription->user, $invoice, $retryCount);

            // If 3 failed attempts and 7 days passed, suspend account
            if ($retryCount >= 3 && $subscription->grace_period_end && $subscription->grace_period_end->isPast()) {
                $subscription->update(['status' => 'suspended']);
                $this->sendAccountSuspendedEmail($subscription->user);
            }
        } catch (\Exception $e) {
            Log::error('Stripe webhook: Error handling invoice.payment_failed', [
                'error' => $e->getMessage(),
                'invoice_id' => $invoice->id ?? null,
            ]);
        }
    }

    protected function handleSubscriptionDeleted($stripeSubscription)
    {
        try {
            $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();
            if ($subscription) {
                $subscription->update([
                    'status' => 'cancelled',
                    'cancel_at_period_end' => false,
                ]);

                $this->sendSubscriptionCancelledEmail($subscription->user);
            }
        } catch (\Exception $e) {
            Log::error('Stripe webhook: Error handling customer.subscription.deleted', [
                'error' => $e->getMessage(),
                'subscription_id' => $stripeSubscription->id ?? null,
            ]);
        }
    }

    protected function handleSubscriptionUpdated($stripeSubscription)
    {
        try {
            $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();
            if (!$subscription) {
                return;
            }

            $subscription->update([
                'status' => $stripeSubscription->status === 'active' ? 'active' : $stripeSubscription->status,
                'current_period_end' => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
                'cancel_at_period_end' => $stripeSubscription->cancel_at_period_end ?? false,
            ]);

            // If reactivated
            if ($stripeSubscription->cancel_at_period_end === false && $subscription->wasChanged('cancel_at_period_end')) {
                $this->sendSubscriptionReactivatedEmail($subscription->user);
            }
        } catch (\Exception $e) {
            Log::error('Stripe webhook: Error handling customer.subscription.updated', [
                'error' => $e->getMessage(),
                'subscription_id' => $stripeSubscription->id ?? null,
            ]);
        }
    }

    protected function determinePlanType($stripeSubscription)
    {
        $interval = $stripeSubscription->items->data[0]->price->recurring->interval ?? 'month';
        $intervalCount = $stripeSubscription->items->data[0]->price->recurring->interval_count ?? 1;

        if ($interval === 'year' || ($interval === 'month' && $intervalCount == 12)) {
            return 'annual';
        }

        return 'monthly';
    }

    // Email notification methods
    protected function sendSubscriptionActivatedEmail($user)
    {
        // TODO: Create email template
        Mail::raw("Your subscription has been activated! Welcome to YWC.", function ($message) use ($user) {
            $message->to($user->email)->subject('Subscription Activated - YWC');
        });
    }

    protected function sendPaymentSucceededEmail($user, $invoice)
    {
        Mail::raw("Your payment of £" . number_format($invoice->amount_paid / 100, 2) . " was successful.", function ($message) use ($user) {
            $message->to($user->email)->subject('Payment Successful - YWC');
        });
    }

    protected function sendPaymentFailedEmail($user, $invoice, $retryCount)
    {
        $message = "Your payment failed. Retry attempt: {$retryCount}/3. Please update your payment method.";
        Mail::raw($message, function ($m) use ($user) {
            $m->to($user->email)->subject('Payment Failed - Action Required - YWC');
        });
    }

    protected function sendAccountSuspendedEmail($user)
    {
        Mail::raw("Your account has been suspended due to failed payments. Please update your payment method to restore access.", function ($message) use ($user) {
            $message->to($user->email)->subject('Account Suspended - YWC');
        });
    }

    protected function sendSubscriptionCancelledEmail($user)
    {
        Mail::raw("Your subscription has been cancelled. Your access will continue until the end of your billing period.", function ($message) use ($user) {
            $message->to($user->email)->subject('Subscription Cancelled - YWC');
        });
    }

    protected function sendSubscriptionReactivatedEmail($user)
    {
        Mail::raw("Your subscription has been reactivated! Thank you for continuing with YWC.", function ($message) use ($user) {
            $message->to($user->email)->subject('Subscription Reactivated - YWC');
        });
    }
}
