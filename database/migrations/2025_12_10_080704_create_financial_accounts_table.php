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
        Schema::create('financial_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Account name
            $table->enum('type', ['savings', 'checking', 'investment', 'pension', 'debt', 'property', 'other']);
            $table->string('account_subtype')->nullable(); // emergency_fund, time_off_fund, SIPP, 401k, etc.
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->string('institution')->nullable(); // Bank/institution name
            $table->string('account_number')->nullable();
            $table->decimal('interest_rate', 5, 2)->nullable(); // For savings/loans
            $table->decimal('monthly_contribution', 15, 2)->nullable();
            $table->json('asset_allocation')->nullable(); // For investment accounts {stocks: 70, bonds: 20, cash: 10}
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_accounts');
    }
};
