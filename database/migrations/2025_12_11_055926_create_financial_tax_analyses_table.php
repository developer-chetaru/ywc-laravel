<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_tax_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nationality')->nullable();
            $table->string('current_residence')->nullable();
            $table->integer('days_in_countries')->nullable(); // JSON: {country: days}
            $table->string('permanent_address')->nullable();
            $table->json('tax_residency_analysis')->nullable();
            $table->json('tax_obligations')->nullable();
            $table->json('optimization_opportunities')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_tax_analyses');
    }
};
