<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Add new required fields
            $table->string('stripe_customer_id')->nullable()->after('user_id');
            $table->string('plan_type')->nullable()->after('stripe_subscription_id'); // 'monthly' or 'annual'
            $table->timestamp('current_period_end')->nullable()->after('end_date');
            $table->boolean('cancel_at_period_end')->default(false)->after('current_period_end');
            $table->timestamp('grace_period_end')->nullable()->after('cancel_at_period_end');
            $table->integer('payment_retry_count')->default(0)->after('grace_period_end');
            
            // Make stripe_subscription_id unique
            $table->unique('stripe_subscription_id');
            
            // Add index for faster queries
            $table->index(['user_id', 'status']);
            $table->index('stripe_customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['stripe_customer_id']);
            $table->dropUnique(['stripe_subscription_id']);
            $table->dropColumn([
                'stripe_customer_id',
                'plan_type',
                'current_period_end',
                'cancel_at_period_end',
                'grace_period_end',
                'payment_retry_count',
            ]);
        });
    }
};
