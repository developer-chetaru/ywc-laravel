<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('advisor_id')->constrained('financial_advisors')->cascadeOnDelete();
            $table->enum('type', ['30min', '60min', '90min', 'specialty']);
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->datetime('scheduled_at');
            $table->string('meeting_link')->nullable();
            $table->text('pre_consultation_notes')->nullable();
            $table->text('consultation_notes')->nullable();
            $table->text('action_plan')->nullable();
            $table->string('recording_url')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_consultations');
    }
};
