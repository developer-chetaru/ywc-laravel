<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Customer;
use Carbon\Carbon;

/**
 * @OA\Tag(
 *     name="Subscriptions",
 *     description="Subscription management endpoints"
 * )
 */
class SubscriptionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/subscriptions/current",
     *     summary="Get current user's subscription",
     *     tags={"Subscriptions"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Subscription retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Subscription retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="plan_type", type="string", enum={"monthly", "annual"}, example="monthly"),
     *                 @OA\Property(property="amount", type="integer", example=1000, description="Amount in cents"),
     *                 @OA\Property(property="status", type="string", enum={"active", "past_due", "cancelled", "suspended", "pending"}, example="active"),
     *                 @OA\Property(property="interval", type="string", example="month"),
     *                 @OA\Property(property="interval_count", type="integer", example=1),
     *                 @OA\Property(property="start_date", type="string", format="date-time", example="2026-01-27T10:00:00Z"),
     *                 @OA\Property(property="end_date", type="string", format="date-time", example="2026-02-27T10:00:00Z"),
     *                 @OA\Property(property="current_period_end", type="string", format="date-time", example="2026-02-27T10:00:00Z"),
     *                 @OA\Property(property="cancel_at_period_end", type="boolean", example=false),
     *                 @OA\Property(property="grace_period_end", type="string", format="date-time", nullable=true),
     *                 @OA\Property(property="payment_retry_count", type="integer", example=0),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="is_in_grace_period", type="boolean", example=false),
     *                 @OA\Property(property="is_cancelled", type="boolean", example=false),
     *                 @OA\Property(property="can_reactivate", type="boolean", example=false)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No subscription found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No subscription found"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function getCurrentSubscription()
    {
        try {
            $user = Auth::user();
            
            // Priority order: suspended > active > past_due > others
            $suspendedSubscription = Subscription::where('user_id', $user->id)
                ->where('status', 'suspended')
                ->latest()
                ->first();
            
            if ($suspendedSubscription) {
                $subscription = $suspendedSubscription;
            } else {
                $activeSubscription = Subscription::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->latest()
                    ->first();
                
                if ($activeSubscription) {
                    $subscription = $activeSubscription;
                } else {
                    $subscription = Subscription::where('user_id', $user->id)
                        ->whereIn('status', ['past_due', 'cancelled', 'pending'])
                        ->latest()
                        ->first();
                }
            }
            
            if (!$subscription) {
                return response()->json([
                    'status' => false,
                    'message' => 'No subscription found',
                    'data' => null
                ], 404);
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Subscription retrieved successfully',
                'data' => [
                    'id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                    'plan_type' => $subscription->plan_type,
                    'amount' => $subscription->amount,
                    'status' => $subscription->status,
                    'interval' => $subscription->interval,
                    'interval_count' => $subscription->interval_count,
                    'start_date' => $subscription->start_date?->toIso8601String(),
                    'end_date' => $subscription->end_date?->toIso8601String(),
                    'current_period_end' => $subscription->current_period_end?->toIso8601String(),
                    'cancel_at_period_end' => $subscription->cancel_at_period_end,
                    'grace_period_end' => $subscription->grace_period_end?->toIso8601String(),
                    'payment_retry_count' => $subscription->payment_retry_count,
                    'is_active' => $subscription->isActive(),
                    'is_in_grace_period' => $subscription->isInGracePeriod(),
                    'is_cancelled' => $subscription->isCancelled(),
                    'can_reactivate' => $subscription->canReactivate(),
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Get current subscription error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve subscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/subscriptions/plans",
     *     summary="Get available subscription plans",
     *     tags={"Subscriptions"},
     *     @OA\Response(
     *         response=200,
     *         description="Plans retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Plans retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="type", type="string", enum={"monthly", "annual"}, example="monthly"),
     *                     @OA\Property(property="name", type="string", example="Monthly Plan"),
     *                     @OA\Property(property="amount", type="integer", example=1000, description="Amount in cents"),
     *                     @OA\Property(property="formatted_amount", type="string", example="£10.00"),
     *                     @OA\Property(property="interval", type="string", example="month"),
     *                     @OA\Property(property="interval_count", type="integer", example=1),
     *                     @OA\Property(property="description", type="string", example="Billed monthly")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getPlans()
    {
        try {
            $plans = [
                [
                    'type' => 'monthly',
                    'name' => 'Monthly Plan',
                    'amount' => 1000, // £10.00 in cents
                    'formatted_amount' => '£10.00',
                    'interval' => 'month',
                    'interval_count' => 1,
                    'description' => 'Billed monthly'
                ],
                [
                    'type' => 'annual',
                    'name' => 'Annual Plan',
                    'amount' => 7188, // £71.88 in cents (save ~40%)
                    'formatted_amount' => '£71.88',
                    'interval' => 'year',
                    'interval_count' => 1,
                    'description' => 'Billed annually (save 40%)'
                ]
            ];
            
            return response()->json([
                'status' => true,
                'message' => 'Plans retrieved successfully',
                'data' => $plans
            ], 200);
        } catch (\Exception $e) {
            Log::error('Get plans error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve plans',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/subscriptions/checkout",
     *     summary="Create Stripe checkout session for subscription",
     *     tags={"Subscriptions"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"plan_type"},
     *             @OA\Property(property="plan_type", type="string", enum={"monthly", "annual"}, example="monthly", description="Subscription plan type")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Checkout session created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Checkout session created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="checkout_url", type="string", format="url", example="https://checkout.stripe.com/pay/cs_test_..."),
     *                 @OA\Property(property="session_id", type="string", example="cs_test_...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid plan type",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid plan type")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function createCheckoutSession(Request $request)
    {
        try {
            $request->validate([
                'plan_type' => 'required|in:monthly,annual'
            ]);

            $user = Auth::user();
            $planType = $request->plan_type;
            
            // Plan pricing
            $plans = [
                'monthly' => [
                    'price_id' => env('STRIPE_PRICE_ID_MONTHLY', 'price_monthly'),
                    'amount' => 1000,
                    'name' => 'Monthly Plan'
                ],
                'annual' => [
                    'price_id' => env('STRIPE_PRICE_ID_ANNUAL', 'price_annual'),
                    'amount' => 7188,
                    'name' => 'Annual Plan'
                ]
            ];
            
            if (!isset($plans[$planType])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid plan type'
                ], 400);
            }
            
            Stripe::setApiKey(config('services.stripe.secret'));
            
            // Get or create Stripe customer
            $customerId = $this->getOrCreateStripeCustomer($user);
            
            // Create checkout session
            $checkoutSession = CheckoutSession::create([
                'customer' => $customerId,
                'payment_method_types' => ['card'],
                'mode' => 'subscription',
                'line_items' => [[
                    'price' => $plans[$planType]['price_id'],
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_type' => $planType,
                ],
                'success_url' => config('app.url') . '/subscription/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => config('app.url') . '/subscription',
            ]);
            
            return response()->json([
                'status' => true,
                'message' => 'Checkout session created successfully',
                'data' => [
                    'checkout_url' => $checkoutSession->url,
                    'session_id' => $checkoutSession->id
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Create checkout session error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to create checkout session',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/subscriptions/customer-portal",
     *     summary="Get Stripe Customer Portal URL",
     *     tags={"Subscriptions"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Customer portal URL retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Customer portal URL retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="url", type="string", format="url", example="https://billing.stripe.com/p/session_...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No subscription or customer found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No subscription or customer found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function getCustomerPortalUrl()
    {
        try {
            $user = Auth::user();
            
            $subscription = Subscription::where('user_id', $user->id)
                ->whereNotNull('stripe_customer_id')
                ->latest()
                ->first();
            
            if (!$subscription || !$subscription->stripe_customer_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'No subscription or customer found'
                ], 404);
            }
            
            Stripe::setApiKey(config('services.stripe.secret'));
            
            $session = \Stripe\BillingPortal\Session::create([
                'customer' => $subscription->stripe_customer_id,
                'return_url' => config('app.url') . '/subscription',
            ]);
            
            return response()->json([
                'status' => true,
                'message' => 'Customer portal URL retrieved successfully',
                'data' => [
                    'url' => $session->url
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Get customer portal URL error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve customer portal URL',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/subscriptions/cancel",
     *     summary="Cancel subscription at period end",
     *     tags={"Subscriptions"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Subscription cancelled successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Subscription will be cancelled at the end of the billing period")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No active subscription found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No active subscription found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function cancel()
    {
        try {
            $user = Auth::user();
            
            $subscription = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->whereNotNull('stripe_subscription_id')
                ->latest()
                ->first();
            
            if (!$subscription) {
                return response()->json([
                    'status' => false,
                    'message' => 'No active subscription found'
                ], 404);
            }
            
            Stripe::setApiKey(config('services.stripe.secret'));
            
            $stripeSubscription = \Stripe\Subscription::retrieve($subscription->stripe_subscription_id);
            $stripeSubscription->cancel_at_period_end = true;
            $stripeSubscription->save();
            
            $subscription->update([
                'cancel_at_period_end' => true
            ]);
            
            return response()->json([
                'status' => true,
                'message' => 'Subscription will be cancelled at the end of the billing period'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Cancel subscription error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to cancel subscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/subscriptions/reactivate",
     *     summary="Reactivate a cancelled subscription",
     *     tags={"Subscriptions"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Subscription reactivated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Subscription reactivated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Subscription cannot be reactivated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Subscription cannot be reactivated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No subscription found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No subscription found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function reactivate()
    {
        try {
            $user = Auth::user();
            
            $subscription = Subscription::where('user_id', $user->id)
                ->where('cancel_at_period_end', true)
                ->whereNotNull('stripe_subscription_id')
                ->latest()
                ->first();
            
            if (!$subscription) {
                return response()->json([
                    'status' => false,
                    'message' => 'No subscription found'
                ], 404);
            }
            
            if (!$subscription->canReactivate()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Subscription cannot be reactivated'
                ], 400);
            }
            
            Stripe::setApiKey(config('services.stripe.secret'));
            
            $stripeSubscription = \Stripe\Subscription::retrieve($subscription->stripe_subscription_id);
            $stripeSubscription->cancel_at_period_end = false;
            $stripeSubscription->save();
            
            $subscription->update([
                'cancel_at_period_end' => false
            ]);
            
            return response()->json([
                'status' => true,
                'message' => 'Subscription reactivated successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Reactivate subscription error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to reactivate subscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/subscriptions/purchase-history",
     *     summary="Get user's purchase history (all subscriptions)",
     *     tags={"Subscriptions"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Purchase history retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Purchase history retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="plan_type", type="string", enum={"monthly", "annual"}, example="monthly"),
     *                     @OA\Property(property="amount", type="integer", example=1000),
     *                     @OA\Property(property="formatted_amount", type="string", example="£10.00"),
     *                     @OA\Property(property="status", type="string", example="active"),
     *                     @OA\Property(property="start_date", type="string", format="date-time", example="2026-01-27T10:00:00Z"),
     *                     @OA\Property(property="end_date", type="string", format="date-time", example="2026-02-27T10:00:00Z"),
     *                     @OA\Property(property="invoice_url", type="string", format="url", nullable=true, example="https://invoice.stripe.com/i/...")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function getPurchaseHistory()
    {
        try {
            $user = Auth::user();
            
            $subscriptions = Subscription::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            $history = $subscriptions->map(function ($subscription) {
                $invoiceUrl = null;
                
                if ($subscription->stripe_subscription_id) {
                    try {
                        Stripe::setApiKey(config('services.stripe.secret'));
                        $invoices = \Stripe\Invoice::all([
                            'subscription' => $subscription->stripe_subscription_id,
                            'limit' => 1
                        ]);
                        
                        if (count($invoices->data) > 0 && $invoices->data[0]->hosted_invoice_url) {
                            $invoiceUrl = $invoices->data[0]->hosted_invoice_url;
                        }
                    } catch (\Exception $e) {
                        Log::warning('Failed to fetch invoice URL', ['error' => $e->getMessage()]);
                    }
                }
                
                return [
                    'id' => $subscription->id,
                    'plan_type' => $subscription->plan_type,
                    'amount' => $subscription->amount,
                    'formatted_amount' => '£' . number_format($subscription->amount / 100, 2),
                    'status' => $subscription->status,
                    'start_date' => $subscription->start_date?->toIso8601String(),
                    'end_date' => $subscription->end_date?->toIso8601String(),
                    'invoice_url' => $invoiceUrl,
                ];
            });
            
            return response()->json([
                'status' => true,
                'message' => 'Purchase history retrieved successfully',
                'data' => $history
            ], 200);
        } catch (\Exception $e) {
            Log::error('Get purchase history error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve purchase history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get or create Stripe customer for user
     */
    protected function getOrCreateStripeCustomer($user)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        
        // Check if user already has a Stripe customer ID
        $existingSubscription = Subscription::where('user_id', $user->id)
            ->whereNotNull('stripe_customer_id')
            ->first();
        
        if ($existingSubscription && $existingSubscription->stripe_customer_id) {
            try {
                // Verify customer still exists in Stripe
                Customer::retrieve($existingSubscription->stripe_customer_id);
                return $existingSubscription->stripe_customer_id;
            } catch (\Exception $e) {
                // Customer doesn't exist, create new one
            }
        }
        
        // Create new Stripe customer
        $customer = Customer::create([
            'email' => $user->email,
            'name' => $user->first_name . ' ' . $user->last_name,
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);
        
        return $customer->id;
    }
}
