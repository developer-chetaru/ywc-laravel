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
        Schema::create('mental_health_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->constrained('mental_health_session_bookings')->onDelete('cascade');
            $table->string('payment_method'); // card, paypal, credits
            $table->string('stripe_payment_id')->nullable();
            $table->string('stripe_session_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('credits_used', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency')->default('GBP');
            $table->string('status')->default('pending'); // pending, completed, failed, refunded
            $table->text('failure_reason')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->string('invoice_number')->unique()->nullable();
            $table->string('invoice_path', 2048)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_payments');
    }
};
