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
        Schema::create('mental_health_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('type'); // earned, used, refunded, expired, manual_adjustment
            $table->string('source')->nullable(); // monthly_calculation, session_usage, cancellation_refund, admin_adjustment
            $table->text('description')->nullable();
            $table->foreignId('related_booking_id')->nullable()->constrained('mental_health_session_bookings')->onDelete('set null');
            $table->date('expires_at')->nullable(); // Credits expire after 12 months
            $table->dateTime('expired_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // For manual adjustments
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_credits');
    }
};
