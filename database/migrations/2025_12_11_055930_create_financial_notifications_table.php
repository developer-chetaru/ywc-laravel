<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['goal_milestone', 'budget_alert', 'bill_reminder', 'investment_reminder', 'tax_reminder', 'consultation_reminder', 'advisor_message']);
            $table->string('title');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();
            
            $table->index(['user_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_notifications');
    }
};
