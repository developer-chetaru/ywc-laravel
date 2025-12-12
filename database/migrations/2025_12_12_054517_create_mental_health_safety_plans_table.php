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
        Schema::create('mental_health_safety_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('warning_signs')->nullable();
            $table->json('coping_strategies')->nullable();
            $table->json('support_people')->nullable(); // Array of contacts
            $table->json('safe_places')->nullable();
            $table->json('professional_contacts')->nullable();
            $table->text('reasons_for_living')->nullable();
            $table->json('emergency_steps')->nullable();
            $table->boolean('is_shared_with_therapist')->default(false);
            $table->foreignId('shared_with_therapist_id')->nullable()->constrained('mental_health_therapists')->onDelete('set null');
            $table->dateTime('last_updated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_safety_plans');
    }
};
