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
        Schema::create('subscriptions', function (Blueprint $table) {
             $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('stripe_session_id')->nullable()->unique();
            $table->string('stripe_subscription_id')->nullable();
            $table->integer('amount')->nullable(); // stored in pennies
            $table->string('status')->default('pending'); // active, cancelled, etc.
            $table->string('interval')->nullable(); // e.g. 'month'
            $table->integer('interval_count')->default(1);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
