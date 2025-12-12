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
        Schema::create('mental_health_therapist_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('therapist_id')->constrained('mental_health_therapists')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->decimal('platform_fee', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2);
            $table->string('payout_method'); // bank_transfer, paypal
            $table->string('payout_account')->nullable(); // Account details
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->dateTime('processed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->string('transaction_id')->nullable();
            $table->json('sessions_included')->nullable(); // Array of session IDs included in payout
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_therapist_payouts');
    }
};
