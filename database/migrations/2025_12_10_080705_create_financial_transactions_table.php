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
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['income', 'expense']);
            $table->date('transaction_date');
            $table->decimal('amount', 15, 2);
            $table->string('category'); // salary, tips, accommodation, food, etc.
            $table->string('description')->nullable();
            $table->enum('period_type', ['working', 'time_off', 'both'])->default('both');
            $table->foreignId('account_id')->nullable()->constrained('financial_accounts')->nullOnDelete();
            $table->foreignId('goal_id')->nullable()->constrained('financial_goals')->nullOnDelete();
            $table->string('receipt_path')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_frequency')->nullable(); // monthly, quarterly, etc.
            $table->timestamps();
            
            $table->index(['user_id', 'transaction_date']);
            $table->index(['user_id', 'type', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
