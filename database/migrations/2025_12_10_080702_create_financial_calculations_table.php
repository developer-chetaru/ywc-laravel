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
        Schema::create('financial_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Nullable for guest calculations
            $table->string('calculator_type'); // retirement_needs, pension_growth, compound_interest, etc.
            $table->json('input_data'); // Store all input parameters
            $table->json('result_data'); // Store calculated results
            $table->string('session_id')->nullable(); // For guest users
            $table->boolean('is_saved')->default(false); // User saved this calculation
            $table->timestamps();
            
            $table->index(['user_id', 'calculator_type']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_calculations');
    }
};
