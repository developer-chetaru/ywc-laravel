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
        Schema::create('financial_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Goal name
            $table->enum('type', ['retirement', 'emergency_fund', 'property_deposit', 'travel', 'debt_payoff', 'education', 'other']);
            $table->decimal('target_amount', 15, 2);
            $table->decimal('current_amount', 15, 2)->default(0);
            $table->date('target_date');
            $table->decimal('monthly_contribution', 15, 2)->nullable();
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('linked_account_id')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_goals');
    }
};
