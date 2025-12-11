<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('period', ['monthly', 'quarterly', 'annual']);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_income', 15, 2)->default(0);
            $table->decimal('total_expenses', 15, 2)->default(0);
            $table->decimal('savings_target', 15, 2)->default(0);
            $table->json('category_budgets')->nullable(); // {category: amount, ...}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['user_id', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_budgets');
    }
};
